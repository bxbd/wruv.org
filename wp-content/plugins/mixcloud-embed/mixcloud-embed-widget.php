<?php
/**
 *
 * @author  : Domenico Biancardi
 * @email   : domenico.biancardi@gmail.com
 * Created  : 29/03/13 - {10.10}
 */


class MixcloudEmbedWidget extends WP_Widget
{


    function MixcloudEmbedWidget()
    {
        $widget_opts = array(
            'classname' => PLUGIN_NAME,
            'description' => __('View a mixcloud profile with your widget', PLUGIN_LOCALE)
        );

        $this->WP_Widget(PLUGIN_SLUG, __(PLUGIN_NAME, PLUGIN_LOCALE), $widget_opts);
        load_plugin_textdomain(PLUGIN_LOCALE, false, dirname(plugin_basename(__FILE__)) . '/lang/');
    }

    function widget($args, $instance)
    {

        //global $before_title, $after_title, $before_widget, $after_widget;
        extract($args, EXTR_SKIP);


        $mixcloud_title = empty($instance['mixcloud_title']) ? '' : apply_filters('mixcloud_title', $instance['mixcloud_title']);
        $mixcloud_profile = empty($instance['mixcloud_profile']) ? '' : apply_filters('mixcloud_profile', $instance['mixcloud_profile']);
        $mixcloud_height = empty($instance['mixcloud_height']) ? '' : apply_filters('mixcloud_height', $instance['mixcloud_height']);
        $mixcloud_width = empty($instance['mixcloud_width']) ? '' : apply_filters('mixcloud_width', $instance['mixcloud_width']);

        $mixcloudEmbed = new mixcloudEmbed();

        $title = apply_filters('widget_title', empty($mixcloud_title) ? "" : $mixcloud_title, $instance, "h3");

        if ($title)
            $title = $before_title . $title . $after_title;

        if (!class_exists("MixcloudProfile")) {
            $path = trailingslashit(dirname(__FILE__));
            if (!file_exists($path . 'mixcloud-embed-core.php')) return false;
            require_once($path . 'mixcloud-embed-core.php');
        }
        $code = new MixcloudProfile(array("height" => $mixcloud_height, "width" => $mixcloud_width), $mixcloud_profile);
        ?>
        <?= $before_widget ?>
            <?= $title ?>
            <?= $code ?>
        <?= $after_widget ?>
    <?

    } // end widget


    function form($instance)
    {


        $instance = wp_parse_args(
            (array)$instance,
            array(
                'mixcloud_title' => '',
                'mixcloud_profile' => '',
                'mixcloud_heigth' => '',
                'mixcloud_width' => ''
            )
        );

        $mixcloud_title = strip_tags(stripslashes($instance['mixcloud_title']));
        $mixcloud_profile = strip_tags(stripslashes($instance['mixcloud_profile']));
        $mixcloud_heigth = strip_tags(stripslashes($instance['mixcloud_heigth']));
        $mixcloud_width = strip_tags(stripslashes($instance['mixcloud_width']));

        ?>

    <div class="wrapper">

        <div class="option">
            <label for="mixcloud_title">
                Widget Title:
            </label><br/>
            <input type="text" class="widefat" id="<?php echo $this->get_field_id('mixcloud_title'); ?>" name="<?php echo $this->get_field_name('mixcloud_title'); ?>" value="<?php echo $instance['mixcloud_title']; ?>" class="">
        </div>


        <div class="option">
            <label for="mixcloud_profile">
                Mixcloud Profile:
            </label><br/>
            <input type="text" class="widefat" id="<?php echo $this->get_field_id('mixcloud_profile'); ?>" name="<?php echo $this->get_field_name('mixcloud_profile'); ?>" value="<?php echo $instance['mixcloud_profile']; ?>" class="">
        </div>

        <div class="option">
            <label for="mixcloud_height">
                Height:
            </label><br/>
            <input type="text" id="<?php echo $this->get_field_id('mixcloud_height'); ?>" name="<?php echo $this->get_field_name('mixcloud_height'); ?>" value="<?php echo $instance['mixcloud_height']; ?>" class="">
        </div>

        <div class="option">
            <label for="mixcloud_width">
                Width:
            </label><br/>
            <input type="text" class="widefat" id="<?php echo $this->get_field_id('mixcloud_width'); ?>" name="<?php echo $this->get_field_name('mixcloud_width'); ?>" value="<?php echo $instance['mixcloud_width']; ?>" class="">
        </div>

    </div>


    <?


    } // end form


}
?>