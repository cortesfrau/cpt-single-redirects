<?php

// If uninstall not called from WordPress, then exit
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) exit;

// Delete options
$options = [
  'cpt_single_redirects',
];
foreach ( $options as $option ) {
  delete_option( 'cpt_single_redirects' );
}
