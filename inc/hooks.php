<?php
function ew_items_setting_notice($data) {
    ?>
    <?php if( isset($_GET["post_type"]) && isset($_GET["page"]) && isset($_GET["message"]) ): ?>
        <?php if($_GET["post_type"] == "envato-portfolio" && $_GET["page"] =="ew_rest_api_data_setting"): ?>
            <div class="notice notice-success is-dismissible">
                <p><?php echo esc_html($_GET["message"]); ?></p>
            </div>
        <?php endif; ?>    
    <?php endif; ?>       
    <?php
}
add_action( 'admin_notices', 'ew_items_setting_notice' );

register_deactivation_hook(EW_FILE, 'ew_schedule_deactivation');

function ew_schedule_deactivation() {
    ew_store_log("All EW schedules have been removed");
	wp_clear_scheduled_hook('ew_get_product_item_from_server_cron_action');
	wp_clear_scheduled_hook('ew_get_single_product_item_from_server_cron_action');
}

