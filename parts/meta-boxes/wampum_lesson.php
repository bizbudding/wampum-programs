<?php
/*
Title: Add Resource
Post Type: wampum_lesson
Capability: edit_posts
Context: normal
Priority: default
Meta box: true
*/

piklist('field', array(
  'type'     => 'html',
  'template' => 'field',
  'value'    => __('Add resources now, or you can always attach resources later.', 'wampum'),
));

// Topic ID
piklist('field', array(
  'type'  => 'hidden',
  'scope' => 'connect_resource_to_lesson',
  'field' => 'lesson_id',
  'value' => get_the_ID(),
));

// Resources
piklist('field', array(
  'type'     => 'group',
  'scope'    => 'connect_resource_to_lesson',
  'field'    => 'add_resource',
  'template' => 'field',
  'add_more' => true,
  'label'       => __('Add New Resources', 'wampum'),
  'description' => __('Add resources now, or you can always attach resources later.', 'wampum'),
  // 'help'        => 'This is help text.',
  'fields' => array(
    array(
      'type'    => 'text',
      'scope'   => 'connect_resource_to_lesson',
      'field'   => 'post_title',
      'label'   => __('Title', 'wampum'),
      'columns' => '12',
    ),
    array(
      'type'    => 'editor',
      'scope'   => 'connect_resource_to_lesson',
      'field'   => 'post_content',
      'label'   => __('Content', 'wampum'),
      'columns' => '12',
    ),
    array(
      'type'  => 'file',
      'scope' => 'connect_resource_to_lesson',
      'field' => 'resource_file',
      'label' => __('Upload File', 'wampum'),
    ),
  ),
));

// piklist('field', array(
  // 'scope' => 'connect_resource_to_lesson',
  // 'type'  => 'submit',
  // 'field' => 'submit',
  // 'value' => 'Submit',
// ));