<?php

/**
 * WooCommerce Redirects plugin
 *
 * Plugin Name: WooCommerce Redirects
 * Plugin URI: http://www.github.com/choreolabs/woocommerce-redirects
 * Description: Customise WooCommerce redirects on a per-product basis.
 * Version: 0.1
 * Author: Choreo Labs
 * Author URI: http://www.choreolabs.com
 * License: GPLv2 or later (license.txt)
 *
 * Copyright (c) 2015 Choreo Labs
 */

define( 'WC_REDIRECTS__TEMPLATES_DIR', plugin_dir_path( __FILE__ ) . 'templates/' );

define( 'WC_REDIRECTS__WC_WARNING_SUPPRESSED', 'wc_redirects__wc_warning_suppressed' );

/**
 * Determine whether the user is currently on the Plugins page
 *
 * @return bool value of \c true if on plugins page; \c false otherwise
 */
function wc_redirects__is_user_on_plugins_page() {
	global $pagenow;

	return $pagenow === 'plugins.php';
}

/**
 * Determine whether or not WooCommerce plugin has been activated
 *
 * @return bool value of \c true if WooCommerce is active; \c false otherwise
 */
function wc_redirects__is_woocommerce_active() {
	return in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) );
}

/**
 * Update a boolean-valued user meta field, or read the latest value
 *
 * @param string $meta_name Name of the user meta field to be updated or read
 * @param callable $clear_failed Closure that will be executed if the flag cannot be cleared
 * @param callable $set_failed Closure that will be executed if the flag cannot be set
 * @param callable $invalid_value Closure that will be executed if the query parameter contains an invalid value
 *
 * @return bool latest known value of the relevant field
 */
function wc_redirects__update_or_read_boolean_user_meta( $meta_name, $clear_failed, $set_failed, $invalid_value ) {
	if ( isset( $_GET[ $meta_name ] ) ) {
		if ( '0' === $_GET[ $meta_name ] ) {
			if ( false === update_user_meta( get_current_user_id(), $meta_name, 'false' ) ) {
				$clear_failed();
			}

			return false;
		} else if ( '1' === $_GET[ $meta_name ] ) {
			if ( false === update_user_meta( get_current_user_id(), $meta_name, 'true' ) ) {
				$set_failed();
			}

			return true;
		} else {
			$invalid_value( "Invalid value provided for '$meta_name' query parameter; expected '0' or '1'" );
		}
	}

	return 'true' === get_user_meta( get_current_user_id(), $meta_name, true );
}

function wc_redirects__show_wc_warning() {
	echo '<div class="error">';
	echo '<p>To enable the functionality of the WooCommerce Redirects plugin, you will need to install'
	     . ' and activate WooCommerce.</p>';
	echo '<p><a href="?' . WC_REDIRECTS__WC_WARNING_SUPPRESSED . '=1">Dismiss</a></p>';
	echo '</div>';
}

function wc_redirects__show_wc_warning_suppression_error() {
	echo '<div class="error">';
	echo '<p>Failed to update user meta data for WooCommerce Redirects. Warnings from this plugin may continue to be displayed.</p>';
	echo '</div>';
}

function wc_redirects__show_wc_warning_reset_error() {
	echo '<div class="error">';
	echo '<p>Failed to update user meta data for WooCommerce Redirects. Warnings from this plugin may continue to be suppressed.</p>';
	echo '</div>';
}

function wc_redirects__maybe_raise_woocommerce_warning() {
	if ( ! wc_redirects__is_user_on_plugins_page() ) {
		return;
	}

	$wc_warning_suppressed = wc_redirects__update_or_read_boolean_user_meta(
		WC_REDIRECTS__WC_WARNING_SUPPRESSED,
		function () {
			add_action( 'admin_notices', 'wc_redirects__show_wc_warning_reset_error' );
		},
		function () {
			add_action( 'admin_notices', 'wc_redirects__show_wc_warning_suppression_error' );
		},
		function ( $error_message ) {
			error_log( $error_message );
		}
	);

	if ( ! wc_redirects__is_woocommerce_active() && ! $wc_warning_suppressed ) {
		add_action( 'admin_notices', 'wc_redirects__show_wc_warning' );
	}
}

add_action( 'plugins_loaded', 'wc_redirects__maybe_raise_woocommerce_warning' );

function wc_redirects__plugins_loaded() {
	if ( wc_redirects__is_woocommerce_active() ) {
		require_once( 'includes/woocommerce-redirects-wc.php' );
		wc_redirects__wc_init_hooks();
		if ( is_admin() ) {
			require_once( 'includes/woocommerce-redirects-wc-admin.php' );
			wc_redirects__wc_admin_init_hooks();
		}
	}
}

add_action( 'plugins_loaded', 'wc_redirects__plugins_loaded' );
