<?php 

require_once 'wonderplugin-audio-functions.php';

class WonderPlugin_Audio_Model {

	private $controller;
	
	function __construct($controller) {
		
		$this->controller = $controller;
	}
	
	function get_upload_path() {
		
		$uploads = wp_upload_dir();
		return $uploads['basedir'] . '/wonderplugin-audio/';
	}
	
	function get_upload_url() {
	
		$uploads = wp_upload_dir();
		return $uploads['baseurl'] . '/wonderplugin-audio/';
	}
	
	function generate_body_code($id, $has_wrapper) {
		
		global $wpdb;
		$table_name = $wpdb->prefix . "wonderplugin_audio";
		
		$ret = "";
		$item_row = $wpdb->get_row( $wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $id) );
		if ($item_row != null)
		{
			$data = str_replace('\\\"', '"', $item_row->data);
			$data = str_replace("\\\'", "'", $data);
			
			$data = json_decode($data);
			
			if (isset($data->customcss) && strlen($data->customcss) > 0)
				$ret .= '<style type="text/css">' . $data->customcss . '</style>';
			
			if (isset($data->skincss) && strlen($data->skincss) > 0)
				$ret .= '<style type="text/css">' . str_replace('#amazingaudioplayer-AUDIOPLAYERID',  '#wonderpluginaudio-' . $id, $data->skincss) . '</style>';
			
			// div data tag
			$ret .= '<div class="wonderpluginaudio" id="wonderpluginaudio-' . $id . '" data-audioplayerid="' . $id . '" data-width="' . $data->width . '" data-height="' . $data->height . '" data-skin="' . $data->skin . '"';
			
			if (isset($data->dataoptions) && strlen($data->dataoptions) > 0)
			{
				$ret .= ' ' . stripslashes($data->dataoptions);
			}
			
			$boolOptions = array('autoplay', 'random', 'responsive', 'showtracklist', 'showprogress', 'showprevnext', 'showloop', 'showloading', 'titleinbarscroll');
			foreach ( $boolOptions as $key )
			{
				if (isset($data->{$key}) )
					$ret .= ' data-' . $key . '="' . ((strtolower($data->{$key}) === 'true') ? 'true': 'false') .'"';
			}
			
			$valOptions = array('loop', 'tracklistitem', 'titleinbarwidth');
			foreach ( $valOptions as $key )
			{
				if (isset($data->{$key}) )
					$ret .= ' data-' . $key . '="' . $data->{$key} . '"';
			}
				
			$ret .= ' data-jsfolder="' . WONDERPLUGIN_AUDIO_URL . 'engine/"'; 
			
			$ret .= ' style="display:block;position:relative;margin:0 auto;';
			
			if ( isset($data->responsive) && strtolower($data->responsive) === 'true' )
				$ret .= 'width:100%;';
			else
				$ret .= 'width:' . $data->width . 'px;';
			
			if ($data->heightmode == 'auto')
				$ret .= 'height:auto;';
			else
				$ret .= 'height:' . $data->height . 'px;';
			$ret .= '"';
			
			$ret .= '>';
			
			if (isset($data->slides) && count($data->slides) > 0)
			{
				$ret .= '<ul class="amazingaudioplayer-audios" style="display:none;">';
				
				foreach ($data->slides as $slide)
				{		
					
					$ret .= '<li';
					$ret .= ' data-artist="' . str_replace("\"", "&quot;", $slide->artist) . '"';
					$ret .= ' data-title="' . str_replace("\"", "&quot;", $slide->title) . '"';
					$ret .= ' data-album="' . str_replace("\"", "&quot;", $slide->album) . '"';
					$ret .= ' data-info="' . str_replace("\"", "&quot;", $slide->info) . '"';
					$ret .= ' data-image="' . $slide->image . '"';
					
					if ( isset($slide->live) && strtolower($slide->live) === 'true' )
						$ret .= ' data-live="true"';
					else
						$ret .= ' data-duration="' . $slide->duration . '"';
					$ret .= '>';
					
					if ($slide->mp3 && strlen($slide->mp3) > 0)
						$ret .= '<div class="amazingaudioplayer-source" data-src="' . $slide->mp3 . '" data-type="audio/mpeg" />';
					if ($slide->ogg && strlen($slide->ogg) > 0)
						$ret .= '<div class="amazingaudioplayer-source" data-src="' . $slide->ogg . '" data-type="audio/ogg" />';
				
					$ret .= '</li>';
					
				}
				$ret .= '</ul>';
				
			}
			if ('F' == 'F')
				$ret .= '<div class="wonderplugin-engine"><a href="http://www.wonderplugin.com/wordpress-audio-player/" title="'. get_option('wonderplugin-audio-engine')  .'">' . get_option('wonderplugin-audio-engine') . '</a></div>';
			$ret .= '</div>';
			
		}
		else
		{
			$ret = '<p>The specified audio id does not exist.</p>';
		}
		return $ret;
	}
	
	function delete_item($id) {
		
		global $wpdb;
		$table_name = $wpdb->prefix . "wonderplugin_audio";
		
		$ret = $wpdb->query( $wpdb->prepare(
				"
				DELETE FROM $table_name WHERE id=%s
				",
				$id
		) );
		
		return $ret;
	}
	
	function clone_item($id) {
	
		global $wpdb, $user_ID;
		$table_name = $wpdb->prefix . "wonderplugin_audio";
		
		$cloned_id = -1;
		
		$item_row = $wpdb->get_row( $wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $id) );
		if ($item_row != null)
		{
			$time = current_time('mysql');
			$authorid = $user_ID;
			
			$ret = $wpdb->query( $wpdb->prepare(
					"
					INSERT INTO $table_name (name, data, time, authorid)
					VALUES (%s, %s, %s, %s)
					",
					$item_row->name,
					$item_row->data,
					$time,
					$authorid
			) );
				
			if ($ret)
				$cloned_id = $wpdb->insert_id;
		}
	
		return $cloned_id;
	}
	
	function is_db_table_exists() {
	
		global $wpdb;
		$table_name = $wpdb->prefix . "wonderplugin_audio";
	
		return ( $wpdb->get_var("SHOW TABLES LIKE '$table_name'") == $table_name );
	}
	
	function is_id_exist($id)
	{
		global $wpdb;
		$table_name = $wpdb->prefix . "wonderplugin_audio";

		$audio_row = $wpdb->get_row( $wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $id) );
		return ($audio_row != null);
	}
	
	function create_db_table() {
	
		global $wpdb;
		$table_name = $wpdb->prefix . "wonderplugin_audio";
		
		$charset = '';
		if ( !empty($wpdb -> charset) )
			$charset = "DEFAULT CHARACTER SET $wpdb->charset";
		if ( !empty($wpdb -> collate) )
			$charset .= " COLLATE $wpdb->collate";
	
		$sql = "CREATE TABLE $table_name (
		id INT(11) NOT NULL AUTO_INCREMENT,
		name tinytext DEFAULT '' NOT NULL,
		data MEDIUMTEXT DEFAULT '' NOT NULL,
		time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
		authorid tinytext NOT NULL,
		PRIMARY KEY  (id)
		) $charset;";
			
		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		dbDelta($sql);
	}
	
	function save_item($item) {
		
		if ( !$this->is_db_table_exists() )
			$this->create_db_table();
				
		global $wpdb, $user_ID;
		$table_name = $wpdb->prefix . "wonderplugin_audio";
		
		$id = $item["id"];
		$name = $item["name"];
		
		unset($item["id"]);
		$data = json_encode($item);
		
		$time = current_time('mysql');
		$authorid = $user_ID;
		
		if ( ($id > 0) && $this->is_id_exist($id) )
		{
			$ret = $wpdb->query( $wpdb->prepare(
					"
					UPDATE $table_name
					SET name=%s, data=%s, time=%s, authorid=%s
					WHERE id=%d
					",
					$name,
					$data,
					$time,
					$authorid,
					$id
			) );
			
			if (!$ret)
			{
				return array(
						"success" => false,
						"id" => $id, 
						"message" => "Cannot update the audio in database"
					);
			}
		}
		else
		{
			$ret = $wpdb->query( $wpdb->prepare(
					"
					INSERT INTO $table_name (name, data, time, authorid)
					VALUES (%s, %s, %s, %s)
					",
					$name,
					$data,
					$time,
					$authorid
			) );
			
			if (!$ret)
			{
				return array(
						"success" => false,
						"id" => -1,
						"message" => "Cannot insert the audio to database"
				);
			}
			
			$id = $wpdb->insert_id;
		}
		
		return array(
				"success" => true,
				"id" => intval($id),
				"message" => "Audio published!"
		);
	}
	
	function get_list_data() {
		
		global $wpdb;
		$table_name = $wpdb->prefix . "wonderplugin_audio";
		
		$rows = $wpdb->get_results( "SELECT * FROM $table_name", ARRAY_A);
		
		$ret = array();
		
		if ( $rows )
		{
			foreach ( $rows as $row )
			{
				$ret[] = array(
							"id" => $row['id'],
							'name' => $row['name'],
							'data' => $row['data'],
							'time' => $row['time'],
							'author' => $row['authorid']
						);
			}
		}
	
		return $ret;
	}
	
	function get_item_data($id)
	{
		global $wpdb;
		$table_name = $wpdb->prefix . "wonderplugin_audio";
	
		$ret = "";
		$item_row = $wpdb->get_row( $wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $id) );
		if ($item_row != null)
		{
			$ret = $item_row->data;
		}

		return $ret;
	}
}
