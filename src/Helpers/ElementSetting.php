<?php

declare(strict_types=1);

namespace Faf\TemplateEngine\Helpers;

/**
 * Class ElementSetting
 *
 * @package fafcms\parser
 */
class ElementSetting extends BaseObject
{
    /**
     * @var string
     */
    public string $name;

    /**
     * @var array
     */
    public array $aliases = [];

    /**
     * @var string
     */
    public string $label;

    /**
     * @var string|null
     */
    public ?string $element = null;

    /**
     * @var bool
     */
    public bool $content = false;

    /**
     * @var bool
     */
    public bool $multiple = false;

    /**
     * @var bool
     */
    public bool $rawData = false;

    /**
     * @var bool
     */
    public bool $safeData = true;

    /**
     * @var string
     */
    public string $multipleAttributeExpression = '/^{{name}}(-(.*))?$/i';

    /**
     * @var bool
     */
    public bool $attributeNameAsKey = false;

    /**
     * @var array
     */
    public array $rules = [];

    /**
     * @var mixed
     */
    public $defaultValue;

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     *
     * @return $this
     */
    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return array
     */
    public function getAliases(): array
    {
        return $this->aliases;
    }

    /**
     * @param array $aliases
     *
     * @return $this
     */
    public function setAliases(array $aliases): self
    {
        $this->aliases = $aliases;
        return $this;
    }

    /**
     * @return string
     */
    public function getLabel(): string
    {
        return $this->label;
    }

    /**
     * @param string $label
     *
     * @return $this
     */
    public function setLabel(string $label): self
    {
        $this->label = $label;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getElement(): ?string
    {
        return $this->element;
    }

    /**
     * @param string|null $element
     *
     * @return $this
     */
    public function setElement(?string $element): self
    {
        $this->element = $element;
        return $this;
    }

    /**
     * @return bool
     */
    public function isContent(): bool
    {
        return $this->content;
    }

    /**
     * @param bool $content
     *
     * @return $this
     */
    public function setContent(bool $content): self
    {
        $this->content = $content;
        return $this;
    }

    /**
     * @return bool
     */
    public function getMultiple(): bool
    {
        return $this->multiple;
    }

    /**
     * @param bool $multiple
     *
     * @return $this
     */
    public function setMultiple(bool $multiple): self
    {
        $this->multiple = $multiple;
        return $this;
    }

    /**
     * @return bool
     */
    public function getRawData(): bool
    {
        return $this->rawData;
    }

    /**
     * @param bool $rawData
     *
     * @return $this
     */
    public function setRawData(bool $rawData): self
    {
        $this->rawData = $rawData;
        return $this;
    }

    /**
     * @return bool
     */
    public function getSafeData(): bool
    {
        return $this->safeData;
    }

    /**
     * @param bool $safeData
     *
     * @return $this
     */
    public function setSafeData(bool $safeData): self
    {
        $this->safeData = $safeData;
        return $this;
    }

    /**
     * @return string
     */
    public function getMultipleAttributeExpression(): string
    {
        return $this->multipleAttributeExpression;
    }

    /**
     * @param string $multipleAttributeExpression
     *
     * @return $this
     */
    public function setMultipleAttributeExpression(string $multipleAttributeExpression): self
    {
        $this->multipleAttributeExpression = $multipleAttributeExpression;
        return $this;
    }

    /**
     * @return bool
     */
    public function getAttributeNameAsKey(): bool
    {
        return $this->attributeNameAsKey;
    }

    /**
     * @param bool $attributeNameAsKey
     *
     * @return $this
     */
    public function setAttributeNameAsKey(bool $attributeNameAsKey): self
    {
        $this->attributeNameAsKey = $attributeNameAsKey;
        return $this;
    }

    /**
     * @return array
     */
    public function getRules(): array
    {
        return $this->rules;
    }

    /**
     * @param array $rules
     *
     * @return $this
     */
    public function setRules(array $rules): self
    {
        $this->rules = $rules;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDefaultValue()
    {
        return $this->defaultValue;
    }

    /**
     * @param mixed $defaultValue
     *
     * @return $this
     */
    public function setDefaultValue($defaultValue): self
    {
        $this->defaultValue = $defaultValue;
        return $this;
    }
}
