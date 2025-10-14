<?php

namespace app\services;

use app\entities\Product;
use app\services\IExtractor;

class RegexpExtractor implements IExtractor
{
    public function extract(string $raw): array
    {
        $pattern = '/<tr[^>]*?>(.*)<\/tr>/sU';
        preg_match_all($pattern, $raw, $matches);

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
        $products = [];
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

//                $this->products['product'] = $product;
                $hasBrand = true;
            }

            if (
                preg_match($offerPattern, $line, $offerMatches)
                && isset($product)
                && $offerMatches[1]
            )
            {
                $newProduct = clone $product;
                $newProduct->price = $offerMatches[1];
                $newProduct->count = $offerMatches[2];
                $newProduct->id = $offerMatches[3];
                $newProduct->time = (int) $offerMatches[4];

                $products[] = $newProduct;
            }
        }

        return $products;
    }

    public function hasProductsData(string $raw): bool
    {
        $pattern = '/class=[\'|"]producer[^>]*>([^<]+)</sU';
        preg_match_all($pattern, $raw, $matches);

        return $matches[1] > 1;
    }
}