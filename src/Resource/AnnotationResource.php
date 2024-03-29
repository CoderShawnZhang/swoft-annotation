<?php
/**
 * 解析资源文件类
 */
namespace SwoftRewrite\Annotation\Resource;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;
use SwoftRewrite\Annotation\Annotation\Mapping\AnnotationParser;
use SwoftRewrite\Annotation\AnnotationRegister;
use SwoftRewrite\Annotation\Contract\LoaderInterface;
use SwoftRewrite\Stdlib\Helper\ComposerHelper;
use SwoftRewrite\Stdlib\Helper\DirectoryHelper;
use SwoftRewrite\Stdlib\Helper\ObjectHelper;

class AnnotationResource extends Resource
{
    public const DEFAULT_EXCLUDED_PSR4_PREFIXES = [
        'Prs\\',
        'Monolog\\',
        'PHPUnit\\',
        'Symfony\\'
    ];
    private $loaderClassName = 'AutoLoader';
    private $loaderClassSuffix = 'php';


    private $classLoader;
    public $onlyNamespaces = [];
    private $excludedPsr4Prefixes;
    public $inPhar = false;

    private $disabledAutoLoaders = [];

    private $excludedFilenames = [
        'Swoft.php' => 1
    ];

    public function __construct(array $config = [])
    {
        $this->excludedPsr4Prefixes = self::DEFAULT_EXCLUDED_PSR4_PREFIXES;
        ObjectHelper::init($this,$config);
        $this->registerLoader();
        $this->classLoader = ComposerHelper::getClassLoader();
    }

    /**
     * @throws \Exception
     */
    public function load()
    {
        $prefixDirsPsr4 = $this->classLoader->getPrefixesPsr4();
        foreach($prefixDirsPsr4 as $ns => $paths){
            if($this->onlyNamespaces && !in_array($ns,$this->onlyNamespaces,true)){
                continue;
            }
            //注册到单独的数组，已经包含的指定psr4文件
            if($this->isExcludedPsr4Prefix($ns)){
                AnnotationRegister::registerExcludeNs($ns);
            }

            //加载框架需要loadClass
            foreach($paths as $path){
                //获取组件的AutoLoader文件路径
                $loaderFile = $this->getAnnotationClassLoaderFile($path);
                if(!file_exists($loaderFile)){
                    continue;
                }
                $loaderClass = $this->getAnnotationLoaderClassName($ns);

                $loaderObject = new $loaderClass();
                $isEnabled = !isset($this->disabledAutoLoaders[$loaderClass]);

                if($isEnabled && $loaderObject instanceof LoaderInterface){
                    //注册loaderfile 文件路径
                    AnnotationRegister::registerAutoLoaderFile($loaderFile);
                    //扫描这个有AutoLoader的src目录，
                    $this->loaderAnnotation($loaderObject);
                }

                AnnotationRegister::addAutoLoader($ns,$loaderObject);
            }
        }
    }

    public function isExcludedPsr4Prefix(string $namespace):bool
    {
        foreach($this->excludedPsr4Prefixes as $prefix){
            if(0 === strpos($namespace,$prefix)){
                return true;
            }
        }
        return false;
    }

    /**
     * Composer  loadClass 拼接 /AutoLoader.php   返回 组件的AutoLoader.php文件路径
     * @param string $path
     * @return string ‌/Users/zhanghongbo/develop/swoftrewrite/swoft/vendor/swoft-rewrite/swoft-stdlib/src/AutoLoader.php
     */
    private function getAnnotationClassLoaderFile(string $path):string
    {
        //  $path=‌/Users/zhanghongbo/develop/swoftrewrite/swoft/vendor/swoft-rewrite/swoft-stdlib/src
        $path = $this->inPhar ? $path : (string)realpath($path);
        return sprintf('%s/%s.%s',$path,$this->loaderClassName,$this->loaderClassSuffix);
    }

    /**
     * @param string $namespace
     * @return string SwoftRewrite\Annotation\AutoLoader
     */
    private function getAnnotationLoaderClassName(string $namespace): string
    {
        //$namespace = ‌SwoftRewrite\Annotation\
        return sprintf('%s%s',$namespace,$this->loaderClassName);
    }

    public function setDisabledAutoLoaders(array $disabledAutoLoaders)
    {
        $this->disabledAutoLoaders = $disabledAutoLoaders;
    }
    public function getDisabledAutoLoaders()
    {
        return $this->disabledAutoLoaders;
    }

    /**
     * 遍历组件的目录，通过 AutoLoader getPrefixDirs() 返回目录
     * 然后spl 迭代目录
     * @param LoaderInterface $loader
     * @throws \Exception
     */
    private function loaderAnnotation(LoaderInterface $loader):void
    {
        $nsPaths = $loader->getPrefixDirs(); //组件的AutoLoader
        foreach($nsPaths as $ns => $path){
            $iterator = DirectoryHelper::recursiveIterator($path);
            /* @var \SplFileInfo $splFileInfo */
            foreach($iterator as $splFileInfo){
                $pathName = $splFileInfo->getPathname();
                //$splFileInfo->isDir();
                if(is_dir($pathName)){
                    continue;
                }
                $fileName = $splFileInfo->getFilename();
                $extension = $splFileInfo->getExtension();

                if($this->loaderClassSuffix !== $extension || strpos($fileName,'.') === 0){
                    continue;
                }

                //是否遍历到了Swoft.php文件
                if(isset($this->excludedFilenames[$fileName])){
                    AnnotationRegister::registerExcludeFilename($fileName);
                    continue;
                }

                $suffix = sprintf('.%s',$this->loaderClassSuffix);
                $pathName = str_replace([$path,'/',$suffix],['','\\',''],$pathName);
                $className = sprintf('%s%s',$ns,$pathName);

                if(!class_exists($className) || $className == 'SwoftTest\Annotation\Testing\testFile\noFile'){
                    continue;
                }
                //$ns = ‌SwoftRewrite\Annotation 命名空间（loaderclass记录的）
                //$className = ‌SwoftRewrite\Annotation\Contract\LoaderInterface
                //遍历组件目录，获取指定注解 ，注册到注解数组
                $this->parseAnnotation($ns,$className);
            }
        }
    }

    private function parseAnnotation(string $namespace,string $className):void
    {
        $reflectionClass = new \ReflectionClass($className);

        if($reflectionClass->isAbstract()){
            return;
        }
        //解析类的注释，
        //  1：是 AnnotationParser 注释。
        //  2不是AnnotationParser注释，将整个类的注释，属性注释，方法注释放到数组annotation，还有类的反射放到数组reflection
        $oneClassAnnotation = $this->parseOneClassAnnotation($reflectionClass);
        //如果不等于空，
        if(!empty($oneClassAnnotation)){
            //扫描到 ‌SwoftRewrite\Event\Manager\EventManager 使用了注解器，但使用的又不是 AnnotationParser注解器，会进来
            AnnotationRegister::registerAnnotation($namespace,$className,$oneClassAnnotation);
        }
    }

    private function parseOneClassAnnotation(\ReflectionClass $reflectionClass): array
    {
        //获取注解
        //例如 扫描到 ‌SwoftRewrite\Event\Annotation\Mapping\Listener 这个文件是 注解器 没有注解，所以 下面每个都不满足 返回$oneClassAnnotation空

        //注解 读取器。
        $reader = new AnnotationReader();
        $className = $reflectionClass->getName();

        $oneClassAnnotation = [];
        $classAnnotations = $reader->getClassAnnotations($reflectionClass);

        //注册 解析
        //扫描到 SwoftRewrite\\Event\\Annotation\\Parser\\ListenerParser 是使用 AnnotationParser 注解器 的类有注解走下面的foreach
        foreach($classAnnotations as $classAnnotation){
            if($classAnnotation instanceof AnnotationParser){
                $this->registerParser($className,$classAnnotation);
                return [];
            }
        }

        // 类解析
        // 扫描到 ‌SwoftRewrite\Event\Manager\EventManager 发现这个类是使用注解器了，但是使用的 不是  AnnotationParser 所以进入下面的if
        if(!empty($classAnnotations)){
            $oneClassAnnotation['annotation'] = $classAnnotations;
            $oneClassAnnotation['reflection'] = $reflectionClass;
        }

        //属性解析
        $reflectionProperties = $reflectionClass->getProperties();
        foreach($reflectionProperties as $reflectionProperty){
            $propertyName = $reflectionProperty->getName();
            $propertyAnnotations = $reader->getPropertyAnnotations($reflectionProperty);
            if(!empty($propertyAnnotations)){
                $oneClassAnnotation['properties'][$propertyName]['annotation'] = $propertyAnnotations;
                $oneClassAnnotation['properties'][$propertyName]['reflection'] = $reflectionProperty;
            }
        }

        //函数解析
        $reflectionMethods = $reflectionClass->getMethods();
        foreach($reflectionMethods as $reflectionMethod){
            $methodName = $reflectionMethod->getName();
            $methodAnnotations = $reader->getMethodAnnotations($reflectionMethod);
            if(!empty($methodAnnotations)){
                $oneClassAnnotation['methods'][$methodName]['annotation'] = $methodAnnotations;
                $oneClassAnnotation['methods'][$methodName]['reflection'] = $reflectionMethod;
            }
        }

        $parentReflectionClass = $reflectionClass->getParentClass();
        if($parentReflectionClass !== false){
            $parentClassAnnotation = $this->parseOneClassAnnotation($parentReflectionClass);
            if(!empty($parentClassAnnotation)){
                $oneClassAnnotation['parent'] = $parentClassAnnotation;
            }
        }

        return $oneClassAnnotation;
    }

    /**
     * 注册 注释 => 类名
     * @param string $parserClassName
     * @param AnnotationParser $annotationParser
     */
    private function registerParser(string $parserClassName,AnnotationParser $annotationParser): void
    {
        $annotationClass = $annotationParser->getAnnotation();
        AnnotationRegister::registerParser($annotationClass,$parserClassName);
    }

    private function registerLoader()
    {
        AnnotationRegistry::registerLoader(function(string $class){
            if(class_exists($class)){
                return true;
            }
            return false;
        });
    }
}