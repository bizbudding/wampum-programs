<?php

/**
 * Display child pages in a flexington grid
 *
 * @version  1.0.0
 *
 * @since  1.5.0
 *
 * $atts = array(
 *     $type  bool    'center'            Whether or not to center the column content (default true)
 *     $type  int     'columns'           How many columns to display on larger browser widths (default 3)
 *     $type  int     'excerpt_length'    The amount of words to show if an excerpt is not manually added and the excerpt is stripped from the content (default false)
 *     $type  bool    'excerpt_more'      Whether or not to show the more link [ relies on apply_filters( 'excerpt_more', '' ) filter ] (default false)
 *     $type  string  'image_size'        Size of image to use (default 'thumbnail')
 *     $type  string  'order'             ASC or DESC (default ASC)
 *     $type  string  'orderby'           The orderby parameter of WP_Query (default 'menu_order')
 *     $type  int     'post_parent'       The post_parent parameter of WP_Query (default current page)
 *     $type  string  'post_status'       The post_status parameter of WP_Query (default 'publish')
 *     $type  string  'post_type'         The post_type parameter of WP_Query (default current)
 *     $type  int     'posts_per_page'    The posts_per_page parameter of WP_Query (default 12)
 *     $type  bool    'show_excerpt'      Whether or not to center the column content (default false)
 *     $type  bool    'show_image'        Whether or not to center the column content (default true)
 *     $type  bool    'show_title'        Whether or not to center the column content (default true)
 * )
 *
 */
add_shortcode( 'wampum-child-pages', function( $atts ) {

    global $post;

    // Shortcode attributes
    $atts = shortcode_atts( array(
        'center'            => true,
        'columns'           => 3,
        'excerpt_length'    => false,
        'excerpt_more'      => false,
        'image_size'        => apply_filters( 'wampum_child_pages_default_image_size', 'thumbnail' ),
        'order'             => 'ASC',
        'orderby'           => 'menu_order',
        'post_parent'       => get_the_ID(),
        'post_status'       => 'publish',
        'post_type'         => $post->post_type,
        'posts_per_page'    => '12',
        'show_excerpt'      => false,
        'show_image'        => true,
        'show_title'        => true,
    ), $atts, 'child-pages' );

    // WP_Query args
    $args = array(
        'order'          => $atts['order'],
        'orderby'        => $atts['orderby'],
        'post_parent'    => intval($atts['post_parent']),
        'post_type'      => $atts['post_type'],
        'post_status'    => $atts['post_status'],
        'posts_per_page' => intval($atts['posts_per_page']),
    );

    $post_type = new WP_Query( $args );

    $output = '';

    if ( $post_type->have_posts() ) {

        switch ( intval($atts['columns']) ) {
            case 1:
                $classes = 'col-xs-12';
                break;
            case 2:
                $classes = 'col-xs-12 col-sm-6';
                break;
            case 3:
                $classes = 'col-xs-12 col-sm-6 col-md-4';
                break;
            case 4:
                $classes = 'col-xs-12 col-sm-6 col-md-3';
                break;
            case 6:
                $classes = 'col-xs-6 col-sm-4 col-md-2';
                break;
        }

        if ( filter_var( $atts['center'], FILTER_VALIDATE_BOOLEAN ) ) {
            $classes .= ' center-xs';
        }

        $excerpt_length = intval( $atts['excerpt_length'] );
        $excerpt_more   = filter_var( $atts['excerpt_more'], FILTER_VALIDATE_BOOLEAN );
        $show_image     = filter_var( $atts['show_image'], FILTER_VALIDATE_BOOLEAN );
        $show_title     = filter_var( $atts['show_title'], FILTER_VALIDATE_BOOLEAN );
        $show_excerpt   = filter_var( $atts['show_excerpt'], FILTER_VALIDATE_BOOLEAN );

        $output .= '<div class="child-pages row gutter-30">';

            while ( $post_type->have_posts() ) : $post_type->the_post();

                $output .= '<div class="child-page col ' . $classes . ' bottom-xs-30">';

                    if ( $show_image && has_post_thumbnail() ) {
                        $output .= '<div class="child-page-image"><a class="image-link" href="' . get_permalink() . '" title="' . the_title_attribute( 'echo=0' ) . '">' . get_the_post_thumbnail( get_the_ID(), $atts['image_size'] ) . '</a></div>';
                    }

                    if ( $show_title ) {
                        $output .= '<h2 class="entry-title" itemprop="headline"><a href="' . get_permalink() . '" title="' . esc_attr(get_the_title()) . '">' . get_the_title() . '</a></h2>';
                    }

                    if ( $show_excerpt ) {

                        // Custom build excerpt based on shortcode parameters
                        if ( $excerpt_length || $excerpt_more ) {

                            $more = $excerpt_more ? apply_filters( 'excerpt_more', '' ) : '';

                            if ( has_excerpt() ) {
                                // Don't add $more to wp_trim_words because it won't show if manual excerpt is less than the excerpt limit
                                $excerpt = wp_trim_words( strip_shortcodes( $post->post_excerpt ), $excerpt_length, '' );
                                $excerpt .= $more;
                            } else {
                                $excerpt = wp_trim_words( strip_shortcodes( $post->post_content ), $excerpt_length, $more );
                            }

                        // Use default, can customize with WP filters
                        } else {
                            $excerpt = get_the_excerpt();
                        }

                        $output .= '<div class="child-page-excerpt">' . $excerpt . '</div>';

                    }

                $output .= '</div>';

            endwhile;

        $output .= '</div>';

    }
    wp_reset_postdata();

    return $output;
});
