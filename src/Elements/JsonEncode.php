<?php

declare(strict_types=1);

namespace Faf\TemplateEngine\Elements;

use Faf\TemplateEngine\Helpers\ElementSetting;
use Faf\TemplateEngine\Helpers\ParserElement;
use Yiisoft\Validator\Rule\Required;

/**
 * Class JsonEncode
 * @package fafcms\parser\elements
 */
class JsonEncode extends ParserElement
{
    /**
     * {@inheritdoc}
     */
    public function name(): string
    {
        return 'json-encode';
    }

    /**
     * {@inheritdoc}
     */
    public function description(): string
    {
        return 'Json encodes defined data';
    }

    /**
     * {@inheritdoc}
     */
    public function elementSettings(): array
    {
        return [
            new ElementSetting([
                'name' => 'data',
                'label' => 'Data',
                'content' => true
            ]),
        ];
    }

    /**
     * {@inheritdoc}
     * @throws \JsonException
     */
    public function run()
    {
        return json_encode($this->data['data'], JSON_THROW_ON_ERROR | JSON_HEX_TAG);
    }
}
