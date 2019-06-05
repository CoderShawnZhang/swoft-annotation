<?php


require dirname(__DIR__) . '/vendor/autoload.php';

\SwoftRewrite\Annotation\AnnotationRegister::load(
    [
        'onlyNamespaces' => [
            'SwoftTest\\Annotation\\Testing\\'
        ],
    ]
);
echo 222;