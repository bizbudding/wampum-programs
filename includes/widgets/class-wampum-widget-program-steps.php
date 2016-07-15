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
			'Wampum - ' . wampum_get_plural_name('wampum_step'), // Name
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

		if ( ! is_singular('wampum_program') ) {
			return;
		}

		$queried_object  = get_queried_object();
		$queried_post_id = $queried_object->ID;

		$program_id = $steps = '';

		if ( wampum_is_program($queried_post_id) ) {
			$program_id	= $queried_post_id;
		} elseif ( wampum_is_step($queried_post_id) ) {
			$program_id = wampum_get_step_program_id($queried_post_id);
		}

		$steps = wampum_get_program_step_ids($program_id);

		// Bail no program or steps
		if ( ! $program_id || ! $steps ) {
			return;
		}

		$completed_ids = array();

		// Step progress
		if ( is_user_logged_in() && wampum_is_program_progress_enabled( $program_id ) ) {

			$completed = Wampum()->connections->get_connected_items( 'user_step_progress', get_current_user_id() );

			if ( $completed ) {
				foreach ( $completed as $complete ) {
					$completed_ids[] = $complete->p2p_to;
				}
			}

		}

		extract( $args );

		echo $before_widget;

		// Build title
		$title = esc_attr( $instance['title'] );
		if ( 1 == $instance['title_from_program'] ) {
			$title = get_the_title( $program_id );
			if ( 1 == $instance['title_link'] ) {
				$title = '<a href="' . get_permalink( $program_id ) . '">' . apply_filters( 'wampum_steps_widget_title', $title ) . '</a>';
			}
		}

		if ( ! empty( $title ) ) {
			echo $before_title . $title . $after_title;
		}

	    echo '<ul class="widget-program-steps">';
	    	// Set default li class
			foreach ( $steps as $step_id ) {
		    	$classes = 'widget-program-step';
				// Add class if current step
				if ( $queried_post_id === $step_id ) {
					$classes .= ' current-step';
				}
				// Add class if step is completed
				if ( in_array($step_id, $completed_ids) ) {
					$classes .= ' completed';
				}
				echo '<li class="' . $classes . '"><a href="' . get_the_permalink( $step_id ) . '" title="' . get_the_title( $step_id ) . '">' . get_the_title( $step_id ) . '</a></li>';
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
		<?php
	}

} // class Wampum_Widget_Program_Steps
