<?php
/**
 * Title: Resources
 * Setting: wampum_settings
 * Order: 20
 */

// Resources
$step = get_post_type_object( 'wampum_step' )->labels;

piklist('field', array(
	'type'		=> 'text',
	'field'		=> 'wampum_step_plural',
	'label'		=> __('Plural name', 'wampum'),
	'value'		=> $step->name,
	'validate'	=> array(
		array(
			'type' => 'safe_text',
		),
	),
	'required' => true,
));

piklist('field', array(
	'type'		=> 'text',
	'field'		=> 'wampum_step_singular',
	'label'		=> __('Singular name', 'wampum'),
	'value'		=> $step->singular_name,
	'validate'	=> array(
		array(
			'type' => 'safe_text',
		),
	),
	'required' => true,
));
