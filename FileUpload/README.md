# FileUpload module

Simple pure-PHP file upload module.

Files
- index.php — front-end upload form and list
- upload.php — upload handler
- helpers.php — utility functions
- config.php — configuration
- uploads/ — storage directory (created at runtime)

Usage
1. Put the FileUpload folder in your webroot.
2. Ensure the webserver can write to FileUpload/uploads or create it with proper permissions.
3. Open index.php in a browser.
