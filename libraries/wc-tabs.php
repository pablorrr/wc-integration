<?php

namespace Main;


use WC_Tabs_Integration;
use WP_Error;

define('WC_TAB_PLUGIN_PATH', plugin_dir_path(__FILE__));
define('MY_PLUGIN_SLUG', 'wc-settings');

if (!class_exists('WC_Tabs') && !class_exists('ABS_WC_Tabs')) :
    interface  important_functions
    {
        function init();

        function wc_tab_admin_notice();
    }

    // refers to Proxy Pattern
    interface _options
    {
        function loop_columns();

        function products_count_per_page();

        function _product_subcategories();

        function wc_tabs_rename();
    }


    // final class WC_Tabs  implements test;
    final class WC_Tabs implements _options,important_functions
    {
        //Singleton on WP Plugin implementation inspired
        // with https://gist.github.com/goncaloneves/e0f07a8db17b06c2f968

        private static $_instance;
        private $tabsIntegrate;



        public static function instance(): WC_Tabs
        {
            if (is_null(self::$_instance)) {
                self::$_instance = new self;
            }
            return self::$_instance;
        }

        /**
         * Constructor.
         * @param $Tabs_Integration
         */
        public function __construct()
        {
            $this->actions();
        }


        /**
         * Initialize the plugin when all plugins are loaded.
         * @param $WC_Tabs_Integration
         */

        private function actions()
        {
            add_action('plugins_loaded', array($this, 'init'));

        }

        /**
         * Init
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

            add_filter('woocommerce_product_tabs', array($this, 'wc_tabs_rename'), 98);
            add_filter('loop_shop_columns', array($this, 'loop_columns'), 20, 1);
            add_filter('loop_shop_per_page', array($this, 'products_count_per_page'), 30, 1);
            add_action('wp_head', array($this, 'add_cat_css'), 49);
            add_action('woocommerce_before_shop_loop', array($this, '_product_subcategories'), 50);

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
         * @param $integrations
         */
        public function wc_tab_integration($integrations)
        {
            $integrations[] = 'WC_Tabs_Integration';
            return $integrations;
        }


        public function _getObj()
        {
            if ($this->tabsIntegrate == null) {
                $this->tabsIntegrate = new WC_Tabs_Integration();
            }
        }

        /**
         * Rename product data tabs
         * source :https://docs.woocommerce.com/document/editing-product-data-tabs/
         */

        public function wc_tabs_rename()
        {
            //proxy
            $this->_getObj();
            return $this->tabsIntegrate->wc_tabs_rename();
        }


        public function loop_columns()
        {  //proxy
            $this->_getObj();
            return $this->tabsIntegrate->loop_columns();
        }


        public function products_count_per_page()
        {
            //proxy
            $this->_getObj();
            return $this->tabsIntegrate->products_count_per_page();
        }


        /**
         * Display category image on shop page
         * https://code.tutsplus.com/tutorials/display-woocommerce-categories-subcategories-and-products-in-separate-lists--cms-25479
         */


        public function _product_subcategories()
        {
            //proxy
            $this->_getObj();
            return $this->tabsIntegrate->_product_subcategories();
        }

        public function add_cat_css()
        {
            if (is_shop()) {
                echo '<style>
                            ul.product-cats > li.category:hover > a > img:hover {
								-moz-transform: scale(1.2) rotate(360deg);
								-webkit-transform: scale(1.2) rotate(360deg);
								-o-transform: scale(1.2) rotate(360deg);
								-ms-transform: scale(1.2) rotate(360deg);
								transform: scale(1.2) rotate(340deg);
								}
                            li.category {
                              display: inline-block;
                              width: 100px;
                              height: 100px;
                              padding: 5px;
                              
                             }
                    </style>';

            }
        }
    }
endif;
