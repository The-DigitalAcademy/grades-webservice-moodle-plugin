<?php

defined('MOODLE_INTERNAL') || die();

global $DB;

$groups = $DB->get_records_menu('groups', [], 'name ASC', 'id, name');

if (empty($groups)) {
    $groups = [0 => 'no groups found'];
}

if ($ADMIN->fulltree) {

    // Add a new settings page for the local plugin.
    $settings = new admin_settingpage('local_grades', get_string('pluginname', 'local_grades'));

    // Create a new settings category under 'Local plugins' if it doesn't exist
    $ADMIN->add('localplugins', $settings);

    // Setting for the course report tag
    $settings->add(new admin_setting_configtext(
        'local_grades/course_report_tag',
        get_string('course_report_tag', 'local_grades'),
        get_string('course_report_tag_desc', 'local_grades'),
        'course_report',
        PARAM_TEXT
    ));

    // Setting for group selection
    $settings->add(new admin_setting_configmulticheckbox(
        'local_grades/activity_report_groups',
        get_string('activity_report_groups', 'local_grades'),
        get_string('activity_report_groups_desc', 'local_grades'),
        [],
        $groups
    ));

    // Setting for the activity tag
    $settings->add(new admin_setting_configtext(
        'local_grades/activity_report_tag',
        get_string('activity_report_tag', 'local_grades'),
        get_string('activity_report_tag_desc', 'local_grades'),
        'deliverable',
        PARAM_TEXT
    ));
}