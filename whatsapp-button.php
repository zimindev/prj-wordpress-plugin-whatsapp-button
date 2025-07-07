<?php
/*
Plugin Name: WhatsApp Button
Description: Adds a floating WhatsApp button that opens chat directly. Admins can set phone number and position.
Version: 1.2
Author: Sasha Zimin
Author URI: https://zimin.dev
License: GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Text Domain: whatsapp-button-zimindev
*/

// Prevent direct access
if (!defined('ABSPATH')) exit;

// Register admin menu
function wb_register_settings_menu() {
    add_options_page(
        'WhatsApp Button Settings',
        'WhatsApp Button',
        'manage_options',
        'whatsapp-button',
        'wb_settings_page'
    );
}
add_action('admin_menu', 'wb_register_settings_menu');

// Settings page
function wb_settings_page() {
    ?>
    <div class="wrap">
        <h1>WhatsApp Button Settings</h1>
        <form method="post" action="options.php">
            <?php
            settings_fields('wb_settings_group');
            do_settings_sections('wb_settings_group');
            ?>
            <table class="form-table">
                <tr valign="top">
                    <th scope="row">WhatsApp Number</th>
                    <td>
                        <input 
                            type="text" 
                            name="wb_phone_number" 
                            value="<?php echo esc_attr(get_option('wb_phone_number')); ?>" 
                            placeholder="e.g. 1234567890" 
                        />
                        <p class="description">Enter with country code (without + sign)</p>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">Button Position</th>
                    <td>
                        <select name="wb_button_position">
                            <option value="right" <?php selected(get_option('wb_button_position'), 'right'); ?>>Right side</option>
                            <option value="left" <?php selected(get_option('wb_button_position'), 'left'); ?>>Left side</option>
                        </select>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">Button Color</th>
                    <td>
                        <input type="color" name="wb_button_color" value="<?php echo esc_attr(get_option('wb_button_color', '#25D366')); ?>">
                    </td>
                </tr>
            </table>
            <?php submit_button(); ?>
        </form>
    </div>
    <?php
}

// Register settings
function wb_register_settings() {
    register_setting(
        'wb_settings_group',
        'wb_phone_number',
        array(
            'sanitize_callback' => 'wb_sanitize_phone_number'
        )
    );
    
    register_setting(
        'wb_settings_group',
        'wb_button_position',
        array(
            'default' => 'right'
        )
    );
    
    register_setting(
        'wb_settings_group',
        'wb_button_color',
        array(
            'default' => '#25D366'
        )
    );
}
add_action('admin_init', 'wb_register_settings');

// Sanitize phone number
function wb_sanitize_phone_number($input) {
    return preg_replace('/\D/', '', $input);
}

// Add WhatsApp button to frontend
function wb_add_whatsapp_button() {
    $phone = get_option('wb_phone_number');
    if (!$phone) return;
    
    $position = get_option('wb_button_position', 'right');
    $color = get_option('wb_button_color', '#25D366');
    
    $whatsapp_url = 'https://wa.me/' . $phone;
    $position_css = ($position === 'left') ? 'left: 20px;' : 'right: 20px;';
    
    echo '
    <a href="' . esc_url($whatsapp_url) . '" target="_blank" class="wb-float" title="Chat on WhatsApp">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="40" height="40" fill="#fff">
            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
        </svg>
    </a>
    <style>
        .wb-float {
            position: fixed;
            width: 70px;
            height: 70px;
            bottom: 25px;
            ' . $position_css . '
            background-color: ' . $color . ';
            color: #FFF;
            border-radius: 50px;
            text-align: center;
            box-shadow: 2px 2px 10px rgba(0,0,0,0.2);
            z-index: 1000;
            display: flex;
            justify-content: center;
            align-items: center;
            transition: all 0.3s ease;
        }
        .wb-float:hover {
            transform: scale(1.1);
            box-shadow: 3px 3px 15px rgba(0,0,0,0.3);
        }
        @media (max-width: 768px) {
            .wb-float {
                width: 60px;
                height: 60px;
                bottom: 20px;
            }
        }
    </style>
    ';
}
add_action('wp_footer', 'wb_add_whatsapp_button');
