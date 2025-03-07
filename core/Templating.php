<?php

namespace Puf\Core;

require_once __DIR__ . '/libs/Mustache/autoload.php';

class Templating
{
    private $mustache;
    private $pagesDir;
    private $partsDir;
    private $pageExt;
    private $partExt;
    private $auth;

    public function __construct($config)
    {
        $this->auth = new SimpleAuth($config);

        if (!isset($config['templating'])) {
            throw new \Exception("Missing 'templating' key in framework config.");
        }

        $templatingConfig = $config['templating'];

        foreach (['pages_directory', 'partials_directory', 'page_extension', 'partial_extension'] as $key) {
            if (empty($templatingConfig[$key])) {
                throw new \Exception("Missing required key '{$key}' in templating config.");
            }
        }

        // Resolve Paths
        $this->pagesDir = realpath(__DIR__ . '/../../' . $templatingConfig['pages_directory']) 
            ?: __DIR__ . '/../../' . $templatingConfig['pages_directory'];

        $this->partsDir = realpath(__DIR__ . '/../../' . $templatingConfig['partials_directory']) 
            ?: __DIR__ . '/../../' . $templatingConfig['partials_directory'];

        $this->pageExt = $templatingConfig['page_extension'];
        $this->partExt = $templatingConfig['partial_extension'];

        // Debug Paths
        error_log("Resolved pages path: " . var_export($this->pagesDir, true));
        error_log("Resolved partials path: " . var_export($this->partsDir, true));

        // Ensure directories exist
        if (!is_dir($this->pagesDir) || !is_dir($this->partsDir)) {
            throw new \Exception(
                "Invalid pages or partials directory in framework config. " .
                "Resolved pages path: " . var_export($this->pagesDir, true) .
                ", Resolved partials path: " . var_export($this->partsDir, true)
            );
        }

        // Load Partials
        $partials = [];
        foreach (glob("{$this->partsDir}/*.{$this->partExt}") ?: [] as $file) {
            $name = "parts/" . basename($file, ".{$this->partExt}");
            error_log("Loading partial: $name from $file");
            $partials[$name] = file_get_contents($file);
        }

        // Login / Logout Button Handling
        $partials['parts/login'] = $this->auth->isLoggedIn() ? '' : 'Login Form HTML';
        $partials['parts/logout'] = $this->auth->isLoggedIn() ?  '' : 'Logout Form HTML';

        // Initialize Mustache
        $this->mustache = new \Mustache_Engine([
            'partials' => $partials
        ]);
    }

    public function render($template, $data = [])
    {
        $templateFile = "{$this->pagesDir}/{$template}.{$this->pageExt}";
        if (!file_exists($templateFile)) {
            throw new \Exception("Template file '{$template}.{$this->pageExt}' not found in {$this->pagesDir}");
        }
    
        $templateContent = file_get_contents($templateFile);
    
        // ✅ Check if metadata.json exists before loading
        $metadataPath = realpath(__DIR__ . '/../../metadata.json') ?: __DIR__ . '/../../metadata.json';
        $metadata = [];
    
        if (file_exists($metadataPath)) {
            $metadataContent = file_get_contents($metadataPath);
            $decodedMetadata = json_decode($metadataContent, true);
    
            if (json_last_error() === JSON_ERROR_NONE) {
                $metadata = $decodedMetadata['meta'] ?? []; // Extract 'meta' subkey
            } else {
                error_log("[ERROR] metadata.json could not be parsed: " . json_last_error_msg());
            }
        } else {
            error_log("[INFO] metadata.json not found, skipping metadata merge.");
        }
    
        // ✅ Function to Flatten Metadata and Add `meta_` Prefix
        function flattenMetadata($array, $prefix = 'meta_') {
            $flat = [];
            foreach ($array as $key => $value) {
                $fullKey = $prefix . $key;
                if (is_array($value)) {
                    if (array_values($value) === $value) {
                        // If it's an indexed array, convert it to a comma-separated string
                        $flat[$fullKey] = implode(', ', $value);
                    } else {
                        // If it's an associative array, recurse
                        $flat = array_merge($flat, flattenMetadata($value, $fullKey . '_'));
                    }
                } else {
                    $flat[$fullKey] = (string) $value;
                }
            }
            return $flat;
        }
    
        // ✅ Apply Flattening with `meta_` Prefix
        $metadata = flattenMetadata($metadata);
    
        // ✅ Merge metadata + auth data + provided data
        $mergedData = array_merge($metadata, [
            'isLoggedIn' => $this->auth->isLoggedIn() ? 'true' : 'false',
            'user' => json_encode($this->auth->getUser(), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
            'was_logged_in' => isset($_GET['was_logged_in']) && $_GET['was_logged_in'] === 'true' ? 'true' : 'false'
        ], $data);
    
        // ✅ Save merged data as `data_sample.json`
        $dataSamplePath = __DIR__ . '/../../data_sample.json';
        file_put_contents($dataSamplePath, json_encode($mergedData, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
    
        // ✅ Render the template with Mustache
        return $this->mustache->render($templateContent, $mergedData);
    }
    
    
    
}
