<?php

declare(strict_types=1);

namespace Faf\TemplateEngine\Elements;

use Faf\TemplateEngine\Helpers\ElementSetting;
use Faf\TemplateEngine\Helpers\ParserElement;
use IntlDateFormatter;
use Yiisoft\Validator\Rule\Required;

/**
 * Class Loop
 *
 * @package Faf\TemplateEngine\Elements
 */
class Loop extends ParserElement
{
    /**
     * {@inheritdoc}
     */
    public function name(): string
    {
        return 'loop';
    }

    /**
     * {@inheritdoc}
     */
    public function aliases(): array
    {
        return ['for', 'foreach'];
    }

    /**
     * {@inheritdoc}
     */
    public function description(): string
    {
        return 'Loops through an array.';
    }

    /**
     * {@inheritdoc}
     */
    public function elementSettings(): array
    {
        return [
            new ElementSetting([
                'name' => 'each',
                'aliases' => [],
                'label' => 'Each',
                'safeData' => false,
                'element' => LoopEach::class,
                'rules' => [
                    new Required(),
                ]
            ]),
            new ElementSetting([
                'name' => 'as',
                'label' => 'As',
                'element' => LoopAs::class,
                'rules' => [
                    new Required(),
                ]
            ]),
            new ElementSetting([
                'name' => 'body',
                'label' => 'Body',
                'element' => LoopBody::class,
                'content' => true
            ]),
            new ElementSetting([
                'name' => 'wrap-tag',
                'label' => 'Wrap tag',
                'element' => TagName::class,
                'defaultValue' => 'div',
                'rules' => [
                    new Required(),
                ]
            ]),
            new ElementSetting([
                'name' => 'wrap-attributes',
                'label' => 'Wrap attributes',
                'element' => TagAttribute::class,
                'rawData' => true,
                'attributeNameAsKey' => true,
                'multiple' => true,
                'multipleAttributeExpression' => '/^wrap-tag-(.*)?$/i'
            ]),
            new ElementSetting([
                'name' => 'wrap-step',
                'label' => 'Wrap step',
                //'element' => TagBody::class,
            ]),
        ];
    }

    /**
     * {@inheritdoc}
     * @throws \Exception
     */
    public function run()
    {
        $each = $this->data['each'];
        $as = $this->data['as'];
        $body = $this->data['body'];
        $wrapStep = $this->data['wrap-step'];
        $wrapTag = $this->data['wrap-tag'];
        $wrapOptions = $this->data['wrap-attributes'];

        $result = '';

        $loopDatas = $this->parser->getRawValue($each);

        if ($loopDatas !== null) {
            $data = $this->parser->data[$as] ?? null;
            $numericIndex = 0;
            $wrapStore = '';
            $itemCount = count($loopDatas) - 1;

            foreach ($loopDatas as $loopIndex => $loopData) {
                $currentIndex = ((is_numeric($loopIndex)) ? $loopIndex + 1 : $loopIndex);
                $this->parser->data[$each . '.$$index'] = $currentIndex;
                $this->parser->data[$as . '.$$index'] = $currentIndex;
                $this->parser->data[$each . '.$$numericIndex'] = $numericIndex;
                $this->parser->data[$as . '.$$numericIndex'] = $numericIndex;
                $this->parser->data[$as] = $loopData;

                if ($wrapStep !== null && $numericIndex % $wrapStep === 0) {
                    $wrapStore = '';
                }

                $childResult = $this->parser->parseElements($body, $this->parser->getCurrentTagName());

                if ($wrapStep !== null) {
                    $wrapStore .= $childResult;
                } else {
                    $result .= $childResult;
                }

                if ($wrapStep !== null && ($numericIndex % $wrapStep === $wrapStep - 1 || $numericIndex === $itemCount)) {
                    $result .= $this->parser->htmlTag($wrapTag, $wrapStore, $wrapOptions, 'wrap-tag-');
                }

                $numericIndex++;
            }

            $this->data[$as] = $data;
        }

        return $result;
    }
}
