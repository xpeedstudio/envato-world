<?php 

function create_envato_world_portfolio() {
   register_post_type( 'envato-portfolio',
     array(
       'labels' => array(
         'name' => esc_html__( 'EnvatoPortfolio', 'envato-world' ),
         'singular_name' => esc_html__( 'Envatoportfolio' , 'envato-world' )
       ),
       'public' => true,
       'has_archive' => true,
       'supports'=>array(
         'title',
         'editor',
         'thumbnail',
         'excerpt'
      ),
     )
   );
 }
 add_action( 'init', 'create_envato_world_portfolio' );

 function load_portfolio_template($template) {
   global $post;

   if ($post->post_type == "envato-portfolio" && $template !== locate_template(array("portfolio.php"))){
     
       return EW_PATH . "/template/portfolio.php";
   }

   return $template;
}

add_filter('single_template', 'load_portfolio_template');