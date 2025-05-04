<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class CsvImportBatch extends Model
{
    protected $table = 'csv_import_batches';

    protected $guarded = [
        'id',
        'created_at',
        'updated_at'
    ];

    const IN_PROGRESS = 1;
    const COMPLETED = 2;

    // Can be used to get all products related to this CSV import batch and rollback the import if necessary
    public function products() :HasManyThrough
    {
        return $this->hasManyThrough(Product::class, ProductImportDetail::class, 'csv_import_batch_id', 'id', 'id', 'product_id');
    }


}
