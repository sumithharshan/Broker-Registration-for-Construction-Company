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

    var selectedDial = '+971';
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

    /* ── DOM ready ───────────────────────────────────────────────────── */
    $(function () {

        /* Country selector */
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
            updateFullPhone();
        });

        $(document).on('click', function (e) {
            if (!$(e.target).closest('#pabr-country-select').length) {
                $('#pabr-flag-dropdown').hide();
            }
            if (!$(e.target).closest('#pabr-company-wrap').length) {
                $('#pabr-dropdown-panel').hide();
            }
        });

        function updateFullPhone() {
            var raw   = $('#pabr_phone').val().replace(/^0+/, '');
            $('#pabr_phone_full').val(selectedDial + raw);
        }

        $('#pabr_phone').on('input', updateFullPhone);

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

        /* Clear company */
        $('#pabr-clear-company').on('click', function (e) {
            e.stopPropagation();
            clearCompany();
        });

        /* Company search input */
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

        /* Select result */
        $(document).on('click', '.pabr-result-item', function () {
            selectCompany($(this).data('value'));
        });

        /* Manual entry toggle */
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

        /* When typing manually and pressing Enter */
        $('#pabr_company_input').on('keydown', function (e) {
            if (e.key === 'Enter' && isManual) {
                e.preventDefault();
                var val = $(this).val().trim();
                if (val) selectCompany(val);
            }
        });

        /* Guest count stepper */
        $('#pabr-dec-btn').on('click', function () {
            var $i = $('#pabr_guests');
            var v  = parseInt($i.val(), 10) || 1;
            if (v > 1) $i.val(v - 1);
        });
        $('#pabr-inc-btn').on('click', function () {
            var $i = $('#pabr_guests');
            var v  = parseInt($i.val(), 10) || 1;
            if (v < 99) $i.val(v + 1);
        });

        /* Form submit */
        $('#pabr-registration-form').on('submit', function (e) {
            e.preventDefault();

            $('#pabr-success,#pabr-error').hide();

            var name        = $('#pabr_name').val().trim();
            var phone       = $('#pabr_phone').val().trim();
            var option_type = $('input[name="pabr_option"]:checked').val();
            var company     = $('#pabr_company').val().trim();
            var guests      = parseInt($('#pabr_guests').val(), 10);

            if (!name || !phone || !option_type || !company || guests < 1) {
                $('#pabr-error').text(pabrData.i18n.required).show();
                return;
            }

            var fullPhone = selectedDial + phone.replace(/^0+/, '');

            var $btn = $('#pabr-submit-btn');
            $btn.prop('disabled', true).text(pabrData.i18n.submitting);

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
                        $('#pabr-error').text(res.data.message).show();
                        $btn.prop('disabled', false).text(pabrData.i18n.submit);
                    }
                },
                error: function () {
                    $('#pabr-error').text('A server error occurred. Please try again.').show();
                    $btn.prop('disabled', false).text(pabrData.i18n.submit);
                }
            });
        });

    });

}(jQuery));
