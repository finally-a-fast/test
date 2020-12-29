<?php

declare(strict_types=1);

namespace Faf\TemplateEngine\Elements;

use Faf\TemplateEngine\Helpers\ParserElement;

/**
 * Class LoopBody
 *
 * @package Faf\TemplateEngine\Elements
 */
class LoopBody extends ParserElement
{
    public bool $parseContent = false;

    /**
     * {@inheritdoc}
     */
    public function name(): string
    {
        return 'loop-body';
    }

    /**
     * {@inheritdoc}
     */
    public function description(): string
    {
        return 'The content which will be executed for each element.';
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
