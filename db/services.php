<?php
// This file is part of Moodle - http://moodle.org/

/**
 * Web service definitions for local_grades.
 *
 * @package    local_grades
 * @copyright  2026 Your Organisation
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$functions = [
    'local_grades_get_ungraded_submissions' => [
        'classname'   => 'local_grades\external\get_ungraded_submissions',
        'description' => 'Get ungraded submission items that require manual grading.',
        'type'        => 'read',
        'ajax'        => true,
    ],

    'local_grades_get_activity_reports' => [
        'classname'   => 'local_grades\external\get_activity_reports',
        'description' => 'Get students course activity data.',
        'type'        => 'read',
        'ajax'        => true,
    ],

    'local_grades_set_essay_grade' => [
        'classname'    => 'local_grades\external\set_essay_grade',
        'methodname'   => 'execute',
        'classpath'    => '',
        'description'  => 'Grade a single essay question slot within a quiz attempt and update the gradebook.',
        'type'         => 'write',
        'ajax'         => false,
        'capabilities' => 'local/grades:setessaygrade',
    ],
];

$services = [
    'AI Grades Service' => [
        'functions'       => ['local_grades_set_essay_grade'],
        'restrictedusers' => 1,
        'enabled'         => 1,
        'shortname'       => 'local_grades_service',
        'downloadfiles'   => 0,
        'uploadfiles'     => 0,
    ],

    'Grades and Reports Web Service' => [
        'functions'       => [
            'local_grades_get_activity_reports',
            'local_grades_get_ungraded_submissions',
        ],
        'restrictedusers' => 1,
        'enabled'         => 1,
        'shortname'       => 'grades_and_reports_ws',
        'downloadfiles'   => 0,
        'uploadfiles'     => 0,
    ],
];
