<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitacd795908a1a176c6024cfa9585aa429
{
    public static $prefixLengthsPsr4 = array (
        'T' => 
        array (
            'Tonik\\Gin\\' => 10,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Tonik\\Gin\\' => 
        array (
            0 => __DIR__ . '/..' . '/tonik/gin/src/Gin',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
        'Tonik\\Gin\\Asset\\Asset' => __DIR__ . '/..' . '/tonik/gin/src/Gin/Asset/Asset.php',
        'Tonik\\Gin\\Contract\\ConfigInterface' => __DIR__ . '/..' . '/tonik/gin/src/Gin/Contract/ConfigInterface.php',
        'Tonik\\Gin\\Foundation\\Autoloader' => __DIR__ . '/..' . '/tonik/gin/src/Gin/Foundation/Autoloader.php',
        'Tonik\\Gin\\Foundation\\Config' => __DIR__ . '/..' . '/tonik/gin/src/Gin/Foundation/Config.php',
        'Tonik\\Gin\\Foundation\\Exception\\BindingResolutionException' => __DIR__ . '/..' . '/tonik/gin/src/Gin/Foundation/Exception/BindingResolutionException.php',
        'Tonik\\Gin\\Foundation\\Exception\\FileNotFoundException' => __DIR__ . '/..' . '/tonik/gin/src/Gin/Foundation/Exception/FileNotFoundException.php',
        'Tonik\\Gin\\Foundation\\Singleton' => __DIR__ . '/..' . '/tonik/gin/src/Gin/Foundation/Singleton.php',
        'Tonik\\Gin\\Foundation\\Theme' => __DIR__ . '/..' . '/tonik/gin/src/Gin/Foundation/Theme.php',
        'Tonik\\Gin\\Template\\Template' => __DIR__ . '/..' . '/tonik/gin/src/Gin/Template/Template.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInitacd795908a1a176c6024cfa9585aa429::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInitacd795908a1a176c6024cfa9585aa429::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInitacd795908a1a176c6024cfa9585aa429::$classMap;

        }, null, ClassLoader::class);
    }
}
