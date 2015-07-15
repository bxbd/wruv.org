<?php

class WonderPlugin_Audio_Creator {

	private $parent_view, $list_table;
	
	function __construct($parent) {
		
		$this->parent_view = $parent;
	}
	
	function render( $id, $config ) {
		
		?>
		
		<h3><?php _e( 'General Options', 'wonderplugin_audio' ); ?></h3>
		
		<div id="wonderplugin-audio-id" style="display:none;"><?php echo $id; ?></div>
		
		<?php 
		$config = str_replace('\\\"', '"', $config);
		$config = str_replace("\\\'", "'", $config);
		$config = str_replace("<", "&lt;", $config);
		$config = str_replace(">", "&gt;", $config);
		?>
		
		<div id="wonderplugin-audio-id-config" style="display:none;"><?php echo $config; ?></div>
		<div id="wonderplugin-audio-license" style="display:none;"><?php echo WONDERPLUGIN_AUDIO_VERSION_TYPE; ?></div>
		<div id="wonderplugin-audio-jsfolder" style="display:none;"><?php echo WONDERPLUGIN_AUDIO_URL . 'engine/'; ?></div>
		<div id="wonderplugin-audio-wp-history-media-uploader" style="display:none;"><?php echo ( function_exists("wp_enqueue_media") ? "0" : "1"); ?></div>
				
		<div style="margin:0 12px;">
		<table class="wonderplugin-form-table">
			<tr>
				<th><?php _e( 'Name', 'wonderplugin_audio' ); ?></th>
				<td><input name="wonderplugin-audio-name" type="text" id="wonderplugin-audio-name" value="My Audio Player" class="regular-text" /></td>
			</tr>
		</table>
		</div>
		
		<h3><?php _e( 'Designing', 'wonderplugin_audio' ); ?></h3>
		
		<div style="margin:0 12px;">
		<ul class="wonderplugin-tab-buttons" id="wonderplugin-audio-toolbar">
			<li class="wonderplugin-tab-button step1 wonderplugin-tab-buttons-selected"><?php _e( 'MP3', 'wonderplugin_audio' ); ?></li>
			<li class="wonderplugin-tab-button step2"><?php _e( 'Skins', 'wonderplugin_audio' ); ?></li>
			<li class="wonderplugin-tab-button step3"><?php _e( 'Options', 'wonderplugin_audio' ); ?></li>
			<li class="wonderplugin-tab-button step4"><?php _e( 'Preview', 'wonderplugin_audio' ); ?></li>
			<li class="laststep"><input class="button button-primary" type="button" value="<?php _e( 'Save & Publish', 'wonderplugin_audio' ); ?>"></input></li>
		</ul>
				
		<ul class="wonderplugin-tabs" id="wonderplugin-audio-tabs">
			<li class="wonderplugin-tab wonderplugin-tab-selected">	
			
				<div class="wonderplugin-toolbar">	
					<input type="button" class="button" id="wonderplugin-add-mp3" value="<?php _e( 'Add Audio', 'wonderplugin_audio' ); ?>" />
				</div>
        		  
			    <ul class="wonderplugin-table" id="wonderplugin-audio-media-table">
			    </ul>
			    <div style="clear:both;"></div>
      
			</li>
			<li class="wonderplugin-tab">
				<form>
					<fieldset>
						
						<?php 
						$skins = array(
								"bar" => "Bar",
								"bartitle" => "Bar with Title",
								"barwhite" => "White Bar",
								"darkbox" => "Dark Box",
								"jukebox" => "Jukebox",
								"musicbox" => "Music Box",
								"lightbox" => "LightBox",
								"barwhitetitle" => "White Bar with Title",
								"button24" => "Button 24",
								"button48" => "Button 48",
								"buttonblue" => "Button Blue"
								);
						
						$index = 0;
						foreach ($skins as $key => $value) {
						?>
							<div class="wonderplugin-tab-skin">
							<label><input type="radio" name="wonderplugin-audio-skin" value="<?php echo $key; ?>" selected> <?php echo $value; ?> <br /><img class="selected" style="max-width:100%;" src="<?php echo WONDERPLUGIN_AUDIO_URL; ?>images/<?php echo $key; ?>.jpg" /></label>
							</div>
						<?php
							$index++;
							if ($index % 3 == 0)
								echo '<div style="clear:both;"></div>';
						}
						?>
						
					</fieldset>
				</form>
			</li>
			<li class="wonderplugin-tab">
			
				<div class="wonderplugin-audio-options">
					<div class="wonderplugin-audio-options-menu" id="wonderplugin-audio-options-menu">
						<div class="wonderplugin-audio-options-menu-item wonderplugin-audio-options-menu-item-selected"><?php _e( 'Skin options', 'wonderplugin_audio' ); ?></div>
						<div class="wonderplugin-audio-options-menu-item"><?php _e( 'Skin CSS', 'wonderplugin_audio' ); ?></div>
						<div class="wonderplugin-audio-options-menu-item"><?php _e( 'Advanced options', 'wonderplugin_audio' ); ?></div>
					</div>
					
					<div class="wonderplugin-audio-options-tabs" id="wonderplugin-audio-options-tabs">
					
						<div class="wonderplugin-audio-options-tab wonderplugin-audio-options-tab-selected">
							<p class="wonderplugin-audio-options-tab-title"><?php _e( 'Options will be restored to the default value if you switch to a new skin in the Skins tab.', 'wonderplugin_audio' ); ?></p>
							<table class="wonderplugin-form-table-noborder">
							
								<tr>
									<th>Width</th>
									<td><label><input name="wonderplugin-audio-width" type="text" id="wonderplugin-audio-width" value="300" class="small-text" /></label></td>
								</tr>
								<tr>
									<th>Height</th>
									<td>
									<label>
										<select name='wonderplugin-audio-heightmode' id='wonderplugin-audio-heightmode'>
										  <option value="auto">Auto</option>
										  <option value="fixed">Fixed</option>
										</select>
									<input name="wonderplugin-audio-height" type="text" id="wonderplugin-audio-height" value="300" class="small-text" /></label></td>
								</tr>								
								<tr>
									<th>Play mode</th>
									<td><label><input name='wonderplugin-audio-autoplay' type='checkbox' id='wonderplugin-audio-autoplay'  /> Auto play</label>
									<br /><label><input name='wonderplugin-audio-random' type='checkbox' id='wonderplugin-audio-random'  /> Random</label>
									</td>
								</tr>
								<tr>
									<th>Loop mode</th>
									<td><label>
										<select name='wonderplugin-audio-loop' id='wonderplugin-audio-loop'>
										  <option value="0">No loop</option>
										  <option value="1">Loop all</option>
										  <option value="2">Loop single</option>
										</select>
									</label></td>
								</tr>
								
								<tr>
									<th>Responsive</th>
									<td><label><input name='wonderplugin-audio-responsive' type='checkbox' id='wonderplugin-audio-responsive'  /> Create a fullwidth audio player</label>
									</td>
								</tr>
								
								<tr>
									<th>Tracklist</th>
									<td><label><input name='wonderplugin-audio-showtracklist' type='checkbox' id='wonderplugin-audio-showtracklist'  /> Show tracklist</label>
									<br /><label>The number of tracks displayed in one page: <input name="wonderplugin-audio-tracklistitem" type="text" id="wonderplugin-audio-tracklistitem" value="10" class="small-text" /></label>
									</td>
								</tr>
								
								<tr>
									<th>Progress bar</th>
									<td><label><input name='wonderplugin-audio-showprogress' type='checkbox' id='wonderplugin-audio-showprogress'  /> Show progress bar</label>
									</td>
								</tr>
								
								<tr>
									<th>Buttons</th>
									<td><label><input name='wonderplugin-audio-showprevnext' type='checkbox' id='wonderplugin-audio-showprevnext'  /> Show previous and next button</label>
									<br /><label><input name='wonderplugin-audio-showloop' type='checkbox' id='wonderplugin-audio-showloop'  /> Show loop button</label>
									</td>
								</tr>
								
								<tr>
									<th>Loading</th>
									<td><label><input name='wonderplugin-audio-showloading' type='checkbox' id='wonderplugin-audio-showloading'  /> Show loading</label>
									</td>
								</tr>
								
								<tr>
									<th>Title in bar</th>
									<td><label>Title width: <input name="wonderplugin-audio-titleinbarwidth" type="text" id="wonderplugin-audio-titleinbarwidth" value="80" class="small-text" /></label>
									<br /><label><input name='wonderplugin-audio-titleinbarscroll' type='checkbox' id='wonderplugin-audio-titleinbarscroll'  /> Automatically scroll title</label>
									</td>
								</tr>
								
							</table>
						</div>
						
						<div class="wonderplugin-audio-options-tab">
							<table class="wonderplugin-form-table-noborder">
								<tr>
									<th>Skin CSS</th>
									<td><textarea name='wonderplugin-audio-skincss' id='wonderplugin-audio-skincss' value='' class='large-text' rows="20"></textarea></td>
								</tr>
							</table>
						</div>
						
						<div class="wonderplugin-audio-options-tab">
							<table class="wonderplugin-form-table-noborder">
								<tr>
									<th>Custom CSS</th>
									<td><textarea name='wonderplugin-audio-custom-css' id='wonderplugin-audio-custom-css' value='' class='large-text' rows="10"></textarea></td>
								</tr>
								<tr>
									<th>Advanced Options</th>
									<td><textarea name='wonderplugin-audio-data-options' id='wonderplugin-audio-data-options' value='' class='large-text' rows="10"></textarea></td>
								</tr>
							</table>
						</div>
					</div>
				</div>
				<div style="clear:both;"></div>
				
			</li>
			<li class="wonderplugin-tab">
				<div id="wonderplugin-audio-preview-tab">
					<div id="wonderplugin-audio-preview-container">
					</div>
				</div>
			</li>
			<li class="wonderplugin-tab">
				<div id="wonderplugin-audio-publish-loading"></div>
				<div id="wonderplugin-audio-publish-information"></div>
			</li>
		</ul>
		</div>
		
		<?php
	}
	
	function get_list_data() {
		return array();
	}
}