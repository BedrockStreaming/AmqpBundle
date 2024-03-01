<?php

$finder = PhpCsFixer\Finder::create()
    ->in([
        __DIR__.'/src',
        __DIR__.'/tests'
    ]);

$config = new class() extends PhpCsFixer\Config {
    public function __construct()
    {
        parent::__construct('Bedrock Streaming');

        $this->setRiskyAllowed(true);
    }

    public function getRules(): array
    {
        return array_merge((new M6Web\CS\Config\BedrockStreaming())->getRules(), [
            'no_unreachable_default_argument_value' => true,
            'trailing_comma_in_multiline' => [
                'after_heredoc' => true,
                'elements' => ['arrays', 'arguments', 'parameters'],
            ],
            'native_function_invocation' => [
                'include' => ['@compiler_optimized']
            ],
            'simplified_null_return' => false,
            'void_return' => true,
            'phpdoc_order' => true,
            'phpdoc_types_order' => false,
            'no_superfluous_phpdoc_tags' => true,
            'php_unit_test_case_static_method_calls' => [
                'call_type' => 'static',
            ],
            'yoda_style' => [
                'equal' => false,
                'identical' => false,
                'less_and_greater' => false
            ],
        ]);
    }
};

$config
    ->setFinder($finder)
    ->setCacheFile('var/cache/tools/.php-cs-fixer.cache');

return $config;
