<?php
/**
 * WC Tabs Settings Integration.
 *
 * @package  WC_Tabs_Integration
 * @category Integration
 * @author   Pablozzz
 */

//TODO: ADD VALIDATE AND ERROS MESSAGE!!!!

if (!class_exists('WC_Tabs_Integration')) :

    class WC_Tabs_Integration extends WC_Integration
    {
        /**
         * @var string
         */
        private $desc_tab;
        /**
         * @var string
         */
        private $rev_tab;
        /**
         * @var string
         */
        private $info_tab;
        /**
         * @var string
         */
        private $col_count;
        /**
         * @var string
         */
        private $prod_count;

        /**
         * @var string
         */
        private $cat_name;
        /**
         * @var string
         */
        private $promo_label;


        /**
         * Init and hook in the integration.
         */
        public function __construct()
        {
            global $woocommerce;

            $this->id = 'wc-tabs-integration';
            $this->method_title = __('WC Tabs Settings', 'wc-tabs');
            $this->method_description = __('Customize your Woocommerce', 'wc-tabs');

            // Load the settings.
            $this->init_form_fields();
            $this->init_settings();

            // Define user set variables.
            $this->desc_tab = $this->get_option('desc_tab');
            $this->rev_tab = $this->get_option('rev_tab');
            $this->info_tab = $this->get_option('info_tab');
            $this->col_count = $this->get_option('col_count');
            $this->prod_count = $this->get_option('prod_count');
            $this->cat_name = $this->get_option('cat_name');
            $this->promo_label = $this->get_option('promo_label');


            // Actions.
            add_action('woocommerce_update_options_integration_' . $this->id, array($this, 'process_admin_options'));
            // Filters.
            add_filter('woocommerce_settings_api_sanitized_fields_' . $this->id, array($this, 'sanitize_settings'));

        }

        /**
         * Get categories table from DB to print as option value
         */

        public function get_categories()
        {
            global $wpdb;
            $prod_cat = $wpdb->get_results(
                'SELECT wp_terms.name 
                    FROM wp_terms inner join wp_term_taxonomy on wp_terms.term_id=wp_term_taxonomy.term_id
                    WHERE wp_term_taxonomy.taxonomy = "product_cat"', ARRAY_N);

            //flatting multidimensional array
            $prod_cat = call_user_func_array('array_merge', $prod_cat);

            //convert keys array as key name same like value
            if (is_array($prod_cat) && !empty($prod_cat)) {

                return array_combine($prod_cat,$prod_cat);
            } else
                return ['EmptyCat'];
        }




        /**
         * Initialize integration settings form fields.
         *
         * @return void
         */
        public function init_form_fields()
        {
            $this->form_fields = array(
                'desc_tab' => array(
                    'title' => __('Description in product tab', 'wc-tabs'),
                    'type' => 'text',
                    'description' => __('Type your custom name for description of product tab', 'wc-tabs'),
                    'desc_tip' => true,
                    'default' => 'title'
                ),
                'rev_tab' => array(
                    'title' => __('Review in product tab', 'wc-tabs'),
                    'type' => 'text',
                    'description' => __('Type your custom name for review of product tab', 'wc-tabs'),
                    'desc_tip' => true,
                    'default' => 'title'
                ),
                'info_tab' => array(
                    'title' => __('Additional Info in product tab', 'wc-tabs'),
                    'type' => 'text',
                    'description' => __('Type your custom name of product tab for additional info', 'wc-tabs'),
                    'desc_tip' => true,
                    'default' => 'title'//doesnt work
                ),
                'col_count' => array(
                    'title' => __('Columns count', 'wc-tabs'),
                    'type' => 'text',
                    'description' => __('Type number of columns per page', 'wc-tabs'),
                    'desc_tip' => true,
                    'default' => '3'
                ),

                'prod_count' => array(
                    'title' => __('Products count per page', 'wc-tabs'),
                    'type' => 'number',
                    'description' => __('Type number of products per page', 'wc-tabs'),
                    'desc_tip' => true,
                    'default' => '2'
                ),

                //https://www.skyverge.com/blog/add-custom-options-to-woocommerce-settings/

                'cat_name' => array(
                    'title' => __('Select category name', 'wc-tabs'),
                    'type' => 'multiselect',
                    'options' => $this->get_categories(),
                    'description' => __('Press ctrl and click on category which you want to show on Shop Page', 'wc-tabs'),
                    'desc_tip' => true,

                ),


                'customize_button' => array(
                    'title' => __('Go to shop page', 'wc-tabs'),
                    'type' => 'button',
                    'custom_attributes' => array(
                        'onclick' => "location.href='" . esc_url(wc_get_page_permalink('shop')) . "'",

                    ),
                    'description' => __('Click to go Shop Page to enter and check single product page tabs names', 'wc-tabs'),
                    'desc_tip' => true,
                )


            );

        }


        /**
         * Generate Button HTML.
         *
         * /*
         * <input type="number" name="age" id="age" min="1" max="10" step="2">
         *
         */
        public function generate_button_html($key, $data)
        {
            $field = $this->plugin_id . $this->id . '_' . $key;
            $defaults = array(
                'class' => 'button-secondary',
                'css' => '',
                'custom_attributes' => array(),
                'desc_tip' => false,
                'description' => 'test',
                'title' => 'test',
            );

            $data = wp_parse_args($data, $defaults);

            ob_start();
            ?>
            <tr valign="top">

                <th scope="row" class="titledesc">
                    <label for="<?php echo esc_attr($field); ?>"><?php echo wp_kses_post($data['title']); ?></label>
                    <?php echo $this->get_tooltip_html($data); ?>
                </th>
                <td class="forminp">
                    <fieldset>
                        <legend class="screen-reader-text"><span><?php echo wp_kses_post($data['title']); ?></span>
                        </legend>
                        <button class="<?php echo esc_attr($data['class']); ?>" type="button"
                                name="<?php echo esc_attr($field); ?>" id="<?php echo esc_attr($field); ?>"
                                style="<?php echo esc_attr($data['css']); ?>" <?php echo $this->get_custom_attribute_html($data); ?>><?php echo wp_kses_post($data['title']); ?></button>
                        <?php echo $this->get_description_html($data); ?>
                    </fieldset>
                </td>
            </tr>
            <?php
            return ob_get_clean();
        }

        /**
         * Get publish products number to use in sanitize.
         */

        public function _get_publish_prod()
        {//source:https://gist.github.com/kloon/4218605
            $count_posts = wp_count_posts('product');
            return $count_posts->publish;
        }


        /**
         * Santize our settings
         * @see process_admin_options()
         */
        public function sanitize_settings($settings)
        {
            // We're just going to make the api key all upper case characters since that's how our imaginary API works
            if (isset($settings) &&
                isset($settings['desc_tab']) &&
                isset($settings['rev_tab']) &&
                isset($settings['info_tab']) &&
                isset($settings['col_count']) &&
                isset($settings['prod_count']) &&
                isset($settings['cat_name'])

            ) {
                $settings['desc_tab'] = strtolower($settings['desc_tab']);
                $settings['rev_tab'] = strtolower($settings['rev_tab']);
                $settings['info_tab'] = strtolower($settings['info_tab']);
                $settings['col_count'] = (int)($settings['col_count']);
                $settings['prod_count'] = $settings['prod_count'] <= $this->_get_publish_prod() ? (int)$settings['prod_count'] : 1;
                //$settings['cat_name'] = $settings['cat_name'];

            }
            return $settings;
        }
    }
endif;


?>