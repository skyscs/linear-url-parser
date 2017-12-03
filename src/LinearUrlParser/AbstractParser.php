<?php

namespace LinearUrlParser;

abstract class AbstractParser
{
    const DEFAULT_PROTOCOL = 'http';

    /**
     * @param string $url
     * @return string
     */
    protected function parseUrl(string $url): string
    {
        $ch = $this->initCurl($url);
        $output = curl_exec($ch);
        curl_close($ch);

        return $output;
    }

    /**
     * @param string $url
     * @return resource
     */
    protected function initCurl(string $url)
    {
        if (substr($url, 0, 2) == '//') {
            $url = self::DEFAULT_PROTOCOL . ':' . $url;
        }

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:10.0.2) Gecko/20100101 Firefox/10.0.2');
        curl_setopt($ch, CURLOPT_COOKIESESSION, true);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);

        return $ch;
    }
}