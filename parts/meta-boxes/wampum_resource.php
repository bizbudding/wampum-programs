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
	'type'	=> 'file',
	'field'	=> 'resource_file',
	'label'	=> __('Upload a file for this resource', 'wampum'),
));