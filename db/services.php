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
        'description' => 'get ungraded submission items that require manaul grading',
        'type'        => 'read',
        'ajax'        => true,
    ],
    'local_grades_get_activity_reports' => [
        'classname'   => 'local_grades\external\get_activity_reports',
        'description' => "get students' course activity data",
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
    ],
];

// A dedicated external service so you can scope a token to just the essay
// grading function rather than granting a token access to every enabled
// web service function in this plugin.
$services = [
    'Grade Quiz Essays Web Service' => [
        'functions'       => ['local_grades_set_essay_grade'],
        'restrictedusers' => 1,     // Requires explicit user authorisation for this service.
        'enabled'         => 1,
        'shortname'       => 'grade_quiz_essays_ws',
        'downloadfiles'   => 0,
        'uploadfiles'     => 0,
    ],
    'Grades & Reports Web Service' => [
        'functions'       => ['local_grades_get_activity_reports', 'local_grades_get_ungraded_submissions'],
        'restrictedusers' => 1,     // Requires explicit user authorisation for this service.
        'enabled'         => 1,
        'shortname'       => 'grades_and_reports_ws',
        'downloadfiles'   => 0,
        'uploadfiles'     => 0,
    ],
    'Grade Assignments Web Service' => [
        'functions'       => ['mod_assign_save_grade'],
        'restrictedusers' => 1,     // Requires explicit user authorisation for this service.
        'enabled'         => 1,
        'shortname'       => 'grade_assignments_ws',
        'downloadfiles'   => 0,
        'uploadfiles'     => 0,
    ],
];