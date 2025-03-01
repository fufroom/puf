<?php

namespace Puf\Core;

require_once __DIR__ . '/libs/Mustache/autoload.php';

class Templating
{
    private $mustache;
    private $config;
    private $pagesDir;
    private $partsDir;
    private $pageExt;
    private $partExt;

    public function __construct()
    {
        $configFile = realpath(__DIR__ . '/../../config.json');
        if (!$configFile || !file_exists($configFile)) {
            throw new \Exception("Configuration file 'config.json' not found in project/");
        }

        $this->config = json_decode(file_get_contents($configFile), true);

        if (!isset($this->config['pages_directory'], $this->config['partials_directory'], $this->config['page_extension'], $this->config['partial_extension'])) {
            throw new \Exception("Missing required configuration keys in config.json");
        }

        // Resolve absolute paths
        $this->pagesDir = realpath(__DIR__ . '/../../../' . $this->config['pages_directory']);
        $this->partsDir = realpath(__DIR__ . '/../../../' . $this->config['partials_directory']);
        $this->pageExt = $this->config['page_extension'];
        $this->partExt = $this->config['partial_extension'];

        // Debugging output for paths
        error_log("Resolved pages path: " . var_export($this->pagesDir, true));
        error_log("Resolved partials path: " . var_export($this->partsDir, true));

        if (!$this->pagesDir || !$this->partsDir) {
            throw new \Exception(
                "Invalid pages or partials directory in config.json. " . 
                "Resolved pages path: " . var_export($this->pagesDir, true) . 
                ", Resolved partials path: " . var_export($this->partsDir, true)
            );
        }

        $partials = [];
        foreach (glob("$this->partsDir/*.$this->partExt") as $file) {
            $name = "parts/" . basename($file, ".$this->partExt");
            error_log("Loading partial: $name from $file"); 
            $partials[$name] = file_get_contents($file);
        }

        $this->mustache = new \Mustache_Engine([
            'partials' => $partials
        ]);
    }

    public function render($template, $data)
    {
        $templateFile = "$this->pagesDir/$template.$this->pageExt";

        if (!file_exists($templateFile)) {
            throw new \Exception("Template file '$template.{$this->pageExt}' not found in {$this->pagesDir}");
        }

        $templateContent = file_get_contents($templateFile);

        return $this->mustache->render($templateContent, $data);
    }
}
