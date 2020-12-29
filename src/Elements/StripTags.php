<?php

declare(strict_types=1);

namespace Faf\TemplateEngine\Elements;

use Faf\TemplateEngine\Helpers\ElementSetting;
use Faf\TemplateEngine\Helpers\ParserElement;

/**
 * Class StripTags
 * @package fafcms\parser\elements
 */
class StripTags extends ParserElement
{
    /**
     * {@inheritdoc}
     */
    public function name(): string
    {
        return 'strip-tags';
    }

    /**
     * {@inheritdoc}
     */
    public function description(): string
    {
        return 'Strip HTML and PHP tags from a string';
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
                'element' => StripTagsString::class,
                'content' => true
            ]),
            new ElementSetting([
                'name' => 'allowable-tags',
                'label' => 'Allowable tags',
                'element' => UcWordsDelimiters::class,
            ]),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        return strip_tags($this->data['string'], $this->data['allowable-tags']);
    }
}
