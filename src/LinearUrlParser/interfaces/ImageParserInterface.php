<?php

namespace LinearUrlParser\Interfaces;

interface ImageParserInterface
{
    public function parseImage(string $id, string $url): string;
}
