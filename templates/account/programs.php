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

	echo '<ul class="my-posts" style="margin-left:0;">';

		foreach ( $programs as $program ) {

			// piklist::pre($program);

			$image_size = apply_filters('wampum_account_programs_image_size', 'thumbnail');

			$image = '';
			if ( has_post_thumbnail( $program->ID ) ) {
			    $image = sprintf( '<a href="%s" title="%s">%s</a>',
					get_permalink( $program->ID ),
					the_title_attribute( 'echo=0' ),
					get_the_post_thumbnail( $program->ID, $image_size )
				);
			}
			echo '<li class="my-post">';
				echo '<span class="post-item post-image">' . $image . '</span>';
				echo '<span class="post-item post-content">';
					echo '<a href="' . get_permalink( $program->ID ) . '">' . $program->post_title . '</a>';
					echo wampum_get_truncated_content($program->post_excerpt, 140);
				echo '</span>';
				echo '<span class="post-item post-actions"><a class="button" href="' . get_permalink( $program->ID ) . '">View</a></span>';
			echo '</li>';

		}

	echo '</ul>';

}

do_action('wampum_account_after_programs');
