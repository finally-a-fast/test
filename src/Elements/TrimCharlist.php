<?php

declare(strict_types=1);

namespace Faf\TemplateEngine\Elements;

use Faf\TemplateEngine\Helpers\ParserElement;

/**
 * Class TrimCharlist
 *
 * @package Faf\TemplateEngine\Elements
 */
class TrimCharlist extends ParserElement
{
    /**
     * {@inheritdoc}
     */
    public function name(): string
    {
        return 'trim-charlist';
    }

    /**
     * {@inheritdoc}
     */
    public function description(): string
    {
        return 'The stripped characters that you want to be stripped. With .. you can specify a range of characters.';
    }

    /**
     * {@inheritdoc}
     */
    public function allowedParents(): ?array
    {
        return [Trim::class];
    }

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        return $this->content;
    }
}
