<?php
/*
 * Plugin Name: Business Survey
 * Description: The easiest way to create survey for your website.
 * Author: ramorg2018
 * Version: 1.0.0
 */

// Check if mbsting exists
if (!defined('ABSPATH')) {
    die('Invalid request.');
}

if (!defined('rds_PLUGIN_VERSION')) {
    define('rds_PLUGIN_VERSION', '1.0.0');
}

// Custom survey image sizes
// add_image_size('rc_qu_size', 600, 400, true); // featured image - removed since version 1.5.0
add_image_size('rc_qu_size2', 400, 400, true); // image-as-answer

/* Include the basic required files
------------------------------------------------------- */

// Custom post types
require dirname(__FILE__) . '/includes/post_type.php'; 

// Custom meta
require dirname(__FILE__) . '/includes/meta.php'; 

// General functions
require dirname(__FILE__) . '/includes/functions.php'; 

// function to check if Business Survey is active
function rds_exists(){
    return;
}

/* Enqueue admin scripts to relevant pages
------------------------------------------------------- */
function rds_add_admin_scripts($hook){
    global $post;
    // Only enqueue if we're on the
    // add/edit questions, survey, or settings page
    if ($hook == 'post-new.php' || $hook == 'post.php' || $hook == 'term.php' || $hook == 'edit-tags.php' || $hook == "business-survey_page_rds_settings" || $hook == "toplevel_page_rds_serves") {
        function rds_print_scripts()
        {
            wp_enqueue_style(
                'rds_admin_style',
                plugin_dir_url(__FILE__) . './includes/css/rds_admin_style.css?v=' . rds_PLUGIN_VERSION
            );
            wp_enqueue_script(
                'rds_admin_script',
                plugins_url('./includes/js/rds_admin.js?v=' . rds_PLUGIN_VERSION, __FILE__),
                array('jquery', 'jquery-ui-sortable'),'1.0',true);
        }
        if ($hook == "business-survey_page_rds_settings" || $hook == "term.php" || $hook == 'edit-tags.php' || $hook == "toplevel_page_rds_serves") {
            rds_print_scripts();
        } else {
            if ($post->post_type === 'serve_type_questions' || $post->post_type === 'serve_type_questions') {
                rds_print_scripts();
            }
        }
    }
}
add_action('admin_enqueue_scripts', 'rds_add_admin_scripts', 10, 1);

/* Add shortcode
------------------------------------------------------- */
function rds_add_shortcode($atts){
    // Attributes
    extract(
        shortcode_atts(
            array(
                'RDserve' => $atts['rdserve'],
            ),
            $atts
        )
    );

    // Code
    ob_start();
    include plugin_dir_path(__FILE__) . './includes/show_survey.php';
    return ob_get_clean();
}
add_shortcode('HDRDserve', 'rds_add_shortcode');

/* Disable Canonical redirection for paginated survey
------------------------------------------------------- */
function rds_disable_redirect_canonical($redirect_url){
    global $post;
    if (has_shortcode($post->post_content, 'HDRDserve')) {
        $redirect_url = false;
    }
    return $redirect_url;
}
add_filter('redirect_canonical', 'rds_disable_redirect_canonical');

/* Create Business Survey Settings page
------------------------------------------------------- */
function rds_create_settings_page(){
    if (rds_user_permission()) {
        function rds_register_serves_page(){

            //Add admin section image
            $plugins_url    =   plugin_dir_url( __FILE__ ) . 'includes/images/rds_img.PNG' ;
            add_menu_page("Business Survey", "Business Survey", 'publish_posts', "rds_serves", 'rds_register_serves_page_callback', $plugins_url);

	    }
        add_action('admin_menu', 'rds_register_serves_page');

        function rds_register_settings_page(){
            add_submenu_page('rds_serves', 'Business Survey Settings', 'Settings', 'publish_posts', 'rds_settings', 'rds_register_settings_page_callback');
        }
        add_action('admin_menu', 'rds_register_settings_page', 11);
    }

    //Check plugin version
    $rds_version = sanitize_text_field(get_option("rds_PLUGIN_VERSION"));
    if (rds_PLUGIN_VERSION != $rds_version) {
        update_option("rds_PLUGIN_VERSION", rds_PLUGIN_VERSION);
        function rds_show_upgrade_message(){
            ?>
			<div class="notice notice-success is-dismissible">
				<p><strong>Business Survey</strong>. Thank you for upgrading. If you experience any issues at all, please don't hesitate to <a href = "https://wordpress.org/support/plugin/business-survey" target = "_blank">reach out for support</a>! I'm always glad to help when I can.</p>
			</div>
			<?php
        }
        add_action('admin_notices', 'rds_show_upgrade_message');
    }
}
add_action('init', 'rds_create_settings_page');

function rds_register_serves_page_callback(){
    require dirname(__FILE__) . '/includes/rds_survey.php';
}

function rds_register_settings_page_callback(){
    require dirname(__FILE__) . '/includes/rds_settings.php';
}
