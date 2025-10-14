<?php

namespace app\entities;

use app\services\IFetcher;
use app\services\CurlFetcher;
use app\services\IExtractor;
use app\services\RegexpExtractor;
use Exception;

class Page
{
    public string $url = 'https://www.autozap.ru/goods';
    public array $postData = [
        'code' => null,
        'count' => 300,
        'page' => 1,
        'search' => 'Найти',
    ];

    public string $raw;
    /**
     * @var Product[]
     */
    public array $products;
    public IFetcher $fetcher;
    public IExtractor $extractor;

    public function __construct($code)
    {
        $this->postData['code'] = $code;
        $this->fetcher = new CurlFetcher();
        $this->extractor = new RegexpExtractor();
    }

    public function getRaw(): void
    {
        $this->raw = $this->fetcher->getRaw($this->url, $this->postData);
    }

    public function hasProductsData(): bool
    {
        return $this->extractor->hasProductsData($this->raw);
    }

    public function extract(): void
    {
        $this->products = $this->extractor->extract($this->raw);
    }

    public function save(): bool
    {
        return (bool) file_put_contents(
            '../runtime/products.json',
            json_encode(
//                $this->getFormattedResult(),
                $this->products,
                JSON_UNESCAPED_UNICODE
            )
        );

    }
}