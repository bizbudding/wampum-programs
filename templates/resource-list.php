<?php
// Enqueue our pre-registered stylesheet
wp_enqueue_style('wampum');

echo '<ul class="wampum-resource-list wtable" style="margin-left:0;">';

    echo '<li class="wtable-row">';
        echo '<span class="wtable-header">' . Wampum()->content->singular_name(get_post_type()) . ' ' . Wampum()->content->plural_name('wampum_resource') . '</span>';
    echo '</li>';

	foreach ( $data as $resource ) {

		$buttons = '';
		$file = get_post_meta( $resource->ID, 'wampum_resource_files', true );
		if ( $file ) {
			$buttons .= '<a target="_blank" class="button wtable-button wtable-button-right" href="' . wp_get_attachment_url($file) . '">Download</a>';
		}
		$buttons .= '<a class="button wtable-button wtable-button-left" href="' . get_permalink($resource->ID) . '">View</a>';

		$image   = '';
		if ( has_post_thumbnail( $resource->ID ) ) {
			$image = sprintf( '<a class="wtable-cell wtable-image" href="%s" title="%s">%s</a>',
				get_permalink(),
				the_title_attribute( 'echo=0' ),
				get_the_post_thumbnail( $resource->ID, 'thumbnail' )
			);
		}
		$title = sprintf( '<span class="wtable-title"><a href="%s" title="%s">%s</a></span>',
			get_permalink($resource->ID),
			the_title_attribute( 'echo=0' ),
			$resource->post_title
		);
		$desc = sprintf( '<span class="wtable-desc">%s</span>',
			wampum_get_truncated_content($resource->post_excerpt, 140)
		);
		$content = sprintf( '<span class="wtable-cell wtable-grow wtable-content">%s</span>',
			$title . $desc
		);
		$actions = '<span class="wtable-cell wtable-auto wtable-actions">' . $buttons . '</span>';
		echo '<li class="wtable-row">' . $image . $content . $actions . '</li>';
	}

echo '</ul>';