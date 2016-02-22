<?php
/**
 * Title: Community
 * Post Type: wc_membership_plan
 */

// TODO: Check a setting on whether site uses Communities or not
// piklist('field', array(
//     'type'        => 'select',
//     'scope'       => 'taxonomy',
//     'field'       => 'wampum_community',
//     'label'       => 'Community',
//     'choices' => array(
//         '' => 'Choose'
//         ) + piklist(get_terms('wampum_community', array(
//             'hide_empty' => false,
//         )), array(
//             'term_id',
//             'name',
//         )
//     ),
//     'required' => true,
// ));