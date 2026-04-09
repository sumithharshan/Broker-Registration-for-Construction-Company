/* Prestige Academy - Frontend Entries Table */
jQuery(function ($) {
    'use strict';

    if ( !$('#pabr-fe-table').length ) return;

    var table = null;
    var perPage = parseInt($('#pabr-fe-table').data('per-page'), 10) || 25;

    /* -- Summary bar ------------------------------------------------ */
    function updateSummary() {
        if ( !table ) return;
        var info     = table.page.info();
        var filtered = table.rows({ filter: 'applied' }).data();
        var guests   = 0;
        filtered.each(function (row) {
            guests += parseInt(row[7], 10) || 0;
        });
        $('#pabr-fe-summary').html(
            '<strong>' + info.recordsDisplay + '</strong> registration(s) &nbsp;|&nbsp; ' +
            '<strong>' + guests + '</strong> total guest(s)'
        );
    }

    table = $('#pabr-fe-table').DataTable({
        order: [[8, 'desc']],
        pageLength: perPage,
        lengthMenu: [10, 25, 50, 100],
        dom: 'lBfrtip',
        buttons: [
            {
                extend: 'csvHtml5',
                text: 'Export Visible (CSV)',
                title: 'registrations',
                bom: true,
                className: 'pabr-fe-dt-btn',
                exportOptions: { columns: ':visible' }
            }
        ],
        language: {
            search: '',
            searchPlaceholder: 'Search all columns...',
            lengthMenu: 'Show _MENU_',
            info: 'Showing _START_-_END_ of _TOTAL_ entries',
            paginate: { first: '<<', last: '>>', next: '>', previous: '<' },
            emptyTable: 'No registrations found.',
            zeroRecords: 'No matching records.',
        },
        columnDefs: [
            { targets: [9], orderable: false },
        ],
        scrollX: true,
        autoWidth: false,
        drawCallback: function () { updateSummary(); },
        initComplete: function () { updateSummary(); },
    });

    /* -- Option filter --------------------------------------------- */
    $('#pabr-fe-filter-option').on('change', function () {
        table.column(5).search($(this).val()).draw();
    });

    /* -- Company filter -------------------------------------------- */
    var companyTimer;
    $('#pabr-fe-filter-company').on('input', function () {
        var val = $(this).val();
        clearTimeout(companyTimer);
        companyTimer = setTimeout(function () {
            table.column(6).search(val).draw();
        }, 300);
    });

    /* -- Date range filter ----------------------------------------- */
    $.fn.dataTable.ext.search.push(function (settings, data) {
        if (settings.nTable.id !== 'pabr-fe-table') return true;
        var from = $('#pabr-fe-date-from').val();
        var to   = $('#pabr-fe-date-to').val();
        if (!from && !to) return true;
        var cellDate = data[8] ? data[8].substring(0, 10) : '';
        if (!cellDate) return true;
        if (from && cellDate < from) return false;
        if (to   && cellDate > to)   return false;
        return true;
    });

    $('#pabr-fe-date-from, #pabr-fe-date-to').on('change', function () {
        table.draw();
    });

    /* -- Reset ----------------------------------------------------- */
    $('#pabr-fe-reset').on('click', function () {
        $('#pabr-fe-filter-option').val('');
        $('#pabr-fe-filter-company').val('');
        $('#pabr-fe-date-from').val('');
        $('#pabr-fe-date-to').val('');
        table.column(5).search('');
        table.column(6).search('');
        table.search('').draw();
    });

    /* -- Export all (server-side CSV, all records) ----------------- */
    $('#pabr-fe-export-all').on('click', function () {
        window.location.href = pabrFeData.ajaxUrl +
            '?action=pabr_fe_export&nonce=' + pabrFeData.nonce;
    });
});
