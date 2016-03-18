<?php
/**
 * My Programs tab of Wampum
 *
 * @uses  /woocommerce-memberships/includes/frontend/class-wc-memberships-frontend.php
 * @uses  /woocommerce-memberships/templates/myaccount/my-memberships.php
 */

wp_enqueue_style('wampum');

echo '<h2>My Programs</h2>';

$programs = wampum_get_user_programs( get_current_user_id() );

if ( ! $programs ) {
	$text = 'You don\'t have any ' . Wampum_Content_Types::plural_name('wampum_program') . ' yet.';
	$text = apply_filters( 'wampum_account_programs_no_programs_text', $text );
	return "<p>{$text}</p>";
}

// piklist::pre($programs);

echo '<div class="wampum-user-programs flex-cols flex-col-xs-12 flex-col-sm-6">';

	foreach ( $programs as $program ) {

		// piklist::pre($program);

		$image_size = apply_filters('wampum_account_programs_image_size', 'thumbnail');

	    $image = sprintf( '<a href="%s" title="%s">%s</a>',
			get_permalink( $program->ID ),
			the_title_attribute( 'echo=0' ),
			get_the_post_thumbnail( $program->ID, $image_size )
		);
		?>
		<div class="wampum-user-program col">
			<?php if ( $image ) { ?>
				<div class="image"><?php echo $image; ?></div>
			<?php } ?>
			<div class="title"><h3><?php echo $program->post_title ?></h3></div>
			<div class="excerpt"><?php echo $program->post_excerpt ?></div>
		</div>
		<?php
	}

echo '</div>';

do_action('wampum_account_after_programs');
