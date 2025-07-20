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