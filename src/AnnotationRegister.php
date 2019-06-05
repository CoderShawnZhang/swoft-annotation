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
    private static $classStats = [
        'parser' => 0,      //扫描到 指定注释
        'annotation' => 0,
        'autoloader' => 0,
    ];

    private static $excludeNamespaces = [];     //存放 可以扫描的composer文件命名空间
    private static $autoLoaderFiles = [];       //存放每个组件下面AutoLoader的绝对地址：‌/Users/zhanghongbo/develop/swoftrewrite/swoft-annotation/test/testing/AutoLoader.php
    private static $parsers = [];               //存放AnnotationParser 标记的注释类 // 注释 => 类名
    private static $annotations = [];           //类注释标记了 @Annotation 类的命名空间 类命名地址 下的注解对象，和类的反射对象
    private static $excludeFilenames = [];      //如果遍历到了Swoft.php文件存入： ‌Swoft.php

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
     * @param string $loadNamespace SwoftTest\Annotation\Testing
     * @param string $className ‌SwoftTest\Annotation\Testing\DemoAnnotationBase
     * @param array $classAnnotation ['annotion'=>[],'reflection'=>[]]
     */
    public static function registerAnnotation(string $loadNamespace,string $className,array $classAnnotation): void
    {
        //
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

    /**
     * @return array
     */
    public static function getParsers(): array
    {
        return self::$parsers;
    }

    /**
     * @return array
     */
    public static function getExcludeNamespaces(): array
    {
        return self::$excludeNamespaces;
    }
}