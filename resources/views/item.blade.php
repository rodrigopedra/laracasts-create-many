<div class="service-group border-bottom pb-3 mb-3" data-index="{{ $index }}">
    <div class="row">
        <div class="form-group col-md-6">
            <label class="form-label" for="service-{{ $index }}">
                Service
            </label>

            <select class="custom-select" required
                    name="items[{{ $index }}][service]"
                    id="service-{{ $index }}">
                <option value="" disabled selected>Select your option</option>

                @foreach ($services as $service)
                    <option value="{{$service->service_name}}"
                            @selected($item?->service_name == $service->service_name)
                            data-amount="{{$service->amount}}">
                        {{$service->service_name}}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="form-group col-md-6">
            <label class="form-label" for="amount-{{ $index }}">
                Amount
            </label>

            <input type="text" class="form-control" readonly required
                   name="items[{{ $index }}][amount]"
                   id="amount-{{ $index }}"
                   placeholder="Amount"
                   value="{{ $item?->amount }}">
        </div>
    </div>

    <div class="form-group">
        <label class="form-label" for="description-{{ $index }}">
            Description
        </label>

        <textarea class="form-control" rows="6" required
                  name="items[{{ $index }}][description]"
                  id="description-{{ $index }}"
                  placeholder="Description..">{{ $item?->description }}</textarea>
    </div>
</div>
