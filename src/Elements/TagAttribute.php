<?php

declare(strict_types=1);

namespace Faf\TemplateEngine\Elements;

use Faf\TemplateEngine\Helpers\ElementSetting;
use Faf\TemplateEngine\Helpers\DataHelper;
use Faf\TemplateEngine\Helpers\ParserElement;
use Yiisoft\Validator\Rule\Boolean;
use Yiisoft\Validator\Rule\Required;

/**
 * Class TagAttribute
 *
 * @package Faf\TemplateEngine\Elements
 */
class TagAttribute extends ParserElement
{
    /**
     * {@inheritdoc}
     */
    public function name(): string
    {
        return 'tag-attribute';
    }

    /**
     * {@inheritdoc}
     */
    public function description(): string
    {
        return 'Gets a tag attribute.';
    }

    /**
     * {@inheritdoc}
     */
    public function elementSettings(): array
    {
        return [
            new ElementSetting([
                'name' => 'name',
                'label' => 'Name',
                'element' => TagAttributeName::class
            ]),
            new ElementSetting([
                'name' => 'value',
                'label' => 'Value',
                'element' => TagAttributeValue::class,
                'content' => true,
            ]),
            new ElementSetting([
                'name' => 'empty',
                'label' => 'Empty',
                'element' => TagAttributeEmpty::class,
                'defaultValue' => false,
                'rules' => [
                    new Required(),
                    new Boolean()
                ]
            ]),
        ];
    }

    /**
     * {@inheritdoc}
     * @throws \Exception
     */
    public function run()
    {
        return new DataHelper([
            'name' => $this->data['name'],
            'value' => $this->data['value'],
            'keepEmpty' => $this->data['empty']
        ]);
    }
}
