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
		elseif( isset($_POST['tbd-upload']) ) {
			$fn = $_FILES['tbd-upload']['tmp_name'];

			// var_export(
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
}

function register_tbd_import($slug, $args) {
	return new TBD_Importer($slug, $args);
}

}
