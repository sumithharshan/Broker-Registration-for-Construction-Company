<?php if ( ! defined( 'ABSPATH' ) ) exit;
$per_page = isset( $atts['per_page'] ) ? absint( $atts['per_page'] ) : 25;
?>
<div class="pabr-fe-wrap">

    <!-- Toolbar -->
    <div class="pabr-fe-toolbar">
        <div class="pabr-fe-filters">
            <select id="pabr-fe-filter-option" class="pabr-fe-select">
                <option value="">All Options</option>
                <option value="Broker">Broker</option>
                <option value="Individual">Individual</option>
                <option value="Client">Client</option>
            </select>

            <input type="text" id="pabr-fe-filter-company" placeholder="Filter by company…" class="pabr-fe-input" />

            <input type="date" id="pabr-fe-date-from" class="pabr-fe-input pabr-fe-date" title="From date" />
            <span class="pabr-fe-date-sep">to</span>
            <input type="date" id="pabr-fe-date-to" class="pabr-fe-input pabr-fe-date" title="To date" />

            <button id="pabr-fe-reset" class="pabr-fe-btn pabr-fe-btn-outline">Reset</button>
        </div>

        <div class="pabr-fe-actions">
            <button id="pabr-fe-export-all" class="pabr-fe-btn pabr-fe-btn-primary">&#8595; Export All (CSV)</button>
        </div>
    </div>

    <!-- Summary -->
    <div class="pabr-fe-summary" id="pabr-fe-summary"></div>

    <!-- Table -->
    <div class="pabr-fe-table-scroll">
        <table id="pabr-fe-table" class="pabr-fe-table display stripe hover" style="width:100%">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Entry Key</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Option</th>
                    <th>Company</th>
                    <th>Guests</th>
                    <th>Submitted At</th>
                    <th>Message</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ( $entries as $i => $row ) : ?>
                <tr>
                    <td><?php echo esc_html( $row['id'] ); ?></td>
                    <td><code class="pabr-fe-key"><?php echo esc_html( $row['entry_key'] ); ?></code></td>
                    <td><?php echo esc_html( $row['name'] ); ?></td>
                    <td><?php echo esc_html( $row['email'] ); ?></td>
                    <td><?php echo esc_html( $row['phone'] ); ?></td>
                    <td>
                        <span class="pabr-fe-badge pabr-fe-badge-<?php echo esc_attr( strtolower( $row['option_type'] ) ); ?>">
                            <?php echo esc_html( $row['option_type'] ); ?>
                        </span>
                    </td>
                    <td><?php echo esc_html( $row['company'] ); ?></td>
                    <td class="pabr-fe-center"><?php echo esc_html( $row['guest_count'] ); ?></td>
                    <td data-order="<?php echo esc_attr( $row['submitted_at'] ); ?>"><?php echo esc_html( $row['submitted_at'] ); ?></td>
                    <td class="pabr-fe-msg" title="<?php echo esc_attr( $row['message'] ); ?>">
                        <?php echo esc_html( wp_trim_words( $row['message'], 8, '…' ) ); ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
