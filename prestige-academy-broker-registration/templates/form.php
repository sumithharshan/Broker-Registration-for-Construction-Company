<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>
<div class="pabr-wrap" id="pabr-form-wrap">
    <div class="pabr-alert pabr-alert-success" id="pabr-success" style="display:none;"></div>
    <div class="pabr-alert pabr-alert-error"   id="pabr-error"   style="display:none;"></div>

    <form id="pabr-registration-form" novalidate autocomplete="off">

        <!-- Name -->
        <div class="pabr-field">
            <label for="pabr_name"><?php esc_html_e( 'Name', 'prestige-academy-reg' ); ?></label>
            <input type="text" id="pabr_name" name="pabr_name" placeholder="" />
        </div>

        <!-- Email -->
        <div class="pabr-field">
            <label for="pabr_email"><?php esc_html_e( 'Email', 'prestige-academy-reg' ); ?></label>
            <input type="email" id="pabr_email" name="pabr_email" placeholder="" />
        </div>

        <!-- Phone -->
        <div class="pabr-field">
            <label for="pabr_phone"><?php esc_html_e( 'Phone', 'prestige-academy-reg' ); ?></label>
            <div class="pabr-phone-wrap">
                <div class="pabr-country-select" id="pabr-country-select">
                    <div class="pabr-selected-flag" id="pabr-selected-flag">
                        <img src="<?php echo esc_url( PABR_PLUGIN_URL . 'assets/flags/ae.png' ); ?>" alt="UAE" class="pabr-flag-img" />
                        <span class="pabr-code" id="pabr-dial-code">+971</span>
                        <span class="pabr-arrow">&#9660;</span>
                    </div>
                    <div class="pabr-flag-dropdown" id="pabr-flag-dropdown" style="display:none;">
                        <input type="text" id="pabr-country-search" placeholder="Search country..." class="pabr-country-search" />
                        <ul id="pabr-country-list"></ul>
                    </div>
                </div>
                <input type="tel" id="pabr_phone" name="pabr_phone" placeholder="" class="pabr-phone-input" />
            </div>
            <input type="hidden" id="pabr_phone_full" name="pabr_phone_full" />
        </div>

        <!-- Options -->
        <div class="pabr-field">
            <label><?php esc_html_e( 'Options', 'prestige-academy-reg' ); ?> <span class="pabr-req">*</span></label>
            <div class="pabr-radio-group">
                <label class="pabr-radio-label">
                    <input type="radio" name="pabr_option" value="Broker" checked />
                    <span class="pabr-radio-custom"></span>
                    <?php esc_html_e( 'Broker', 'prestige-academy-reg' ); ?>
                </label>
                <label class="pabr-radio-label">
                    <input type="radio" name="pabr_option" value="Individual" />
                    <span class="pabr-radio-custom"></span>
                    <?php esc_html_e( 'Individual', 'prestige-academy-reg' ); ?>
                </label>
                <label class="pabr-radio-label">
                    <input type="radio" name="pabr_option" value="Client" />
                    <span class="pabr-radio-custom"></span>
                    <?php esc_html_e( 'Client', 'prestige-academy-reg' ); ?>
                </label>
            </div>
        </div>

        <!-- Company Name -->
        <div class="pabr-field">
            <label for="pabr_company_input"><?php esc_html_e( 'Company Name', 'prestige-academy-reg' ); ?> <span class="pabr-req">*</span></label>
            <div class="pabr-autocomplete-wrap" id="pabr-company-wrap">
                <div class="pabr-company-display" id="pabr-company-display">
                    <span id="pabr-company-text"><?php esc_html_e( 'Select or type company name…', 'prestige-academy-reg' ); ?></span>
                    <button type="button" class="pabr-clear-btn" id="pabr-clear-company" style="display:none;" title="Clear">&#10005;</button>
                    <span class="pabr-chevron">&#9660;</span>
                </div>
                <div class="pabr-dropdown-panel" id="pabr-dropdown-panel" style="display:none;">
                    <input type="text" id="pabr_company_input" placeholder="Type 3+ characters to search…" autocomplete="off" class="pabr-company-search-input" />
                    <div class="pabr-company-results" id="pabr-company-results"></div>
                    <div class="pabr-other-option" id="pabr-other-toggle">
                        <span><?php esc_html_e( '+ Enter company name manually', 'prestige-academy-reg' ); ?></span>
                    </div>
                </div>
            </div>
            <input type="hidden" id="pabr_company" name="pabr_company" required />
        </div>

        <!-- Guest Count — always visible -->
        <div class="pabr-field">
            <label for="pabr_guests"><?php esc_html_e( 'Guest Count', 'prestige-academy-reg' ); ?> <span class="pabr-req">*</span></label>
            <div class="pabr-number-wrap">
                <button type="button" class="pabr-num-btn" id="pabr-dec-btn">&#8722;</button>
                <input type="number" id="pabr_guests" name="pabr_guests" value="1" min="1" max="99" required />
                <button type="button" class="pabr-num-btn" id="pabr-inc-btn">&#43;</button>
            </div>
        </div>

        <!-- Message -->
        <div class="pabr-field">
            <label for="pabr_message"><?php esc_html_e( 'Message', 'prestige-academy-reg' ); ?></label>
            <textarea id="pabr_message" name="pabr_message" rows="5" placeholder=""></textarea>
        </div>

        <div class="pabr-field">
            <button type="submit" class="pabr-submit-btn" id="pabr-submit-btn">
                <?php esc_html_e( 'Submit Registration', 'prestige-academy-reg' ); ?>
            </button>
        </div>

    </form>
</div>
