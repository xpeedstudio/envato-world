<?php
class CustomCptSettingsPage
{
    /**
     * Holds the values to be used in the fields callbacks
     */
    private $options;

    /**
     * Start up
     */
    public function __construct()
    {
        add_action( 'admin_menu', array( $this, 'add_plugin_page' ) );
        add_action( 'admin_init', array( $this, 'page_init' ) );
    }

    /**
     * Add options page
     */
    public function add_plugin_page()
    {
        // This page will be under "Settings"
        add_options_page(
            'CPT Settings', 
            __('Envato world','envato-world'), 
            'manage_options', 
            'ew-cpt-setting-admin', 
            array( $this, 'create_admin_page' )
        );

        add_submenu_page(
         'edit.php?post_type=envato-portfolio',
         __( 'Envato Settings', 'envato-world' ),
         __( 'Envato Settings', 'envato-world' ),
         'manage_options',
         'ew_rest_api_data_setting',
         array($this,'ew_rest_api_data_setting_page')
         
       );
    }
    
    /**
     * Options page callback
     */
    public function create_admin_page()
    {
        // Set class property
        $this->options = get_option( 'ew_options_data' );
        ?>
        <div class="wrap">
            <h1>CPT Settings</h1>
            <form method="post" action="options.php">
            <?php
                // This prints out all hidden setting fields
                settings_fields( 'my_option_group' );
                do_settings_sections( 'ew-cpt-setting-admin' );
                submit_button();
            ?>
            </form>
        </div>
        <?php
    }

    /**
     * Register and add settings
     */
    public function page_init()
    {        
        register_setting(
            'my_option_group', // Option group
            'ew_options_data', // Option name
            array( $this, 'sanitize' ) // Sanitize
        );

        add_settings_section(
            'setting_section_id', // ID
            __('EW CPT Settings','envato-world'), // Title
            array( $this, 'print_section_info' ), // Callback
            'ew-cpt-setting-admin' // Page
        );  

        add_settings_field(
            'token_number', // ID
            __('Envato Api Token ','envato-world'), // Title 
            array( $this, 'token_number_callback' ), // Callback
            'ew-cpt-setting-admin', // Page
            'setting_section_id' // Section           
        );
        
        add_settings_field(
         'ew_product_cache_time', // ID
         __('Envato Product Cache Time','envato-world'), // Title 
         array( $this, 'ew_product_cache_time_callback' ), // Callback
         'ew-cpt-setting-admin', // Page
         'setting_section_id' // Section           
        );
        
        add_settings_field(
         'ew_single_product_cache_time', // ID
         __('Envato Product Cache Time(single)','envato-world'), // Title 
         array( $this, 'ew_single_product_cache_time_callback' ), // Callback
         'ew-cpt-setting-admin', // Page
         'setting_section_id' // Section           
        ); 

        add_settings_field(
            'ew_portfolio_allowed_category', 
            __('Allowed Category','envato-world'), 
            array( $this, 'ew_portfolio_allowed_category_callback' ), 
            'ew-cpt-setting-admin', 
            'setting_section_id'
        );

        add_settings_field(
            'ew_portfolio_schedule_update_time', 
            __('Schedule time','envato-world'), 
            array( $this, 'ew_portfolio_schedule_update_time_callback' ), 
            'ew-cpt-setting-admin', 
            'setting_section_id'
        );

        add_settings_field(
            'title', 
            __('Title','envato-world'), 
            array( $this, 'title_callback' ), 
            'my-setting-admin', 
            'setting_section_id'
        );      
    }

    /**
     * Sanitize each setting field as needed
     *
     * @param array $input Contains all settings fields as array keys
     */
    public function sanitize( $input )
    {
        $new_input = array();
        if( isset( $input['token_number'] ) )
            $new_input['token_number'] = sanitize_text_field( $input['token_number'] );

        if( isset( $input['title'] ) )
            $new_input['title'] = sanitize_text_field( $input['title'] );

        if( isset( $input['ew_portfolio_allowed_category'] ) )
            $new_input['ew_portfolio_allowed_category'] = sanitize_text_field( $input['ew_portfolio_allowed_category'] );

        if( isset( $input['ew_product_cache_time'] ) )
            $new_input['ew_product_cache_time'] = sanitize_text_field( $input['ew_product_cache_time'] );

        if( isset( $input['ew_single_product_cache_time'] ) )
            $new_input['ew_single_product_cache_time'] = sanitize_text_field( $input['ew_single_product_cache_time'] );

        if( isset( $input['ew_portfolio_schedule_update_time'] ) )
            $new_input['ew_portfolio_schedule_update_time'] = sanitize_text_field( $input['ew_portfolio_schedule_update_time'] );
        return $new_input;
    }

    /** 
     * Print the Section text
     */
    public function print_section_info()
    {
        print 'Enter your settings below:';
    }
    public function ew_portfolio_schedule_update_time_callback(){
       
        echo '<select id="ew_portfolio_schedule_update_time" name="ew_options_data[ew_portfolio_schedule_update_time]" >';
        echo '<option value="">'.__("Select schedule","envato-world").'</option>'; 
        foreach(wp_get_schedules() as $key_time=>$schedule_time):
            if( isset($schedule_time["display"]) ): 
                if($key_time==$this->options['ew_portfolio_schedule_update_time']):  
                    echo '<option selected value="'.$key_time.'">'.$schedule_time["display"].'</option>';
                else:
                    echo '<option value="'.$key_time.'">'.$schedule_time["display"].'</option>';      
                endif;     
            endif;   
        endforeach;  
        echo '</select>';
       
    }
    public function ew_single_product_cache_time_callback(){
      printf(
         '<input type="text" id="ew_single_product_cache_time" name="ew_options_data[ew_single_product_cache_time]" value="%s" /> <span class="cache-time"> %s </span> ',
         isset( $this->options['ew_single_product_cache_time'] ) ? esc_attr( $this->options['ew_single_product_cache_time']) : '',
         esc_html__("In seconds","envato-world")
     );
    }
    public function ew_portfolio_allowed_category_callback(){

        $transient_data = get_transient( 'ew_item_category_data' );
        if(!$transient_data){
            $transient_data =[]; 
        }
      printf(
         '<input type="text" id="ew_portfolio_allowed_category" name="ew_options_data[ew_portfolio_allowed_category]" value="%s" />' .'<i> Hints: '. implode(',',$transient_data ). '</i>',
         isset( $this->options['ew_portfolio_allowed_category'] ) ? esc_attr( $this->options['ew_portfolio_allowed_category']) : ''
     );
    }
    public function ew_product_cache_time_callback(){
      printf(
         '<input type="text" id="ew_product_cache_time" name="ew_options_data[ew_product_cache_time]" value="%s" /> <span class="cache-time"> %s  </span> ',
         isset( $this->options['ew_product_cache_time'] ) ? esc_attr( $this->options['ew_product_cache_time']) : '',
         esc_html__("In seconds","envato-world")
     );
    }

    /** 
     * Get the settings option array and print one of its values
     */
    public function token_number_callback()
    {
        printf(
            '<input type="text" id="token_number" name="ew_options_data[token_number]" value="%s" />',
            isset( $this->options['token_number'] ) ? esc_attr( $this->options['token_number']) : ''
        );
    }

    /** 
     * Get the settings option array and print one of its values
     */
    public function title_callback()
    {
        printf(
            '<input type="text" id="title" name="ew_options_data[title]" value="%s" />',
            isset( $this->options['title'] ) ? esc_attr( $this->options['title']) : ''
        );
    }

    public function ew_rest_api_data_setting_page(){
       
    ?>
    
    <div class="ew-admin-inner-content">
     
      <h1> Envato Setting </h1>
      <hr/>
      <h3> Cache envato all item </h3>
      <form action="<?php echo esc_url( admin_url('admin-post.php') ); ?>" method="post">

      <label for="ew_data_type"><?php esc_html__("Data source","envato-world"); ?> </label>
      <select id="ew_data_type" name="ew_data_type" >
           <option  value="0"> <?php echo esc_html__("Select option","envato-world"); ?> </option>
           <option  value="ew_restapi"> <?php echo esc_html__("Envato Server to local","envato-world");?> </option>
           <option  value="ew_cpt"> <?php echo esc_html__("CPT cache","envato-world"); ?> </option>
      </select>
         <label for="username"><?php echo esc_html__("User","envato-world"); ?></label>
         <input value="<?php echo get_option( 'ew_envato_option_form_user' ); ?>" type="text" name="user_name" id="username" required>
         <label for="sitename"> <?php echo esc_html__("Site","envato-world"); ?> </label>
         <input value="<?php echo get_option( 'ew_envato_option_form_site' ); ?>" type="text" name="site_name" id="sitename" required>
         <br/>
         <input type="hidden" name="action" value="ew_cpt_api_settings_form">
         <input id="ew-admin-submit" type="submit" value="Submit">
      </form>
  
  
        <form action="<?php echo esc_url( admin_url('admin-post.php') ); ?>" method="post">
           <input type="hidden" name="action" value="ew_cpt_api_settings_form_cache_remove">
           <input id="ew-admin-submit" type="submit" value="Clear cache">
        </form>

        <form action="<?php echo esc_url( admin_url('admin-post.php') ); ?>" method="post">
           <input type="hidden" name="action" value="ew_cpt_api_settings_for_clear_schedule">
           <input id="ew-admin-submit" type="submit" value="Remove Schedule">
        </form>
   
     
</div>
   <?php     
   }
}

if( is_admin() ){
    $CustomCptSettingsPage = new CustomCptSettingsPage();


    add_action( 'admin_post_ew_cpt_api_settings_form', 'ew_cpt_api_settings_form' );

    function ew_cpt_api_settings_form() {
   
        $user = esc_html($_POST["user_name"]);
        $site = esc_html($_POST["site_name"]);
        $sourece = esc_html($_POST["ew_data_type"]);
  
        update_option( 'ew_envato_option_form_user', $user );
        update_option( 'ew_envato_option_form_site', $site );
        update_option( 'ew_envato_option_form_data_source', $sourece );
        $message = esc_html__("Done","envato-world");
        if( $sourece=='0' ){
              
            wp_redirect(admin_url('edit.php?axsdas=asdasd&post_type=envato-portfolio&page=ew_rest_api_data_setting&message='.$message));
        }

        if( $sourece=="ew_cpt" ){

            ewMakeCpt();
        
        } elseif ($sourece=="ew_restapi") { // server source
         
         $products = [];
         if($site!='' && $user!='') {
            $products = ewgetAllProducts($user,$site);
         }
       
         if(count($products)) {
           
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
                  makeEwPortfolioItem($pr_value);
               }
               //end new item add
            }
            flush_rewrite_rules();
         }
         ewMakeCpt();
         ewgetItemCategory($user,$site);
     
   }
   
         wp_redirect(admin_url('edit.php?post_type=envato-portfolio&page=ew_rest_api_data_setting&message='.$message));
   
    }


    add_action( 'admin_post_ew_cpt_api_settings_form_cache_remove', 'ew_cpt_api_settings_form_cache_remove' );

    function ew_cpt_api_settings_form_cache_remove(){
        $message = esc_html__("Done","envato-world");
        try{
        
            ew_product_cache_clear();
            scheduleClear();   
            wp_redirect(admin_url('edit.php?post_type=envato-portfolio&page=ew_rest_api_data_setting&message='.$message));
           
        } catch (\Exception $ex) {
            $message = $ex->getMessage();
            wp_redirect(admin_url('edit.php?post_type=envato-portfolio&page=ew_rest_api_data_setting&message='.$message));
        }
    
    
    }

    add_action("admin_post_ew_cpt_api_settings_for_clear_schedule","ew_cpt_api_settings_for_clear_schedule_callback");
    function ew_cpt_api_settings_for_clear_schedule_callback(){
        $message = esc_html__("Done","envato-world");
        try{
        
            scheduleClear();   
            wp_redirect(admin_url('edit.php?post_type=envato-portfolio&page=ew_rest_api_data_setting&message='.$message));
    
        } catch (\Exception $ex) {
            $message = $ex->getMessage();
            wp_redirect(admin_url('edit.php?post_type=envato-portfolio&page=ew_rest_api_data_setting&message='.$message));
        }
        
    }

}

   