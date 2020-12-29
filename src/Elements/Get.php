<?php

declare(strict_types=1);

namespace Faf\TemplateEngine\Elements;

use Faf\TemplateEngine\Helpers\ElementSetting;
use Faf\TemplateEngine\Helpers\ParserElement;
use Yiisoft\Validator\Rule\InRange;
use Yiisoft\Validator\Rule\Required;

/**
 * Class Get
 *
 * @package fafcms\parser\elements
 */
class Get extends ParserElement
{
    /**
     * {@inheritdoc}
     */
    public function name(): string
    {
        return 'get';
    }

    /**
     * {@inheritdoc}
     */
    public function description(): string
    {
        return 'Gets a value.';
    }

    /**
     * {@inheritdoc}
     */
    public function elementSettings(): array
    {
        return [
            new ElementSetting([
                'name' => 'format',
                'label' => 'Format',
                'element' => GetFormat::class,
                'defaultValue' => 'raw',
                'rules' => [
                    new Required(),
                    new InRange(['json', 'raw', 'serialize']),
                ]
            ]),
            new ElementSetting([
                'name' => 'params',
                'label' => 'Params',
                'element' => Param::class,
                'rawData' => true,
                'content' => true,
                'attributeNameAsKey' => true,
                'multiple' => true,
                'multipleAttributeExpression' => '/^(.*)?$/i',
                'rules' => [
                    new Required(),
                ]
            ]),
        ];
    }

    /**
     * @return array|false|mixed|string
     * @throws \JsonException
     * @throws \Exception
     */
    public function run()
    {
        $params = $this->data['params'];
        unset($params['format']);

        if (count($params) <= 1) {
            $params = reset($params);
        }

        if ($this->data['format'] === 'json') {
            $params = json_encode($params, JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        } elseif ($this->data['format'] === 'serialize') {
            $params = serialize($params);
        }

        return $params;
    }
}
