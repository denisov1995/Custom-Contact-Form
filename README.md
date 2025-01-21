# Custom Contact Form Plugin

Custom Contact Form is a WordPress plugin that adds a contact form as a Gutenberg block. When the form is filled out by a user, a customer is created in the HubSpot CRM system, a message is sent to the email specified in the plugin settings, and the messages are logged in the WordPress admin area.

## Installation

1. Download the plugin files and place them in the `wp-content/plugins/custom-contact-form` directory.
2. Open the terminal in the plugin folder and run the command `npm install @wordpress/scripts --save-dev`. If necessary and when making changes, use the command `npm run build`.
3. Activate the plugin through the "Plugins" menu in WordPress.
4. Go to the "Contact Form" section and configure the plugin settings.

## Settings

In the plugin settings section, you can specify:

- **Email Recipient**: the email address where the messages will be sent.
- **HubSpot API Token**: the API token for integrating with HubSpot.

## Usage

1. In the Gutenberg editor, add a new block and select the `Custom Contact Form` block.
2. Place the block on a page or post.
3. Users can fill out the form, after which the data will be submitted.

## Functionality

- **Email Sending**: messages are sent to the email specified in the settings.
- **HubSpot Integration**: customers are created in the HubSpot CRM system.
- **Logging**: all submitted messages are logged in the WordPress admin area.

## File Overview

- `custom-contact-form.php`: the main plugin file that loads the necessary modules and functions.
- `includes/ajax-handler.php`: handles AJAX requests for form submission.
- `includes/settings-page.php`: the plugin settings page.
- `includes/logger-page.php`: logs submitted messages.

## Requirements

- WordPress 5.0 or higher.
- PHP 7.0 or higher.

Author: **Artem Denisov**
