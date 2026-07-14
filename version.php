<?php

/**
 * Version details for local_grades.
 *
 * @package    local_grades
 * @copyright  2026 Your Organisation
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$plugin->component = 'local_grades';
$plugin->version   = 2026071300;      // YYYYMMDDXX — bumped for merge
$plugin->requires  = 2024100700;      // Moodle 4.5+ — raised to match set_essay_grade.php's API usage (core_external\, mod_quiz\quiz_attempt, manual_grade())
$plugin->maturity  = MATURITY_BETA;
$plugin->release   = '0.2.0';

// Note: the essay-grading functionality in this plugin has been built and
// tested end-to-end against a live Moodle 5.3dev (build 20260624) instance —
// it reflects current APIs (mod_quiz\quiz_attempt namespace, core_external\
// classes, question_usage_by_activity::manual_grade(), and
// $quizobj->get_grade_calculator()->recompute_final_grade()), not older
// documentation. See README.md's "Notes" section for the full list of API
// differences encountered. Since 5.3 is an active dev branch (code freeze
// 24 Aug 2026), a future build could still shift things further.