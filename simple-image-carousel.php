<?php
/*
Plugin Name: Simple Image Carousel
Plugin URI:  http://umeshghimire.com.np
Description: Image Carousel
Version: 1.0.0
Author: Umesh Ghimire
Author URI: http://umeshghimire.com.np
Date: 2015-09-11
*/

if (!defined('ABSPATH')) {
    exit;
}

class Simple_Image_Carousel
{

    public $pluginUrl;

    public $postType;


    public function __construct()
    {
        $this->postType = "simplecarousel";
        $this->pluginUrl = plugin_dir_path(__FILE__);


        add_action('init', array($this, 'register_shortcodes'));


        add_action('wp_enqueue_scripts', array($this, 'simple_image_carousel_script'), 10);

        add_action('wp_enqueue_scripts', array($this, 'simple_image_carousel_css'), 10); // Load CSS

        add_action('init', array($this, 'simple_image_carousel_posttype'));

        add_filter('manage_posts_columns', array($this, '_columns_head'));

        add_action('manage_posts_custom_column', array($this, '_columns_content'), 10, 2);

        //add_action('add_meta_boxes', array($this, 'simple_image_carousel_link_metabox'));

        add_action('save_post', array($this, 'simple_image_carousel_callback_save_data'));


    }

    function _get_featured_image($post_ID)
    {
        $post_thumbnail_id = get_post_thumbnail_id($post_ID);
        if ($post_thumbnail_id) {
            $post_thumbnail_img = wp_get_attachment_image_src($post_thumbnail_id, 'simplecarousel');

            return $post_thumbnail_img[0];
        }
    }


    function _columns_head($defaults)
    {
        $defaults['featured_image'] = 'Featured Image';

        return $defaults;
    }

    function _columns_content($column_name, $post_ID)
    {
        if ($column_name == 'featured_image') {
            $post_featured_image = $this->_get_featured_image($post_ID);
            if ($post_featured_image) {
                echo '<img src="' . $post_featured_image . '" height="100" />';
            }
        }
    }

    function simple_image_carousel_posttype()
    {
        register_post_type($this->postType,
            array(
                'labels' => array(
                    'name' => __('Simple Image Carousel'),
                    'singular_name' => __('Carousel'),
                    'menu_name' => __('Simple Image Carousel'),
                    'all_items' => __('All Carousel'),
                    'add_new_item' => __('Add New Carousel', 'simplecarousel')
                ),
                'supports' => array('title', 'thumbnail'),
                'rewrite' => array(
                    'slug' => 'simple_image_carousel',
                    'with_front' => true
                ),
                'public' => true


            )
        );
        flush_rewrite_rules(false);
    }


    function register_shortcodes()
    {


        add_shortcode('simple-image-carousel', array($this, 'image_carousel'));

    }

    function image_carousel($atts)
    {


        $args = array(
            'post_type' => $this->postType,
            'post_status' => 'publish',
            'posts_per_page' => -1,
            'order' => 'desc'
        );
        $carousel_post_type = new WP_Query($args);


        if ($carousel_post_type->have_posts()):
            echo '<div class="' . $this->postType . '">';
            echo '<div class="' . $this->postType . '_InnerWrapper">';
            echo '<ul class="' . $this->postType . '_List">';
            while ($carousel_post_type->have_posts()): $carousel_post_type->the_post();

                $image_URL = get_post_meta(get_the_ID(), '_simple_image_url_meta_value_key', true);


                echo '<li>';

                if ($image_URL != '') {

                    echo '<a href="#" target="_blank">';
                } else {

                    echo '<a href="#">';
                }

                the_post_thumbnail();
                echo '</a>';
                echo '</li>';


            endwhile;
            echo '</ul>';
            echo '<div style="clear:both"></di    v>';
            echo '</div>';
            echo '</div>';
        endif;


        return;


    }


    public function init($post)
    {


        $this->isValidPostType($post) ? require_once($this->pluginUrl . 'form.php') : '';


        return;

    }

    public function simple_image_carousel_script()
    {

        if (wp_script_is('jquery')) {

            //wp_enqueue_script('', 'https://code.umesh.com/jquery-2.1.4.min.js', array(), '2.1.4', true);
        }

        wp_enqueue_script('', plugins_url('js/simple-image-carousel.js', __FILE__), array(), '1.0.0', true);


    }

    public function simple_image_carousel_css()
    {


        wp_enqueue_style('', plugins_url('css/simple-image-carousel.css', __FILE__), '1.0.0');


    }

    public function simple_image_carousel_link_metabox()
    {

        $screens = array($this->postType);

        foreach ($screens as $screen) {

            add_meta_box(
                'simple_image_carousel_callback',
                __('Image URL', 'simple_image_carousel_metabox_id'),
                array($this, 'simple_image_carousel_callback'),//image_url_meta_box_callback',
                $screen
            );
        }
    }


    public function simple_image_carousel_callback($post)
    {

        wp_nonce_field('simple_image_carousel_callback_save_data', 'simple_image_carousel_link_nonce');


        $value = get_post_meta($post->ID, '_simple_image_url_meta_value_key', true);

        echo '<label for="simple_image_carousel_link">';
        _e('Image Link');
        echo '</label> ';
        echo '<input type="text" id="simple_image_carousel_link" name="simple_image_carousel_link" value="' . esc_attr($value) . '" size="25" />';
    }

    public function simple_image_carousel_callback_save_data($post_id)
    {


        if (!isset($_POST['simple_image_carousel_link'])) {
            return;
        }


        if (!wp_verify_nonce($_POST['simple_image_carousel_link_nonce'], 'simple_image_carousel_callback_save_data')) {

            return $post_id;
        }


        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        if (isset($_POST['post_type']) && $this->postType == $_POST['post_type']) {

            if (!current_user_can('edit_page', $post_id)) {
                return;
            }

        } else {

            if (!current_user_can('edit_post', $post_id)) {
                return;
            }
        }


        if (!isset($_POST['simple_image_carousel_link'])) {
            return;
        }


        $my_data = sanitize_text_field($_POST['simple_image_carousel_link']);


        update_post_meta($post_id, '_simple_image_url_meta_value_key', $my_data);
    }


}


$Simple_Image_Carousel = new Simple_Image_Carousel();

