<?php
/*
  Plugin Name:  Comparatore Prezzi WooCommerce - Generatore Data Feed XML e CSV per WooCommerce
  Plugin URI:   http://www.rsaweb.com
  Description:  Plugin Wordpress generatore di Data Feed in formato XML e CSV per comparatori di prezzi e marketplace come Trovaprezzi
  Version:      1.0
  Author:       wemiura
  Author URI:   https://profiles.wordpress.org/wemiura
  License:     GPL2
  License URI:
  Text Domain: cpw
  Domain Path: /languages
 */


function check_required_plugin_cpw_free() {
	
	if ( ( is_plugin_active( 'comparatore-prezzi-woocommerce/comparatore-prezzi-woocommerce.php' ) ) ) {
		wp_die( 'Disattiva la Versione Premium di Comparatore Prezzi WooCommerce prima di utilizzare questa versione FREE' );
	}
	
	if ( ! ( is_plugin_active( 'woocommerce/woocommerce.php' ) ) ) {
		wp_die( 'Questo Plugin richiede che sia installato ed attivato Woocommerce <a href="https://wordpress.org/plugins/woocommerce/" target="_blank" />https://wordpress.org/plugins/woocommerce/</a>' );
	}
	
}

register_activation_hook( __FILE__, 'check_required_plugin_cpw_free' );

include_once( 'admin/cpw-admin-brand.php' );
include_once( 'admin/cpw-admin-functions.php' );
include_once( 'admin/cpw-admin-menu.php' );
include_once( 'admin/cpw-admin-ajax.php' );
include_once( 'admin/cpw-admin-trovaprezzi.php' );

include_once( 'public/csv/cpw-public-csv.php' );

