<?php

declare(strict_types=1);

namespace Faf\TemplateEngine\Elements;

use Faf\TemplateEngine\Helpers\ParserElement;

/**
 * Class StrReplaceSubject
 *
 * @package Faf\TemplateEngine\Elements
 */
class StrReplaceSubject extends ParserElement
{
    /**
     * {@inheritdoc}
     */
    public function name(): string
    {
        return 'str-replace-subject';
    }

    /**
     * {@inheritdoc}
     */
    public function description(): string
    {
        return 'The string or array being searched and replaced on, otherwise known as the haystack.';
    }

    /**
     * {@inheritdoc}
     */
    public function allowedParents(): ?array
    {
        return [StrReplace::class];
    }

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        return $this->content;
    }
}
