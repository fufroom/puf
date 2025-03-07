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

    public function getAllFiles($prefix = "")
    {
        $pattern = $prefix ? "{$this->dataPath}/{$prefix}-*.json" : "{$this->dataPath}/*.json";
        $files = glob($pattern) ?: [];
    
        $items = [];
        foreach ($files as $file) {
            $key = basename($file, ".json");
            if (!str_contains($key, "-deleted-")) { // Ensure deleted items are excluded
                $items[$key] = json_decode(file_get_contents($file), true);
            }
        }
    
        file_put_contents(__DIR__ . '/debug.log', "Loaded Items ({$prefix}): " . json_encode($items) . "\n", FILE_APPEND);
    
        return $items;
    }
    
    
    

    public function saveItem(string $type, array $data, ?string $id = null, array $uploadedFiles = []): array
    {
        $id = $id ?? uniqid();
        $fileKey = "{$type}-{$id}";
        $item = $this->getFile($fileKey) ?? ["id" => $id, "type" => $type, "images" => []];
    
        // Ensure title and description are set (default to empty if missing)
        $item["title"] = trim($data['title'] ?? '');
        $item["description"] = trim($data['description'] ?? '');
        $item["link"] = trim($data['link'] ?? '');
    
        // Handle image order and uploaded files
        $imageOrder = is_string($data['image_order']) ? json_decode($data['image_order'], true) : $data['image_order'];
        if (!is_array($imageOrder)) {
            $imageOrder = [];
        }
    
        $finalImages = array_values(array_filter(array_merge(
            array_map(fn($url) => basename($url), $imageOrder), // Keep only filenames
            array_map(fn($file) => basename($file['url']), $uploadedFiles)
        )));
    
        $item["images"] = $finalImages;
    
        // Save updated item
        $this->saveFile($fileKey, $item);
        
        return $item;
    }
    
    

    public function deleteItem($type, $id)
    {
        $filePath = "{$this->dataPath}/{$type}-{$id}.json";

        if (!file_exists($filePath)) {
            return false;
        }

        $deletedDir = "{$this->dataPath}/deleted/";
        if (!is_dir($deletedDir)) mkdir($deletedDir, 0777, true);

        $timestamp = date("YmdHis");
        $deletedFilename = "{$deletedDir}{$type}-{$id}-deleted-{$timestamp}.json";

        return rename($filePath, $deletedFilename);
    }
}
