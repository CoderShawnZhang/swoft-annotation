<?php
/**
 * Created by PhpStorm.
 * User: zhanghongbo
 * Date: 2019/6/4
 * Time: 上午11:08
 */

namespace SwoftTest\Annotation\Testing;

use SwoftTest\Annotation\Testing\Annotation\Mapping\DemoClass;
use SwoftTest\Annotation\Testing\Annotation\Mapping\DemoProperty;
use SwoftTest\Annotation\Testing\Annotation\Mapping\DemoMethod;


/**
 * Class DemoAnnotation
 *
 * @DemoClass(name="浩克出击")
 */
class DemoAnnotation extends DemoAnnotationBase
{
    /**
     * @DemoProperty(name="green")
     *
     * @var string
     */
    private $color = 'green';

    /**
     * @DemoMethod(name="change")
     */
    public function change()
    {

    }
}