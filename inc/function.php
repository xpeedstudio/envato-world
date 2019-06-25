<?php 
require_once(ABSPATH . 'wp-admin/includes/media.php');
require_once(ABSPATH . 'wp-admin/includes/file.php');
require_once(ABSPATH . 'wp-admin/includes/image.php');

function ewShortNumber($num) 
{   

   if(!is_numeric($num)){
     return 0;
   }
    $units = ['', 'K', 'M', 'B', 'T'];
    for ($i = 0; $num >= 1000; $i++) {
        $num /= 1000;
    }
    return round($num, 1) . $units[$i];
}
function scheduleClear(){
 
   try{
      
      update_option('cron', '');
      return true;
   }catch (\Exception $ex) {
         return false;
      }
}
function ew_product_cache_clear($schedule=false){
   try{
   set_time_limit(1200);
   $user = strtolower(get_option( 'ew_envato_option_form_user' ));
   global $wpdb;
   $results = $wpdb->get_results( "DELETE FROM {$wpdb->prefix}options WHERE `option_name` LIKE ('%_ew_%')" );
   $attachment_data = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}posts WHERE `post_title` LIKE '%ew_attachment_from_remote%' AND `post_type` LIKE '%attachment%'" );
   foreach($attachment_data as $post){
      wp_delete_attachment($post->ID);
   }
      
   $args=array(
      'post_type'   => 'envato-portfolio',
      'order'       => 'DESC', 
      'numberposts' => -1,
   //    'meta_query' => array(
   //       array(
   //           'key' => 'ew_envato_item_user',
   //           'value' => $user,
   //           'compare' => 'LIKE'
   //       )
   //   )
   );
   $product_items = get_posts($args);
   
   foreach($product_items  as $value) {
         $post_id = $value->ID;
         if($post_id){
            wp_delete_post($post_id,true); 
         }
        
   }
     
   return true;

   }catch (\Exception $ex) {
     return false;
  }
  
}

function ewgetAllProducts( $user ="xpeedstudio", $site ="themeforest", $schedule=false ){
   $cache_time_data  = get_option("ew_options_data");
   $cache_time = 60*60;
   if(isset($cache_time_data["ew_product_cache_time"])){
      $cache_time = $cache_time_data["ew_product_cache_time"];
   }
  
   $transient_data = get_transient( 'ew_all_product_data' );
   if( ! empty( $transient_data ) && $schedule==false ) {
     
      return $transient_data;
   }

   $latest_items_data = [];
   $token_number = 'UtYTHyPDvL7Rn753jdqaf4pyZqmoMqOq';
   $token_data = get_option("ew_options_data");
   
   if( isset($token_data["token_number"]) ) {
      if(strlen($token_data["token_number"])>12){
         if(ewvalidateApiToken($token_number)){
            $token_number = $token_data["token_number"]; 
         }
      }
   }

  

     $user = strtolower( get_option( 'ew_envato_option_form_user' ) );
     $site = strtolower( get_option( 'ew_envato_option_form_site' ) );


  
   $url = "https://api.envato.com/v1/market/new-files-from-user:".$user.",".$site.".json";
   $args = array(
      'headers' => array(
      'Authorization' => 'Bearer '.$token_number,
      "Content-type" => "application/json" 
      )
   );

   $data          = wp_remote_get( $url, $args );
   $response_code =  wp_remote_retrieve_response_code($data);
 
   if($response_code==200){
     
     $body = wp_remote_retrieve_body( $data );
     $content_body = \json_decode($body,true);
     
     if(isset($content_body["new-files-from-user"]) ) {
         $latest_items_data = $content_body["new-files-from-user"];
        
         set_transient("ew_all_product_data", $latest_items_data, $cache_time );
         return $latest_items_data;
       
      }
   }// success 
   //
   return $latest_items_data;
}

function ewgetItemCategory( $user ="xpeedstudio", $site ="themeforest",$schedule=false ){
   $cache_time_data  = get_option("ew_options_data");
   $allowed_cats = ["wordpress","blog-magazine","business","site-templates","portfolio","woocommerce"];
   $cache_time = 0;
   
   if(isset($cache_time_data["ew_portfolio_allowed_category"])){
       if($cache_time_data["ew_portfolio_allowed_category"]!=''){
         $allowed_cats = explode(",",$cache_time_data["ew_portfolio_allowed_category"]);
       }
   }
   
   $transient_data = get_transient( 'ew_item_category_data' );
  
   if( ! empty( $transient_data ) ) {
      
      return $transient_data;
   }

   $token_number = 'UtYTHyPDvL7Rn753jdqaf4pyZqmoMqOq';
   $token_data = get_option("ew_options_data");
   $category = [];
   if(isset($token_data["token_number"])){
      if(strlen($token_data["token_number"])>12){
         if(ewvalidateApiToken($token_number)){
            $token_number = $token_data["token_number"]; 
         }
      }
   }
   if($schedule){

      $user = strtolower( get_option( 'ew_envato_option_form_user' ) );
      $site = strtolower( get_option( 'ew_envato_option_form_site' ) );
      
    }
   $url = "https://api.envato.com/v1/market/new-files-from-user:".$user.",".$site.".json";
  
   $args = array(
      'headers' => array(
      'Authorization' => 'Bearer '.$token_number,
      "Content-type" => "application/json" 
      )
   );
 
   $data          = wp_remote_get( $url, $args );
   $response_code =  wp_remote_retrieve_response_code($data);
 
   if($response_code==200){
  
     $body = wp_remote_retrieve_body( $data );
     $content_body = \json_decode($body,true);
     
     if(isset($content_body["new-files-from-user"]) ) {
         $latest_items_data = $content_body["new-files-from-user"];
         $category = filterCategory($latest_items_data); 
         set_transient("ew_item_category_data", $category );
        
      }
   }else{// success 
      return $allowed_cats;
   }
   //
   return $category;
}

 function filterCategory($items = null ){
   $allcats = [];
   $category_list = [];
    if($items==null){
       return [];
    }
    foreach ($items as $key => $value) { 
       if(isset($value["category"])){
          $explode  =  explode("/",$value["category"]);  
          if(is_array($explode)){
              foreach($explode as $item){
                $allcats[$item] = $item;
              } 
          }  
       }
    }
   return $allcats;
 }
 

function ewSingleitemdata($item_id=null,$schedule=false){

   if(is_null($item_id) || $item_id==''){
     return [];
   }
   $cache_time_data  = get_option("ew_options_data");
   $cache_time = 129200;
   if(isset($cache_time_data["ew_single_product_cache_time"])){
      $cache_time = $cache_time_data["ew_single_product_cache_time"];
   }
  
   $transient_id = 'ew_single_item_data_'.$item_id;
   $transient_data = get_transient( $transient_id );
  
   if( ! empty( $transient_data ) && $schedule==false ) {
      
      return $transient_data;
   }
   
   if($item_id>0) {
        
         $token_number = 'UtYTHyPDvL7Rn753jdqaf4pyZqmoMqOq';
         $token_data = get_option("ew_options_data");
         if(isset($token_data["token_number"])){
            if(strlen($token_data["token_number"])>12){
               if(ewvalidateApiToken($token_number)){
                  $token_number = $token_data["token_number"]; 
               }
            }
         }
       
         $url = "https://api.envato.com/v3/market/catalog/item?id=".$item_id;
         $args = array(
            'headers' => array(
            'Authorization' => 'Bearer '.$token_number,
            "Content-type" => "application/json" 
            )
         );

         $data          = wp_remote_get( $url, $args );
         $response_code =  wp_remote_retrieve_response_code($data);
            if($response_code==200){
               $body = wp_remote_retrieve_body( $data );
               $content_body = \json_decode($body,true);
               set_transient($transient_id, $content_body, $cache_time );
               return $content_body;
         }// success 
   } //api end  

   return [];
    
}

function ewvalidateApiToken($token = ''){
  
   if($token==''){
      return false;
   }
   $token = trim($token);
   $url = "https://api.envato.com/v1/market/features:themeforest.json";
   $args = array(
      'headers' => array(
      'Authorization' => 'Bearer '.$token,
      "Content-type" => "application/json" 
      )
   );

   $data          = wp_remote_get( $url, $args );
   $response_code =  wp_remote_retrieve_response_code($data);
      if($response_code==200){
        return true;
   }// success 
  
   return false;

}

function ewMakeCpt($schedule=false){
   $args=array(
      'post_type'   => 'envato-portfolio',
      'order'       => 'DESC', 
      'numberposts' => -1,
  );
  $product_items = get_posts($args);

  foreach($product_items  as $value) {
       $post_id = $value->ID;
       $value_id = get_post_meta( $post_id, 'ew_envato_item_id',true);
       ewSingleitemdata($value_id,$schedule);
  }

}

function makeEwPortfolioItem($item_value,$schedule=false){
   
      try{
     
      if(!count($item_value)) {
         return null;
      }
    
      $post_title = '';
      $item_live_url = '';
      if(isset($item_value["item"])){
         $post_title = $item_value["item"];
      }
      if(isset($item_value["live_preview_url"])) {
         $item_live_url = $item_value["live_preview_url"];
      }
   
      $ew_single_cpt_args = array(
         'post_title'   => $post_title, 
         'post_type'    => 'envato-portfolio', 
         'post_content' =>'',
         'post_status'  => 'publish'
      ); 

      $success_post_id = wp_insert_post($ew_single_cpt_args); 
      
      if($success_post_id){
       
      $my_data_id = $item_value["id"];
      $my_data_url = $item_value["url"];
      $my_data_license_url = '';
      $my_data_user = $item_value["user"];
      $my_data_sales =$item_value["sales"];
      $my_data_rating = $item_value["rating"];
      $my_data_cost = $item_value["cost"];
      $my_data_uploaded_on = $item_value["uploaded_on"];
      $my_data_updated_on = $item_value["last_update"];
      $my_data_tags = $item_value["tags"];
      $my_data_category = str_replace("/",",",$item_value["category"]);
       
      $tr= \media_sideload_image( $item_live_url, $success_post_id,'ew_attachment_from_remote' );
            
      $attachments = get_posts( array(
       'post_type' => 'attachment',
       'post_mime_type'=>'image', 
       'posts_per_page' => 1,
       'post_parent' => $success_post_id,
      
       ) );

       foreach ($attachments as $attachment) {
          set_post_thumbnail($success_post_id, $attachment->ID);
          break;
       }
   

      update_post_meta( $success_post_id, 'ew_envato_item_id', $my_data_id );
      update_post_meta( $success_post_id, 'ew_envato_item_url', $my_data_url );
      update_post_meta( $success_post_id, 'ew_envato_item_license_url', $my_data_license_url );
      update_post_meta( $success_post_id, 'ew_envato_item_user', $my_data_user );
      update_post_meta( $success_post_id, 'ew_envato_item_sales', $my_data_sales );
      update_post_meta( $success_post_id, 'ew_envato_item_rating', $my_data_rating );
      update_post_meta( $success_post_id, 'ew_envato_item_cost', $my_data_cost );
      update_post_meta( $success_post_id, 'ew_envato_item_uploaded_on', $my_data_uploaded_on );
      update_post_meta( $success_post_id, 'ew_envato_item_updated_on', $my_data_updated_on );
      update_post_meta( $success_post_id, 'ew_envato_item_tags', $my_data_tags );
      update_post_meta( $success_post_id, 'ew_envato_item_category', $my_data_category );
      
     }

   return $success_post_id; 

   } catch (\Exception $ex) {
      return false;
   }
}


//debug
if (!function_exists('ew_store_log')) {

   function ew_store_log($log) {
       if (true === WP_DEBUG) {
           if (is_array($log) || is_object($log)) {
               error_log(print_r($log, true));
           } else {
               error_log($log);
           }
       }
   }

}

if (!function_exists('ew_is_connected')) {
   function ew_is_connected() {
      $connected = @fsockopen("www.google.com", 80); //website, port  (try 80 or 443)                                      
      if ($connected){
         fclose($connected);       
         return true;
      }
      return false;
   }
}

function ew_clear_all_crons( $hook ) {
   $crons = _get_cron_array();
   if ( empty( $crons ) ) {
       return;
   }
   foreach( $crons as $timestamp => $cron ) {
       if ( ! empty( $cron[$hook] ) )  {
           unset( $crons[$timestamp][$hook] );
       }

       if ( empty( $crons[$timestamp] ) ) {
           unset( $crons[$timestamp] );
       }
   }
   _set_cron_array( $crons );
}