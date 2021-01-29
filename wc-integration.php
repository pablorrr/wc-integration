<?php
/**
 * Plugin Name: WC Integration
 * Plugin URI: https://websitecreator.cba.pl
 * Description: Change product name in WC integration bookmark
 * Author: Pawel Kalisz
 * Author URI: https://websitecreator.cba.pl
 * Version: 1.0
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 *
 */
if (!defined('ABSPATH')) exit;

define('WC_TAB_PLUGIN_PATH', plugin_dir_path(__FILE__));
define('MY_PLUGIN_SLUG', 'wc-settings');

if (!class_exists('WC_Tabs')) :

    class WC_Tabs
    {

        /**
         * Construct the plugin.
         */
        public function __construct()
        {
            add_action('plugins_loaded', array($this, 'init'));
            add_filter('woocommerce_product_tabs', array($this, 'wc_tabs_rename'), 98);
        }

        /**
         * Initialize the plugin.
         */
        public function init()
        {
            if (!function_exists('is_plugin_active'))
                require_once(ABSPATH . '/wp-admin/includes/plugin.php');

            // Checks if WooCommerce is installed.
            if (class_exists('WC_Integration') && class_exists('woocommerce') &&
                is_plugin_active('woocommerce/woocommerce.php')) {
                // Include our integration class.
                include_once WC_TAB_PLUGIN_PATH . 'class-wc-integration.php';

                // Register the integration.
                add_filter('woocommerce_integrations', array($this, 'wc_tab_integration'));
            } else {
                // throw an admin error if you like
                add_action('admin_notices', array($this, 'wc_tab_admin_notice'));
                return;
            }

            add_filter('plugin_action_links_' . plugin_basename(__FILE__), array($this, 'wc_tab_action_links'));
        }

        function wc_tab_action_links($links)
        {
            $links[] = '<a href="' . menu_page_url(MY_PLUGIN_SLUG, false) . '&tab=integration&section=wc-tabs-integration">Settings</a>';
            return $links;
        }

        //admin notices when WC is not activated
        public function wc_tab_admin_notice()
        {
            $woo_err = new WP_Error('woo_err', 'Woocommerce not activated!');
            $woo_activ_error = new WP_Error('woo_activ_err', 'please activate Woocommerce before run integrate demo
                        plugin');
            ?>
            <div class="notice notice-error is-dismissible">
                <p><?php echo $woo_err->get_error_message('woo_err'); ?>
                    <strong><?php echo $woo_activ_error->get_error_message('woo_activ_err'); ?></strong>.</p>

            </div>
            <?php
        }

        /**
         * Add a new integration to WooCommerce.
         */
        public function wc_tab_integration($integrations)
        {
            $integrations[] = 'WC_Tabs_Integration';
            return $integrations;
        }

        /**
         * Rename product data tabs
         * source :https://docs.woocommerce.com/document/editing-product-data-tabs/
         */

        public function wc_tabs_rename($tabs)
        {
            $optIntegrate = new WC_Tabs_Integration;

            $tabs['description']['title'] = esc_html(__($optIntegrate->get_option('desc_tab')));        // Rename the description tab
            $tabs['reviews']['title'] = esc_html(__($optIntegrate->get_option('rev_tab')));            // Rename the reviews tab
            $tabs['additional_information']['title'] = esc_html(__($optIntegrate->get_option('info_tab'))); // Rename the additional information tab

            return $tabs;

        }


    }
endif;

$WC_Tabs = new WC_Tabs(__FILE__);

