# Local Grades Web Service API
A Moodle local plugin that provides a custom web service endpoint to fetch grading related data.


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
    - Add the functions to a service.
    - Ensure the user accessing the service has the necessary capabilities.

## 📑 API Reference
Documentation Link: https://your-moodle-site.com/admin/webservice/documentation.php

### functions
- `local_grades_get_ungraded_submissions` - get ungraded submission items that require manaul grading
- `local_grades_get_activity_reports` - get students' course activity data from tagged courses, activites and selected groups.

**Example**

```
curl -X GET "https://your-moodle-site.com/webservice/rest/server.php" \
     -d "wstoken=YOUR_WEB_SERVICE_TOKEN" \
     -d "wsfunction=local_grades_get_ungraded_submissions" \
     -d "moodlewsrestformat=json"
```

## Developer Resources
- [moodledev.io: Writing a new service ](https://moodledev.io/docs/5.2/apis/subsystems/external/writing-a-service)
This documentation covers the creation of a new external service for use in a web service