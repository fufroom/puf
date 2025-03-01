<?php

// Load Mustache interfaces first
require_once __DIR__ . '/Cache.php';
require_once __DIR__ . '/Loader/MutableLoader.php';

// Load core Mustache files
require_once __DIR__ . '/Autoloader.php';
require_once __DIR__ . '/Engine.php';
require_once __DIR__ . '/Loader.php';
require_once __DIR__ . '/Parser.php';
require_once __DIR__ . '/Tokenizer.php';
require_once __DIR__ . '/Template.php';
require_once __DIR__ . '/Context.php';
require_once __DIR__ . '/LambdaHelper.php';
require_once __DIR__ . '/HelperCollection.php';
require_once __DIR__ . '/Source.php';
require_once __DIR__ . '/Logger.php';
require_once __DIR__ . '/Exception.php';
require_once __DIR__ . '/Compiler.php';

// Include subdirectory files dynamically
foreach (glob(__DIR__ . '/Cache/*.php') as $file) {
    require_once $file;
}
foreach (glob(__DIR__ . '/Loader/*.php') as $file) {
    require_once $file;
}
foreach (glob(__DIR__ . '/Logger/*.php') as $file) {
    require_once $file;
}
foreach (glob(__DIR__ . '/Exception/*.php') as $file) {
    require_once $file;
}
foreach (glob(__DIR__ . '/Source/*.php') as $file) {
    require_once $file;
}

// Register Mustache autoloader
\Mustache_Autoloader::register();
