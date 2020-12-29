<?php

declare(strict_types=1);

namespace Faf\TemplateEngine\Elements;

use Faf\TemplateEngine\Helpers\ParserElement;

/**
 * Class TagName
 *
 * @package Faf\TemplateEngine\Elements
 */
class TagName extends ParserElement
{
    /**
     * {@inheritdoc}
     */
    public function name(): string
    {
        return 'tag-name';
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
        return [Tag::class];
    }

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        return $this->content;
    }
}
