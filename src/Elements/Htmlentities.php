<?php

declare(strict_types=1);

namespace Faf\TemplateEngine\Elements;

use Faf\TemplateEngine\Helpers\ElementSetting;
use Faf\TemplateEngine\Helpers\ParserElement;
use Yiisoft\Validator\Rule\Boolean;
use Yiisoft\Validator\Rule\Required;

/**
 * Class Htmlentities
 *
 * @package Faf\TemplateEngine\Elements
 */
class Htmlentities extends ParserElement
{
    /**
     * {@inheritdoc}
     */
    public function name(): string
    {
        return 'htmlentities';
    }

    /**
     * {@inheritdoc}
     */
    public function description(): string
    {
        return 'Convert all applicable characters to HTML entities';
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
                'element' => HtmlentitiesString::class,
                'content' => true
            ])
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        //TODO $quote_style = null, $charset = null, $double_encode = true
        return htmlentities($this->data['string']);
    }
}
