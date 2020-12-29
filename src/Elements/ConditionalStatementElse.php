<?php

declare(strict_types=1);

namespace Faf\TemplateEngine\Elements;

use Faf\TemplateEngine\Helpers\ParserElement;

/**
 * Class ConditionalStatementElse
 *
 * @package Faf\TemplateEngine\Elements
 */
class ConditionalStatementElse extends ParserElement
{
    public bool $parseContent = false;

    /**
     * {@inheritdoc}
     */
    public function name(): string
    {
        return 'conditional-statement-else';
    }

    /**
     * {@inheritdoc}
     */
    public function aliases(): array
    {
        return ['else', 'if-else'];
    }

    /**
     * {@inheritdoc}
     */
    public function description(): string
    {
        return 'Part which gets executed if the condition is false.';
    }

    /**
     * {@inheritdoc}
     */
    public function allowedParents(): ?array
    {
        return [ConditionalStatement::class];
    }

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        return $this->content;
    }
}
