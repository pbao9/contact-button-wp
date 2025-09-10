<?php
/**
 * Plugin Name: Baro Contact Chat
 * Description: A simple contact chat plugin with customizable buttons (Phone, Zalo, Messenger, Email) and color options.
 * Version: 1.1
 * Author: Baro Dev
 * Author URI: https://baro-dev.io.vn, https://devro-tech.com, https://tgs.com.vn
 * Plugin URI: https://baro-dev.io.vn
 */

if (!defined('WPINC')) {
    die;
}

define('BCC_PLUGIN_URL', plugin_dir_url(__FILE__));
define('BCC_PLUGIN_DIR', plugin_dir_path(__FILE__));

class Baro_Contact_Chat {

    public function __construct() {
        add_action('admin_menu', [$this, 'bcc_add_admin_menu']);
        add_action('admin_init', [$this, 'bcc_register_settings']);
        add_action('wp_enqueue_scripts', [$this, 'bcc_enqueue_scripts']);
        add_action('admin_enqueue_scripts', [$this, 'bcc_admin_enqueue_scripts']);
        add_action('wp_footer', [$this, 'bcc_display_chat_buttons']);
    }

    public function bcc_add_admin_menu() {
        add_menu_page(
            'Baro Contact Chat',
            'Baro Contact Chat',
            'manage_options',
            'baro-contact-chat',
            [$this, 'bcc_create_admin_page'],
            'dashicons-format-chat',
            80
        );
    }

    public function bcc_register_settings() {
        register_setting('bcc_settings_group', 'bcc_enable');
        register_setting('bcc_settings_group', 'bcc_phone_number');
        register_setting('bcc_settings_group', 'bcc_zalo_link');
        register_setting('bcc_settings_group', 'bcc_messenger_link');
        register_setting('bcc_settings_group', 'bcc_email_address');
        register_setting('bcc_settings_group', 'bcc_primary_color');
        register_setting('bcc_settings_group', 'bcc_gradient_enable');
        register_setting('bcc_settings_group', 'bcc_gradient_top_color');
        register_setting('bcc_settings_group', 'bcc_gradient_bottom_color');
        register_setting('bcc_settings_group', 'bcc_gradient_direction');
    }

    public function bcc_create_admin_page() {
        ?>
        <div class="wrap">
            <h1>Baro Contact Chat Settings</h1>
            <form method="post" action="options.php">
                <?php settings_fields('bcc_settings_group'); ?>
                <?php do_settings_sections('bcc_settings_group'); ?>
                <table class="form-table">
                    <tr valign="top">
                        <th scope="row">Enable Plugin</th>
                        <td><input type="checkbox" name="bcc_enable" value="1" <?php checked(1, get_option('bcc_enable'), true); ?> /></td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">Phone Number</th>
                        <td><input type="text" name="bcc_phone_number" value="<?php echo esc_attr(get_option('bcc_phone_number')); ?>" class="regular-text" /></td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">Zalo Link (e.g., https://zalo.me/your_phone)</th>
                        <td><input type="text" name="bcc_zalo_link" value="<?php echo esc_attr(get_option('bcc_zalo_link')); ?>" class="regular-text" /></td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">Messenger Link (e.g., https://m.me/your_page)</th>
                        <td><input type="text" name="bcc_messenger_link" value="<?php echo esc_attr(get_option('bcc_messenger_link')); ?>" class="regular-text" /></td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">Email Address</th>
                        <td><input type="email" name="bcc_email_address" value="<?php echo esc_attr(get_option('bcc_email_address')); ?>" class="regular-text" /></td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">Primary Color</th>
                        <td><input type="text" name="bcc_primary_color" value="<?php echo esc_attr(get_option('bcc_primary_color', '#0073aa')); ?>" class="color-picker" /></td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">Enable Gradient Background</th>
                        <td><input type="checkbox" name="bcc_gradient_enable" value="1" <?php checked(1, get_option('bcc_gradient_enable'), true); ?> /></td>
                    </tr>
                    <tr valign="top" class="gradient-options">
                        <th scope="row">Gradient Top Color</th>
                        <td><input type="text" name="bcc_gradient_top_color" value="<?php echo esc_attr(get_option('bcc_gradient_top_color', '#0073aa')); ?>" class="color-picker" /></td>
                    </tr>
                    <tr valign="top" class="gradient-options">
                        <th scope="row">Gradient Bottom Color</th>
                        <td><input type="text" name="bcc_gradient_bottom_color" value="<?php echo esc_attr(get_option('bcc_gradient_bottom_color', '#00b8e6')); ?>" class="color-picker" /></td>
                    </tr>
                    <tr valign="top" class="gradient-options">
                        <th scope="row">Gradient Direction</th>
                        <td>
                            <select name="bcc_gradient_direction">
                                <option value="to bottom" <?php selected(get_option('bcc_gradient_direction'), 'to bottom'); ?>>Top to Bottom</option>
                                <option value="to right" <?php selected(get_option('bcc_gradient_direction'), 'to right'); ?>>Left to Right</option>
                                <option value="to bottom right" <?php selected(get_option('bcc_gradient_direction'), 'to bottom right'); ?>>Top Left to Bottom Right</option>
                                <option value="to bottom left" <?php selected(get_option('bcc_gradient_direction'), 'to bottom left'); ?>>Top Right to Bottom Left</option>
                            </select>
                        </td>
                    </tr>
                </table>
                <?php submit_button(); ?>
            </form>
        </div>
        <?php
    }

    public function bcc_enqueue_scripts() {
        if (get_option('bcc_enable')) {
            wp_enqueue_style('bcc-style', BCC_PLUGIN_URL . 'assets/css/style.css');
            wp_enqueue_script('bcc-main-script', BCC_PLUGIN_URL . 'assets/js/main.js', [], '1.0', true);
            
            $primary_color = get_option('bcc_primary_color', '#0073aa');
    
            // Hàm chuyển HEX sang RGBA
            if (!function_exists('hex2rgba')) {
                function hex2rgba($color, $alpha = 1){
                    $color = str_replace('#','',$color);
                    if(strlen($color) == 3){
                        $r = hexdec(str_repeat(substr($color,0,1),2));
                        $g = hexdec(str_repeat(substr($color,1,1),2));
                        $b = hexdec(str_repeat(substr($color,2,1),2));
                    } else {
                        $r = hexdec(substr($color,0,2));
                        $g = hexdec(substr($color,2,2));
                        $b = hexdec(substr($color,4,2));
                    }
                    return "rgba($r,$g,$b,$alpha)";
                }
            }
    
            $primary_color_rgba = hex2rgba($primary_color, 0.8);

            $custom_css = "
                .bcc-contact-buttons a,
                .bcc-phone {
                    background-color: {$primary_color};
                }
                @keyframes pulse {
                    0% { box-shadow: 0 0 0 0px rgba(0,0,0,0.2); }
                    100% { box-shadow: 0 0 0 20px rgba(0,0,0,0); }
                }
                .bcc-contact-buttons .phone-wrapper span {
                    background-color: {$primary_color_rgba};
                }
            ";

            $gradient_enable = get_option('bcc_gradient_enable');

            if($gradient_enable){
                $gradient_top_color = get_option('bcc_gradient_top_color', '#0073aa');
                $gradient_bottom_color = get_option('bcc_gradient_bottom_color', '#00b8e6');
                $gradient_direction = get_option('bcc_gradient_direction', 'to bottom');
                $custom_css .= "
                    @media (max-width: 478px){
                        .bcc-contact-buttons {
                            background: linear-gradient(" . $gradient_direction . ", " . $gradient_top_color . ", " . $gradient_bottom_color . ");
                        }
                    }
                ";
            } else {
                $custom_css .= "
                    @media (max-width: 478px){
                        .bcc-contact-buttons {
                            background-color: {$primary_color};
                        }
                    }
                ";
            }
    
            wp_add_inline_style('bcc-style', $custom_css);
        }
    }
    

    public function bcc_admin_enqueue_scripts($hook) {
        if ($hook != 'toplevel_page_baro-contact-chat') {
            return;
        }
        wp_enqueue_style('wp-color-picker');
        wp_enqueue_script('bcc-admin-script', BCC_PLUGIN_URL . 'assets/js/admin.js', ['jquery', 'wp-color-picker'], false, true);
    }


    public function bcc_display_chat_buttons() {
        if (!get_option('bcc_enable')) {
            return;
        }

        $phone = get_option('bcc_phone_number');
        $zalo = get_option('bcc_zalo_link');
        $messenger = get_option('bcc_messenger_link');
        $email = get_option('bcc_email_address');
        
        $icon_url = BCC_PLUGIN_URL . 'assets/images/';

        echo '<div class="bcc-contact-buttons">';

        // Phone Button
        if (!empty($phone)) {
            echo '<a href="tel:' . esc_attr($phone) . '" class="bcc-phone bcc-pulse">' . 
                 '<img src="' . $icon_url . 'btn_phone.png" alt="Phone">' . 
                 '<span class="bcc-phone-text">' . esc_html($phone) . '</span>' . 
                 '</a>';
        }

        // Zalo Button
        if (!empty($zalo)) {
            echo '<a href="' . esc_url($zalo) . '" target="_blank" class="bcc-zalo bcc-pulse"><img src="' . $icon_url . 'btn_zalo.png" alt="Zalo"></a>';
        }

        // Messenger Button
        if (!empty($messenger)) {
            echo '<a href="' . esc_url($messenger) . '" target="_blank" class="bcc-messenger bcc-pulse"><img src="' . $icon_url . 'btn_mess.png" alt="Messenger"></a>';
        }

        // Email Button (Re-enabled)
        if (!empty($email)) {
            echo '<a href="mailto:' . esc_attr($email) . '" class="bcc-email"><img src="' . $icon_url . 'email.svg" alt="Email"></a>';
        }

        echo '</div>';
    }
}

new Baro_Contact_Chat();