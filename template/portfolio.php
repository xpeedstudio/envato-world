<?php

/**
 * portfolio.php
 *
 * Template Name: EW Portfolio
 * Template Post Type: envato-portfolio
 */
get_header();
$reserved_data = [];
?>
<div class="ew-portfolio-post" id="post-<?php the_ID(); ?>" role="main">
   <div class="ew-portfolio-content">
      <?php while ( have_posts() ) : the_post(); ?>
      <?php
          $item_type = '';
          $post_id = $post->ID;

          $value_id = get_post_meta( $post_id, 'ew_envato_item_id',true);

          $value_url = get_post_meta( $post_id, 'ew_envato_item_url',true);
          $value_license_url = get_post_meta( $post_id, 'ew_envato_item_license_url',true);
          $value_user = get_post_meta( $post_id, 'ew_envato_item_user',true);
          $value_sales = get_post_meta( $post_id, 'ew_envato_item_sales',true);
          $value_rating = get_post_meta( $post_id, 'ew_envato_item_rating',true);
          $value_cost = get_post_meta( $post_id, 'ew_envato_item_cost',true);
          $value_uploaded_on = get_post_meta( $post_id, 'ew_envato_item_uploaded_on',true);
          $value_tags = get_post_meta( $post_id, 'ew_envato_item_tags',true);
          $value_category = get_post_meta( $post_id, 'ew_envato_item_category',true);

          $reserved_data = ewSingleitemdata($value_id);

          if(isset($reserved_data["classification"])){
             $classification = explode('/',$reserved_data["classification"]);
            if(in_array("wordpress",$classification) ){
               $item_type = esc_html__("wordpress","envato-world");
            }elseif(in_array("site-templates",$classification) ){
               $item_type =  esc_html__("html","envato-world");
            }elseif(in_array("joomla",$classification) ){
               $item_type = esc_html__("joomla","envato-world");
            }

          }


         ?>
      <div class="container">
         <div class="row">
            <div class="col-md-8">


               <div class="product-preview-img">
                  <div class="content-inner xs-padding">
                     <?php if(get_the_post_thumbnail_url($post_id)): ?>
                     <a href="<?php echo esc_url("https://".$reserved_data["site"].$reserved_data["previews"]["live_site"]["href"]); ?>"
                        target="_blank"><img class="img-responsive" src="<?php echo esc_url(get_the_post_thumbnail_url($post_id)); ?>"> </a>
                     <?php else: ?>
                     <a href="<?php echo esc_url("https://".$reserved_data["site"].$reserved_data["previews"]["live_site"]["href"]); ?>"
                        target="_blank"><img class="img-responsive"
                        src="<?php echo esc_url($reserved_data["previews"]["landscape_preview"]["landscape_url"]); ?>"
                        alt="<?php echo esc_html($reserved_data["name"]); ?>"> </a>
                     <?php endif; ?>

                     <ul class="product-preview-info">
                  <?php if(isset($reserved_data["price_cents"])): ?>
                  <li>
                     <span class="info-title"> <?php echo esc_html__("Price","envato-world"); ?></span>
                     <span class="amount">
                        <?php echo esc_html("$".number_format(($reserved_data["price_cents"]/100), 2, '.', ' ') ); ?>
                     </span>
                  </li>
                  <?php endif; ?>
                  <?php if(isset($reserved_data["number_of_sales"])): ?>
                  <li>
                     <span class="info-title"><?php echo esc_html__("Sales","envato-world"); ?></span>
                     <span> <?php echo esc_html($reserved_data["number_of_sales"]); ?> </span>
                  </li>
                  <?php endif; ?>
                  <?php if(isset($reserved_data["rating_count"])): ?>
                  <li>
                     <span class="info-title"><?php echo esc_html__("Loved","envato-world"); ?> </span>
                     <span> <?php echo esc_html($reserved_data["rating_count"]); ?> </span>
                  </li>
                  <?php endif; ?>
                  <?php if(isset($item_type)): ?>
                  <li>
                     <span class="info-title"><?php echo esc_html__("Type : ","envato-world"); ?></span>
                     <span><a href="#"> <?php echo esc_html($item_type); ?></a></span>
                  </li>
                  <?php endif; ?>
               </ul>
                  </div>
               </div>

               <div class="content-inner ew-description">
                  <?php if($post->post_content==''): ?>
                  <?php if(isset($reserved_data["description"])): ?>
                  <?php echo wp_kses_post(($reserved_data["description"])); ?>
                  <?php endif; ?>
                  <?php else: ?>
                  <?php echo wp_kses_post(($post->post_content)); ?>
                  <?php endif; ?>

               </div>
            </div>
            <div class="col-md-4">
               <div class="project-overview text-center">
                  <div class="content-inner">
                     <?php if(isset($reserved_data["url"])): ?>
                     <a href="<?php echo esc_url($reserved_data["url"]); ?>" class="xs_envato_btn_theme btn-buy-template">
                        Buy This <?php echo esc_html($item_type=="html"?"html Template":"wordpress theme"); ?></a>
                     <?php endif; ?>
                     <?php if($value_license_url!=''): ?>
                     <div class="license"><a class="read-more" href="#">Read about the license</a></div>
                     <?php endif; ?>
                  </div>
                  <div class="product-button">
                  <?php if(isset($reserved_data["previews"])): ?>
                  <?php $reserved_data_live_site = $reserved_data["previews"]; ?>
                  <?php if(isset($reserved_data_live_site["live_site"]) && ew_is_connected()): ?>
                  <a class="xs_envato_btn_theme"
                     href="<?php echo esc_url("https://".$reserved_data["site"].$reserved_data["previews"]["live_site"]["href"]); ?>"
                     target="_blank"><?php echo esc_html("Live Demo","envato-world"); ?> </a>
                  <?php endif; ?>
                  <?php endif; ?>
               </div>
               </div>
               <div class="xs_envato_product_info">
               <h3 class="block-title"><span><?php echo esc_html($item_type=="html"?" Template":"Theme"); ?>
                        Information</span></h3>
               </div>
               <div class="project-details">
                  <?php if(isset($reserved_data["published_at"])): ?>
                  <div class="dl-horizontal">
                     <strong>Created On:</strong>
                     <span> <?php echo esc_html(date('M d, Y', strtotime($reserved_data["published_at"]))); ?> </span>
                  </div>
                  <?php endif; ?>
                  <?php if(isset($reserved_data["updated_at"])): ?>
                  <div class="dl-horizontal">
                     <strong>Updated On:</strong>
                     <span> <?php echo esc_html(date('M d, Y', strtotime($reserved_data["updated_at"]))); ?> </span>
                  </div>
                  <?php endif; ?>

                  <?php if(isset($reserved_data["wordpress_theme_metadata"])): ?>
                  <div class="dl-horizontal">
                     <strong>Version:</strong>
                     <span><?php echo esc_html($reserved_data["wordpress_theme_metadata"]["version"]); ?> </span>
                  </div>
                  <?php endif; ?>
                  <?php if(isset($reserved_data["attributes"])): ?>
                  <?php foreach($reserved_data["attributes"] as $item_attr):
                     if($item_attr['name'] == 'demo-url'){
                        break;
                     }
                     ?>
                  <div class="dl-horizontal">
                     <strong> <?php echo str_replace('-', ' ', $item_attr["name"]) ?> </strong>
                     <span>
                        <?php
                              if(is_array($item_attr["value"])):
                              echo implode(' , ',$item_attr["value"]);
                              else:
                                 if(wp_http_validate_url($item_attr["value"])):
                                 echo "<a href=".esc_url($item_attr["value"]).">".esc_html__("Open","envato-world") ."</a>" ;
                                 else:
                                 echo esc_html($item_attr["value"]);
                                 endif;
                              endif;
                           ?>
                     </span>
                  </div>
                  <?php endforeach; ?>
                  <?php endif; ?>
               </div>
            </div>
         </div>
      </div>
   </div>
   <?php endwhile; ?>
</div> <!-- end main-content -->
</div> <!-- end main-content -->
<?php get_footer(); ?>