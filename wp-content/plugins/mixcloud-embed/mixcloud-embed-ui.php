<?php
/**
 *
 * @author  : Domenico Biancardi
 * @email   : domenico.biancardi@gmail.com
 * Created  : 29/03/13 - {16.19}
 */

class MixcloudEmbedUI
{

    /**
     * @var MixcloudEmbedCore
     */
    private $core;

    function MixcloudEmbedUI(&$mixcloudEmbedCore) {
        global $wp_version;
        $this->core = &$mixcloudEmbedCore;

    }

    function HtmlShowOptionsPage()
    {
        if (!current_user_can('manage_options')) {
            wp_die(__('You do not have sufficient permissions to access this page.'));
        }
        ?>
    <div class="wrap">
        <h2>Mixcloud Embed Default Settings</h2>

        <p>These settings will become the new defaults used by the Mixcloud Embed throughout your blog.</p>

        <p>You can always override these settings on a per-shortcode basis. Setting the 'params' attribute in a
            shortcode overrides these defaults individually.</p>

        <form method="post" action="<?php echo $this->core->GetBackLink() ?>">
            <?php settings_fields('mixcloud-embed-settings'); ?>
            <table class="form-table">

                <tr valign="top">
                    <th scope="row">Widget Type</th>
                    <td>
                        <input type="radio" id="mixcloud-embed_player_iframe" name="mixcloud-embed_player_iframe" value="true"  <?php if ($this->core->GetOption('player_iframe') == "true") echo 'checked'; ?> />
                        <label for="mixcloud-embed_player_iframe" style="margin-right: 1em;">HTML5 (Iframe)</label>
                        <input type="radio" id="mixcloud-embed_player_iframe" name="mixcloud-embed_player_iframe" value="false" <?php if ($this->core->GetOption('player_iframe') == "false") echo 'checked'; ?> />
                        <label for="mixcloud-embed_player_iframe" style="margin-right: 1em;">Flash (Object Tag)</label>
                    </td>
                </tr>

                <tr valign="top">
                    <th scope="row">Player Height for Tracks</th>
                    <td>
                        <input type="text" id="mixcloud-embed_player_height" name="mixcloud-embed_player_height" value="<?php echo $this->core->GetOption('player_height'); ?>"/>
                        (px, or %)<br/>
                        Leave blank to use the default.
                    </td>
                </tr>

                <tr valign="top">
                    <th scope="row">Player Width</th>
                    <td>
                        <input type="text" name="mixcloud-embed_player_width" id="mixcloud-embed_player_width" value="<?php echo $this->core->GetOption('player_width'); ?>"/>
                        (px, or %)<br/>
                        Leave blank to use the default.
                    </td>
                </tr>


                <tr valign="top">
                    <th scope="row">Color</th>
                    <td>
                        <input type="text" name="mixcloud-embed_color" id="mixcloud-embed_color" value="<?php echo $this->core->GetOption('color'); ?>"/>
                        (color hex code e.g. ff6699)<br/>
                        Defines the color to paint the play button, waveform and selections.
                    </td>
                </tr>


                <tr valign="top">
                    <th scope="row">Show Cover</th>
                    <td>
                        <input type="checkbox" name="mixcloud-embed_cover" id="mixcloud-embed_cover" value="1" <?=($this->core->GetOption('cover') == "1") ? "checked='checked'":""?>/>
                        View the cover of cloudcast
                    </td>
                </tr>

                <tr valign="top">
                    <th scope="row">Mini Player</th>
                    <td>
                        <input type="checkbox" name="mixcloud-embed_mini" id="mixcloud-embed_mini" value="1" <?php echo $this->core->GetOption('mini'); ?>" <?=($this->core->GetOption('mini') == "1") ? "checked='checked'":""?>/>
                        View only essential controls
                    </td>
                </tr>

                <tr valign="top">
                    <th scope="row">Light Widget</th>
                    <td>
                        <input type="checkbox" name="mixcloud-embed_light" id="mixcloud-embed_light" value="1" <?php echo $this->core->GetOption('light'); ?>" <?=($this->core->GetOption('light') == "1") ? "checked='checked'":""?>/>
                        Light color of embed controller
                    </td>
                </tr>

                <tr valign="top">
                    <th scope="row">Autoplay</th>
                    <td>
                        <input type="checkbox" name="mixcloud-embed_autoplay" id="mixcloud-embed_autoplay" value="1" <?php echo $this->core->GetOption('autoplay'); ?>" <?=($this->core->GetOption('autoplay') == "1") ? "checked='checked'":""?>/>
                        Autoplay a cloudcast
                    </td>
                </tr>

                <tr valign="top">
                    <th scope="row">Show Tracklist</th>
                    <td>
                        <input type="checkbox" name="mixcloud-embed_tracklist" id="mixcloud-embed_tracklist" value="1" <?php echo $this->core->GetOption('tracklist'); ?>" <?=($this->core->GetOption('tracklist') == "1") ? "checked='checked'":""?>/>
                        Show the tracklist
                    </td>
                </tr>

                <tr valign="top">
                    <th scope="row">Show Artwork</th>
                    <td>
                        <input type="checkbox" name="mixcloud-embed_artwork" id="mixcloud-embed_artwork" value="1" <?php echo $this->core->GetOption('artwork'); ?>" <?=($this->core->GetOption('artwork') == "1") ? "checked='checked'":""?>/>
                        Show the artwork
                    </td>
                </tr>

            </table>

            <h3>Some example shortcode</h3>
            <table>
                <tr>
                    <td>View a single cloudcast on post</td>
                </tr>
                <tr>
                    <td><pre>[mixcloud]https://www.mixcloud.com/dottblanchard/dott-blanchard-discovering-music/[/mixcloud]</pre></td>
                </tr>
                <tr>
                    <td>View a multi cloudcast on post</td>
                </tr>
                <tr>
                    <td><pre>[mixcloud]http://www.mixcloud.com/BJT/playlists/bjt-djset/[/mixcloud]</pre></td>
                </tr>
            </table>

            <p class="submit">
                <input type="submit" name="mixcloud-embed_update" value="<?php _e('Update options', 'mixcloud-embed'); ?>" />
            </p>

        </form>
    </div>
    <?php
    }
}
?>