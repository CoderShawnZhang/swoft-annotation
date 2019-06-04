<?php
/**
 * Created by PhpStorm.
 * User: zhanghongbo
 * Date: 2019/6/4
 * Time: 上午11:08
 */

namespace SwoftTest\Annotation\Testing\Annotation\Mapping;

/**
 * @Annotation
 * @Target("CLASS")
 * @Attributes({
 *    @Attribute("name",type="string")
 * })
 */
class DemoClass
{
    private $name;

    public function __construct(array $values)
    {
        if(isset($values['value'])){
            $this->name = $values['value'];
        }
        if(isset($values['name'])){
            $this->name = $values['name'];
        }
    }

    public function setName(string $name){
        $this->name = $name;
    }

    public function getName()
    {
        return $this->name;
    }
}