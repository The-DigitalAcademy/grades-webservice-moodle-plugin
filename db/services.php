<?php

defined('MOODLE_INTERNAL') || die();

$functions = [
    'local_grades_get_ungraded_submissions' => [
        'classname' => 'local_grades\external\get_ungraded_submissions',
        'description' => 'get ungraded submission items that require manaul grading',
        'type' => 'read',
        'ajax' => true
    ],
];