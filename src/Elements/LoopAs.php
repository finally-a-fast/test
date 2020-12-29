<?php

declare(strict_types=1);

namespace Faf\TemplateEngine\Elements;

use Faf\TemplateEngine\Helpers\ParserElement;

/**
 * Class LoopAs
 *
 * @package Faf\TemplateEngine\Elements
 */
class LoopAs extends ParserElement
{
    /**
     * {@inheritdoc}
     */
    public function name(): string
    {
        return 'loop-as';
    }

    /**
     * {@inheritdoc}
     */
    public function description(): string
    {
        return 'The current identifier.';
    }

    /**
     * {@inheritdoc}
     */
    public function allowedParents(): ?array
    {
        return [Loop::class];
    }

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        return $this->content;
    }
}
