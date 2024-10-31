<div class="myc-content-overlay myc-content-overlay-toggle <?php echo $class_names; ?>">
	<div class="myc-content-overlay-header">
		<span class="myc-content-overlay-header-text"><?php echo $overlay_header_text; ?></span>
		<span class="myc-icon-toggle-up" title="<?php _e( 'Open', 'my-chatbot' ); ?>" <?php if ( $is_overlay_open == true) { echo 'style="display: none"'; } ?>></span>
		<span class="myc-icon-toggle-down" title="<?php _e( 'Close', 'my-chatbot' ); ?>" <?php if ( $is_overlay_open == false) { echo 'style="display: none"'; } ?>></span>
	</div>
	<?php if ( strlen( $overlay_powered_by_text ) > 0 ) {
		?>
		<div class="myc-content-overlay-powered-by" <?php if ( $is_overlay_open == false) { echo 'style="display: none"'; } ?>><?php echo $overlay_powered_by_text; ?></div>
		<?php
	} ?>
	<div class="myc-content-overlay-container" <?php if ( $is_overlay_open == false) { echo 'style="display: none"'; } ?>>
		<?php echo do_shortcode( '[my_chatbot]' ); ?>
	</div>
	<div class="myc-content-overlay-bottom" <?php if ( $is_overlay_open == false) { echo 'style="display: none"'; } ?>>
		<?php do_action( 'myc_content_overlay_bottom' ); ?>
		<div class="myc-content-overlay-left-icons">
			<span class="myc-font-size myc-icon-font-size" title="<?php _e( 'Change font size', 'my-chatot' ); ?>"></span>
			<span class="myc-clear myc-icon-trash" title="<?php _e( 'Clear conversation history', 'my-chatot' ); ?>"></span>
		</div>
	</div>
</div>
