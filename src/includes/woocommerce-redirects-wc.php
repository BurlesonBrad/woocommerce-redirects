<?php

/**
 * Functions to support integration with WooCommerce frontend and admin pages
 *
 * Copyright (c) 2015 Choreo Labs
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'WC_REDIRECTS__REDIRECTION_TYPE_META_NAME', '_wc_redirects_add_to_cart_redirection_type' );

//---------------------------------------------------------------------------------------------------------------------
//
// Helper functions
//
//---------------------------------------------------------------------------------------------------------------------

function wc_redirects__get_shop_page_url() {
	return get_permalink( wc_get_page_id( 'shop' ) );
}

function wc_redirects__get_home_page_url() {
	return get_site_url();
}

function wc_redirects__get_checkout_page_url() {
	global $woocommerce;

	return $woocommerce->cart->get_checkout_url();
}

function wc_redirects__get_valid_redirection_types() {
	static $redirection_types;

	if ( ! is_array( $redirection_types ) ) {
		$redirection_types = array(
			array(
				'name'     => 'home_page',
				'label'    => __( 'Home Page', 'wc_redirects' ),
				'callback' => 'wc_redirects__get_home_page_url'
			),
			array(
				'name'     => 'shop_page',
				'label'    => __( 'Shop Page', 'wc_redirects' ),
				'callback' => 'wc_redirects__get_shop_page_url'
			),
			array(
				'name'     => 'checkout_page',
				'label'    => __( 'Checkout Page', 'wc_redirects' ),
				'callback' => 'wc_redirects__get_checkout_page_url'
			)
		);
	}

	return $redirection_types;
}

//---------------------------------------------------------------------------------------------------------------------
//
// Filters
//
//---------------------------------------------------------------------------------------------------------------------

function wc_redirects__add_to_cart_redirect( $url ) {
	static $cached_result = null;
	if ( isset( $cached_result ) ) {
		return $cached_result;
	}

	if ( ! isset( $_REQUEST['add-to-cart'] ) ) {
		return $url;
	}

	$product_id = apply_filters( 'woocommerce_add_to_cart_product_id', absint( $_REQUEST['add-to-cart'] ) );

	$selected_redirection_type = get_post_meta( $product_id, WC_REDIRECTS__REDIRECTION_TYPE_META_NAME, true );
	if ( ! empty( $selected_redirection_type ) ) {
		foreach ( wc_redirects__get_valid_redirection_types() as $redirection_type ) {
			if ( $redirection_type['name'] === $selected_redirection_type ) {
				$result = call_user_func( $redirection_type['callback'] );
				if ( $result === false ) {
					wc_add_notice( "Item added to cart, but redirection " . "to ${redirection_type['label']} failed." );
				} else {
					$url = $result;
				}
				break;
			}
		}
	}

	return $cached_result = $url;
}

//---------------------------------------------------------------------------------------------------------------------
//
// Entry point
//
//---------------------------------------------------------------------------------------------------------------------

function wc_redirects__wc_init_hooks() {
	add_filter( 'woocommerce_add_to_cart_redirect', 'wc_redirects__add_to_cart_redirect' );
}
