<?php
/**
 * Title: My Term Metabox
 * Description: My cool new metabox
 * Taxonomy: wampum_program
 * Capability: manage_options
 * Locked: true
 * New: true
 * Collapse: true
 */

// Let's create a text box field
piklist('field', array(
	'type'			=> 'text',
	'field'			=> 'field_name',
	'label'			=> __('Example Field'),
	'description'	=> __('Field Description'),
	'attributes'	=> array(
	 	'class' => 'text',
	),
));
