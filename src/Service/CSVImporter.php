<?php
declare(strict_types=1);

namespace App\Service;

use App\Service\File\FileManager;
use App\Service\File\DataProcessorInterface;
use Exception;

class CSVImporter extends FileManager
{
    private string $delimiter;
    private DataProcessorInterface $dataProcessor;

    public function __construct(string $filePath, DataProcessorInterface $dataProcessor, string $delimiter = ',')
    {
        parent::__construct($filePath);
        $this->delimiter = $delimiter;
        $this->dataProcessor = $dataProcessor;
    }

    /**
     * @throws Exception
     */
    public function import(): array
    {
        $results = [];
        try {
            $handle = $this->openFile();
            while (($data = fgetcsv($handle, 0, $this->delimiter)) !== FALSE) {
                $result = $this->dataProcessor->processRow($data);
                if ($result) {
                    $results[] = $result;
                }
            }
            $this->closeFile($handle);
        } catch (Exception $e) {
            echo "Error: " . $e->getMessage() . "\n";
        }
        return $results;
    }
}
