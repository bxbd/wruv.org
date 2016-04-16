<?php
/*
Plugin Name: TBD Importer
Plugin URI:
Description: Import posts, pages, comments, custom fields, categories, tags and more from ... anything you want!
Author: Brian Bittman
Author URI: http://burlingtonbytes.com/
Version: 0.0.0
Text Domain: tbd-importer
License: GPL version 2 or later - http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
*/

require __DIR__ . '/vendor/autoload.php';
use League\Csv\Reader;

if ( ! class_exists( 'TBD_Importer' ) ) {
class TBD_Importer {

	var $slug;
	var $post_type = null;
	var $col_specs = array();
	var $handlers = array(
		'init' => [],
		'row' => null,
		'sample' => null
	);

	public function __construct( $slug, $args ) {
		$this->slug = $slug;

		if( isset( $args['post_type'] ) ) {
			$this->post_type = $args['post_type'];
		}
		if( isset( $args['init'] ) ) {
			$this->handlers['init'] = $args['init'];
		}

		if( isset( $args['cols'] ) ) {
			foreach( $args['cols'] as $col )  {
				$this->col_specs[ $col ] = [
					'required' => false
				];
			}
		}
		if( isset( $args['required_cols'] ) ) {
			foreach( $args['required_cols'] as $col )  {
				$this->col_specs[ $col ] = [
					'required' => true
				];
			}
		}

		if( isset( $args['sample'] ) ) {
			$this->handlers['sample'] = $args['sample'];
		}

		if( isset( $args['rowmap'] ) ) {
			$this->handlers['row'] = function() {
				// foreach( $args['rowmap'] as $col => $wp_field )  {
				// 	$this->$col_specs[ $col ] = [
				// 		'required' => true
				// 	];
				// }
			};
		}
		elseif( isset( $args['row'] ) ) {
			$this->handlers['row'] = $args['row'];
		}

		add_action( 'admin_menu', array( $this, 'action_init' ) );
		// add_action( 'init', array( $this, 'action_init') );
	}

	public function col_names() {
		return array_keys($this->col_specs);
	}

	public function action_init() {
		add_submenu_page(
		// wp_die( var_export([
			'edit.php?post_type=' . $this->post_type,
			'Import ' . $this->post_type,
			'Import ' . $this->post_type,
			'manage_options',
			'tbd-importer_' . $this->slug,
			array( $this, 'action_menu_page' )
		);
		// ], true));
	}
	public function action_menu_page() {
		if( isset($_GET['view-sample']) ) {
			$sample_data = call_user_func( $this->handlers['sample'], $this );

			ob_clean();
			ob_start();
			foreach( $sample_data as $row ) {
				echo implode("\t", $row) . "\n";
			}
			$content = ob_get_clean();

			$fn = sprintf('"%s"', $this->slug . '-sample.tsv');
			$size = ob_get_length();
			header('Content-Description: File Transfer');

			header('Content-Type: application/octet-stream');
			header('Content-Disposition: attachment; filename=' . $fn);
			header('Content-Transfer-Encoding: binary');
			header('Connection: Keep-Alive');
			header('Expires: 0');
			header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
			header('Pragma: public');
			header('Content-Length: ' . $size);
			// header('Content-Type: text/plain');

			echo $content;

			exit;
		}
		elseif( isset($_FILES['tbd-upload']) ) {
			$fn = $_FILES['tbd-upload']['tmp_name'];

			$this->do_import($fn);

			echo "file imported";
			exit;
		}

		?>

		<a href="?<?php echo http_build_query(array_merge($_GET, array("view-sample" => "1"))); ?>">View Sample File</a>
		<hr />
		<form method="post" enctype="multipart/form-data">
			Upload file:
			<input type="file" name="tbd-upload">
			<input type="submit" value="OK">
		</form>
		<?php
	}

	public function do_import($fn) {
		header('Content-Type: text/plain');
		$csv = Reader::createFromPath($fn);
		$headers = $csv->fetchOne();

		$cbs = $this->handlers;
		if( isset($cbs['init']) ) {
			// $h['init']->()
		}

		$post_params = array_keys(array(//lifted right out of wp_insert_post, it's the defaults, we only want the keys
			'post_author' => $user_id,
			'post_content' => '',
			'post_content_filtered' => '',
			'post_title' => '',
			'post_excerpt' => '',
			'post_status' => 'draft',
			'post_type' => 'post',
			'comment_status' => '',
			'ping_status' => '',
			'post_password' => '',
			'to_ping' =>  '',
			'pinged' => '',
			'post_parent' => 0,
			'menu_order' => 0,
			'guid' => '',
			'import_id' => 0,
			'context' => '',
		));
		if( isset($cbs['row']) ) {
			$cb = $cbs['row'];
			$csv->setOffset(1)->each(function( $row, $index, $iterator ) use ($post_params, $cb, $headers) {
				$row_assoc = [];
				for( $i = 0; $i < count($headers); $i++ ) {
					$row_assoc[ $headers[$i] ] = $row[$i];
				}
				$post_spec = $cb($index, $row_assoc);

				if( !empty($post_spec) ) {
					$post_args = array(
						'post_type' => $this->post_type,
						'post_status' => 'publish',
					);
					foreach( $post_spec as $pa => $pa_val) {
						if( in_array($pa, $post_params) ) {
							$post_args[$pa] = $pa_val;
						}
					}

					$pid = wp_insert_post($post_args);
					if( isset($post_spec['post_meta']) ) {
						foreach( $post_spec['post_meta'] as $pm => $pm_val ) {
							var_export([$pid, $pm, $pm_val, true]);
							add_post_meta($pid, $pm, $pm_val, true);
						}
					}
				}

				return true;
				// return false;//stops the loop
			});
		}
	}
}

function register_tbd_import($slug, $args) {
	return new TBD_Importer($slug, $args);
}

}
