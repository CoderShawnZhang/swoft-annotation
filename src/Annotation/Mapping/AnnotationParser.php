<?php
/**
 * Created by PhpStorm.
 * User: zhanghongbo
 * Date: 2019/6/3
 * Time: 下午3:14
 */
namespace SwoftRewrite\Annotation\Annotation\Mapping;

/**
 * Class AnnotationParser
 *
 * @Annotation
 * @package SwoftRewrite\Annotation\Annota\Mapping
 */
final class AnnotationParser
{
    private $annotation = '';

    public function __construct(array $values)
    {
        if(isset($values['value'])){
            $this->annotation = $values['value'];
        }
        if(isset($values['annotation'])){
            $this->annotation = $values['annotation'];
        }
    }

    public function getAnnotation()
    {
        return $this->annotation;
    }
}