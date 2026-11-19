<?php

/**
 * Helper class for aggregating complex learner performance data.
 *
 * This class compiles data from several Moodle subsystems (Grades, Groups,
 * Activities, and Tags) into a unified dataset.
 *
 * @package     local_grades
 * @subpackage  helpers
 * @copyright   2026 Your Name/Organization
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_grades\helpers;

defined('MOODLE_INTERNAL') || die();

/**
 * dataquery_helper class.
 *
 * Provides methods to extract learner performance metrics including
 * grades, submission timeliness, and activity metadata.
 */
class dataquery_helper {

    /**
     * Compiles detailed grade and submission data for learners.
     *
     * @param array $courseids List of course IDs to include.
     * @param array $groupids List of group IDs to filter learners by.
     * @param array $activity_tagids List of activity tag IDs.
     * @return \stdClass[] Activity data records.
     */
    public static function get_activity_data(
        array $courseids,
        array $groupids,
        array $activity_tagids
    ) {
        global $DB;

        if (empty($courseids) || empty($activity_tagids)) {
            return [];
        }

        list($courseinsql, $courseparams) = $DB->get_in_or_equal(
            $courseids,
            SQL_PARAMS_NAMED,
            'courseid'
        );

        list($taginsql, $tagparams) = $DB->get_in_or_equal(
            $activity_tagids,
            SQL_PARAMS_NAMED,
            'tagid'
        );

        $params = array_merge($courseparams, $tagparams);
        $groupfilter = '';

        // Apply group filtering only when specific groups are selected.
        if (!empty($groupids)) {
            list($groupinsql, $groupparams) = $DB->get_in_or_equal(
                $groupids,
                SQL_PARAMS_NAMED,
                'groupid'
            );

            $groupfilter = " AND g.id $groupinsql";
            $params = array_merge($params, $groupparams);
        }

        // Moodle stores dates as Unix timestamps.
        $params['now1'] = time();
        $params['now2'] = $params['now1'];
        $params['now3'] = $params['now1'];

        $sql = "
            SELECT
                CONCAT(c.id, '-', g.id, '-', u.id, '-', cm.id) AS id,
                c.fullname AS coursename,
                g.name AS groupname,
                u.id AS userid,
                u.firstname,
                u.lastname,

                CASE
                    WHEN m.name = 'assign' THEN 'assignment'
                    ELSE m.name
                END AS activitytype,

                CASE
                    WHEN m.name = 'assign' THEN a.name
                    WHEN m.name = 'quiz' THEN q.name
                    WHEN m.name = 'forum' THEN f.name
                END AS activityname,

                CASE
                    WHEN m.name = 'assign' THEN ao.duedate
                    WHEN m.name = 'quiz' THEN qo.timeclose
                    WHEN m.name = 'forum' THEN f.duedate
                END AS duedate,

                CASE
                    WHEN m.name = 'assign'
                        AND gi_assign.grademax > 0
                        THEN ROUND(
                            (gg_a.finalgrade / gi_assign.grademax) * 100,
                            2
                        )
                    WHEN m.name = 'quiz'
                        AND gi_quiz.grademax > 0
                        THEN ROUND(
                            (gg_q.finalgrade / gi_quiz.grademax) * 100,
                            2
                        )
                    WHEN m.name = 'forum' AND fp.discussion > 0
                        THEN 100
                END AS grade,

                CASE
                    WHEN m.name = 'assign' THEN a_s.timemodified
                    WHEN m.name = 'quiz' THEN qa.timemodified
                    WHEN m.name = 'forum' THEN fp.created
                END AS submissiondate,

                CASE
                    WHEN m.name = 'assign' THEN
                        CASE
                            WHEN a_s.timemodified IS NULL
                                AND ao.duedate > 0
                                AND ao.duedate < :now1
                                THEN 'missed'
                            WHEN ao.duedate = 0
                                AND a_s.timemodified > 0
                                THEN 'ontime'
                            WHEN ao.duedate > 0
                                AND a_s.timemodified > ao.duedate
                                THEN 'late'
                            WHEN a_s.timemodified > 0
                                THEN 'ontime'
                            ELSE 'pending'
                        END

                    WHEN m.name = 'quiz' THEN
                        CASE
                            WHEN qa.timemodified IS NULL
                                AND qo.timeclose > 0
                                AND qo.timeclose < :now2
                                THEN 'missed'
                            WHEN qo.timeclose = 0
                                AND qa.timemodified > 0
                                THEN 'ontime'
                            WHEN qo.timeclose > 0
                                AND qa.timemodified > qo.timeclose
                                THEN 'late'
                            WHEN qa.timemodified > 0
                                THEN 'ontime'
                            ELSE 'pending'
                        END

                    WHEN m.name = 'forum' THEN
                        CASE
                            WHEN fp.created IS NULL
                                AND f.duedate > 0
                                AND f.duedate < :now3
                                THEN 'missed'
                            WHEN f.duedate = 0
                                AND fp.created > 0
                                THEN 'ontime'
                            WHEN f.duedate > 0
                                AND fp.created > f.duedate
                                THEN 'late'
                            WHEN fp.created > 0
                                THEN 'ontime'
                            ELSE 'pending'
                        END
                END AS submissionstatus

            FROM {groups} g

            JOIN {groups_members} gm
                ON gm.groupid = g.id

            JOIN {user} u
                ON u.id = gm.userid

            JOIN {course} c
                ON c.id = g.courseid

            JOIN {course_modules} cm
                ON cm.course = c.id

            JOIN {tag_instance} ti
                ON ti.itemid = cm.id
                AND ti.itemtype = 'course_modules'

            JOIN {tag} t
                ON t.id = ti.tagid

            JOIN {modules} m
                ON m.id = cm.module

            LEFT JOIN {assign} a
                ON a.id = cm.instance

            LEFT JOIN {assign_overrides} ao
                ON ao.assignid = a.id
                AND ao.groupid = g.id

            LEFT JOIN {quiz} q
                ON q.id = cm.instance

            LEFT JOIN {quiz_overrides} qo
                ON qo.quiz = q.id
                AND qo.groupid = g.id

            LEFT JOIN {assign_submission} a_s
                ON a_s.assignment = a.id
                AND a_s.userid = u.id
                AND a_s.latest = 1

            LEFT JOIN {grade_items} gi_assign
                ON gi_assign.iteminstance = a.id
                AND gi_assign.itemmodule = 'assign'
                AND gi_assign.courseid = c.id

            LEFT JOIN {grade_grades} gg_a
                ON gg_a.itemid = gi_assign.id
                AND gg_a.userid = u.id

            LEFT JOIN {quiz_attempts} qa
                ON qa.quiz = q.id
                AND qa.userid = u.id
                AND qa.preview = 0
                AND qa.state = 'finished'

            LEFT JOIN {grade_items} gi_quiz
                ON gi_quiz.iteminstance = q.id
                AND gi_quiz.itemmodule = 'quiz'
                AND gi_quiz.courseid = c.id

            LEFT JOIN {grade_grades} gg_q
                ON gg_q.itemid = gi_quiz.id
                AND gg_q.userid = u.id

            LEFT JOIN {forum} f
                ON f.id = cm.instance

            LEFT JOIN {forum_discussions} fd
                ON fd.forum = f.id

            LEFT JOIN {forum_posts} fp
                ON fp.discussion = fd.id
                AND fp.userid = u.id

            WHERE c.id $courseinsql
                $groupfilter
                AND m.name IN ('quiz', 'assign', 'forum')
                AND t.id $taginsql
                AND u.suspended = 0

            ORDER BY u.firstname, u.lastname
        ";

        return $DB->get_records_sql($sql, $params);
    }
}
