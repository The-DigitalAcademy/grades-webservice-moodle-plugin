<?php
namespace local_grades\external;

use core_external\external_function_parameters;
use core_external\external_multiple_structure;
use core_external\external_single_structure;
use core_external\external_value;

class get_ungraded_submissions extends \core_external\external_api {

    /**
     * Returns description of method parameters
     * @return external_function_parameters
     */
    public static function execute_parameters(): external_function_parameters {
        return new external_function_parameters([]);
    }

    public static function execute_returns() {
        return new external_multiple_structure(
            new external_single_structure([
                'id' => new external_value(PARAM_TEXT, 'item id'),
                'coursename' => new external_value(PARAM_TEXT, 'course name'),
                'activitytype' => new external_value(PARAM_TEXT, 'type of activity (assign, quiz, forum)'),
                'activityname' => new external_value(PARAM_TEXT, 'activity name'),
                'username' => new external_value(PARAM_TEXT, "learner's name"),
                'timemodified' => new external_value(PARAM_INT, "submission modification timestamp"),
                'coursepath' => new external_value(PARAM_TEXT, "url path to course"),
                'activitypath' => new external_value(PARAM_TEXT, "url path to activity"),
                'gradepath' => new external_value(PARAM_TEXT, "url path to the submission's grading interface"),

            ])
        );
    }

    public static function execute() {
        global $DB;
        
        $assign_sql = "
            SELECT
                CONCAT('a', asub.id) as id,
                asub.timemodified,
                a.name as activityname,
                'assign' as activitytype,
                c.fullname as coursename,
                ag.grade,
                ag.attemptnumber,
                CONCAT(u.firstname, ' ', u.lastname) as username,
                CONCAT('/course/view.php', CHAR(63), 'id=', c.id) as coursepath,
                CONCAT('/mod/assign/view.php', CHAR(63), 'id=', cm.id) as activitypath,
                CONCAT('/mod/assign/view.php', CHAR(63), 'id=', cm.id, '&action=grader&userid=', u.id) as gradepath
            FROM {assign_submission} asub
            JOIN {assign} a
                ON a.id = asub.assignment
            JOIN {course} c
                ON c.id = a.course
            JOIN {user} u
                ON u.id = asub.userid
            JOIN {course_modules} cm
                ON cm.instance = a.id
            JOIN {modules} m
                ON m.id = cm.module
                AND m.name = 'assign'
            LEFT JOIN {assign_grades} ag
                ON ag.assignment = asub.assignment
                AND ag.userid = asub.userid
            WHERE asub.status = 'submitted' AND (ag.grade IS NULL OR ag.grade < 0)
            ORDER BY asub.id
        ";


        $assign_records = $DB->get_records_sql($assign_sql);

        $quiz_sql = "
            SELECT 
                CONCAT('q', quiza.id) as id,
                c.fullname as coursename,
                'quiz' as activitytype,
                q.name as activityname,
                CONCAT(u.firstname, ' ', u.lastname) as username,
                quiza.timemodified,
                CONCAT('/course/view.php', CHAR(63), 'id=', c.id) as coursepath,
                CONCAT('/mod/quiz/view.php', CHAR(63), 'id=', cm.id) as activitypath,
                CONCAT('/mod/quiz/review.php', CHAR(63), 'attempt=', quiza.id) as gradepath
            FROM {quiz_attempts} quiza
            JOIN {question_usages} questu
                ON questu.id = quiza.uniqueid
            JOIN {question_attempts} questa
                ON questa.questionusageid = questu.id
                AND questa.behaviour = 'manualgraded'
            JOIN {quiz} q
                ON q.id = quiza.quiz
            JOIN {course_modules} cm
                ON cm.instance = q.id
            JOIN {modules} m
                ON m.id = cm.module
                AND m.name = 'quiz'
            JOIN {course} c
                ON c.id = q.course
            JOIN {user} u
                ON u.id = quiza.userid
            ORDER BY quiza.id
        ";

        $quiz_records = $DB->get_records_sql($quiz_sql);

        return array_merge(array_values($assign_records), array_values($quiz_records));
    }
}