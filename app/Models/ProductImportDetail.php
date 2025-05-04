<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductImportDetail extends Model
{
    protected $table = 'product_import_details';

    protected $guarded = [
        'id',
        'created_at',
        'updated_at'
    ];
    
}
