<?php

declare(strict_types=1);

namespace Faf\TemplateEngine\Elements;

use Faf\TemplateEngine\Helpers\ElementSetting;
use Faf\TemplateEngine\Helpers\ParserElement;

/**
 * Class ConditionalStatementConditionStartsWith
 *
 * @package Faf\TemplateEngine\Elements
 */
class ConditionalStatementConditionStartsWith extends ParserElement
{
    /**
     * {@inheritdoc}
     */
    public function name(): string
    {
        return 'conditional-statement-condition-starts-with';
    }

    /**
     * {@inheritdoc}
     */
    public function aliases(): array
    {
        return ['condition-starts-with', 'starts-with'];
    }

    /**
     * {@inheritdoc}
     */
    public function description(): string
    {
        return 'Returns true if param 1 starts with param 2.';
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

        return (strpos($params[0], $params[1]) === 0);
    }
}
