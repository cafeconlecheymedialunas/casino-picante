<?php

$base = 'C:\Users\dex360\Downloads\cASINO (1)\casino-laravel\\';

$files = [
    'app/Livewire/Lineas.php',
    'app/Livewire/Bonos.php',
    'app/Livewire/Users/UsersIndex.php',
    'app/Livewire/Novedades.php',
    'app/Livewire/Agentes.php',
    'app/Livewire/Sorteos.php',
    'app/Livewire/Tickets.php',
    'app/Livewire/Promociones.php',
];

$patterns = [
    // Fix malformed notify() calls - pattern: $this->notify('...', "...", '...', '...'); leftover
    '/\s*\$this->notify\(\s*\K[\'"][^\'"]*[\'"][^;]*;\s*\K(?:\s*\'[^\']*\'\s*,\s*)?/m' => '',
];

foreach ($files as $file) {
    $path = $base.$file;
    if (! file_exists($path)) {
        echo "SKIP: $file (not found)\n";

        continue;
    }

    $content = file_get_contents($path);
    $original = $content;

    // Fix 1: Remove leftover notify() multi-line garbage after proper notify() calls
    $content = preg_replace(
        '/(\$this->notify\([^;]+;)\s*[\'"][^\'"]*,[\'"][^;]*;/m',
        '$1',
        $content
    );

    // Fix 2: Ensure proper notify() format: $this->notify('title', 'message', 'module', 'link', 'type');
    // This is too complex for regex - let's just check syntax
    if ($content !== $original) {
        file_put_contents($path, $content);
        echo "FIXED: $file\n";
    } else {
        echo "OK: $file\n";
    }
}

echo "\nDONE.\n";
