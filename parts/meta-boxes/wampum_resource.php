<?php
/*
Title: Resource File
Description: My cool new metabox
Post Type: wampum_resource
Capability: edit_posts
Context: normal
Priority: high
Order: 1
*/

piklist('field', array(
	'type'		=> 'file',
	'field'		=> 'wampum_resource_files',
	'label'		=> __('Upload a file for this resource', 'wampum'),
	'template'	=> 'field',
	// 'validate' => array(
	// 	array(
	// 		'type' => 'file_exists',
	// 	),
	// ),
));