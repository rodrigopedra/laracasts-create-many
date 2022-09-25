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

<form method="POST" action="/details" class="container">
    @csrf

    @if($message = session()->pull('success'))
        <p class="alert alert-success">{{ $message }}</p>
    @endif

    <h1>Invoice #{{ $invoice->getKey() }}</h1>
    <hr>

    <h2>Items</h2>

    <section id="details">
        @forelse($invoice->details as $item)
            @include('item', ['index' => $loop->index, 'item' => $item])
        @empty
            <p class="text-center">
                <em>no items added</em>
            </p>
            <hr>
        @endforelse
    </section>

    <button type="button" class="btn btn-success" id="add-details">Add details</button>
    <button type="submit" class="btn btn-primary">Save</button>
</form>

<template id="details-template">
    @include('item', ['index' => '##INDEX##', 'item' => null])
</template>

<script>
$(function () {
    let $details = $('#details');
    let $template = $('#details-template');

    $('#add-details').on('click', addItem);
    $details.on('change', setAmount);

    function addItem() {
        let index = nextIndex();
        let template = $template.html();

        if (index === 0) {
            $details.empty();
        }

        $details.append(template.replaceAll('##INDEX##', index));
    }

    function nextIndex() {
        let indices = $details
            .find('.service-group')
            .map((index, element) => Number($(element).data('index')));

        if (indices.length === 0) {
            return 0;
        }

        return Math.max(...indices) + 1;
    }

    function setAmount(event) {
        let $target = $(event.target);

        if (!$target.is('[id^=service]')) {
            return;
        }

        let amount = $target.find('option:selected').data('amount');

        $target.closest('.service-group').find('[id^=amount]').val(amount);
    }
});
</script>
</body>
</html>
