<?php
/**
 * Title: Extend WC Membership Metabox
 * Post Type: wc_membership_plan
 */

piklist('field', array(
    'type'        => 'select',
    'scope'       => 'taxonomy',
    'field'       => 'wampum_program',
    'label'       => 'Programs',
    // 'description' => 'Terms will appear when they are added to this taxonomy.',
    // 'choices'     => array(
    //     '' => 'Choose Term'
    //         ) + piklist(get_terms('wampum_program', array(
    //         'hide_empty' => false,
    //     )), array(
    //         'term_id',
    //         'name',
    //     )
    // ),
    'choices' => array(
        '' => 'Choose'
		) + piklist(get_terms('wampum_program', array(
            'hide_empty' => false,
        )), array(
            'term_id',
            'name',
        )
    ),
    'required' => true,
));