<?php
/**
 * Created by PhpStorm.
 * User: zhanghongbo
 * Date: 2019/6/3
 * Time: 上午11:59
 */

namespace SwoftRewrite\Annotation;


use SwoftRewrite\Annotation\Contract\LoaderInterface;

class AutoLoader implements LoaderInterface
{
    public function getPrefixDirs()
    {
        return [
            __NAMESPACE__ => __DIR__,
        ];
    }
}