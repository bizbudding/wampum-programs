<?php
/**
 * Title: Resources
 * Setting: wampum_settings
 * Order: 20
 */

// Resources
$lesson = get_post_type_object( 'wampum_lesson' )->labels;

piklist('field', array(
	'type'		=> 'text',
	'field'		=> 'wampum_lesson_plural',
	'label'		=> __('Plural name', 'wampum'),
	'value'		=> $lesson->name,
	'validate'	=> array(
		array(
			'type' => 'safe_text',
		),
	),
	'required' => true,
));

piklist('field', array(
	'type'		=> 'text',
	'field'		=> 'wampum_lesson_singular',
	'label'		=> __('Singular name', 'wampum'),
	'value'		=> $lesson->singular_name,
	'validate'	=> array(
		array(
			'type' => 'safe_text',
		),
	),
	'required' => true,
));
