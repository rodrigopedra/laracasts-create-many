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

    <section id="items">
        @foreach($invoice->items as $item)
            @include('item', ['services' => $services, 'item' => $item, 'index' => $loop->index])
        @endforeach
    </section>

    <button type="button" class="btn btn-success" id="add-service">Add Service</button>
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
    let $items = $('#items');
    let itemTemplate = $('#item-template').get(0).content;
    let removeTemplate = $('#remove-template').get(0).content;
    let emptyTemplate = $('#empty-template').get(0).content;

    $('#add-service').on('click', addItem);
    $items.on('click', 'button.remove-service', removeItem);
    $items.on('change', 'select[id^=service]', setAmount);

    verifyEmpty();

    function addItem() {
        let index = nextIndex();
        let $clone = $(itemTemplate.cloneNode(true));

        if (index === 0) {
            $items.empty();
        }

        $clone.data('id', index);

        $clone.find('[id^=id]').attr('name', `items[${index}][id]`);

        $clone.find('[id^=service]')
            .attr('name', `items[${index}][service]`)
            .attr('id', `service-${index}`);

        $clone.find('[id^=amount]')
            .attr('name', `items[${index}][amount]`)
            .attr('id', `amount-${index}`);

        $clone.find('[id^=description]')
            .attr('name', `items[${index}][description]`)
            .attr('id', `description-${index}`);

        $clone.appendTo($items);
    }

    function removeItem() {
        if (!confirm('Remove this service?')) {
            return;
        }

        let $service = $(this).parents('.service-group').first();
        let serviceId = $service.find('[name$="[id]"]').val();

        $service.remove();
        verifyEmpty();

        if (!serviceId) {
            return;
        }

        $(removeTemplate.cloneNode(true)).find('input').val(serviceId).appendTo($items);
    }

    function verifyEmpty() {
        if ($items.find('.service-group').length === 0) {
            $(emptyTemplate.cloneNode(true)).appendTo($items);
        }
    }

    function setAmount() {
        let $select = $(this);
        let amount = $select.find('option:selected').data('amount');

        $select.parents('.service-group').first().find('input[id^=amount]').val(amount);
    }

    function nextIndex() {
        let indices = $items
            .find('.service-group')
            .map((index, element) => Number($(element).data('index')));

        if (indices.length === 0) {
            return 0;
        }

        return Math.max(...indices) + 1;
    }
});
</script>
</body>
</html>
