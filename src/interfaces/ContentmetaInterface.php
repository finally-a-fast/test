<?php

namespace fafcms\helpers\interfaces;

/**
 * Interface ContentmetaInterface
 * @package fafcms\helpers\interfaces
 */
interface ContentmetaInterface
{
    /**
     * @return string
     */
    public static function contentmetaName(): string;

    /**
     * @return string
     */
    public static function contentmetaId(): string;

    /**
     * @return string|\Closure|array
     */
    public static function contentmetaSiteId();
}
