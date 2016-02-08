<?php
/*
Title: Add Resource
Method: post
Logged in: true
Message: This is the success message.
*/

/**
 * Piklist forms automatically generate a shortcode:
 *
 * If your form is in a PLUGIN (i.e. wp-content/plugins/my-plugin/parts/forms/my-form.php)
 *
 * Use [piklist_form form="my-form" add_on="my-plugin"]
 *
 * If your form is in a THEME (i.e. wp-content/themes/my-theme/piklist/parts/forms/my-form.php)
 * Use [piklist_form form="my-form" add_on="theme"]
 */

// Topic ID
piklist('field', array(
  'type'  => 'hidden',
  'scope' => 'connect_resource_to_step',
  'field' => 'step_id',
  'value' => get_the_ID(),
));

// Resources
piklist('field', array(
  'type'        => 'group',
  'scope'       => 'connect_resource_to_step',
  'field'       => 'add_resource',
  'add_more'    => true,
  'label'       => __('Add New Resources', 'wampum'),
  'description' => __('Add resources now, or you can always attach resources later.', 'wampum'),
  // 'help'        => 'This is help text.',
  'fields' => array(
    array(
      'type'  => 'text',
      'scope' => 'connect_resource_to_step',
      'field' => 'post_title',
      'label' => __('Resource Title', 'wampum'),
    ),
    array(
      'type'  => 'editor',
      'scope' => 'connect_resource_to_step',
      'field' => 'post_content',
      'label' => __('Resource Content', 'wampum'),
    ),
    array(
      'type'  => 'file',
      'scope' => 'connect_resource_to_step',
      'field' => 'resource_file',
      'label' => __('Upload File', 'wampum'),
    ),
  ),
));

piklist('field', array(
  'scope' => 'post',
  'type'  => 'submit',
  'field' => 'submit',
  'value' => 'Submit',
));
