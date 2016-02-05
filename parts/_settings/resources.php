<?php
/**
 * Title: Resources
 * Setting: wampum_settings
 * Order: 30
 */

// Resources
$resource = get_post_type_object( 'wampum_resource' )->labels;

piklist('field', array(
	'type'		=> 'text',
	'field'		=> 'wampum_resource_plural',
	'label'		=> __('Plural name', 'wampum'),
	'value'		=> $resource->name,
	'validate'	=> array(
		array(
			'type' => 'safe_text',
		),
	),
	'required' => true,
));

piklist('field', array(
	'type'		=> 'text',
	'field'		=> 'wampum_resource_singular',
	'label'		=> __('Singular name', 'wampum'),
	'value'		=> $resource->singular_name,
	'validate'	=> array(
		array(
			'type' => 'safe_text',
		),
	),
	'required' => true,
));
