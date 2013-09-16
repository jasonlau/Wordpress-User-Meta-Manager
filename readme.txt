=== User Meta Manager ===

Contributors: Jason Lau
Donate link: http://jasonlau.biz
Tags: user, users, meta, usermeta, wp_usermeta, data, table, database, edit, add, update, delete, save, saved, list, manage, manager, management, customize, custom, create, activate, register, registers, short, tag, short code, member, members, admin, administrate, administrator, administrative, tool, plugin, module, addon, jason, lau, jasonlau, jasonlau.biz, profile, field, fields, extra
Requires at least: 3.3.1
Stable tag: 3.0.3
Tested up to: 3.6.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

== Description ==

User Meta Manager is a handy plugin, with a simple interface, for managing user meta data.

**Features:**

*Add, edit, or delete meta data for individual members, or all members.
*Add custom user meta fields to the user profile editor.
*Create forms using short codes which allow members to update user meta data.
*Short codes for restricting access to content based on meta data values.
*Short codes for inserting user meta data into posts or pages.
*Automatically assign a meta key and default value to new registrations.
*Edit which meta data is displayed in the User Meta Manager Home screen.
*Backup and restore user meta data.

Contribute to the development of this plugin at https://github.com/jasonlau/Wordpress-User-Meta-Manager

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