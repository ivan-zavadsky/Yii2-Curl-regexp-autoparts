<?php

namespace app\services;

use Exception;

interface IExtractor
{
    /**
     * @throws Exception
     */
    public function extract(string $raw): array;

    public function hasProductsData(string $raw): bool;
}