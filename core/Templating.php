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

        // User Login Status
        $data['isLoggedIn'] = $this->auth->isLoggedIn();
        $data['user'] = $this->auth->getUser();

        // 🌿 Detect if user *was* logged in (logout button sets `?was_logged_in=true`)
        $data['was_logged_in'] = isset($_GET['was_logged_in']) && $_GET['was_logged_in'] === 'true';

        return $this->mustache->render($templateContent, $data);
    }
}
