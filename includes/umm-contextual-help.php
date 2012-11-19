<?php
if(!defined("UMM_PATH")) die();

function umm_help($contextual_help, $screen_id, $screen) {
	$screen = get_current_screen();
    if($screen->id != "users_page_user-meta-manager")
    return;
    
    $backup_notice = '<div class="umm-warning">' . __('<strong>IMPORTANT:</strong> <ins>Always</ins> backup your data before making changes to your website.', 'user-meta-manager') . '</div>';
    $tabs = array(array(
        __('Introduction'),
        $backup_notice . 
        '<h2>' . __('What is <em>User Meta</em>?', 'user-meta-manager') . '</h2>
        <p>' . __('<em>User Meta</em> is user-specific data which is stored in the <em>wp_usermeta</em> database table. This data is stored by WordPress and various and sundry plugins, and can consist of anything from profile information to membership levels.', 'user-meta-manager') . '</p>'
    ),
    
    array(
        __('Plugin Settings', 'user-meta-manager'),
        $backup_notice . 
        '<h2>' . __( 'User Meta Manager Settings', 'user-meta-manager') . '</h2><p>' . __( 'The User Meta Manager <em>Home</em> screen displays a list of your website\'s users from which you may select a single user to edit.' , 'user-meta-manager') . '</p>
        <p>' . __( 'Locate from the list which User you wish to work with, place your mouse over that item, and the following links will appear as your mouse moves over each user -', 'user-meta-manager') . '</p>
        <ol start="1">
    <li>' . __('<strong>Add Meta:</strong> Add new, custom meta data for a single user. It\'s wise to only use letters, numbers, and underscores while adding and naming new meta keys. Meta values can consist of any characters. Once meta-data is added here, it can only be managed here. Use the <em>Add Custom Meta</em> button to add custom meta for all users.', 'user-meta-manager') . '</li>
    <li>' . __('<strong>Edit Meta:</strong> Edit existing meta-data values for each member.', 'user-meta-manager') . '</li>   
    <li>' . __('<strong>Delete Meta:</strong> Delete individual meta keys for a single user or for <em>All Users</em>. You can select which meta data to delete from the drop menu.', 'user-meta-manager') . '</li>
    </ol>'
    ),
    
    array(
        __('Home', 'user-meta-manager'),
        $backup_notice . 
        '<h2>' . __( 'The <em>Home</em> Screen', 'user-meta-manager') . '</h2><p>' . __( 'The User Meta Manager <em>Home</em> screen displays a list of your website\'s users from which you may select a single user to edit.', 'user-meta-manager') . '</p>
        <p>' . __( 'Locate from the list which User you wish to work with, place your mouse over that item, and the following links will appear as your mouse moves over each user -', 'user-meta-manager') . '</p>
        <ol start="1">
    <li>' . __('<strong>Add Meta:</strong> Add new, custom meta data for a single user. It\'s wise to only use letters, numbers, and underscores while adding and naming new meta keys. Meta values can consist of any characters. Once meta-data is added here, it can only be managed here. Use the <em>Add Custom Meta</em> button to add custom meta for all users.', 'user-meta-manager') . '</li>
    <li>' . __('<strong>Edit Meta:</strong> Edit existing meta-data values for each member.', 'user-meta-manager') . '</li>   
    <li>' . __('<strong>Delete Meta:</strong> Delete individual meta keys for a single user or for <em>All Users</em>. You can select which meta data to delete from the drop menu.', 'user-meta-manager') . '</li>
    </ol>'
    ),
    
    array(
        __('Add Custom Meta', 'user-meta-manager'),
        $backup_notice . 
        '<h2>' . __( 'Custom Meta-Data For All Users', 'user-meta-manager') . '</h2><p>' . __('Adding custom meta-data will add the  <strong><em>Key</em></strong> and <strong><em>Default Value</em></strong> to all existing users. The <strong><em>Default Value</em></strong> you set will become the default value for all users, and all future registrations. Optionally, select a <strong><em>Profile Field Type</em></strong> to view more options for adding this field to the WordPress user profile editor.', 'user-meta-manager').'</p>'
    ),
    array(
        __('Edit Custom Meta', 'user-meta-manager'),
        $backup_notice . 
        '<h2>' . __( 'Edit Custom Meta-Data For All Users' ) . '</h2><p>' . __('Editing custom meta-data will edit the <strong><em>Key</em></strong> and default <strong><em>Value</em></strong> for future registrations. Selecting <em>Yes</em> for <strong><em>Update Value For All Current Users</em></strong> will update the current value for all existing members, overwriting any existing value. Optionally, select a <strong><em>Profile Field Type</em></strong> to view more options for adding this field to the WordPress user profile editor. Select <em>None</em> from the <strong><em>Profile Field Type</em></strong> menu to remove an existing custom profile field.', 'user-meta-manager').'</p>'
    ),
    array(
        __('Delete Custom Meta', 'user-meta-manager'),
        $backup_notice . 
        '<h2>' . __( 'Delete Custom Meta-Data For All Users' ) . '</h2><p>' . __('Deleting custom meta-data will delete the Key and data for ALL existing members AND future registrations.', 'user-meta-manager').'</p>'
    ),
    array(
        __('Edit Columns'),
        '<h2>' . __( 'Editing Home Screen List Columns', 'user-meta-manager') . '</h2><p>' . __( 'This screen controls which columns are displayed in the <em>Home</em> screen list.<br /><br />The list on top displays the columns which are currently in use. By selecting an item from the list, and pressing the <strong><em>Remove Selected Column</em></strong> button, columns can be removed from the results table, except the <strong><em>ID</em></strong> and <strong><em>User Login</em></strong> columns, which are required.<br /><br />Columns can be added to the results table using the bottom form. To add a column, select a <strong><em>Key</em></strong> from the menu, enter a <strong><em>Label</em></strong> for the column, and press the <strong><em>Add Column</em></strong> button. The new column will then be added to the results table, and will become searchable. The <em>Label</em> is displayed at the top of the column for identification purposes.', 'user-meta-manager') . '</p>'
    ),
    array(
        __('Backup &amp; Restore', 'user-meta-manager'),
        '<h2>' . __( 'Backup &amp; Restore User Meta Data' ) . '</h2><p>' . __( 'There are several options available for backing-up and restoring the wp_usermeta database. This plugin creates the first backup automatically when first installed.', 'user-meta-manager') . '</p><ol start="1">
    <li>' . __('<strong>Backup:</strong> Create a backup, which is stored in the database, and can only be run by this plugin.', 'user-meta-manager') . '</li>
    <li>' . __('<strong>Restore:</strong> Restore a backup which was generated using the above method.', 'user-meta-manager') . '</li>   
    <li>' . __('<strong>Generate SQL:</strong> Generates the SQL needed for restoring usermeta data from a database manager, such as phpMyAdmin. Produces a code which can be copied and pasted.', 'user-meta-manager') . '</li>
    <li>' . __('<strong>Generate PHP:</strong> Generates the PHP code needed for restoring usermeta data from a PHP file. Copy and paste the code to a PHP file, save it in the root WordPress directory, and run via a Browser.', 'user-meta-manager') . '</li>
    <li>' . __('<strong>Generate PHP Restoration File:</strong> Generates a PHP-formatted restoration file on the server in the wp-content/user-meta-manager/backups folder. The generated file can be run from the browser. You will be prompted before restoration will commence.', 'user-meta-manager') . '</li>   
    <li>' . __('<strong>Delete Backup Files:</strong> Delete ALL existing backup files from the server.', 'user-meta-manager') . '</li>
    </ol>'
    ),
    array(
        __('Shortcodes', 'user-meta-manager'),
        '<h2>' . __( 'Shortcodes' ) . '</h2><p>' . __( 'A <em>Shortcode</em> is a non-HTML code snippet, which can be added to Posts or Pages. The purpose for using a shortcode is to extend certain plugin functionality to the Post or Page in which it is inserted. Shortcodes are plugin-specific and each one functions according to how the plugin is programmed by developer, and what settings are used by the end-user.', 'user-meta-manager') . '</p>
        <p>' . __( 'Following is a list of the shortcodes for the User Meta Manager plugin, and their uses.<br />    <strong>Display data for a particular user:</strong>
    <pre>[usermeta key="meta key" user="user id"]</pre>
    <br />
    <strong>Display data for the current user:</strong>
    <pre>[usermeta key="meta key"]</pre>
    <br />
    <strong>Restrict access based on meta key and value:</strong>
    <pre>[useraccess key="meta key" value="meta value" message="You do not have permission to view this content."]Restricted content.[/useraccess]</pre>
    Allowed users will have a matching meta value.<br /><br /><br />
    <strong>Restrict access based on user ID:</strong>
    <pre>[useraccess users="1 22 301" message="You do not have permission to view this content."]Restricted content.[/useraccess]</pre>
    Allowed user IDs are listed in the <em>users</em> attribute.<br /><br /><br />
    <strong>Restrict access based on multiple meta keys and values:</strong>
    <pre>[useraccess json=\'{"access_level":"gold","sub_level":"silver"}\' message="You need permission to view this content."]Restricted content.[/useraccess]</pre>
    The <em>json</em> attribute is used to define a list of meta keys and values. The list must be JSON encoded, as seen in the example above. Users with matching meta keys and values will be granted access to restricted content.<br/><br/>
    JSON formatting -
    <pre>{"meta_key":"meta_value", "meta_key":"meta_value", "meta_key":"meta_value"}</pre>
    Additionally, you could repeat the same meta key multiple times.
    <pre>json=\'{"access_level":"gold", "sub_level":"silver", "sub_level":"bronze", "sub_level":"aluminum-foil"}\'</pre></p>', 'user-meta-manager')
    ),
    array(
        __('License', 'user-meta-manager'),
        __( '<p><strong>Disclaimer:</strong><br/>Use at your own risk. No warranty expressed or implied. Always backup your database before making changes.</p>
        <p><strong>License:</strong><br/>&copy;2012 <a href="http://websitedev.biz" target="_blank">http://websitedev.biz</a> <a href="http://jasonlau.biz" target="_blank">http://jasonlau.biz</a></p>
        <p>This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 3 of the License, or (at your option) any later version.</p>
        <p>This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.</p>
        <p>See the GNU General Public License for more details.<br /><a href="http://www.gnu.org/licenses/gpl.html" target="_blank">http://www.gnu.org/licenses/gpl.html</a></p>', 'user-meta-manager')
    ),
    array(
        __('Contribute Code', 'user-meta-manager'),
        __( '<h2>You Can Help Make This Plugin Perfect!</h2><p>If you are a talented WordPress developer, who would like to contribute to the developement of this plugin, go to <a href="https://github.com/jasonlau/Wordpress-User-Meta-Manager" target="_blank">https://github.com/jasonlau/Wordpress-User-Meta-Manager</a>. There you will find the development package and <a href="https://github.com/" target="_blank">GitHub</a> repository.</p>
        <p>Additionally, you can contact me at <a href="http://jasonlau.biz/home/contact-me" target="_blank">http://jasonlau.biz/home/contact-me</a>.</p>', 'user-meta-manager')
    ),
    array(
        __('Donate', 'user-meta-manager'),
        __( '<h2>Every Little-Bit Helps!</h2><p>Developing this plugin takes a lot of time, and as we all know, time equals money.</p>
        <p>I\'ve given a lot to you, and perhaps you would like to return the favor with a modest donation?</p>
        <p>Use the following PayPal button to make a donation. Your donations help pay for past and future development of this plugin.</p>
        <p>Thanks in advance!</p>
        <form action="https://www.paypal.com/cgi-bin/webscr" method="post">
<input type="hidden" name="cmd" value="_s-xclick">
<input type="hidden" name="hosted_button_id" value="X5Y2R65973XZ6">
<input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_donate_SM.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
<img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1">
</form>', 'user-meta-manager')
    )
    );
    
    $x = 1;
    foreach($tabs as $tab):
        $screen->add_help_tab(array(
          'id'	=> 'umm_help_tab_' . $x,
          'title'	=> __($tab[0]),
          'content'	=> __($tab[1])
        ));
        $x++;
    endforeach;
    
}
?>