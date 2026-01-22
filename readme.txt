=== CF7 Prodamus XL Integration ===

Contributors: yourname
Tags: contact form 7, prodamus, xl, crm, integration, leads, deals
Requires at least: 5.0
Tested up to: 6.4
Stable tag: 1.0.0
Requires PHP: 7.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Integrates Contact Form 7 with Prodamus XL API to automatically create leads and deals from form submissions.

== Description ==

This plugin integrates Contact Form 7 with Prodamus XL CRM system. When a user submits a Contact Form 7 form, the plugin automatically:

* Creates a new lead/contact in Prodamus XL
* Creates a deal in the specified pipeline stage
* Maps form fields to Prodamus XL contact fields

== Features ==

* Automatic lead and deal creation
* Configurable API settings
* Support for multiple forms
* Detailed logging
* Easy to configure admin interface

== Installation ==

1. Upload the plugin files to the `/wp-content/plugins/cf7-prodamus-xl-integration` directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the 'Plugins' screen in WordPress
3. Use the CF7 Prodamus XL screen to configure the plugin
4. Enter your Prodamus XL API token and other settings

== Form Field Mapping ==

The plugin automatically maps these Contact Form 7 fields:

* `your-name` - Contact name (first and last name)
* `your-email` - Contact email (required)
* `your-phone` - Contact phone number
* `your-contactway` - Preferred contact method

== Configuration ==

1. Go to **CF7 Prodamus XL** in your WordPress admin
2. Enter your API token from Prodamus XL
3. Configure Stage ID and Responsible User ID (optional)
4. Specify which forms to integrate (optional)

== Frequently Asked Questions ==

= Where do I find my API token? =

Log into your Prodamus XL account and go to Settings > API to generate or view your API token.

= What if my form fields have different names? =

You can modify the field names in the integration file or contact the developer for custom mapping.

= How do I test the integration? =

Submit a test form and check your Prodamus XL account for new leads. Also check WordPress debug logs.

== Changelog ==

= 1.0.0 =
* Initial release
* Basic integration with Prodamus XL API
* Admin settings page
* Support for Contact Form 7 field mapping