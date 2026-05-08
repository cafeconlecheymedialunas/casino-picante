<?php

$base = 'C:\Users\dex360\Downloads\cASINO (1)\casino-laravel\\';

$files = [
    'app\Livewire\Lineas.php',
    'app\Livewire\Bonos.php',
    'app\Livewire\Users\UsersIndex.php',
    'app\Livewire\Novedades.php',
    'app\Livewire\Agentes.php',
    'app\Livewire\Sorteos.php',
    'app\Livewire\Tickets.php',
    'app\Livewire\Promociones.php',
];

function fixNotifyCalls($content)
{
    // Fix 1: Remove leftover multi-line garbage after notify() calls
    // Pattern: notify('...', "...", '...', '...');\n'...', "...", ...\n...
    $content = preg_replace_callback(
        '/(\$this->notify\([^;]+;)\s*[\'"][^\'"]*[\'"][^;]*;/s',
        function ($matches) {
            return $matches[1];
        },
        $content
    );

    // Fix 2: Fix any notify() calls that have wrong format
    // Convert notify(', ", ', type') to notify('title', 'message', 'module', 'link', 'type')
    $content = preg_replace(
        '/\$this->notify\(\s*,\s*,\s*,\s*\)/',
        '$this->notify(\'\1\', \'\2\', \'\3\', \'\4\', \'\5\')',
        $content
    );

    return $content;
}

foreach ($files as $file) {
    $path = $base.$file;
    if (! file_exists($path)) {
        echo "SKIP: $file (not found)\n";

        continue;
    }

    $content = file_get_contents($path);
    $original = $content;

    $content = fixNotifyCalls($content);

    if ($content !== $original) {
        file_put_contents($path, $content);
        echo "FIXED: $file\n";
    } else {
        echo "OK: $file\n";
    }
}

echo "\nDONE.\n";
