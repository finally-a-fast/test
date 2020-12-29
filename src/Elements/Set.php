<?php

declare(strict_types=1);

namespace Faf\TemplateEngine\Elements;

use Exception;
use Faf\TemplateEngine\Helpers\ElementSetting;
use Faf\TemplateEngine\Helpers\DataHelper;
use Faf\TemplateEngine\Helpers\ParserElement;
use Yiisoft\Validator\Rule\Required;

/**
 * Class Set
 *
 * @package fafcms\parser\elements
 */
class Set extends ParserElement
{
    /**
     * {@inheritdoc}
     */
    public function name(): string
    {
        return 'set';
    }

    /**
     * {@inheritdoc}
     */
    public function description(): string
    {
        return 'Sets a value.';
    }

    /**
     * {@inheritdoc}
     */
    public function elementSettings(): array
    {
        return [
            new ElementSetting([
                'name' => 'params',
                'label' => 'Params',
                'element' => Param::class,
                'rawData' => true,
                'content' => true,
                'attributeNameAsKey' => true,
                'multiple' => true,
                'multipleAttributeExpression' => '/^(.*)?$/i',
                'rules' => [
                    new Required(),
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
        foreach ($this->data['params'] as $name => $value) {
            if ($value instanceof DataHelper) {
                $name  = $value->name;
                $value = $value->value;
            }

            if ($name === null) {
                throw new Exception('To set data the name attribute is required.');
            }

            $this->parser->setAttributeData($name, $value);
        }
    }
}
