<?php
/**
 * LED Factory - Autoloader Manual
 * Substitui o composer autoload para ambiente sem terminal
 */

spl_autoload_register(function ($class) {
    // Mapa de namespaces para diretórios
    $prefixes = [
        'Dompdf\\'                        => __DIR__ . '/dompdf/dompdf/src/',
        'Dompdf\\Css\\'                    => __DIR__ . '/dompdf/dompdf/src/Css/',
        'FontLib\\'                        => __DIR__ . '/phenx/php-font-lib/src/FontLib/',
        'Sabberworm\\CSS\\'               => __DIR__ . '/sabberworm/php-css-parser/src/',
        'Svg\\'                            => __DIR__ . '/phenx/php-svg-lib/src/Svg/',
    ];

    foreach ($prefixes as $prefix => $baseDir) {
        $len = strlen($prefix);
        if (strncmp($prefix, $class, $len) !== 0) {
            continue;
        }

        $relativeClass = substr($class, $len);
        $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';

        if (file_exists($file)) {
            require $file;
            return;
        }
    }
});

// Carregar Helpers e Cpdf do Dompdf
$helpersFile = __DIR__ . '/dompdf/dompdf/src/Helpers.php';
if (file_exists($helpersFile)) {
    require_once $helpersFile;
}

// Cpdf - renderizador PDF interno do Dompdf
$cpdfFile = __DIR__ . '/dompdf/dompdf/lib/Cpdf.php';
if (file_exists($cpdfFile)) {
    require_once $cpdfFile;
}
