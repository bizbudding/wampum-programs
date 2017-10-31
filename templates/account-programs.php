<?php

// Enqueue our pre-registered stylesheet
wp_enqueue_style('wampum');

echo '<ul class="wampum-programs-list wtable" style="margin-left:0;">';

	echo '<li class ="wtable-row">';
		echo '<span class ="wtable-header">Program</span>';
	echo '</li>';

	foreach ( $data as $program ) {

		$image_size = apply_filters('wampum_account_programs_image_size', 'thumbnail');

		$image = '';
		if ( has_post_thumbnail( $program->ID ) ) {
			$image = sprintf( '<a class="wtable-cell wtable-image" href="%s" title="%s">%s</a>',
				get_permalink( $program->ID ),
				the_title_attribute( 'echo=0' ),
				get_the_post_thumbnail( $program->ID, $image_size )
			);
		}
		$title = sprintf( '<span class="wtable-title"><a href="%s" title="%s">%s</a></span>',
			get_permalink( $program->ID ),
			the_title_attribute( 'echo=0' ),
			get_the_title( $program->ID )
		);
		$desc = sprintf( '<span class="wtable-desc">%s</span>',
			wampum_get_truncated_content( do_shortcode( get_the_excerpt( $program->ID ) ), 140 )
		);
		$content = sprintf( '<span class="wtable-cell wtable-grow wtable-content">%s</span>',
			$title . $desc
		);
		$buttons = '<a class="button wtable-button" href="' . get_permalink($program->ID) . '">View</a>';
		$actions = '<span class="wtable-cell wtable-actions">' . $buttons . '</span>';
		echo '<li class="wtable-row">' . $image . $content . $actions . '</li>';
	}

echo '</ul>';
