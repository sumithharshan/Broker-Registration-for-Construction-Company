/* Prestige Academy Broker Registration – Admin JS */
jQuery(function ($) {
    'use strict';

    var table = $('#pabr-entries-table').DataTable({
        order: [[9, 'desc']],
        pageLength: 25,
        lengthMenu: [10, 25, 50, 100, 200],
        dom: 'lBfrtip',
        buttons: [
            {
                extend: 'csvHtml5',
                text: '&#8595; Export Visible (CSV)',
                title: 'pabr-registrations',
                bom: true,
                className: 'pabr-dt-btn'
            }
        ],
        language: {
            search: 'Search:',
            lengthMenu: 'Show _MENU_ entries',
            info: 'Showing _START_ to _END_ of _TOTAL_ entries',
            paginate: {
                first: '«', last: '»', next: '›', previous: '‹'
            }
        },
        columnDefs: [
            { targets: [10, 11], orderable: false }
        ],
        scrollX: true,
        autoWidth: false,
    });

    /* ── Custom column filters ─────────────────────────────────────── */

    /* Option filter */
    $('#pabr-filter-option').on('change', function () {
        table.column(5).search($(this).val()).draw();
    });

    /* Company filter */
    var companyTimer;
    $('#pabr-filter-company').on('input', function () {
        var val = $(this).val();
        clearTimeout(companyTimer);
        companyTimer = setTimeout(function () {
            table.column(6).search(val).draw();
        }, 300);
    });

    /* Date range filter */
    $.fn.dataTable.ext.search.push(function (settings, data) {
        var from = $('#pabr-filter-date-from').val();
        var to   = $('#pabr-filter-date-to').val();
        if (!from && !to) return true;
        var cellDate = data[9] ? data[9].substring(0, 10) : '';
        if (!cellDate) return true;
        if (from && cellDate < from) return false;
        if (to   && cellDate > to)   return false;
        return true;
    });

    $('#pabr-filter-date-from, #pabr-filter-date-to').on('change', function () {
        table.draw();
    });

    /* Reset filters */
    $('#pabr-reset-filters').on('click', function () {
        $('#pabr-filter-option').val('');
        $('#pabr-filter-company').val('');
        $('#pabr-filter-date-from').val('');
        $('#pabr-filter-date-to').val('');
        table.column(5).search('');
        table.column(6).search('');
        table.search('').draw();
    });

    /* ── Export all (server-side CSV) ─────────────────────────────── */
    $('#pabr-export-excel').on('click', function () {
        var url = pabrAdminData.ajaxUrl +
            '?action=pabr_export_excel&nonce=' + pabrAdminData.nonce;
        window.location.href = url;
    });
});
