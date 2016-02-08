<?php
/*
Title: New Topic
Method: post
Logged in: true
Redirect: /new-topic/
*/
// Message: Data saved in Piklist Demos, under the Validation tab.

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

/**
 * The shortcode for this form is:
 * [piklist_form form="new-post-with-validation" add_on="piklist-demos"]
 */

/**
 * The fields in this form are exactly like the fields in piklist-demos/parts/meta-boxes/field-validate.php
 * Only the 'scope' paramater needed to be added.
 */

// Post Type
piklist('field', array(
    'type'  => 'hidden',
    'scope' => 'post',
    'field' => 'post_type',
    'value' => 'wampum_step',
));

// Status
piklist('field', array(
  'type'  => 'hidden',
  'scope' => 'post',
  'field' => 'post_status',
  'value' => 'publish',
));

// Title
piklist('field', array(
  'type'       => 'text',
  'scope'      => 'post',
  'field'      => 'post_title',
  'label'      => __('Title', 'piklist-demo'),
  // 'attributes' => array(
    // 'wrapper_class' => 'post_title',
    // 'style'         => 'width: 100%',
  // ),
  'required' => true,
));

// Content
piklist('field', array(
  'type'    => 'editor',
  'scope'   => 'post',
  'field'   => 'post_content',
  'label'   => __('Editor Content'),
  'options' => array (
    'wpautop'          => true,
    'drag_drop_upload' => true,
    'tabindex'         => '',
    'editor_css'       => '',
    'editor_class'     => '',
    // 'media_buttons'    => true,
    'teeny'            => false,
    'dfw'              => false,
    'tinymce'          => array(
      'toolbar1' => 'formatselect, bold, italic, underline, blockquote, strikethrough, bullist, numlist, undo, redo, link, unlink, fullscreen',
    ),
  ),
  'sanitize' => array(
    array( 'type' => 'wp_kses_post' )
  ),
  'required' => true,
  'value'    => '',
));

  // piklist('field', array(
  //   'type' => 'text'
  //   ,'scope' => 'post_meta' // scope needs to be set on EVERY field for front-end forms.
  //   ,'field' => 'validate_text_required'
  //   ,'label' => __('Text Required', 'piklist-demo')
  //   ,'description' => "required => true"
  //   ,'attributes' => array(
  //     'wrapper_class' => 'validate_text_required'
  //     ,'placeholder' => __('Enter text or this page won\'t save.', 'piklist-demo')
  //   )
  //   ,'required' => true
  //   ,'validate' => array(
  //     array(
  //       'type' => 'limit'
  //       ,'options' => array(
  //         'min' => 2
  //         ,'max' => 6
  //         ,'count' => 'characters'
  //       )
  //     )
  //   )
  // ));


  // piklist('field', array(
  //   'type' => 'text'
  //   ,'scope' => 'post_meta' // scope needs to be set on EVERY field for front-end forms.
  //   ,'label' => __('File Name', 'piklist-demo')
  //   ,'field' => 'sanitize_file_name'
  //   ,'description' => __('Converts multiple words to a valid file name', 'piklist-demo')
  //   ,'sanitize' => array(
  //     array(
  //       'type' => 'file_name'
  //     )
  //   )
  //   ,'attributes' => array(
  //     'wrapper_class' => 'sanitize_file_name'
  //     ,'class' => 'large-text'
  //   )
  // ));

  // piklist('field', array(
  //   'type' => 'text'
  //   ,'scope' => 'post_meta' // scope needs to be set on EVERY field for front-end forms.
  //   ,'field' => 'validate_emaildomain'
  //   ,'label' => __('Email address', 'piklist-demo')
  //   ,'description' => __('Validate Email and Email Domain', 'piklist-demo')
  //   ,'attributes' => array(
  //     'wrapper_class' => 'validate_emaildomain'
  //     ,'class' => 'large-text'
  //   )
  //   ,'validate' => array(
  //     array(
  //       'type' => 'email'
  //     )
  //     ,array(
  //       'type' => 'email_domain'
  //     )
  //   )
  // ));

  // piklist('field', array(
  //   'type' => 'text'
  //   ,'scope' => 'post_meta' // scope needs to be set on EVERY field for front-end forms.
  //   ,'field' => 'validate_file_exists'
  //   ,'label' => __('File exists?', 'piklist-demo')
  //   ,'description' => sprintf(__('Test with: %s', 'piklist-demo'), 'http://wordpress.org/plugins/about/readme.txt')
  //   ,'attributes' => array(
  //     'wrapper_class' => 'validate_file_exists'
  //     ,'class' => 'large-text'
  //   )
  //   ,'validate' => array(
  //     array(
  //       'type' => 'file_exists'
  //     )
  //   )
  // ));

  // piklist('field', array(
  //   'type' => 'text'
  //   ,'scope' => 'post_meta' // scope needs to be set on EVERY field for front-end forms.
  //   ,'field' => 'validate_image'
  //   ,'label' => __('Image')
  //   ,'description' => sprintf(__('Test with: %s', 'piklist-demo'), 'http://piklist.com/wp-content/themes/piklistcom-base/images/piklist-logo@2x.png')
  //   ,'attributes' => array(
  //     'wrapper_class' => 'validate_image'
  //     ,'class' => 'large-text'
  //   )
  //   ,'validate' => array(
  //     array(
  //       'type' => 'image'
  //     )
  //   )
  // ));

  // piklist('field', array(
  //   'type' => 'checkbox'
  //   ,'scope' => 'post_meta' // scope needs to be set on EVERY field for front-end forms.
  //   ,'field' => 'validate_checkbox_limit'
  //   ,'label' => __('Checkbox', 'piklist-demo')
  //   ,'description' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.'
  //   ,'value' => 'third'
  //   ,'choices' => array(
  //     'first' => __('First Choice', 'piklist-demo')
  //     ,'second' => __('Second Choice', 'piklist-demo')
  //     ,'third' => __('Third Choice', 'piklist-demo')
  //   )
  //   ,'required' => true
  //   ,'validate' => array(
  //     array(
  //       'type' => 'limit'
  //       ,'options' => array(
  //         'min' => 2
  //         ,'max' => 2
  //       )
  //     )
  //   )
  //   ,'attributes' => array(
  //     'wrapper_class' => 'validate_checkbox_limit'
  //   )
  // ));

  // piklist('field', array(
  //   'type' => 'file'
  //   ,'scope' => 'post_meta' // scope needs to be set on EVERY field for front-end forms.
  //   ,'field' => 'validate_upload_media_limit'
  //   ,'label' => __('Add File(s)', 'piklist-demo')
  //   ,'description' => __('No more than one file is allowed', 'piklist-demo')
  //   ,'required' => true
  //   ,'options' => array(
  //     'modal_title' => __('Add File(s)', 'piklist-demo')
  //     ,'button' => __('Add', 'piklist-demo')
  //   )
  //   ,'attributes' => array(
  //     'wrapper_class' => 'validate_upload_media_limit'
  //     ,'class' => 'large-text'
  //   )
  //   ,'validate' => array(
  //     array(
  //       'type' => 'limit'
  //       ,'options' => array(
  //         'min' => 0
  //         ,'max' => 1
  //       )
  //     )
  //   )
  // ));

// piklist('field', array(
  // 'type'        => 'group',
  // 'scope'       => 'new_resources',
  // 'field'       => 'add_new_resources',
  // 'add_more'    => true,
  // 'label'       => __('Add New Resources', 'piklist-demo'),
  // 'description' => __('Add resources now, or you can always attach resources later.', 'piklist-demo'),
  // 'fields' => array(
    // array(
      // 'type'       => 'text',
      // 'field'      => 'resource_title',
      // 'label'      => __('Resource Title', 'piklist-demo'),
    // ),
    // array(
      // 'type'       => 'file',
      // 'field'      => 'resource_file',
      // 'label'      => __('Upload File', 'piklist-demo'),
    // ),
  // ),
// ));



piklist('field', array(
  'scope' => 'post',
  'type'  => 'submit',
  'field' => 'submit',
  'value' => 'Submit',
));
