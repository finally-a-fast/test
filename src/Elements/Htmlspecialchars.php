<?php

declare(strict_types=1);

namespace Faf\TemplateEngine\Elements;

use Faf\TemplateEngine\Helpers\ElementSetting;
use Faf\TemplateEngine\Helpers\ParserElement;
use Yiisoft\Validator\Rule\Boolean;
use Yiisoft\Validator\Rule\Required;

/**
 * Class Htmlspecialchars
 *
 * @package Faf\TemplateEngine\Elements
 */
class Htmlspecialchars extends ParserElement
{
    /**
     * {@inheritdoc}
     */
    public function name(): string
    {
        return 'htmlspecialchars';
    }

    /**
     * {@inheritdoc}
     */
    public function description(): string
    {
        return 'Convert special characters to HTML entities';
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
                'element' => HtmlspecialcharsString::class,
                'content' => true
            ])
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        //TODO $flags = ENT_COMPAT | ENT_HTML401, $encoding = 'UTF-8', $double_encode = true
        return htmlspecialchars($this->data['string']);
    }
}
