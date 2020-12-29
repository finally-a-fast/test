<?php

declare(strict_types=1);

namespace Faf\TemplateEngine\Elements;

use Faf\TemplateEngine\Helpers\ParserElement;

/**
 * Class StripTagsAllowableTags
 *
 * @package Faf\TemplateEngine\Elements
 */
class StripTagsAllowableTags extends ParserElement
{
    /**
     * {@inheritdoc}
     */
    public function name(): string
    {
        return 'strip-tags-allowable-tags';
    }

    /**
     * {@inheritdoc}
     */
    public function description(): string
    {
        return 'You can use the optional second parameter to specify tags which should not be stripped.';
    }

    /**
     * {@inheritdoc}
     */
    public function allowedParents(): ?array
    {
        return [StripTags::class];
    }

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        return $this->content;
    }
}
