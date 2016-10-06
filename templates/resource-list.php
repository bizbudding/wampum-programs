<?php
// Enqueue our pre-registered stylesheet
wp_enqueue_style('wampum');

echo '<ul id="resource-list" class="wampum-resource-list wtable" style="margin-left:0;">';

    echo '<li class="wtable-row">';
        echo '<span class="wtable-header">' . wampum_get_plural_name('wampum_resource') . '</span>';
    echo '</li>';

	foreach ( $data as $resource_id ) {
		$resource			= get_post($resource_id);
		$resource_popup_url	= add_query_arg( 'resource', $resource_id, get_permalink() );
		$buttons			= '';
		// $file = get_post_meta( $resource->ID, 'wampum_resource_file', true );
		// if ( $file ) {
		// 	$buttons .= '<a target="_blank" class="button wtable-button wtable-button-right" href="' . wp_get_attachment_url($file) . '">Download</a>';
		// }
		$buttons .= '<a class="button wtable-button wtable-button-left" href="' . $resource_popup_url . '">View</a>';

		$image   = '';
		if ( has_post_thumbnail( $resource->ID ) ) {
			$image = sprintf( '<a class="wtable-cell wtable-image" href="%s" title="%s">%s</a>',
				$resource_popup_url,
				the_title_attribute( 'echo=0' ),
				get_the_post_thumbnail( $resource->ID, 'thumbnail' )
			);
		}
		$title = sprintf( '<span class="wtable-title"><a href="%s" title="%s">%s</a></span>',
			$resource_popup_url,
			the_title_attribute( 'echo=0' ),
			$resource->post_title . '<br /><a href="' . get_edit_post_link($resource->ID) . '">[Edit]</a>'
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