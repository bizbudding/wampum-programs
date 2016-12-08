<?php
/**
 * Adds Wampum_Widget_Program_Steps widget.
 */
class Wampum_Widget_Program_Steps extends WP_Widget {

	/**
	 * Register widget with WordPress.
	 */
	public function __construct() {
		parent::__construct(
	 		'wampum_widget_program_children', // Base ID
			'Wampum - ' . wampum_get_singular_name('wampum_program') . ' Children', // Name
			array( 'description' => __( 'Show child pages of a program', 'wampum' ), ) // Args
		);
	}

	/**
	 * Front-end display of widget.
	 *
	 * @see WP_Widget::widget()
	 *
	 * @param array $args     Widget arguments.
	 * @param array $instance Saved values from database.
	 */
	public function widget( $args, $instance ) {

		if ( ! is_singular('wampum_program') ) {
			return;
		}

		$queried_object  = get_queried_object();
		$queried_post_id = $queried_object->ID;

		$program_id = $children = '';

		$program_id = wampum_get_top_parent_id();

		$children = wampum_get_children_ids($program_id);

		// Bail no program or children
		if ( ! $program_id || ! $children ) {
			return;
		}

		$completed_ids = array();

		// Step progress
		// NO LONGER USED - SAVE FOR REFERENCE
		// if ( is_user_logged_in() && wampum_is_program_progress_enabled( $program_id ) ) {

		// 	// Enqueue our pre-registered stylesheet
		// 	wp_enqueue_style('wampum');

		// 	$completed = get_posts( array(
		// 		'connected_type'	=> 'user_program_progress',
		// 		'connected_items'	=> get_current_user_id(),
		// 		'nopaging'			=> true,
		// 		'suppress_filters'	=> false,
		// 	) );
		// 	if ( $completed ) {
		// 		foreach ( $completed as $complete ) {
		// 			$completed_ids[] = $complete->p2p_to;
		// 		}
		// 	}

		// }

		extract( $args );

		echo $before_widget;

		// Build title
		$title = esc_attr( $instance['title'] );
		if ( 1 == $instance['title_from_program'] ) {
			$title = get_the_title( $program_id );
			if ( 1 == $instance['title_link'] ) {
				$title = '<a href="' . get_permalink( $program_id ) . '">' . apply_filters( 'wampum_programs_widget_title', $title ) . '</a>';
			}
		}

		if ( ! empty( $title ) ) {
			echo $before_title . $title . $after_title;
		}

	    echo '<ul class="widget-program-children">';
	    	// Set default li class
			foreach ( $children as $child_id ) {
		    	$classes = 'widget-program-child';
				// Add class if current child
				if ( $queried_post_id === $child_id ) {
					$classes .= ' current-child';
				}
				// Add class if child is completed
				// NO LONGER IN USE, SAVE FOR REFERENCE
				// if ( in_array($child_id, $completed_ids) ) {
				// 	$classes .= ' completed';
				// }
				echo '<li class="' . $classes . '"><a href="' . get_the_permalink( $child_id ) . '" title="' . get_the_title( $child_id ) . '">' . get_the_title( $child_id ) . '</a></li>';
			}

		echo '</ul>';

		echo $after_widget;
	}

	/**
	 * Sanitize widget form values as they are saved.
	 *
	 * @see WP_Widget::update()
	 *
	 * @param array $new_instance Values just sent to be saved.
	 * @param array $old_instance Previously saved values from database.
	 *
	 * @return array Updated safe values to be saved.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = array();
		// $instance['title'] = strip_tags( $new_instance['title'] );
		/* Strip tags (if needed) and update the widget settings. */
		$instance['title'] 				= esc_attr( $new_instance['title'] );
		$instance['title_from_program'] = (int) $new_instance['title_from_program'];
		$instance['title_link']			= (int) $new_instance['title_link'];
		// $instance['show_nested']		= (int) $new_instance['show_nested'];
		return $instance;
	}

	/**
	 * Back-end widget form.
	 *
	 * @see WP_Widget::form()
	 *
	 * @param array $instance Previously saved values from database.
	 */
	public function form( $instance ) {
		// Set up some default widget settings.
		$defaults = array( 'title' => '', 'title_from_program' => 0, 'title_link' => 0 );
		$instance = wp_parse_args( (array) $instance, $defaults );
		?>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $instance['title'] ); ?>" />
		</p>

		<p>
			<input class="checkbox" type="checkbox" value="1" <?php checked( $instance['title_from_program'], 1 ); ?> id="<?php echo $this->get_field_id( 'title_from_program' ); ?>" name="<?php echo $this->get_field_name( 'title_from_program' ); ?>" />
			<label for="<?php echo $this->get_field_id( 'title_from_program' ); ?>"><?php _e( 'Use program name as widget title.', 'wampum' );?></label>
		</p>

		<p>
			<input class="checkbox" type="checkbox" value="1" <?php checked( $instance['title_link'], 1 ); ?> id="<?php echo $this->get_field_id( 'title_link' ); ?>" name="<?php echo $this->get_field_name( 'title_link' ); ?>" />
			<label for="<?php echo $this->get_field_id( 'title_link' ); ?>"><?php _e( 'Make title a link', 'wampum' ); echo '<br /><em>('; _e( 'only if "Use program name as widget title." is checked', 'wampum' ); echo ')</em></label>';?>
		</p>

		<!-- <p> -->
			<!-- <input class="checkbox" type="checkbox" value="1" <?php checked( $instance['show_nested'], 1 ); ?> id="<?php echo $this->get_field_id( 'show_nested' ); ?>" name="<?php echo $this->get_field_name( 'show_nested' ); ?>" /> -->
			<!-- <label for="<?php echo $this->get_field_id( 'show_nested' ); ?>"><?php _e( 'Show all nested posts', 'wampum' ); ?></label> -->
		<!-- </p> -->
		<?php
	}

}
