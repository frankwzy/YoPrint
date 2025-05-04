<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Http\Services\ProductBulkImportService;
use App\Models\CsvImportBatch;
use App\Models\Product;
use App\Models\ProductImportDetail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;


class ProcessProductBulkImportJob implements ShouldQueue
{
    use Queueable;

    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $fullPath;
    protected $columns;
    protected $csvImportBatch;

    public function __construct($fullPath, $columns, CsvImportBatch $csvImportBatch)
    {
        $this->fullPath = $fullPath;
        $this->columns = $columns;
        $this->csvImportBatch = $csvImportBatch;
    }

    public function handle()
    {
        $productBulkImportService = app(ProductBulkImportService::class);
        $allRows = $productBulkImportService->extractAndCleanData($this->fullPath, $this->columns);
        $existingProducts = Product::all()->keyBy('id');

        // These 3 collections of data can be used to generate a summary report of the import process in the future.
        $updatedData = [];
        $createdData = [];
        $errorData = [];

        foreach ($allRows as $row) {
            // Data Validation to ensure that data is in valid format
            $validationResult = $this->validateProductData($row);
            if (!$validationResult['isValid']) {
                $errorData[] = [
                    'row' => $row,
                    'errors' => $validationResult['errors'] ?? [],
                ];
                continue;
            }

            try {
                // Begin database transaction to ensure that data will be rolled back if any error occurs.
                DB::beginTransaction();
                if ($existingProducts->has((int)$row['unique_key'])) {
                    $product = $existingProducts[(int)$row['unique_key']];
                    if ($this->checkIfProductNeedsUpdate($product, $row)) {
                        $update = $this->updateProduct($product, $row);
                        $updatedData[] = $update;
                    }
                } else {
                    $newProduct = $this->createNewProduct($row, $this->csvImportBatch->id);
                    $createdData[] = $newProduct;
                }
                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();
                $errorData[] = [
                    'row' => $row,
                    'exception' => $e->getMessage(),
                ];
            }
        }

        $this->csvImportBatch->update([
            'status_id' => CsvImportBatch::COMPLETED,
        ]);
    }

    private function validateProductData($row)
    {
        $validator = Validator::make($row, [
            'unique_key' => 'required|string',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'style' => 'required|string|max:20',
            'sanmar_color' => 'required|string|max:50',
            'size' => 'required|string|max:7',
            'color' => 'required|string|max:50',
            'price' => 'required|numeric|min:0',
        ]);

        return [
            'isValid' => !$validator->fails(),
            'errors' => $validator->errors()->toArray()
        ];
    }

    private function checkIfProductNeedsUpdate(Product $product, array $newData): bool
    {
        if (
            $product->id != $newData['unique_key'] ||
            $product->title != $newData['title'] ||
            $product->description != $newData['description'] ||
            $product->style != $newData['style'] ||
            $product->sanmar_mainframe_color != $newData['sanmar_color'] ||
            $product->size != $newData['size'] ||
            $product->color_name != $newData['color'] ||
            $product->piece_price != $newData['price']
        ) {
            return true;
        }
        return false;
    }

    private function updateProduct(Product $product, $row)
    {
        $updated = $product->update([
            'id' => $row['unique_key'],
            'title' => $row['title'],
            'description' => $row['description'],
            'style' => $row['style'],
            'sanmar_mainframe_color' => $row['sanmar_color'],
            'size' => $row['size'],
            'color_name' => $row['color'],
            'piece_price' => $row['price'],
        ]);

        return $updated;
    }

    private function createNewProduct($row, $csvImportBatchId)
    {
        $product = Product::create([
            'id' => $row['unique_key'],
            'title' => $row['title'],
            'description' => $row['description'],
            'style' => $row['style'],
            'sanmar_mainframe_color' => $row['sanmar_color'],
            'size' => $row['size'],
            'color_name' => $row['color'],
            'piece_price' => $row['price'],
        ]);

        // This acts as a pivot table to link products with csv import batches
        ProductImportDetail::create([
            'product_id' => $row['unique_key'],
            'csv_import_batch_id' => $csvImportBatchId,
        ]);

        return $product;
    }
}
