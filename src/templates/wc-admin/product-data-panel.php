<?php

/**
 * Template for per-product configuration of WooCommerce Redirects plugin
 *
 * Copyright (c) 2015 Choreo Labs
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! isset( $redirection_type_meta_name ) ) {
	return;
}

$use_default = true;

if ( isset( $redirection_types ) && is_array( $redirection_types ) && ! empty( $selected_redirection_type ) ) {
	foreach ( $redirection_types as $redirection_type ) {
		if ( $selected_redirection_type === $redirection_type['name'] ) {
			$use_default = false;
			break;
		}
	}
}

if ( $use_default ) {
	$selected_redirection_type = '';
}

?>

<div id="wc_redirects_data" class="panel woocommerce_options_panel">
	<div class="options_group">
		<p class="form-field">
			<label for="<?php echo esc_attr( $redirection_type_meta_name ); ?>">
				<?php echo esc_html( __( 'Add-to-cart Redirect', 'wc_redirects' ) ); ?>
			</label>
			<select id="<?php echo esc_attr( $redirection_type_meta_name ); ?>"
			        name="<?php echo esc_attr( $redirection_type_meta_name ); ?>"
			        class="select">
				<option value=""<?php echo $use_default ? ' selected' : ''; ?>><?php
					echo __( 'Default', 'wc_redirects' );
					?></option>
				<?php if ( is_array( $redirection_types ) ): ?>
					<?php foreach ( $redirection_types as $redirection_type ) : ?>
						<?php $is_selected = $selected_redirection_type === $redirection_type['name']; ?>
						<option value="<?php echo esc_attr( $redirection_type['name'] ); ?>"<?php
						echo $is_selected ? ' selected' : '' ?>>
							<?php echo esc_attr( $redirection_type['label'] ); ?>
						</option>
					<?php endforeach; ?>
				<?php endif; ?>
			</select>
		</p>
	</div>
</div>
