<?php
/**
 * 注册 解析类
 */

namespace SwoftRewrite\Annotation;


use SwoftRewrite\Annotation\Contract\LoaderInterface;
use SwoftRewrite\Annotation\Resource\AnnotationResource;

/**
 * Class AnnotationRegister
 * @package SwoftRewrite\Annotation
 */
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

    private static $parsers = [];

    private static $annotations = [];

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
     * 注册 注解
     */
    public static function registerAnnotation(string $loadNamespace,string $className,array $classAnnotation): void
    {
        self::$classStats['annotation']++;
        self::$annotations[$loadNamespace][$className] = $classAnnotation;
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

    public static function registerParser(string $annotationClass,string $parserClassName):void
    {
        self::$classStats['parser']++;
        self::$parsers[$annotationClass] = $parserClassName; // 注释 => 类名
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

    public static function getClassStats(){
        return self::$classStats;
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

    public static function getAnnotations()
    {
        return self::$annotations;
    }
}