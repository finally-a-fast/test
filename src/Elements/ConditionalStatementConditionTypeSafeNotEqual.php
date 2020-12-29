<?php

declare(strict_types=1);

namespace Faf\TemplateEngine\Elements;

use Faf\TemplateEngine\Helpers\ElementSetting;
use Faf\TemplateEngine\Helpers\ParserElement;

/**
 * Class ConditionalStatementConditionTypeSafeNotEqual
 *
 * @package Faf\TemplateEngine\Elements
 */
class ConditionalStatementConditionTypeSafeNotEqual extends ParserElement
{
    /**
     * {@inheritdoc}
     */
    public function name(): string
    {
        return 'conditional-statement-condition-type-safe-not-equal';
    }

    /**
     * {@inheritdoc}
     */
    public function aliases(): array
    {
        return ['condition-type-safe-not-equal', 'type-safe-not-equal'];
    }

    /**
     * {@inheritdoc}
     */
    public function description(): string
    {
        return 'Returns true if param 1 is not equal to param 2 or have not the same type';
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

        return ($params[0] !== $params[1]);
    }
}
