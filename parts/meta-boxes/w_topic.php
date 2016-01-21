<?php
/*
Title: Add Resource
Post Type: w_topic
Capability: edit_posts
Context: normal
Priority: default
Meta box: true
*/

piklist('field', array(
  'type'     => 'html',
  'template' => 'field',
  'value'    => __('Add resources now, or you can always attach resources later.', 'piklist-demo'),
));

// Topic ID
piklist('field', array(
  'type'  => 'hidden',
  'scope' => 'connect_resource_to_topic',
  'field' => 'topic_id',
  'value' => get_the_ID(),
));

// Resources
piklist('field', array(
  'type'     => 'group',
  'scope'    => 'connect_resource_to_topic',
  'field'    => 'add_resource',
  'template' => 'field',
  'add_more' => true,
  'label'       => __('Add New Resources', 'piklist-demo'),
  'description' => __('Add resources now, or you can always attach resources later.', 'piklist-demo'),
  // 'help'        => 'This is help text.',
  'fields' => array(
    array(
      'type'    => 'text',
      'scope'   => 'connect_resource_to_topic',
      'field'   => 'post_title',
      'label'   => __('Title', 'piklist-demo'),
      'columns' => '12',
    ),
    array(
      'type'    => 'editor',
      'scope'   => 'connect_resource_to_topic',
      'field'   => 'post_content',
      'label'   => __('Content', 'piklist-demo'),
      'columns' => '12',
    ),
    array(
      'type'  => 'file',
      'scope' => 'connect_resource_to_topic',
      'field' => 'resource_file',
      'label' => __('Upload File', 'piklist-demo'),
    ),
  ),
));

// piklist('field', array(
  // 'scope' => 'connect_resource_to_topic',
  // 'type'  => 'submit',
  // 'field' => 'submit',
  // 'value' => 'Submit',
// ));