<?php
/**
 * Created by PhpStorm.
 * User: zhanghongbo
 * Date: 2019/6/4
 * Time: 下午4:06
 */

namespace SwoftRewrite\Annotation\Annotation\Parser;

abstract class Parser implements ParserInterface
{
    /**
     * Class annotation
     */
    public const TYPE_CLASS = 1;

    /**
     * Property annotation
     */
    public const TYPE_PROPERTY = 2;

    /**
     * Method annotation
     */
    public const TYPE_METHOD = 3;

    protected $className = '';
    protected $reflectClass;
    protected $classAnnotations = [];
    protected $methodName = '';

    public function __construct(string $className,\ReflectionClass $reflectionClass,array $classAnnotations)
    {
        $this->className = $className;
        $this->reflectClass = $reflectionClass;
        $this->classAnnotations = $classAnnotations;
    }

    public function setMethodName(string $methodName)
    {
        $this->methodName = $methodName;
    }
}