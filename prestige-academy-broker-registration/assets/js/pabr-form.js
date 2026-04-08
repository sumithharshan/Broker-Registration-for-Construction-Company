/* Prestige Academy Broker Registration – Frontend JS */
(function ($) {
    'use strict';

    /* ── Country / phone data ─────────────────────────────────────────── */
    var countries = [
        { code: 'ae', name: 'United Arab Emirates', dial: '+971' },
        { code: 'sa', name: 'Saudi Arabia',          dial: '+966' },
        { code: 'kw', name: 'Kuwait',                dial: '+965' },
        { code: 'bh', name: 'Bahrain',               dial: '+973' },
        { code: 'qa', name: 'Qatar',                 dial: '+974' },
        { code: 'om', name: 'Oman',                  dial: '+968' },
        { code: 'jo', name: 'Jordan',                dial: '+962' },
        { code: 'eg', name: 'Egypt',                 dial: '+20'  },
        { code: 'lb', name: 'Lebanon',               dial: '+961' },
        { code: 'in', name: 'India',                 dial: '+91'  },
        { code: 'pk', name: 'Pakistan',              dial: '+92'  },
        { code: 'ph', name: 'Philippines',           dial: '+63'  },
        { code: 'gb', name: 'United Kingdom',        dial: '+44'  },
        { code: 'us', name: 'United States',         dial: '+1'   },
        { code: 'de', name: 'Germany',               dial: '+49'  },
        { code: 'fr', name: 'France',                dial: '+33'  },
        { code: 'cn', name: 'China',                 dial: '+86'  },
        { code: 'ru', name: 'Russia',                dial: '+7'   },
        { code: 'ng', name: 'Nigeria',               dial: '+234' },
        { code: 'ke', name: 'Kenya',                 dial: '+254' },
    ];

    var selectedDial        = '+971';
    var selectedCountryCode = 'ae';

    function buildCountryList(filter) {
        var $ul   = $('#pabr-country-list');
        var html  = '';
        var lower = filter ? filter.toLowerCase() : '';
        $.each(countries, function (i, c) {
            if (!lower || c.name.toLowerCase().indexOf(lower) > -1 || c.dial.indexOf(lower) > -1) {
                html += '<li data-dial="' + c.dial + '" data-code="' + c.code + '">' +
                    '<img src="' + pabrData.pluginUrl + 'assets/flags/' + c.code + '.png" class="pabr-flag-img pabr-flag-sm" alt="' + c.code + '" /> ' +
                    c.name + ' <span class="pabr-dial-opt">' + c.dial + '</span></li>';
            }
        });
        $ul.html(html);
    }

    /* ── Company autocomplete ─────────────────────────────────────────── */
    var searchTimer = null;
    var currentXhr  = null;
    var isManual    = false;

    function doSearch(term) {
        if (currentXhr) { currentXhr.abort(); }
        var $results = $('#pabr-company-results');
        $results.html('<div class="pabr-loading">Searching…</div>');

        currentXhr = $.ajax({
            url: pabrData.ajaxUrl,
            method: 'GET',
            data: { action: 'pabr_search_company', nonce: pabrData.nonce, term: term },
            success: function (res) {
                if (res.success && res.data.length) {
                    var html = '';
                    $.each(res.data, function (i, name) {
                        html += '<div class="pabr-result-item" data-value="' + $('<div>').text(name).html() + '">' +
                            $('<div>').text(name).html() + '</div>';
                    });
                    $results.html(html);
                } else {
                    $results.html('<div class="pabr-no-results">No matches found.</div>');
                }
            },
            error: function () {
                $results.html('');
            }
        });
    }

    function selectCompany(val) {
        $('#pabr_company').val(val);
        $('#pabr-company-text').text(val);
        $('#pabr-clear-company').show();
        $('#pabr-dropdown-panel').hide();
        clearFieldError('company');
        isManual = false;
    }

    function clearCompany() {
        $('#pabr_company').val('');
        $('#pabr-company-text').text('Select or type company name…');
        $('#pabr-clear-company').hide();
        $('#pabr_company_input').val('').attr('placeholder', 'Type 3+ characters to search…');
        $('#pabr-company-results').html('');
        $('#pabr-other-toggle').show();
        isManual = false;
    }

    /* ── Field error helpers ─────────────────────────────────────────── */
    function showFieldError(field, message) {
        clearFieldError(field);
        var $wrap;
        if (field === 'name')    $wrap = $('#pabr_name').closest('.pabr-field');
        if (field === 'phone')   $wrap = $('#pabr_phone').closest('.pabr-field');
        if (field === 'option')  $wrap = $('input[name="pabr_option"]').closest('.pabr-field');
        if (field === 'company') $wrap = $('#pabr-company-wrap').closest('.pabr-field');
        if (field === 'guests')  $wrap = $('#pabr_guests').closest('.pabr-field');
        if ($wrap && $wrap.length) {
            $wrap.addClass('pabr-field-error');
            $wrap.append('<span class="pabr-field-msg">' + $('<div>').text(message).html() + '</span>');
        } else {
            $('#pabr-error').text(message).show();
        }
    }

    function clearFieldError(field) {
        var $wrap;
        if (field === 'name')    $wrap = $('#pabr_name').closest('.pabr-field');
        if (field === 'phone')   $wrap = $('#pabr_phone').closest('.pabr-field');
        if (field === 'option')  $wrap = $('input[name="pabr_option"]').closest('.pabr-field');
        if (field === 'company') $wrap = $('#pabr-company-wrap').closest('.pabr-field');
        if (field === 'guests')  $wrap = $('#pabr_guests').closest('.pabr-field');
        if ($wrap && $wrap.length) {
            $wrap.removeClass('pabr-field-error');
            $wrap.find('.pabr-field-msg').remove();
        }
    }

    function clearAllErrors() {
        $('.pabr-field').removeClass('pabr-field-error');
        $('.pabr-field-msg').remove();
        $('#pabr-success,#pabr-error').hide();
    }

    /* ── Client-side validation ──────────────────────────────────────── */
    function validateForm() {
        clearAllErrors();
        var valid = true;

        var name    = $('#pabr_name').val().trim();
        var phone   = $('#pabr_phone').val().trim();
        var company = $('#pabr_company').val().trim();
        var guests  = parseInt($('#pabr_guests').val(), 10);

        if (!name) {
            showFieldError('name', 'Name is required.');
            valid = false;
        }
        if (!phone) {
            showFieldError('phone', 'Phone number is required.');
            valid = false;
        }
        if (!company) {
            showFieldError('company', 'Please select or enter a company name.');
            valid = false;
        }
        if (isNaN(guests) || guests < 1) {
            showFieldError('guests', 'Guest count must be at least 1.');
            valid = false;
        }
        if (guests > 99) {
            showFieldError('guests', 'Guest count cannot exceed 99.');
            valid = false;
        }

        return valid;
    }

    /* ── DOM ready ───────────────────────────────────────────────────── */
    $(function () {

        buildCountryList('');

        $('#pabr-selected-flag').on('click', function (e) {
            e.stopPropagation();
            var $dd = $('#pabr-flag-dropdown');
            $dd.toggle();
            if ($dd.is(':visible')) { $('#pabr-country-search').focus(); }
        });

        $('#pabr-country-search').on('input', function () {
            buildCountryList($(this).val());
        });

        $(document).on('click', '#pabr-country-list li', function () {
            selectedDial        = $(this).data('dial');
            selectedCountryCode = $(this).data('code');
            $('#pabr-dial-code').text(selectedDial);
            $('#pabr-selected-flag img').attr('src', pabrData.pluginUrl + 'assets/flags/' + selectedCountryCode + '.png');
            $('#pabr-flag-dropdown').hide();
        });

        $(document).on('click', function (e) {
            if (!$(e.target).closest('#pabr-country-select').length) {
                $('#pabr-flag-dropdown').hide();
            }
            if (!$(e.target).closest('#pabr-company-wrap').length) {
                $('#pabr-dropdown-panel').hide();
            }
        });

        /* Company dropdown toggle */
        $('#pabr-company-display').on('click', function () {
            var $panel = $('#pabr-dropdown-panel');
            if ($panel.is(':visible')) {
                $panel.hide();
            } else {
                $panel.show();
                $('#pabr_company_input').focus();
            }
        });

        $('#pabr-clear-company').on('click', function (e) {
            e.stopPropagation();
            clearCompany();
        });

        $('#pabr_company_input').on('input', function () {
            var term = $(this).val().trim();
            clearTimeout(searchTimer);
            if (term.length < 3) {
                $('#pabr-company-results').html(
                    term.length ? '<div class="pabr-hint">Type ' + (3 - term.length) + ' more character(s)…</div>' : ''
                );
                return;
            }
            searchTimer = setTimeout(function () { doSearch(term); }, 250);
        });

        $(document).on('click', '.pabr-result-item', function () {
            selectCompany($(this).data('value'));
        });

        $('#pabr-other-toggle').on('click', function () {
            var typed = $('#pabr_company_input').val().trim();
            if (typed) {
                selectCompany(typed);
            } else {
                $('#pabr_company_input').attr('placeholder', 'Enter your company name…').focus();
                isManual = true;
                $('#pabr-other-toggle').hide();
            }
        });

        /* Block Enter key on all fields except textarea.
           Exception: company search input — Enter saves the typed value. */
        $('#pabr-registration-form').on('keydown', function (e) {
            if (e.key !== 'Enter') return;
            if (e.target.tagName.toLowerCase() === 'textarea') return;

            if (e.target.id === 'pabr_company_input') {
                e.preventDefault();
                var val = $('#pabr_company_input').val().trim();
                if (val) selectCompany(val);
                return;
            }

            e.preventDefault();
        });

        /* Clear per-field errors on user interaction */
        $('#pabr_name').on('input', function () { clearFieldError('name'); });
        $('#pabr_phone').on('input', function () { clearFieldError('phone'); });
        $('input[name="pabr_option"]').on('change', function () { clearFieldError('option'); });
        $('#pabr_guests').on('input', function () { clearFieldError('guests'); });

        /* Guest count stepper */
        $('#pabr-dec-btn').on('click', function () {
            var $i = $('#pabr_guests');
            var v  = parseInt($i.val(), 10) || 1;
            if (v > 1) { $i.val(v - 1); clearFieldError('guests'); }
        });
        $('#pabr-inc-btn').on('click', function () {
            var $i = $('#pabr_guests');
            var v  = parseInt($i.val(), 10) || 0;
            if (v < 99) { $i.val(v + 1); clearFieldError('guests'); }
        });

        /* Form submit */
        $('#pabr-registration-form').on('submit', function (e) {
            e.preventDefault();

            if (!validateForm()) return;

            var name        = $('#pabr_name').val().trim();
            var phone       = $('#pabr_phone').val().trim();
            var option_type = $('input[name="pabr_option"]:checked').val();
            var company     = $('#pabr_company').val().trim();
            var guests      = parseInt($('#pabr_guests').val(), 10);
            var fullPhone   = selectedDial + phone.replace(/^0+/, '');

            var $btn = $('#pabr-submit-btn');
            $btn.prop('disabled', true).text('Submitting…');

            $.ajax({
                url: pabrData.ajaxUrl,
                method: 'POST',
                data: {
                    action:       'pabr_submit',
                    nonce:        pabrData.nonce,
                    pabr_name:    name,
                    pabr_email:   $('#pabr_email').val().trim(),
                    pabr_phone:   fullPhone,
                    pabr_option:  option_type,
                    pabr_company: company,
                    pabr_guests:  guests,
                    pabr_message: $('#pabr_message').val().trim(),
                    pabr_browser: navigator.userAgent,
                },
                success: function (res) {
                    if (res.success) {
                        $('#pabr-registration-form').hide();
                        $('#pabr-success').html(
                            res.data.message + '<br><small>Entry Key: <strong>' + res.data.entry_key + '</strong></small>'
                        ).show();
                    } else {
                        var field = res.data && res.data.field ? res.data.field : '';
                        var msg   = res.data && res.data.message ? res.data.message : 'An error occurred. Please try again.';
                        if (field) {
                            showFieldError(field, msg);
                        } else {
                            $('#pabr-error').text(msg).show();
                        }
                        $btn.prop('disabled', false).text('Submit Registration');
                    }
                },
                error: function () {
                    $('#pabr-error').text('A server error occurred. Please try again.').show();
                    $btn.prop('disabled', false).text('Submit Registration');
                }
            });
        });

    });

}(jQuery));
