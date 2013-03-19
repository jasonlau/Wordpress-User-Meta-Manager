<?php

/**
 * @author Jason Lau
 * @copyright 2013
 * @package user-meta-manager
 */
 
if(!defined("UMM_PATH")) die();

function umm_help($contextual_help, $screen_id, $screen) {
    if($screen->id != 'users_page_user-meta-manager')
    return;
    $umm_settings = umm_get_option('settings');
    $retain_data = $umm_settings['retain_data'];
    switch($retain_data){
        case 'no':
        $retain_yes = '';
        $retain_no = ' selected="selected"';
        break;
        
        default:
        $retain_yes = ' selected="selected"';
        $retain_no = '';
    }
    
    $shortcut_editing = $umm_settings['shortcut_editing'];
    switch($shortcut_editing){
        case 'yes':
        $shortcut_editing_yes = ' selected="selected"';
        $shortcut_editing_no = '';
        break;
        
        default:
        $shortcut_editing_yes = '';
        $shortcut_editing_no = ' selected="selected"';
    }
    
    $pfields_position = (!isset($umm_settings['pfields_position']) || empty($umm_settings['pfields_position'])) ? 1 : $umm_settings['pfields_position'];
    switch($pfields_position){
        case 0:
        $pfields_position_top = ' selected="selected"';
        $pfields_position_bottom = '';
        break;
        
        default:
        $pfields_position_top = '';
        $pfields_position_bottom = ' selected="selected"';
    }
    
    if(empty($umm_settings)) $umm_settings = array('retain_data' => 'yes');
    $backup_notice = '<div class="umm-warning">' . __('<strong>IMPORTANT:</strong> <ins>Always</ins> backup your data before making changes to your website.', UMM_SLUG) . '</div>';
    $tabs = array(array(
        __('Introduction', UMM_SLUG),
        $backup_notice . 
        '<h2>' . __('What is <em>User Meta</em>?', UMM_SLUG) . '</h2>
        <p>' . __('<em>User Meta</em> is user-specific data which is stored in the <em>wp_usermeta</em> database table. This data is stored by WordPress and various and sundry plugins, and can consist of anything from profile information to membership levels.', UMM_SLUG) . '</p>'
    ),
    
    array(
        __('Plugin Settings', UMM_SLUG),
        '<h2>' . __( 'User Meta Manager Settings', UMM_SLUG) . '</h2>
        <div class="umm-update-settings-result hidden"></div>
        <form id="umm_update_settings_form" action="' . UMM_AJAX . 'umm_update_settings&amp;u=0" method="post"><table class="umm-settings-table widefat umm-rounded-corners">
<tr>
	<td>
        <strong>' . __('Retain Saved Data On Uninstall', UMM_SLUG) . '</strong><br />
        <select size="1" name="retain_data">
	<option value="' . __('yes', UMM_SLUG) . '"' . $retain_yes . '>' . __('Yes', UMM_SLUG) . '</option>
	<option value="' . __('no', UMM_SLUG) . '"' . $retain_no . '>' . __('No', UMM_SLUG) . '</option>
</select><br /><span>' . __('Select <em>No</em> to remove all traces of this plugin from the database when uninstalled. All saved custom meta data will be lost.', UMM_SLUG) . '</span></td>
</tr>
<tr class="alternate">
	<td><strong>' . __('Meta Editor Shortcut', UMM_SLUG) . '</strong><br />
        <select size="1" name="shortcut_editing">
	<option value="' . __('yes', UMM_SLUG) . '"' . $shortcut_editing_yes . '>' . __('Yes', UMM_SLUG) . '</option>
	<option value="' . __('no', UMM_SLUG) . '"' . $shortcut_editing_no . '>' . __('No', UMM_SLUG) . '</option>
</select><br /><span>' . __('Skips step 1 in the single-member meta data editor, and displays the entire list of meta keys and values for the selected member. Otherwise, you will have to select a single key to edit.', UMM_SLUG) . '</span></td>
</tr>
<tr>
	<td><strong>' . __('Custom Profile Field Section Title', UMM_SLUG) . '</strong><br />
        <input type="text" name="section_title" value="' . $umm_settings['section_title'] . '"><br /><span>' . __('Optional title for the section of custom profile fields, which is visible in the profile editor.', UMM_SLUG) . '</span></td>
</tr>
<tr class="alternate">
	<td><input data-form="umm_update_settings_form" data-subpage="umm_update_settings" data-wait="' . __('Wait...', UMM_SLUG) . '" class="button-primary umm-update-settings-submit" type="submit" value="' . __('Update Settings', UMM_SLUG) . '">
        <input name="first_run" type="hidden" value="no">
        <input name="return_page" type="hidden" value="' . UMM_AJAX . 'umm_update_settings&amp;u=0"></td>
</tr>
</table>
        </form><br />
<form id="umm_sync_data_form" action="' . UMM_AJAX . 'umm_sync_user_meta&amp;u=0" method="post">
        <table class="umm-settings-table widefat umm-rounded-corners">
<tr>
	<td><strong>' . __('Sync Meta Data', UMM_SLUG) . '</strong><br />       
        <input data-form="umm_sync_data_form" data-subpage="umm_sync_user_meta" data-wait="' . __('Wait...', UMM_SLUG) . '" class="button-primary umm-update-settings-submit" type="submit" value="' . __('Sync', UMM_SLUG) . '"><br /><span>' . __('If you use a plugin to import users instead of using the WP registration process, use this button to sync your saved custom meta with all new users.', UMM_SLUG) . '</span>
        <input name="first_run" type="hidden" value="no">
        <input name="return_page" type="hidden" value="' . UMM_AJAX . 'umm_update_settings&amp;u=0">
        </td>
</tr>
</table></form>'
    ),
    
    array(
        __('Home', UMM_SLUG),
        $backup_notice . 
        '<h2>' . __( 'The <em>Home</em> Screen', UMM_SLUG) . '</h2><p>' . __( 'The User Meta Manager <em>Home</em> screen displays a list of your website\'s users from which you may select a single user to edit.', UMM_SLUG) . '</p>
        <p>' . __( 'Locate from the list which User you wish to work with, place your mouse over that item, and the following links will appear as your mouse moves over each user -', UMM_SLUG) . '</p>
        <ol start="1">
         <li>' . __('<strong>Add Meta:</strong> Add meta data for a single user. Insert a meta key and default value and press <em>Submit</em>. The new meta-data will be applied to this user only, and can only be managed via the <em>Home</em> table actions, and not via the Custom Meta actions.', UMM_SLUG) . '</li>
    <li>' . __('<strong>Edit Meta:</strong> Edit existing meta-data values for each member.', UMM_SLUG) . '</li>   
    <li>' . __('<strong>Delete Meta:</strong> Delete individual meta keys for a single user or for <em>All Users</em>. You can select which meta data to delete from the drop menu.', UMM_SLUG) . '</li>
    </ol>'
    ),
    
    array(
        __('Add Custom Meta', UMM_SLUG),
        $backup_notice . 
        '<h2>' . __( 'Custom Meta-Data For All Users', UMM_SLUG) . '</h2><p>' . __('Adding custom meta-data will add the  <strong><em>Key</em></strong> and <strong><em>Default Value</em></strong> to all existing users. The <strong><em>Default Value</em></strong> you set will become the default value for all users, and all future registrations. Optionally, select a <strong><em>Field Type</em></strong> to view more options for adding this field to the WordPress user profile editor, a Post, or a Page.', UMM_SLUG).'</p>'
    ),
    
    array(
        __('Edit Custom Meta', UMM_SLUG),
        $backup_notice . 
        '<h2>' . __( 'Edit Custom Meta-Data For All Users' ) . '</h2><p>' . __('Editing custom meta-data will edit the <strong><em>Key</em></strong> and default <strong><em>Value</em></strong> for future registrations. Selecting <em>Yes</em> for <strong><em>Update Value For All Current Users</em></strong> will update the current value for all existing members, overwriting any existing value. Optionally, select a <strong><em>Field Type</em></strong> to view more options for adding this field to the WordPress user profile editor, a Post, or a Page. Select <em>None</em> from the <strong><em>Field Type</em></strong> menu to remove an existing custom field.', UMM_SLUG).'</p>'
    ),
    
    array(
        __('Delete Custom Meta', UMM_SLUG),
        $backup_notice . 
        '<h2>' . __( 'Delete Custom Meta-Data For All Users' ) . '</h2><p>' . __('Deleting custom meta-data will delete the Key and data for ALL existing members AND future registrations.', UMM_SLUG).'</p>'
    ),
    
    array(
        __('Field Types And Field Settings', UMM_SLUG), 
        '<h2>' . __( 'Field Types &amp; Field Settings' ) . '</h2><p>' . __('The <em>Add Custom Meta</em> and <em>Edit Custom Meta</em> screens each contain a <strong><em>Field Type</em></strong> menu. The <em>Field Type</em> menu controls which type of form field, if any, the meta field will be represented as. The form field can be displayed in the user profile editor, or by using a short code, it can also be displayed in a Post or Page.', UMM_SLUG).'</p>
        <p>' . __('When a <em>Field Type</em> is selected from the menu, additional settings for the field will appear. These settings are somewhat generic and may require that you add some additional attributes. For example, if you select the <em>Field Type</em>, "<em>Range</em>", you would need to specify the Range attributes in the <strong><em>Additional Attributes</em></strong> field.', UMM_SLUG).'</p>
        <strong>' . __('Example', UMM_SLUG) . ':</strong>
        <pre>min="1" max="10"</pre>
        <p><strong>' . __('Reference', UMM_SLUG) . ':</strong> <a href="http://www.w3schools.com/html/html5_form_input_types.asp" target="_blank">' . __('W3Schools HTML5 Input Types', UMM_SLUG) . '</a></p>
        <table class="umm-help-field-types-table widefat">
     <tr>
	<th colspan="2">' . __('Settings', UMM_SLUG) . '</th>
</tr>   
<tr class="alternate">
	<th>' . __('Label', UMM_SLUG) . '</th>
	<td>' . __('A text label which is displayed before the field. HTML is allowed.', UMM_SLUG) . '</td>
</tr>
<tr>
	<th>' . __('Classes', UMM_SLUG) . '</th>
	<td>' . __('A single, or multiple CSS classes which will be added to the field. Seperate classes with a space.', UMM_SLUG) . '<br />
    <strong>' . __('Example', UMM_SLUG) . ':</strong><pre>my-class1 my-class2</pre>
    </td>
</tr>
<tr class="alternate">
	<th>' . __('Additional Attributes', UMM_SLUG) . '</th>
	<td>' . __('Any additional attributes to add to the field.', UMM_SLUG) . '<br />
    <strong>' . __('Example', UMM_SLUG) . ':</strong><pre>cols="40" rows="5" title="' . __('My title', UMM_SLUG) . '" placeholder="' . __('Input text here.', UMM_SLUG) . '"</pre></td>
</tr>
<tr>
	<th>' . __('HTML After', UMM_SLUG) . '</th>
	<td>' . __('Any HTML or text to display directly following the field.', UMM_SLUG) . '</td>
</tr>
<tr class="alternate">
	<th>' . __('Required', UMM_SLUG) . '</th>
	<td>' . __('Adds the HTML5 <a title="W3Schools HTML5 Required Attribute Reference" href="http://www.w3schools.com/html5/att_input_required.asp" target="_blank">required</a> attribute to the field. Some older browsers may not support this. Not recommended for use with <em>Radio Button Group</em>.', UMM_SLUG) . '</td>
</tr>
<tr>
	<th>' . __('Allow Tags', UMM_SLUG) . '</th>
	<td>' . __('Strips all HTML tags from the input upon submission.', UMM_SLUG) . '</td>
</tr>
<tr class="alternate">
	<th>' . __('Add To Profile', UMM_SLUG) . '</th>
	<td>' . __('Appends this field to the user profile editor. Otherwise, the field can still be displayed using the form short code. (See <em>Short Codes</em>)', UMM_SLUG) . '</td>
</tr>
<tr>
	<th>' . __('Options - Label', UMM_SLUG) . '</th>
	<td>' . __('A label for the option. This is only visible when <em>Field Type</em> is "<em>Select</em>" or "<em>Radio Button Group</em>"', UMM_SLUG) . '</td>
</tr>
<tr class="alternate">
	<th>' . __('Options - Value', UMM_SLUG) . '</th>
	<td>' . __('The value for the option.', UMM_SLUG) . '</td>
</tr>
</table>'
   ),
    
    array(
        __('Edit Columns', UMM_SLUG),
        '<h2>' . __( 'Editing Home Screen List Columns', UMM_SLUG) . '</h2><p>' . __( 'This screen controls which columns are displayed in the <em>Home</em> screen list.<br /><br />The list on top displays the columns which are currently in use. By selecting an item from the list, and pressing the <strong><em>Remove Selected Column</em></strong> button, columns can be removed from the results table, except the <strong><em>ID</em></strong> and <strong><em>User Login</em></strong> columns, which are required.<br /><br />Columns can be added to the results table using the bottom form. To add a column, select a <strong><em>Key</em></strong> from the menu, enter a <strong><em>Label</em></strong> for the column, and press the <strong><em>Add Column</em></strong> button. The new column will then be added to the results table, and will become searchable. The <em>Label</em> is displayed at the top of the column for identification purposes.', UMM_SLUG) . '</p>'
    ),
    
    array(
        __('Backup &amp; Restore', UMM_SLUG),
        '<h2>' . __( 'Backup &amp; Restore User Meta Data' ) . '</h2><p>' . __( 'There are several options available for backing-up and restoring the wp_usermeta database. This plugin creates the first backup automatically when first installed.', UMM_SLUG) . '</p><ol start="1">
    <li>' . __('<strong>Backup:</strong> Creates a duplicate backup version of the wp_usermeta table, which is then added to the database.', UMM_SLUG) . '</li>
    <li>' . __('<strong>Restore:</strong> Restore a backup which was generated using the above method.', UMM_SLUG) . '</li>   
    <li>' . __('<strong>Generate SQL:</strong> Generates the SQL needed for restoring usermeta data from a database manager, such as phpMyAdmin. Produces a code which can be copied and pasted.', UMM_SLUG) . '</li>
    <li>' . __('<strong>Generate PHP:</strong> Generates the PHP code needed for restoring usermeta data from a PHP file. Copy and paste the code to a PHP file, save it in the root WordPress directory, and run via a Browser.', UMM_SLUG) . '</li>
    <li>' . __('<strong>Generate PHP Restoration File:</strong> Generates a PHP-formatted restoration file on the server in the wp-content/user-meta-manager/backups folder. The generated file can be run from the browser. You will be prompted before restoration will commence.', UMM_SLUG) . '</li>   
    <li>' . __('<strong>Delete Backup Files:</strong> Delete ALL existing backup files from the server.', UMM_SLUG) . '</li>
    </ol>'
    ),
    
    array(
        __('Short Codes', UMM_SLUG),
        '<h2>' . __( 'Short Codes' ) . '</h2><p>' . __( 'A <em>Short Code</em> is a non-HTML code snippet, which can be added to Posts or Pages. The purpose for using a <em>short code</em> is to extend certain plugin features to the Post or Page in which it is inserted.', UMM_SLUG) . '</p>
        <p>' . __( 'Following is a list of the <em>short codes</em> for the User Meta Manager plugin, and their uses.<br />    <strong>Display data for a particular user:</strong>
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
    <pre>json=\'{"access_level":"gold", "sub_level":"silver", "sub_level":"bronze", "sub_level":"aluminum-foil"}\'</pre><br /><br />
    <strong>Display a form in a Post or Page:</strong><br />
    Display a form which allows members to update meta data. The updated data is saved and optionally emailed to a set address. Any Custom Meta keys can be added to the form. Only Custom Meta keys which were made using this plugin will work. Additionally, you must also set a Field Type for the field you wish to display. 
    <pre>[usermeta class="my-form-css-class" submit="Submit" success="Update successful!" error="An error occurred!" fields="test1, test2" vars="one=1&amp;amp;two=2&amp;amp;three=3" email_to="" email_from="" subject="Your email subject" message="A brief introduction.\n\n%s\n\nBest regards,\nWebsite Administrator"]</pre>
    <strong>Form Short Code Attributes</strong><br />
    <ul>
	<li><strong>class:</strong> (Optional) A CSS class to add to the form.</li>
    <li><strong>submit:</strong> (Optional) A label for the submit button.</li>
    <li><strong>success:</strong> (Optional) A message to display if the form submission is successful.</li>
    <li><strong>error:</strong> (Optional)) A message to display if the form submission fails.</li>
    <li><strong>fields:</strong> (Required) A comma delimited list of meta keys to display. Each meta key will be displayed as it\'s set <em>Field Type</em> in the order in which you list them.</li>
    <li><strong>vars:</strong> (Optional) A URL encoded string of extra variable/value pairs you wish to pass with the form submission. Each pair will be converted to a hidden form field, and will be added to the form.</li>
    <li><strong>email_to:</strong> (Optional) An email address to send the results of the form submission to. Leave this empty or remove it altogether to disable this feature. If you use this feature, also use all of the following attributes.</li>
    <li><strong>email_from:</strong> (Used with email_to) An email address as the sender.</li>
    <li><strong>subject:</strong> (Used with email_to) An email subject.</li>
    <li><strong>message:</strong> (Required with email_to) A message to send in the email. <strong>\n</strong> = line break. <strong>%s</strong> = contents of the form submission. <strong>Important:</strong> You must add <strong>%s</strong> where you want the form submission results displayed in the message.</li>
</ul>
    </p>', UMM_SLUG)
    ),
    
    array(
        __('Form Short Code Builder', UMM_SLUG),
        '<h2>' . __('Form Short Code Builder', UMM_SLUG) . '</h2>'
        . umm_shortcode_builder()
    ),
    
    array(
        __('License', UMM_SLUG),
        __( '<p><strong>Disclaimer:</strong><br/>Use at your own risk. No warranty expressed or implied. Always backup your database before making changes.</p>
        <p><strong>License:</strong><br/>&copy;2012 <a href="http://websitedev.biz" target="_blank">http://websitedev.biz</a> <a href="http://jasonlau.biz" target="_blank">http://jasonlau.biz</a></p>
        <p>This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 3 of the License, or (at your option) any later version.</p>
        <p>This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.</p>
        <p>See the GNU General Public License for more details.<br /><a href="http://www.gnu.org/licenses/gpl.html" target="_blank">http://www.gnu.org/licenses/gpl.html</a></p>', UMM_SLUG)
    ),
    
    array(
        __('Contribute Code', UMM_SLUG),
        __( '<h2>You Can Help Make This Plugin Perfect!</h2><p>If you are a talented WordPress developer, who would like to contribute to the developement of this plugin, go to <a href="https://github.com/jasonlau/Wordpress-User-Meta-Manager" target="_blank">https://github.com/jasonlau/Wordpress-User-Meta-Manager</a>. There you will find the development package and <a href="https://github.com/" target="_blank">GitHub</a> repository.</p>
        <p>Additionally, you can contact me at <a href="http://jasonlau.biz/home/contact-me" target="_blank">http://jasonlau.biz/home/contact-me</a>.</p>', UMM_SLUG)
    ),
    
    array(
        __('Donate', UMM_SLUG),
        __( '<h2>Every Little-Bit Helps!</h2><p>Developing this plugin takes a lot of time, and as we all know, time equals money.</p>
        <p>I\'ve given a lot to you, and perhaps you would like to return the favor with a modest donation?</p>
        <p>Use the following PayPal button to make a donation. Your donations help pay for past and future development of this plugin.</p>
        <p>Thanks in advance!</p>
        <form action="https://www.paypal.com/cgi-bin/webscr" method="post">
<input type="hidden" name="cmd" value="_s-xclick">
<input type="hidden" name="hosted_button_id" value="X5Y2R65973XZ6">
<input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_donate_SM.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
<img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1">
</form>', UMM_SLUG)
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