<?php
global $wampum_members, $wampum_programs;
$programs = $wampum_members->get_programs( get_current_user_id() );

foreach ( $programs as $program ) {
    echo '<div class="">';
    echo '<a href="' . $wampum_programs->get_link($program) . '">' . $program->name . '</a>';
    echo '</div>';
}