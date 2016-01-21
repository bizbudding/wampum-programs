<?php
/*
Title: Edit Profile
Method: post
Logged in: true
Message: User Profile Saved.
*/


/**
 * Piklist forms automatically generate a shortcode:
 *
 * If your form is in a PLUGIN (i.e. wp-content/plugins/my-plugin/parts/forms/my-form.php)
 * Use [piklist_form form="my-form" add_on="my-plugin"]
 *
 * If your form is in a THEME (i.e. wp-content/themes/my-theme/piklist/parts/forms/my-form.php)
 * Use [piklist_form form="my-form" add_on="theme"]
 *
 * The "form" parameter is the file name of your form without ".php".
 *
 */

/**
 * The shortcode for this form is:
 * [piklist_form form="edit-profile" add_on="piklist-demos"]
 */

// piklist('field', array(
//   'type'       => 'text',
//   'scope'      => 'user', // user_login is in the wp_users table, so scope is: user
//   'field'      => 'user_login',
//   'label'      => __('User login', 'piklist-demo'),
//   'attributes' => array(
//     'autocomplete'  => 'off',
//     'wrapper_class' => 'user_login'
//   ),
// ));

piklist('field', array(
  'type'   => 'group',
  'scope'  => 'wampum_reset_password',
  'fields' => array(
    array(
      'type'       => 'password',
      'scope'      => 'user',
      'field'      => 'user_pass',
      'label'      => __('New Password', 'piklist-demo'),
      'columns'    => '6',
      'value'      => false, // Setting to false forces no value to show in form.,
      'attributes' => array(
        'autocomplete'  => 'off',
        'wrapper_class' => 'user_pass',
      ),
    ),
    array(
      'type'     => 'password',
      'scope'    => 'user',
      'field'    => 'user_pass_repeat',
      'label'    => __('Repeat New Password', 'piklist-demo'),
      'columns'  => '6',
      'value'    => false, // Setting to false forces no value to show in form.
      'validate' => array(
        array(
          'type'    => 'match',
          'options' => array(
            'field' => 'user_pass',
          ),
        ),
      ),
      'attributes' => array(
        'wrapper_class' => 'user_pass_repeat',
      ),
    ),
  ),
));

  piklist('field', array(
    'type' => 'password'
    ,'scope' => 'user'
    ,'field' => 'user_pass'
    ,'label' => __('New Password', 'piklist-demo')
    ,'position' => 'start'
    ,'columns' => '6'
    ,'value' => false // Setting to false forces no value to show in form.
    ,'attributes' => array(
      'autocomplete' => 'off'
      ,'wrapper_class' => 'user_pass'
    )
  ));

  piklist('field', array(
    'type' => 'password'
    ,'scope' => 'user'
    ,'field' => 'user_pass_repeat'
    ,'label' => __('Repeat New Password', 'piklist-demo')
    ,'position' => 'end'
    ,'columns' => '6'
    ,'value' => false // Setting to false forces no value to show in form.
    ,'validate' => array(
      array(
        'type' => 'match'
        ,'options' => array(
          'field' => 'user_pass'
        )
      )
    )
    ,'attributes' => array(
      'wrapper_class' => 'user_pass_repeat'
    )
  ));

piklist('field', array(
  'type'   => 'group',
  'scope'  => 'user_meta',
  'fields' => array(
    array(
      'type'       => 'text',
      'field'      => 'first_name',
      'label'      => __('First name', 'piklist-demo'),
      'columns'    => '6',
      'attributes' => array(
        'wrapper_class' => 'first_name',
      ),
    ),
    array(
      'type'       => 'text',
      'field'      => 'last_name',
      'label'      => __('Last name', 'piklist-demo'),
      'columns'    => '6',
      'attributes' => array(
        'wrapper_class' => 'last_name'
      ),
    ),
  ),
));

// piklist('field', array(
//   'type'       => 'text',
//   'scope'      => 'user_meta',
//   'field'      => 'first_name',
//   'label'      => __('First name', 'piklist-demo'),
//   'attributes' => array(
//     'wrapper_class' => 'first_name',
//   ),
// ));

// piklist('field', array(
//   'type'       => 'text',
//   'scope'      => 'user_meta',
//   'field'      => 'last_name',
//   'label'      => __('Last name', 'piklist-demo'),
//   'attributes' => array(
//     'wrapper_class' => 'last_name'
//   ),
// ));

piklist('field', array(
  'type' => 'text',
  'scope' => 'user',
  'field' => 'display_name',
  'label' => __('Display name', 'piklist-demo'),
  'attributes' => array(
    'wrapper_class' => 'display_name'
  ),
));

?>

<h3><?php _e('Contact Info'); ?></h3>

<?php

piklist('field', array(
  'type'     => 'text',
  'scope'    => 'user',
  'field'    => 'user_email',
  'label'    => __('Email', 'piklist-demo'),
  'required' => true,
  'validate' => array(
    array(
      'type' => 'email_exists',
    ),
    array(
      'type' => 'email',
    ),
    array(
      'type' => 'email_domain',
    ),
  ),
  'attributes' => array(
    'wrapper_class' => 'user_email',
  ),
));

piklist('field', array(
  'type'     => 'text',
  'scope'    => 'user',
  'field'    => 'user_url',
  'label'    => __('Website', 'piklist-demo'),
  'validate' => array(
    array(
      'type' => 'url',
    ),
  ),
  'attributes' => array(
    'wrapper_class' => 'user_url',
  ),
));

piklist('field', array(
  'type'       => 'textarea',
  'scope'      => 'user_meta',
  'field'      => 'description',
  'label'      => __('Short Bio', 'piklist-demo'),
  'attributes' => array(
    'rows'          => '4',
    'wrapper_class' => 'description'
  ),
));

// Submit button
piklist('field', array(
  'type'  => 'submit',
  'field' => 'submit',
  'value' => 'Submit',
));
