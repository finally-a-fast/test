<?php

declare(strict_types=1);

namespace Faf\TemplateEngine\Elements;

use Faf\TemplateEngine\Helpers\ElementSetting;
use Faf\TemplateEngine\Helpers\ParserElement;
use Yiisoft\Validator\Rule\Required;

/**
 * Class FormatAsTime
 * @package fafcms\parser\elements
 */
class FormatAsTime extends ParserElement
{
    /**
     * {@inheritdoc}
     */
    public function name(): string
    {
        return 'format-as-time';
    }

    /**
     * {@inheritdoc}
     */
    public function aliases(): array
    {
        return ['time'];
    }

    /**
     * {@inheritdoc}
     */
    public function description(): string
    {
        return 'Formats the value as a time.';
    }

    /**
     * {@inheritdoc}
     */
    public function elementSettings(): array
    {
        return [
            new ElementSetting([
                'name' => 'string',
                'aliases' => [
                    'date',
                    'time',
                    'datetime',
                    'value'
                ],
                'label' => 'String',
                'element' => FormatAsTimeString::class,
                'content' => true,
                'defaultValue' => 'NOW',
                'rules' => [
                    new Required(),
                ]
            ]),
            new ElementSetting([
                'name' => 'format',
                'label' => 'Format',
                'element' => FormatAsTimeFormat::class
            ])
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        return $this->parser->formatTime($this->data['string'], $this->data['format']);
    }
}
