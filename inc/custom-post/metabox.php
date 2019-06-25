<?php
function envato_setting_meta_box_callback($post){
   // Add a nonce field so we can check for it later.
   wp_nonce_field( 'envato_item_id_nonce', 'envato_item_id_nonce' );

   $value_id = get_post_meta( $post->ID, 'ew_envato_item_id', true );
 
   $value_url = get_post_meta( $post->ID, 'ew_envato_item_url', true );
   $value_license_url = get_post_meta( $post->ID, 'ew_envato_item_license_url', true );
   $value_user = get_post_meta( $post->ID, 'ew_envato_item_user', true );
   $value_sales = get_post_meta( $post->ID, 'ew_envato_item_sales', true );
   $value_rating = get_post_meta( $post->ID, 'ew_envato_item_rating', true );
   $value_cost = get_post_meta( $post->ID, 'ew_envato_item_cost', true );
   $value_uploaded_on = get_post_meta( $post->ID, 'ew_envato_item_uploaded_on', true );
   $value_updated_on = get_post_meta( $post->ID, 'ew_envato_item_updated_on', true );
   $value_tags = get_post_meta( $post->ID, 'ew_envato_item_tags', true );
   $value_category = get_post_meta( $post->ID, 'ew_envato_item_category', true );
  
 ?>
 <div class="container">
      <div class="row">
         <div class="col-25">
            <label for="envato_item_id"> <?php echo esc_html__("Item ID","envato-world"); ?> </label>
         </div>
         <div class="col-75">
           <input type="text"  id="envato_item_id" name="envato_item_id" value="<?php echo esc_attr($value_id); ?>">
         </div>

     
         
         <div class="col-25">
            <label for="envato_item_url"><?php echo esc_html__(" Item url","envato-world"); ?> </label>
         </div>
         <div class="col-75">
            <input type="text" width="100%" size="100"  id="envato_item_url" name="envato_item_url" value="<?php echo esc_attr($value_url); ?>">
         </div>
         <div class="col-25">
            <label for="envato_item_url"><?php echo esc_html__("Item license url","envato-world"); ?> </label>
         </div>
         <div class="col-75">
            <input type="text" width="100%" size="100"  id="envato_item_license_url" name="envato_item_license_url" value="<?php echo esc_attr($value_license_url); ?>">
         </div>

            
         <div class="col-25">
            <label for="envato_item_user"><?php echo esc_html__("Item user","envato-world"); ?> </label>
         </div>
         <div class="col-75">
            <input type="text" width="100%" size="100"  id="envato_item_user" name="envato_item_user" value="<?php echo esc_attr($value_user); ?>">
         </div>

         <div class="col-25">
            <label for="envato_item_sales"><?php echo esc_html__("Item sales","envato-world"); ?> </label>
         </div>
         <div class="col-75">
            <input type="text" width="100%" size="100"  id="envato_item_sales" name="envato_item_sales" value="<?php echo esc_attr($value_sales); ?>">
         </div>

         <div class="col-25">
            <label for="envato_item_rating"><?php echo esc_html__("Item rating","envato-world"); ?> </label>
         </div>
         <div class="col-75">
            <input type="text" width="100%" size="100"  id="envato_item_rating" name="envato_item_rating" value="<?php echo esc_attr($value_rating); ?>">
         </div>

         <div class="col-25">
            <label for="envato_item_cost"><?php echo esc_html__("Item cost","envato-world"); ?> </label>
         </div>
         <div class="col-75">
            <input type="text" width="100%" size="100"  id="envato_item_cost" name="envato_item_cost" value="<?php echo esc_attr($value_cost); ?>">
         </div>

         
         <div class="col-25">
            <label for="envato_item_uploaded_on"><?php echo esc_html__("Item uploaded on","envato-world"); ?> </label>
         </div>
         <div class="col-75">
            <input type="text" id="envato_item_uploaded_on" name="envato_item_uploaded_on" value="<?php echo esc_attr($value_uploaded_on); ?>">
         </div>

         <div class="col-25">
            <label for="envato_item_updated_on"><?php echo esc_html__("Item updated on","envato-world"); ?> </label>
         </div>
         <div class="col-75">
            <input type="text" id="envato_item_updated_on" name="envato_item_updated_on" value="<?php echo esc_attr($value_updated_on); ?>">
         </div>

         <div class="col-25">
            <label for="envato_item_tags"><?php echo esc_html__("Item tags","envato-world"); ?> </label>
         </div>
         <div class="col-75">
            <input type="text" width="100%" size="100"  id="envato_item_tags" name="envato_item_tags" value="<?php echo esc_attr($value_tags); ?>">
         </div>

         <div class="col-25">
            <label for="envato_item_category"><?php echo esc_html__("Item category","envato-world"); ?> </label>
         </div>
         <div class="col-75">
            <input type="text" width="100%" size="100"  id="envato_item_category" name="envato_item_category" value="<?php echo esc_attr($value_category); ?>">
         </div>

      </div>
 </div>
 <?php

}
 function global_notice_meta_box() {

   $screens = array( 'envato-portfolio' );

   foreach ( $screens as $screen ) {
       add_meta_box(
           'envato-settings',
           __( 'Envato Settings', 'envato-world' ),
           'envato_setting_meta_box_callback',
           $screen
       );
   }
}

add_action( 'add_meta_boxes', 'global_notice_meta_box' );

function save_envato_setting_meta_box_callback( $post_id ) {

   if ( ! isset( $_POST['envato_item_id_nonce'] ) ) {
       return;
   }
   if ( ! wp_verify_nonce( $_POST['envato_item_id_nonce'], 'envato_item_id_nonce' ) ) {
       return;
   }
   if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
       return;
   }
   if ( ! current_user_can( 'edit_post', $post_id ) || 'envato-portfolio' != $_POST['post_type'] ) {
      return;
   }   
   if ( ! isset( $_POST['envato_item_id'] ) ) {
       return;
   }
   $my_data_id = sanitize_text_field( $_POST['envato_item_id'] );

   $my_data_url = sanitize_text_field( $_POST['envato_item_url'] );
   $my_data_license_url = sanitize_text_field( $_POST['envato_item_license_url'] );
   $my_data_user = sanitize_text_field( $_POST['envato_item_user'] );
   $my_data_sales = sanitize_text_field( $_POST['envato_item_sales'] );
   $my_data_rating = sanitize_text_field( $_POST['envato_item_rating'] );
   $my_data_cost = sanitize_text_field( $_POST['envato_item_cost'] );
   $my_data_uploaded_on = sanitize_text_field( $_POST['envato_item_uploaded_on'] );
   $my_data_updated_on = sanitize_text_field( $_POST['envato_item_updated_on'] );
   $my_data_tags = sanitize_text_field( $_POST['envato_item_tags'] );
   $my_data_category = sanitize_text_field( $_POST['envato_item_category'] );
   update_post_meta( $post_id, 'ew_envato_item_id', $my_data_id );

   update_post_meta( $post_id, 'ew_envato_item_url', $my_data_url );
   update_post_meta( $post_id, 'ew_envato_item_license_url', $my_data_license_url );
   update_post_meta( $post_id, 'ew_envato_item_user', $my_data_user );
   update_post_meta( $post_id, 'ew_envato_item_sales', $my_data_sales );
   update_post_meta( $post_id, 'ew_envato_item_rating', $my_data_rating );
   update_post_meta( $post_id, 'ew_envato_item_cost', $my_data_cost );
   update_post_meta( $post_id, 'ew_envato_item_uploaded_on', $my_data_uploaded_on );
   update_post_meta( $post_id, 'ew_envato_item_updated_on', $my_data_updated_on );
   update_post_meta( $post_id, 'ew_envato_item_tags', $my_data_tags );
   update_post_meta( $post_id, 'ew_envato_item_category', $my_data_category );
}

add_action( 'save_post', 'save_envato_setting_meta_box_callback' );

// title
