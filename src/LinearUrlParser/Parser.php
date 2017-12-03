<?php

namespace LinearUrlParser;

use LinearUrlParser\Interfaces\ImageParserInterface;

/**
 * Class Parser
 * @package LinearUrlParser
 */
class Parser extends AbstractParser implements ImageParserInterface
{
    const IMAGE_FORMAT = '.png';

    /**
     * @var string
     */
    protected $imageFolderPath;

    /**
     * @var string
     */
    protected $imageSelector;

    /**
     * @var int
     */
    protected $timeoutMin;

    /**
     * @var int
     */
    protected $timeoutMax;

    /**
     * Parser constructor.
     * @param string $imageFolderPath
     * @param string $imageSelector
     * @param int $timeoutMin
     * @param int $timeoutMax
     */
    public function __construct(
        string $imageFolderPath,
        string $imageSelector,
        int $timeoutMin,
        int $timeoutMax
    )
    {
        $this->imageFolderPath = $imageFolderPath;
        $this->imageSelector = $imageSelector;
        $this->timeoutMin = $timeoutMin;
        $this->timeoutMax = $timeoutMax;
    }

    /**
     * @param string $id
     * @param string $url
     * @return string
     */
    public function parseImage(string $id, string $url): string
    {
        $localPath = $this->imageFolderPath . DIRECTORY_SEPARATOR . $id . self::IMAGE_FORMAT;
        if (file_exists($localPath)) {
            return $localPath;
        }

        $html = $this->parseUrl($url);

        if (preg_match($this->imageSelector, $html, $matches)) {
            $imageUrl = $matches[1];

            $this->saveImage($imageUrl, $localPath);
            usleep(rand($this->timeoutMin, $this->timeoutMax));
        }

        return $localPath;
    }

    /**
     * @param string $url
     * @param string $localPath
     * @return bool
     */
    protected function saveImage(string $url, string $localPath): bool
    {
        $ch = $this->initCurl($url);

        curl_setopt($ch, CURLOPT_BINARYTRANSFER, 1);

        $imageData = curl_exec($ch);
        curl_close($ch);

        $fp = fopen($localPath, 'x');
        fwrite($fp, $imageData);
        fclose($fp);

        return true;
    }
}
