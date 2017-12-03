<?php

namespace LinearUrlParser;

use LinearUrlParser\Interfaces\UrlGeneratorInterface;

/**
 * Class UrlGenerator
 * @package LIUrlParser
 */
class UrlGenerator implements UrlGeneratorInterface
{
    /**
     * @var string
     */
    private $baseUrl;

    /**
     * @var string
     */
    private $query;

    /**
     * @var bool
     */
    private $reverse;

    /**
     * @var array
     */
    private $range = [];

    /**
     * @var string
     */
    private $lowestRange;

    /**
     * @var string
     */
    private $highestRange;

    /**
     * UrlGenerator constructor.
     * @param string $baseUrl
     * @param string $query
     * @param array $ranges
     * @param bool $reverse
     */
    public function __construct(string $baseUrl, string $query, array $ranges, bool $reverse)
    {
        $this->baseUrl = $baseUrl;
        $this->query = $query;
        $this->reverse = $reverse;

        $this->initRange($ranges);
    }

    /**
     * @param int $bulkSize
     * @return array
     */
    public function generateLinearUrlBulk(int $bulkSize): array
    {
        $queries = [];

        $currentQuery = $this->query;
        for ($i = 0; $i < $bulkSize; $i++) {
            $queries[] = $this->getNextQuery($currentQuery);
        }

        return $this->createUrls($queries);
    }

    /**
     * @param string $query
     * @return string
     */
    protected function getNextQuery(string &$query): string
    {
        $parsedQuery = array_reverse(str_split($query));

        $needToIterate = true;
        foreach ($parsedQuery as $key => $item) {
            if ($needToIterate === false) {
                break;
            }

            if ($item === $this->lowestRange && $this->reverse) {
                $parsedQuery[$key] = $this->highestRange;
                continue;
            }

            if ($item === $this->highestRange && !$this->reverse) {
                $parsedQuery[$key] = $this->lowestRange;
                continue;
            }

            foreach ($this->range as $index => $value) {
                if ($this->range[$index] === $item) {
                    $parsedQuery[$key] = $this->reverse ? $this->range[$index - 1] : $this->range[$index + 1];
                    break;
                }
            }

            $needToIterate = false;
        }

        return $query = implode('', array_reverse($parsedQuery));
    }

    /**
     * @param array $queries
     * @return array
     */
    protected function createUrls(array $queries): array
    {
        $urls = [];
        foreach ($queries as $query) {
            $urls[$query] = $this->createUrl($query);
        }

        return $urls;
    }

    /**
     * @param string $query
     * @return string
     */
    protected function createUrl(string $query): string
    {
        return $this->baseUrl . '/' . $query;
    }

    /**
     * @param array $ranges
     */
    protected function initRange(array $ranges): void
    {
        foreach ($ranges as $range) {
            $this->range = array_merge($this->range, range(
                $range[0],
                $range[1]
            ));
        }

        array_walk($this->range, function (&$val): void {
            $val = (string)$val;
        });

        $this->lowestRange = current($this->range);
        $this->highestRange = end($this->range);

        reset($this->range);
    }
}
