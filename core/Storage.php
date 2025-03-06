<?php

namespace Puf\Core;

class Storage
{
    private $dataPath;

    public function __construct($config)
    {
        $this->dataPath = realpath(__DIR__ . '/../../../' . ($config['storage_directory'] ?? ''));

        if (!$this->dataPath) {
            $this->dataPath = __DIR__ . '/../../../' . ($config['storage_directory'] ?? '');
        }

        if (!is_dir($this->dataPath)) {
            if (!mkdir($this->dataPath, 0777, true) && !is_dir($this->dataPath)) {
                throw new \Exception("Failed to create storage directory: {$this->dataPath}");
            }
        }
    }

    public function saveFile($name, $content)
    {
        $filePath = "{$this->dataPath}/{$name}.json";
        if (file_put_contents($filePath, json_encode($content, JSON_PRETTY_PRINT)) === false) {
            throw new \Exception("Failed to save file: {$filePath}");
        }
    }

    public function getFile($name)
    {
        $filePath = "{$this->dataPath}/{$name}.json";
        return file_exists($filePath) ? json_decode(file_get_contents($filePath), true) : null;
    }

    public function deleteFile($name)
    {
        $filePath = "{$this->dataPath}/{$name}.json";
        return file_exists($filePath) ? unlink($filePath) : false;
    }

    public function getAllFiles($prefix)
    {
        $files = glob("{$this->dataPath}/{$prefix}-*.json") ?: [];
        return array_map(fn($file) => json_decode(file_get_contents($file), true), $files);
    }
}
