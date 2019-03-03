<?php

namespace VoteMoel;

spl_autoload_register(function ($class) {
    if (strpos($class, 'VoteMoel\\') === 0) {
        $name = substr($class, strlen('VoteMoel'));
        require __DIR__ . strtr($name, '\\', DIRECTORY_SEPARATOR) . '.php';
    }
});
