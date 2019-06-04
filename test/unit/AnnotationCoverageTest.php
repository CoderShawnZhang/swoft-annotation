<?php
/**
 * Created by PhpStorm.
 * User: zhanghongbo
 * Date: 2019/6/4
 * Time: 下午3:07
 */

namespace SwoftTest\Annotation\Unit;


use PHPUnit\Framework\TestCase;
use SwoftRewrite\Annotation\AnnotationRegister;
use SwoftRewrite\Annotation\Resource\AnnotationResource;
use SwoftRewrite\Stdlib\Helper\ComposerHelper;

class AnnotationCoverageTest extends TestCase
{
    public function testA()
    {
        $testIsExcludedPsr4Prefix_ns = 'PHPUnit\\';
        $testIsExcludePsr4Prefix_path = __DIR__ . '/..' . '/myclabs/deep-copy/src/DeepCopy';

        $composerLoader = ComposerHelper::getClassLoader();
        $hasPHPUnit = false;
        foreach($composerLoader->getPrefixesPsr4() as $ns => $path){
            if(strpos($ns,$testIsExcludedPsr4Prefix_ns) === 0){
                $hasPHPUnit = true;
                break;
            }
        }
        if(!$hasPHPUnit){
            $composerLoader->addPsr4($testIsExcludedPsr4Prefix_ns,$testIsExcludePsr4Prefix_path);
            AnnotationRegister::load(
                [
                    'onlyNamespaces' => [
                        'SwoftTest\\Annotation\\Testing\\',
                        'PHPUnit\\'
                    ],
                ]
            );
        }
       self::assertTrue(true);
    }

    public function testB()
    {
        $array = [1,2,3];
        $annotationResource = new AnnotationResource();
        $annotationResource->setDisabledAutoLoaders($array);
        $res = $annotationResource->getDisabledAutoLoaders();
        self::assertEquals($array,$res);
    }
}