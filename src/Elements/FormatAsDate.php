<?php

declare(strict_types=1);

namespace Faf\TemplateEngine\Elements;

use Faf\TemplateEngine\Helpers\ElementSetting;
use Faf\TemplateEngine\Helpers\ParserElement;
use IntlDateFormatter;
use Yiisoft\Validator\Rule\Required;

/**
 * Class FormatAsDate
 *
 * @package Faf\TemplateEngine\Elements
 */
class FormatAsDate extends ParserElement
{
    /**
     * {@inheritdoc}
     */
    public function name(): string
    {
        return 'format-as-date';
    }

    /**
     * {@inheritdoc}
     */
    public function aliases(): array
    {
        return ['date'];
    }

    /**
     * {@inheritdoc}
     */
    public function description(): string
    {
        return 'Formats the value as a date.';
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
                'element' => FormatAsDateString::class,
                'content' => true,
                'defaultValue' => 'NOW',
                'rules' => [
                    new Required(),
                ]
            ]),
            new ElementSetting([
                'name' => 'format',
                'label' => 'Format',
                'element' => FormatAsDateFormat::class,
                'defaultValue' => IntlDateFormatter::MEDIUM,
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
        return $this->parser->formatDate($this->data['string'], $this->data['format']);
    }
}
