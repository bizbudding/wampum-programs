<?php
/**
 * Title: Programs
 * Post Type: wampum_step
 * Capability: edit_posts
 * Context: side
 * Priority: default
 * Meta box: true
 */

/**
 * Allow sites to modify the field type (radio, select)
 * Only allow 1 choice!
 */
$metabox_type = apply_filters( 'wampum_program_metabox_type', 'radio' );

piklist('field', array(
    'type'        => $metabox_type,
    // 'scope'       => 'taxonomy',
    // 'template'    => 'theme',
    'field'       => 'wampum-program',
    // 'label'       => 'Programs',
    // 'description' => 'Terms will appear when they are added to this taxonomy.',
    'choices'     => array(
        '' => 'Choose Term'
            ) + piklist(get_terms('wampum_program', array(
            'hide_empty' => false,
        )), array(
            'term_id',
            'name',
        )
    ),
    'position' => 'wrap',
));