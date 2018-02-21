<?php
spl_autoload_register(function($className){
    define('ROOT', realpath(dirname(__FILE__)) . DIRECTORY_SEPARATOR);

    $filename = strtolower(trim($className, '/\\'));
    $parts = explode('\\', $filename);
    if((isset($parts[0]) && $parts[0] === 'simplepcre') && (isset($parts[1]) && $parts[1] === 'regexp')){
        require_once ROOT . 'RegExp.php';
    }
});
?>