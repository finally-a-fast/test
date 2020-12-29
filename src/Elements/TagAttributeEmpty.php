<?php

declare(strict_types=1);

namespace Faf\TemplateEngine\Elements;

use Faf\TemplateEngine\Helpers\ParserElement;

/**
 * Class TagAttributeEmpty
 *
 * @package Faf\TemplateEngine\Elements
 */
class TagAttributeEmpty extends ParserElement
{
    /**
     * {@inheritdoc}
     */
    public function name(): string
    {
        return 'tag-attribute-empty';
    }

    /**
     * {@inheritdoc}
     */
    public function description(): string
    {
        return 'Keep empty tag.';
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
        return $this->content;
    }
}
