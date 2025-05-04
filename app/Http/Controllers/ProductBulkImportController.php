<?php

namespace App\Http\Controllers;

use App\Http\Services\ProductBulkImportService;
use App\Jobs\ProcessProductBulkImportJob;
use App\Models\CsvImportBatch;
use App\Models\Product;
use App\Models\ProductImportDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

ini_set('memory_limit', '512M');


class ProductBulkImportController extends Controller
{
    const UNIQUE_KEY = 'UNIQUE_KEY';
    const TITLE = 'PRODUCT_TITLE';
    const DESCRIPTION = 'PRODUCT_DESCRIPTION';
    const STYLE = 'STYLE#';
    const SANMAR_COLOR = 'SANMAR_MAINFRAME_COLOR';
    const SIZE = 'SIZE';
    const COLOR = 'COLOR_NAME';
    const PRICE = 'PIECE_PRICE';

    public function __construct(
        private ProductBulkImportService $productBulkImportService
    ) {}

    public function bulkUploadProductsViaCsv(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required|file|mimes:csv|max:40960',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors()
            ], 400);
        }

        $file = $request->file('file');

        $fileName = Str::uuid() . '.' . $file->getClientOriginalExtension();

        $path = $file->storeAs('csv_imports', $fileName, 'public');
        $fullPath = Storage::disk('public')->path($path);

        $columns = [
            'unique_key' => self::UNIQUE_KEY,
            'title' => self::TITLE,
            'description' => self::DESCRIPTION,
            'style' => self::STYLE,
            'sanmar_color' => self::SANMAR_COLOR,
            'size' => self::SIZE,
            'color' => self::COLOR,
            'price' => self::PRICE,
        ];

        // Create a new CSV import batch record and dispatch a job to process the file
        // Can be used for batch rollbacks
        $csvImportBatch = CsvImportBatch::create([
            'original_filename' => $file->getClientOriginalName(),
            'filename' => basename($path),
            'file_path' => $path,
            'uuid' => Str::uuid(),
            'status_id' => CsvImportBatch::IN_PROGRESS,
        ]);

        ProcessProductBulkImportJob::dispatch($fullPath, $columns, $csvImportBatch);

        return response()->json([
            'success' => true,
            'message' => 'File uploaded and queued for processing',
            'batch_id' => $csvImportBatch->id,
        ]);
    }

}
