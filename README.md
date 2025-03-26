# SFEnvironment Signmaker

This PHP application generates printable signs for organizing refuse into bins. You provide it with a background and images of items that the user can select. It leverages Dompdf to convert HTML into PDF for printing.

## Overview

The Signmaker application allows users to create custom signs by selecting images from a specified directory and placing them on a background image within defined bounding box coordinates. The application then generates a PDF of the created sign, suitable for printing.

This project is designed to be used in conjunction with a service such as `sfenvironment/signmaker_ui`, a Drupal module, that can dynamically assemble the necessary parameters, but the parameters can be passed manually as documented below.

## Features

-   **Customizable Sign Layout:** Users can define the bounding box (top, right, bottom, left) within which images are placed on the background.
-   **Image Selection:** Users can select images from a specified directory to include in their sign.
-   **PDF Generation:** The application generates a PDF document of the created sign using Dompdf.
-   **Dynamic Stylesheet Inclusion:** Supports inclusion of multiple stylesheets via URL parameters.
-   **JavaScript Interaction:** Client-side JavaScript (`signmaker.js`) handles image selection and layout interactions.

## Prerequisites

-   PHP with Dompdf extension installed.
-   Web server with PHP support.
-   A directory containing images for the sign.
-   A background image for the sign.

## Installation

1.  **Require via composer:**
    ```bash
    composer require sfenvironment/signmaker
2. **Ensure the web server has write permissions to the directory where you want to save the PDFs (if applicable).**

## Usage

Access the `index.php` file through your web browser with the following URL parameters:

-   `directory`: The path to the directory containing the images and background (required).
-   `background`: The filename of the background image (optional, defaults to `background.*` if present).
-   `bbox`: The bounding box coordinates in percentages, separated by commas (top, right, bottom, left) (required).
-   `root`: The root url of the application, for asset loading.
-   `stylesheets`: A comma separated list of stylesheets to include.

Example URL:
http://your-server/?directory=/path/to/images&bbox=10,10,90,90&root=/your/root&stylesheets=/assets/css/extra.css,/assets/css/more.css
### User Interaction

1.  **Select Items:** The left sidebar displays a list of images from the specified directory. Users can select images to add to the sign.
2.  **Image Placement:** The selected images are displayed within the bounding box on the background image.
3.  **Reset/Generate:**
    -   **Start Over:** Clears the selected images and resets the sign.
    -   **Save PDF:** Generates and downloads a PDF of the sign.

## Code Explanation

### `Signmaker::handle()`

-   Retrieves configuration parameters from the URL.
-   Generates the HTML structure for the sign, including image selection menu and PDF generation controls.
-   Dynamically calculates CSS styles based on the bounding box coordinates.
-   Embeds JavaScript configuration data into the HTML.

### `Signmaker::generate($html, $output = 'output', $additional_chroot = [])`

-   Uses Dompdf to convert the provided HTML into a PDF.
-   Sets PDF options, including remote image loading and chroot for file access.
-   Handles different output modes: `stream` (displays PDF in browser) and `output` (returns PDF data).
-   Injects the main stylesheet into the HTML.

### `Signmaker::getConfiguration()`

-   Parses URL parameters to retrieve configuration data.
-   Scans the specified directory for image files.
-   Validates required parameters (directory, bbox).
-   Constructs and returns the configuration array.

## Assets

-   `assets/css/style.css`: Contains the base CSS styles for the sign.
-   `assets/js/signmaker.js`: Handles client-side interactions.

## Dependencies

-   Dompdf: PDF generation library.

## Notes

-   Ensure the image directory and background image are accessible to the web server.
-   Adjust the bounding box coordinates to customize the image placement.
-   The application assumes images are located within the specified directory.
-   Error handling is basic, consider adding more robust validation and error reporting.

## Contributing

Feel free to submit pull requests or open issues for improvements or bug fixes.
