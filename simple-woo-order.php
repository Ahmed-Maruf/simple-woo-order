<?php
/*
Plugin Name: Simple Order For WooCommerce
Plugin URI: https://github.com/Ahmed-Maruf/simple-woo-order
Description: Quickly create WooCommerce order for existing and new customers.
Version: 1.0.0
Author: Ahmed Maruf
Author URI: https://devmaruf.me
License: GPLv2 or later
Text Domain: swo
*/


//Todo: Check if dependant plugin is installed


function swo_enqueue_scripts()
{
    wp_enqueue_style(
        'swo-style',
        plugin_dir_url(__FILE__).'assets/css/style.css',
        array(),
        '1.0.0',
    );

    wp_enqueue_script(
        'swo-script',
        plugin_dir_url(__FILE__).'assets/js/qofw.js',
        array('jquery', 'thickbox'),
        '1.0.0',
        true
    );

    wp_localize_script(
        'swo-script',
        'swo',
        array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'dc' => __('Discount Coupon', 'qofw'),
            'cc' => __('Coupon Code', 'qofw'),
            'dt' => __('Discount In Taka', 'qofw'),
            'pt' => __('WooCommerce Quick Order', 'qofw'), //plugin title
        ));
}

add_action('admin_enqueue_scripts', 'swo_enqueue_scripts');

/**
 * Generate a new menu item in the admin
 */

add_action('admin_menu', function () {
    add_menu_page(
        __('Quick Order Create', 'swo'),
        __('WC Quick Order', 'swo'),
        'manage_woocommerce',
        'quick-order-create',
        'swo_admin_page'
    );
});

/**
 *
 */
function swo_admin_page()
{
    ?>
    <div class="qofw-form-wrapper">
        <div class="qofw-form-title">
            <h4><?php _e('WooCommerce Quick Order', 'swo'); ?></h4>
        </div>
        <div class='qofw-form-container'>
            <div class="qofw-form">
                <form action='' class='pure-form pure-form-aligned' method='POST'>
                    <fieldset>
                        <input type='hidden' name='customer_id' id='customer_id' value='0'>
                        <div class='pure-control-group'>
                            <?php $label = __('Email Address', 'swo'); ?>
                            <label for='name'><?php echo $label; ?></label>
                            <input class='qofw-control' required name='email' id='email' type='email'
                                   placeholder='<?php echo $label; ?>'>
                        </div>

                        <div class='pure-control-group'>
                            <?php $label = __('First Name', 'swo'); ?>
                            <label for='first_name'><?php echo $label; ?></label>
                            <input class='qofw-control' required name='first_name' id='first_name' type='text'
                                   placeholder='<?php echo $label; ?>'>
                        </div>

                        <div class='pure-control-group'>
                            <?php $label = __('Last Name', 'swo'); ?>
                            <label for='last_name'><?php echo $label; ?></label>
                            <input class='qofw-control' required name='last_name' id='last_name' type='text'
                                   placeholder='<?php echo $label; ?>'>
                        </div>

                        <div class='pure-control-group' id='password_container'>
                            <?php $label = __('Password', 'swo'); ?>
                            <label for='password'><?php echo $label; ?></label>
                            <input class='qofw-control-right-gap' name='password' id='password' type='text'
                                   placeholder='<?php echo $label; ?>'>
                            <button type='button' id='qofw_genpw' class="button button-primary button-hero">
                                <?php _e('Generate', 'swo'); ?>
                            </button>
                        </div>

                        <div class='pure-control-group'>
                            <?php $label = __('Phone Number', 'swo'); ?>
                            <label for='phone'><?php echo $label; ?></label>
                            <input class='qofw-control' name='phone' id='phone' type='text'
                                   placeholder='<?php echo $label; ?>'>
                        </div>

                        <div class='pure-control-group'>
                            <?php $label = __('Discount in Taka', 'swo'); ?>
                            <label id="discount-label" for="discount"><?php echo $label; ?></label>
                            <input class='qofw-control' name="discount" id="discount" type='text'
                                   placeholder='<?php echo $label; ?>'>
                        </div>

                        <div class='pure-control-group' style="margin-top:20px;margin-bottom:20px;">
                            <?php $label = __('I want to input coupon code', 'swo'); ?>
                            <label for='coupon'></label>
                            <input type='checkbox' name='coupon' id='coupon' value='1'/><?php echo $label; ?>
                        </div>

                        <div class='pure-control-group'>
                            <?php $label = __('Product Name', 'swo'); ?>
                            <label for='item'><?php echo $label; ?></label>
                            <select class='qofw-control' name='item' id='item'>
                                <option value="0"><?php _e('Select One', 'swo'); ?></option>
                            </select>
                        </div>

                        <div class=' pure-control-group'>
                            <?php $label = __('Order Note', 'swo'); ?>
                            <label for='note'><?php echo $label; ?></label>
                            <input class='qofw-control' name='note' id="note" type='text'
                                   placeholder='<?php echo $label; ?>'>
                        </div>

                        <div class='pure-control-group' style='margin-top:20px;'>
                            <label></label>
                            <button type='submit' name='submit' class='button button-primary button-hero'>
                                <?php _e('Create Order', 'swo'); ?>
                            </button>
                        </div>
                    </fieldset>
                    <input type="hidden" name="action" value="swo_form">
                    <input type="hidden" name="swo_identifier" value="">
                </form>
            </div>
            <div class="qofw-info">
            </div>
            <div class="qofw-clearfix"></div>
        </div>

    </div>
    <?php
}


add_action('wp_ajax_swo_fetch_user', function () {
    $email = strtolower(sanitize_text_field($_POST['email']));
    $user = get_user_by_email($email);

    if ($user) {
        wp_send_json(array(
            'error' => false,
            'id' => $user->ID,
            'fn' => $user->first_name,
            'ln' => $user->last_name,
            'pn' => get_user_meta($user->ID, 'phone_number', true)
        ), 200);

    } else {
        wp_send_json(array(
            'error' => true,
            'id' => 0,
            'fn' => __('Not Found', 'swo'),
            'ln' => __('Not Found', 'swo'),
            'pn' => ''
        ), 200);
    }
});

add_action('wp_ajax_swo_gen_password', function (){
    wp_send_json(wp_generate_password(12));
});
