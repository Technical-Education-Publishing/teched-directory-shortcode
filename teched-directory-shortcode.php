<?php
/**
 * Plugin Name: Directory Shortcode
 * Plugin URI: https://github.com/Technical-Education-Publishing/teched-directory-shortcode
 * Description: Add directory listings via a shortcode
 * Version: 1.0.0
 * Text Domain: teched-directory-shortcode
 * Author: Real Big Marketing
 * Author URI: https://realbigmarketing.com
 * Contributors: joelyoder
 * GitHub Plugin URI: realbig/teched-directory-shortcode
 * GitHub Branch: master
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'TechEd_Directory_Shortcode' ) ) {

    /**
     * Main TechEd_Directory_Shortcode class
     *
     * @since      1.0.0
     */
    final class TechEd_Directory_Shortcode {
        
        /**
         * @var          array $plugin_data Holds Plugin Header Info
         * @since        1.0.0
         */
        public $plugin_data;
        
        /**
         * @var          array $admin_errors Stores all our Admin Errors to fire at once
         * @since        1.0.0
         */
        private $admin_errors = array();

        /**
         * Get active instance
         *
         * @access     public
         * @since      1.0.0
         * @return     object self::$instance The one true TechEd_Directory_Shortcode
         */
        public static function instance() {
            
            static $instance = null;
            
            if ( null === $instance ) {
                $instance = new static();
            }
            
            return $instance;

        }
        
        protected function __construct() {
            
            $this->setup_constants();
            $this->load_textdomain();
            
            if ( version_compare( get_bloginfo( 'version' ), '4.4' ) < 0 ) {
                
                $this->admin_errors[] = sprintf( _x( '%s requires v%s of %sWordPress%s or higher to be installed!', 'First string is the plugin name, followed by the required WordPress version and then the anchor tag for a link to the Update screen.', 'teched-directory-shortcode' ), '<strong>' . $this->plugin_data['Name'] . '</strong>', '4.4', '<a href="' . admin_url( 'update-core.php' ) . '"><strong>', '</strong></a>' );
                
                if ( ! has_action( 'admin_notices', array( $this, 'admin_errors' ) ) ) {
                    add_action( 'admin_notices', array( $this, 'admin_errors' ) );
                }
                
                return false;
                
            }
            
            $this->require_necessities();
            
            // Register our CSS/JS for the whole plugin
            add_action( 'init', array( $this, 'register_scripts' ) );
            
        }

        /**
         * Setup plugin constants
         *
         * @access     private
         * @since      1.0.0
         * @return     void
         */
        private function setup_constants() {
            
            // WP Loads things so weird. I really want this function.
            if ( ! function_exists( 'get_plugin_data' ) ) {
                require_once ABSPATH . '/wp-admin/includes/plugin.php';
            }
            
            // Only call this once, accessible always
            $this->plugin_data = get_plugin_data( __FILE__ );

            if ( ! defined( 'TechEd_Directory_Shortcode_VER' ) ) {
                // Plugin version
                define( 'TechEd_Directory_Shortcode_VER', $this->plugin_data['Version'] );
            }

            if ( ! defined( 'TechEd_Directory_Shortcode_DIR' ) ) {
                // Plugin path
                define( 'TechEd_Directory_Shortcode_DIR', trailingslashit( plugin_dir_path( __FILE__ ) ) );
            }

            if ( ! defined( 'TechEd_Directory_Shortcode_URL' ) ) {
                // Plugin URL
                define( 'TechEd_Directory_Shortcode_URL', trailingslashit( plugin_dir_url( __FILE__ ) ) );
            }
            
            if ( ! defined( 'TechEd_Directory_Shortcode_FILE' ) ) {
                // Plugin File
                define( 'TechEd_Directory_Shortcode_FILE', __FILE__ );
            }

        }

        /**
         * Internationalization
         *
         * @access     private 
         * @since      1.0.0
         * @return     void
         */
        private function load_textdomain() {

            // Set filter for language directory
            $lang_dir = trailingslashit( TechEd_Directory_Shortcode_DIR ) . 'languages/';
            $lang_dir = apply_filters( 'teched_directory_shortcode_languages_directory', $lang_dir );

            // Traditional WordPress plugin locale filter
            $locale = apply_filters( 'plugin_locale', get_locale(), 'teched-directory-shortcode' );
            $mofile = sprintf( '%1$s-%2$s.mo', 'teched-directory-shortcode', $locale );

            // Setup paths to current locale file
            $mofile_local   = $lang_dir . $mofile;
            $mofile_global  = trailingslashit( WP_LANG_DIR ) . 'teched-directory-shortcode/' . $mofile;

            if ( file_exists( $mofile_global ) ) {
                // Look in global /wp-content/languages/teched-directory-shortcode/ folder
                // This way translations can be overridden via the Theme/Child Theme
                load_textdomain( 'teched-directory-shortcode', $mofile_global );
            }
            else if ( file_exists( $mofile_local ) ) {
                // Look in local /wp-content/plugins/teched-directory-shortcode/languages/ folder
                load_textdomain( 'teched-directory-shortcode', $mofile_local );
            }
            else {
                // Load the default language files
                load_plugin_textdomain( 'teched-directory-shortcode', false, $lang_dir );
            }

        }
        
        /**
         * Include different aspects of the Plugin
         * 
         * @access     private
         * @since      1.0.0
         * @return     void
         */
        private function require_necessities() {
            
        }
        
        /**
         * Show admin errors.
         * 
         * @access     public
         * @since      1.0.0
         * @return     HTML
         */
        public function admin_errors() {
            ?>
            <div class="error">
                <?php foreach ( $this->admin_errors as $notice ) : ?>
                    <p>
                        <?php echo $notice; ?>
                    </p>
                <?php endforeach; ?>
            </div>
            <?php
        }
        
        /**
         * Register our CSS/JS to use later
         * 
         * @access     public
         * @since      1.0.0
         * @return     void
         */
        public function register_scripts() {
            
            wp_register_style(
                'teched-directory-shortcode',
                TechEd_Directory_Shortcode_URL . 'dist/assets/css/app.css',
                null,
                defined( 'WP_DEBUG' ) && WP_DEBUG ? time() : TechEd_Directory_Shortcode_VER
            );
            
            wp_register_script(
                'teched-directory-shortcode',
                TechEd_Directory_Shortcode_URL . 'dist/assets/js/app.js',
                array( 'jquery' ),
                defined( 'WP_DEBUG' ) && WP_DEBUG ? time() : TechEd_Directory_Shortcode_VER,
                true
            );
            
            wp_localize_script( 
                'teched-directory-shortcode',
                'techEdDirectoryShortcode',
                apply_filters( 'teched_directory_shortcode_localize_script', array() )
            );
            
            wp_register_style(
                'teched-directory-shortcode-admin',
                TechEd_Directory_Shortcode_URL . 'dist/assets/css/admin.css',
                null,
                defined( 'WP_DEBUG' ) && WP_DEBUG ? time() : TechEd_Directory_Shortcode_VER
            );
            
            wp_register_script(
                'teched-directory-shortcode-admin',
                TechEd_Directory_Shortcode_URL . 'dist/assets/js/admin.js',
                array( 'jquery' ),
                defined( 'WP_DEBUG' ) && WP_DEBUG ? time() : TechEd_Directory_Shortcode_VER,
                true
            );
            
            wp_localize_script( 
                'teched-directory-shortcode-admin',
                'techEdDirectoryShortcode',
                apply_filters( 'teched_directory_shortcode_localize_admin_script', array() )
            );
            
        }
        
    }
    
} // End Class Exists Check

/**
 * The main function responsible for returning the one true TechEd_Directory_Shortcode
 * instance to functions everywhere
 *
 * @since      1.0.0
 * @return     \TechEd_Directory_Shortcode The one true TechEd_Directory_Shortcode
 */
add_action( 'plugins_loaded', 'teched_directory_shortcode_load' );
function teched_directory_shortcode_load() {

    require_once trailingslashit( __DIR__ ) . 'core/teched-directory-shortcode-functions.php';
    TECHEDDIRECTORYSHORTCODE();

}