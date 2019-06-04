<?php
/**
 * Created by PhpStorm.
 * User: zhanghongbo
 * Date: 2019/6/4
 * Time: 上午11:07
 */

namespace SwoftTest\Annotation\Testing;


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