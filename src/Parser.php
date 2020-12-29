<?php

declare(strict_types=1);

namespace Faf\TemplateEngine;

use Closure;

use DateTime;
use DateTimeZone;
use Faf\TemplateEngine\Elements\Trim;
use Faf\TemplateEngine\Elements\TrimString;
use Faf\TemplateEngine\Elements\TrimCharlist;
use Faf\TemplateEngine\Helpers\DataHelper;
use IntlCalendar;
use IntlDateFormatter;
use IntlTimeZone;
use IvoPetkov\HTML5DOMElement;
use IvoPetkov\HTML5DOMDocument;
use DOMXPath;

use Faf\TemplateEngine\Elements\{Base64Decode,
    Base64Encode,
    Calc,
    Call,
    CallFunction,
    ConditionalStatement,
    ConditionalStatementCondition,
    ConditionalStatementConditionAnd,
    ConditionalStatementConditionEmpty,
    ConditionalStatementConditionEndsNotWith,
    ConditionalStatementConditionEndsWith,
    ConditionalStatementConditionEqual,
    ConditionalStatementConditionFalse,
    ConditionalStatementConditionGreaterEqualThan,
    ConditionalStatementConditionGreaterThan,
    ConditionalStatementConditionLessEqualThan,
    ConditionalStatementConditionLessThan,
    ConditionalStatementConditionNotEmpty,
    ConditionalStatementConditionOr,
    ConditionalStatementConditionStartsNotWith,
    ConditionalStatementConditionStartsWith,
    ConditionalStatementConditionTrue,
    ConditionalStatementConditionTypeSafeEqual,
    ConditionalStatementConditionTypeSafeNotEqual,
    ConditionalStatementElse,
    ConditionalStatementThen,
    ConditionalStatementConditionNotEqual,
    FormatAsDate,
    FormatAsDateFormat,
    FormatAsDateString,
    FormatAsDatetime,
    FormatAsDatetimeFormat,
    FormatAsDatetimeString,
    FormatAsShortSize,
    FormatAsShortSizeDecimals,
    FormatAsShortSizeValue,
    FormatAsTime,
    FormatAsTimeFormat,
    FormatAsTimeString,
    Get,
    GetFormat,
    Htmlentities,
    HtmlentitiesString,
    HtmlEntityDecode,
    HtmlEntityDecodeString,
    Htmlspecialchars,
    HtmlspecialcharsDecode,
    HtmlspecialcharsDecodeString,
    HtmlspecialcharsString,
    JsonEncode,
    Loop,
    LoopAs,
    LoopBody,
    LoopEach,
    Nl2Br,
    Nl2BrString,
    Param,
    ParamName,
    ParamValue,
    Parse,
    ParseString,
    Round,
    RoundValue,
    Set,
    StripTags,
    StripTagsAllowableTags,
    StripTagsString,
    StrReplace,
    StrReplaceSubject,
    StrToLower,
    StrToLowerString,
    StrToUpper,
    StrToUpperString,
    Tag,
    TagAttribute,
    TagAttributeEmpty,
    TagAttributeName,
    TagAttributeValue,
    TagBody,
    TagName,
    TimeTag,
    UcWords,
    UcWordsDelimiters,
    UcWordsString,
    VarDump};

use Faf\TemplateEngine\Helpers\BaseObject;
use Faf\TemplateEngine\Helpers\ElementSetting;
use Faf\TemplateEngine\Helpers\ParserElement;
use Locale;
use NumberFormatter;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Psr\SimpleCache\CacheInterface;
use Psr\SimpleCache\InvalidArgumentException;
use Exception;
use RuntimeException;
use Yiisoft\Validator\Rules;

class Parser extends BaseObject
{
    public const ROOT = 'root';

    public const MODE_PROD = 0;
    public const MODE_DEV = 1;

    public const TYPE_HTML = 0;
    public const TYPE_TEXT = 1;

    protected const FORMAT_TYPE_DATE_TIME = 1;
    protected const FORMAT_TYPE_DATE = 2;
    protected const FORMAT_TYPE_TIME = 3;

    //region properties
    /**
     * @var string
     */
    public string $name = 'fafte';

    /**
     * @var string[]
     */
    protected array $elements = [
        Base64Encode::class,
        Call::class,
        CallFunction::class,
        FormatAsDate::class,
        FormatAsDateFormat::class,
        FormatAsDateString::class,
        FormatAsDatetime::class,
        FormatAsDatetimeFormat::class,
        FormatAsDatetimeString::class,
        FormatAsShortSize::class,
        FormatAsShortSizeDecimals::class,
        FormatAsShortSizeValue::class,
        FormatAsTime::class,
        FormatAsTimeFormat::class,
        FormatAsTimeString::class,
        Get::class,
        GetFormat::class,
        JsonEncode::class,
        Param::class,
        ParamName::class,
        ParamValue::class,
        Set::class,
        StripTags::class,
        StripTagsAllowableTags::class,
        StripTagsString::class,
        StrToLower::class,
        StrToLowerString::class,
        StrToUpper::class,
        StrToUpperString::class,
        UcWords::class,
        UcWordsDelimiters::class,
        UcWordsString::class,
        Trim::class,
        TrimString::class,
        TrimCharlist::class,
        Nl2Br::class,
        Nl2BrString::class,
        Htmlentities::class,
        HtmlentitiesString::class,
        HtmlEntityDecode::class,
        HtmlEntityDecodeString::class,
        Htmlspecialchars::class,
        HtmlspecialcharsString::class,
        HtmlspecialcharsDecode::class,
        HtmlspecialcharsDecodeString::class,
        Parse::class,
        ParseString::class,
        RoundValue::class,
        Round::class,
        VarDump::class,
        StrReplace::class,
        StrReplaceSubject::class,
        TimeTag::class,
        Base64Decode::class,
        ConditionalStatement::class,
        ConditionalStatementConditionNotEqual::class,
        ConditionalStatementConditionEqual::class,
        ConditionalStatementConditionTypeSafeEqual::class,
        ConditionalStatementConditionTypeSafeNotEqual::class,
        ConditionalStatementConditionEndsWith::class,
        ConditionalStatementConditionEndsNotWith::class,
        ConditionalStatementConditionStartsWith::class,
        ConditionalStatementConditionStartsNotWith::class,
        ConditionalStatementConditionTrue::class,
        ConditionalStatementConditionFalse::class,
        ConditionalStatementConditionEmpty::class,
        ConditionalStatementConditionNotEmpty::class,
        ConditionalStatementConditionLessThan::class,
        ConditionalStatementConditionLessEqualThan::class,
        ConditionalStatementConditionGreaterThan::class,
        ConditionalStatementConditionGreaterEqualThan::class,
        ConditionalStatementThen::class,
        ConditionalStatementElse::class,
        ConditionalStatementCondition::class,
        ConditionalStatementConditionAnd::class,
        ConditionalStatementConditionOr::class,
        Loop::class,
        LoopBody::class,
        LoopEach::class,
        LoopAs::class,
        Tag::class,
        TagName::class,
        TagBody::class,
        TagAttribute::class,
        TagAttributeName::class,
        TagAttributeValue::class,
        TagAttributeEmpty::class,
        Calc::class
    ];

    /**
     * @var LoggerInterface|null
     */
    public ?LoggerInterface $logger = null;

    /**
     * @var CacheInterface|null
     */
    protected ?CacheInterface $cache = null;

    protected int $cacheTtl = 3600;

    protected int $mode = self::MODE_PROD;

    protected int $type = self::TYPE_HTML;

    protected int $maxDeep = 100;

    /**
     * @var mixed
     */
    public $data;

    /**
     * @var string|null
     */
    protected ?string $language = null;

    /**
     * @var string|null
     */
    private ?string $currentLanguage = null;

    /**
     * @var float
     */
    protected float $debugStartTime;

    /**
     * @var array
     */
    protected array $debugData = [];

    /**
     * @var bool
     */
    protected bool $returnRawData = false;

    /**
     * @var int
     */
    protected int $currentDeep = 0;

    /**
     * @var array
     */
    protected array $nodeStats = [];

    /**
     * @var array
     */
    protected array $parserElements;

    /**
     * @var array
     */
    protected array $parserElementsByClassName;

    /**
     * @var string
     */
    protected string $tempTagName;

    /**
     * @var array
     */
    protected array $bootstrapCache = [];

    /**
     * @var string
     */
    protected string $currentTagName = self::ROOT;

    /**
     * @var string
     */
    protected string $parentTagName = self::ROOT;

    /**
     * @var array
     */
    protected array $allowedChildElements = [];

    /**
     * @var HTML5DOMDocument
     */
    protected HTML5DOMDocument $htmlTagDom;
    //endregion properties

    //region getter and setter
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
     * @return string[]
     */
    public function getElements(): array
    {
        return $this->elements;
    }

    /**
     * @param string[] $elements
     *
     * @return $this
     */
    public function setElements(array $elements): self
    {
        $this->elements = $elements;
        $this->refresh();
        return $this;
    }

    /**
     * @param string[] $elements
     *
     * @return $this
     */
    public function addElements(array $elements): self
    {
        $this->elements = array_merge($this->elements, $elements);
        $this->refresh();
        return $this;
    }

    /**
     * @return int
     */
    public function getCurrentDeep(): int
    {
        return $this->currentDeep;
    }

    /**
     * @return string
     */
    public function getCurrentTagName(): string
    {
        return $this->currentTagName;
    }

    /**
     * @return int
     */
    public function getType(): int
    {
        return $this->type;
    }

    /**
     * @param int $type
     *
     * @return $this
     */
    public function setType(int $type): self
    {
        $this->type = $type;
        $this->refresh();
        return $this;
    }

    /**
     * @return string|null
     */
    public function getLanguage(): ?string
    {
        if ($this->language === null) {
            $this->language = Locale::getDefault();
        }

        return $this->language;
    }

    /**
     * @param string|null $language
     *
     * @return $this
     */
    public function setLanguage(?string $language): self
    {
        $this->language = $language;

        if ($this->language !== null) {
            $this->language = strtolower($this->language);
        }

        return $this;
    }

    /**
     * @param $data
     *
     * @return $this
     */
    public function setData(&$data): self
    {
        $this->data = &$data;

        return $this;
    }

    /**
     * @param LoggerInterface|null $logger
     *
     * @return $this
     */
    public function setLogger(?LoggerInterface $logger): self
    {
        $this->logger = $logger;

        if ($this->logger === null) {
            $this->logger = new NullLogger();
        }

        return $this;
    }

    /**
     * @return LoggerInterface
     */
    public function getLogger(): LoggerInterface
    {
        if ($this->logger === null) {
            $this->logger = new NullLogger();
        }

        return $this->logger;
    }

    /**
     * @param CacheInterface|null $cache
     *
     * @return $this
     */
    public function setCache(?CacheInterface $cache): self
    {
        $this->cache = $cache;

        return $this;
    }

    /**
     * @param int $mode
     *
     * @return $this
     */
    public function setMode(int $mode): self
    {
        $this->mode = $mode;

        return $this;
    }

    /**
     * @return bool
     */
    public function getReturnRawData(): bool
    {
        return $this->returnRawData;
    }

    /**
     * @param bool $returnRawData
     *
     * @return Parser
     */
    public function setReturnRawData(bool $returnRawData): self
    {
        $this->returnRawData = $returnRawData;
        return $this;
    }

    /**
     * @return array
     */
    public function getNodeStats(): array
    {
        return $this->nodeStats;
    }
    //endregion getter and setter

    //region init
    public function init(): void
    {
        if ($this->logger === null) {
            $this->logger = new NullLogger();
        }

        $this->refresh();
    }

    public function refresh(): void
    {
        if ($this->mode === self::MODE_DEV) {
            $this->debugStartTime = microtime(true);
        }

        $debugId = $this->debugStart('Refresh');

        $elementClasses = $this->elements;

        foreach ($elementClasses as $elementClass) {
            $parserElement = new $elementClass([
                'parser' => $this
            ]);

            foreach ($parserElement->tagNameAliases() as $tagNameAlias) {
                $this->parserElements[$tagNameAlias] = $parserElement;
            }

            $this->parserElements[$parserElement->tagName()] = $parserElement;
            $this->parserElementsByClassName[$elementClass] = $parserElement;
        }

        foreach ($this->parserElements as $currentTagName => $parserElement) {
            if (!isset($this->bootstrapCache[$parserElement->name()])) {
                $parserElement->bootstrap();
                $this->bootstrapCache[$parserElement->name()] = true;
            }
        }

        $this->allowedChildElements = $this->loadAllowedChildElements();

        $this->tempTagName = 'temp-tag-' . $this->name . '-temp-tag';

        $this->specialTagMap = [
            '<body' => '<' . $this->tempTagName . '-body',
            '</body' => '</' . $this->tempTagName . '-body',
            '<head' => '<' . $this->tempTagName . '-head',
            '</head' => '</' . $this->tempTagName . '-head',
            '<html' => '<' . $this->tempTagName . '-html',
            '</html' => '</' . $this->tempTagName . '-html',
        ];

        $this->htmlTagDom = new HTML5DOMDocument();

        $this->debugEnd($debugId);
    }
    //endregion init

    //region child elements
    protected function loadAllowedChildElements(): array
    {
        $debugId = $this->debugStart('Load allowed child elements');
        $key = 'fafte-allowed-child-elements-' . md5(implode('', array_keys($this->parserElements)));
        $allowedChildElements = null;

        if ($this->cache !== null) {
            try {
                $allowedChildElements = $this->cache->get($key);
            } catch (InvalidArgumentException $e) {
            }
        }

        if ($allowedChildElements === null) {
            $allowedChildElements = [];

            foreach ($this->parserElements as $currentTagName => $parserElement) {
                $allowedTypes = $parserElement->allowedTypes();

                if ($allowedTypes === null) {
                    $allowedTypes = [''];
                }

                foreach ($allowedTypes as $allowedType) {
                    if (!isset($allowedChildElements[$allowedType][$currentTagName])) {
                        $allowedChildElements[$allowedType][$currentTagName] = [];
                    }

                    $allowedParents = $parserElement->allowedParents();

                    if ($allowedParents === null) {
                        $allowedParents = [''];
                    }

                    foreach ($allowedParents as $allowedParent) {
                        if ($allowedParent === '') {
                            $allowedChildElements[$allowedType][''][] = $currentTagName;
                        } elseif ($allowedParent === self::ROOT) {
                            $allowedChildElements[$allowedType][self::ROOT][] = $currentTagName;
                        } elseif (isset($this->parserElementsByClassName[$allowedParent])) {
                            $parentElement = $this->parserElementsByClassName[$allowedParent];

                            foreach ($parentElement->tagNameAliases() as $tagNameAlias) {
                                $allowedChildElements[$allowedType][$tagNameAlias][] = $currentTagName;
                            }

                            $allowedChildElements[$allowedType][$parentElement->tagName()][] = $currentTagName;
                        }
                    }
                }
            }

            if ($this->cache !== null) {
                try {
                    $this->cache->set($key, $allowedChildElements, $this->cacheTtl);
                } catch (InvalidArgumentException $e) {
                }
            }
        }

        $this->debugEnd($debugId);
        return $allowedChildElements;
    }

    /**
     * @param int    $type
     * @param string $tagName
     *
     * @return array
     */
    protected function getAllowedChildElements(int $type, string $tagName): array
    {
        $debugId = $this->debugStart('Get allowed child elements for ' . $tagName . ' of type ' . $type);

        $allowedChildElementsByType = array_merge_recursive($this->allowedChildElements[''] ?? [], $this->allowedChildElements[$type] ?? []);
        $allowedChildElementsByTag = array_merge_recursive($allowedChildElementsByType[''] ?? [], $allowedChildElementsByType[$tagName] ?? []);

        $this->debugEnd($debugId);

        return $allowedChildElementsByTag;
    }
    //endregion child elements

    //region formatter and helper
    /**
     * @param int                            $type
     * @param DateTime|string                $dateTime
     * @param string|int                     $format
     * @param IntlTimeZone|DateTimeZone|null $timeZone
     *
     * @return false|string
     */
    protected function dateTimeFormatter(int $type, $dateTime, $format, $timeZone)
    {
        $calendar = IntlCalendar::fromDateTime($dateTime);
        $calendar->setTimeZone($timeZone);

        $dateType = IntlDateFormatter::NONE;
        $timeType = IntlDateFormatter::NONE;

        $intlFormat = '';
        $useFormatAsType = false;

        if ($format === null) {
            $format = '';
        } elseif (is_int($format)) {
            $useFormatAsType = true;
        }

        if ($type === self::FORMAT_TYPE_DATE_TIME) {
            /**
             * @var int $format
             */
            $dateType = $useFormatAsType ? $format : IntlDateFormatter::MEDIUM;
            $timeType = $useFormatAsType ? $format : IntlDateFormatter::MEDIUM;
        } elseif ($type === self::FORMAT_TYPE_DATE) {
            /**
             * @var int $format
             */
            $dateType = $useFormatAsType ? $format : IntlDateFormatter::MEDIUM;
        } elseif ($type === self::FORMAT_TYPE_TIME) {
            /**
             * @var int $format
             */
            $timeType = $useFormatAsType ? $format : IntlDateFormatter::MEDIUM;
        }

        if (!$useFormatAsType) {
            /**
             * @var string $format
             */
            $intlFormat = $format;
        }

        $df = new IntlDateFormatter($this->currentLanguage, $dateType, $timeType, $timeZone, $calendar, $intlFormat);
        return $df->format($calendar);
    }

    /**
     * @param DateTime|string               $dateTime
     * @param string|int                     $format
     * @param IntlTimeZone|DateTimeZone|null $timeZone
     *
     * @return false|string
     */
    public function formatDateTime($dateTime, $format, $timeZone = null)
    {
        return $this->dateTimeFormatter(self::FORMAT_TYPE_DATE_TIME, $dateTime, $format, $timeZone);
    }

    /**
     * @param DateTime|string               $time
     * @param string|int                     $format
     * @param IntlTimeZone|DateTimeZone|null $timeZone
     *
     * @return false|string
     */
    public function formatTime($time, $format, $timeZone = null)
    {
        return $this->dateTimeFormatter(self::FORMAT_TYPE_TIME, $time, $format, $timeZone);
    }

    /**
     * @param DateTime|string               $date
     * @param string|int                     $format
     * @param IntlTimeZone|DateTimeZone|null $timeZone
     *
     * @return false|string
     */
    public function formatDate($date, $format, $timeZone = null)
    {
        return $this->dateTimeFormatter(self::FORMAT_TYPE_DATE, $date, $format, $timeZone);
    }

    /**
     * @param        $number
     * @param int    $style
     * @param string $pattern
     * @param array  $attributes
     * @param array  $symbols
     * @param array  $textAttributes
     *
     * @return false|string
     */
    public function formatNumber($number, int $style, string $pattern = '', array $attributes = [], array $symbols = [], array $textAttributes = [])
    {
        $numberFormatter = new NumberFormatter($this->currentLanguage, $style, $pattern);

        foreach ($attributes as $name => $value) {
            $numberFormatter->setAttribute($name, $value);
        }

        foreach ($symbols as $name => $value) {
            $numberFormatter->setSymbol($name, $value);
        }

        foreach ($textAttributes as $name => $value) {
            $numberFormatter->setTextAttribute($name, $value);
        }

        return $numberFormatter->format($number);
    }

    /**
     * @param string $name
     * @param string $content
     * @param array  $options
     * @param string $attributePrefix
     *
     * @return string
     * @throws \JsonException
     */
    public function htmlTag(string $name, string $content = '', array $options = [], string $attributePrefix = ''): string
    {
        /**
         * @var HTML5DOMElement $element
         */
        $element = $this->htmlTagDom->createElement($name);
        $element->innerHTML = $content;

        foreach ($options as $attribute => $value) {
            if ($value instanceof DataHelper) {
                if (!$value->keepEmpty && $value->value === '') {
                    continue;
                }

                $attribute = $value->name;
                $value = $value->value;
            } else {
                if ($value === '') {
                    continue;
                }

                if ($attributePrefix !== '') {
                    $attribute = (mb_strpos($attribute, $attributePrefix) === 0 ? mb_substr($attribute, mb_strlen($attributePrefix)) : $attribute);
                }
            }

            if (is_array($value) || is_object($value)) {
                $value = json_encode(
                    $value,
                    JSON_UNESCAPED_UNICODE | JSON_HEX_QUOT | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_THROW_ON_ERROR,
                    512
                );
            }

            if ($value === true) {
                $value = 'true';
            } elseif ($value === false) {
                $value = 'false';
            }

            $element->setAttribute($attribute, (string)$value);
        }

        return $element->outerHTML;
    }
    //endregion formatter and helper

    /**
     * @param $string
     *
     * @return array|object|string
     * @throws Exception
     */
    public function parse($string)
    {
        $debugId = $this->debugStart('Parse');

        $result = $this->parseElements($string, self::ROOT, $this->returnRawData);

        if (!$this->returnRawData && is_string($result)) {
            $result = str_ireplace(
                ['<' . $this->tempTagName . '-special>', '</' . $this->tempTagName . '-special>'],
                ['<!', '>'],
                $result
            );

            $result = str_ireplace(
                array_values($this->specialTagMap),
                array_keys($this->specialTagMap),
                $result
            );
        }

        $this->debugEnd($debugId);

        return $result;
    }

    /**
     * @param string $string
     * @param string $currentTagName
     * @param bool   $rawData
     *
     * @return array|object|string
     * @throws Exception
     */
    public function parseElements(string $string, string $currentTagName, bool $rawData = false)
    {
        $parentLanguage = $this->currentLanguage;
        $this->currentLanguage = $this->getLanguage();

        $parentTagName = $this->parentTagName;
        $this->parentTagName = $this->currentTagName;
        $this->currentTagName = $currentTagName;

        $parseElementDebugId = $this->debugStart('Parse element ' . $this->currentTagName . ' (parent: ' . $this->parentTagName . ')');

        $parserElements = $this->getAllowedChildElements($this->type, $this->currentTagName);

        $dom = new HTML5DOMDocument();
        $dom->loadHTML('<!DOCTYPE html><html lang=""><body><'.$this->tempTagName.'>' . $this->getSafeHtml($string) . '</'.$this->tempTagName.'></body></html>', LIBXML_NONET | HTML5DOMDocument::ALLOW_DUPLICATE_IDS);

        $xPath = new DOMXPath($dom);
        $filterReplacements = '//' . implode('|//', $parserElements);
        $unfilteredDomNodes = $xPath->query($filterReplacements);

        $domNodes = [];

        /**
         * @var HTML5DOMElement $unfilteredDomNode
         */
        foreach ($unfilteredDomNodes as $unfilteredDomNode) {
            $nodePath = $unfilteredDomNode->getNodePath();

            $count = substr_count($nodePath, '/' . $this->name);

            if ($count === 1) {
                $domNodes[] = $unfilteredDomNode;
            }
        }

        $domNodeCount = count($domNodes);
        $getParsedContent = false;

        if ($domNodeCount === 0) {
            $result = $string;
        } else {
            $result = [];
            $oldDeep = $this->currentDeep;
            $this->currentDeep++;

            if ($this->currentDeep > $this->maxDeep) {
                $this->getLogger()->emergency('Max deep of ' . $this->maxDeep . ' reached', [
                    'time' => microtime(true),
                    'memory' => memory_get_usage()
                ]);
            }

            foreach ($domNodes as $domNode) {
                $tagName = $domNode->tagName;

                if (isset($this->parserElements[$tagName])) {
                    $tagName = $this->parserElements[$tagName]->tagName();

                    if (!isset($this->nodeStats[$tagName]['usage'])) {
                        $this->nodeStats[$tagName]['usage'] = 0;
                    }

                    $this->nodeStats[$tagName]['usage']++;
                    $this->nodeStats[$tagName]['number'] = ($this->nodeStats[$tagName]['number'] ?? 0) + 1;

                    $this->prepareNode($xPath, $domNode, $this->parserElements[$tagName], $tagName);

                    $childCurrentTagName = $this->currentTagName;
                    $childParentTagName = $this->parentTagName;

                    $this->parentTagName = $this->currentTagName;
                    $this->currentTagName = $tagName;

                    $replacement = $this->parserElements[$tagName]->run();

                    $this->parentTagName = $childParentTagName;
                    $this->currentTagName = $childCurrentTagName;

                    if ($rawData && $domNodeCount > 1) {
                        $result[] = $replacement;
                    } elseif ($rawData && (is_array($replacement) || is_object($replacement))) {
                        $result = $replacement;
                    } else {
                        if ($replacement !== null) {
                            if (!is_string($replacement)) {
                                $replacement = $this->getSafeValue($replacement);
                            }

                            $replacement = $this->getSafeHtml($replacement ?? '');
                        }

                        $domNode->outerHTML = $replacement ?? '';
                        $getParsedContent = true;
                    }
                }
            }

            $this->currentDeep = $oldDeep;

            if ($getParsedContent) {
                /**
                 * @var HTML5DOMElement[] $node
                 */
                $node = $xPath->query('//' . $this->tempTagName);
                $result = $node[0]->innerHTML;
            }
        }

        $this->currentLanguage = $parentLanguage;
        $this->currentTagName = $this->parentTagName;
        $this->parentTagName = $parentTagName;

        $this->debugEnd($parseElementDebugId);

        return $result;
    }

    /**
     * @param string $string
     *
     * @return string
     */
    public function fullTrim(string $string): string
    {
        return trim(str_replace('&nbsp;', mb_chr(0xA0, 'UTF-8'), $string), " \t\n\r\0\x0B" . mb_chr(0xC2, 'UTF-8') . mb_chr(0xA0, 'UTF-8'));
    }

    protected array $specialTagMap = [];

    /**
     * @param string $string
     *
     * @return string
     */
    protected function getSafeHtml(string $string): string
    {
        $string = preg_replace('/<!(?<tag>[^\->]+)>/mi', '<'.$this->tempTagName.'-special>$1</'.$this->tempTagName.'-special>', $string);

        return str_ireplace(
            array_keys($this->specialTagMap),
            array_values($this->specialTagMap),
            $string
        );
    }

    /**
     * @param DOMXPath        $xPath
     * @param HTML5DOMElement $domNode
     * @param ParserElement   $parserElement
     * @param string          $currentTagName
     *
     * @throws Exception
     */
    protected function prepareNode(DOMXPath $xPath, HTML5DOMElement $domNode, ParserElement $parserElement, string $currentTagName): void
    {
        $debugId = $this->debugStart('Prepare node ' . $currentTagName);

        $attributes = [];
        $data = [];
        $elements = [];
        $hasChildren = false;
        $contentElementSetting = null;
        $content = null;
        $nodePath = $domNode->getNodePath();

        foreach ($domNode->attributes as $attr) {
            $attributes[$attr->nodeName] = $attr->nodeValue;
        }

        $elementSettings = $parserElement->elementSettings();

        if ($elementSettings !== []) {
            foreach ($elementSettings as $elementSetting) {
                $data[$elementSetting->name] = [];

                //region elements
                if ($elementSetting->element !== null) {
                    $childElementTagName = $this->parserElementsByClassName[$elementSetting->element]->tagName();

                    $childElementTagNames = array_merge(
                        [$childElementTagName],
                        $this->parserElementsByClassName[$elementSetting->element]->tagNameAliases()
                    );

                    $unfilteredChildDomNodes = $xPath->query('//' . implode('|//', $childElementTagNames));
                    $childDomNodes = [];

                    /**
                     * @var HTML5DOMElement $unfilteredChildDomNode
                     */
                    foreach ($unfilteredChildDomNodes as $unfilteredChildDomNode) {
                        $childNodePath = $unfilteredChildDomNode->getNodePath();

                        $childNodePathParts = explode('/', $childNodePath);
                        $currentChildNode = array_pop($childNodePathParts);

                        $bracketPosition = mb_strpos($currentChildNode, '[');

                        if ($bracketPosition !== false) {
                            $currentChildNode = mb_substr($currentChildNode, 0, $bracketPosition);
                        }

                        if (implode('/', $childNodePathParts) === $nodePath && in_array($currentChildNode, $childElementTagNames, true)) {
                            $childDomNodes[] = $unfilteredChildDomNode;
                        }
                    }

                    $childDomNodeCount = count($childDomNodes);

                    if ($childDomNodeCount > 0) {
                        $hasChildren = true;

                        if ($childDomNodeCount > 1 && !$elementSetting->multiple) {
                            throw new RuntimeException('Validation error of element "' . $parserElement->tagName() . '". Element contains multiple "' . $childElementTagName . '" child elements but only one is allowed!');
                        }

                        foreach ($childDomNodes as $childDomNode) {
                            $data[$elementSetting->name][] = &$this->getData($elementSetting, $this->parseElements($childDomNode->outerHTML, $currentTagName, $elementSetting->rawData));
                        }
                    }
                }
                //endregion elements

                //region attribute
                if ($elementSetting->multiple) {
                    $multipleAttributeExpression = strtr($elementSetting->multipleAttributeExpression, [
                        '{{name}}' => $elementSetting->name,
                    ]);

                    $attributeNames = preg_grep($multipleAttributeExpression, array_keys($attributes));
                } else {
                    $attributeNames = array_merge([$elementSetting->name], $elementSetting->getAliases());
                }

                foreach ($attributeNames as $attributeName) {
                    $attributeContent = $attributes[$attributeName] ?? null;

                    if ($attributeContent !== null) {
                        $attributeContent = &$this->getData($elementSetting, $attributeContent);

                        if ($elementSetting->attributeNameAsKey) {
                            $data[$elementSetting->name][$attributeName] = $attributeContent;
                        } else {
                            $data[$elementSetting->name][] = $attributeContent;
                        }
                    }

                    unset($attributeContent);
                }
                //endregion attribute

                if (!$elementSetting->multiple) {
                    $data[$elementSetting->name] = $data[$elementSetting->name][array_key_first($data[$elementSetting->name])] ?? null;
                }

                if ($elementSetting->content) {
                    $contentElementSetting = $elementSetting;
                    $content = $data[$elementSetting->name];
                }
            }
        }

        if ($content === null && !$hasChildren) {
            $content = $domNode->innerHTML;

            if ($parserElement->getParseContent()) {
                if ($contentElementSetting !== null && $contentElementSetting->element !== null) {
                    $contentElementSettingName = $this->parserElementsByClassName[$contentElementSetting->element]->tagName();
                    $content = $this->parseElements('<' . $contentElementSettingName . '>' . $content . '</' . $contentElementSettingName . '>', $currentTagName, $contentElementSetting->rawData);
                } else {
                    $content = $this->parseElements($content, $currentTagName, $parserElement->getContentAsRawData());
                }
            }

            if ($contentElementSetting !== null) {
                $data[$contentElementSetting->name] = &$this->getData($contentElementSetting, $content);
                $content = &$data[$contentElementSetting->name];
            }
        }

        //Set data to make it possible to access other properties in validation
        $parserElement->setData($data);

        if ($elementSettings !== []) {
            foreach ($elementSettings as $elementSetting) {
                if ($elementSetting->defaultValue !== null && ($data[$elementSetting->name] === null || $data[$elementSetting->name] === [] || $data[$elementSetting->name] === '')) {
                    $data[$elementSetting->name] = $elementSetting->defaultValue;
                }

                try {
                    $rules = new Rules($elementSetting->rules);
                    $result = $rules->validate($data[$elementSetting->name]);

                    if ($result->isValid() === false) {
                        throw new RuntimeException('Validation error of ElementSetting "'  . $elementSetting->name . '" of element "' . $currentTagName . '".' . PHP_EOL . 'Line: ' . $domNode->getLineNo() . PHP_EOL . 'Code: ' . $domNode->outerHTML . PHP_EOL . 'Error: ' . print_r($result->getErrors(), true));
                    }
                } catch (Exception $e) {
                    throw new RuntimeException('Cannot validate ElementSetting "'  . $elementSetting->name . '" of element "' . $currentTagName . '".' . PHP_EOL . 'Line: ' . $domNode->getLineNo() . PHP_EOL . 'Code: ' . $domNode->outerHTML . PHP_EOL . 'Error: ' . $e->getMessage());
                }
            }
        }

        //Set data to update default values etc.
        $parserElement->setData($data)
            ->setContent($content)
            ->setAttributes($attributes)
            ->setElements($elements)
            ->setDomNode($domNode);

        $this->debugEnd($debugId);
    }

    /**
     * @param ElementSetting $elementSetting
     * @param mixed          $data
     *
     * @return mixed
     */
    protected function &getData(ElementSetting $elementSetting, $data)
    {
        if (!$elementSetting->safeData) {
            return $data;
        }

        return $this->getRawValue($data);
    }

    /**
     * @param      $name
     * @param null $data
     * @param bool $callLastClosure
     *
     * @return mixed|null
     */
    public function &getAttributeData($name, &$data = null, $callLastClosure = true)
    {
        if ($data === null) {
            $data = &$this->data;
        }

        return self::getValue($data, $name, $callLastClosure);
    }

    /**
     * @param string $name
     * @param        $value
     * @param null   $data
     */
    public function setAttributeData(string $name, &$value, &$data = null): void
    {
        if ($data === null) {
            $data = &$this->data;
        }

        self::setValue($data, $name, $value);
    }

    /**
     * @param        $data
     * @param string $path
     * @param        $value
     */
    public static function setValue(&$data, string $path, &$value): void
    {
        static::checkForClosure($data);

        if ($path === '') {
            $data = $value;
            return;
        }

        $keys = explode('.', $path);

        while (count($keys) > 1) {
            $key = array_shift($keys);

            if (!isset($data[$key])) {
                $data[$key] = [];
            }

            static::checkForClosure($data[$key]);
            $data = &$data[$key];
        }

        $data[array_shift($keys)] = &$value;
    }

    /**
     * @param        $data
     * @param string $path
     * @param bool   $callLastClosure
     *
     * @return mixed|null
     */
    public static function &getValue(&$data, string $path, $callLastClosure = true)
    {
        static::checkForClosure($data);

        $workData = $data;
        $value = null;

        if (is_array($data)) {
            $magicKeyValue = static::checkForMagicKey($data, $path);

            if ($magicKeyValue !== null) {
                return $magicKeyValue;
            }

            if (isset($data[$path])) {
                static::checkForClosure($data[$path]);

                return $data[$path];
            }
        }

        if (($pos = strrpos($path, '.')) !== false) {
            $mainKey = substr($path, 0, $pos);
            $workData = static::getValue($data, $mainKey, $callLastClosure);

            // TODO Removed to fix bug in loops. Need to check if it has a negative effect.
            // $data[$mainKey] = $workData;

            $path = substr($path, $pos + 1);
        }

        $magicKeyValue = static::checkForMagicKey($workData, $path);

        if ($magicKeyValue !== null) {
            return $magicKeyValue;
        }

        if (is_object($workData)) {
            if (!$callLastClosure) {
                if (method_exists($workData, $path)) {
                    $methodName = $path;
                } elseif (method_exists($workData, 'get' . ucfirst($path))) {
                    $methodName = 'get' . ucfirst($path);
                }

                if (isset($methodName)) {
                    $value = Closure::fromCallable([$workData, $methodName]);
                    return $value;
                }
            }

            $value = $workData->$path;
            return $value;
        }


        if (isset($workData[$path])) {
            if (is_array($workData)) {
                $value = &$workData[$path];
            } else {
                $value = $workData[$path];
            }

            static::checkForClosure($value);
        }

        return $value;
    }

    /**
     * @param        $array
     * @param string $key
     *
     * @return mixed|null
     */
    protected static function checkForMagicKey($array, string $key)
    {
        if ($key === '$$count' && is_countable($array)) {
            return count($array);
        }

        return null;
    }

    /**
     * @param $value
     */
    protected static function checkForClosure(&$value): void
    {
        if ($value instanceof Closure) {
            $value = $value();
        }
    }

    /**
     * @param mixed $value
     *
     * @return mixed
     */
    public function &getRawValue($value)
    {
        if (is_array($value) || is_object($value)) {
            return $value;
        }

        if (is_string($value)) {
            $value = $this->fullTrim($value);
        }

        if (is_numeric($value)) {
            /**
             * @var int|float $value
             */
            if (is_int($value) || mb_strpos((string)$value, '.') === false) {
                $value = (int)$value;
                return $value;
            }

            $value = (float)$value;
            return $value;
        }

        if ($value === 'true' || $value === 't') {
            $value = true;
            return $value;
        }

        if ($value === 'false' || $value === 'f') {
            $value = false;
            return $value;
        }

        if ((mb_strpos($value, '\'') === 0 && mb_strrpos($value, '\'') === mb_strlen($value) - 1) ||
            (mb_strpos($value, '"') === 0 && mb_strrpos($value, '"') === mb_strlen($value) - 1)) {
            $value = mb_substr($value, 1, -1);
            return $value;
        }

        if (mb_strpos($value, '.') === 0) {
            /** @noinspection PhpUnnecessaryLocalVariableInspection */
            $dataValue = &$this->getAttributeData(mb_substr($value, 1));
            return $dataValue;
        }

        return $value;
    }

    /**
     * @param mixed $value
     *
     * @return string
     */
    public function getSafeValue($value): string
    {
        if (is_numeric($value)) {
            $value = (string)$value;
        } elseif ($value === true) {
            $value = 'true';
        } elseif ($value === false) {
            $value = 'false';
        } elseif (is_string($value)) {
            $value = '\'' . $value . '\'';
        } else {
            if (!isset($this->data['temp-data-storage'])) {
                $this->data['temp-data-storage'] = [];
            }

            $count = count($this->data['temp-data-storage']);
            $tempName = 'temp-data-storage.' . $count;//.?
            $this->setAttributeData($tempName, $value);
            /** @noinspection UselessUnsetInspection */
            unset($value);
            $value = $tempName;
        }

        return $value;
    }

    //region debug
    /**
     * @param string $message
     *
     * @return string|null
     */
    protected function debugStart(string $message): ?string
    {
        if ($this->mode === self::MODE_DEV) {
            $debugId = md5(uniqid((string)mt_rand(), true));

            $this->debugData[$debugId] = [
                'message' => $message,
                'memory' => memory_get_usage(),
                'time' => microtime(true)
            ];

            $this->debug(str_repeat('│ ', count($this->debugData) - 1) . '┌─ '. $message);
            return $debugId;
        }

        return null;
    }

    /**
     * @param string|null $debugId
     */
    protected function debugEnd(?string $debugId): void
    {
        if ($this->mode === self::MODE_DEV && $debugId !== null) {
            $debugData = $this->debugData[$debugId];

            $currentMemory = memory_get_usage();
            $memory = $currentMemory - $debugData['memory'];

            $currentTime = microtime(true);

            unset($this->debugData[$debugId]);

            $this->debug(str_repeat('│ ', count($this->debugData)) . '└─ '. $debugData['message'], [
                'memory' => $this->getHumanSize($memory),
                'time' => $this->getHumanTime($debugData['time'], $currentTime),
            ]);
        }
    }

    /**
     * @param float $start
     * @param float $end
     *
     * @return string
     */
    protected function getHumanTime(float $start, float $end): string
    {
        $microseconds = $end - $start;
        $minutes = (int)($seconds = (int)($milliseconds = ($microseconds * 10000)) / 10000) / 60;

        return (($minutes % 60) > 0 ? ($minutes % 60) . 'min ' : '') .
            (($seconds % 60) > 0 ? ($seconds % 60) . 'sec ' : '') .
            ($milliseconds > 0 ? (($milliseconds % 10000) / 10) . 'ms' : '');
    }

    /**
     * @param int $size
     *
     * @return string
     */
    protected function getHumanSize(int $size): string
    {
        $unit = ['b','kb','mb','gb','tb','pb'];

        if ($size === 0) {
            return '0 b';
        }

        return round($size / (1024 ** ($i = floor(log($size, 1024)))), 2) . ' ' . $unit[(int)$i];
    }

    /**
     * @param string $message
     * @param array  $extras
     */
    protected function debug(string $message, array $extras = []): void
    {
        if ($this->mode === self::MODE_DEV) {
            foreach ($extras as $name => $value) {
                $message .= ' | ' . $name . ': ' . $value;
            }

            $this->getLogger()->debug($message);
        }
    }
    //endregion debug
}
