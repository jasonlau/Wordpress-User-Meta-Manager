<?php
if(!defined("UMM_PATH")) die();

function umm_help($contextual_help, $screen_id, $screen) {
	$screen = get_current_screen();
    if($screen->id != "users_page_user-meta-manager")
    return;
    
    $backup_notice = '<div class="umm-warning">' . __('<strong>IMPORTANT:</strong> <ins>Always</ins> backup your data before making changes to your website.', 'user-meta-manager') . '</div>';
    $tabs = array(array(
        __('What is User Meta?'),
        $backup_notice . 
        '<p>' . __('What is <em>User Meta</em>? <em>User Meta</em> is user-specific data which is stored in the <em>wp_usermeta</em> database table. This data is stored by WordPress and various and sundry plugins, and can consist of anything from profile information to membership levels.', 'user-meta-manager') . '</p>'
    ),
    array(
        __('Home'),
        $backup_notice . 
        '<p>' . __( 'The User Meta Manager table displays a list of your website\'s users from which you may select a user to edit. Follow ' ) . '</p>
        <p>' . __( 'Locate from the list which User you want to work with, and place your mouse over that item. The following links will appear as your mouse moves over each user.' ) . '</p>
        <ol start="1">
    <li>' . __('<strong>Add Meta:</strong> Add new, custom meta data for each user, or for <em>All Users</em>. If the meta data is added to <em>All Users</em>, new registrations will automatically receive the meta key and default value. Only use letters, numbers, and underscores while adding and naming new meta keys. Meta values can consist of any characters.', 'user-meta-manager') . '</li>
    <li>' . __('<strong>Edit Meta:</strong> Edit existing meta data for each member.', 'user-meta-manager') . '</li>   
    <li>' . __('<strong>Delete Meta:</strong> Delete individual meta keys for a single user or for <em>All Users</em>. You can select which meta data to delete from the drop menu.', 'user-meta-manager') . '</li>
    </ol>'
    ),
    
    array(
        __('Add Custom Meta'),
        $backup_notice . 
        '<p>'.__('Adding custom meta data will add the meta key and value to all existing users. The value you set will become the default value for all users. New registrations will receive the custom meta key and default value. Check the checkbox to view more options for adding this field to the WordPress user profile editor.', 'user-meta-manager').'</p>'
    ),
    array(
        __('Edit Custom Meta'),
        $backup_notice . 
        '<p>' . __( '' ) . '</p>'
    ),
    array(
        __('Delete Custom Meta'),
        $backup_notice . 
        '<p>' . __( '' ) . '</p>'
    ),
    array(
        __('Edit Columns'),
        '<p>' . __( 'This form controls which columns are displayed in the results table. The list on top displays the columns which are currently in use. By selecting an item from the list, and pressing the <em>Remove Selected Column</em> button, columns can be removed from the results table, except the <em>ID</em> and <em>User Login</em> columns, which are required. Columns can be added to the results table using the bottom form. To add a column, select a <em>Key</em> from the menu, enter a <em>Label</em> for the column, and press the <em>Add Column</em> button. The new column will then be added to the results table, and will become searchable. The <em>Label</em> is displayed at the top of the column for identification purposes.' ) . '</p>'
    ),
    array(
        __('Backup &amp; Restore'),
        '<p>' . __( '' ) . '</p>'
    ),
    array(
        __('Shortcodes'),
        '<p>' . __( '' ) . '</p>'
    ),
    array(
        __('License'),
        __( '<p><strong>Disclaimer:</strong><br/>Use at your own risk. No warranty expressed or implied. Always backup your database before making changes.</p>
        <p><strong>License:</strong><br/>&copy;2012 <a href="http://websitedev.biz" target="_blank">http://websitedev.biz</a> <a href="http://jasonlau.biz" target="_blank">http://jasonlau.biz</a></p>
        <p>This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 3 of the License, or (at your option) any later version.</p>
        <p>This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.</p>
        <p>See the GNU General Public License for more details.<br /><a href="http://www.gnu.org/licenses/gpl.html" target="_blank">http://www.gnu.org/licenses/gpl.html</a></p>')
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