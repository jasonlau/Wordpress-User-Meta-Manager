<?php

/**
 * @author Jason Lau
 * @copyright 2013+
 * @package user-meta-manager
 */
 
if(!defined("UMM_PATH")) die();

function umm_help($contextual_help, $screen_id, $screen) {
    global $wpdb;
    if($screen->id != 'users_page_user-meta-manager')
    return;
    $umm_settings = umm_get_option('settings');
    $pro_settings = '';
    $pro = false;
    
    if(umm_is_pro()):
       if(function_exists('umm_pro_settings')):
          $pro_settings = umm_pro_settings($umm_settings);
          $pro = true;
       endif; 
    endif;
    
    $retain_data = (!isset($umm_settings['retain_data']) || empty($umm_settings['retain_data'])) ? 'yes' : $umm_settings['retain_data'];
    $max_users = (!isset($umm_settings['max_users']) || empty($umm_settings['max_users']) || $umm_settings['max_users']>100) ? 100 : $umm_settings['max_users'];
    $shortcut_editing = (!isset($umm_settings['shortcut_editing']) || empty($umm_settings['shortcut_editing'])) ? 'no' : $umm_settings['shortcut_editing'];
    $pfields_position = (!isset($umm_settings['pfields_position']) || empty($umm_settings['pfields_position'])) ? 1 : $umm_settings['pfields_position'];
    $duplicate_check_override = (!isset($umm_settings['duplicate_check_override']) || empty($umm_settings['duplicate_check_override'])) ? 'no' : $umm_settings['duplicate_check_override'];
    $bot_field = (!isset($umm_settings['bot_field']) || empty($umm_settings['bot_field'])) ? $umm_settings['bot_field'] : $umm_settings['bot_field'];    
    $backup_notice = '<div class="umm-warning">' . __('<strong>IMPORTANT:</strong> <ins>Always</ins> backup your data before making changes to your website.', UMM_SLUG) . '</div>';  
    $pro_message = ($pro) ? __(' <strong>You are using the Pro version. This setting will not be used.</strong>', UMM_SLUG) : __(' The <a href="http://jasonlau.biz/home/membership-options#umm-pro" target="_blank">Pro Plugin</a> extends User Meta Manager\'s capabilities.', UMM_SLUG);
    
    $default_html_before = '<h3 class="umm-custom-fields">[section-title]</h3>
<table class="form-table umm-custom-fields">
   <tbody>';
   $default_html_during = '<tr><th>[label]</th><td>[field]</td></tr>';
   $default_html_after = '</tbody>';
    
    $html_before_register = (!isset($umm_settings['html_before_register'])) ? $default_html_before : $umm_settings['html_before_register'];
    $html_during_register = (!isset($umm_settings['html_during_register'])) ? $default_html_during : $umm_settings['html_during_register'];
    $html_after_register = (!isset($umm_settings['html_after_register'])) ? $default_html_after : $umm_settings['html_after_register'];
    
    $html_before_profile = (!isset($umm_settings['html_before_profile'])) ? $default_html_before : $umm_settings['html_before_profile'];
    $html_during_profile = (!isset($umm_settings['html_during_profile'])) ? $default_html_during : $umm_settings['html_during_profile'];
    $html_after_profile = (!isset($umm_settings['html_after_profile'])) ? $default_html_after : $umm_settings['html_after_profile'];
    
    $html_before_shortcode = (!isset($umm_settings['html_before_shortcode'])) ? $default_html_before : $umm_settings['html_before_shortcode'];
    $html_during_shortcode = (!isset($umm_settings['html_during_shortcode'])) ? $default_html_during : $umm_settings['html_during_shortcode'];
    $html_after_shortcode = (!isset($umm_settings['html_after_shortcode'])) ? $default_html_after : $umm_settings['html_after_shortcode'];
    
    $html_before_adduser = (!isset($umm_settings['html_before_adduser'])) ? $default_html_before : $umm_settings['html_before_adduser'];
    $html_during_adduser = (!isset($umm_settings['html_during_adduser'])) ? $default_html_during : $umm_settings['html_during_adduser'];
    $html_after_adduser = (!isset($umm_settings['html_after_adduser'])) ? $default_html_after : $umm_settings['html_after_adduser'];
    
    switch($retain_data){
        case 'no':
        $retain_yes = '';
        $retain_no = ' selected="selected"';
        break;
        
        default:
        $retain_yes = ' selected="selected"';
        $retain_no = '';
    }
     
    switch($shortcut_editing){
        case 'yes':
        $shortcut_editing_yes = ' selected="selected"';
        $shortcut_editing_no = '';
        break;
        
        default:
        $shortcut_editing_yes = '';
        $shortcut_editing_no = ' selected="selected"';
    }
    
    switch($pfields_position){
        case 0:
        $pfields_position_top = ' selected="selected"';
        $pfields_position_bottom = '';
        break;
        
        default:
        $pfields_position_top = '';
        $pfields_position_bottom = ' selected="selected"';
    }
    
    switch($duplicate_check_override){
        case 'yes':
        $duplicate_check_override_yes = ' selected="selected"';
        $duplicate_check_override_no = '';
        break;
        
        default:
        $duplicate_check_override_yes = '';
        $duplicate_check_override_no = ' selected="selected"';
    }
    
    $tabs = array(array(
        __('Donate/Extend', UMM_SLUG),
        __( '<h2>Every Little Bit Helps!</h2>
        <p>Developing this plugin takes a lot of time, and as we all know, time equals money.</p>
        <p>I\'ve given a lot to you, and perhaps you would like to return the favor with a modest donation?</p>
        <p>Use the following PayPal button to make a donation. Your donations help pay for past and future development of this plugin.</p>
        <p>Thanks in advance!</p>
        <form action="https://www.paypal.com/cgi-bin/webscr" method="post">
<input type="hidden" name="cmd" value="_s-xclick">
<input type="hidden" name="hosted_button_id" value="X5Y2R65973XZ6">
<input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_donate_SM.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
<img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1">
</form><h2><i class="dashicons dashicons-awards"></i>User Meta Manager Pro Extension</h2><p>User Meta Manager Pro is a premium extension for the User Meta Manager plugin. User Meta Manager Pro extends the capabilities of this plugin.</p>
<p><strong>Only the Pro plugin will be developed in the future.</strong> No additional features will be added to the free version.</p>
        <p><a href="http://jasonlau.biz/home/membership-options#umm-pro" target="_blank">Get the Pro Plugin!</a></p>
        <h3>Pro Features</h3>
        <ul>
        <li>For websites with an unlimited number of users.</li>
        <li>Advanced field validation using <em>match</em> or <em>search</em> methods.</li>
        <li>Regular expressions matching.</li>
        <li>Canned/saved regular expressions. Easily save and reuse your favorite regex patterns!</li>
        <li>Case sensitive or insensitive validation search. Easily ban words or phrases from custom fields.</li>
        <li>Custom error messages.</li>
        <li>Redirect a user after custom form submission.</li>
        <li>Backup, restore, export/import User Meta Manager settings.</li>
        <li>Restore/import wp_usermeta table from User Meta Manager CSV backup.</li>
        <li>Backup, restore, export/import WordPress Users.</li>
        <li>More to come ...</li>
        </ul>
        
        ', UMM_SLUG)
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
	<td>
        <strong>' . __('Maximum Number Of Users To Query', UMM_SLUG) . '</strong><br />
        <input type="number" name="max_users" min="1" max="100" value="' . $max_users . '"><br />
        <span>' . __('This is the maximum number of users this plugin will handle.', UMM_SLUG) . $pro_message . '</span>
        </td>
</tr>

<tr>
	<td><strong>' . __('Meta Editor Shortcut', UMM_SLUG) . '</strong><br />
        <select size="1" name="shortcut_editing">
	<option value="' . __('yes', UMM_SLUG) . '"' . $shortcut_editing_yes . '>' . __('Yes', UMM_SLUG) . '</option>
	<option value="' . __('no', UMM_SLUG) . '"' . $shortcut_editing_no . '>' . __('No', UMM_SLUG) . '</option>
</select><br /><span>' . __('Skips step 1 in the single-member meta data editor, and displays the entire list of meta keys and values for the selected member. Otherwise, you will have to select a single key to edit.', UMM_SLUG) . '</span></td>
</tr>

<tr class="alternate">
	<td><strong>' . __('Duplicate Meta Key Check Override', UMM_SLUG) . '</strong><br />
        <select size="1" name="duplicate_check_override">
	<option value="' . __('yes', UMM_SLUG) . '"' . $duplicate_check_override_yes . '>' . __('Yes', UMM_SLUG) . '</option>
	<option value="' . __('no', UMM_SLUG) . '"' . $duplicate_check_override_no . '>' . __('No', UMM_SLUG) . '</option>
</select><br /><span>' . __('Select <em>Yes</em> to override the safety feature that prevents existing meta keys from being overwritten while new custom meta keys are introduced.', UMM_SLUG) . '</span></td>
</tr>


<tr>
	<td><strong>' . __('Bot Field Name', UMM_SLUG) . '</strong><br />
        <input type="text" name="bot_field" value="' . $bot_field . '"><br /><span>' . __('Name of the hidden form field used to test for spam-bots.', UMM_SLUG) . '</span></td>
</tr>

<tr class="alternate">
	<td><strong>' . __('Custom Field Section Title', UMM_SLUG) . '</strong><br />
        <input type="text" name="section_title" value="' . $umm_settings['section_title'] . '"><br /><span>' . __('Optional title for the section of custom fields. This option is utilized in the <em>HTML Markup</em> options below.', UMM_SLUG) . '</span></td>
</tr>

<tr>
	<td><strong>' . __('HTML Markup', UMM_SLUG) . '</strong>
    <p>Here you can customize the HTML markup User Meta Manager uses while displaying custom meta fields. Each of the following tabs controls the HTML markup for specific screens.</p>
    
    <div id="umm-tabs">
  <ul>
    <li><a href="#umm-tabs-1">Registration</a></li>
    <li><a href="#umm-tabs-2">Profile/Edit User</a></li>
    <li><a href="#umm-tabs-3">Short Code</a></li>
    <li><a href="#umm-tabs-4">Add User</a></li>
  </ul>
  <div id="umm-tabs-1">
  <p>User registration form markup.</p>
  <strong>' . __('HTML Before', UMM_SLUG) . '</strong><br />
        <textarea class="umm-settings-textarea" name="html_before_register">' . stripslashes($html_before_register) . '</textarea><br /><span>' . __('HTML before the loop.', UMM_SLUG) . '</span><br /><br />
        <strong>' . __('HTML During', UMM_SLUG) . '</strong><br />
        <textarea class="umm-settings-textarea" name="html_during_register">' . stripslashes($html_during_register) . '</textarea><br /><span>' . __('HTML during the loop.', UMM_SLUG) . '</span><br /><br />
        <strong>' . __('HTML After', UMM_SLUG) . '</strong><br />
        <textarea class="umm-settings-textarea" name="html_after_register">' . stripslashes($html_after_register) . '</textarea><br /><span>' . __('HTML after the loop.', UMM_SLUG) . '</span>
  </div>
  <div id="umm-tabs-2">
  <p>Profile and Edit User forms markup.</p>
  <strong>' . __('HTML Before', UMM_SLUG) . '</strong><br />
        <textarea class="umm-settings-textarea" name="html_before_profile">' . stripslashes($html_before_profile) . '</textarea><br /><span>' . __('HTML before the loop.', UMM_SLUG) . '</span><br /><br />
        <strong>' . __('HTML During', UMM_SLUG) . '</strong><br />
        <textarea class="umm-settings-textarea" name="html_during_profile">' . stripslashes($html_during_profile) . '</textarea><br /><span>' . __('HTML during the loop.', UMM_SLUG) . '</span><br /><br />
        <strong>' . __('HTML After', UMM_SLUG) . '</strong><br />
        <textarea class="umm-settings-textarea" name="html_after_profile">' . stripslashes($html_after_profile) . '</textarea><br /><span>' . __('HTML after the loop.', UMM_SLUG) . '</span>
  </div>
  <div id="umm-tabs-3">
  <p>Short Codes markup.</p>
  <strong>' . __('HTML Before', UMM_SLUG) . '</strong><br />
        <textarea class="umm-settings-textarea" name="html_before_shortcode">' . stripslashes($html_before_shortcode) . '</textarea><br /><span>' . __('HTML before the loop.', UMM_SLUG) . '</span><br /><br />
        <strong>' . __('HTML During', UMM_SLUG) . '</strong><br />
        <textarea class="umm-settings-textarea" name="html_during_shortcode">' . stripslashes($html_during_shortcode) . '</textarea><br /><span>' . __('HTML during the loop.', UMM_SLUG) . '</span><br /><br />
        <strong>' . __('HTML After', UMM_SLUG) . '</strong><br />
        <textarea class="umm-settings-textarea" name="html_after_shortcode">' . stripslashes($html_after_shortcode) . '</textarea><br /><span>' . __('HTML after the loop.', UMM_SLUG) . '</span>
  </div>
  <div id="umm-tabs-4">
  <p>Add User form markup.</p>
  <strong>' . __('HTML Before', UMM_SLUG) . '</strong><br />
        <textarea class="umm-settings-textarea" name="html_before_adduser">' . stripslashes($html_before_adduser) . '</textarea><br /><span>' . __('HTML before the loop.', UMM_SLUG) . '</span><br /><br />
        <strong>' . __('HTML During', UMM_SLUG) . '</strong><br />
        <textarea class="umm-settings-textarea" name="html_during_adduser">' . stripslashes($html_during_adduser) . '</textarea><br /><span>' . __('HTML during the loop.', UMM_SLUG) . '</span><br /><br />
        <strong>' . __('HTML After', UMM_SLUG) . '</strong><br />
        <textarea class="umm-settings-textarea" name="html_after_adduser">' . stripslashes($html_after_adduser) . '</textarea><br /><span>' . __('HTML after the loop.', UMM_SLUG) . '</span>
  </div>
</div>
<code><strong>[section-title]</strong> ' . __('Replaced by the <em>Custom Field Section Title</em> option.', UMM_SLUG) . '<br /><strong>[field]</strong> ' . __('Replaced by the field\'s HTML and HTML After option, if any.', UMM_SLUG) . '<br /><strong>[label]</strong> ' . __('Replaced by the field label. Example: My Field', UMM_SLUG) . '<br /><strong>[field-name]</strong> ' . __('Replaced by the field name. Example: my_field', UMM_SLUG) . '<br /><strong>[field-slug]</strong> ' . __('Replaced by the field slug. Example: my-field', UMM_SLUG) . '</code>
    </td>
</tr>
' . $pro_settings . '

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
</table>
</form>'
    ),
    
    array(
        __('The Home Screen', UMM_SLUG),
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
	<td>' . __('Adds the HTML5 <a title="W3Schools HTML5 Required Attribute Reference" href="http://www.w3schools.com/html5/att_input_required.asp" target="_blank">required</a> attribute to the field. Some older browsers may not support this. Not recommended for use with <em>Radio Button Group</em> or <em>Checkbox Group</em>.', UMM_SLUG) . '</td>
</tr>
<tr>
	<th>' . __('Allow Tags', UMM_SLUG) . '</th>
	<td>' . __('Strips all HTML tags from the input upon submission.', UMM_SLUG) . '</td>
</tr>
<tr class="alternate">
	<th>' . __('Add To Profile', UMM_SLUG) . '</th>
	<td>' . __('Appends this field to the user profile editor, registration, and administrative screens. Otherwise, the field can still be displayed using the form short code. (See <em>Short Codes</em>)', UMM_SLUG) . '</td>
</tr>
<tr>
	<th>' . __('Unique Value', UMM_SLUG) . '</th>
	<td>' . __('Require users to input a unique value. A user cannot enter the same value as another user.', UMM_SLUG) . '</td>
</tr>
<tr class="alternate">
	<th>' . __('Roles', UMM_SLUG) . '</th>
	<td>' . __('Select which user roles can view this field. Either select <em>All</em> for all roles or select multiple and specific roles.', UMM_SLUG) . '</td>
</tr>
<tr>
	<th>' . __('Allow Multiple Selections', UMM_SLUG) . '</th>
	<td>' . __('For Select Menus only. Make this field accept multiple selections.', UMM_SLUG) . '</td>
</tr>
<tr class="alternate">
	<th>' . __('Size', UMM_SLUG) . '</th>
	<td>' . __('For Select Menus only. The number of options visible to the user. Overflow will scroll.', UMM_SLUG) . '</td>
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
        <p>' . __( 'Following is a list of the <em>short codes</em> for the User Meta Manager plugin, and their uses.</p>
    <h2>Display User Meta Data</h2>
    <p><strong>Display a single meta key, or core user data for a particular user:</strong>
    <pre>[usermeta key="meta key" user="user id"]</pre><br />
    Additionally, the following core user data can also be used instead of a meta key: ID, user_login, user_nicename, user_email, user_url, user_registered, display_name.
    <br /><br />
    <strong>Display a single meta key for the current user:</strong>
    <pre>[usermeta key="meta key"]</pre>
    <br />
    <strong>Display multiple meta keys for the current user:</strong>
    <pre>[usermeta keys="key1, key2, key3" after_key=" "]<br /><br /><strong>Example Output:</strong><br /><br />key1 key2 key3</pre>
    <br />
    <strong>Display multiple meta keys for the current user in a list:</strong>
    <pre>&lt;ul&gt;[usermeta keys="key1, key2, key3" before_key="&amp;lt;li&amp;gt;" after_key="&amp;lt;/li&amp;gt;"]&lt;/ul&gt;<br /><br /><strong>Example Output:</strong><ul><li>key1</li><li>key2</li><li>key3</li></ul></pre>
    <br />
    <strong>Display multiple meta keys for a particular user:</strong>
    <pre>[usermeta keys="key1, key2, key3" user="user id"]</pre><br />
    Additionally, the following core user data can also be used: ID, user_login, user_nicename, user_email, user_url, user_registered, display_name.</p>
    <h2>Restrict User Access To Content</h2>
    <p><strong>Restrict access to content based on meta key and value:</strong>
    <pre>[useraccess key="meta key" value="meta value" message="You do not have permission to view this content."]Restricted content.[/useraccess]</pre>
    Allowed users will have a matching meta value.<br /><br /><br />
    <strong>Restrict access to content based on user ID:</strong>
    <pre>[useraccess users="1 22 301" message="You do not have permission to view this content."]Restricted content.[/useraccess]</pre>
    Allowed user IDs are listed in the <em>users</em> attribute.<br /><br /><br />
    <strong>Restrict access to content and bounce the user to a different page:</strong>
    <pre>[useraccess key="meta key" value="meta value" message="You do not have permission to view this content." url="http://jasonlau.biz"]Restricted content.[/useraccess]</pre>
    Restricted users are redirected to the address contained in the <strong>url</strong> attribute.<br /><br /><br />
    <strong>Bounce restricted users to a different page:</strong>
    <pre>[useraccess users="1 22 301" key="meta key" value="meta value" message="You do not have permission to view this content." url="http://jasonlau.biz"]</pre>
    In this example, allowed users are listed in the <strong>users</strong> attribute. However, allowed users must have a matching meta key and value, or they will be restricted also. Restricted users are redirected to the address contained in the <strong>url</strong> attribute. <strong>Important:</strong> This method does not hide restricted content. Use the previous method to hide restricted content from view.<br /><br /><br />
    <strong>Restrict access based on multiple meta keys and values:</strong>
    <pre>[useraccess json=\'{"access_level":"gold"},{"sub_level":"silver"}\' message="You need permission to view this content."]Restricted content.[/useraccess]</pre>
    The <em>json</em> attribute is used to define a list of meta keys and values. The list must be JSON encoded, as seen in the example above. A user with a meta key and value that matches any one or more of those listed will be granted access to restricted content.<br/><br/>
    JSON formatting -
    <pre>{"meta_key":"meta_value"},{"meta_key":"meta_value"},{"meta_key":"meta_value"}</pre>
    Additionally, you could repeat the same meta key multiple times.
    <pre>json=\'{"access_level":"gold"},{"sub_level":"silver"},{"sub_level":"bronze"},{"sub_level":"aluminum-foil"}\'</pre></p>
    <h2>User Meta Forms</h2>
    <p><strong>Display a form in a Post or Page:</strong><br />
    Display a form which allows members to update meta data. The updated data is saved and optionally emailed to a set address. Any Custom Meta keys can be added to the form. Only Custom Meta keys which were made using this plugin will work. Additionally, you must also set a Field Type for the field you wish to display. Use the <strong>Form Short Code Builder</strong> tab on the left to easily generate the short code.
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
    <li><i class="dashicons dashicons-awards"></i><strong>redirect:</strong> Insert a URL to redirect the user to a different page after the form is submitted. This method uses JavaScript to redirect the user.</li>
</ul></p>
<h2>Query The Database</h2>
You can query the wp_users and wp_usermeta tables and display results in a Post or Page with the <strong>ummquery</strong> short code.
<pre>[ummquery before_result="&lt;section class=\'umm-query-result\'&gt;" after_result="&lt;/section&gt;" before_item="&lt;ul class=\'umm-query-user\'&gt;" after_item="&lt;/ul>" item="&lt;li class=\'umm-query-item\'&gt;&lt;strong&gt;%k&lt;/strong&gt;: %v&lt;/li&gt;" key_format="ucwords" value_format="ucfirst" list="user_nicename, meta_value" where="usermeta.meta_key=\'my_custom_key\'"]</pre>
The above example produces a list of users, displaying the meta keys listed in the <strong><em>list</em></strong> attribute.<br /><br />
<strong>ummquery Short Code Attributes</strong><br />
    <ul>
    <!-- TODO
<li><strong>query:</strong> (Optional) Run a basic database query. Omit this to automatically join the ' . $wpdb->users . ' and  ' . $wpdb->usermeta . ' table in a query.</li>
-->
	<li><strong>before_result:</strong> HTML to display before the set of results.</li>
    <li><strong>after_result:</strong> HTML to display after the set of results.</li>
    <li><strong>before_item:</strong> HTML to display before each item.</li>
    <li><strong>after_item:</strong> HTML to display after each item.</li>
    <li><strong>item:</strong> HTML to display with each item. <strong>%k</strong> is replaced by the item key. <strong>%v</strong> is replaced by the item value.</li>
    <li><strong>key_format:</strong> (Optional) Strips the underscore from and automatically formats the item key. <strong>lc</strong> = All lowercase. <strong>uc</strong> = All uppercase. <strong>ucfirst</strong> = Uppercase the first word. <strong>ucwords</strong> = Uppercase the first letter in each word.
    For example, using key_format <strong>ucwords</strong> on the key <strong>my_custom_key</strong> would produce <strong>My Custom Key</strong>.</li>
    <li><strong>list:</strong> A comma-delimited list of database keys to list in the results.</li>
    <li><strong>where:</strong> (Optional) MySQL WHERE statement. Prefix wp_usermeta columns with <strong><em>usermeta</em></strong> and wp_users columns with <strong><em>users</em></strong>.
    <pre>where="usermeta.meta_key=\'my_custom_key\' AND usermeta.meta_value=\'abc123\' AND users.user_url!=\'\'"</pre>
    </li>
</ul>
<h2>Nested Short Codes</h2>
<p>You can use short codes inside of short codes to restrict access to meta data forms.
<pre>[useraccess users="1 22 301" message="You do not have permission to view this content."][usermeta class="my-form-css-class" submit="Submit" success="Update successful!" error="An error occurred!" fields="test1, test2" vars="one=1&amp;amp;two=2&amp;amp;three=3" email_to="" email_from="" subject="Your email subject" message="A brief introduction.\n\n%s\n\nBest regards,\nWebsite Administrator"][/useraccess]</pre>
In the above example, only members with a matching user id can access the meta data form.</p>
<h2>One Code To Rule Them All!</h2>
<p>Version 3.1.3 adds the <strong>[umm]</strong> short code, which can replace any of the above short codes.</p> 
<pre>[umm meta]</pre>
The <strong>meta</strong> attribute causes the <strong>[umm]</strong> short code to function exactly like the <strong>[usermeta]</strong> short code, and accepts the same arguments also.
<pre>[umm access]</pre>
The <strong>access</strong> attribute causes the <strong>[umm]</strong> short code to function exactly like the <strong>[useraccess]</strong> short code, and accepts the same arguments also.
<pre>[umm query]</pre>
The <strong>query</strong> attribute causes the <strong>[umm]</strong> short code to function exactly like the <strong>[ummquery]</strong> short code, and accepts the same arguments also.
<p>Additionally, the <strong>[umm]</strong> short code will perform the following functions -</p> 
<h3>Display User Profile Editor</h3>
<p>The <strong>[umm]</strong> short code can be used to display the entire user profile editor anyplace a short code can be used.</p>
<pre>[umm profile]</pre>
<p>The above example displays the entire user profile editor.</p> 
<p>Using the <strong>hide</strong> attribute, you can hide any part of the page. The hide attribute accepts any <a href="http://api.jquery.com/category/selectors/" target="_blank">selectors jQuery accepts</a>. You can list any number of objects to hide. The list must be comma-delimited.</p>
<pre>[umm profile hide="h3:contains(\'Personal Options\'), table:contains(\'Admin Color Scheme\'), h3:contains(\'Name\'), table:contains(\'Username\'), h3:contains(\'Contact Info\'), table:contains(\'Website\'), tr:contains(\'Password\')"]</pre>
<p>The example above hides several sections of the profile editor.</p>
<p>If the user is not logged in, a login form will be displayed instead of the profile editor. The <strong>redirect</strong> attribute can be used to send the user to a specific URL address on login/logout.</p>
 <pre>[umm profile redirect="http://homeurl"]</pre>
 <p>Alternatively the <strong>bounce</strong> attribute can be used to send a visitor to a specific URL address. If the user is not logged in, JavaScript redirects the user to a different page.</p>
 <pre>[umm profile bounce="http://homeurl"]</pre>
 <p>The <strong>loading</strong> attribute controls the message that is displayed while the profile editor is loading.</p>
 <pre>[umm profile loading="Loading Profile ..."]</pre>
 <p>Alternatively, the <strong>loading</strong> attribute accepts HTML, so an image can be displayed instead of text.</p>
 <pre>[umm profile loading="&lt;img src=\'loading.gif\' /&gt;"]</pre>
<p><strong>Note:</strong> JavaScript is used to load and post the form. This may not work well for some themes.</p>
<h3>Display A Login Form</h3>
<pre>[umm login]</pre>
The <strong>redirect</strong> attribute can be used to send the user to a specific URL address on login.
<pre>[umm login redirect="http://homeurl"]</pre>
<h3>Display A Login And Logout Link</h3>
<pre>[umm loginout]</pre>
The <strong>redirect</strong> attribute can be used to send the user to a specific URL address on login/logout.
<pre>[umm loginout redirect="http://homeurl"]</pre>
    ', UMM_SLUG)
    ),
    
    array(
        __('PHP API', UMM_SLUG),
        '<h2>' . __('PHP API', UMM_SLUG) . '</h2><p>' . __( 'Below are some PHP methods you can use to test User Meta Manager data.', UMM_SLUG) . '</p>
        <ul class="umm-methods-list">
        <li><strong class="umm-method">umm_value_contains($key, $search_for, $exact, $user_id)</strong>
        <p>' . __('Test if a meta value contains a string.', UMM_SLUG) . '</p>
        <ul>
          <li><strong>$key</strong> (' . __('string', UMM_SLUG) . ') ' . __('The meta key to test.', UMM_SLUG) . '</li>
          <li><strong>$search_for</strong> (' . __('string', UMM_SLUG) . ') ' . __('The string to search for.', UMM_SLUG) . '</li>
          <li><strong>$exact</strong> (' . __('boolean', UMM_SLUG) . ') ' . __('Optional exact match. Default is case-insensitive.', UMM_SLUG) . '</li>
          <li><strong>$user_id</strong> (' . __('number', UMM_SLUG) . ') ' . __('Optional user ID. Default is the current user.', UMM_SLUG) . '</li>
        </ul>
        <strong>' . __('Example', UMM_SLUG) . ':</strong>
        <pre>$meta_key_to_search = "my_key";
$string_to_search_for = "abc";
$case_sensitive = true;
if(umm_value_contains($meta_key_to_search, $string_to_search_for, $case_sensitive)){
    // ' . __('Exact match for abc', UMM_SLUG) . '
} else {
    // ' . __('No exact match for abc', UMM_SLUG) . '
}</pre></li>
        <li><strong class="umm-method">umm_value_is($key, $search_for, $user_id)</strong>
        <p>' . __('Test if a meta value is an exact match.', UMM_SLUG) . '</p>
        <ul>
          <li><strong>$key</strong> (' . __('string', UMM_SLUG) . ') ' . __('The meta key to test.', UMM_SLUG) . '</li>
          <li><strong>$search_for</strong> (' . __('string', UMM_SLUG) . ') ' . __('The string to match.', UMM_SLUG) . '</li>
          <li><strong>$user_id</strong> (' . __('number', UMM_SLUG) . ') ' . __('Optional user ID. Default is the current user.', UMM_SLUG) . '</li>
        </ul>
        <strong>' . __('Example', UMM_SLUG) . ':</strong>
        <pre>$meta_key_to_test = "my_key";
$string_to_match = "abc";
if(umm_value_is($meta_key_to_test, $string_to_match)){
    // ' . __('Exact match for abc', UMM_SLUG) . '
} else {
    // ' . __('No exact match for abc', UMM_SLUG) . '
}</pre></li>
</ul>
        '
    ),
    
    array(
        __('Form Short Code Builder', UMM_SLUG),
        '<h2>' . __('Form Short Code Builder', UMM_SLUG) . '</h2>'
        . umm_shortcode_builder()
    ),
    
    array(
        __('License', UMM_SLUG),
        __( '<p><strong>Disclaimer:</strong><br/>Use at your own risk. No warranty expressed or implied. Always backup your database before making changes.</p>
        <p><strong>License:</strong><br/>&copy;2014+ <a href="http://jasonlau.biz" target="_blank">http://jasonlau.biz</a></p>
        <p>User Meta Manager (free version) is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 3 of the License, or (at your option) any later version.</p>
        <p>This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.</p>
        <p>See the GNU General Public License for more details.<br /><a href="http://www.gnu.org/licenses/gpl.html" target="_blank">http://www.gnu.org/licenses/gpl.html</a></p>', UMM_SLUG)
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