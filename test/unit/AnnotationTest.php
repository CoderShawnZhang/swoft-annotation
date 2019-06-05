<?php
/**
 * Created by PhpStorm.
 * User: zhanghongbo
 * Date: 2019/6/4
 * Time: 上午11:14
 */

namespace SwoftTest\Annotation\Unit;


use PHPUnit\Framework\TestCase;
use SwoftRewrite\Annotation\Annotation\Parser\ParserInterface;
use SwoftRewrite\Annotation\AnnotationRegister;
use SwoftRewrite\Annotation\AutoLoader;
use SwoftRewrite\Annotation\Contract\LoaderInterface;
use SwoftRewrite\Annotation\Test\AnnotationDemo;
use SwoftTest\Annotation\Testing\Annotation\Mapping\DemoClass;
use SwoftTest\Annotation\Testing\Annotation\Mapping\DemoMethod;
use SwoftTest\Annotation\Testing\Annotation\Mapping\DemoProperty;
use SwoftTest\Annotation\Testing\Annotation\Parser\DemoClassParser;
use SwoftTest\Annotation\Testing\Annotation\Parser\DemoClassParser1;
use SwoftTest\Annotation\Testing\DemoAnnotation;

// @codeCoverageIgnoreStart
abstract class Parser implements ParserInterface
{

}
// @codeCoverageIgnoreEnd

class AnnotationTest extends TestCase
{
    public $testDir = 'SwoftTest\\Annotation\\Testing\\';
    public function testInit()
    {
        AnnotationRegister::load(
            [
                'onlyNamespaces' => [
                    $this->testDir
                ],
            ]
        );
        $this->assertTrue(true);
    }

    public function testAnnotation1()
    {
        self::assertTrue(true);
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
        $AnnotationPropertyName = [
            DemoProperty::class
        ];
        foreach($demoAnnotation['properties']['color']['annotation'] as $annotation){
            self::assertTrue(in_array(get_class($annotation),$AnnotationPropertyName));
            if($annotation instanceof DemoProperty){
                $this->assertEquals($annotation->getName(),'green');
            }
        }
        $AnnotationMethodName = [
            DemoMethod::class
        ];
        foreach($demoAnnotation['methods']['change']['annotation'] as $annotation){
            self::assertTrue(in_array(get_class($annotation),$AnnotationMethodName));
            if($annotation instanceof DemoMethod){
                $this->assertEquals($annotation->getName(),'change');
            }
        }
    }

    /**
     * @depends testAnnotationClass
     */
    public function testParser()
    {
        $parsers = AnnotationRegister::getParsers();//获取

        $parserClassName = [
            DemoClassParser::class,
            DemoClassParser1::class
        ];

        if (empty($parsers)) {
            $this->assertTrue(false);
        }

        foreach($parsers as $parser){
            $this->assertTrue(in_array($parser,$parserClassName));
        }
    }

    /**
     * @depends testAnnotationClass
     */
    public function testAutoLoader()
    {
        $autoLoader = AnnotationRegister::getAutoLoader($this->testDir);
        $this->assertTrue($autoLoader instanceof LoaderInterface);
    }
}