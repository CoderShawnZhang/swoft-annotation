# swoft-annotation

# 单元测试
phpunit test/unit/AnnotationTest.php 

#测试单个函数
 phpunit test/unit/AnnotationTest.php --filter testParser --coverage-html test/codeCoverage/

# 代码覆盖率
phpunit test/unit/AnnotationTest.php --coverage-html test/codeCoverage/


#如何使用
$config = [];
AnnotationRegister::load($config);


/**
 * Class EnumParser
 *
 * @since 2.0
 *
 * @AnnotationParser(annotation=Enum::class)
 */
 
 会自动扫描该类