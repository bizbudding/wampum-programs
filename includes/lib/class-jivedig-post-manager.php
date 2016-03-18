<?php
/**
 * Create an list of posts with action links to view | edit | delete
 *
 * @package   JiveDig_Post_Manager
 * @author    Mike Hemberger
 * @link      TBD
 * @copyright 2016 Mike Hemberger
 * @license   GPL-2.0+
 * @version   1.0.0
 */

if ( ! class_exists( 'JiveDig_Post_Manager' ) )  {
	/**
	 * Post Manager
	 *
	 * When using in a plugin, create a new class that extends this one and just overrides the properties.
	 *
	 * @package JiveDig_Post_Manager
	 * @author  Mike Hemberger
	 */
	class JiveDig_Post_Manager {

		/**
		 * Template file
		 *
		 * @since 1.0.0
		 *
		 * @var string
		 */
		// protected $no_posts = 'Sorry, no posts to display';



		// public function __construct() {
			// add_action( 'rest_api_init', array( $this, 'register_rest_endpoints' ) );
			// add_action( 'wp_enqueue_scripts', array( $this, 'register_scripts' ) );
		// }

		public function posts( $args ) {
			$posts = $this->get_the_posts( $args );
			if ( ! $post_type->have_posts() ) {
				wp_reset_postdata();
				return false;
			}
			$output = '';
			while ( $posts->have_posts() ) : $posts->the_post();
				$output .= $this->posts_template( $posts );
			endwhile;
			wp_reset_postdata();

			return $output;
		}

		/**
		 * In the loop
		 *
		 * @param  [type] $posts [description]
		 *
		 * @return [type]        [description]
		 */
		public function posts_template( $posts ) {
			$output  = '';
			$output .= '<div class="pm-post">';
				$output .= '<div class="pm-post-image">' . $this->get_image( get_the_ID(), 'thumbnail' ) . '</div>';
				$output .= '<div class="pm-post-title">' . get_the_title() . '</div>';
				$output .= '<div class="pm-post-excerpt">' . get_the_excerpt() . '</div>';
				$output .= '<div class="pm-post-status">' . $this->get_status( $post_id ) . '</div>';
				$output .= '<div class="pm-post-actions">' . $this->get_actions( $post_id ) . '</div>';
			$output .= '</div>';
		}

		public function get_image( $post_id, $image_size, $fallback = '' ) {
			$image = $fallback;
			if ( has_post_thumbnail( $post_id ) ) {
			    $image = sprintf( '<a href="%s" title="%s">%s</a>',
					get_permalink( $post_id ),
					the_title_attribute( 'echo=0' ),
					get_the_post_thumbnail( $post_id, $image_size )
				);
			}
			return $image;
		}

		public function get_status( $post_id ) {

		}

		public function get_actions( $post_id ) {

		}

		// Taken from EDD FES just for reference while i build this
		public function product_list_actions( $product_id ) {

			if( 'publish' == get_post_status( $product_id ) ) : ?>
				<a href="<?php echo esc_html( get_permalink( $product_id ) );?>" title="<?php _e( 'View', 'edd_fes' );?>" class="edd-fes-action view-product-fes"><?php _e( 'View', 'edd_fes' );?></a>
			<?php endif; ?>

			<?php if ( EDD_FES()->helper->get_option( 'fes-allow-vendors-to-edit-products', true ) && 'future' != get_post_status( $product_id ) ) : ?>
				<a href="<?php echo add_query_arg( array( 'task' => 'edit-product', 'post_id' => $product_id ), get_permalink() ); ?>" title="<?php _e( 'Edit', 'edd_fes' );?>" class="edd-fes-action edit-product-fes"><?php _e( 'Edit', 'edd_fes' );?></a>
			<?php endif; ?>

			<?php if ( EDD_FES()->helper->get_option( 'fes-allow-vendors-to-delete-products', true ) ) : ?>
				<a href="<?php echo add_query_arg( array( 'task' => 'delete-product', 'post_id' => $product_id ), get_permalink() );?>" title="<?php _e( 'Delete', 'edd_fes' );?>" class="edd-fes-action edit-product-fes"><?php _e( 'Delete', 'edd_fes' );?></a>
			<?php endif;
		}

		protected function get_the_posts( $args ) {
		    $args = array(
		    	'author'	     => get_current_user_id(),
		        'post_type'      => 'post',
		        'posts_per_page' => '12',
		        'post_status'    => 'publish',
		    );
		    return = new WP_Query( $args );
		}

	}
}
