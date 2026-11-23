# Local Grades Web Service API

A Moodle local plugin that provides custom web service endpoints for retrieving grading-related data, reporting learner activity, and grading quiz essay questions through an AI grading pipeline.

## Features

- Retrieve ungraded submissions that require manual grading.
- Retrieve learner activity reports from selected courses, activities and groups.
- Support incremental activity extraction using modification timestamps.
- Track activity changes to retrieve new or modified records.
- Submit AI-generated essay grades and feedback to Moodle's gradebook.

## Installation

### Prerequisites

- Moodle 4.5 or later.
- PHP 8 or later.
- Administrator access to the Moodle instance.

### Step-by-step installation

1. Navigate to your Moodle installation's `local` directory.

2. Clone the repository:

```bash
cd <moodle-root>/local
git clone https://github.com/The-DigitalAcademy/grades-webservice-moodle-plugin grades
```

3. Log in to Moodle as an administrator.

4. Navigate to **Site administration → Notifications**.

5. Follow the prompts to install the plugin.

6. Configure the required web service functions and permissions.

### Web service configuration

To access the API externally:

1. Navigate to **Site administration → Server → Web services**.
2. Enable web services if they are not already enabled.
3. Create or select an external service.
4. Add the required `local_grades` functions to the service.
5. Generate a web service token for the authorised user.
6. Ensure the user has the necessary capabilities.

## API Reference

Moodle web service documentation:

`https://your-moodle-site.com/admin/webservice/documentation.php`

### Available functions

| Function | Description |
|---|---|
| `local_grades_get_ungraded_submissions` | Retrieves submissions that require manual grading. |
| `local_grades_get_activity_reports` | Retrieves learner activity data from selected courses, activities and groups. Supports incremental extraction. |
| `local_grades_set_essay_grade` | Updates an essay question's grade and feedback through Moodle's grading pipeline. |

## Activity Reports

The `local_grades_get_activity_reports` function retrieves learner activity information, including:

- Course and group information.
- Learner identification and details.
- Activity type and activity name.
- Learner grades.
- Activity due dates.
- Submission dates.
- Submission status.
- Last modification timestamps.

### Incremental extraction

The activity reporting function supports incremental extraction through the optional `since` parameter.

The parameter accepts a Unix timestamp and returns activity records modified since the specified time.

**Parameter:**

| Parameter | Type | Description |
|---|---|---|
| `since` | Integer | Unix timestamp used to retrieve new or modified activity records. Defaults to `0`, which requests records without a modification-time restriction. |

Example:

```bash
curl -X POST "https://your-moodle-site.com/webservice/rest/server.php" \
  -d "wstoken=YOUR_WEB_SERVICE_TOKEN" \
  -d "wsfunction=local_grades_get_activity_reports" \
  -d "moodlewsrestformat=json" \
  -d "since=1795119078"
```

When integrating with an external reporting pipeline, the application can:

1. Store the timestamp of the last successful extraction.
2. Pass that timestamp through the `since` parameter during the next extraction.
3. Retrieve new or modified activity records.
4. Update existing records or insert new records into the reporting database.
5. Advance the extraction checkpoint after successful processing.

This supports incremental reporting without repeatedly processing the entire activity dataset.

## Essay Grading

The `local_grades_set_essay_grade` function updates a single essay question slot within a quiz attempt and pushes the result through Moodle's grading pipeline to the gradebook.

The function requires the `local/grades:setessaygrade` capability.

This capability is not granted to any role by default. It must be explicitly assigned to the role used by the AI grading service account.

### Example request

```bash
curl -X POST "https://your-moodle-site.com/webservice/rest/server.php" \
  -d "wstoken=YOUR_WEB_SERVICE_TOKEN" \
  -d "wsfunction=local_grades_set_essay_grade" \
  -d "moodlewsrestformat=json" \
  -d "attemptid=123" \
  -d "slot=11" \
  -d "grade=4.5" \
  -d "feedback=Well argued, with a clear South African example." \
  -d "feedbackformat=1"
```

## Example: Retrieve Ungraded Submissions

```bash
curl -X POST "https://your-moodle-site.com/webservice/rest/server.php" \
  -d "wstoken=YOUR_WEB_SERVICE_TOKEN" \
  -d "wsfunction=local_grades_get_ungraded_submissions" \
  -d "moodlewsrestformat=json"
```

## Security and Permissions

- Keep web service tokens secure.
- Only grant web service access to authorised users.
- Assign the essay grading capability explicitly to the appropriate service account.
- Avoid committing tokens, passwords or other sensitive configuration details to the repository.

## Developer Resources

- [Moodle Developer Documentation](https://moodledev.io/)
- [Writing a new Moodle Web Service](https://moodledev.io/docs/4.5/apis/subsystems/external)
