<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit92ccdfa141344339af92fe46582e52cb
{
    public static $files = array (
        '7c5ea46b0417a3814b23c0314e980c54' => __DIR__ . '/../..' . '/Main/wc-tabs.php',
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->classMap = ComposerStaticInit92ccdfa141344339af92fe46582e52cb::$classMap;

        }, null, ClassLoader::class);
    }
}
