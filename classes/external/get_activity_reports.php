<?php
namespace local_grades\external;

use core_external\external_function_parameters;
use core_external\external_multiple_structure;
use core_external\external_single_structure;
use core_external\external_value;
use local_grades\helpers\dataquery_helper;
use local_grades\helpers\tag_helper;

class get_activity_reports extends \core_external\external_api {

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
                'groupname' => new external_value(PARAM_TEXT, 'group name'),
                'userid' => new external_value(PARAM_INT, 'user id'),
                'firstname' => new external_value(PARAM_TEXT, "learner's first name"),
                'lastname' => new external_value(PARAM_TEXT, "learner's last name"),
                'activitytype' => new external_value(PARAM_TEXT, 'type of activity (assign, quiz, forum)'),
                'activityname' => new external_value(PARAM_TEXT, 'activity name'),
                'grade' => new external_value(PARAM_FLOAT, "learner's grade for activity"),
                'duedate' => new external_value(PARAM_INT, "activity due date timestamp"),
                'submissiondate' => new external_value(PARAM_INT, "submission date timestamp"),
                'submissionstatus' => new external_value(PARAM_TEXT, "submission status"),
            ])
        );
    }

    public static function execute() {
        global $DB;

        // Resolve Course Tag: Only process courses marked with this specific tag.
        $course_tagname = get_config('local_grades', 'course_report_tag');
        $course_tagid = tag_helper::get_tagid($course_tagname);
        if (!$course_tagname || !$course_tagid) return [];

        // Resolve Activity Tag: Only include grades for activities marked with this tag.
        $activity_tagname = get_config('local_grades', 'activity_report_tag');
        $activity_tagid = tag_helper::get_tagid($activity_tagname);
        if (!$activity_tagname || !$activity_tagid) return [];

        // Identify Course Scope: Get IDs of all courses currently using the course tag.
        $courseids = tag_helper::get_tagged_courses_ids($course_tagid);
        if (empty($courseids)) return [];

        // Group Filtering: Retrieve specific group IDs from plugin settings if configured.
        $groupstr = get_config('local_grades', 'activity_report_groups');
        $groupids = !empty($groupstr) ? explode(',', $groupstr) : [];

        $records = dataquery_helper::get_activity_data($courseids, $groupids, [$activity_tagid]);

        return $records;
    }
}