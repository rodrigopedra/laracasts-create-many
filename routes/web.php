<?php

use App\Models\Invoice;
use App\Models\InvoiceDetail;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\Rule;

Route::get('/', function () {
    $invoice = Invoice::query()
        ->with('items')
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

Route::get('/alternative', function () {
    $invoice = Invoice::query()
        ->with('items')
        ->firstOrCreate();

    if ($invoice->items->isEmpty()) {
        // the JS code expects at list one item to be present
        $invoice->setRelation('items', Collection::make([new InvoiceDetail()]));
    }

    // hard-coded for simplicity
    $services = [
        (object) ['service_name' => 'First', 'amount' => 10],
        (object) ['service_name' => 'Second', 'amount' => 20],
        (object) ['service_name' => 'Third', 'amount' => 30],
    ];

    return view('alternative', [
        'invoice' => $invoice,
        'services' => $services,
    ]);
});

Route::post('/items/{invoice}', function (Request $request, Invoice $invoice) {
    $validated = $request->validate([
        'items' => ['array'],
        'items.*.id' => [
            'nullable',
            Rule::exists('invoice_details', 'id')
                ->where('invoice_id', $invoice->getKey()),
        ],
        'items.*.service' => ['required'],
        'items.*.amount' => ['required'],
        'items.*.description' => ['required'],
        'remove' => ['array'],
        'remove.*' => [
            'required',
            Rule::exists('invoice_details', 'id')
                ->where('invoice_id', $invoice->getKey()),
        ],
    ]);

    [$newItems, $oldItems] = collect($validated['items'] ?? [])
        ->map(function ($item) use ($invoice) {
            $item['invoice_id'] = $invoice->getKey();

            return $item;
        })
        ->partition(fn ($item) => blank($item['id'] ?? null));

    $invoice->items()->createMany($newItems);
    $invoice->items()->upsert($oldItems->all(), ['id'], ['service', 'amount', 'description']);

    if (array_key_exists('remove', $validated)) {
        $invoice->items()->whereKey($validated['remove'])->delete();
    }

    return back()->with('success', 'saved');
});
