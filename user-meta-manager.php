<?php

/**
 * Plugin Name: User Meta Manager
 * Plugin URI: http://websitedev.biz
 * Description: Add, edit, or delete user meta data with this handy plugin. Easily restrict access or insert user meta data into posts or pages.
 * Version: 2.0.0 beta-dev 1.1
 * Author: Jason Lau
 * Author URI: http://websitedev.biz
 * Text Domain: user-meta-manager
 * Disclaimer: Use at your own risk. No warranty expressed or implied.
 * 
 * Always backup your database before making changes.
 * 
 * Copyright 2012 http://websitedev.biz http://jasonlau.biz
 * 
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  
 * 
 * See the GNU General Public License for more details.
 * http://www.gnu.org/licenses/gpl.html
 */

 if (realpath(__FILE__) == realpath($_SERVER['SCRIPT_FILENAME'])) {
    exit('Please don\'t access this file directly.');
}

define('UMM_VERSION', '2.0.0 beta-dev 1.1');
define("UMM_PATH", plugin_dir_path(__FILE__) . '/');

include(UMM_PATH . 'includes/umm-table.php');
include(UMM_PATH . 'includes/umm-contextual-help.php');

function umm_add_custom_meta(){
    global $wpdb;
    $user_id = $_REQUEST['u'];
    $output = umm_fyi('<p>'.__('Insert a key and default value in the fields below.', 'user-meta-manager').'</p>');
    $output .= '<form id="umm_update_user_meta_form" method="post">
    <strong>'.__('Key', 'user-meta-manager').':</strong><br />
    <input name="meta_key" type="text" value="" placeholder="'.__('Meta Key', 'user-meta-manager').'" /><br />
    <strong>'.__('Default Value', 'user-meta-manager').':</strong><br />
    <textarea rows="3" cols="40" name="meta_value"  placeholder=""></textarea>
    ';
    $output .= umm_profile_field_editor();
    $output .= '<br />
    <input id="umm_update_user_meta_submit" data-form="umm_update_user_meta_form" data-subpage="umm_update_user_meta" data-wait="'.__('Wait...', 'user-meta-manager').'" class="button-primary" type="submit" value="'.__('Submit', 'user-meta-manager').'" />
    <input name="all_users" type="hidden" value="true" /><input name="mode" type="hidden" value="add" /><input name="u" type="hidden" value="all" /><input name="return_page" type="hidden" value="admin-ajax.php?action=umm_add_custom_meta&u=0" />
    </form>  
    ';
    print $output;
    exit;
}

function umm_add_user_meta(){
    global $wpdb;
    $user_id = $_REQUEST['u'];
    $output = umm_button('home', null, "umm-back-button") . umm_subpage_title($user_id, __('Adding Meta Data For %s', 'user-meta-manager'));
    $output .= umm_fyi('<p>'.__('Insert a meta key and default value and press <em>Submit</em>. The new meta-data will be applied to this user only, and can only be managed via the table actions, and not via the <em>Custom Meta</em> actions.', 'user-meta-manager').'</p>');
    $output .= '<form id="umm_update_user_meta_form" method="post">
    <strong>'.__('Key', 'user-meta-manager').':</strong><br />
    <input name="meta_key" type="text" value="" placeholder="'.__('Meta Key', 'user-meta-manager').'" /><br />
    <strong>'.__('Value', 'user-meta-manager').':</strong><br />
    <input name="meta_value" type="text" value="" size="40" placeholder="'.__('Default Value', 'user-meta-manager').'" />';
    $output .= '<br />
    <input id="umm_update_user_meta_submit" data-form="umm_update_user_meta_form" data-subpage="umm_update_user_meta" data-wait="'.__('Wait...', 'user-meta-manager').'" class="button-primary" type="submit" value="'.__('Submit', 'user-meta-manager').'" />
    <input name="mode" type="hidden" value="add" /><input name="u" type="hidden" value="' . $user_id . '" /><input name="return_page" type="hidden" value="admin-ajax.php?action=umm_add_user_meta&u=' . $user_id . '" />
    </form>  
    ';   
    print $output;
    exit;
}

function umm_admin_init(){
    if(function_exists('load_plugin_textdomain'))
    load_plugin_textdomain( 'user-meta-manager', false, dirname(plugin_basename( __FILE__ )) . '/language/' ); 
}

function umm_admin_menu(){
  add_submenu_page('users.php', 'User Meta Manager', 'User Meta Manager', 'publish_pages', 'user-meta-manager', 'umm_ui');
  add_action('admin_enqueue_scripts', 'umm_load_scripts');
}

function umm_backup(){
    global $wpdb, $current_user;
    $backup_files = get_option('umm_backup_files');
    $backup_files = (!$backup_files || $backup_files == '') ? array() : $backup_files;
    $back_button = umm_button("umm_backup_page&u=1", null, "umm-back-button");
    switch($_REQUEST['mode']){
        case "sql":
        $data = get_option('umm_backup');
        $budate = get_option('umm_backup_date');
        $sql = "DELETE FROM `" . $wpdb->usermeta . "`;\n";
        $sql .= "INSERT INTO `" . $wpdb->usermeta . "` (`umeta_id`, `user_id`, `meta_key`, `meta_value`) VALUES\n";      
        foreach($data as $d):
          $sql .= "(";
          foreach($d as $key => $value):
            if($key == 'umeta_id' || $key == 'user_id'):
              $sql .= $value . ", ";
            elseif($key == 'meta_value'):
              $sql .= "'" . addslashes($value) . "', ";
            else:
              $sql .= "'" . addslashes($value) . "', ";
            endif;              
          endforeach;
          $sql = trim($sql,", ");
          $sql .= "),\n";
        endforeach;
        $sql = trim($sql,",\n") . ";";
        $output = "<p>" . __("Below is the sql needed to restore the usermeta table.", "user-meta-manager") . "</p><strong>" . __("Backup from", "user-meta-manager") . " " . $budate . "</strong><br />\n<textarea onclick=\"this.focus();this.select();\" cols=\"65\" rows=\"15\">" . $sql . "</textarea>";
        break;
        
        case "php":
        $data = get_option('umm_backup');
        $budate = get_option('umm_backup_date');
        $output = '<?php
';
        $output .= "require('" . ABSPATH . "wp-load.php');\n";
        $output .= 'if(!is_user_logged_in() OR !current_user_can(\'update_core\')) wp_die("' . __("Authorization required!", "user-meta-manager") . '");
global $wpdb;
if(isset($_REQUEST[\'umm_confirm_restore\'])):
';
        $output .= '$wpdb->query("DELETE FROM $wpdb->usermeta");' . "\n";
        foreach($data as $d):
          $output .= '$wpdb->query("INSERT INTO $wpdb->usermeta (umeta_id, user_id, meta_key, meta_value) VALUES(';
          foreach($d as $key => $value):
            if($key == 'umeta_id' || $key == 'user_id'):
              $output .= $value . ", ";
            elseif($key == 'meta_value'):
              $output .= "'" . addslashes($value) . "')";
            else:
              $output .= "'" . addslashes($value) . "', ";
            endif;              
          endforeach;
          $output = trim($output,", ");
          $output .= "\");\n";
        endforeach;
        $output .= "print('" . __("Restore complete.", "user-meta-manager") . "');\nelse:\nprint('<form action=\"#\" method=\"post\"><p>" . __("Are you sure you want to restore all user meta data to the backup version?", "user-meta-manager") . "</p><button type=\"submit\">" . __("Yes", "user-meta-manager") . "</button><input type=\"hidden\" name=\"umm_confirm_restore\" value=\"1\" /></form>');\nendif;\n?>";
        
        if($_REQUEST['tofile'] == "yes"):
          $rs = umm_random_str(10);
          $file = WP_PLUGIN_DIR . "/user-meta-manager/backups/" . "usermeta-backup-" . date("m.j.Y-") . date("g.i.a") . "-" . $current_user->ID . "-" . $_SERVER['REMOTE_ADDR'] . "-" . $rs . ".php";
          $link = WP_PLUGIN_URL . "/user-meta-manager/backups/" . "usermeta-backup-" . date("m.j.Y-") . date("g.i.a") . "-" . $current_user->ID . "-" . $_SERVER['REMOTE_ADDR'] . "-" . $rs . ".php";
          array_push($backup_files, $file);
          update_option('umm_backup_files', $backup_files);
          if($fp = fopen($file, "w+")){
            chmod($file, 0755);
            fwrite($fp, trim($output));
            fclose($fp);
            chmod($file, 0755);
            $output = "<p>" . __("Backup php file was successfully generated at ", "user-meta-manager") . " <a href=\"" . $link . "\" target=\"_blank\">" . $link . "</a></p><p>" . __("Run the file in your browser to begin the restoration process.", "user-meta-manager") . "</p>";
          } else {
            $output = "<p>" . __("Error: Backup php file could not be generated at ", "user-meta-manager") . " " . WP_PLUGIN_DIR . "/user-meta-manager/backups" . "</p><p>" . __("Please be sure the directory exists and is owner-writable.", "user-meta-manager") . "</p>";
          }          
        else:
        $output = "<p>" . __("Below is the php needed to restore the usermeta table. Save this code as a php file to the root WordPress folder, then run it in your browser.", "user-meta-manager") . "</p><strong>" . __("Backup from", "user-meta-manager") . " " . $budate . "</strong><br />\n<textarea onclick=\"this.focus();this.select();\" cols=\"65\" rows=\"15\">" . $output . "</textarea>";
        endif;
        break;
        
        default:
        update_option('umm_backup', umm_usermeta_data());
        update_option('umm_backup_date', date("M d, Y") . ' ' . date("g:i A"));
        $output = "<p>" . __("User meta data backup was successful.", "user-meta-manager") . "</p>";
        break;
    }   
    print $back_button . $output;
    exit;
}

function umm_backup_page(){
    global $wpdb;
    $budate = get_option('umm_backup_date');
    if($budate == "") $budate = __("No backup", "user-meta-manager");
    
    $output = umm_fyi('<p>'.__('Use the following links to backup and restore user meta data.', 'user-meta-manager').'</p>');  
    $output .= '<div class="umm-backup-page-container">';
    $output .= '<ul><li><a href="#" data-subpage="admin-ajax.php?action=umm_backup&amp;u=1" title="'.__('Backup', 'user-meta-manager').'" class="umm-subpage">'.__('Backup', 'user-meta-manager').'</a> <strong>'.__('Last Backup:', 'user-meta-manager'). '</strong> ' . $budate . '</li>';  
    $output .= '<li><a href="#" data-subpage="admin-ajax.php?action=umm_restore_confirm&amp;u=1" title="'.__('Restore', 'user-meta-manager').'" class="umm-subpage">'.__('Restore', 'user-meta-manager').'</a></li>
    <li><a href="#" data-subpage="admin-ajax.php?action=umm_backup&amp;mode=sql&amp;u=1" title="'.__('Generate SQL', 'user-meta-manager').'" class="umm-subpage">'.__('Generate SQL', 'user-meta-manager').'</a></li>
    <li><a href="#" data-subpage="admin-ajax.php?action=umm_backup&amp;mode=php&amp;u=1" title="'.__('Generate PHP', 'user-meta-manager').'" class="umm-subpage">'.__('Generate PHP', 'user-meta-manager').'</a></li>
    <li><a href="#" data-subpage="admin-ajax.php?action=umm_backup&amp;mode=php&amp;tofile=yes&amp;u=1" title="'.__('Generate PHP Restoration File', 'user-meta-manager').'" class="umm-subpage">'.__('Generate PHP Restoration File', 'user-meta-manager').'</a></li>
    <li><a href="#" data-subpage="admin-ajax.php?action=umm_delete_backup_files" title="'.__('Delete All Backup Files', 'user-meta-manager').'" class="umm-subpage">'.__('Delete All Backup Files', 'user-meta-manager').'</a></li>
    </ul>';
    $output .= '</div>';
    print $output;
    exit;
}

function umm_button($go_to, $label=null, $css_class=null){
    $label = (!$label) ? __('<< Back', 'user-meta-manager') : $label;
    $css_class = (!$css_class) ? 'button-secondary umm-button' : 'button-secondary umm-button ' . $css_class;
    switch($go_to){
        case 'home':
        $umm_button = '<button href="#" data-type="' . $go_to . '" title="' . $label . '" class="umm-homelink ' . $css_class . '">' . $label . '</button>';
        break;
        
        default:
        $umm_button = '<button href="#" data-type="subpage" data-subpage="admin-ajax.php?action=' . $go_to . '" title="' . $label . '" class="' . $css_class . '">' . $label . '</button>';
    }
    return $umm_button;
}

function umm_column_exists($key){
   $used_columns = umm_get_columns();
   return array_key_exists($key, $used_columns);
}

function umm_deactivate(){
    // Preserve data
    // delete_option('user_meta_manager_data');
    // delete_option('umm_users_columns');
    // delete_option('umm_usermeta_columns');
    // delete_option('umm_backup');
    // delete_option('umm_backup_date');
    // delete_option('umm_backup_files');
    // delete_option('umm_profile_fields');
}

function umm_delete_backup_files(){
    if(!empty($_REQUEST['umm_confirm_backups_delete'])):
    $backups_folder = WP_PLUGIN_DIR . "/user-meta-manager/backups";
    chmod($backups_folder, 0755);
    $backup_files = get_option('umm_backup_files', $backup_files);
    
    if(is_array($backup_files) && !empty($backup_files)):
    foreach($backup_files as $backup_file):
      @unlink($backup_file);
    endforeach;
    endif;
    update_option('umm_backup_files', array());
    
    $output = '<p class="umm-message">' . __('All backup files successfully deleted.', 'user-meta-manager') . ' <a href="#" data-subpage="admin-ajax.php?action=umm_backup_page" class="umm-subpage">' . __('Back', 'user-meta-manager') . '</a></p>';
    else:
    $output = '<p class="umm-warning"><strong>' . __('Are you sure you want to delete all backup files?', 'user-meta-manager') . '</strong><br /><a href="#" data-subpage="admin-ajax.php?action=umm_delete_backup_files&amp;umm_confirm_backups_delete=yes" class="umm-subpage">' . __('Yes', 'user-meta-manager') . '</a> <a href="#" data-subpage="admin-ajax.php?action=umm_backup_page" class="umm-subpage">' . __('Cancel', 'user-meta-manager') . '</a></p>';
    endif;
    print $output;
    exit;
    return;
}

function umm_default_keys(){
    global $wpdb;
    $data = umm_usermeta_data("ORDER BY user_id DESC LIMIT 1");
    $umm_data = get_option('user_meta_manager_data');
    if($umm_data):
        foreach($umm_data as $key => $value):
            update_user_meta($data[0]->user_id, $key, $value, false);
        endforeach;
    endif;
}

function umm_delete_custom_meta(){
    global $wpdb;
    $data = get_option('user_meta_manager_data');
    if(!empty($data)):    
    $delete_key = $_REQUEST['umm_edit_key'];
    
    if($delete_key == ""):
    $output = umm_fyi('<p>'.__('Select from the menu a meta key to delete.').'</p>');  
    $output .= '<form id="umm_update_user_meta_form" method="post">
    <strong>'.__('Meta Key', 'user-meta-manager').':</strong> <select id="umm_edit_key" name="umm_edit_key" class="umm_meta_key_menu">
    <option value="">'.__('Select A Meta Key', 'user-meta-manager').'</option>
    ';

    if($data):
       foreach($data as $key => $value):
        $output .= "<option value=\"".$key ."\">".$key ."</option>";
       endforeach; 
    endif;   

    $output .= '</select> <input id="umm_update_user_meta_submit" data-form="umm_update_user_meta_form" data-subpage="umm_update_user_meta" data-wait="'.__('Wait...', 'user-meta-manager').'" class="button-primary button-delete" type="submit" value="'.__('Submit', 'user-meta-manager').'" /><input name="all_users" type="hidden" value="true" /><input name="mode" type="hidden" value="" /><input name="u" type="hidden" value="all" /><input name="return_page" type="hidden" value="admin-ajax.php?action=umm_delete_custom_meta&u=0" />
    </form>  
    ';
    else:
    $output = '<form id="umm_update_user_meta_form" method="post">
    <strong>'.__('Deleting', 'user-meta-manager').':</strong> ' . $delete_key . '
    <p class="umm-warning">
    '.__('Are you sure you want to delete that item?', 'user-meta-manager').'<br />
    <input id="umm_update_user_meta_submit" data-form="umm_update_user_meta_form" data-subpage="umm_update_user_meta" data-wait="'.__('Wait...', 'user-meta-manager').'" class="button-primary button-delete" type="submit" value="'.__('Yes', 'user-meta-manager').'" /> ';
    $output .= umm_button("umm_delete_custom_meta&u=0", __('Cancel', 'user-meta-manager'));
    $output .= '<input name="meta_key" type="hidden" value="' . $delete_key . '" />
    <input name="all_users" type="hidden" value="true" /><input name="mode" type="hidden" value="delete" /><input name="u" type="hidden" value="all" /><input name="return_page" type="hidden" value="admin-ajax.php?action=umm_delete_custom_meta&u=0" /></p>
    </form>';   
    endif;
    else: // !empty($data)
    $output = __('No custom meta to delete.', 'user-meta-manager');
    endif; // !empty($data)
    print $output;
    exit;
}

function umm_delete_user_meta(){
    global $wpdb;
    $user_id = $_REQUEST['u'];
    $data = umm_usermeta_data("WHERE user_id = $user_id");
    $output = umm_button('home', null, "umm-back-button") . umm_subpage_title($user_id, __('Deleting Meta Data For %s', 'user-meta-manager'));
    
    $all_users = $_REQUEST['all_users'];
    $delete_key = (isset($_REQUEST['umm_edit_key']) && trim($_REQUEST['umm_edit_key']) != "" && trim($_REQUEST['umm_edit_key']) != "undefined") ? trim($_REQUEST['umm_edit_key']) : "";
    
    if($delete_key == ""):
    
    $output .= umm_fyi('<p>'.__('Select a <em>Meta Key</em> to delete, then press the <em>Submit</em> button. Select <em>All Users</em> to delete the selected item from all users.').'</p>', 'user-meta-manager');
    $output .= '<form id="umm_update_user_meta_form" method="post">
    <strong>'.__('Meta Key', 'user-meta-manager').':</strong> <select id="umm_edit_key" name="umm_edit_key" class="umm_meta_key_menu">
    <option value="">'.__('Select A Meta Key', 'user-meta-manager').'</option>
    ';

    foreach($data as $d):
        $output .= "<option value=\"".$d->meta_key ."\">".$d->meta_key ."</option>";
    endforeach;

    $output .= '</select><br />
    <strong>'.__('All Users', 'user-meta-manager').':</strong> <select name="all_users" size="1">
	<option value="false">'.__('No', 'user-meta-manager').'</option>
	<option value="true">'.__('Yes', 'user-meta-manager').'</option>
</select><br />
    <input id="umm_update_user_meta_submit" data-form="umm_update_user_meta_form" data-subpage="umm_update_user_meta" data-wait="'.__('Wait...', 'user-meta-manager').'" class="button-primary button-delete" type="submit" value="'.__('Submit', 'user-meta-manager').'" />
    <input name="mode" type="hidden" value="" /><input name="u" type="hidden" value="' . $user_id . '" /><input name="return_page" type="hidden" value="admin-ajax.php?action=umm_delete_user_meta&u=' . $user_id . '" />
    </form>  
    ';
    else:
    $output = '<form id="umm_update_user_meta_form" method="post">
    <strong>'.__('Deleting', 'user-meta-manager').':</strong> ' . $delete_key . '
    <p class="umm-warning">
    '.__('Are you sure you want to delete that item?', 'user-meta-manager').'<br />
    <input id="umm_update_user_meta_submit" data-form="umm_update_user_meta_form" data-subpage="umm_update_user_meta" data-wait="'.__('Wait...', 'user-meta-manager').'" class="button-primary button-delete" type="submit" value="'.__('Yes', 'user-meta-manager').'" /> ';
    $output .= umm_button("umm_delete_user_meta&u=" . $user_id, __('Cancel', 'user-meta-manager'));
    $output .= '<input name="meta_key" type="hidden" value="' . $delete_key . '" /><input name="all_users" type="hidden" value="' . $all_users . '" />
    <input name="all_users" type="hidden" value="true" /><input name="mode" type="hidden" value="delete" /><input name="u" type="hidden" value="' . $user_id . '" /><input name="return_page" type="hidden" value="admin-ajax.php?action=umm_delete_user_meta&u=' . $user_id . '" /></p>
    </form>';
    endif;
    print $output;
    exit;
}

function umm_edit_columns(){
    $columns = umm_get_columns();
    $output = umm_fyi('<p>'.__('Use the forms below to edit which table columns are displayed.', 'user-meta-manager').'</p>');
    $output .= '<form id="umm_manage_columns_form" method="post">
    <h3>'.__('Display Columns', 'user-meta-manager').'</h3>
    <table class="umm_edit_columns_table wp-list-table widefat fixed">
    <thead>
    <tr>
      <th></th>
      <th>'.__('Key', 'user-meta-manager').'</th>
      <th>'.__('Label', 'user-meta-manager').'</th>
    </tr>
  </thead>
  ';
  $x = 1;
  foreach($columns as $k => $v){
    $c = ($x%2) ? "" : "alternate";
    $cb = ($k != 'ID' && $k != 'user_login') ? '<input type="radio" value="'.$k.'|" name="umm_column_key" />' : '<input type="radio" value="'.$k.'|" name="umm_column_key" disabled="disabled" title="Required" />';
    $output .= "<tr class=\"$c\"><td>$cb</td><td>$k</td><td>$v</td></tr>\n";
    $x++;
  }
   $output .= '</table>
   <input id="umm_update_user_meta_submit" data-form="umm_manage_columns_form" data-subpage="umm_update_columns" data-wait="'.__('Wait...', 'user-meta-manager').'" class="button-primary" type="submit" value="'.__('Remove Selected Column', 'user-meta-manager').'" />
   <input name="mode" type="hidden" value="remove_columns" /><input name="return_page" type="hidden" value="admin-ajax.php?action=umm_edit_columns" />
   </form>
   <form id="umm_add_columns_form" method="post">
   <h3>'.__('Add A New Column', 'user-meta-manager').'</h3>
   <strong>'.__('Key', 'user-meta-manager').':</strong> <select name="umm_column_key">
   <option value="">'.__('Keys', 'user-meta-manager').'</option>';
   $output .= umm_users_keys_menu(false, true); 
   $output .= umm_usermeta_keys_menu(false, true);
   $output .= '</select><br>
   <strong>'.__('Label', 'user-meta-manager').':</strong> <input name="umm_column_label" type="text" value="" placeholder="'.__('Enter a label', 'user-meta-manager').'" title="'.__('Enter a label which will appear in the top row of the results table.', 'user-meta-manager').'" /><br />';   
   $output .= '<input id="umm_update_user_meta_submit" data-form="umm_add_columns_form" data-subpage="umm_update_columns" data-wait="'.__('Wait...', 'user-meta-manager').'" class="button-primary" type="submit" value="'.__('Add Column', 'user-meta-manager').'" />
    <input name="mode" type="hidden" value="add_columns" /><input name="return_page" type="hidden" value="admin-ajax.php?action=umm_edit_columns" />
    </form>  
    ';
    print $output;
    exit;
}

function umm_edit_custom_meta(){
    global $wpdb;
    $data = get_option('user_meta_manager_data');
    if(!$data):
       $output = __('No custom meta to edit.', 'user-meta-manager'); 
    else:
    $edit_key = $_REQUEST['umm_edit_key'];
    if($edit_key == ""):
        $output = umm_fyi('<p>'.__('Select from the menu a meta key to edit.', 'user-meta-manager').'</p>');
        $output .= "<form id=\"umm_update_user_meta_form\" method=\"post\">
        <strong>Edit Key:</strong> <select id=\"umm_edit_key\" name=\"umm_edit_key\" title=\"".__('Select a meta key to edit.', 'user-meta-manager')."\">\n<option value=\"\">".__('Select A Key To Edit', 'user-meta-manager')."</option>\n";
        foreach($data as $key => $value):
            $output .= '<option value="'.$key.'">'.$key.'</option>
            ';
        endforeach;    
            $output .= '</select> 
    <input id="umm_edit_custom_meta_submit" data-form="umm_update_user_meta_form" data-subpage="umm_update_user_meta" data-wait="'.__('Wait...', 'user-meta-manager').'" class="button-primary" type="submit" value="'.__('Submit', 'user-meta-manager').'" />
    <input name="mode" type="hidden" value="" /><input name="u" type="hidden" value="all" /><input name="return_page" type="hidden" value="admin-ajax.php?action=umm_edit_custom_meta" /> 
    </form> 
    ';
        
    else:
    $profile_fields = get_option('umm_profile_fields');
    if(!$profile_fields) $profile_fields = array();
    $output = '<strong>' . __('Now Editing', 'user-meta-manager') . ':</strong> <span class="umm-highlight">' . $_REQUEST['umm_edit_key'] . '</span>';
    $output .= umm_fyi('<p>'.__('Editing custom meta data here will edit the value for all new users. The value you set will become the default value for all users. New registrations will receive the custom meta key and default value.', 'user-meta-manager').'</p>');
    $output .= '<form id="umm_update_user_meta_form" method="post">
    ';
    
    
    if(!$data):
       $output .= '<tr>
       <td colspan="2">' . __('No custom meta to display.', 'user-meta-manager') . '</td>
       </tr>'; 
    else:
        foreach($data as $key => $value):
        if($key == $_REQUEST['umm_edit_key']):
            $output .= '<strong>' . __('Value', 'user-meta-manager') . ':</strong><input name="meta_key" type="hidden" value="' . $key . '" /><br /><input name="meta_value" type="text" value="' . htmlspecialchars($value) . '" size="40" /><br />';
            endif; 
        endforeach;
    endif;
    $output .= '<strong>' . __('Update Value For All Current Users', 'user-meta-manager') . ':</strong><br /><input type="checkbox" name="all_users" value="1" title="' . __('Check the box to update the value for all current users. Leave blank to update the value for future registrations only.', 'user-meta-manager') . '" /> ' . __('Yes', 'user-meta-manager') . ''; 
    $output .= umm_profile_field_editor($edit_key);
    $output .= '<input id="umm_update_user_meta_submit" data-form="umm_update_user_meta_form" data-subpage="umm_update_user_meta" data-wait="'.__('Wait...', 'user-meta-manager').'" class="button-primary" type="submit" value="'.__('Update', 'user-meta-manager').'" />
    <input name="mode" type="hidden" value="edit" /><input name="u" type="hidden" value="all" /><input name="return_page" type="hidden" value="admin-ajax.php?action=umm_edit_custom_meta" />
    </form>  
    ';
    endif; // edit_key
    endif; // !$data
    print $output;
    exit;
}

function umm_edit_user_meta(){  
    global $wpdb;
    $user_id = $_REQUEST['u'];
    $data = umm_usermeta_data("WHERE user_id = $user_id");
    $output = umm_button('home', null, "umm-back-button") . umm_subpage_title($user_id, __('Editing Meta Data For %s', 'user-meta-manager'));
    $output .= umm_fyi('<p>'.__('Editing an item here will only edit the item for the selected user and not for all users.<br /><a href="#" data-subpage="admin-ajax.php?action=umm_edit_custom_meta&u=1" data-nav_button="Edit Custom Meta" title="Edit Custom Meta" class="umm-subpage">Edit Custom Meta Data For All Users</a>', 'user-meta-manager').'</p>');
    $edit_key = $_REQUEST['umm_edit_key'];
    if($edit_key == ""):
        $output .= "<form id=\"umm_update_user_meta_form\" method=\"post\">
        <strong>Edit Key:</strong> <select id=\"umm_edit_key\" name=\"umm_edit_key\" title=\"".__('Select a meta key to edit.', 'user-meta-manager')."\">\n<option value=\"\">".__('Select A Key To Edit', 'user-meta-manager')."</option>\n";
        foreach($data as $d):
            $output .= '<option value="'.$d->meta_key.'">'.$d->meta_key.'</option>
            ';
        endforeach;    
            $output .= '</select> 
    <input id="umm_edit_custom_meta_submit" data-form="umm_update_user_meta_form" data-subpage="umm_update_user_meta" data-wait="'.__('Wait...', 'user-meta-manager').'" class="button-primary" type="submit" value="'.__('Submit', 'user-meta-manager').'" />
    <input name="mode" type="hidden" value="edit" /><input name="u" type="hidden" value="' . $user_id . '" /><input name="return_page" type="hidden" value="admin-ajax.php?action=umm_edit_user_meta&u=' . $user_id . '" />
    </form> 
    ';
        
    else:
    
    $output .= '<strong>' . __('Now Editing', 'user-meta-manager') . ':</strong> ' . $_REQUEST['umm_edit_key'] . '
<form id="umm_update_user_meta_form" method="post">
    <table class="umm_edit_table wp-list-table widefat">
    <thead>
    <tr>
      <th>'.__('Key', 'user-meta-manager').'</th>
      <th>'.__('Value', 'user-meta-manager').'</th>
    </tr>
  </thead>
    ';
    $x = 1;
    foreach($data as $d):
    $class = ($x%2) ? ' class="alternate"' : '';
    if($d->meta_key == $edit_key):
        $output .= "<tr" . $class . "><td width=\"25%\">".$d->meta_key ."</td><td><input name=\"meta_key\" type=\"hidden\" value=\"". $d->meta_key ."\" /><input name=\"meta_value\" type=\"text\" value=\"". htmlspecialchars($d->meta_value) ."\" size=\"40\" /></td></tr>";
        $x++;
    endif;         
    endforeach;

    $output .= '</table>
    <input id="umm_update_user_meta_submit" data-form="umm_update_user_meta_form" data-subpage="umm_update_user_meta" data-wait="'.__('Wait...', 'user-meta-manager').'" class="button-primary" type="submit" value="'.__('Update', 'user-meta-manager').'" />
    <input name="mode" type="hidden" value="edit" /><input name="u" type="hidden" value="' . $user_id . '" /><input name="return_page" type="hidden" value="admin-ajax.php?action=umm_edit_user_meta&u=' . $user_id . '" />
    </form>  
    ';
    endif;
    print $output;
    exit;
}

function umm_fyi($message){
    return "<div class=\"umm-fyi\">" . $message . "</div>";
}

function umm_get_columns(){
    $users_columns = (!get_option("umm_users_columns") ? array('ID' => __('ID', 'user-meta-manager'), 'user_login' => __('User Login', 'user-meta-manager'), 'user_registered' => __('Date Registered', 'user-meta-manager')) : get_option("umm_users_columns"));
    $usermeta_columns = (!get_option("umm_usermeta_columns")) ? array() : get_option("umm_usermeta_columns");
    return array_merge($users_columns, $usermeta_columns);
}

function umm_install(){
   add_option('umm_backup', umm_usermeta_data());
   add_option('umm_backup_date', date("M d, Y") . ' ' . date("g:i A"));
   add_option('user_meta_manager_data', '');
   add_option('umm_profile_fields', array());
   add_option('umm_users_columns', array('ID' => __('ID', 'user-meta-manager'), 'user_login' => __('User Login', 'user-meta-manager'), 'user_registered' => __('Date Registered', 'user-meta-manager')));
   add_option('umm_usermeta_columns', array());
   add_option('umm_backup_files', array());
}

function umm_load_scripts($hook) {
    if($hook && $hook == "users_page_user-meta-manager"):
    //update_option('umm_profile_fields', array());
    //update_option('user_meta_manager_data', array());
       wp_enqueue_script('jquery');
       wp_register_script('umm_jquery_ui', plugins_url('/js/jquery-ui-1.9.0.min.js?version='.rand(100,1000), __FILE__));
       wp_enqueue_script('umm_jquery_ui');
       wp_register_style('umm_css', plugins_url('/css/user-meta-manager.css', __FILE__));
       wp_enqueue_style('umm_css');
       wp_register_script('umm_js', plugins_url('/js/user-meta-manager.js?version='.rand(100,1000), __FILE__));
       wp_enqueue_script('umm_js');
    endif;
}

function umm_profile_field_editor($umm_edit_key=null){
    $profile_fields = get_option('umm_profile_fields');
    $options_output = '';
    $select_option_row = '<tr class="umm-select-option-row">
	<td><input name="umm_profile_select_label[]" type="text" placeholder="'.__('Label', 'user-meta-manager').'" value="" /></td>
	<td><input name="umm_profile_select_value[]" type="text" placeholder="'.__('Value', 'user-meta-manager').'" value="" /></td>
	<td><button class="umm-add-row button-secondary umm-profile-editor umm-add-option">+</button> <button class="umm-remove-row button-secondary umm-profile-editor umm-remove-option">-</button></td>
</tr>
';
    
    if(!empty($umm_edit_key) && array_key_exists($umm_edit_key, $profile_fields)):
          $value = stripslashes(htmlspecialchars_decode($profile_fields[$umm_edit_key]['value']));
          $type = $profile_fields[$umm_edit_key]['type'];
          $label = stripslashes(htmlspecialchars_decode($profile_fields[$umm_edit_key]['label']));
          $class = $profile_fields[$umm_edit_key]['class'];
          $attrs = stripslashes(htmlspecialchars_decode($profile_fields[$umm_edit_key]['attrs']));
          $after = stripslashes(htmlspecialchars_decode($profile_fields[$umm_edit_key]['after']));
          $required = $profile_fields[$umm_edit_key]['required'];
          $options = (!is_array($profile_fields[$umm_edit_key]['options'])) ? array() : $profile_fields[$umm_edit_key]['options'];
          
          $x = 1;          
          foreach($options as $option):
            $hide_button = ($x == 1) ? ' hidden' : '';
            if(!empty($option['label'])):          
            $options_output .= '<tr class="umm-select-option-row">
	<td><input name="umm_profile_select_label[]" type="text" placeholder="'.__('Label', 'user-meta-manager').'" value="' . stripslashes($option['label']) . '" /></td>
	<td><input name="umm_profile_select_value[]" type="text" placeholder="'.__('Value', 'user-meta-manager').'" value="' . stripslashes(htmlspecialchars_decode($option['value'])) . '" /></td>
	<td><button class="umm-add-row button-secondary umm-profile-editor umm-add-option">+</button> <button class="umm-remove-row button-secondary umm-profile-editor umm-remove-option' . $hide_button . '">-</button></td>
</tr>
';
          endif; //!empty($option['label'])
          $x++;
          endforeach;
          
          if(empty($options_output)):
            $options_output .= $select_option_row;
          endif;
                          
        else:
        $options_output .= $select_option_row;
    endif;
    
    $output = '<div class="umm-profile-field-editor">
    <strong>Profile Field <a title="'.__('W3Schools HTML5 Input Types Reference Page', 'user-meta-manager').'" href="http://www.w3schools.com/html/html5_form_input_types.asp" target="_blank">'.__('Type', 'user-meta-manager').'</a>:</strong><br /><select class="umm-profile-field-type" size="1" name="umm_profile_field_type">
    <option value="" title="'.__('Do not add to user profile.', 'user-meta-manager').'"';
    if($type == '') $output .= ' selected="selected"';
    $output .= '>'.__('None', 'user-meta-manager').'</option>
	<option value="text"';
    if($type == 'text') $output .= ' selected="selected"';
    $output .= '>'.__('Text', 'user-meta-manager').'</option>
	<option value="color"';
    if($type == 'color') $output .= ' selected="selected"';
    $output .= '>'.__('Color', 'user-meta-manager').'</option>
    <option value="date"';
    if($type == 'date') $output .= ' selected="selected"';
    $output .= '>'.__('Date', 'user-meta-manager').'</option>
    <option value="datetime"';
    if($type == 'datetime') $output .= ' selected="selected"';
    $output .= '>'.__('Date-Time', 'user-meta-manager').'</option>
    <option value="datetime-local"';
    if($type == 'datetime-local') $output .= ' selected="selected"';
    $output .= '>'.__('Date-Time-Local', 'user-meta-manager').'</option>
    <option value="email"';
    if($type == 'email') $output .= ' selected="selected"';
    $output .= '>'.__('Email', 'user-meta-manager').'</option>
    <option value="month"';
    if($type == 'month') $output .= ' selected="selected"';
    $output .= '>'.__('Month', 'user-meta-manager').'</option>
    <option value="number"';
    if($type == 'number') $output .= ' selected="selected"';
    $output .= '>'.__('Number', 'user-meta-manager').'</option>
    <option value="range"';
    if($type == 'range') $output .= ' selected="selected"';
    $output .= '>'.__('Range', 'user-meta-manager').'</option>
    <option value="search"';
    if($type == 'search') $output .= ' selected="selected"';
    $output .= '>'.__('Search', 'user-meta-manager').'</option>
    <option value="tel"';
    if($type == 'tel') $output .= ' selected="selected"';
    $output .= '>'.__('Telephone', 'user-meta-manager').'</option>
    <option value="time"';
    if($type == 'time') $output .= ' selected="selected"';
    $output .= '>'.__('Time', 'user-meta-manager').'</option>
    <option value="url"';
    if($type == 'url') $output .= ' selected="selected"';
    $output .= '>'.__('URL', 'user-meta-manager').'</option>
    <option value="week"';
    if($type == 'week') $output .= ' selected="selected"';
    $output .= '>'.__('Week', 'user-meta-manager').'</option>
    <option value="textarea"';
    if($type == 'textarea') $output .= ' selected="selected"';
    $output .= '>'.__('Textarea', 'user-meta-manager').'</option>
    <option value="checkbox"';
    if($type == 'checkbox') $output .= ' selected="selected"';
    $output .= '>'.__('Checkbox', 'user-meta-manager').'</option>
    <option value="radio"';
    if($type == 'radio') $output .= ' selected="selected"';
    $output .= '>'.__('Radio Button Group', 'user-meta-manager').'</option>
    <option value="select"';
    if($type == 'select') $output .= ' selected="selected"';
    $output .= '>'.__('Select Menu', 'user-meta-manager').'</option>
    </select>';
    
    $hidden = (empty($type)) ? ' hidden' : '';
    
    $output .= '<div class="umm-input-options' . $hidden . ' umm-profile-field-options">
    <h3>'.__('Settings', 'user-meta-manager').'</h3>
    <strong>'.__('Label', 'user-meta-manager').':</strong><br />
    <textarea rows="3" cols="40" name="umm_profile_field_label"  placeholder="">' . $label . '</textarea>
    <br />
    <strong>'.__('Classes', 'user-meta-manager').':</strong><br />
    <textarea rows="3" cols="40" name="umm_profile_field_class"  placeholder="">' . $class . '</textarea>
    <br />
    <strong>'.__('Additional Attributes', 'user-meta-manager').':</strong><br />
    <textarea rows="3" cols="40" name="umm_profile_field_attrs" type="text" placeholder="'.__('Example', 'user-meta-manager').': min=&quot;1&quot; max=&quot;5&quot; title=&quot;'.__('My Title', 'user-meta-manager').'&quot; placeholder=&quot;'.__('My Text', 'user-meta-manager').'&quot">' . $attrs . '</textarea>
    <br />
    <strong>'.__('HTML After', 'user-meta-manager').':</strong><br />
    <textarea rows="3" cols="40" name="umm_profile_field_after" placeholder="">' . $after . '</textarea>
    <br />   
    <strong>'.__('Required', 'user-meta-manager').':</strong> <select size="1" name="umm_profile_field_required">
	<option value="no"';
    if($required == 'no' || $required == '') $output .= ' selected="selected"';
    $output .= '>No</option>
	<option value="yes"';
    if($required == 'yes') $output .= ' selected="selected"';
    $output .= '>Yes</option>
    </select><br />
    </div>';
    
    $hidden = ($type == 'select' || $type == 'radio') ? '' : ' hidden';
    
    $output .= '
    <div class="umm-select-options' . $hidden . ' umm-profile-field-options">
    <h3>'.__('Options', 'user-meta-manager').'</h3>
    <table class="umm-select-options-table">
<tr>
	<th>Label</th>
	<th>Value</th>
	<th></th>
</tr>
';
$output .= $options_output;
$output .= '</table>
<table class="umm-select-options-clone hidden">
 <tr class="umm-select-option-row">
	<td><input name="umm_profile_select_label[]" type="text" placeholder="'.__('Label', 'user-meta-manager').'" value="" /></td>
	<td><input name="umm_profile_select_value[]" type="text" placeholder="'.__('Value', 'user-meta-manager').'" value="" /></td>
	<td><button class="umm-add-row button-secondary umm-profile-editor">+</button> <button class="umm-remove-row button-secondary umm-profile-editor">-</button></td>
</tr>
</table>
    </div>
    </div>';   
    return $output;
}

function umm_random_str($number_of_digits = 1, $type = 3){
    // $type: 1 - numeric, 2 - letters, 3 - mixed, 4 - all ascii chars.
    for($x = 0; $x < $number_of_digits; $x++):
        while(substr($num, strlen($num) - 1, strlen($num)) == $r):
            switch ($type) {
                case "1":
                    $r = rand(0, 9);
                    break;

                case "2":
                    $n = rand(0, 999);
                    if($n % 2):
                        $r = chr(rand(0, 25) + 65);
                    else:
                        $r = strtolower(chr(rand(0, 25) + 65));
                    endif;
                    break;

                case "3":
                    if(is_numeric(substr($num, strlen($num) - 1, strlen($num)))):
                        $n = rand(0, 999);
                        if($n % 2):
                            $r = chr(rand(0, 25) + 65);
                        else:
                            $r = strtolower(chr(rand(0, 25) + 65));
                        endif;
                    else:
                        $r = rand(0, 9);
                    endif;
                    break;
                    
                    case "4":
                    if(is_numeric(substr($num, strlen($num) - 1, strlen($num)))):
                        $n = rand(0, 999);
                        if($n % 2):
                            $r = chr(rand(33, 231));
                        else:
                            $r = strtolower(chr(rand(33, 231)));
                        endif;
                    else:
                        $r = rand(33, 231);
                    endif;                   
                    break;
            }
        endwhile;
        $num .= $r;
    endfor;
    return $num;
}

function umm_restore(){
    global $wpdb;
    $data = get_option('umm_backup');
    $wpdb->query("DELETE FROM " . $wpdb->usermeta);
    $cols = array();
    foreach($data as $d):
      foreach($d as $key => $value):
        $cols[$key] = $value;   
      endforeach;
    $wpdb->insert($wpdb->usermeta, $cols); 
    $cols = array();       
    endforeach;
    $output = "<p>" . __("User meta data successfully restored.", "user-meta-manager") . "</p>";
    print $output;
    exit;
}

function umm_restore_confirm(){
    $budate = get_option('umm_backup_date');
    if($budate == ""): 
      $output = __("No backup data to restore!", "user-meta-manager");
    else:  
      $output = "<p>" . __("Are you sure you want to restore all user meta data to the backup version?", "user-meta-manager") . " <a href=\"#\" data-subpage=\"admin-ajax.php?action=umm_restore&u=1\" title=\"" . __('Restore', 'user-meta-manager') . "\" class=\"umm-subpage\">".__('Yes', 'user-meta-manager')."</a></p>";
    endif;
    print $output;
    exit;
}

function umm_show_profile_fields($echo=true, $debug=false){
   global $current_user;
    $profile_fields = get_option('umm_profile_fields');
    if($debug) print_r($profile_fields);
    if(!empty($profile_fields)):
    $output = "<table class=\"form-table\">
<tbody>\n";

    foreach($profile_fields as $profile_field_name => $profile_field_settings):
      $default_value = stripslashes(htmlspecialchars_decode($profile_field_settings['value']));
      $user_value = stripslashes(htmlspecialchars_decode(get_user_meta($current_user->ID, $profile_field_name, true)));
      
      $value = (empty($user_value)) ? $default_value : $user_value;
      
      $output .= "<tr>
	<th><label for=\"" . $profile_field_name . "\" class=\"" . str_replace(" ", "-", strtolower($profile_field_name)) . "\">" . stripslashes(htmlspecialchars_decode($profile_field_settings['label'])) . "</label></th>
	<td>";
    switch($profile_field_settings['type']){
            case 'text':
            case 'color':
            case 'date':
            case 'datetime':
            case 'datetime-local':
            case 'email':
            case 'month':
            case 'number':
            case 'range':
            case 'search':
            case 'tel':
            case 'time':
            case 'url':
            case 'week':           
            $output .= "<input type=\"" . $profile_field_settings['type'] . "\" name=\"" . $profile_field_name . "\" value=\"" . $value . "\" class=\"" . stripslashes(htmlspecialchars_decode($profile_field_settings['class'])) . "\"";
            if($profile_field_settings['required'] == 'yes')
            $output .= ' required="required"';
            if(!empty($profile_field_settings['attrs']))
            $output .= ' ' . stripslashes(htmlspecialchars_decode($profile_field_settings['attrs']));
            $output .= " />";
            break;
            
            case 'textarea':
            $output .= "<textarea name=\"" . $profile_field_name . "\" class=\"" . stripslashes(htmlspecialchars_decode($profile_field_settings['class'])) . "\"";
            if($profile_field_settings['required'] == 'yes')
            $output .= ' required="required"';
            if(!empty($profile_field_settings['attrs']))
            $output .= ' ' . stripslashes(htmlspecialchars_decode($profile_field_settings['attrs']));
            $output .= ">" . $value . "</textarea>\n";
            break;
            
            case 'checkbox':
            $output .= "<input type=\"checkbox\" name=\"" . $profile_field_name;
            $output .= "\" value=\"" . $value . "\" class=\"" . stripslashes(htmlspecialchars_decode($profile_field_settings['class'])) . "\"";
            if($profile_field_settings['required'] == 'yes')
              $output .= ' required="required"';
            if(!empty($value))
              $output .= ' checked="checked"';
            if(!empty($profile_field_settings['attrs']))
              $output .= ' ' . stripslashes(htmlspecialchars_decode($profile_field_settings['attrs']));
            $output .= " />";
            break; 
            
            case 'radio':
            foreach($profile_field_settings['options'] as $option => $option_settings):
              if(!empty($option_settings['label'])):
              $output .= "<input type=\"" . $profile_field_settings['type'] . "\" name=\"" . $profile_field_name;
              if(count($profile_field_settings['options']) > 1) $output .= '[]';
              $output .= "\" value=\"" . $option_settings['value'] . "\" class=\"" . stripslashes(htmlspecialchars_decode($profile_field_settings['class'])) . "\"";
              if($profile_field_settings['required'] == 'yes')
              $output .= ' required="required"';
              if($option_settings['value'] == $value)
              $output .= ' checked="checked"';
              if(!empty($profile_field_settings['attrs']))
              $output .= ' ' . stripslashes(htmlspecialchars_decode($profile_field_settings['attrs']));
              $output .= " /><label class=\"" . str_replace(" ", "-", strtolower($profile_field_name)) . "\">" . $option_settings['label'] . "</label> ";
              endif;
            endforeach; 
            break;
            
            case 'select':
            $output .= "<select name=\"" . $profile_field_name . "\" class=\"" . stripslashes(htmlspecialchars_decode($profile_field_settings['class'])) . "\"";
            if($profile_field_settings['required'] == 'yes')
            $output .= ' required="required"';
            if(!empty($profile_field_settings['attrs']))
            $output .= ' ' . stripslashes(htmlspecialchars_decode($profile_field_settings['attrs']));
            $output .= ">\n";
            foreach($profile_field_settings['options'] as $option => $option_settings):
            if(!empty($option_settings['label'])):
            $output .= '<option value="' . stripslashes($option_settings['value']) . '"';
              if($option_settings['value'] == $value) $output .= ' selected="selected"';
            $output .= '>'.stripslashes($option_settings['label']).'</option>
            ';
            endif;
            endforeach; 
            $output .= "<select>\n";           
            break;
            
            default:
            $output .= "<input type=\"text\" name=\"" . $profile_field_name . "\" value=\"" . $value . "\" class=\"" . stripslashes(htmlspecialchars_decode($profile_field_settings['class'])) . "\"";
            if($profile_field_settings['required'] == 'yes')
            $output .= ' required="required"';
            if(!empty($profile_field_settings['attrs']))
            $output .= ' ' . stripslashes(htmlspecialchars_decode($profile_field_settings['attrs']));
            $output .= " />";
        }
    
    if(!empty($profile_field_settings['after'])) 
    $output .= stripslashes(htmlspecialchars_decode($profile_field_settings['after']));
    
    $output .= "</td>
</tr>";
    endforeach;
    $output .= "</tbody>\n</table>\n";
    endif; // !empty($profile_fields)
    if($echo):
    echo  $output;
    else:
    return $output;
    endif;
}

function umm_sort($a, $b){
    $orderby = UMM_ORDERBY;
    $order = strtolower(UMM_ORDER);
    switch($order){        
        case "desc":
        if ( $a->$orderby > $b->$orderby ) return -1;
        if ( $a->$orderby < $b->$orderby ) return 1;
        return 0;
        break;
        
        default:
        if ( $a->$orderby < $b->$orderby ) return -1;
        if ( $a->$orderby > $b->$orderby ) return 1;
        return 0;
        break;
    }   
}

function umm_subpage_title($user_id, $text){
    $nickname = get_user_meta($user_id, 'nickname', true);
    $output = '<h3 class="umm-subpage-title">' . sprintf($text, "<a href=\"" . admin_url("user-edit.php?user_id=" . $user_id) . "\" target=\"_blank\"><em>" . $nickname .  "</em></a>") . '</h3>';
    return $output;
}

function umm_ui(){
    if(!current_user_can('edit_users')):
    _e("You do not have the appropriate permission to view this content.", "user-meta-manager");
    else:
    $_UMM_UI = new UMM_UI();
    $_UMM_UI->display_module();
    endif;
}

function umm_update_columns(){
    global $wpdb;
    $umm_column = @explode("|", $_REQUEST['umm_column_key']);
    $umm_column_key = $umm_column[0];
    switch($_REQUEST['mode']){
        case "add_columns":        
        $umm_table = $umm_column[1];
        $umm_column_label = $_REQUEST['umm_column_label'];
        if($umm_column_key == '' || $umm_column_label == ''):
          $output = __('Key and label are both required. Try again.', 'user-meta-manager');
        else:
          if(umm_column_exists($umm_column_key)):
            $output = __('Column already exists.', 'user-meta-manager');
          else:
            switch($umm_table){
                case "users":
                $users_columns = (!get_option("umm_users_columns") ? array('ID' => __('ID', 'user-meta-manager'), 'user_login' => __('User Login', 'user-meta-manager'), 'user_registered' => __('Date Registered', 'user-meta-manager')) : get_option("umm_users_columns"));
                $users_columns[$umm_column_key] = $umm_column_label;
                update_option("umm_users_columns", $users_columns);
                break;
                
    
                case "usermeta":
                $usermeta_columns = (!get_option("umm_usermeta_columns")) ? array() : get_option("umm_usermeta_columns");
                $usermeta_columns[$umm_column_key] = $umm_column_label;
                update_option("umm_usermeta_columns", $usermeta_columns);
                break;
            }
            $output = __('Column successfully added.', 'user-meta-manager');
          endif;           
        endif;
        break;
        
        case "remove_columns":
        if(empty($_REQUEST['umm_column_key'])):
          $output = __('No key was selected. Select a key to remove from the table.', 'user-meta-manager');
        else:
        $users_columns = (!get_option("umm_users_columns") ? array('ID' => __('ID', 'user-meta-manager'), 'user_login' => __('User Login', 'user-meta-manager'), 'user_registered' => __('Date Registered', 'user-meta-manager')) : get_option("umm_users_columns"));
        $usermeta_columns = (!get_option("umm_usermeta_columns")) ? array() : get_option("umm_usermeta_columns");
        if(array_key_exists($umm_column_key, $users_columns)):
            unset($users_columns[$umm_column_key]);
            update_option("umm_users_columns", $users_columns);
        elseif(array_key_exists($umm_column_key, $usermeta_columns)):
            unset($usermeta_columns[$umm_column_key]);
            update_option("umm_usermeta_columns", $usermeta_columns);         
        endif;
        $output = __('Column successfully removed.', 'user-meta-manager');
        endif;
        break;
        
    }
    print $output;
    exit; 
}

function umm_update_profile_fields(){
    global $current_user;
    $saved_profile_fields = (!get_option('umm_profile_fields')) ? array() : get_option('umm_profile_fields');
    foreach($saved_profile_fields as $field_name => $field_settings):
      $posted_value = (isset($_REQUEST[$field_name])) ? trim($_REQUEST[$field_name]) : '';
      $field_value = ($posted_value == '') ? $field_settings['value'] : addslashes(htmlspecialchars($posted_value));
      update_user_meta($current_user->ID, $field_name, $field_value);
    endforeach;
}

function umm_update_user_meta(){
    global $wpdb;
    $mode = $_POST['mode'];
    $all_users = (!empty($_POST['all_users'])) ? true : false;
    $umm_data = get_option('user_meta_manager_data');
    $meta_key = (!empty($_POST['meta_key'])) ? $_POST['meta_key'] : '';
    $meta_value = $_POST['meta_value'];     
    if($meta_key != ""):
    
    if(array_key_exists($meta_key, $umm_data) && $_POST['mode'] == 'add'):
    // meta_key already exists
    $output = '<span class="umm-error-message">' . __('Error: Meta key already existed. Choose a different name.', 'user-meta-manager') . '</span>';
    else: 
    
    switch($mode){       
        case "add":
        case "edit":
        
        if($all_users):
            $data = $wpdb->get_results("SELECT * FROM " . $wpdb->users);
            foreach($data as $user):
                update_user_meta($user->ID, $meta_key, $meta_value, false);
            endforeach;
            $umm_data[$meta_key] = $_POST['meta_value'];
            update_option('user_meta_manager_data', $umm_data);
        else:
            update_user_meta($_POST['u'], $meta_key, $meta_value, false);
        endif;
        
        $saved_profile_fields = get_option('umm_profile_fields');
        
        if(empty($saved_profile_fields)) $saved_profile_fields = array(); 
              
        $options = array();
        if(!empty($_POST['umm_profile_field_type'])):        
          if(!empty($_POST['umm_profile_select_label']) && ($_POST['umm_profile_field_type'] == 'select' || $_POST['umm_profile_field_type'] == 'radio')):
          $x = 0;
          foreach($_POST['umm_profile_select_label'] as $option_label):
            if($option_label != ''):
            $options[$x] = array('label' => $option_label, 'value' => $_POST['umm_profile_select_value'][$x]);
            $x++;
            endif; 
          endforeach;
          else:
          $options = array();
          endif;
        
          $new_profile_field_data = array('value' => $meta_value,
                                          'type' => $_POST['umm_profile_field_type'],
                                          'label' => htmlspecialchars($_POST['umm_profile_field_label']),
                                          'class' => $_POST['umm_profile_field_class'],
                                          'attrs' => htmlspecialchars($_POST['umm_profile_field_attrs']),
                                          'after' => htmlspecialchars($_POST['umm_profile_field_after']) ,
                                          'required' => $_POST['umm_profile_field_required'],
                                          'options' => $options);                   
        endif;
        
        if(!empty($meta_key)):
        
        $umm_data[$meta_key] = $meta_value;
        
        if($_POST['u'] == 'all'):
            $data = $wpdb->get_results("SELECT * FROM $wpdb->users");
            foreach($data as $user):
               update_user_meta($user->ID, $meta_key, maybe_unserialize(trim(stripslashes($meta_value))));
            endforeach;
         endif;   
         
         if((!array_key_exists($meta_key, $saved_profile_fields) || array_key_exists($meta_key, $saved_profile_fields)) && !empty($_POST['umm_profile_field_type'])):
           // add or update profile field
           $saved_profile_fields[$meta_key] = $new_profile_field_data;
           update_option('umm_profile_fields', $saved_profile_fields);
         elseif(array_key_exists($meta_key, $saved_profile_fields) && (!isset($_POST['umm_profile_field_type']) || empty($_POST['umm_profile_field_type']))):
           // remove profile field
           unset($saved_profile_fields[$meta_key]);
           update_option('umm_profile_fields', $saved_profile_fields);
         endif; // !array_key_exists                
         update_option('user_meta_manager_data', $umm_data);
         update_user_meta($_POST['u'], $meta_key, maybe_unserialize(trim(stripslashes($meta_value))));
         switch($mode){
            case 'add':
            $output = __('Meta data successfully added.', 'user-meta-manager');
            break;
            
            default:
            $output = __('Meta data successfully updated.', 'user-meta-manager');
         }
                 
        else: // !$meta_key
        switch($mode){
            case 'add':
            $output = '<span class="umm-error-message">' . __('Error: No meta key entered.', 'user-meta-manager') . '</span>';
            break;
            
            default:
            $output = '<span class="umm-error-message">' . __('Error: No meta key selected.', 'user-meta-manager') . '</span>';
         }        
        endif;                    
        break;

        case "delete":
        if($_POST['meta_key']):
        $meta_key = $_POST['meta_key'];
        $saved_profile_fields = get_option('umm_profile_fields');
        if($all_users):
            $data = $wpdb->get_results("SELECT * FROM $wpdb->users");
            foreach($data as $user):
                delete_user_meta($user->ID, $meta_key);
            endforeach;
            unset($umm_data[$meta_key]);
            update_option('user_meta_manager_data', $umm_data);
            if(array_key_exists($meta_key, $saved_profile_fields)):
            // remove profile field
            unset($saved_profile_fields[$meta_key]);
            update_option('umm_profile_fields', $saved_profile_fields);
            // remove custom column
            $users_columns = (!get_option("umm_users_columns") ? array('ID' => __('ID', 'user-meta-manager'), 'user_login' => __('User Login', 'user-meta-manager'), 'user_registered' => __('Date Registered', 'user-meta-manager')) : get_option("umm_users_columns"));
            $usermeta_columns = (!get_option("umm_usermeta_columns")) ? array() : get_option("umm_usermeta_columns");
            if(array_key_exists($meta_key, $users_columns)):
               unset($users_columns[$meta_key]);
               update_option("umm_users_columns", $users_columns);
            elseif(array_key_exists($meta_key, $usermeta_columns)):
               unset($usermeta_columns[$meta_key]);
               update_option("umm_usermeta_columns", $usermeta_columns);
            endif; // array_key_exists
         endif; // array_key_exists            
        else: // all_users
            delete_user_meta($_POST['u'], $_POST['meta_key']);
        endif;
        $output = __('Meta data successfully deleted.', 'user-meta-manager');
        endif;
        break;
    }
    endif; // meta_key already exists 
    else: // if($meta_key) 
    if($mode) $output =  __('Meta Key is required!', 'user-meta-manager');
    endif;
    print $output;
    exit;
}

function umm_useraccess_shortcode($atts, $content) {
    global $current_user;
    $access = true;
    $key = $atts['key'];
    $value = $atts['value'];
    $users = ($atts['users']) ? explode(" ", $atts['users']) : false;
    $message = $atts['message'];
    
    if($atts['json']):
    $access = false;
      $json = json_decode($atts['json']);
      foreach($json as $k => $v):
        if($k && $v):
          $meta_value = get_user_meta($current_user->ID, $k, true);
          if($meta_value == trim($v)):        
            $access = true;
          endif;  
        endif;
    endforeach; 
    elseif($key && $value):
        $meta_value = get_user_meta($current_user->ID, $key, true);
      if($meta_value != trim($value)):        
          $access = false;
      endif;
    endif;
    

    if($users):
        if(!in_array($current_user->ID, $users)):
           $access = false; 
        endif;
    endif;

    if(!$access):
        if($message):
            $content = $message;
        else:
            $content = __('You do not have sufficient permissions to access this content.', 'user-meta-manager');
        endif;
    endif;    
    return $content;         
}

function umm_usermeta_data($criteria="ORDER BY umeta_id ASC"){
    global $wpdb;
    $data = $wpdb->get_results("SELECT * FROM $wpdb->usermeta " . $criteria);
    return $data;
}

function umm_usermeta_keys_menu($select=true,$optgroup=false,$include_used=false){
    global $wpdb;
    $used_columns = umm_get_columns();
    $output = '';
    if($select):
      $output .= '<select name="umm_usermeta_keys">' . "\n";
    endif;
    if($optgroup):
      $output .= '<optgroup label="wp_usermeta">' . "\n";
    endif;  
    $data = $wpdb->get_results("SELECT DISTINCT meta_key FROM " . $wpdb->usermeta);
    foreach($data as $d):
    if(!array_key_exists($d->meta_key, $used_columns) || (array_key_exists($d->meta_key, $used_columns) && $include_used)):
        $output .= '<option value="' . $d->meta_key . '|usermeta">' . $d->meta_key . '</option>' . "\n";         
    endif;
    endforeach;
    if($optgroup):
      $output .= '</optgroup>' . "\n";
    endif;
    $output .= '</select>' . "\n";
    return $output;    
}

function umm_usermeta_shortcode($atts, $content) {
    global $current_user;
    $key = $atts['key'];
    $user = ($atts['user']) ? $atts['user'] : $current_user->ID;
    
    if($key):
    $content = get_user_meta($user, $key, true);
    return $content; 
    endif;         
}

function umm_users_keys_menu($select=true, $optgroup=false, $include_used=false){
    global $wpdb;
    $used_columns = umm_get_columns();
    $output = '';
    if($select):
      $output .= '<select name="umm_users_keys">' . "\n";
    endif;
    if($optgroup):
      $output .= '<optgroup label="wp_users">' . "\n";
    endif;
    $data = $wpdb->get_results('SELECT * FROM ' . $wpdb->users . ' LIMIT 1');
    foreach($data as $k):
    $k = (array) $k;
    foreach($k as $kk => $vv):
        if(!array_key_exists($kk, $used_columns)):
        $output .= '<option value="' . $kk . '|users">' . $kk . '</option>' . "\n";
        endif;
    endforeach;                
    endforeach;
    if($optgroup):
      $output .= '</optgroup>' . "\n";
    endif;
    if($select):
      $output .= '</select>' . "\n";
    endif;
    return $output; 
}

add_action('admin_menu', 'umm_admin_menu');
add_action('admin_init', 'umm_admin_init');
add_action('user_register', 'umm_default_keys');
//add_action('profile_personal_options', 'umm_show_profile_fields');
add_action('edit_user_profile', 'umm_show_profile_fields');
add_action('profile_update', 'umm_update_profile_fields');
add_action('show_user_profile', 'umm_show_profile_fields');

add_action('wp_ajax_umm_edit_user_meta','umm_edit_user_meta');
add_action('wp_ajax_umm_add_user_meta','umm_add_user_meta');
add_action('wp_ajax_umm_delete_user_meta','umm_delete_user_meta');
add_action('wp_ajax_umm_edit_custom_meta','umm_edit_custom_meta');
add_action('wp_ajax_umm_add_custom_meta','umm_add_custom_meta');
add_action('wp_ajax_umm_delete_custom_meta','umm_delete_custom_meta');
add_action('wp_ajax_umm_update_user_meta','umm_update_user_meta');
add_action('wp_ajax_umm_edit_columns','umm_edit_columns');
add_action('wp_ajax_umm_update_columns','umm_update_columns');
add_action('wp_ajax_umm_backup_page','umm_backup_page');
add_action('wp_ajax_umm_backup','umm_backup');
add_action('wp_ajax_umm_delete_backup_files','umm_delete_backup_files');
add_action('wp_ajax_umm_restore','umm_restore');
add_action('wp_ajax_umm_restore_confirm','umm_restore_confirm');

add_shortcode('usermeta', 'umm_usermeta_shortcode');
add_shortcode('useraccess', 'umm_useraccess_shortcode');


add_filter('contextual_help', 'umm_help', 10, 3);

register_activation_hook(__FILE__, 'umm_install');
register_deactivation_hook(__FILE__, 'umm_deactivate');

?>