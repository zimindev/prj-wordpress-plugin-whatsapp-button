<?php
/*
Plugin Name: WhatsApp Button
Description: Adds a floating WhatsApp button to the site. Admins can set the number from settings.
Version: 1.0
Author: Sasha Zimin
Author URI:htpps//:zimin.dev
License: GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Text Domain: whatsapp-button-zimindev
Domain Path: /languages
GitHub Plugin URI:#
*/

//! Prevent direct access to the file for security
if (!defined('ABSPATH')) exit;

//! Register the admin settings menu under "Settings" in WordPress dashboard
function wb_register_settings_menu() {
    // Adds a submenu page for WhatsApp Button settings
    add_options_page(
        'WhatsApp Button Settings',  // Page title
        'WhatsApp Button',           // Menu title
        'manage_options',            // Capability required to access this page
        'whatsapp-button',           // Menu slug
        'wb_settings_page'           // Callback function that outputs the page content
    );
}
add_action('admin_menu', 'wb_register_settings_menu');

//! Output the HTML for the WhatsApp Button settings page
function wb_settings_page() {
    ?>
    <div class="wrap">
        <h1>WhatsApp Button Settings</h1>
        <form method="post" action="options.php">
            <?php
            //! Output necessary hidden fields and nonce for the settings group
            settings_fields('wb_settings_group');
            //! Output registered settings sections and fields for this group (none here, but required)
            do_settings_sections('wb_settings_group');
            ?>
            <table class="form-table">
                <tr valign="top">
                <th scope="row">WhatsApp Phone Number</th>
                <td>
                    <!-- Input field for the WhatsApp phone number stored in options -->
                    <input 
                        type="text" 
                        name="wb_phone_number" 
                        value="<?php echo esc_attr(get_option('wb_phone_number')); ?>" 
                        placeholder="e.g. 1234567890" 
                    />
                </td>
                </tr>
            </table>
            <!-- Submit button to save the settings -->
            <?php submit_button(); ?>
        </form>
    </div>
    <?php
}

//! Register the setting in WordPress to allow storing and sanitizing the phone number
function wb_register_settings() {
    register_setting(
        'wb_settings_group',   // Option group name
        'wb_phone_number'      // Option name to store the phone number
    );
}
add_action('admin_init', 'wb_register_settings');

//! Add the floating WhatsApp button to the frontend footer of the site
function wb_add_whatsapp_button() {
    //! Get the saved phone number, stripping any non-digits to ensure proper formatting
    $phone = preg_replace('/\D/', '', get_option('wb_phone_number'));
    
    //! If no valid phone number is set, do not display the button
    if (!$phone) return;

    //! Output the HTML for the floating WhatsApp button and inline CSS styles
    echo '
    <a href="https://wa.me/' . esc_attr($phone) . '" target="_blank" class="wb-float" title="Chat on WhatsApp">
        <img src="https://img.icons8.com/color/48/000000/whatsapp--v1.png" alt="WhatsApp">
    </a>
    <style>
        /* Styles for the floating button */
        .wb-float {
            position: fixed;        /* Fix position on screen */
            width: 60px;
            height: 60px;
            bottom: 20px;           /* Distance from bottom */
            right: 20px;            /* Distance from right */
            background-color: #25D366; /* WhatsApp green */
            color: #FFF;
            border-radius: 50px;    /* Rounded circle */
            text-align: center;
            font-size: 30px;
            box-shadow: 2px 2px 3px #999;
            z-index: 1000;          /* Make sure it sits on top */
            display: flex;          /* Flexbox to center image */
            justify-content: center;
            align-items: center;
        }
        .wb-float img {
            width: 32px;
            height: 32px;
        }
    </style>
    ';
}
//! Hook the button output to wp_footer to show it on the frontend pages
add_action('wp_footer', 'wb_add_whatsapp_button');
