=== User Meta Manager ===

Contributors: Jason Lau
Donate link: http://jasonlau.biz
Tags: user, users, meta, usermeta, wp_usermeta, data, table, database, edit, add, update, delete, save, saved, list, manage, manager, management, customize, custom, create, activate, register, registers, short, tag, short code, member, members, admin, administrate, administrator, administrative, tool, plugin, module, addon, jason, lau, jasonlau, jasonlau.biz, profile, field, fields, extra
Requires at least: 3.3.1
Stable tag: 3.4.6
Tested up to: 4.1.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

== Description ==

User Meta Manager is a handy plugin, with a simple interface, for managing user meta data.

= Features =

* Add, edit, or delete meta data for individual members, or all members.
* Add custom user meta fields to the user profile editor.
* Create forms using short codes which allow members to update user meta data.
* Short codes for restricting access to content based on meta data values.
* Short codes for inserting user meta data into posts or pages.
* Automatically assign a meta key and default value to new registrations.
* Edit which meta data is displayed in the User Meta Manager Home screen.
* Backup and restore user meta data.

The free version is limited to listing 100 users. The Pro version is unlimited, and has some advanced features. Get the Pro plugin at http://jasonlau.biz/home/membership-options#umm-pro

= Pro Features =

* For websites with an unlimited number of users.
* Advanced field validation using match or search methods.
* Regular expressions matching.
* Canned/saved regular expressions. Easily save and reuse your favorite regex patterns!
* Case sensitive or insensitive validation search. Easily ban words or phrases from custom fields.
* Custom error messages.
* Redirect a user after custom form submission.
* Backup, restore, export/import User Meta Manager settings.
* Restore/import wp_usermeta table from User Meta Manager CSV backup.
* Backup, restore, export/import WordPress Users.
* More to come ...
* No additional features will be added to the free version.

Please report bugs in the Support section, and not the Reviews section.

== Installation ==

1. Use the Plugin installer which is located in the WordPress Dashboard menu under "Plugins > Add New". OR ...
2. Download User Meta Manager.
3. Unzip user-meta-manager.zip. 
4. Upload the folder named 'user-meta-manger' to the wp-content/plugins directory on your server
5. Go to the plugins manager in your Wordpress Dashboard and locate User Meta Manager in the list of plugins (under 'U' for 'User Meta Manager').
6. Click Activate.  
Once the plugin is activated, you will find User Meta Manager in the Dashboard menu under "Users".

== Frequently Asked Questions ==

= Do you accept donations? =
Yes I do. Thank you in advance!

= Do you have more WordPress plugins? =
Yes I do. Check my website at JasonLau.biz.

== Screenshots ==

1. screenshot-1.png The User Meta Manager Home screen.

== Changelog ==

= 3.4.6 =
Fixed a PHP warning. Verified compatibility with WP 4.1.1.

= 3.4.5 =
Verified compatibility with WP 4.1.

= 3.4.4 =
Bug fix.

= 3.4.3 =
Cleaned some code. Verified compatibility with WordPress 4.0.

= 3.4.2 =
Fixed a couple of bugs. Cleaned some code.

= 3.4.1 =
Fixed a bug. Added the field type Random String to the Pro version, in addition to some other features.

= 3.4.0 =
Fixed a bug in the json short code. View the documentation for the correct format for this short code option.

= 3.3.9 =
Fixed a bug in the json short code.

= 3.3.8 =
Fixed a bug in the json short code, and a couple of JS bugs.

= 3.3.7 =
Fixed a bug that caused default values to be overwritten in non-profile fields.

= 3.3.6 =
Fixed a bug that prevented some custom fields from displaying in the Add User screen.

= 3.3.5 =
Removed user-meta-manager/includes/umm-csv.php

= 3.3.4 =
Changed how csv backup is called.

= 3.3.3 =
Changed the HTML output slightly to resolve a potential conflict with other plugins.

= 3.3.2 =
Fixed a bug in the Add Custom Meta process.

= 3.3.1 =
Default values are only set if the field is not a profile field.

= 3.3.0 =
Fixed a minor bug. Verified compatibility with WP 3.9

= 3.2.9 =
Fixed a bug in the Edit Columns process.

= 3.2.8 =
Fixed a bug in the Add User process.

= 3.2.7 =
Fixed a couple of bugs in the new user registration process.

= 3.2.6 =
All editable roles are now included in the field editor.

= 3.2.5 =
Fixed a bug in the profile short code.

= 3.2.4 =
Fixed a bug.

= 3.2.3 =
Arrays are now properly displayed on the Home screen.

= 3.2.2 =
Fixed a bug in the Home screen display.

= 3.2.1 =
Improved ummquery shortcode to better handle serialized results.

= 3.2.0 =
Fixed a bug in checkbox group validation.

= 3.1.9 =
Fixed a bug in the CSV export feature.

= 3.1.8 =
Further integrated the Pro extension.

= 3.1.7 =
Fixed a few minor glitches.

= 3.1.6 =
Further integrated the Pro extension. Pro users should update at this time also.

= 3.1.5 =
Fixed a bug in the Unique Value feature.

= 3.1.4 =
Made some changes to the UI. A pro extension is now available to extend this plugin. Due to some complaints I received, the free version is now limited to 100 users.

= 3.1.3 =
Added some new short codes for displaying the user profile editor, login form, and more.

= 3.1.2 =
Fixed a roles-related bug. Changed when the initial database backup takes place. Verified compatibility with WordPress 3.8.

= 3.1.1 =
Fixed a bug in the userquery shortcode. Stripped slashes from custom meta values containing apostrophes.

= 3.1.0 =
Fixed a few bugs.

= 3.0.9 =
Fixed a few bugs.

= 3.0.8 =
Fixed a few bugs in the form short code. Added support for multiple forms on a single page. Verified compatibility with WordPress 3.7.1.

= 3.0.7 =
Fixed a variable conflict. Verified compatibility with WordPress 3.7.

= 3.0.6 =
Added userquery short code which allows you to query the wp_users and wp_usermeta tables and display results. Added support for multiple select menu selections. Added support for displaying fields based on user role.

= 3.0.5 =
Added url attribute to the useraccess shortcode. Optionally insert a url to bounce restricted users to. Fixed a couple of bugs.

= 3.0.4 =
Bug fix. Updated language file.

= 3.0.3 =
Core user data, ID, user_login, user_nicename, user_email, user_url, user_registered, display_name, can now be displayed using the usermeta short code.

= 3.0.2 =
Added Unique Value option to the Add Custom Meta and Edit Custom Meta screens. This allows you to create meta data which is exclusive to each user. If a user enters a duplicate value, the submission is terminated, an error message is returned, and the CSS class umm-error-field is added to the faulty field. Added total support for checkbox groups. Added new short code attributes. Added support for nested short codes. Updated Help section. Verified compatibility with latest WordPress 3.6.1.

= 3.0.1 =
Bug fixes. Added nonce field to all custom meta forms. Verified compatibility with latest WordPress version.

= 3.0.0 =
Bug fix. Fixed a bug which caused default html markup to not be set.

= 2.2.9 =
You can now change the HTML markup for the custom field output. Look in the User Meta Manager contextual help tab under Plugin Settings for new options.

= 2.2.8 =
Custom profile fields are now automatically added to the user registration form and the Add New User form. Confirmed compatibility with WordPress version 3.5.2. Fixed a pagination bug. Added a couple of PHP API methods for testing meta data.

= 2.2.7 =
New feature allows you to change the name of the hidden form-field used to test for spam-bots. Now the profile field section title is only displayed in the profile or user editors.

= 2.2.6 =
Fixed a bug which caused empty checkboxes to not update properly.

= 2.2.5 =
Fixed bug which caused empty form fields to not update. Added to the plugin settings an override for the duplicate meta key check.

= 2.2.4 =
Repaired a bug which caused saved field type data to be lost during the upgrade process. Fixed a bug in the profile field editor, which caused the "Allow Tags" menu to display the incorrect value.

= 2.2.3 =
Crunched all saved data to a single option, and changed the way data is saved, thus greatly reducing the number of database queries.

= 2.2.2 =
Fixed a few language and css bugs.

= 2.2.1 =
New feature: Added a CSV builder to the Backup section.

= 2.2.0 =
Improved multisite support.

= 2.1.9 =
New feature: Added multisite support. Now User Meta Manager manages data only for the users of the current site.

= 2.1.8 =
New feature: Added a button to the plugin settings section which allows you to sync custom meta data to all users. Also fixed two bugs in the meta data deletion process.

= 2.1.7 =
New feature: Added a form short code builder to the contextual help section.

= 2.1.6 =
Fixed a bug which caused single-user meta key deletion to fail.

= 2.1.5 =
More bug fixes.

= 2.1.4 =
More bug fixes.

= 2.1.3 =
Fixed a number of bugs.

= 2.1.2 =
Fixed a javascript bug and a css bug.

= 2.1.1 =
Fixed a javascript bug.

= 2.1.0 =
Now you can sort the custom fields by dragging and dropping them in position in the Edit Custom Meta screen. This will affect the order in which the custom fields are displayed in the profile editor. Added the option to display a title for the custom profile fields section. Look for the new setting in the contextual help screen under Plugin Settings. Replaced the label tag with the span tag for radio button groups.

= 2.0.9 =
Fixed a bug which prevented numeric user meta values from being updated. Verified compatibility with WP 3.5.1.

= 2.0.8 =
Removed some development code which set php error reporting to ALL.

= 2.0.7 =
Replaced depreciated eregi calls with other methods. Fixed some language bugs. Cleaned numerous PHP notices. Fixed a bug which caused custom user meta data to be unintentionally updated for all users. Added link to edit users in the User Login column.

= 2.0.6 =
Fixed a bug which threw an error while updating custom meta data.

= 2.0.5 =
Reimplemented the single-member "Add Meta" feature. Added an option to shortcut the single-member meta data editing process.

= 2.0.4 =
Added Czech translation. Verified compatibility with WordPress version 3.5.

= 2.0.3 =
Maintenance and Contextual help update.

= 2.0.2 =
Changes to the backup and restore process.
 
= 2.0.1 =
Fixed a potential bug in the activation process. 
 
= 2.0.0 =
This is a near complete overhaul with too many changes to list. 
 
= 1.5.7 =
Fixed a bug which caused meta values to not display correctly in the editor.

= 1.5.6 =
Fixed a bug which caused meta values containing apostrophes to not display correctly in the editor.

= 1.5.5 =
This update adds features which allow you to edit which columns are displayed in the results table. Look for the new Edit Columns link.

= 1.5.4 =
Made a slight change to the new json short code attribute.

= 1.5.3 =
Added a new short code attribute, which allows you to restrict content based upon multiple meta keys and values.

= 1.5.2 =
Minor code changes.

= 1.5.1 =
Minor change.

= 1.5 =
Fixed another pagination bug and made some other minor changes.

= 1.4.1 =
Verified compatibility with WordPress 3.4.1. Fixed pagination bug and made some other minor changes.

= 1.4 =
Various and sundry minor changes throughout the plugin.

= 1.3 =
Fixed a typo.

= 1.2 =
Enabled multilingual support.

= 1.1 =
Numerous updates and some improvements.

= 1.0 =
Initial release. Prayers have been answered.

== Upgrade Notice ==

= 1.0 =
Initial release. Prayers have been answered.

= 1.1 =
Numerous updates and some improvements.

= 1.2 =
Enabled multilingual support.

= 1.3 =
Fixed a typo.

= 1.4 =
Various and sundry minor changes throughout the plugin.

= 1.4.1 =
Verified compatibility with WordPress 3.4.1. Fixed pagination bug and made some other minor changes.

= 1.5 =
Fixed another pagination bug and made some other minor changes.

= 1.5.1 =
Minor change.

= 1.5.2 =
Minor code changes.

= 1.5.3 =
Added a new short code attribute, which allows you to restrict content based upon multiple meta keys and values.

= 1.5.4 =
Made a slight change to the new json short code attribute.

= 1.5.5 =
This update adds features which allow you to edit which columns are displayed in the results table. Look for the new Edit Columns link.

= 1.5.6 =
Fixed a bug which caused meta values containing apostrophes to not display correctly in the editor.

= 1.5.7 =
Fixed a bug which caused meta values to not display correctly in the editor.

= 2.0.0 =
This is a near complete overhaul with too many changes to list. 
 
= 2.0.1 =
Fixed a potential bug in the activation process. 
 
= 2.0.2 =
Changes to the backup and restore process.
 
= 2.0.3 =
Maintenance and Contextual help update.

= 2.0.4 =
Added Czech translation. Verified compatibility with WordPress version 3.5.

= 2.0.5 =
Reimplemented the single-member "Add Meta" feature. Added an option to shortcut the single-member meta data editing process.

= 2.0.6 =
Fixed a bug which threw an error while updating custom meta data.

= 2.0.7 =
Replaced eregi calls with other methods. Fixed some language bugs.

= 2.0.8 =
Removed some development code which set php error reporting to ALL.

= 2.0.9 =
Fixed a bug which prevented numeric user meta values from being updated. Verified compatibility with WP 3.5.1.

= 2.1.0 =
Now you can sort the custom fields by dragging and dropping them in position in the Edit Custom Meta screen. This will affect the order in which the custom fields are displayed in the profile editor. Added the option to display a title for the custom profile fields section. Look for the new setting in the contextual help screen under Plugin Settings. Replaced the label tag with the span tag for radio button groups.

= 2.1.1 =
Fixed a javascript bug.

= 2.1.2 =
Fixed a javascript bug and a css bug.

= 2.1.3 =
Fixed a number of bugs.

= 2.1.4 =
More bug fixes.

= 2.1.5 =
More bug fixes.

= 2.1.6 =
Fixed a bug which caused single-user meta key deletion to fail.

= 2.1.7 =
New feature: Added a form short code builder to the contextual help section.

= 2.1.8 =
New feature: Added a button to the plugin settings section which allows you to sync custom meta data to all users. Also fixed two bugs in the meta data deletion process.

= 2.1.9 =
New feature: Added multisite support. Now User Meta Manager manages data only for the users of the current site.

= 2.2.0 =
Improved multisite support.

= 2.2.1 =
New feature: Added a CSV builder to the Backup section.

= 2.2.2 =
Fixed a few language and css bugs.

= 2.2.3 =
Crunched all saved data to a single option, and changed the way data is saved, thus greatly reducing the number of database queries.

= 2.2.4 =
Repaired a bug which caused saved field type data to be lost during the upgrade process. Fixed a bug in the profile field editor, which caused the "Allow Tags" menu to display the incorrect value.

= 2.2.5 =
Fixed bug which caused empty form fields to not update. Added to the plugin settings an override for the duplicate meta key check.

= 2.2.6 =
Fixed a bug which caused empty checkboxes to not update properly.

= 2.2.7 =
New feature allows you to change the name of the hidden form-field used to test for spam-bots. Now the profile field section title is only displayed in the profile or user editors.

= 2.2.8 =
Custom profile fields are now automatically added to the user registration form and the Add New User form. Confirmed compatibility with WordPress version 3.5.2. Fixed a pagination bug. Added a couple of PHP API methods for testing meta data.

= 2.2.9 =
You can now change the HTML markup for the custom field output. Look in the User Meta Manager contextual help tab under Plugin Settings for new options.

= 3.0.0 =
Bug fix. Fixed a bug which caused default html markup to not be set.

= 3.0.1 =
Bug fixes. Added nonce field to all custom meta forms. Verified compatibility with latest WordPress version.

= 3.0.2 =
Added <strong>Unique Value</strong> option to the <em>Add Custom Meta</em> and <em>Edit Custom Meta</em> screens. This allows you to create meta data which is exclusive to each user. If a user enters a duplicate value, the submission is terminated, an error message is returned, and the CSS class <strong>umm-error-field</strong> is added to the faulty field. Added total support for <strong>checkbox groups</strong>. Added <strong>new short code attributes</strong>. Added <strong>support for nested short codes</strong>. Updated Help section. Verified compatibility with latest WordPress 3.6.1.

= 3.0.3 =
Core user data, ID, user_login, user_nicename, user_email, user_url, user_registered, display_name, can now be displayed using the usermeta short code.

= 3.0.4 =
Bug fix. Updated language file.

= 3.0.5 =
Added url attribute to the useraccess shortcode. Optionally insert a url to bounce restricted users to. Fixed a couple of bugs.

= 3.0.6 =
Added userquery short code which allows you to query the wp_users and wp_usermeta tables and display results. Added support for multiple select menu selections. Added support for displaying fields based on user role.

= 3.0.7 =
Fixed a variable conflict. Verified compatibility with WordPress 3.7.

= 3.0.8 =
Fixed a few bugs in the form short code. Added support for multiple forms on a single page. Verified compatibility with WordPress 3.7.1.

= 3.0.9 =
Fixed a few bugs.

= 3.1.0 =
Fixed a few bugs.

= 3.1.1 =
Fixed a bug in the userquery shortcode. Stripped slashes from custom meta values containing apostrophes.

= 3.1.2 =
Fixed a roles-related bug. Changed when the initial database backup takes place. Verified compatibility with WordPress 3.8.

= 3.1.3 =
Added some new short codes for displaying the user profile editor, login form, and more.

= 3.1.4 =
Made some changes to the UI. A pro extension is now available to extend this plugin. Due to some complaints I received, the free version is now limited to 100 users.

= 3.1.5 =
Fixed a bug in the Unique Value feature.

= 3.1.6 =
Further integrated the Pro extension. Pro users should update at this time also.

= 3.1.7 =
Fixed a few minor glitches.

= 3.1.8 =
Further integrated the Pro extension.

= 3.1.9 =
Fixed a bug in the CSV export feature.

= 3.2.0 =
Fixed a bug in checkbox group validation.

= 3.2.1 =
Improved ummquery shortcode to better handle serialized results.

= 3.2.2 =
Fixed a bug in the Home screen display.

= 3.2.3 =
Arrays are now properly displayed on the Home screen.

= 3.2.4 =
Fixed a bug.

= 3.2.5 =
Fixed a bug in the profile short code.

= 3.2.6 =
All editable roles are now included in the field editor.

= 3.2.7 =
Fixed a couple of bugs in the new user registration process.

= 3.2.8 =
Fixed a bug in the Add User process.

= 3.2.9 =
Fixed a bug in the Edit Columns process.

= 3.3.0 =
Fixed a minor bug. Verified compatibility with WP 3.9

= 3.3.1 =
Default values are only set if the field is not a profile field.

= 3.3.2 =
Fixed a bug in the Add Custom Meta process.

= 3.3.3 =
Changed the HTML output slightly to resolve a potential conflict with other plugins.

= 3.3.4 =
Changed how csv backup is called.

= 3.3.5 =
Removed user-meta-manager/includes/umm-csv.php

= 3.3.6 =
Fixed a bug that prevented some custom fields from displaying in the Add User screen.

= 3.3.7 =
Fixed a bug that caused default values to be overwritten in non-profile fields.

= 3.3.8 =
Fixed a bug in the json short code, and a couple of JS bugs.

= 3.3.9 =
Fixed a bug in the json short code.

= 3.4.0 =
Fixed a bug in the json short code. View the documentation for the correct format for this short code option.

= 3.4.1 =
Fixed a bug. Added the field type Random String to the Pro version, in addition to some other features.

= 3.4.2 =
Fixed a couple of bugs. Cleaned some code.

= 3.4.3 =
Cleaned some code. Verified compatibility with WordPress 4.0.

= 3.4.4 =
Bug fix.

= 3.4.5 =
Verified compatibility with WP 4.1.

= 3.4.6 =
Fixed a PHP warning. Verified compatibility with WP 4.1.1.