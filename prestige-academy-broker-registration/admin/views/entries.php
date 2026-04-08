<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>
<div class="wrap pabr-admin-wrap">
    <h1 class="pabr-admin-title">
        <?php esc_html_e( 'Academy Broker Registrations', 'prestige-academy-reg' ); ?>
        <span class="pabr-total-badge"><?php echo count( $entries ); ?> <?php esc_html_e( 'entries', 'prestige-academy-reg' ); ?></span>
    </h1>

    <div class="pabr-toolbar">
        <!-- Filters -->
        <div class="pabr-filters">
            <select id="pabr-filter-option" class="pabr-select">
                <option value=""><?php esc_html_e( 'All Options', 'prestige-academy-reg' ); ?></option>
                <option value="Broker"><?php esc_html_e( 'Broker', 'prestige-academy-reg' ); ?></option>
                <option value="Individual"><?php esc_html_e( 'Individual', 'prestige-academy-reg' ); ?></option>
                <option value="Client"><?php esc_html_e( 'Client', 'prestige-academy-reg' ); ?></option>
            </select>

            <input type="text" id="pabr-filter-company" placeholder="<?php esc_attr_e( 'Filter by company…', 'prestige-academy-reg' ); ?>" class="pabr-filter-input" />

            <input type="date" id="pabr-filter-date-from" class="pabr-filter-input" title="<?php esc_attr_e( 'From date', 'prestige-academy-reg' ); ?>" />
            <span class="pabr-date-sep"><?php esc_html_e( 'to', 'prestige-academy-reg' ); ?></span>
            <input type="date" id="pabr-filter-date-to" class="pabr-filter-input" title="<?php esc_attr_e( 'To date', 'prestige-academy-reg' ); ?>" />

            <button id="pabr-reset-filters" class="button"><?php esc_html_e( 'Reset', 'prestige-academy-reg' ); ?></button>
        </div>

        <!-- Export -->
        <div class="pabr-export-btns">
            <button id="pabr-export-excel" class="button button-primary">
                &#8595; <?php esc_html_e( 'Export Excel (CSV)', 'prestige-academy-reg' ); ?>
            </button>
        </div>
    </div>

    <table id="pabr-entries-table" class="display stripe hover nowrap pabr-entries-table" style="width:100%">
        <thead>
            <tr>
                <th><?php esc_html_e( '#',            'prestige-academy-reg' ); ?></th>
                <th><?php esc_html_e( 'Entry Key',    'prestige-academy-reg' ); ?></th>
                <th><?php esc_html_e( 'Name',         'prestige-academy-reg' ); ?></th>
                <th><?php esc_html_e( 'Email',        'prestige-academy-reg' ); ?></th>
                <th><?php esc_html_e( 'Phone',        'prestige-academy-reg' ); ?></th>
                <th><?php esc_html_e( 'Option',       'prestige-academy-reg' ); ?></th>
                <th><?php esc_html_e( 'Company',      'prestige-academy-reg' ); ?></th>
                <th><?php esc_html_e( 'Guests',       'prestige-academy-reg' ); ?></th>
                <th><?php esc_html_e( 'IP Address',   'prestige-academy-reg' ); ?></th>
                <th><?php esc_html_e( 'Submitted At', 'prestige-academy-reg' ); ?></th>
                <th><?php esc_html_e( 'Message',      'prestige-academy-reg' ); ?></th>
                <th><?php esc_html_e( 'Browser',      'prestige-academy-reg' ); ?></th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ( $entries as $row ) : ?>
            <tr>
                <td><?php echo esc_html( $row['id'] ); ?></td>
                <td><code><?php echo esc_html( $row['entry_key'] ); ?></code></td>
                <td><?php echo esc_html( $row['name'] ); ?></td>
                <td><?php echo esc_html( $row['email'] ); ?></td>
                <td><?php echo esc_html( $row['phone'] ); ?></td>
                <td>
                    <span class="pabr-badge pabr-badge-<?php echo esc_attr( strtolower( $row['option_type'] ) ); ?>">
                        <?php echo esc_html( $row['option_type'] ); ?>
                    </span>
                </td>
                <td><?php echo esc_html( $row['company'] ); ?></td>
                <td class="pabr-center"><?php echo esc_html( $row['guest_count'] ); ?></td>
                <td><?php echo esc_html( $row['ip_address'] ); ?></td>
                <td data-order="<?php echo esc_attr( $row['submitted_at'] ); ?>"><?php echo esc_html( $row['submitted_at'] ); ?></td>
                <td class="pabr-msg-cell" title="<?php echo esc_attr( $row['message'] ); ?>">
                    <?php echo esc_html( wp_trim_words( $row['message'], 8, '…' ) ); ?>
                </td>
                <td class="pabr-browser-cell" title="<?php echo esc_attr( $row['browser'] ); ?>">
                    <?php echo esc_html( PABR_Admin::parse_browser( $row['browser'] ) ); ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
