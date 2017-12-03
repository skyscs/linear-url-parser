<?php

namespace LinearUrlParser\Interfaces;

interface UrlGeneratorInterface
{
    public function generateLinearUrlBulk(int $chunkSize): array;
}
