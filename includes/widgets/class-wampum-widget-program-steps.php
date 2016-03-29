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
	 		'wampum_widget_program_steps', // Base ID
			'Wampum - ' . Wampum()->content->plural_name('wampum_step'), // Name
			array( 'description' => __( 'Show steps of a program', 'wampum' ), ) // Args
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

		// Bail if not viewing a step
		// if ( 'wampum_step' !== get_post_type() ) {
			// return;
		// }
		// Post types to check agains
		$post_types = array('wampum_program','wampum_step');
		$post_type  = get_post_type();
		// Bail if not viewing a step or program
		if ( ! in_array($post_type, $post_types) ) {
			return;
		}

		// global $wp_query;
		$queried_object = get_queried_object();
		// echo '<pre>';
	    // var_dump( Wampum()->connections->get_steps_from_program_query( $wp_query ) );
	    // print_r( $queried_object );
	    // var_dump( $wp_query->steps );
	    // echo '</pre>';
		// Get current post ID
		// $queried_post_id = get_the_ID();
		$queried_post_id = $queried_object->ID;

		// // Get program
		// if ( 'wampum_program' === get_post_type() ) {
		// 	$program_id = $queried_post_id;
		// } else {
		// 	// Get the program this step is from
		// 	$program_id = Wampum()->content->get_step_program_id( $queried_post_id );
		// 	// $program_id = 405;
		// }
		// // Get all steps from program
		// $steps = Wampum()->content->get_program_steps( $program_id );

		$program_id = $steps = '';

		if ( 'wampum_program' === $post_type ) {
			$program_id	= $queried_post_id;
			$steps		= Wampum()->connections->get_steps_from_program_query( $queried_object );
		} elseif ( 'wampum_step' === $post_type ) {
			// $program_id	= Wampum()->content->get_step_program_id( $queried_post_id );
			$program_id	= Wampum()->connections->get_program_from_step_query( $queried_object )->ID;
			$steps		= Wampum()->connections->get_steps_from_step_query( $queried_object );
		}

		// Bail no program or steps
		if ( ! $program_id || ! $steps ) {
			return;
		}


		$completed_ids = array();

		// Step progress
		if ( is_user_logged_in() && Wampum()->step_progress->is_step_progress_enabled( $program_id ) ) {

			$completed = Wampum()->connections->get_connected_items( 'user_step_progress', get_current_user_id() );

			if ( $completed ) {
				foreach ( $completed as $complete ) {
					$completed_ids[] = $complete->p2p_to;
				}
			}

		}

		extract( $args );
		$title = apply_filters( 'widget_title', $instance['title'] );

		echo $before_widget;

		if ( ! empty( $title ) ) {
			echo $before_title . $title . $after_title;
		}

	    echo '<ul class="widget-program-steps">';
	    	// Set default li class
			foreach ( $steps as $step ) {
		    	$classes = 'widget-program-step';
				// Add class if current step
				if ( $queried_post_id === $step->ID ) {
					$classes .= ' current-step';
				}
				// Add class if step is completed
				if ( in_array($step->ID, $completed_ids) ) {
					$classes .= ' completed';
				}
				echo '<li class="' . $classes . '"><a href="' . get_the_permalink( $step ) . '" title="' . get_the_title( $step ) . '">' . get_the_title( $step ) . '</a></li>';
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
		$instance['title'] = strip_tags( $new_instance['title'] );
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
		if ( isset( $instance[ 'title' ] ) ) {
			$title = $instance[ 'title' ];
		}
		else {
			$title = __( 'New title', 'amc' );
		}
		?>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
		</p>
		<?php
	}

} // class Wampum_Widget_Program_Steps
