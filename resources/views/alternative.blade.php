<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Multi-index</title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css"
          integrity="sha384-xOolHFLEh07PJGoPkLv1IbcEPTNtaed2xpHsD9ESMhqIYd0nLMwNLD69Npy4HI+N"
          crossorigin="anonymous">

    <script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.slim.min.js"
            integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj"
            crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"
            integrity="sha384-Fy6S3B9q64WdZWQUiU+q4/2Lc9npb8tCaSX9FK7E8HnRr0Jz8D6OP9dO5Vg3Q9ct"
            crossorigin="anonymous"></script>
</head>
<body>

<form method="POST" action="/items/{{ $invoice->getKey() }}" class="container my-3">
    @csrf

    <h1>Invoice #{{ $invoice->getKey() }}</h1>
    <hr>

    @if($message = session()->pull('success'))
        <p class="alert alert-success">{{ $message }}</p>
    @endif

    <h2>Items</h2>

    <section class="more-service-box" id="items">
        @foreach($invoice->items as $item)
            @include('item', ['services' => $services, 'item' => $item, 'index' => $loop->index])
        @endforeach
    </section>

    <button type="button" class="btn btn-success" id="addmore">Add Service</button>
    <button type="submit" class="btn btn-primary">Save</button>
</form>

<template id="item-template">
    @include('item', ['services' => $services, 'item' => null, 'index' => ''])
</template>

<template id="remove-template">
    <input type="hidden" name="remove[]" value="">
</template>

<template id="empty-template">
    <p class="text-center"><em>no items added</em></p>
    <hr>
</template>

<script>
$(function () {
    // Add Service , Amount, Description
$('#addmore').click(function () {
    var index = $('.service-group').length;
    var $service = $('.service-group:first').clone();

    $service.find('select[name*=service]')
        .val('')
        .attr('name', 'items[' + index + '][service]')
        .attr('id', 'service-' + index);

    $service.find('input[name*=amount]')
        .val('')
        .attr('name', 'items[' + index + '][amount]')
        .attr('id', 'amount-' + index);

    $service.find('textarea[name*=description]')
        .val('')
        .attr('name', 'items[' + index + '][description]')
        .attr('id', 'description-' + index);

    $service.appendTo('.more-service-box');
});
});
</script>
</body>
</html>
