<?php
add_shortcode("artist", "artist_shortcode");
function artist_shortcode($atts, $content) {
    extract(shortcode_atts(array(
        "items" => 3,
        "cat" => null,
        "id" => null,
        "nav" => false,
        "order" => "desc",
        "orderby" => "ID",
        "videos" => null
    ), $atts));
    $order       = strtoupper($order);
    $items_count = 0;
    $items_src   = null;
    if ($id == null) {
        $query = array(
            'post_type' => 'artist',
            'orderby' => $orderby,
            'order' => $order,
            'posts_per_page' => $items,
			'cat' => $cat
        );
	if ($cat) {
     $query = array(
        'posts_per_page' => $items, 
        'orderby' => $orderby,
		'order' => $order,
        'post_type' => 'artist',
        'tax_query' => array(
            array(
                'taxonomy' => 'artists',
                'field' => 'slug',
                'terms' => array($cat)
            )));
    }
        $wp_query_artist = new WP_Query($query);
    }
    $items_src .= ' 
    <div class="home-shr clearfix">
	  <div class="phshr-col">
		 <div class="home-width">';
    while ($wp_query_artist->have_posts()):
        $wp_query_artist->the_post();
        global $post;
        $title          = get_the_title();
        $image_id       = get_post_thumbnail_id();
        $at_born        = get_post_meta($post->ID, 'at_born', true);
        $at_genres      = get_post_meta($post->ID, 'at_genres', true);
        $time           = strtotime($at_born);
        $pretty_date_yy = date('Y', $time);
        $pretty_date_M  = date('F', $time);
        $pretty_date_d  = date('d', $time);
        $cover          = wp_get_attachment_image_src($image_id, 'video-shortcode');
        $cover_large    = wp_get_attachment_image_src($image_id, 'photo-large');
        $no_cover       = get_template_directory_uri();
        $items_src .= '
            <div class="phshr-fix wz-last">
               <div class="phshr-cover">
                  <div class="wz-wrap wz-hover">';
        if ($image_id) {
            $items_src .= '
                     <img src="' . $cover[0] . '" alt="' . get_the_title() . '" />';
        } else {
            $items_src .= '
                     <img src="' . $no_cover . '/images/no-cover/media-shr.png" alt="no image" />';
        }
        $items_src .= '	
                     <div class="he-view">
                        <div class="bg a0" data-animate="fadeIn">
                           <a href="' . get_permalink() . '" class="atshr-link a2" data-animate="zoomIn"></a>
                           <a href="' . $cover_large[0] . '" class="phshr-zoom a2" data-animate="zoomIn" data-rel="prettyPhoto-cover"></a>
                        </div>
                     </div>			
                  </div>
               </div><!-- end .phshr-cover -->  
               <a href="' . get_permalink() . '">
                  <div class="phshr-info">	
                     <div class="phshr-title">' . $title . '</div>';
        if ($at_born) {
            $items_src .= '
                     <div class="phshr-des">' . $pretty_date_d . ' ' . $pretty_date_M . ' ' . $pretty_date_yy . '</div>';
        } else {
            $items_src .= '
                     <div class="phshr-des">' . $at_genres . ' </div>';
        }
        $items_src .= '
                  </div>
               </a>
            </div><!-- end .phshr-fix wz-last -->';
    endwhile;
    wp_reset_query();
    $items_src .= '    
		</div><!-- end .home-width -->
	  </div><!-- end .phshr-col -->
   </div><!-- end .home-shr clearfix -->';
    return $items_src;
}
?>