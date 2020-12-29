<?php

declare(strict_types=1);

namespace Faf\TemplateEngine\Elements;

use Faf\TemplateEngine\Helpers\ElementSetting;
use Faf\TemplateEngine\Helpers\DataHelper;
use Faf\TemplateEngine\Helpers\ParserElement;

/**
 * Class Param
 *
 * @package fafcms\parser\elements
 */
class Param extends ParserElement
{
    /**
     * {@inheritdoc}
     */
    public function name(): string
    {
        return 'param';
    }

    /**
     * {@inheritdoc}
     */
    public function aliases(): array
    {
        return ['param-1', 'param-2'];
    }

    /**
     * {@inheritdoc}
     */
    public function description(): string
    {
        return 'Gets a data param.';
    }

    /**
     * {@inheritdoc}
     */
    public function elementSettings(): array
    {
        return [
            new ElementSetting([
                'name' => 'name',
                'label' => 'Name',
                'element' => ParamName::class
            ]),
            new ElementSetting([
                'name' => 'value',
                'label' => 'Value',
                'element' => ParamValue::class,
                'content' => true,
            ]),
        ];
    }

    /**
     * {@inheritdoc}
     * @throws \Exception
     */
    public function run()
    {
        $this->data['value'] = $this->parser->getRawValue($this->parser->parseElements((string)$this->data['value'], $this->tagName(), true));
        return new DataHelper($this->data);
    }
}
