<div class="list_cemeteries">
    @foreach ($cemeteries as $cemetery)
        <label class="flex_input_checkbox my-2 checkbox">
            <input type="checkbox" class="cemetery_checkbox" value="{{ $cemetery->id }}"
                   data-cemetery-name="{{ $cemetery->title }}"
                   data-cemetery-address="{{ $cemetery->adres }}">
            {{ $cemetery->title }} ({{ $cemetery->adres }})
        </label>
    @endforeach
</div>
