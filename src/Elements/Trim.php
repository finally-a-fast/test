<?php

declare(strict_types=1);

namespace Faf\TemplateEngine\Elements;

use Faf\TemplateEngine\Helpers\ElementSetting;
use Faf\TemplateEngine\Helpers\ParserElement;
use Yiisoft\Validator\Rule\Boolean;
use Yiisoft\Validator\Rule\Required;

/**
 * Class Trim
 *
 * @package Faf\TemplateEngine\Elements
 */
class Trim extends ParserElement
{
    /**
     * {@inheritdoc}
     */
    public function name(): string
    {
        return 'trim';
    }

    /**
     * {@inheritdoc}
     */
    public function description(): string
    {
        return 'Strip whitespace (or other characters) from the beginning and end of a string';
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
                'element' => TrimString::class,
                'content' => true
            ]),
            new ElementSetting([
                'name' => 'charlist',
                'label' => 'Charlist',
                'element' => TrimCharlist::class,
                'defaultValue' => " \t\n\r\0\x0B",
            ]),
            new ElementSetting([
                'name' => 'full-trim',
                'label' => 'Full trim',
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
     */
    public function run()
    {
        if ($this->data['full-trim']) {
            return $this->parser->fullTrim($this->data['string']);
        }

        return trim($this->data['string'], $this->data['charlist']);
    }
}
