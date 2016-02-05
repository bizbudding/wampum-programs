<?php
/**
 * Title: Programs
 * Setting: wampum_settings
 * Order: 10
 */

$program = get_post_type_object( 'wampum_program' )->labels;

piklist('field', array(
	'type'		=> 'text',
	'field'		=> 'wampum_program_plural',
	'label'		=> __('Plural name', 'wampum'),
	'value'		=> $program->name,
	'validate'	=> array(
		array(
			'type' => 'safe_text',
		),
	),
	'required' => true,
));

piklist('field', array(
	'type'		=> 'text',
	'field'		=> 'wampum_program_singular',
	'label'		=> __('Singular name', 'wampum'),
	'value'		=> $program->singular_name,
	'validate'	=> array(
		array(
			'type' => 'safe_text',
		),
	),
	'required' => true,
));
