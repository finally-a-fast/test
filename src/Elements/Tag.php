<?php

declare(strict_types=1);

namespace Faf\TemplateEngine\Elements;

use Faf\TemplateEngine\Helpers\ElementSetting;
use Faf\TemplateEngine\Helpers\ParserElement;
use Yiisoft\Validator\Rule\Required;

/**
 * Class Tag
 *
 * @package Faf\TemplateEngine\Elements
 */
class Tag extends ParserElement
{
    /**
     * {@inheritdoc}
     */
    public function name(): string
    {
        return 'tag';
    }

    /**
     * {@inheritdoc}
     */
    public function description(): string
    {
        return 'Creates an html tag.';
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
                'element' => TagName::class,
                'rules' => [
                    new Required(),
                ]
            ]),
            new ElementSetting([
                'name' => 'attributes',
                'label' => 'Attributes',
                'element' => TagAttribute::class,
                'rawData' => true,
                'attributeNameAsKey' => true,
                'multiple' => true,
                'multipleAttributeExpression' => '/^(.*)?$/i',
            ]),
            new ElementSetting([
                'name' => 'body',
                'label' => 'Body',
                'element' => TagBody::class,
            ]),
        ];
    }

    /**
     * {@inheritdoc}
     * @throws \JsonException
     */
    public function run()
    {
        $options = $this->data['attributes'];
        unset($options['name'], $options['body']);

        return $this->parser->htmlTag($this->data['name'], $this->data['body'] ?? '', $options);
    }
}
