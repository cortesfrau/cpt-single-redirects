<?php

// Security
defined( 'ABSPATH' ) OR exit;

/**
 * Plugin Name: CPT Single Redirects
 * Text Domain: cpt_single_redirects
 * Description: This plugin allows the redirection of CPT Single Templates.
 * Version: 1.0.0
 * Author: Lluís Cortès
 * Author URI: https://lluiscortes.com
 */


// Activation, Deactivation, Uninstall
register_uninstall_hook( __FILE__, ['CPT_Single_Redirects', 'uninstall'] );


// Plugin's class
class CPT_Single_Redirects {


  // Construct
  public function __construct() {

    // Admin menu
    add_action('admin_menu', [$this, 'settings_page'] );


    // Register settings
    add_action( 'admin_init', [$this, 'register_settings'] );

    // Template redirection
    add_action( 'template_redirect', [$this, 'template_redirection'] );

  }


  // Plugin Unistall
  public static function uninstall() {
    if ( ! current_user_can( 'activate_plugins' ) ) {
      return;
    }

    check_admin_referer( 'bulk-plugins' );

    if ( __FILE__ != WP_UNINSTALL_PLUGIN ) {
      return;
    }

    // Delete Options
    delete_option( 'cpt_single_redirects' );
  }


  // Admin menu
  public function settings_page() {
    add_submenu_page(
      'options-general.php',
      'CPT Single Redirects',
      'CPT Single Redirects',
      'administrator',
      __FILE__,
      [$this, 'settings_content']
    );
  }


  // Register settings
  public function register_settings() {
    register_setting( 'cpt-single-redirects-settings', 'cpt_single_redirects' );
  }


  // Get Settings
  public function get_settings() {
    return get_option( 'cpt_single_redirects' );
  }


  // Get CTP Objects
  public function get_cpt_objects() {

    // Registered custom post types
    $args = [
      '_builtin' => false,
    ];
    $custom_post_types = get_post_types( $args );

    // CPT Objects
    $cpt_objects = [];
    foreach ( $custom_post_types as $slug ) {

     $cpt_objects[] = get_post_type_object($slug);

    }
    return $cpt_objects;
  }


  // Settings page content
  public function settings_content() {

    // Settings Data
    $cpt_single_redirects = $this->get_settings();

    ?>

    <div class="wrap">
      <h1>CPT Single Redirects</h1>
      <p><?php echo __( 'Here you can set up the desired redirection for each custom post type single template.', 'cpt_single_redirects' ); ?></p>

      <form method="post" action="options.php">

        <?php settings_fields( 'cpt-single-redirects-settings' ); ?>
        <?php do_settings_sections( 'cpt-single-redirects-settings' ); ?>

        <table>

          <?php foreach ( $this->get_cpt_objects() as $cpt ) {

            // Setting variables
            $cpt_label          = $cpt->label;
            $cpt_slug           = $cpt->name;
            $redirection_value  = empty( $cpt_single_redirects[$cpt_slug] ) ? '' : $cpt_single_redirects[$cpt_slug];

            ?>

            <p>
              <label for="<?php echo $cpt_slug . '-redirect'; ?>"><?php echo $cpt_label; ?></label>
              <input type="text" name="cpt_single_redirects[<?php echo  $cpt_slug; ?>]" id="<?php echo $cpt_slug . '-redirect'; ?>" value="<?php echo $redirection_value; ?>">
            </p>

          <?php } ?>

        <?php submit_button(); ?>

      </form>
    </div>

  <?php }


  // Template redirection
  public function template_redirection() {

    // Settings Data
    $cpt_single_redirects = $this->get_settings();

    foreach ( $cpt_single_redirects as $cpt_slug => $redirection ) {
      if ( is_singular( $cpt_slug ) ) {
        wp_redirect( $redirection, 301 );
      }
    }
    return;
  }

}


// Instantiation
$cpt_single_redirects = new CPT_Single_Redirects();


