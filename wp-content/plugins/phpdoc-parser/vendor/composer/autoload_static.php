<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit3236ae577c62534a9dc5c669b85d17a5
{
    public static $files = array (
        'fe1bcd0336136e435eaf197895daf81a' => __DIR__ . '/..' . '/nikic/php-parser/lib/bootstrap.php',
        '2795ee4f58713c0c57b8e0e1b528277b' => __DIR__ . '/../..' . '/lib/runner.php',
        '70422b1e2bdbbe0abd94363c5534e7bd' => __DIR__ . '/../..' . '/lib/template.php',
    );

    public static $prefixLengthsPsr4 = array (
        'P' => 
        array (
            'Psr\\Log\\' => 8,
        ),
        'C' => 
        array (
            'Composer\\Installers\\' => 20,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Psr\\Log\\' => 
        array (
            0 => __DIR__ . '/..' . '/psr/log/Psr/Log',
        ),
        'Composer\\Installers\\' => 
        array (
            0 => __DIR__ . '/..' . '/composer/installers/src/Composer/Installers',
        ),
    );

    public static $prefixesPsr0 = array (
        'p' => 
        array (
            'phpDocumentor' => 
            array (
                0 => __DIR__ . '/..' . '/phpdocumentor/reflection-docblock/src',
                1 => __DIR__ . '/..' . '/phpdocumentor/reflection/src',
                2 => __DIR__ . '/..' . '/phpdocumentor/reflection/tests/unit',
                3 => __DIR__ . '/..' . '/phpdocumentor/reflection/tests/mocks',
            ),
        ),
        'P' => 
        array (
            'Parsedown' => 
            array (
                0 => __DIR__ . '/..' . '/erusev/parsedown',
            ),
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
        'WP_Parser\\Command' => __DIR__ . '/../..' . '/lib/class-command.php',
        'WP_Parser\\File_Reflector' => __DIR__ . '/../..' . '/lib/class-file-reflector.php',
        'WP_Parser\\Function_Call_Reflector' => __DIR__ . '/../..' . '/lib/class-function-call-reflector.php',
        'WP_Parser\\Hook_Reflector' => __DIR__ . '/../..' . '/lib/class-hook-reflector.php',
        'WP_Parser\\Importer' => __DIR__ . '/../..' . '/lib/class-importer.php',
        'WP_Parser\\Method_Call_Reflector' => __DIR__ . '/../..' . '/lib/class-method-call-reflector.php',
        'WP_Parser\\Plugin' => __DIR__ . '/../..' . '/lib/class-plugin.php',
        'WP_Parser\\Pretty_Printer' => __DIR__ . '/../..' . '/lib/class-pretty-printer.php',
        'WP_Parser\\Relationships' => __DIR__ . '/../..' . '/lib/class-relationships.php',
        'WP_Parser\\Static_Method_Call_Reflector' => __DIR__ . '/../..' . '/lib/class-static-method-call-reflector.php',
        'WP_Parser\\WP_CLI_Logger' => __DIR__ . '/../..' . '/lib/class-wp-cli-logger.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit3236ae577c62534a9dc5c669b85d17a5::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit3236ae577c62534a9dc5c669b85d17a5::$prefixDirsPsr4;
            $loader->prefixesPsr0 = ComposerStaticInit3236ae577c62534a9dc5c669b85d17a5::$prefixesPsr0;
            $loader->classMap = ComposerStaticInit3236ae577c62534a9dc5c669b85d17a5::$classMap;

        }, null, ClassLoader::class);
    }
}