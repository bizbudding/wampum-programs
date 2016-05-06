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
	$text = 'You don\'t have access to any ' . Wampum()->content->plural_name('wampum_program') . ' yet.';
	$text = apply_filters( 'wampum_account_programs_no_programs_text', $text );
	echo "<p>{$text}</p>";
} else {

	echo '<ul class="account-items" style="margin-left:0;">';

		foreach ( $programs as $program ) {

			$image_size = apply_filters('wampum_account_programs_image_size', 'thumbnail');

			$image = '';
			if ( has_post_thumbnail( $program->ID ) ) {
			    $image = sprintf( '<a href="%s" title="%s">%s</a>',
					get_permalink( $program->ID ),
					the_title_attribute( 'echo=0' ),
					get_the_post_thumbnail( $program->ID, $image_size )
				);
			}
			echo '<li class="account-item">';
				echo '<span class="item-col item-image">' . $image . '</span>';
				echo '<span class="item-col item-content">';
					echo '<span class="item-title"><a href="' . get_permalink( $program->ID ) . '">' . $program->post_title . '</a></span>';
					echo '<span class="item-description">' . wampum_get_truncated_content($program->post_excerpt, 140) . '</span>';
				echo '</span>';
				echo '<span class="item-col item-actions"><a class="button" href="' . get_permalink( $program->ID ) . '">View</a></span>';
			echo '</li>';
		}

	echo '</ul>';

}

do_action('wampum_account_after_programs');
