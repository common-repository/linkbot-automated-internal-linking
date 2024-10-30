<?php
/*
Plugin Name: Linkbot - Automated Internal Linking
Description: This plugin integrates the Linkbot JavaScript snippet into all WordPress pages, enhancing SEO through sophisticated internal linking strategies. Linkbot optimizes website SEO by creating valuable internal links, generating detailed linking reports, and offering automated link building. This leads to better search engine comprehension, increased organic traffic, and improved sales conversions.
Version: 1.2.0
Author: Linkbot Team
Author URI: https://www.linkbot.com
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
*/

// Prevent direct file access
if (!defined('ABSPATH')) {
    exit;
}

// Register settings
function linkbot_register_settings() {
    register_setting('linkbot_options_group', 'linkbot_property_id', 'sanitize_text_field');
}
add_action('admin_init', 'linkbot_register_settings');

// Create menu item for settings page
function linkbot_register_options_page() {
    add_options_page('Linkbot Settings', 'Linkbot', 'manage_options', 'linkbot', 'linkbot_options_page');
}
add_action('admin_menu', 'linkbot_register_options_page');

// Options page content
function linkbot_options_page() {
    ?>
    <div class="wrap">
        <h2>Linkbot Settings</h2>
        <div id="linkbot-instructions" style="background-color: #f9f9f9; border: 1px solid #ddd; padding: 10px 20px; margin-top: 15px; border-radius: 5px;">
            <h3>Getting Started with Linkbot</h3>
            <p style="font-size: 14px;">Follow these steps to configure your Linkbot plugin:</p>
            <ol>
                <li><strong>Create an Account:</strong> Visit <a href="https://www.linkbot.com" target="_blank" rel="noopener noreferrer">Linkbot's website</a> and create an account if you haven't already.</li>
                <li><strong>Add Your Site:</strong> In your Linkbot account, add your WordPress site as a new property.</li>
                <li><strong>Find Property ID:</strong> On the Linkbot account home page, hover over your site's property panel to find the 'Property Settings' link. In the Property Settings, locate your unique Property ID.</li>
                <li><strong>Enter Property ID:</strong> Copy and paste the Property ID into the field below.</li>
            </ol>
        </div>
        <form method="post" action="options.php" style="margin-top: 20px;">
            <?php settings_fields('linkbot_options_group'); ?>
            <table class="form-table">
                <tr valign="top">
                    <th scope="row"><label for="linkbot_property_id">Linkbot Property ID:</label></th>
                    <td><input type="text" id="linkbot_property_id" name="linkbot_property_id" value="<?php echo esc_attr(get_option('linkbot_property_id')); ?>" class="regular-text" /></td>
                </tr>
            </table>
            <?php submit_button(); ?>
        </form>
    </div>
    <?php
}

// Insert the Linkbot script
function linkbot_enqueue_script() {
    $property_id = trim(get_option('linkbot_property_id'));
    
    if (!empty($property_id)) {
        // Build the script URL without specifying version in the URL itself
        $script_url = 'https://bot.linkbot.com/bot.js?property-id=' . esc_attr($property_id) . '#' . esc_attr($property_id);
        
        // Enqueue the script with version specified via wp_enqueue_script
        wp_enqueue_script('linkbot-script', $script_url, array(), '1.2.0', true);
		}
	}
	add_action('wp_footer', 'linkbot_enqueue_script');


// Add the defer attribute and set the ID for the Linkbot script in a uniquely named function
function linkbot_add_defer_attribute($tag, $handle) {
    if ('linkbot-script' !== $handle) {
        return $tag;
    }
    // Check if the specific ID is already in the tag
    if (strpos($tag, 'id="pagemapIdscript"') === false) {
        // Add or replace the existing ID with 'pagemapIdscript'
        // First, remove any existing id attribute
        $tag = preg_replace('/\s+id="[^"]*"/', '', $tag);
        // Now, add the new id attribute
        $tag = str_replace(' src', ' id="pagemapIdscript" src', $tag);
    }
    // Ensure the defer attribute is included
    if (strpos($tag, 'defer="defer"') === false) {
        $tag = str_replace('<script ', '<script defer="defer" ', $tag);
    }
    return $tag;
}
add_filter('script_loader_tag', 'linkbot_add_defer_attribute', 10, 2);


// Function to display a notice if the property ID is not set
function linkbot_property_id_notice() {
    ?>
    <div class="notice notice-error">
        <p><strong>Linkbot:</strong> Your plugin requires a Linkbot Property ID to function properly. Please <a href="admin.php?page=linkbot">configure it in the Linkbot settings</a>.</p>
    </div>
    <?php
}

// Add a link to the plugin action links
function linkbot_plugin_action_links($links) {
    $settings_link = '<a href="admin.php?page=linkbot">Settings</a>';
    array_unshift($links, $settings_link);
    return $links;
}
add_filter('plugin_action_links_' . plugin_basename(__FILE__), 'linkbot_plugin_action_links');


