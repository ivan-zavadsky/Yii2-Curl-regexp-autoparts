<?php

namespace app\entities;

use Exception;

class Page
{
    public $url = 'https://www.autozap.ru/goods';
    public $code;
    public $pattern = '/(?#
            )producer[^>]*>(.*)<.*(?#
            )code[^>]*>(.*)<.*(?#
            )name[^>]*>[^>]*>(.*)<.*(?#
            )price[^>]*>[^>]*>(.*)<.*(?#
            )storehouse-quantity[^>]*>[^>]*>(.*)<.*(?#
            )id=[\'|"]g.*value=[\'|"](.*)[\'|"].*(?#
            )article[^>]*>(.*)<.*(?#
            )/sU'
    ;
    public $rawPage;
    public $result;

    public function __construct($code)
    {
        $this->code = $code;
    }
    public function getRawPage(): void
    {
        $file = '../runtime/' . $this->code . '.txt';
        if (file_exists($file)) {
            $this->rawPage = file_get_contents($file);
            return;
        }

        $postData = array(
            'code' => $this->code,
            'count' => 300,
            'page' => 1,
            'search' => 'Найти',
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        if (curl_errno($ch)) {
            throw new Exception(curl_error($ch));
        } else {

            $this->rawPage = $response;
            file_put_contents($file, $this->rawPage);
        }
        curl_close($ch);
    }

    public function hasProductsData(): bool
    {
        $pattern = '/class=[\'|"]producer[^>]*>([^<]+)</sU';
        preg_match_all($pattern, $this->rawPage, $matches);

        return $matches[1] > 1;
    }

    public function extract(): void
    {
        $pattern = '/<tr[^>]*?>(.*)<\/tr>/sU';
        preg_match_all($pattern, $this->rawPage, $matches);

        $productPattern = '/(?#
            )producer[^>]*>(.*)<.*(?#
            )code[^>]*>(.*)<.*(?#
            )name[^>]*>[^>]*>(.*)<.*(?#
            )/sU'
        ;
        $offerPattern = '/(?#
            )price[^>]*>[^>]*>(.*)<.*(?#
            )storehouse-quantity[^>]*>[^>]*>(.*)<.*(?#
            )id=[\'|"]g.*value=[\'|"](.*)[\'|"].*(?#
            )article[^>]*>(.*)<.*(?#
            )/sU'
        ;

        $hasBrand = false;
        foreach ($matches[0] as $line) {
            if (
                preg_match($productPattern, $line, $productMatches)
                && $productMatches[1]
            )
            {
                if ($hasBrand) {
                    break;
                }
                $product = new Product();
                $product->brand = $productMatches[1];
                $product->article = $productMatches[2];
                $product->name = $productMatches[3];

                $this->result['product'] = $product;
                $hasBrand = true;
            }

            if (
                preg_match($offerPattern, $line, $offerMatches)
                && $offerMatches[1]
            )
            {
                $offer = new Offer();
                $offer->price = $offerMatches[1];
                $offer->count = $offerMatches[2];
                $offer->id = $offerMatches[3];
                $offer->time = (int) $offerMatches[4];

                $this->result['offers'][] = $offer;
            }
        }
    }

    public function getFormattedResult(): array
    {
        $products = [];
        foreach ($this->result['offers'] as $offer) {
            $products[] = array_merge(
                (array) $offer,
                (array) $this->result['product']
            );
        }

        return $products;
    }

    public function save(): bool
    {
        return (bool) file_put_contents(
            '../runtime/products.json',
            json_encode(
                $this->getFormattedResult(),
                JSON_UNESCAPED_UNICODE
            )
        );

    }
}