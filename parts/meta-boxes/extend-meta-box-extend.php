<?php
/**
 * Flow: Lesson
 * Tab: My Tab
 * Title: My Flow Custom Fields
 * Post Type: wampum_lesson
 */

// 1st Flow field
piklist('field', array(
'type' => 'text'
,'field' => 'field_one'
,'label' => 'Flow First Field'

));

// 2nd flow field colour picker
piklist('field', array(
'type' => 'colorpicker'
,'field' => 'field_two'
,'label' => 'Flow Second Field'
));