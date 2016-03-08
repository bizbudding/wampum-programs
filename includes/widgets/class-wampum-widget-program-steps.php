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
			'Wampum - ' . Wampum_Content_Types::plural_name('wampum_step'), // Name
			// 'TEST', // Name
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
		if ( 'wampum_step' !== get_post_type() ) {
			return;
		}

		global $wampum_content_types;

		// Get current post ID
		$queried_step_id = get_the_ID();
		// Get the program this step is from
		$program = $wampum_content_types->get_step_program( $queried_step_id );
		// Get all steps from program
		$steps   = $wampum_content_types->get_program_steps( $program );

		// Bail no steps
		if ( ! $steps ) {
			return;
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
				// Add current step class
				if ( $queried_step_id === $step->ID ) {
					$classes .= ' current-step';
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
