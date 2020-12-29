<?php

declare(strict_types=1);

namespace Faf\TemplateEngine\Elements;

use Faf\TemplateEngine\Helpers\ParserElement;

/**
 * Class HtmlspecialcharsString
 *
 * @package Faf\TemplateEngine\Elements
 */
class HtmlspecialcharsString extends ParserElement
{
    /**
     * {@inheritdoc}
     */
    public function name(): string
    {
        return 'htmlspecialchars-string';
    }

    /**
     * {@inheritdoc}
     */
    public function description(): string
    {
        return 'The string being converted.';
    }

    /**
     * {@inheritdoc}
     */
    public function allowedParents(): ?array
    {
        return [Htmlspecialchars::class];
    }

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        return $this->content;
    }
}
