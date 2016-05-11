<?php
/**
 * Title: Add Step Resources
 * Post Type: wampum_step
 * Capability: edit_posts
 * Context: normal
 * Priority: default
 * Meta box: true
 */

// Enqueue our scripts (previously registered)
wp_enqueue_script('wampum-select2');
wp_enqueue_style('wampum-select2');

piklist('field', array(
  'type'     => 'html',
  'template' => 'field',
  'value'    => __('Attach resources to this step.', 'wampum'),
));

// Topic ID
piklist('field', array(
  'type'  => 'hidden',
  'scope' => 'connect_resource_to_step',
  'field' => 'step_id',
  'value' => get_the_ID(),
));

piklist('field', array(
    'type'     => 'select',
    'scope'    => 'connect_resource_to_step',
    'field'    => 'existing_resources',
    'template' => 'theme',
    'label'    => __('Add Existing Resources', 'wampum'),
    'choices'  => piklist(
        get_posts(
            array(
                'post_type' => 'wampum_resource',
                'orderby'   => 'post_date',
            ),
            'objects'
        ),
        array(
            'ID',
            'post_title',
        )
    ),
    'attributes' => array(
        'class'    => 'wampum-select2',
        'multiple' => 'multiple',
    ),
));

// Resources
piklist('field', array(
    'type'     => 'group',
    'scope'    => 'connect_resource_to_step',
    'field'    => 'add_resource',
    'template' => 'theme',
    'add_more' => true,
    'label'    => __('Add New Resources', 'wampum'),
    'fields'   => array(
        array(
            'type'        => 'text',
            'scope'       => 'connect_resource_to_step',
            'field'       => 'post_title',
            // 'template'    => 'theme',
            'label'       => __('Title', 'wampum'),
            // 'description' => __('Title is required when adding new resources', 'wampum'),
            'columns'     => '12',
        ),
        array(
            'type'    => 'editor',
            'scope'   => 'connect_resource_to_step',
            'field'   => 'post_content',
            'label'   => __('Content', 'wampum'),
            'columns' => '12',
        ),
        array(
            'type'     => 'file',
            'scope'    => 'connect_resource_to_step',
            'field'    => 'resource_files',
            'label'    => __('Upload File(s)', 'wampum'),
            // 'validate' => array(
            //     array(
            //         'type' => 'file_exists',
            //     ),
            // ),
            // 'options' => array(
            //     'basic' => true,
            // ),
        ),
    ),
));

// piklist('field', array(
  // 'scope' => 'connect_resource_to_step',
  // 'type'  => 'submit',
  // 'field' => 'submit',
  // 'value' => 'Submit',
// ));