<?php

declare(strict_types=1);

namespace Faf\TemplateEngine\Elements;

use Faf\TemplateEngine\Helpers\ParserElement;

/**
 * Class HtmlspecialcharsDecodeString
 *
 * @package Faf\TemplateEngine\Elements
 */
class HtmlspecialcharsDecodeString extends ParserElement
{
    /**
     * {@inheritdoc}
     */
    public function name(): string
    {
        return 'htmlspecialchars-decode-string';
    }

    /**
     * {@inheritdoc}
     */
    public function description(): string
    {
        return 'The string to decode';
    }

    /**
     * {@inheritdoc}
     */
    public function allowedParents(): ?array
    {
        return [HtmlspecialcharsDecode::class];
    }

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        return $this->content;
    }
}
