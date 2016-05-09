<?php
/**
 * Title: General Settings
 * Setting: wampum_settings
 * Order: 10
 */

// This was disabled after 0.0.63 beta when we decided to go with default Woo account dashboard
piklist('field', array(
    'type'     => 'select',
    'field'    => 'account_page',
    'label'    => __('Account Page', 'wampum'),
    'choices'  => piklist(
        get_posts(
            array(
                'post_type' => 'page',
                'orderby'   => 'post_title',
            ),
            'objects'
        ),
        array(
            'ID',
            'post_title',
        )
    ),
));
