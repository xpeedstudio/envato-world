<?php 
function ew_products_update_cron_job_recurrence( $schedules ) 
{
	$schedules['weekly'] = array(
        'interval' => 604800, // 1 week in seconds
        'display'  => __( 'Once Weekly' ,"envato-world"),
    );

    if(!isset($schedules['120sec']))
    {
        $schedules['120sec'] = array(
            'display' => __( 'Every 120 Seconds', 'envato-world' ),
            'interval' => 15,
        );
    }
     
    if(!isset($schedules['180sec']))
    {
        $schedules['180sec'] = array(
        'display' => __( 'Every 180 Seconds', 'envato-world' ),
        'interval' => 180,
        );
    }
    
    return $schedules;
}
add_filter( 'cron_schedules', 'ew_products_update_cron_job_recurrence' );
   $schedule_time_data = get_option("ew_options_data");
   $schedule_time = '';
   if(isset($schedule_time_data["ew_portfolio_schedule_update_time"])){
      $schedule_time = $schedule_time_data["ew_portfolio_schedule_update_time"];
   }
 
// Hook into that action that'll fire daily
//localhost/project/wp-cron.php?doing_wp_cron for manual cron job
//define('DISABLE_WP_CRON', true);
add_action( 'ew_get_product_item_from_server_cron_action', 'ew_get_product_item_from_server_function_to_run' );
function ew_get_product_item_from_server_function_to_run() {

    $user = get_option( 'ew_envato_option_form_user' );
    $site = get_option( 'ew_envato_option_form_site' );
  
   // ew_product_cache_clear(true);
    
    $products = ewgetAllProducts($user,$site,true);
   
    if(count($products)) {
        sort($products);  
        foreach($products as $pr_key=>$pr_value){
           
           $ew_meta_id = '';
           $ew_meta_id= $pr_value["id"];
        
           global $wpdb;
           $is_product_db = $wpdb->get_results( 
           $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}postmeta WHERE `meta_key` LIKE '%envato_item_id%' AND `meta_value` = %s", 
              $ew_meta_id 
              ) 
           );
          
           // new item add to db
           if(!count($is_product_db)) {
           
              makeEwPortfolioItem($pr_value,true);
           }
           //end new item add
        }
        
     }
     ewMakeCpt(true);
     ewgetItemCategory($user,$site);
     flush_rewrite_rules();


  
}

add_action( 'ew_get_single_product_item_from_server_cron_action', 'ew_get_single_product_item_from_server_function_to_run' );

function ew_get_single_product_item_from_server_function_to_run(){
   
    ewMakeCpt(true);

}
// first schedule
if($schedule_time!='') {
    // Schedule an action if it's not already scheduled
    if ( ! wp_next_scheduled( 'ew_get_product_item_from_server_cron_action' ) ) {
    
        wp_schedule_event( time(), $schedule_time, 'ew_get_product_item_from_server_cron_action' );
    
    }
}
//second schedule

    
if ( ! wp_next_scheduled( 'ew_get_single_product_item_from_server_cron_action' ) ) {
   
   wp_schedule_event( time(), 'twicedaily' , 'ew_get_single_product_item_from_server_cron_action' );

}

