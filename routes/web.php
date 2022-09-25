<?php

use App\Models\Invoice;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    $invoice = Invoice::query()
        ->with('details')
        ->firstOrCreate();

    // hard-coded for simplicity
    $services = [
        (object) ['service_name' => 'First', 'amount' => 10],
        (object) ['service_name' => 'Second', 'amount' => 20],
        (object) ['service_name' => 'Third', 'amount' => 30],
    ];

    return view('welcome', [
        'invoice' => $invoice,
        'services' => $services,
    ]);
});

Route::post('/details', function (\Illuminate\Http\Request $request) {
    /** @var \App\Models\Invoice $invoice */
    $invoice = Invoice::query()->firstOrCreate();

    $validated = $request->validate([
        'items' => ['array', 'required'],
        'items.*.service' => ['required'],
        'items.*.amount' => ['required'],
        'items.*.description' => ['required'],
    ]);

    $invoice->details()->createMany($validated['items']);

    return back()->with('success', 'saved');
});
