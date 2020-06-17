<?php
spl_autoload_register(function ($class_name) {
//    echo '<pre>';
//    print_r($class_name);
//    echo '</pre>';

    $classes_path = str_replace('\\','/', $class_name);
    $full_path = __DIR__ . '/' . $classes_path . '.php';

    if (file_exists($full_path)) {
        require_once $full_path;
    }
});