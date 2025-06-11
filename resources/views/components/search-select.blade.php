@props([
    'name',
    'label',
    'placeholder' => 'Search...',
    'url',
    'value' => null,
    'displayField' => 'name',
    'required' => false,
    'multiple' => false,
    'tags' => false,
    'allowClear' => true,
    'minimumInputLength' => 2,
    'delay' => 250
])

<div class="form-group">
    <label for="{{ $name }}">{{ $label }} @if($required) <span class="text-danger">*</span> @endif</label>
    <select 
        name="{{ $name }}" 
        id="{{ $name }}" 
        class="form-control search-select @error($name) is-invalid @enderror"
        data-url="{{ $url }}"
        data-display-field="{{ $displayField }}"
        data-placeholder="{{ $placeholder }}"
        data-minimum-input-length="{{ $minimumInputLength }}"
        data-delay="{{ $delay }}"
        {{ $multiple ? 'multiple' : '' }}
        {{ $tags ? 'data-tags="true"' : '' }}
        {{ $allowClear ? 'data-allow-clear="true"' : '' }}
        {{ $required ? 'required' : '' }}
        {{ $attributes }}
    >
        @if($value)
            @if(is_array($value))
                @foreach($value as $item)
                    <option value="{{ $item->id }}" selected>{{ $item->{$displayField} }}</option>
                @endforeach
            @else
                <option value="{{ $value->id }}" selected>{{ $value->{$displayField} }}</option>
            @endif
        @endif
    </select>
    @error($name)
        <span class="invalid-feedback">{{ $message }}</span>
    @enderror
</div>

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
$(document).ready(function() {
    $('.search-select').each(function() {
        var $select = $(this);
        var url = $select.data('url');
        var displayField = $select.data('display-field');
        var minimumInputLength = $select.data('minimum-input-length');
        var delay = $select.data('delay');
        var tags = $select.data('tags');
        var allowClear = $select.data('allow-clear');

        $select.select2({
            theme: 'bootstrap4',
            placeholder: $select.data('placeholder'),
            allowClear: allowClear,
            minimumInputLength: minimumInputLength,
            tags: tags,
            ajax: {
                url: url,
                dataType: 'json',
                delay: delay,
                data: function(params) {
                    return {
                        search: params.term,
                        page: params.page || 1
                    };
                },
                processResults: function(data, params) {
                    params.page = params.page || 1;

                    return {
                        results: data.data.map(function(item) {
                            return {
                                id: item.id,
                                text: item[displayField],
                                data: item
                            };
                        }),
                        pagination: {
                            more: data.current_page < data.last_page
                        }
                    };
                },
                cache: true
            },
            templateResult: function(data) {
                if (data.loading) {
                    return data.text;
                }

                var $container = $(
                    "<div class='select2-result'>" +
                        "<div class='select2-result__text'>" + data.text + "</div>" +
                    "</div>"
                );

                return $container;
            },
            templateSelection: function(data) {
                return data.text;
            }
        });
    });
});
</script>
@endpush 