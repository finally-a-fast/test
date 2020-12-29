<?php

declare(strict_types=1);

namespace Faf\TemplateEngine\Elements;

use Faf\TemplateEngine\Helpers\ElementSetting;
use Faf\TemplateEngine\Helpers\ParserElement;
use Yiisoft\Validator\Rule\Required;

/**
 * Class StrToLower
 * @package fafcms\parser\elements
 */
class StrToLower extends ParserElement
{
    /**
     * {@inheritdoc}
     */
    public function name(): string
    {
        return 'strtolower';
    }

    /**
     * {@inheritdoc}
     */
    public function description(): string
    {
        return 'Make a string lowercase';
    }

    /**
     * {@inheritdoc}
     */
    public function elementSettings(): array
    {
        return [
            new ElementSetting([
                'name' => 'string',
                'label' => 'String',
                'element' => StrToLowerString::class,
                'content' => true,
                'rules' => [
                    new Required(),
                ]
            ])
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        return strtolower($this->data['string']);
    }
}
