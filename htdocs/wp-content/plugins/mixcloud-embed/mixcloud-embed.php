<?php

/*

 Mixcloud Embed
 ==============================================================================

 The Mixcloud Embed plugin allows you to add the Mixcloud player into your WordPress blog or page, by using the [mixcloud] shortcode.
 You can also display a playlist of the cloudcast.
 In the last version there is the possibility to use a widget on your sidebar for view your Mixcloud profile.

   Domenico Biancardi


 Info for WordPress:
 ==============================================================================
 Plugin Name: Mixcloud Embed
 Description: The Mixcloud Embed plugin allows you to embed the Mixcloud player with the playlist or put a widget with your Mixcloud account.
 Version: 2.2.0
 Requires at least: 3.5.1
 Tested up to: 3.8.1
 Stable tag: 2.2.0
 Contributors: Domenico Biancardi
 Author: Domenico Biancardi
 Author URI: http://www.domenicobiancardi.com
 Text Domain: mixcloud-embed


*/

/**
 * Loader class for the Mixcloud Embed
 *
 */

class MixcloudEmbed
{

    /**
     * Table name used to cache a playlist
     * @var
     */
    private $table_name;

    /**
     * Enabled the sitemap plugin with registering all required hooks
     *
     * If the sm_command and sm_key GET params are given, the function will init the generator to rebuild the sitemap.
     * @static
     * @return bool
     */
    static function Enable()
    {


        MixcloudEmbed::InitPluginConstants();

        register_activation_hook(__FILE__, array("MixcloudEmbed", 'DbInstall'));

        add_action('wp_print_styles', array("MixcloudEmbed", 'AddCss'));

        // Adding 'mixcloud' shortcode. & is necessary because we are calling a function inside the class.
        add_shortcode('mixcloud', array("MixcloudEmbed", 'CallCreateShortcode'));

        // Adding a admin menu to do a default setting for the embed code
        add_action('admin_menu', array("MixcloudEmbed", 'RegisterAdminPage'));

        // Adding a button into a rich text editor
        add_action('admin_init', array('MixcloudEmbed', 'SetupEditorButton'));

        return true;

    }

    static function EnableWidget()
    {

        MixcloudEmbed::InitPluginConstants();


        if (!class_exists("MixcloudEmbedWidget")) {

            $path = trailingslashit(dirname(__FILE__));
            if (!file_exists($path . 'mixcloud-embed-widget.php')) return false;
            require_once($path . 'mixcloud-embed-widget.php');

        }
        register_widget('MixcloudEmbedWidget');


        return true;

    }


    /**
     * Init plugin constants
     * @static
     */
    static function InitPluginConstants()
    {

        if (!defined('PLUGIN_LOCALE')) {
            define('PLUGIN_LOCALE', 'mixcloud-embed-locale');
        }

        if (!defined('PLUGIN_NAME')) {
            define('PLUGIN_NAME', 'Mixcloud Embed');
        }

        if (!defined('PLUGIN_SLUG')) {
            define('PLUGIN_SLUG', 'Mixcloud-Embed');
        }

    }

    /**
     * Registers the plugin in the admin menu system
     * @static
     */
    static function RegisterAdminPage()
    {

        if (function_exists('add_options_page')) {
            add_options_page(__('Mixcloud Embed Options', 'mixcloud-embed'), __('Mixcloud-Embed', 'mixcloud'), 'level_10', MixcloudEmbed::GetBaseName(), array('MixcloudEmbed', 'CallHtmlShowOptionsPage'));
        }
    }

    /**
     * Add a button into a rich text editor
     * @static
     * @since 1.6
     */
    static function SetupEditorButton()
    {

        if (get_user_option('rich_editing') == 'true' && current_user_can('edit_posts')) {
            add_action('admin_print_scripts', array('MixcloudEmbed', 'OutputTinyMCEDialogVars'));
            add_filter('mce_external_plugins', array('MixcloudEmbed', 'AddTinyMCEButtonScript'));
            add_filter('mce_buttons', array('MixcloudEmbed', 'RegisterTinyMCEButton'));
        }
    }


    /**
     * Add the Dialog for TinyMCE
     * @static
     * @since 1.6
     */
    static function OutputTinyMCEDialogVars()
    {
    $data = array(
        'pluginVersion' => Version::getVersion(),
        'includesUrl' => includes_url(),
        'pluginsUrl' => plugins_url()
    );

    ?>
    <script type="text/javascript">
        // <![CDATA[
        window.mixcloudEmbedDialogData = <?php echo json_encode($data); ?>;
        // ]]>
    </script>
<?php
}
    /**
     * Add a button script for a TinyMCE
     * @static
     * @since 1.6
     */
    static function AddTinyMCEButtonScript($plugin_array)
    {
        $plugin_array['MixcloudEmbedButton'] = plugins_url('mixcloud-embed-button.js', __FILE__);
        return $plugin_array;
    }
    /**
     * Register a button for a TinyMCE
     * @static
     * @since 1.6
     */
    static function RegisterTinyMCEButton($buttons)
    {
        array_push($buttons, '|', 'MixcloudEmbedButton');
        return $buttons;
    }

    /**
     * Invokes the HtmlShowOptionsPage method of the generator
     * @static
     */
    static function CallHtmlShowOptionsPage()
    {
        if (MixcloudEmbed::LoadPlugin()) {
            $mixcloudObject = & MixcloudEmbedCore::GetInstance();
            $mixcloudObject->HtmlShowOptionsPage();
        }
    }


    /**
     * Invokes the CreateShortcode method of the generator
     * @static
     */
    static function CallCreateShortcode($options, $contents)
    {
        if (MixcloudEmbed::LoadPlugin()) {
            $mixcloudObject = & MixcloudEmbedCore::GetInstance();
            return $mixcloudObject->CreateShortcode($options, $contents);
        }
    }

    /**
     * Add a custom css
     * @static
     */
    static function AddCss()
    {
        wp_enqueue_style('mixcloud-embed', plugins_url('mixcloud-embed.css', __FILE__));
    }

    /**
     * Install a db table dedicated to mixcloud-embed
     * @static
     */
    static function DbInstall()
    {
        global $jal_db_version;


        $sql = "CREATE TABLE " . MixcloudEmbed::$table_name . " (
              id mediumint(9) NOT NULL AUTO_INCREMENT,
              url VARCHAR(55) DEFAULT '' NOT NULL,
              playlist text NOT NULL,
              UNIQUE KEY id (id)
                );";


        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);

        add_option("jal_db_version", $jal_db_version);
    }


    /**
     * Loads the actual generator class and tries to raise the memory and time limits if not already done by WP
     *
     * @return boolean true if run successfully
     */
    static function LoadPlugin()
    {

        $mem = abs(intval(@ini_get('memory_limit')));
        if ($mem && $mem < 64) {
            @ini_set('memory_limit', '64M');
        }

        $time = abs(intval(@ini_get("max_execution_time")));
        if ($time != 0 && $time < 120) {
            @set_time_limit(120);
        }

        if (!class_exists("MixcloudEmbedCore")) {

            $path = trailingslashit(dirname(__FILE__));
            if (!file_exists($path . 'mixcloud-embed-core.php')) return false;
            require_once($path . 'mixcloud-embed-core.php');
        }

        if (!class_exists("FlashObject")) {

            $path = trailingslashit(dirname(__FILE__));
            if (!file_exists($path . 'flash-object.php')) return false;
            require_once($path . 'flash-object.php');
        }

        MixcloudEmbedCore::Enable();
        return true;
    }

    /**
     * Returns the plugin basename of the plugin (using __FILE__)
     * @static
     * @return string The plugin basename, "sitemap" for example
     */
    static function GetBaseName()
    {
        return plugin_basename(__FILE__);
    }

    /**
     * Returns the name of this loader script, using __FILE__
     * @static
     * @return string The __FILE__ value of this loader script
     */
    static function GetPluginFile()
    {
        return __FILE__;
    }

    /**
     * Returns the plugin version
     *
     * Uses the WP API to get the meta data from the top of this file (comment)
     * @static
     * @return string The version like 3.1.1
     */
    static function GetVersion()
    {
        // @TODO eliminare tutto quello che fa riferimento a sm_version
        if (!isset($GLOBALS["sm_version"])) {
            if (!function_exists('get_plugin_data')) {
                if (file_exists(ABSPATH . 'wp-admin/includes/plugin.php')) require_once(ABSPATH . 'wp-admin/includes/plugin.php'); //2.3+
                else if (file_exists(ABSPATH . 'wp-admin/admin-functions.php')) require_once(ABSPATH . 'wp-admin/admin-functions.php'); //2.1
                else return "0.ERROR";
            }
            $data = get_plugin_data(__FILE__, false, false);
            $GLOBALS["sm_version"] = $data['Version'];
        }
        return $GLOBALS["sm_version"];
    }
}


class Version {
    static function getVersion(){
        return "1.6";
    }
}


///Enable the plugin for the init hook, but only if WP is loaded. Calling this php file directly will do nothing.
if (defined('ABSPATH') && defined('WPINC')) {
    add_action("init", array("MixcloudEmbed", "Enable"), 1000, 0);
    add_action("widgets_init", array("MixcloudEmbed", "EnableWidget"));
}
?>