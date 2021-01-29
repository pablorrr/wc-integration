<?php
/**
 * WC Tabs Settings Integration.
 *
 * @package  WC_Tabs_Integration
 * @category Integration
 * @author   Pablozzz
 */

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
         * Init and hook in the integration.
         */
        public function __construct()
        {
            global $woocommerce;

            $this->id = 'wc-tabs-integration';
            $this->method_title = __('WC Tabs Settings', 'wc-tabs');
            $this->method_description = __('Customize tabs names on single product page', 'wc-tabs');

            // Load the settings.
            $this->init_form_fields();
            $this->init_settings();

            // Define user set variables.
            $this->desc_tab = $this->get_option('desc_tab');
            $this->rev_tab = $this->get_option('rev_tab');
            $this->info_tab = $this->get_option('info_tab');
            // Actions.
            add_action('woocommerce_update_options_integration_' . $this->id, array($this, 'process_admin_options'));
            // Filters.
            add_filter('woocommerce_settings_api_sanitized_fields_' . $this->id, array($this, 'sanitize_settings'));
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
                    'title' => __('Description', 'wc-tabs'),
                    'type' => 'text',
                    'description' => __('Type your custom name for description of product', 'wc-tabs'),
                    'desc_tip' => true,
                    'default' => ''
                ),
                'rev_tab' => array(
                    'title' => __(' Review ', 'wc-tabs'),
                    'type' => 'text',
                    'description' => __('Type your custom name for review of product', 'wc-tabs'),
                    'desc_tip' => true,
                    'default' => ''
                ),
                'info_tab' => array(
                    'title' => __('Additional Info', 'wc-tabs'),
                    'type' => 'text',
                    'description' => __('Type your custom name for additional info of product', 'wc-tabs'),
                    'desc_tip' => true,
                    'default' => ''
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
                <?php
                echo $this->get_option('desc_tab');
                echo $this->get_option('rev_tab');
                echo $this->get_option('info_tab');?>
            </tr>
            <?php
            return ob_get_clean();
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
                isset($settings['info_tab'])) {
                $settings['desc_tab'] = strtolower($settings['desc_tab']);
                $settings['rev_tab'] = strtolower($settings['rev_tab']);
                $settings['info_tab'] = strtolower($settings['info_tab']);
            }
            return $settings;
        }


        /**
         * Validate the API key
         * @see validate_settings_fields()
         */
        public function validate_tab_name_field($key)
        {
            // get the posted value
            $value = $_POST[$this->plugin_id . $this->id . '_' . $key];

            if (isset($value) &&
                20 < strlen($value)) {
                $this->errors[] = $key;
            }
            return $value;
        }


        /**
         * Display errors by overriding the display_errors() method
         * @see display_errors()
         */
        public function display_errors()
        {

            // loop through each error and display it
            foreach ($this->errors as $key => $value) {
                ?>
                <div class="error">
                    <p><?php _e('Looks like you made a mistake with the ' . $value . ' field. Make sure it isn&apos;t longer than 20 characters', 'wc-tabs'); ?></p>
                </div>
                <?php
            }
        }


    }

endif;