=== User Meta Manager ===

Contributors: Jason Lau
Donate link: http://jasonlau.biz
Tags: user, users, meta, usermeta, wp_usermeta, data, table, database, edit, add, update, delete, save, saved, list, manage, manager, management, customize, custom, create, activate, register, registers, short, tag, short code, member, members, admin, administrate, administrator, administrative, tool, plugin, module, addon, jason, lau, jasonlau, jasonlau.biz, profile, field, fields, extra
Requires at least: 3.3.1 
Stable tag: 2.0.0 beta-dev 1.3
Tested up to: 3.4.2

== Description ==

THIS IS THE BETA-DEV VERSION OF THIS PLUGIN; BUGS ARE POSSIBLE AND SUPPORT IS LIMITED.
For the current stable release, go to http://wordpress.org/extend/plugins/user-meta-manager/

<p>User Meta Manager allows administrators to add, edit, or delete user meta data.</p> 
<p>User Meta Manager also provides <em>short codes</em> for inserting user meta data into posts or pages, or restricting user-access to content.</p>
<p><strong>To display data for a particular user:</strong><br /><pre>[usermeta key="meta key" user="user id"]</pre></p>
<p><strong>To display data for the current user:</strong><br /><pre>[usermeta key="meta key"]</pre></p>
<p>An additional short code is available for restricting user access based on a meta key and value or user ID.</p>
<p><strong>To restrict access based on meta key and value:</strong><br /><pre>[useraccess key="meta key" value="meta value" message="You do not have permission to view this content."]Restricted content.[/useraccess]</pre>
Allowed users will have a matching meta value.</p>
<p><strong>To restrict access based on multiple meta keys and values:</strong><br /><pre>[useraccess json='{"access_level":"gold","sub_level":"silver"}' message="You do not have permission to view this content."]Restricted content goes here.[/useraccess]</pre>
The <em>json</em> attribute is used to define a list of meta keys and values. The list must be JSON encoded, as seen in the example above. Users with matching meta keys and values will be granted access to restricted content.</p>
<p><strong>To restrict access based on user ID:</strong><br /><pre>[useraccess users="1 22 301" message="You do not have permission to view this content."]Restricted content.[/useraccess]</pre>
Allowed user IDs are listed in the users attribute.</p>
<p>If you find this plugin useful, please rate it up. If for some reason you think this plugin is broken or has a bug, be helpful and contact me at http://jasonlau.biz/home/contact-me.</p>

== Installation ==

*Use the Plugin installer which is located in the WordPress Dashboard menu under "Plugins > Add New". OR ...
*Download User Meta Manager.
*Unzip user-meta-manager.zip. 
*Upload the folder named 'user-meta-manger' to the wp-content/plugins directory on your server
*Go to the plugins manager in your Wordpress Dashboard and locate User Meta Manager in the list of plugins (under 'U' for 'User Meta Manager').
*Click Activate.  
Once the plugin is activated, you will find User Meta Manager in the Dashboard menu under "Users".

== Frequently Asked Questions ==

= Do you accept donations? =
Yes I do. Thank you in advance!

= Do you have more WordPress plugins? =
Yes I do. Check my website at JasonLau.biz.

== Screenshots ==

1. screenshot-1.png Is a picture of the User Meta Manager Admin panel.


== Changelog ==

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