<?php

/**
 * Plugin Name: My Chatbot
 * Plugin URI: https://github.com/danielpowney/my-chatbot
 * Description: An artificial intelligent chatbot for WordPress powered by Dialogflow.
 * Author: Daniel Powney
 * Author URI: https://danielpowney.com
 * Version: 1.1
 * Text Domain: my-chatbot
 * Domain Path: languages
 *
 * My Chatbot is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * any later version.
 *
 * My Chatbot is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Easy Digital Downloads. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package     MYC
 * @author 		Daniel Powney
 * @version		1.0
 *
 */
// Exit if accessed directly.
if ( !defined( 'ABSPATH' ) ) {
    exit;
}

if ( function_exists( 'myc_freemius' ) ) {
    myc_freemius()->set_basename( false, __FILE__ );
} else {
    // DO NOT REMOVE THIS IF, IT IS ESSENTIAL FOR THE `function_exists` CALL ABOVE TO PROPERLY WORK.
    
    if ( !function_exists( 'myc_freemius' ) ) {
        // Create a helper function for easy SDK access.
        function myc_freemius()
        {
            global  $myc_freemius ;
            
            if ( !isset( $myc_freemius ) ) {
                // Include Freemius SDK.
                require_once dirname( __FILE__ ) . '/freemius/start.php';
                $myc_freemius = fs_dynamic_init( array(
                    'id'               => '7068',
                    'slug'             => 'my-chatbot',
                    'premium_slug'     => 'my-chatbot-pro',
                    'type'             => 'plugin',
                    'public_key'       => 'pk_d7153672efc4077f81d47a19b2dcf',
                    'is_premium'       => false,
                    'premium_suffix'   => 'Pro',
                    'has_addons'       => false,
                    'has_paid_plans'   => true,
                    'menu'             => array(
                    'slug'           => 'my-chatbot',
                    'override_exact' => true,
                    'first-path'     => 'index.php?page=myc-getting-started',
                    'contact'        => false,
                    'support'        => false,
                    'parent'         => array(
                    'slug' => 'options-general.php',
                ),
                ),
                    'navigation'       => 'tabs',
                    'is_org_compliant' => true,
                    'anonymous_mode'   => true,
                    'is_live'          => true,
                ) );
            }
            
            return $myc_freemius;
        }
        
        // Init Freemius.
        myc_freemius();
        // Signal that SDK was initiated.
        do_action( 'myc_freemius_loaded' );
        function myc_freemius_settings_url()
        {
            return admin_url( 'options-general.php?page=my-chatbot' );
        }
        
        myc_freemius()->add_filter( 'connect_url', 'myc_freemius_settings_url' );
        myc_freemius()->add_filter( 'after_skip_url', 'myc_freemius_settings_url' );
        myc_freemius()->add_filter( 'after_connect_url', 'myc_freemius_settings_url' );
        myc_freemius()->add_filter( 'after_pending_connect_url', 'myc_freemius_settings_url' );
    }
    
    /**
     * Main My_Chatbot Class.
     *
     * @since 1.4
     */
    final class My_Chatbot
    {
        /**
         * MYC API Object.
         *
         * @var object|EDD_API
         * @since 1.5
         */
        public  $api ;
        /** Singleton *************************************************************/
        /**
         * @var My_Chatbot The one true My_Chatbot
         * @since 1.4
         */
        private static  $instance ;
        /**
         * Used to identify multiple chatbots on the same page...
         */
        public static  $sequence = 0 ;
        /**
         * Main My_Chatbot Instance.
         *
         * Insures that only one instance of My_Chatbot exists in memory at any one
         * time. Also prevents needing to define globals all over the place.
         *
         * @since 0.1
         * @static
         * @staticvar array $instance
         * @uses My_Chatbot::setup_constants() Setup the constants needed.
         * @uses My_Chatbot::includes() Include the required files.
         * @uses My_Chatbot::load_textdomain() load the language files.
         * @see MYC()
         * @return object|My_Chatbot The one true My_Chatbot
         */
        public static function instance()
        {
            
            if ( !isset( self::$instance ) && !self::$instance instanceof My_Chatbot ) {
                self::$instance = new My_Chatbot();
                self::$instance->setup_session();
                self::$instance->setup_constants();
                add_action( 'plugins_loaded', array( self::$instance, 'load_textdomain' ) );
                add_action(
                    'in_plugin_update_message-my-chatbot/my-chatbot.php',
                    array( self::$instance, 'upgrade_notice' ),
                    10,
                    2
                );
                self::$instance->includes();
                self::$instance->api = new MYC_API();
            }
            
            return self::$instance;
        }
        
        /**
         * Throw error on object clone.
         *
         * The whole idea of the singleton design pattern is that there is a single
         * object therefore, we don't want the object to be cloned.
         *
         * @since 1.6
         * @access protected
         * @return void
         */
        public function __clone()
        {
            // Cloning instances of the class is forbidden.
            _doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'my-chatbot' ), '1.6' );
        }
        
        /**
         * Disable unserializing of the class.
         *
         * @since 1.6
         * @access protected
         * @return void
         */
        public function __wakeup()
        {
            // Unserializing instances of the class is forbidden.
            _doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'my-chatbot' ), '1.6' );
        }
        
        /**
         * Setup plugin constants.
         *
         * @access private
         * @since 1.4
         * @return void
         */
        private function setup_constants()
        {
            // Plugin version.
            if ( !defined( 'MYC_VERSION' ) ) {
                define( 'MYC_VERSION', '1.1' );
            }
            // Plugin slug.
            if ( !defined( 'MYC_SLUG' ) ) {
                define( 'MYC_SLUG', 'my-chatbot' );
            }
            // Plugin Folder Path.
            if ( !defined( 'MYC_PLUGIN_DIR' ) ) {
                define( 'MYC_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
            }
            // Plugin Folder URL.
            if ( !defined( 'MYC_PLUGIN_URL' ) ) {
                define( 'MYC_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
            }
            // Plugin Root File.
            if ( !defined( 'MYC_PLUGIN_FILE' ) ) {
                define( 'MYC_PLUGIN_FILE', __FILE__ );
            }
        }
        
        /**
         * Include required files.
         *
         * @access private
         * @since 1.4
         * @return void
         */
        private function includes()
        {
            global  $myc_options ;
            require_once MYC_PLUGIN_DIR . 'includes/admin/settings/register-settings.php';
            $myc_options = myc_get_settings();
            require_once MYC_PLUGIN_DIR . 'includes/actions.php';
            if ( file_exists( MYC_PLUGIN_DIR . 'includes/deprecated-functions.php' ) ) {
                require_once MYC_PLUGIN_DIR . 'includes/deprecated-functions.php';
            }
            require_once MYC_PLUGIN_DIR . 'includes/ajax-functions.php';
            require_once MYC_PLUGIN_DIR . 'includes/api/class-myc-api.php';
            require_once MYC_PLUGIN_DIR . 'includes/template-functions.php';
            require_once MYC_PLUGIN_DIR . 'includes/widgets.php';
            require_once MYC_PLUGIN_DIR . 'includes/misc-functions.php';
            require_once MYC_PLUGIN_DIR . 'includes/shortcodes.php';
            require_once MYC_PLUGIN_DIR . 'includes/scripts.php';
            require_once MYC_PLUGIN_DIR . 'includes/post-meta-box.php';
            // TODO
            //require_once MYC_PLUGIN_DIR . 'includes/gutenberg.php';
            require_once MYC_PLUGIN_DIR . 'includes/rest-api.php';
            
            if ( is_admin() ) {
                require_once MYC_PLUGIN_DIR . 'includes/admin/admin-actions.php';
                require_once MYC_PLUGIN_DIR . 'includes/admin/admin-pages.php';
                require_once MYC_PLUGIN_DIR . 'includes/admin/settings/display-settings.php';
                require_once MYC_PLUGIN_DIR . 'includes/admin/upgrades/upgrade-functions.php';
                require_once MYC_PLUGIN_DIR . 'includes/admin/welcome.php';
            }
            
            require_once MYC_PLUGIN_DIR . 'includes/install.php';
        }
        
        /**
         * Loads the plugin language files.
         *
         * @access public
         * @since 1.4
         * @return void
         */
        public function load_textdomain()
        {
            global  $wp_version ;
            // Set filter for plugin's languages directory.
            $myc_lang_dir = dirname( plugin_basename( MYC_PLUGIN_FILE ) ) . '/languages/';
            $myc_lang_dir = apply_filters( 'myc_languages_directory', $myc_lang_dir );
            // Traditional WordPress plugin locale filter.
            $get_locale = get_locale();
            if ( $wp_version >= 4.7 ) {
                $get_locale = get_user_locale();
            }
            /**
             * Defines the plugin language locale used in AffiliateWP.
             *
             * @var $get_locale The locale to use. Uses get_user_locale()` in WordPress 4.7 or greater,
             *                  otherwise uses `get_locale()`.
             */
            $locale = apply_filters( 'plugin_locale', $get_locale, 'my-chatbot' );
            $mofile = sprintf( '%1$s-%2$s.mo', 'my-chatbot', $locale );
            // Look for wp-content/languages/myc/my-chatbot-{lang}_{country}.mo
            $mofile_global1 = WP_LANG_DIR . '/myc/my-chatbot-' . $locale . '.mo';
            // Look for wp-content/languages/myc/myc-{lang}_{country}.mo
            $mofile_global2 = WP_LANG_DIR . '/myc/myc-' . $locale . '.mo';
            // Look in wp-content/languages/plugins/my-chatbot
            $mofile_global3 = WP_LANG_DIR . '/plugins/my-chatbot/' . $mofile;
            
            if ( file_exists( $mofile_global1 ) ) {
                load_textdomain( 'my-chatbot', $mofile_global1 );
            } elseif ( file_exists( $mofile_global2 ) ) {
                load_textdomain( 'my-chatbot', $mofile_global2 );
            } elseif ( file_exists( $mofile_global3 ) ) {
                load_textdomain( 'my-chatbot', $mofile_global3 );
            } else {
                // Load the default language files.
                load_plugin_textdomain( 'my-chatbot', false, $myc_lang_dir );
            }
        
        }
        
        /**
         * Ensures MYC session cookie exists
         */
        public function setup_session()
        {
            
            if ( !(isset( $_COOKIE['myc_session_id'] ) && strlen( $_COOKIE['myc_session_id'] ) > 0) ) {
                $session_id = md5( uniqid( 'myc-' ) );
                setcookie(
                    'myc_session_id',
                    $session_id,
                    time() + 86400 * 30,
                    '/'
                );
                // 86400 = 1 day
            }
        
        }
        
        /**
         * Displays upgrade notice
         */
        public function upgrade_notice( $data, $response )
        {
            if ( isset( $data['upgrade_notice'] ) ) {
                printf( '<div class="update-message">%s</div>', wpautop( $data['upgrade_notice'] ) );
            }
        }
    
    }
    /**
     * The main function for that returns My_Chatbot
     *
     * The main function responsible for returning the one true My_Chatbot
     * Instance to functions everywhere.
     *
     * Use this function like you would a global variable, except without needing
     * to declare the global.
     *
     * Example: <?php $myc = MYC(); ?>
     *
     * @since 1.4
     * @return object|My_Chatbot The one true My_Chatbot Instance.
     */
    function MYC()
    {
        return My_Chatbot::instance();
    }
    
    // Get MYC Running.
    MYC();
}
