<?php

date_default_timezone_set('America/New_York'); //this is totally fucked


function wruv_current_streamtitle() {
	$onair = wruv_current_sched_slot();
	return wruv_streamtitle_str( $onair );

}
function wruv_streamtitle($ts) { //if you want it for "now" use current, above
	$onair = wruv_sched_slot($ts);
	return wruv_streamtitle_str( $onair );

}
function wruv_streamtitle_str($onair) {
	if( !empty($onair['show_name']) && !empty($onair['show_dj_name']) ) {
 		?><?= $onair['show_name'] ?> with <?= $onair['show_dj_name'] ?><?php
	}
	elseif( empty($onair['show_name']) && !empty($onair['show_dj_name']) ) {
 		?><?= !empty($onair['genre']) ? $onair['genre'] . ' with ' : '' ?><?= $onair['show_dj_name'] ?><?php
	}
	elseif( !empty($onair['show_name']) && empty($onair['show_dj_name']) ) {
		?><?= $onair['show_name'] ?><?= !empty($onair['genre']) ? ' featuring ' . $onair['genre'] : '' ?><?php
	}
	elseif( !empty($onair['genre']) ) {
		?><?= $onair['genre'] ?><?php
	}
	//  if song
	//  ( song.time | strftime("%l") | trim  song.time | strftime(':%m') | trim
	//   ' ' _ song.artist if song.artist  ', ' if song.artist && song.track  '"' _ song.track _ '"' if song.track )

 	?>, till <?= word_time($onair['timeslot_end']) ?><?php
}

function wruv_current_sched_slot() {
	global $current_schedule_slot;
	if( empty($current_schedule_slot) ) {
		date_default_timezone_set('America/New_York'); //this is totally fucked
		// echo date('w') . ' ' . date('g');
		$current_schedule_slot = wruv_sched_slot(date('U'));
	}
	return $current_schedule_slot;
}

function wruv_hour($date = null) {
	return (date('w', $date) * 24) + date('G', $date);
}

function wruv_sched_slot($date) {
	$query_hr = wruv_hour($date);

	global $wpdb;

/*
	SELECT wp_posts.*, mt_slot_end.meta_value AS slot_end
	FROM wp_posts
		INNER JOIN wp_postmeta AS mt_slot_end ON ( wp_posts.ID = mt_slot_end.post_id  AND mt_slot_end.meta_key = 'wruv_sched_slot_end' )
*/


	$metakeys = ['slot_end', 'show_name', 'show_dj_name', 'genre', 'dayslot', 'timeslot_start', 'timeslot_end', 'year' ];
	$joins = [];
	$fields = [];
	foreach( $metakeys as $k ) {
		$_k = "wruv_sched_$k";
		array_push($joins, "INNER JOIN wp_postmeta AS mt_$k ON ( wp_posts.ID = mt_$k.post_id  AND mt_$k.meta_key = '$_k' )");
		array_push($fields, "mt_$k.meta_value AS $k");
	}

	$sched_slot_sql = "
		SELECT wp_posts.post_title, " . implode(", ", $fields) . "
		FROM wp_posts
			" . implode("\n", $joins) . "
		WHERE
			mt_slot_end.meta_value > $query_hr
			AND mt_year.meta_value = " . WRUV_SCHED_YEAR . "
		ORDER BY slot_end+0
		LIMIT 1
	";

	// echo "<pre>" . $sched_slot_sql . "</pre>";
	$sched_slot = $wpdb->get_results($sched_slot_sql);
	$ret = (array)$sched_slot[0];
	$ret['show_time_str'] = sched_time_str($ret['dayslot'], $ret['timeslot_start'], $ret['timeslot_end']);

	return $ret;
}


function parse_files_this_week() { //this code was translated from perl, which is why it seems odd
	$file_txt = get_transient('this_week_html');

	if( !$file_txt ) {
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, "http://www.uvm.edu/~wruv/res/thisweek/");
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$file_txt = curl_exec($ch);
		$remaining_m = 15 -(date('i', time()) % 15);
		set_transient('this_week_html', $file_txt, $remaining_m * 60);
	}
	return $file_txt;
	foreach( explode("\n", $file_txt) as $line ) {
		if( preg_match("/\<a href=\"([^\"]+)\"/", $line, $m ) ) {
			$fn = $m[1];
			if( preg_match("/^\./", $fn) || !preg_match("/^[0-9\-]{10}/", $fn) ) continue;

			$finfo = preg_split("/\s{2,}/", $line);
			$modified_str = $finfo[0];
			$size = $finfo[1];

			// var_export([$fn, $line, $modified_str, $size]); exit;
		}
	}
/*

   my %files;
   my $now = time;

   foreach( split(/\n/, $file_txt) ) {
      if( /<a href="([^"]+)"/ ) { #"
         my $fn = $1;
         next if $fn =~ /^\./ || $fn !~ /^[0-9\-]{10}/;

         my (undef, $modified_str, $size) = split( /\s{2,}/, $_ );

         my $modified = Date::Parse::str2time( $modified_str );
         my $now_uploading = $now - ($modified || 0) <= 1;  #if it's been modified in the last 1 seconds, this is still being uploaded.

         my ($date, $rest) = split /00h_/, $fn;

         my ($y, $m, $d) = split /-/, $date;
         my $h;
         ($d, $h) = split /_/, $d;

         my $jd = julian_day($y, $m, $d);
         my $ts = jd_secondslocal($jd, $h, 0, 0);
         my $wkday_i = day_of_week( $jd );
         if( $h >= 0 && $h <= 4 ) {
           $wkday_i = ($wkday_i - 1) % 7;
         }

         my $wkday = qw(Sun Mon Tue Wed Thu Fri Sat)[day_of_week( $jd )];

         my $file_info = {
            now_uploading => $now_uploading,
            file => $fn,
            ts => $ts,
            'y'=>$y,
            'm'=>$m,
            'd'=>$d,
            'h'=>$h,
            'wkday' => $wkday,
            'wkday_i' => $wkday_i,
            # title => $slot_rec->{show_name},
            # genre => $slot_rec->{genre},
            # dj => $slot_rec->{show_dj_name},
            ##  title => $title,
            ##  genre => $genre,
            ##  dj => $dj,
            ##  dj_id => $slot_rec->{dj_id},
         };

         my $fkey = "$wkday_i-$h";

         #discard it unless we already found one for this slot and it's the one from this week
         next if exists $files{$fkey} && $files{$fkey}{ts} > $ts;
         $files{$fkey} = $file_info
      }
   }

   return [ sort { $a->{ts} <=> $b->{ts} } values %files ];
}
*/
}


function get_schedslot_meta($id, $key, $single = true) {
	return get_post_meta( $id, "wruv_sched_$key", $single );
}

function dow($d) { //return 3 letter day of week
	return date('D', strtotime("Sunday +{$d} days"));
}

function ampm($h) {
	$h %= 24;
	return date("ga", strtotime("$h:00"));
}

function word_time($t) {
	if( $t == 12 ) return 'noon';

	$t12 = $t % 12;
		if($t12 == 0 ){ $ret =  'midnight'; }
    elseif($t12 == 1) { $ret =  'one'; }
    elseif($t12 == 2) { $ret =  'two'; }
    elseif($t12 == 3) { $ret =  'three'; }
    elseif($t12 == 4) { $ret =  'four'; }
    elseif($t12 == 5) { $ret =  'five'; }
    elseif($t12 == 6) { $ret =  'six'; }
    elseif($t12 == 7) { $ret =  'seven'; }
    elseif($t12 == 8) { $ret =  'eight'; }
    elseif($t12 == 9) { $ret =  'nine'; }
    elseif($t12 == 10) { $ret = 'ten'; }
    elseif($t12 == 11) { $ret = 'eleven'; }

    return $ret;
}

function sched_time_str($d, $hs, $he) {
	$dayname = dow($d);

	$result = $dayname . ' ' . ampm($hs) . '-' . ampm($he);
	$result = str_replace('12am', 'midnight', $result);
	$result = str_replace('12pm', 'noon', $result);

	return $result;
}

add_shortcode( 'weekly-schedule', function($atts) {

	date_default_timezone_set('America/New_York'); //so very totally fucked

	$today_day = date('w');

	//keep in mind that according to wruv scheduling,
	// technically the hours from midnight to 6am occur on the same 'day'
	// as the rest of the shows from 6am to midnight
	// $current_hr = ($today_day * 24) + $_GET['h'];
	$current_hr = ($today_day * 24) + date('G');


	$dayslot = isset($_GET['d']) ? $_GET['d'] : $today_day;

	?>

	<div class="bl2page-col sched-header" style="display: block">
		<table width="100%">
			<tr>
				<?php for( $d = 0; $d < 7; $d++ ) { ?>
					<th align="center">
						<?php if( $d != $dayslot ) { ?><a href="?d=<?= $d ?>"><?php } ?>
						<?= date('D', strtotime("Sunday +{$d} days")); ?>
						<?php if( $d != $dayslot ) { ?></a><?php } ?>
					</th>
				<?php } ?>
				<th><a href="?archives=1" class="invert">LISTEN TO ARCHIVES</a></th>
			</tr>
		</table>
	</div>

	<?php


		if( isset($_GET['archives']) ) {
			$show_files = parse_files_this_week();
			echo "<div class='so-sorry'>Our archives will be moved into the schedule soon, please bear with us</div>";
			echo preg_replace('/href="/', 'href="http://www.uvm.edu/~wruv/res/thisweek/', $show_files);
			return;
		}

		$sched_query = new WP_Query([
			'post_type' => 'schedule_slot',
			'posts_per_page' => -1,

			'meta_query' => array(
				array(
					'key' => 'wruv_sched_dayslot',
					'value' => $dayslot,
					'compare' => '='
				),
				array(
					'key' => 'wruv_sched_year',
					'value' => WRUV_SCHED_YEAR,
					'compare' => '='
				)
			),
			'meta_value_num' => true,
			'meta_key' => 'wruv_sched_slot_end',
			'orderby' => 'meta_value_num',
			'order' => 'ASC',
		]);
		// echo $sched_query->request;
		// wp_die();


	$gy_output = '';

	// $show_files = parse_files_this_week();

	if($sched_query->have_posts()) {
		$current_slot = wruv_current_sched_slot();

		while ($sched_query->have_posts()) {
			$sched_query->the_post();

			$show_slotend = get_schedslot_meta( get_the_ID(), 'slot_end');
			$show_start = get_schedslot_meta( get_the_ID(), 'timeslot_start');
			$show_end = get_schedslot_meta( get_the_ID(), 'timeslot_end');
			$show_time_str = sched_time_str( $dayslot, $show_start, $show_end );

			$show_title = get_schedslot_meta( get_the_ID(), 'show_name');
			$show_dj = get_schedslot_meta( get_the_ID(), 'show_dj_name');
			$show_with = true;
			if( empty($show_title) ) {
				$show_title = $show_dj;
				$show_dj = '';
			}
			$show_genre = get_schedslot_meta( get_the_ID(), 'genre');


			$is_current_slot = $current_slot['slot_end'] == $show_slotend;

			// foreach( $show_files as $file ) {
			// 	if( $file['wkday_i'] == $dayslot
			// 		&& $file['h'] == ($show_start % 24 )
			// 	) {
			// 		$show_file = $file;
			// 		break;
			// 	}
			// }

			//kind of hate how this works
			$show_isgy = get_schedslot_meta( get_the_ID(), 'graveyard');
			if( $show_isgy ) {
				ob_start();
			}
		?>
			<div class="bl2page-col sched-item<?= $is_current_slot ? " now" : '' ?>">
				<?php //= "$dayslot == $today_day && $show_start <= $current_hr && $show_end > $current_hr" ?>
				<div class="sched-time"><?= $show_time_str ?></div>
				<h2 class="bl2page-title sched-title"><?= $show_title ?></h2>
				<?php if( !empty($show_dj) ) { ?>
					<span class="sched-dj"><small class="sched-with">with</small> <span class="sched-dj-name"><?= $show_dj ?></span></span>
				<?php } ?>
				<?php if( !empty($show_genre) ) { ?>
					<div class="bl2page-text">
						<p class="sched-genre"><i><?= $show_genre ?></i></p>
					</div>
				<?php } ?>
				<?= $show_file ?>
			</div>
			<?php

			if( $show_isgy ) {
				$gy_output .= ob_get_clean();
			}
		}
	}

	echo $gy_output;
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
	//
	// add_filter( 'manage_posts_columns' , function($columns) {
	// 	return array_merge(
	// 		$columns,
	//         array(
	// 			'publisher' => __('Publisher'),
	//             'book_author' =>__( 'Book Author')
	// 		)
	// 	);
	// }, 10, 2 );

	if( is_admin() ){
    	add_action('pre_get_posts', function($query) {
			if( $query->get('post_type') == 'schedule_slot' ) {
				if($query->get('orderby') == ''){
					$query->set('meta_key', 'wruv_sched_slot_end');
		            $query->set('orderby', 'meta_value_num');
					$query->set('order', 'ASC');
		        }

				$query->set('meta_query', [[
					'key' => 'wruv_sched_year',
					'value' => WRUV_SCHED_YEAR,
					'compare' => '='
				]]);
			}
		});
	}
	/* todo:
		add meta fields to quickedit
		remove "trash" links in all places (quicklink, post edit, bulk actions)
		custom sorting
		links on values
		search meta
		(multiple?) filters on meta (especially year and day)
		remove top nav links
		post status fields
		smaller/more rows per page?

	*/
	add_filter( 'manage_schedule_slot_posts_columns' , function($columns) {
		// echo '<pre>' . $GLOBALS['wp_query']->request; exit;
		$new_columns = array(
	        // 'cb' => '<input type="checkbox" />',
			'timeslot' => __('Slot', 'text_domain'),
			'wruv_sched_show_name' => __('Show', 'text_domain'),
			'wruv_sched_show_dj_name' => __('DJ', 'text_domain'),
			'wruv_sched_genre' => __('Genre', 'text_domain'),
			'wruv_sched_dj_actual_name' => __('Person', 'text_domain'),
			'wruv_sched_dj_email' => __('Email', 'text_domain'),
			// 'wruv_sched_year' => __('YEAR#', 'text_domain'),
	    );
		unset($columns['cb']);
		unset($columns['title']);
		unset($columns['author']);
		unset($columns['comments']);
		unset($columns['date']);

		return $new_columns + $columns;

	});
	add_action( 'manage_schedule_slot_posts_custom_column' , function($column, $post_id) {
		$v = get_post_meta( $post_id, $column, true);
		if( strrpos($column, 'wruv_sched_timeslot_') === 0 ) {
			echo '<pre>' . ampm($v) . '</pre>';
		}
		elseif( strrpos($column, 'wruv_sched_') === 0 ) {
			echo $v;
		}
		elseif( $column == 'timeslot' ) {
			$ts = get_post_meta( $post_id, 'wruv_sched_slot_end', true);
			$d = get_post_meta( $post_id, 'wruv_sched_dayslot', true);
			$start = get_post_meta( $post_id, 'wruv_sched_timeslot_start', true);
			$end = get_post_meta( $post_id, 'wruv_sched_timeslot_end', true);
			echo '<pre><b>' . dow($d) . "\n" . ampm($start) . '-' . ampm($end) . "<!--[@+$ts hrs]-->" . '</b></pre>';
		}
		// $new_columns = array(
	    //     'cb' => '<input type="checkbox" />',
		// 	'wruv_sched_dayslot' => __('DAY', 'text_domain'),
		// 	'wruv_sched_timeslot_start' => __('from', 'text_domain'),
		// 	'wruv_sched_timeslot_end' => __('to', 'text_domain'),
		//
		// 	'wruv_sched_show_name' => __('Show', 'text_domain'),
		// 	'wruv_sched_show_dj_name' => __('DJ', 'text_domain'),
		// 	'wruv_sched_genre' => __('Genre', 'text_domain'),
		// 	'wruv_sched_dj_actual_name' => __('Person', 'text_domain'),
		// 	'wruv_sched_dj_email' => __('Email', 'text_domain'),
		// 	'wruv_sched_year' => __('YEAR#', 'text_domain'),
	    // );
		// unset($columns['cb']);
		// unset($columns['title']);
		// unset($columns['author']);
		// unset($columns['comments']);
		// unset($columns['date']);
		//
		// return $new_columns + $columns;

	}, 10, 2 );

}




if( function_exists('register_tbd_import') ) {
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
	'row' => function($index, $row) {
		$result = [];
		$result['post_title'] = $row['show_name'] ?: $row['timeslot_name'];
		$meta = [];
		foreach( $row as $col => $val ) {
			$meta["wruv_sched_$col"] = $val;
		}
		if( count($meta) > 0 ) $result['post_meta'] = $meta;
		return $result;
	},

	'sample' => 'wruv_schedule_sample',
]);
}

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
		$hr_end_ampm = date("ga", strtotime($slot_end($_hr) . ":00"));

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
	for( $dayslot = 0; $dayslot <= 6; $dayslot++ ) {
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
