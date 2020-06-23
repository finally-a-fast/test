<?php

namespace fafcms\helpers\interfaces;

interface SearchInterface
{
    public static function searchQuery(string $search, int $limit): array;

    public function getSearchUrl(): array;

    public function getSearchLabel(): string;
}
