<?php

declare(strict_types=1);

namespace Faf\TemplateEngine\Elements;

use Faf\TemplateEngine\Helpers\ElementSetting;
use Faf\TemplateEngine\Helpers\DataHelper;
use Faf\TemplateEngine\Helpers\ParserElement;
use Yiisoft\Validator\Rule\Required;

/**
 * Class Call
 *
 * @package fafcms\parser\elements
 */
class Call extends ParserElement
{
    /**
     * {@inheritdoc}
     */
    public function name(): string
    {
        return 'call';
    }

    /**
     * {@inheritdoc}
     */
    public function aliases(): array
    {
        return ['exec', 'execute'];
    }

    /**
     * {@inheritdoc}
     */
    public function description(): string
    {
        return 'Calls a function.';
    }

    /**
     * {@inheritdoc}
     */
    public function elementSettings(): array
    {
        return [
            new ElementSetting([
                'name' => 'function',
                'aliases' => ['callable', 'name'],
                'label' => 'Function',
                'element' => CallFunction::class,
                'content' => true,
                'rules' => [
                    new Required(),
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
     * {@inheritdoc}
     * @throws \Exception
     */
    public function run()
    {
        $data = null;
        $closure = $this->parser->getAttributeData($this->data['function'], $data, false);

        if ($closure === null) {
            return null;
        }

        $rawParams = $this->data['params'];
        unset($rawParams['function'], $rawParams['callable'], $rawParams['name']);

        //TODO this can get removed
        $params = DataHelper::formatParams($rawParams, false, $this->parser);

        return call_user_func_array($closure, $params);
    }
}
