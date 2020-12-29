<?php

declare(strict_types=1);

namespace Faf\TemplateEngine\Helpers;

use Faf\TemplateEngine\Parser;
use IvoPetkov\HTML5DOMElement;

/**
 * Class ParserElement
 * @package fafcms\parser
 */
abstract class ParserElement extends BaseObject
{
    /**
     * @var Parser
     */
    protected Parser $parser;

    /**
     * @var bool
     */
    protected bool $prefixParserName = true;

    /**
     * @var bool specify if content should be parsed. If set to false content will return raw html.
     */
    protected bool $parseContent = true;

    /**
     * @var bool specify if parsed content should return raw data.
     */
    protected bool $contentAsRawData = false;

    /**
     * @var array Parsed data of element
     */
    protected array $data = [];

    /**
     * @var array Raw attributes of element
     */
    protected array $attributes = [];

    /**
     * @var array Raw child elements of element
     */
    protected array $elements = [];

    /**
     * @var mixed Content of element
     */
    protected $content;

    //region getter and setter
    /**
     * @return Parser
     */
    public function getParser(): Parser
    {
        return $this->parser;
    }

    /**
     * @param Parser $parser
     *
     * @return $this
     */
    public function setParser(Parser $parser): self
    {
        $this->parser = $parser;
        return $this;
    }

    protected HTML5DOMElement $domNode;

    /**
     * @return HTML5DOMElement
     */
    public function getDomNode(): HTML5DOMElement
    {
        return $this->domNode;
    }

    /**
     * @param HTML5DOMElement $domNode
     *
     * @return $this
     */
    public function setDomNode(HTML5DOMElement $domNode): self
    {
        $this->domNode = $domNode;
        return $this;
    }

    /**
     * @return bool
     */
    public function isPrefixParserName(): bool
    {
        return $this->prefixParserName;
    }

    /**
     * @param bool $prefixParserName
     *
     * @return $this
     */
    public function setPrefixParserName(bool $prefixParserName): self
    {
        $this->prefixParserName = $prefixParserName;
        return $this;
    }

    /**
     * @return bool
     */
    public function getParseContent(): bool
    {
        return $this->parseContent;
    }

    /**
     * @return bool
     */
    public function getContentAsRawData(): bool
    {
        return $this->contentAsRawData;
    }

    /**
     * @return array
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * @param array $data
     *
     * @return $this
     */
    public function setData(array $data): self
    {
        $this->data = $data;
        return $this;
    }

    /**
     * @return array
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }

    /**
     * @param array $attributes
     *
     * @return $this
     */
    public function setAttributes(array $attributes): self
    {
        $this->attributes = $attributes;
        return $this;
    }

    /**
     * @return array
     */
    public function getElements(): array
    {
        return $this->elements;
    }

    /**
     * @param array $elements
     *
     * @return $this
     */
    public function setElements(array $elements): self
    {
        $this->elements = $elements;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @param $content
     *
     * @return $this
     */
    public function setContent($content): self
    {
        $this->content = $content;

        return $this;
    }
    //endregion getter and setter


    /**
     * @return ElementSetting[]
     */
    public function elementSettings(): array
    {
        return [];
    }

    /**
     * @return string
     */
    abstract public function name(): string;

    /**
     * @return array
     */
    public function aliases(): array
    {
        return [];
    }

    /**
     * @return string
     */
    abstract public function description(): string;

    /**
     * @return array
     */
    public function editorOptions(): array
    {
        return ['tag' => 'div'];
    }

    /**
     * @return array|null
     */
    public function allowedTypes(): ?array
    {
        return null;
    }

    /**
     * @return array|null
     */
    public function allowedParents(): ?array
    {
        return null;
    }

    /**
     * Initializes parser element and executes bootstrap components.
     * This method is called by parser component after loading available parser elements.
     * If you override this method, make sure you also call the parent implementation.
     */
    public function bootstrap(): void
    {

    }

    /**
     * @return string
     */
    public function tagName(): string
    {
        return ($this->prefixParserName ? $this->parser->name . '-' : '') . $this->name();
    }

    /**
     * @var array|null
     */
    private ?array $tagNameAliases = null;

    /**
     * @return array
     */
    public function tagNameAliases(): array
    {
        if ($this->tagNameAliases === null) {
            $this->tagNameAliases = [];

            foreach ($this->aliases() as $alias) {
                $this->tagNameAliases[] = ($this->prefixParserName ? $this->parser->name . '-' : '') . $alias;
            }
        }

        return $this->tagNameAliases;
    }

    /**
     * @return mixed|void
     */
    abstract public function run();
}
