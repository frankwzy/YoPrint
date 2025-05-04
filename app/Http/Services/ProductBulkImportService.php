<?php

namespace App\Http\Services;

use League\Csv\Reader;

ini_set('memory_limit', '512M');

class ProductBulkImportService
{

    public function extractAndCleanData($fullPath, $columns): array
    {
        $csv = Reader::createFromPath($fullPath, 'r');
        $csv->setHeaderOffset(0);
    
        $uniqueRows = [];
        foreach ($csv as $record) {
            $rowData = [];
            foreach ($columns as $key => $column) {
                $cellValue = $record[$column] ?? null;
                $rowData[$key] = $this->cleanToUtf8($cellValue);
            }
            
            // If there is another row that has the same unique key, replace the existing row. If there is not, a new record is added.
            $uniqueKey = $rowData['unique_key'];
            $uniqueRows[$uniqueKey] = $rowData;
        }
        return array_values($uniqueRows);
    }

    private function cleanToUtf8($value): string
    {
        if ($value === null) {
            return '';
        }

        $value = html_entity_decode($value, ENT_QUOTES | ENT_HTML5, 'UTF-8');

        if (!mb_check_encoding($value, 'UTF-8')) {
            $value = mb_convert_encoding($value, 'UTF-8', 'auto');
        }

        $value = iconv('UTF-8', 'UTF-8//IGNORE', $value);

        $value = trim($value);

        $value = preg_replace('/\s+/', ' ', $value);

        return $value;
    }
}
