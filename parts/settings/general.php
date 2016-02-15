<?php
/**
 * Title: General Settings
 * Setting: wampum_settings
 * Order: 10
 */

piklist('field', array(
    'type'     => 'select',
    'field'    => 'account_page',
    'label'    => __('Wampum Account Page', 'wampum'),
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