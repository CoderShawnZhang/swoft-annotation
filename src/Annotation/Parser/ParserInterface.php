<?php
/**
 * Created by PhpStorm.
 * User: zhanghongbo
 * Date: 2019/6/4
 * Time: 下午4:06
 */

namespace SwoftRewrite\Annotation\Annotation\Parser;


interface ParserInterface
{
    public function parse(int $type,$annotationObject);
}