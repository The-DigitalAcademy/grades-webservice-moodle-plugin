# Local Grades: Ungraded Submissions API
A Moodle local plugin that provides a custom web service endpoint to retrieve all student submissions requiring manual grading across Assignments and Quizzes.

## 🚀 Features
- Consolidates ungraded items from multiple activity types into a single list.
- Supports mod_assign (Assignments) and mod_quiz (Quizzes with manual grading questions).
- Provides direct deep-links to the grading interface for each submission.
- Exposed via Moodle's External Web Service (REST/AJAX).

## 🛠 Installation
### Prerequisites
- Administrator access to the Moodle instance.
- Moodle 5.x or later
- PHP 8+

### Step-by-step Installation
1. Download the plugin files.
2. Place the plugin files in the `local/` directory of your Moodle installation. The directory name for the plugin files should be gradereports:
```
moodle-root
└── local
    └── grades
         └── classes/
         └── db/
         └── README.md
         └── version.php
```
**You can do this using git clone:**
```
cd <moodle-root>/local
git clone https://github.com/The-DigitalAcademy/grades-webservice-moodle-plugin grades
```
3. Log in as an administrator to your Moodle site.
4. Navigate to Site administration > Notifications. Moodle will detect the new plugin and prompt you to Install it.
6. Follow the on-screen prompts to complete the installation.
7. To use the API externally:
    - Go to Site Administration > Server > Web Services.
    - Add the function local_grades_get_ungraded_submissions to a service.
    - Ensure the user accessing the service has the necessary capabilities.

## 📑 API Reference

**Function:** `local_grades_get_ungraded_submissions`
This function returns a list of submissions that have been submitted but do not yet have a recorded grade.

**Parameters**
- `wstoken`: The unique API key generated for the user.
- `wsfunction`: The name of the function defined in db/services.php.
- `moodlewsrestformat`: By default, Moodle returns XML. Adding json here makes the output much easier to parse in modern applications.

```
curl -X GET "https://your-moodle-site.com/webservice/rest/server.php" \
     -d "wstoken=YOUR_WEB_SERVICE_TOKEN" \
     -d "wsfunction=local_grades_get_ungraded_submissions" \
     -d "moodlewsrestformat=json"
```

**Response Structure**
The function returns an array of objects, each containing:

| Key           | Type      | Description                               |
|---------------|-----------|-------------------------------------------|
| coursename    | string    | The full name of the course.
|activitytype   | string    | The type of activity (assign or quiz).
|activitynamwe  | string    |The display name of the activity.
|username       | string    |The full name of the student.
|timemodified   | int	    |Unix timestamp of the last submission change.
|coursepath     | string    |Relative URL to the course page.
|activitypath   | string    |Relative URL to the activity main page.

### Example Response (JSON)
```
[
  {
    "coursename": "Introduction to Web Dev",
    "activitytype": "assign",
    "activityname": "Final Project",
    "username": "Jane Doe",
    "timemodified": 1710943200,
    "coursepath": "/course/view.php?id=5",
    "activitypath": "/mod/assign/view.php?id=12",
    "gradepath": "/mod/assign/view.php?id=12&action=grader&userid=42"
  }
]
```

## Developer Resources
- [moodledev.io: Writing a new service ](https://moodledev.io/docs/5.2/apis/subsystems/external/writing-a-service)
This documentation covers the creation of a new external service for use in a web service