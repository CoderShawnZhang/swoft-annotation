<?php
/**
 * Created by PhpStorm.
 * User: zhanghongbo
 * Date: 2019/6/3
 * Time: 下午12:02
 */
namespace SwoftRewrite\Annotation\Contract;

/**
 * Interface LoaderInterface
 * @package SwoftRewrite\Annotation\Contract
 */
interface LoaderInterface
{
    /**
     * @return mixed
     */
    public function getPrefixDirs();
}