<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CsvImportBatch;

class PollController extends Controller
{
    public function getImportStatus($batchId)
    {
        $batch = CsvImportBatch::findOrFail($batchId);
        return response()->json([
            'status' => $batch->status_id,
        ]);
    }
}