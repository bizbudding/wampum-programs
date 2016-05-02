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
}

// echo '<div class="wampum-user-programs flex-cols flex-col-xs-12 flex-col-sm-6">';
echo '<ul class="my-posts" style="margin-left:0;">';
// echo '<li class="my-post-heading"><span class="post-heading post-image">Post Date</span><span class="post-heading post-content">Title</span><span class="post-heading post-actions">Actions</span>';

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
	?>
<!-- 	<div class="program flex-cols middle-xs">
		<div class="image col"><?php echo $image; ?></div>
		<div class="content col">
			<div class="title"><h3><a href="<?php echo get_permalink( $program->ID ); ?>"><?php echo $program->post_title ?></a></h3></div>
			<div class="excerpt"><?php echo $program->post_excerpt ?></div>
		</div>
		<div class="actions col">
			<a class="button" href="<?php echo get_permalink( $program->ID ); ?>">View</a>
		</div>
	</div> -->
	<?php


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

do_action('wampum_account_after_programs');
