<?php
/**
 * Title: Program Settings
 * Description: My cool new metabox
 * Post Type: wampum_program
 * Capability: manage_options
 * Context: normal
 * Priority: high
 * Order: 1
 */

piklist('field', array(
    'type'        => 'group',
    // 'template'	  => 'field',
    // 'scope'       => 'post_meta',
    'field'       => 'wampum_program_step_progress',
    'label'       => 'Step Progress',
	'fields' => array(
	    array(
	    	'type'    => 'checkbox',
	    	'field'	  => 'enabled',
		    'choices' => array(
		        'yes' => 'Enable step progress on this program',
		    ),
		    // 'value' => 'yes',
		),
		array(
			'type'		 => 'text',
			'field'		 => 'connect_text',
			'label'		 => __( 'Connect Text', 'wampum' ),
			'conditions' => array(
		    	array(
			        'field' => 'wampum_program_step_progress:enabled',
			        'value' => 'yes',
				),
		    ),
			// 'attributes' => array(
				// 'placeholder' => __( 'Mark Complete', 'wampum' ),
			// ),
			// 'value'			=> __( 'Mark Complete', 'wampum' ),
			// 'columns'		=> 12,
		),
		array(
			'type'		 => 'text',
			'field'		 => 'connected_text',
			'label'		 => __( 'Connected Text', 'wampum' ),
			'conditions' => array(
		    	array(
			        'field' => 'wampum_program_step_progress:enabled',
			        'value' => 'yes',
				),
		    ),
			// 'attributes' => array(
				// 'placeholder' => __( 'Completed', 'wampum' ),
			// ),
			// 'value'			=> __( 'Completed', 'wampum' ),
			// 'columns'		=> 12,
		),
	),

    // 'required' => true,
));