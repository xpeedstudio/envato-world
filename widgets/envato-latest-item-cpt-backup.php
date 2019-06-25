<?php
namespace Envatoworld\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Elementor Hello World
 *
 * Elementor widget for hello world.
 *
 * @since 1.0.0
 */
class Envato_latest_item_cpt extends Widget_Base {

	/**
	 * Retrieve the widget name.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'envato-latest-items-cpt';
	}

	/**
	 * Retrieve the widget title.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return __( 'Themeforest latest item', 'envato-world' );
	}

	/**
	 * Retrieve the widget icon.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'eicon-post-list';
	}

	/**
	 * Retrieve the list of categories the widget belongs to.
	 *
	 * Used to determine where to display the widget in the editor.
	 *
	 * Note that currently Elementor supports only one category.
	 * When multiple categories passed, Elementor uses the first one.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 *
	 * @return array Widget categories.
	 */
	public function get_categories() {
		return [ 'general' ];
	}


	/**
	 * Register the widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @since 1.0.0
	 *
	 * @access protected
	 */
	protected function _register_controls() {
		$this->start_controls_section(
			'section_content',
			[
				'label' => __( 'Content', 'envato-world' ),
			]
		);

		$this->add_control(
			'title',
			[
				'label' => __( 'Title', 'envato-world' ),
				'type' => Controls_Manager::TEXT,
			]
      );
      $this->add_control(
			'feature_style',
			[
				'label' => __( 'Feature style', 'envato-world' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'label_on' => __( 'Enable', 'envato-world' ),
				'label_off' => __( 'Disable', 'envato-world' ),
				'return_value' => 'yes',
				'default' => 'yes',
			]
		);


      $this->add_control(
			'user',
			[
				'label' => __( 'User', 'envato-world' ),
            'type' => Controls_Manager::TEXT,
            'default' => 'xpeedstudio'
			]
      );

      $this->add_control(
			'site',
			[
				'label' => __( 'Site', 'envato-world' ),
            'type' => Controls_Manager::TEXT,
             'default' => 'themeforest'
 			]
      );

     
		$this->end_controls_section();

		$this->start_controls_section(
			'section_style',
			[
				'label' => __( 'Style', 'envato-world' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		

		$this->end_controls_section();
	}

	/**
	 * Render the widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.0.0
	 *
	 * @access protected
	 */
	protected function render() {
      
     
      $allowed_cats = ["wordpress","blog-magazine","joomla","site-templates","portfolio","woocommerce"];
      $ew_options_data = get_option("ew_options_data");
      
      $latest_items =[];
      $settings = $this->get_settings_for_display();
      $feature_style = $settings["feature_style"];
      $user = strtolower( get_option( 'ew_envato_option_form_user' ) );
      $site = strtolower( get_option( 'ew_envato_option_form_site' ) );
      $category = [];
      
      $category = ewgetItemCategory($user,$site);
      
      if(isset($ew_options_data["ew_portfolio_allowed_category"])){
         if($ew_options_data["ew_portfolio_allowed_category"]!=''){
            $allowed_cats = explode(',',$ew_options_data["ew_portfolio_allowed_category"]); 
         }
      }
   
     // success 
     
      $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
      $args=array(
         'post_type'   => 'envato-portfolio',
         'order'       => 'DESC', 
         'posts_per_page' => 20,
         'post_status' => 'publish',
         'paged'=>$paged
      );
      $latest_items = new \WP_Query($args);
  
   ?>

<!-- MAIN (Center website) -->
<div class="main">
   <h2> <?php echo $settings["title"]; ?> </h2>

   <div id="myBtnContainer">
     <button id="btn" class="active" onclick="filterSelection('all')"> <?php echo esc_html__("Show all","envato-world");  ?> </button>
         <?php foreach($category as $item): ?>
            <?php if (in_array($item, $allowed_cats)): ?>
               <button id="btn" onclick="filterSelection('<?php echo esc_attr($item); ?>')"> <?php echo esc_html($item=="site-templates"?"html":$item); ?> </button>
            <?php endif; ?>   

         <?php endforeach; ?>
   </div>

   <?php if ($latest_items->have_posts()): ?>
      <div class="row ew-portfolio">
      <?php while ($latest_items->have_posts()) : $latest_items->the_post(); ?>
         <?php 
         $item_type = "mixed";
         $post_id = get_the_ID();
         $value_id = get_post_meta( $post_id, 'ew_envato_item_id',true);
            
         $value_url = get_post_meta( $post_id, 'ew_envato_item_url',true);
         $value_user = get_post_meta( $post_id, 'ew_envato_item_user',true);
         $value_sales = get_post_meta( $post_id, 'ew_envato_item_sales',true);
         $value_rating = get_post_meta( $post_id, 'ew_envato_item_rating',true);
         $value_cost = get_post_meta( $post_id, 'ew_envato_item_cost',true);
         $value_uploaded_on = get_post_meta( $post_id, 'ew_envato_item_uploaded_on',true);
         $value_uploaded_on= date('M d, Y', strtotime($value_uploaded_on));
         $value_updated_on = get_post_meta( $post_id, 'ew_envato_item_updated_on',true);
         $value_updated_on = date('M d, Y', strtotime($value_updated_on));
         $value_tags = get_post_meta( $post_id, 'ew_envato_item_tags',true);
         $value_category = get_post_meta( $post_id, 'ew_envato_item_category',true);
         $product_icon_class = "";
         
         if($value_category!=''){
            $classification = explode(',',$value_category);
            if(in_array("wordpress",$classification) ){
               $item_type = esc_html__('wordpress',"envato-world");
               $product_icon_class = "wordpress";
            }elseif(in_array("site-templates",$classification) ){
               $item_type = esc_html__('html',"envato-world");
               $product_icon_class = "html5";
            }elseif(in_array("joomla",$classification) ){
               $item_type = esc_html__('joomla',"envato-world");
               $product_icon_class = "joomla";
            }
            elseif(in_array("shopping",$classification) || in_array("retail",$classification) ){
               $item_type = esc_html__('ecommerce',"envato-world");
               $product_icon_class = "shopping-cart";
            }
         }
        
      
         ?>
         <div class="column col-lg-4 col-md-6 <?php echo esc_html($value_category); ?>">
             <img  class="img-responsive" src="<?php echo esc_url(get_the_post_thumbnail_url($post_id)); ?>" alt="<?php echo get_the_title(); ?>" >
               <?php if($feature_style=="yes"): ?>
                  <div class="feature-price">
                     <span> <?php echo esc_html("$".(int)$value_cost); ?> </span>
                  </div>
               <?php endif; ?>
           <div class="content <?php echo esc_attr($feature_style=="yes"?"feature-content":""); ?>">
               <div class="portfolio">
                  <div class="date float-left"><?php echo esc_html__("Updated:","envato-world"); ?>  <?php echo esc_html($value_updated_on); ?> </div>
                  <div class="float-right"> 
                     <span class="fa fa-star <?php echo esc_html($value_rating>0?"checked":''); ?>"></span>
                     <span class="fa fa-star <?php echo esc_html($value_rating>1?"checked":''); ?>"></span>
                     <span class="fa fa-star <?php echo esc_html($value_rating>2?"checked":''); ?>"></span>
                     <span class="fa fa-star <?php echo esc_html($value_rating>3?"checked":''); ?>"></span>
                     <span class="fa fa-star <?php echo esc_html($value_rating>4?"checked":''); ?>"></span>
                  <?php  echo esc_html($value_rating); ?> 
                  </div>
               </div>
              <span class="clearfix"> </span>
              <h3><a href="<?php echo esc_url(get_the_permalink()); ?>"><?php echo esc_html(get_the_title()); ?></a></h3> 
              <?php if($feature_style!="yes"): ?>
                  <div class="p-price"><?php echo esc_html("$".number_format((float)$value_cost, 2, '.', '')); ?> </div>
              <?php endif; ?>
              <div class="p-footer"> 
                 <div class="d-inline p-2 "><i class="fa fa-<?php echo esc_attr($product_icon_class); ?> wordpress-color" aria-hidden="true"></i> <?php echo esc_html($item_type); ?></div>
                  <div class="d-inline p-2 "> <i class="fa fa-cloud-download" id="download-color"  aria-hidden="true"></i> <?php echo ewShortNumber($value_sales); ?></div>
                  <div class="d-inline p-2 "><i class="fa fa-tags tags-color" aria-hidden="true"></i> <?php $tags = explode(',',$value_tags); echo esc_html(isset($tags[0])?$tags[0]:''); ?></div>
               </div> 
                     
            </div>
        
         </div>
         <?php wp_reset_query();  endwhile; ?>
      </div>
      
               <nav class="p-navigation">
                  <ul>
                 
                     <?php 
                        $ew_prev = esc_html__("PREV","envato-world");
                        $ew_next = esc_html__("NEXT","envato-world");
                      ?>
                     <li> 
                     <?php previous_posts_link( '&laquo; '.$ew_prev, $latest_items->max_num_pages) ?>
                     </li> 
                     <li><?php next_posts_link( $ew_next.' &raquo;', $latest_items->max_num_pages) ?></li>
                  </ul>
               </nav>
           
         <?php endif; ?>      
      </div>
     <?php 
   }

   public function filterCategory($items = null ){
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
   public function ewCategory($site) {
      if($site==''){
        return [];
      }
      $category = [];
      $category_list = [];
      $url = "https://api.envato.com/v1/market/categories:".$site.".json";
  
      $args = array(
       'headers' => array(
         'Authorization' => 'Bearer UtYTHyPDvL7Rn753jdqaf4pyZqmoMqOq',
         "Content-type" => "application/json" 
         )
       );

      $data=wp_remote_get( $url, $args );
      $response_code =  wp_remote_retrieve_response_code($data);
   
      if($response_code==200){
      $body = wp_remote_retrieve_body( $data );
      $content_body= \json_decode($body,true);
     
      if(isset($content_body["categories"]) ) {
         $category = $content_body["categories"]; 
        
         foreach($category as $value){
          
            if(is_array($value)){
               $category_list[ $value["path"] ] = $value["name"]; 
            } 
          
         } 
        
         return $category_list;
      }
        
    
      return $category;
 
   }
   
}

}
