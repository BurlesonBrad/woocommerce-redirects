<?php

/**
 * Functions to support integration with WooCommerce admin pages
 *
 * Copyright (c) 2015 Choreo Labs
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once( 'woocommerce-redirects-wc.php' );

//---------------------------------------------------------------------------------------------------------------------
//
// Actions
//
//---------------------------------------------------------------------------------------------------------------------

function wc_redirects__wc_admin_process_product_meta( $post_id ) {
	if ( isset( $_POST[ WC_REDIRECTS__REDIRECTION_TYPE_META_NAME ] ) ) {
		$value = $_POST[ WC_REDIRECTS__REDIRECTION_TYPE_META_NAME ];

		$is_valid = false;
		if ( $value === '' ) {
			$is_valid = true;
		} else {
			foreach ( wc_redirects__get_valid_redirection_types() as $registration_type ) {
				if ( $value === $registration_type['name'] ) {
					$is_valid = true;
					break;
				}
			}
		}

		if ( ! $is_valid ) {
			WC_Admin_Meta_Boxes::add_error(
				__( 'The selected Redirection Type is not valid.', 'wc_redirects' ) );

			return;
		}

		if ( $value === '' ) {
			delete_post_meta( $post_id, WC_REDIRECTS__REDIRECTION_TYPE_META_NAME );
		} else {
			update_post_meta( $post_id, WC_REDIRECTS__REDIRECTION_TYPE_META_NAME, $value );
		}
	}
}

function wc_redirects__wc_admin_product_data_panels() {
	global $post;

	/** @noinspection PhpUnusedLocalVariableInspection */
	$selected_redirection_type = get_post_meta( $post->ID, WC_REDIRECTS__REDIRECTION_TYPE_META_NAME, true );

	/** @noinspection PhpUnusedLocalVariableInspection */
	$redirection_types = wc_redirects__get_valid_redirection_types();

	/** @noinspection PhpUnusedLocalVariableInspection */
	$redirection_type_meta_name = WC_REDIRECTS__REDIRECTION_TYPE_META_NAME;

	include WC_REDIRECTS__TEMPLATES_DIR . 'wc-admin/product-data-panel.php';
}

//---------------------------------------------------------------------------------------------------------------------
//
// Filters
//
//---------------------------------------------------------------------------------------------------------------------

function wc_redirects__wc_admin_product_data_tabs( $product_data_tabs ) {
	$product_data_tabs['wc_redirects'] = array(
		'label'  => __( 'Redirects', 'wc_redirects' ),
		'target' => 'wc_redirects_data'
	);

	return $product_data_tabs;
}

//---------------------------------------------------------------------------------------------------------------------
//
// Entry point
//
//---------------------------------------------------------------------------------------------------------------------

function wc_redirects__wc_admin_init_hooks() {
	add_action( 'woocommerce_process_product_meta', 'wc_redirects__wc_admin_process_product_meta' );
	add_action( 'woocommerce_product_data_panels', 'wc_redirects__wc_admin_product_data_panels' );
	add_filter( 'woocommerce_product_data_tabs', 'wc_redirects__wc_admin_product_data_tabs' );
}
