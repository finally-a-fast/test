<?php

declare(strict_types=1);

namespace Faf\TemplateEngine\Elements;

use Faf\TemplateEngine\Helpers\ParserElement;

/**
 * Class ConditionalStatementConditionOr
 *
 * @package Faf\TemplateEngine\Elements
 */
class ConditionalStatementConditionOr extends ParserElement
{
    public bool $contentAsRawData = true;

    /**
     * {@inheritdoc}
     */
    public function name(): string
    {
        return 'conditional-statement-condition-or';
    }

    /**
     * {@inheritdoc}
     */
    public function aliases(): array
    {
        return ['condition-or', 'if-condition-or'];
    }

    /**
     * {@inheritdoc}
     */
    public function description(): string
    {
        return 'Returns true if one child condition is true.';
    }

    /**
     * {@inheritdoc}
     */
    public function allowedParents(): ?array
    {
        return [ConditionalStatementCondition::class];
    }

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        return ConditionalStatement::checkConditionArray('or', $this->content);
    }
}
