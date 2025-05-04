@extends('layouts.app')

@section('title', 'Import Page')

@section('content')
    <h1 class="text-3xl font-bold mb-6">Import Batch List</h1>

<div class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
    <table id="importsTable" class="display">
        <thead>
            <tr>
                <th>Id</th>
                <th>Status</th>
                <th>Original File Name</th>
                <th>Import Date</th>
            </tr>
        </thead>
    </table>
</div>
@endsection

@section('scripts')
    @vite('resources/js/import-list.js')
@endsection
