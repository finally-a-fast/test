<?php

declare(strict_types=1);

namespace Faf\TemplateEngine\Elements;

use Faf\TemplateEngine\Helpers\ElementSetting;
use Faf\TemplateEngine\Helpers\ParserElement;
use Yiisoft\Validator\Rule\Boolean;
use Yiisoft\Validator\Rule\Required;

/**
 * Class HtmlEntityDecode
 *
 * @package Faf\TemplateEngine\Elements
 */
class HtmlEntityDecode extends ParserElement
{
    /**
     * {@inheritdoc}
     */
    public function name(): string
    {
        return 'html-entity-decode';
    }

    /**
     * {@inheritdoc}
     */
    public function description(): string
    {
        return 'Convert all HTML entities to their applicable characters';
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
                'element' => HtmlEntityDecodeString::class,
                'content' => true
            ])
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        //TODO $quote_style = null, $charset = null
        return html_entity_decode($this->data['string']);
    }
}
