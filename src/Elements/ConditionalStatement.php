<?php

declare(strict_types=1);

namespace Faf\TemplateEngine\Elements;

use Faf\TemplateEngine\Helpers\DataHelper;
use Faf\TemplateEngine\Helpers\ElementSetting;
use Faf\TemplateEngine\Helpers\ParserElement;

/**
 * Class ConditionalStatement
 *
 * @package Faf\TemplateEngine\Elements
 */
class ConditionalStatement extends ParserElement
{
    /**
     * {@inheritdoc}
     */
    public function name(): string
    {
        return 'conditional-statement';
    }

    /**
     * {@inheritdoc}
     */
    public function aliases(): array
    {
        return ['if'];
    }

    /**
     * {@inheritdoc}
     */
    public function description(): string
    {
        return 'Expressions which gets evaluated.';
    }

    /**
     * {@inheritdoc}
     */
    public function elementSettings(): array
    {
        return [
            new ElementSetting([
               'name' => 'condition',
               'label' => 'Condition',
               'element' => ConditionalStatementCondition::class,
               'rawData' => true,
           ]),
           new ElementSetting([
               'name' => 'then',
               'label' => 'Then',
               'element' => ConditionalStatementThen::class,
           ]),
           new ElementSetting([
               'name' => 'else',
               'label' => 'Else',
               'element' => ConditionalStatementElse::class,
           ]),
        ];
    }

    /**
     * {@inheritdoc}
     * @throws \Exception
     */
    public function run()
    {
        $conditions = $this->data['condition'];

        if (!is_array($conditions)) {
            $conditions = [$conditions];
        }

        if (self::checkConditionArray('and', $conditions)) {
            $content = $this->data['then'];
        } else {
            $content = $this->data['else'];
        }

        if (is_string($content)) {
            return $this->parser->parseElements($content, $this->tagName());
        }

        return $content;
    }

    /**
     * @param string $type
     * @param array  $conditions
     *
     * @return bool
     */
    public static function checkConditionArray(string $type, array $conditions): bool
    {
        $result = false;

        foreach ($conditions as $condition) {
            if ($condition) {
                $result = true;

                if ($type === 'or') {
                    break;
                }
            } elseif ($type === 'and') {
                $result = false;
                break;
            }
        }

        return $result;
    }

    /**
     * @param array $rawParams
     *
     * @return array
     */
    public static function getParams(array $rawParams): array
    {
        $params = [];

        foreach ($rawParams as $index => $value) {
            if ($value instanceof DataHelper) {
                $value = $value->value;
            }

            $params[$index] = $value;
        }

        return $params;
    }
}
