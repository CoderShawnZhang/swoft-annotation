<?php
/**
 * 解析资源文件类
 */
namespace SwoftRewrite\Annotation\Resource;

use SwoftRewrite\Annotation\AnnotationRegister;
use SwoftRewrite\Annotation\Contract\LoaderInterface;
use SwoftRewrite\Stdlib\Helper\ComposerHelper;
use SwoftRewrite\Stdlib\Helper\DirectoryHelper;

class AnnotationResource
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
    private $onlyNamespaces = [];
    private $excludedPsr4Prefixes;
    private $inPhar = false;

    private $disabledAutoLoaders = [];

    private $excludedFilenames = [
        'Swoft.php' => 1
    ];


    public function __construct(array $config = [])
    {
        $this->excludedPsr4Prefixes = self::DEFAULT_EXCLUDED_PSR4_PREFIXES;
        $this->classLoader = ComposerHelper::getClassLoader();
    }

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
                    $this->loaderAnnotation($loaderObject);
                }

                AnnotationRegister::addAutoLoader($ns,$loaderObject);
            }
        }
    }

    public function isExcludedPsr4Prefix(string $namespec):bool
    {
        foreach($this->excludedPsr4Prefixes as $prefix){
            if(0 === strpos($namespec,$prefix)){
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

    private function loaderAnnotation(LoaderInterface $loader):void
    {
        $nsPaths = $loader->getPrefixDirs();
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
                //是否是已经在包含的包含的文件里了
                if(isset($this->excludedFilenames[$fileName])){
                    AnnotationRegister::registerExcludeFilename($fileName);
                    continue;
                }

                $suffix = sprintf('.%s',$this->loaderClassSuffix);
                $pathName = str_replace([$path,'/',$suffix],['','\\',''],$pathName);
                $className = sprintf('%s%s',$ns,$pathName);

                if(!class_exists($className)){
                    continue;
                }
                //$ns = ‌SwoftRewrite\Annotation 命名空间（loaderclass记录的）
                //$className = ‌SwoftRewrite\Annotation\Contract\LoaderInterface
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
        $oneClassAnnotation = $this->
    }

    private function parseOneClassAnnotation(\ReflectionClass $reflectionClass): array
    {
        //注解 读取器。
        $reader = new AnnotationReader
    }
}