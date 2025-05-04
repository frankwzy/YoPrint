<?php

namespace App\Http\Controllers;

use App\Http\Resources\ProductResource;
use App\Models\CsvImportBatch;
use App\Models\Product;

class ProductController extends Controller
{

    public function index() 
    {
        return view('welcome');
    }

    public function getProductsList()
    {
        $products = Product::all();

        return response()->json(['data' => ProductResource::collection($products)]);
    }

    public function importListPage()
    {
        return view('import-list');
    }

    public function getImports()
    {
        $imports = CsvImportBatch::all();

        return response()->json(['data' => $imports]);
    }
}
