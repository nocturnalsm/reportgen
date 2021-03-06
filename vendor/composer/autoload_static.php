<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit302077c81479787de61788efadd290b5
{
    public static $prefixLengthsPsr4 = array (
        'P' => 
        array (
            'PhpQuery\\' => 9,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'PhpQuery\\' => 
        array (
            0 => __DIR__ . '/..' . '/phpquery/phpquery/src',
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit302077c81479787de61788efadd290b5::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit302077c81479787de61788efadd290b5::$prefixDirsPsr4;

        }, null, ClassLoader::class);
    }
}
