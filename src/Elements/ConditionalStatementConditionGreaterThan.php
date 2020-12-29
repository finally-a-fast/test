<?php

declare(strict_types=1);

namespace Faf\TemplateEngine\Elements;

use Faf\TemplateEngine\Helpers\ElementSetting;
use Faf\TemplateEngine\Helpers\ParserElement;

/**
 * Class ConditionalStatementConditionGreaterThan
 *
 * @package Faf\TemplateEngine\Elements
 */
class ConditionalStatementConditionGreaterThan extends ParserElement
{
    /**
     * {@inheritdoc}
     */
    public function name(): string
    {
        return 'conditional-statement-condition-greater-than';
    }

    /**
     * {@inheritdoc}
     */
    public function aliases(): array
    {
        return ['condition-greater-than', 'greater-than'];
    }

    /**
     * {@inheritdoc}
     */
    public function description(): string
    {
        return 'Returns true if param 1 greater than param 2.';
    }

    /**
     * {@inheritdoc}
     */
    public function elementSettings(): array
    {
        return [
            new ElementSetting([
                'name' => 'params',
                'label' => 'Params',
                'element' => Param::class,
                'rawData' => true,
                'content' => true,
                'multiple' => true,
                'multipleAttributeExpression' => '/^(.*)?$/i',
            ]),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function allowedParents(): ?array
    {
        return [
            ConditionalStatementCondition::class,
            ConditionalStatementConditionAnd::class,
            ConditionalStatementConditionOr::class
        ];
    }

    /**
     * {@inheritdoc}
     * @return bool|mixed
     */
    public function run()
    {
        $params = ConditionalStatement::getParams($this->data['params']);

        return ($params[0] > $params[1]);
    }
}
