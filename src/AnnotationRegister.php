<?php
/**
 * 注册 解析类
 */

namespace SwoftRewrite\Annotation;


use SwoftRewrite\Annotation\Contract\LoaderInterface;
use SwoftRewrite\Annotation\Resource\AnnotationResource;

class AnnotationRegister
{
    /**
     * @var array
     * @example
     * [
     *      'namespace1',
     *      'namespace2'
     * ]
     */
    private static $excludeNamespaces = [];

    private static $autoLoaderFiles = [];

    private static $classStats = [
        'parser' => 0,
        'annotation' => 0,
        'autoloader' => 0,
    ];

    private static $excludeFilenames = [];

    public static function load(array $config = [])
    {
        $resource = new AnnotationResource($config);
        $resource->load();
    }


    /**
     * 注册 预定义 classloader包含的 psr4fix
     */
    public static function registerExcludeNs(string $ns): void
    {
        self::$excludeNamespaces[] = $ns;
    }

    /**
     * @param string $file
     */
    public static function registerAutoLoaderFile(string $file): void
    {
        self::$autoLoaderFiles[] = $file;
    }

    public static function registerExcludeFilename(string $filename): void
    {
        self::$excludeFilenames[] = $filename;
    }

    /**
     * 注册命名空间对应的loaderclass对象
     * @param string $namespace
     * @param LoaderInterface $autoLoader
     */
    public static function addAutoLoader(string $namespace,LoaderInterface $autoLoader):void
    {
        self::$classStats['autoloader']++;
        self::$autoLoaderFiles[$namespace] = $autoLoader;
    }

    /**
     * 根据命名空间获取loaderclass对象
     *
     * @param string $namespace
     * @return null|LoaderInterface
     */
    public static function getAutoLoader(string $namespace): ?LoaderInterface
    {
        return self::$autoLoaderFiles[$namespace] ?? null;
    }
}