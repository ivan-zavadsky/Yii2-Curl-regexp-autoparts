<?php

namespace app\services;

use Exception;

interface IFetcher
{
    /**
     * @throws Exception
     */
    public function getRaw(string $url, array $postData): string;
}