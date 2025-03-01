<?php

namespace Puf\Core;

class Storage
{
    private $dataPath;

    public function __construct()
    {
        $this->dataPath = __DIR__ . '/../../data/';
        if (!is_dir($this->dataPath)) {
            mkdir($this->dataPath, 0777, true);
        }
    }

    public function saveFile($name, $content)
    {
        $filePath = $this->dataPath . $name . '.json';
        file_put_contents($filePath, json_encode($content, JSON_PRETTY_PRINT));
    }

    public function getFile($name)
    {
        $filePath = $this->dataPath . $name . '.json';
        if (file_exists($filePath)) {
            return json_decode(file_get_contents($filePath), true);
        }
        return null;
    }

    public function deleteFile($name)
    {
        $filePath = $this->dataPath . $name . '.json';
        if (file_exists($filePath)) {
            unlink($filePath);
            return true;
        }
        return false;
    }

    public function getAllFiles($prefix)
    {
        $files = glob($this->dataPath . "$prefix-*.json");
        $data = [];
        foreach ($files as $file) {
            $content = json_decode(file_get_contents($file), true);
            if ($content) {
                $data[] = $content;
            }
        }
        return $data;
    }
}
