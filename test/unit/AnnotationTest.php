<?php
/**
 * Created by PhpStorm.
 * User: zhanghongbo
 * Date: 2019/6/4
 * Time: 上午11:14
 */

namespace SwoftTest\Annotation\Unit;


use PHPUnit\Framework\TestCase;
use SwoftRewrite\Annotation\AnnotationRegister;
use SwoftRewrite\Annotation\Test\AnnotationDemo;
use SwoftTest\Annotation\Testing\Annotation\Mapping\DemoClass;
use SwoftTest\Annotation\Testing\DemoAnnotation;


class AnnotationTest extends TestCase
{
    public function testInit()
    {
        AnnotationRegister::load(
            [
                'onlyNamespaces' => [
                    'SwoftTest\\Annotation\\Testing\\'
                ],
            ]
        );
        $this->assertTrue(true);
    }

    /**
     * @depends testInit
     */
    public function testAnnotationClass()
    {
        $testAnnotationClassFile = 'SwoftTest\Annotation\Testing';
        $testAnnotationText = '浩克出击';

        $annotations = AnnotationRegister::getAnnotations();
        $demoAnnotation = $annotations[$testAnnotationClassFile][DemoAnnotation::class] ?? [];
        $this->assertTrue(!empty($demoAnnotation));

        $this->assertTrue(isset($demoAnnotation['reflection']));

        $AnnotationClassName = [
            DemoClass::class
        ];
        foreach($demoAnnotation['annotation'] as $annotation){
            self::assertTrue(in_array(get_class($annotation),$AnnotationClassName));
            if($annotation instanceof DemoClass){
                $this->assertEquals($annotation->getName(),$testAnnotationText);
            }
        }
    }
}