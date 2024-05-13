<?php

namespace App\Service\File;

interface DataProcessorInterface
{
    public function processRow(array $rowData);
}