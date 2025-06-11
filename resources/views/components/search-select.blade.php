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
<style>
.select2-container--bootstrap4 .select2-selection {
    border-radius: 0.375rem !important;
    border: 1px solid #ced4da !important;
    min-height: 38px !important;
    box-shadow: none !important;
    background: #fff !important;
}
.select2-container--bootstrap4 .select2-selection--single .select2-selection__rendered {
    line-height: 36px !important;
    padding-left: 12px !important;
}
.select2-container--bootstrap4 .select2-selection--single .select2-selection__arrow {
    height: 36px !important;
    right: 8px !important;
}
.select2-container--bootstrap4 .select2-selection--single {
    display: flex;
    align-items: center;
}
.select2-container--bootstrap4 .select2-selection__clear {
    color: #dc3545 !important;
    font-size: 1.2em !important;
    margin-right: 8px !important;
}
.select2-container--bootstrap4 .select2-search--dropdown .select2-search__field {
    border-radius: 0.375rem !important;
    border: 1px solid #ced4da !important;
    padding: 6px 12px !important;
}
</style>
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
                            // Determine 'text' for display and 'data' for the full object
                            // This logic handles both contractor (item.name) and client (item.text) structures
                            var displayText = item.text || item[displayField];
                            var fullData = item.data || item; // Use item.data if present, else the whole item

                            return {
                                id: item.id,
                                text: displayText,
                                data: fullData
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
                // Always show data.text (which is pre-formatted from processResults)
                var html = '<div><strong>' + (data.text || '') + '</strong>';
                var emailToDisplay = data.data && data.data.email ? data.data.email : '';
                if (emailToDisplay) {
                    html += '<br><small>' + emailToDisplay + '</small>';
                }
                html += '</div>';
                return $(html);
            },
            templateSelection: function(data) {
                return data.text;
            }
        })
        // Show results on focus if minimumInputLength is 0
        .on('focus', function() {
            if (parseInt(minimumInputLength) === 0) {
                $(this).select2('open');
            }
        })
        // Also show on click for some browsers
        .on('click', function() {
            if (parseInt(minimumInputLength) === 0) {
                $(this).select2('open');
            }
        })
        .on('select2:select', function(e) {
            // Trigger a custom event with the full selected item data
            var selected = e.params.data;
            var targetId = $(this).attr('id');
            document.dispatchEvent(new CustomEvent('select2-item:selected', {
                detail: {
                    id: targetId,
                    data: selected.data
                }
            }));
        });
    });
});
</script>
@endpush 