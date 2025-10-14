<?php

namespace app\services;

use Exception;

class CurlFetcher implements IFetcher
{
    private ICacher $Cacher;

    public function __construct()
    {
        $this->Cacher = new FileCacher();
    }
    /**
     * @throws Exception
     */
    public function getRaw(string $url, array $postData): string
    {
        $cacheKey = $this->getCacheKey($url, $postData);
        if ($this->Cacher->has($cacheKey)) {
            return $this->Cacher->get($cacheKey);
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        if (curl_errno($ch)) {
            throw new Exception(curl_error($ch));
        } else {
            $this->Cacher->set($cacheKey, $response);
        }
        curl_close($ch);

        return $response;
    }

    private function getCacheKey($url, $postData): string
    {
        return md5($url . serialize($postData));
    }


}