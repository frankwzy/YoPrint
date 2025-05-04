@extends('layouts.app')

@section('title', 'Main Page')

@section('content')
    <h1 class="text-3xl font-bold mb-6">Products</h1>

<div class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
    <table id="productsTable" class="display">
        <thead>
            <tr>
                <th>Unique Key</th>
                <th>Title</th>
                <th>Description</th>
                <th>Style</th>
                <th>Sanmar Color</th>
                <th>Size</th>
                <th>Color</th>
                <th>Piece Price</th>
            </tr>
        </thead>
    </table>
</div>
@endsection

@section('scripts')
    @vite('resources/js/welcome.js')
@endsection
