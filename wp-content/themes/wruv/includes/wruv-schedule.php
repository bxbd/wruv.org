<?php

function get_schedslot_meta($id, $key, $single = true) {
	return get_post_meta( $id, "_wruv_sched_$col", $single );
}

add_shortcode( 'weekly-schedule', function($atts) {
	$sched_query = new WP_Query([
		'post_type' => 'schedule_slot',
		'posts_per_page' => 10,
		'page' => 1,
		'paged' => 1,

		'meta_query' => array(
			array(
				'dj_show_title' => 'color',
			),
		),

		'meta_key' => 'timeslot_start',
		'orderby' => 'meta_value',
	]);
	// var_export($sched_query);
	// wp_die();

	if($sched_query->have_posts()) {
		while ($sched_query->have_posts()) {
			$sched_query->the_post();

			$show_title = get_schedslot_meta( get_the_ID(), 'show_name');
			$show_dj = get_schedslot_meta( get_the_ID(), 'show_dj_name');
			$show_with = true;
			if( empty($show_title) ) {
				$show_title = $show_dj;
				$show_dj = '';
			}
			$show_genre = get_schedslot_meta( get_the_ID(), 'show_genre');

			// the_post();
			// $image_id     = get_post_thumbnail_id($post->ID);
			// $cover   	  = wp_get_attachment_image_src($image_id, 'blog-home');
			// $cover_large  = wp_get_attachment_image_src($image_id, 'photo-large');
			// $num_comments = get_comments_number();
			?>
			<div class="bl2page-col">
				<h2 class="bl2page-title sched-title"><?= $show_title ?></h2>
				<div class="bl2page-text">
					<?php if( !empty($shape_dj) ) { ?>
						<p class="sched-dj"><small class="sched-with">with</small> <?= $show_dj ?></p>
					<?php } ?>
					<?php if( !empty($show_genre) ) { ?>
						<p class="sched-genre"><i><?= $show_genre ?></i></p>
					<?php } ?>
				</div>
			</div>
			<?php
		}
	}
});

add_action ('init', 'register_schedule_slot_post_type');
function register_schedule_slot_post_type() {

	$labels = array(
		'name'                  => _x( 'Schedule Slots', 'Post Type General Name', 'text_domain' ),
		'singular_name'         => _x( 'Schedule Slot', 'Post Type Singular Name', 'text_domain' ),
		'menu_name'             => __( 'Schedule', 'text_domain' ),
		'name_admin_bar'        => __( 'Schedule', 'text_domain' ),
		'archives'              => __( 'Schedule Archives', 'text_domain' ),
		'parent_item_colon'     => __( 'Parent Item:', 'text_domain' ),
		'all_items'             => __( 'All Schedule Slots', 'text_domain' ),
		'add_new_item'          => __( 'Add New Slot', 'text_domain' ),
		'add_new'               => __( 'Add New', 'text_domain' ),
		'new_item'              => __( 'New Slot', 'text_domain' ),
		'edit_item'             => __( 'Edit Slot', 'text_domain' ),
		'update_item'           => __( 'Update Slot', 'text_domain' ),
		'view_item'             => __( 'View Slot', 'text_domain' ),
		'search_items'          => __( 'Search Schedule', 'text_domain' ),
		'not_found'             => __( 'Not found', 'text_domain' ),
		'not_found_in_trash'    => __( 'Not found in Trash', 'text_domain' ),
		'featured_image'        => __( 'Featured Image', 'text_domain' ),
		'set_featured_image'    => __( 'Set featured image', 'text_domain' ),
		'remove_featured_image' => __( 'Remove featured image', 'text_domain' ),
		'use_featured_image'    => __( 'Use as featured image', 'text_domain' ),
		'insert_into_item'      => __( 'Insert into slot', 'text_domain' ),
		'uploaded_to_this_item' => __( 'Uploaded to this slot', 'text_domain' ),
		'items_list'            => __( 'Schedule slot list', 'text_domain' ),
		'items_list_navigation' => __( 'Schedule slot list navigation', 'text_domain' ),
		'filter_items_list'     => __( 'Filter slots list', 'text_domain' ),
	);
	$args = array(
		'label'                 => __( 'Schedule Slot', 'text_domain' ),
		'description'           => __( 'A single slot on the schedule', 'text_domain' ),
		'labels'                => $labels,
		'supports'              => array( 'title', 'thumbnail', 'custom-fields', ),
		'taxonomies'            => array( 'category_schedule_slots' ),
		'hierarchical'          => false,
		'public'                => true,
		'show_ui'               => true,
		'show_in_menu'          => true,
		'menu_icon'				=> 'dashicons-calendar-alt',
		'menu_position'         => 5,
		'show_in_admin_bar'     => true,
		'show_in_nav_menus'     => true,
		'can_export'            => true,
		'has_archive'           => false,
		'exclude_from_search'   => true,
		'publicly_queryable'    => true,
		'rewrite'               => false,
		'capability_type'       => 'page',
		'capabilities' => array(
			'create_posts' => false, // Removes support for the "Add New" function ( use 'do_not_allow' instead of false for multisite set ups )
		),
		'map_meta_cap' => true, // Set to `false`, if users are not allowed to edit/delete existing posts
	);
	register_post_type( 'schedule_slot', $args );
}

$tbd = register_tbd_import('wruv_schedule_noon-to-three', [
	'post_type' => 'schedule_slot',

	'required_cols' => explode(',',
		'slot_number,year,dayslot,timeslot_start,timeslot_end,slot_end,graveyard,timeslot_name,'
		. 'show_dj_name,show_name,genre,dj_actual_name,dj_email,dj_phone'
	),
	//
	// 'init' => function() {
	// 	//(by default it will check for req'd columns, by creating another init fcn)
	// 	//ensure all rows exist
	//
	//
	// },

	// 'rowmap' => [
	// 	'show_name' => 'post_name',
	// 	'*' => function($col) { return "postmeta/_wruv_sched_$col"; },
	// ],
	//the above would beget the below
	'row' => function($row) {
		$result = [];
		$result['post_name'] = $row['show_name'];
		$meta = [];
		foreach( $row as $col => $val ) {
			$meta["_wruv_sched_$col"] = $val;
		}
		if( count($meta) > 0 ) $result['post_meta'] = $meta;
		return $result;
	},

	'sample' => 'wruv_schedule_sample',
]);

function sample_value($col) {
	include_once( __DIR__ . '/hipster-ipsum.php');
	return random_hipster_word();
}
function wruv_schedule_sample($tbd, $opts = []) {
	$slot_length = function($hr) {
		if( in_array($hr, [ 6, 9, 12, 15 ]) ) {
			return 3;
		}
		return 2;
	};
	$slot_end = function($hr) use ($slot_length) {
		return $hr + $slot_length($hr);
	};
	$timeslot_name = function($day, $hr) use ($opts, $slot_end) {
		$dayname = date('D', strtotime("Sunday +{$day} days"));

		$_hr = $hr % 24;
		$hr_ampm = date("ga", strtotime("$_hr:00"));
		$hr_ampm-$hr_end_ampm = date("ga", strtotime($slot_end($_hr) . ":00"));

		$result = "$dayname $hr_ampm-$hr_end_ampm";
		$result = str_replace('12am', 'midnight', $result);
		$result = str_replace('12pm', 'noon', $result);

		if( $hr > 24 ) {
			$result .= ' GRAVEYARD';
		}
		return $result;
	};

	if( isset($opts['year']) ) {
		$year = $opts['year'];
	}
	else {
		$trimester = date('n');
		$year = date('Y') . ( $trimester < 6 ? '01' : $trimester < 9 ? '02' : '03' );
	}

	$slot_i = 0;
	$headers = $tbd->col_names();
	$sample = [ $headers ];
	$inc_gy = isset($opts['include_graveyard']) && $opts['include_graveyard'];
	for( $dayslot = 1; $dayslot <= 6; $dayslot++ ) {
		for( $hr = 6; $hr < 30; $hr += $slot_length($hr) ) {
			$slot_i++;
			if( !$inc_gy && $hr > 24 ) continue;

			$row = [];
			foreach( $headers as $col ) {

				if( $col == 'slot_number' ) {
					$row[] = $slot_i;
				}
				elseif( $col == 'year' ) {
					$row[] = $year;
				}
				elseif( $col == 'dayslot' ) {
					$row[] = $dayslot;
				}
				elseif( $col == 'timeslot_start' ) {
					$row[] = $hr;
				}
				elseif( $col == 'timeslot_end' ) {
					$row[] = $hr + $slot_length($hr);
				}
				elseif( $col == 'timeslot_name' ) {
					$row[] = $timeslot_name($dayslot, $hr);
				}
				elseif( $col == 'slot_end' ) {
					$row[] = $slot_end($hr) + ( $dayslot * 24 );
				}
				elseif( $col == 'graveyard' ) {
					$row[] = $hr > 24 ? 1 : 0;
				}
				elseif( $col == 'dj_phone' ) {
					$row[] = '802-000-0000';
				}
				elseif( $col == 'dj_email' ) {
					$row[] = preg_replace('/[^a-z0-9]/', '', strtolower(sample_value($col))) . '@wruv.org';
				}
				else {
					$row[] = sample_value($col);
				}
			}
			$sample[] = $row;
		}
	}
	return $sample;
};
