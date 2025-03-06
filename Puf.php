<?php

// Load Framework Config Once
$configPath = realpath(__DIR__ . '/../_config_puf_framework.json');
if (!$configPath || !file_exists($configPath)) {
    throw new \Exception("Framework config file '_config_puf_framework.json' not found at: " . __DIR__ . '/../_config_puf_framework.json');
}

$PUF_CONFIG = json_decode(file_get_contents($configPath), true);

if (!is_array($PUF_CONFIG)) {
    throw new \Exception("Invalid format in '_config_puf_framework.json'. Ensure it contains valid JSON.");
}

// Load Core Components
require __DIR__ . '/core/Compilation.php';
require __DIR__ . '/core/SimpleAuth.php';
require __DIR__ . '/core/Router.php';
require __DIR__ . '/core/Storage.php';
require __DIR__ . '/core/Templating.php';
require __DIR__ . '/core/FileUpload.php'; // ✅ Load FileUpload

// Initialize Components with Config
$compiler = new Puf\Core\Compilation($PUF_CONFIG);
$auth = new Puf\Core\SimpleAuth($PUF_CONFIG);
$storage = new Puf\Core\Storage($PUF_CONFIG);
$templating = new Puf\Core\Templating($PUF_CONFIG);
$router = new Puf\Core\Router($PUF_CONFIG);

// ✅ Allow images, PDFs, and text files for uploading
$fileUpload = new Puf\Core\FileUpload(__DIR__ . '/uploads', ['jpg', 'jpeg', 'png', 'gif', 'pdf', 'txt']);

// Run CSS Compilation
$compiler->compile();

