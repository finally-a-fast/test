<?php

declare(strict_types=1);

namespace Faf\TemplateEngine\Elements;

use Faf\TemplateEngine\Helpers\ParserElement;

/**
 * Class TagAttributeName
 *
 * @package Faf\TemplateEngine\Elements
 */
class TagAttributeName extends ParserElement
{
    /**
     * {@inheritdoc}
     */
    public function name(): string
    {
        return 'tag-attribute-name';
    }

    /**
     * {@inheritdoc}
     */
    public function description(): string
    {
        return 'The tag name.';
    }

    /**
     * {@inheritdoc}
     */
    public function allowedParents(): ?array
    {
        return [TagAttribute::class];
    }

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        return $this->parser->fullTrim($this->content);
    }
}
