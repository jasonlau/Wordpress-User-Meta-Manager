<?php

/**
 * Plugin Name: User Meta Manager
 * Plugin URI: https://github.com/jasonlau/Wordpress-User-Meta-Manager
 * Description: Add, edit, or delete user meta data with this handy plugin. Easily restrict access or insert user meta data into posts or pages and more. <strong>Get the Pro extension <a href="http://jasonlau.biz/home/membership-options#umm-pro">here</a>.</strong>
 * Version: 3.4.6
 * Author: Jason Lau
 * Author URI: http://jasonlau.biz
 * Text Domain: user-meta-manager
 * Disclaimer: Use at your own risk. No warranty expressed or implied.
 * 
 * Always backup your database before making changes.
 * 
 * Copyright 2012+ http://jasonlau.biz
 * 
 * This free version of User Meta Manager is free software; you can redistribute it and/or modify
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

define('UMM_VERSION', '3.4.6');
define("UMM_PATH", plugin_dir_path(__FILE__) . '/');
define("UMM_SLUG", "user-meta-manager");
define("UMM_AJAX", "admin-ajax.php?action=umm_switch_action&amp;umm_sub_action=");
//error_reporting(E_ALL);
include(UMM_PATH . 'includes/umm-table.php');
include(UMM_PATH . 'includes/umm-contextual-help.php');

function umm_add_custom_meta(){
    global $wpdb;
    $output = umm_fyi('<p>'.__('Insert a key and default value in the fields below.', UMM_SLUG).'</p>');
    $output .= '<form id="umm_update_user_meta_form" method="post">
    <strong>'.__('Key', UMM_SLUG).':</strong><br />
    <input name="umm_meta_key[]" title="'.__('Letters, numbers, and underscores only', UMM_SLUG).'" type="text" value="" placeholder="'.__('Meta Key', UMM_SLUG).'" /><br />
    <strong>'.__('Value', UMM_SLUG).':</strong><br />
    <textarea rows="3" cols="40" name="umm_meta_value[]"  placeholder=""></textarea>
    ';
    $output .= '<br /><strong>' . __('Use This Value For All Current Users', UMM_SLUG) . ':</strong><br /><input type="checkbox" name="all_users" value="1" title="' . __('Check the box to add this value to all current users. Note: Using this option on a checkbox field will cause the checkbox to initially be checked for all current users.', UMM_SLUG) . '" /> ' . __('Yes', UMM_SLUG) . ''; 
    $output .= umm_profile_field_editor();
    $output .= '<br />
    <input id="umm_add_user_meta_submit" data-form="umm_update_user_meta_form" data-subpage="umm_update_user_meta" data-wait="'.__('Wait...', UMM_SLUG).'" class="button-primary" type="submit" value="'.__('Submit', UMM_SLUG).'" />
    <input name="mode" type="hidden" value="add" /><input name="umm_user" type="hidden" value="all" /><input name="return_page" type="hidden" value="' . UMM_AJAX . 'umm_add_custom_meta&umm_user=0" />
    </form>  
    ';
    print $output;
    exit;
}

function umm_add_registration_fields(){
    $content = umm_show_profile_fields(false, false, 'register', 'registerform');
    echo $content;
}

function umm_add_user_fields(){
    $umm_content = umm_show_profile_fields(false, false, 'adduser', 'createuser');
    $umm_output = '<div id="umm-add-user-fields" style="display:none;">' . $umm_content . '</div>
    <script type="text/javascript">
       jQuery(function($){
	      $("form#createuser p.submit").before(\'<input type="hidden" name="umm_form" value="createuser" />\' + $("div#umm-add-user-fields").html());
          var is_duplicate = function(obj, key, value){
            var request = $.ajax({
                url: \'admin-ajax.php?action=umm_switch_action&umm_sub_action=umm_is_duplicate&echo=true&umm_key=\' + key + \'&umm_value=\' + value,
                type: "POST",
                dataType: "json"
            });
            request.done(function(data){
                if(data.is_duplicate){
            if(!$(".umm-duplicate-warning").html()){
                $("input#createusersub").prop("disabled", "disabled");
            obj.after(\'<div class="umm-warning umm-duplicate-warning hidden" style="color:red">\' + data.error_message.replace(\'%s\',obj.parent().parent().find(\'th\').html()) + \'</div>\');
            obj.css({"background-color": "#FFFF99"});
            $(".umm-duplicate-warning").show(\'slow\');
            }
            return true;
        } else {
            if($(".umm-duplicate-warning").html()){
                $(".umm-duplicate-warning").hide(\'slow\').remove();
            }
            obj.css({"background-color": "inherit"});
            $("input#createusersub").prop("disabled", "");
            return false;
        }
            });
        };
        $(document).on(\'change\', ".umm-unique", function(event){
            is_duplicate($(this), $(this).attr(\'name\'), $(this).val());
        });
        
        $(document).on(\'click\', "input#createusersub", function(event){
            event.preventDefault();
            var has_duplicates = false;
            $(".umm-unique").each(function(){
                if(is_duplicate($(this), $(this).attr(\'name\'), $(this).val())){
                    has_duplicates = true;
                }
            });
            if(!has_duplicates){
                    $("form#createuser").submit();
            }     
        });
	   });
    </script>
    ';
   echo $umm_output;
}

function umm_add_user_meta(){
    global $wpdb;
    $user_id = $_REQUEST['umm_user'];
    $output = umm_button('home', null, "umm-back-button") . umm_subpage_title($user_id, __('Adding Meta Data For %s', UMM_SLUG));
    $output .= umm_fyi('<p>'.__('Insert a meta key and default value and press <em>Submit</em>.', UMM_SLUG).'</p>');
    $output .= '<form id="umm_update_user_meta_form" method="post">
    <strong>'.__('Key', UMM_SLUG).':</strong><br />
    <input name="umm_meta_key[]" title="'.__('Letters, numbers, and underscores only', UMM_SLUG).'" type="text" value="" placeholder="'.__('Meta Key', UMM_SLUG).'" /><br />
    <strong>'.__('Value', UMM_SLUG).':</strong><br />
    <textarea cols="30" name="umm_meta_value[]" placeholder="'.__('Default Value', UMM_SLUG).'"></textarea>';
    $output .= '<br />
    <input id="umm_update_user_meta_submit" data-form="umm_update_user_meta_form" data-subpage="umm_update_user_meta" data-wait="'.__('Wait...', UMM_SLUG).'" class="button-primary" type="submit" value="'.__('Submit', UMM_SLUG).'" />
    <input name="mode" type="hidden" value="add" /><input name="umm_user" type="hidden" value="' . $user_id . '" /><input name="return_page" type="hidden" value="' . UMM_AJAX . 'umm_add_user_meta&umm_user=' . $user_id . '" />
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
  add_submenu_page('users.php', 'User Meta Manager', 'User Meta Manager', 'publish_pages', UMM_SLUG, 'umm_ui');
  add_action('admin_enqueue_scripts', 'umm_load_scripts');
}

function umm_backup($backup_mode=false, $tofile=false, $print=true){
    global $wpdb, $current_user, $table_prefix;
    $mode = (!isset($_REQUEST['mode']) || empty($_REQUEST['mode'])) ? '' : $_REQUEST['mode'];
    $mode = (empty($backup_mode)) ? $mode : $backup_mode;
    if(umm_is_pro() && $mode == 'pro'):
       if(function_exists('umm_pro_backup')):
          umm_pro_backup();
          exit;
       endif; 
    endif;    
    $backup_files = umm_get_option('backup_files');
    
    $to_file = (!isset($_REQUEST['tofile']) || empty($_REQUEST['tofile'])) ? '' : $_REQUEST['tofile'];
    $tofile = (empty($tofile)) ? $to_file : $tofile;
    $backup_files = (!$backup_files || $backup_files == '') ? array() : $backup_files;
    $back_button = umm_button("umm_backup_page&umm_user=1", null, "umm-back-button");
    switch($mode){
        case "sql":
        $data = $wpdb->get_results("SELECT * FROM " . $table_prefix . "umm_usermeta_backup");
        $budate = umm_get_option('backup_date');
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
        $output = '<p class="umm-message">' . __("Below is the sql needed to restore the usermeta table.", UMM_SLUG) . "</p><strong>" . __("Backup from", UMM_SLUG) . " " . $budate . "</strong><br />\n<textarea onclick=\"this.focus();this.select();\" cols=\"65\" rows=\"15\">" . $sql . "</textarea>";
        break;
        
        case "php":
        $data = $wpdb->get_results("SELECT * FROM " . $table_prefix . "umm_usermeta_backup");
        $budate = umm_get_option('backup_date');
        $output = '<?php
';
        $output .= "require('" . ABSPATH . "wp-load.php');\n";
        $output .= 'if(!is_user_logged_in() OR !current_user_can(\'update_core\')) wp_die("' . __("Authorization required!", UMM_SLUG) . '");
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
        $output .= "print('" . __("Restore complete.", UMM_SLUG) . "');\nelse:\nprint('<form action=\"#\" method=\"post\"><p>" . __("Are you sure you want to restore all user meta data to the backup version?", UMM_SLUG) . "</p><button type=\"submit\">" . __("Yes", UMM_SLUG) . "</button><input type=\"hidden\" name=\"umm_confirm_restore\" value=\"1\" /></form>');\nendif;\n?>";
        
        if($tofile == "yes"):
          $rs = umm_random_str(10);
          $temp_file = WP_PLUGIN_DIR . "/user-meta-manager/backups/" . "usermeta-backup-" . date("m.j.Y-") . ".php";
          $file = WP_PLUGIN_DIR . "/user-meta-manager/backups/" . "usermeta-backup-" . date("m.j.Y-") . date("g.i.a") . "-" . $current_user->ID . "-" . $_SERVER['REMOTE_ADDR'] . "-" . $rs . ".php";
          $link = WP_PLUGIN_URL . "/user-meta-manager/backups/" . "usermeta-backup-" . date("m.j.Y-") . date("g.i.a") . "-" . $current_user->ID . "-" . $_SERVER['REMOTE_ADDR'] . "-" . $rs . ".php";
          array_push($backup_files, $file);
          umm_update_option('backup_files', $backup_files);
          
          if($fp = @fopen($temp_file, "w+")):
            @chmod($temp_file, 0755);
            fwrite($fp, trim($output));
            fclose($fp);
            // Some servers need permissions set
            @chmod($temp_file, 0755);
            @rename($temp_file, $file);
            
            $output = '<p class="umm-message">' . __("Backup php file was successfully generated at ", UMM_SLUG) . ' <a href="' . $link . '" target="_blank">' . $link . '</a></p><p>' . __("Run the file in your browser to begin the restoration process.", UMM_SLUG) . '</p>' . "\n";
          else:
            $output = '<p class="umm-warning">' . __("Error: Backup php file could not be generated at ", UMM_SLUG) . ' ' . WP_PLUGIN_DIR . '/user-meta-manager/backups' . '</p><p>' . __("Please be sure the directory exists and is owner-writable.", UMM_SLUG) . '</p>' . "\n";
          endif;          
        else:
        $output = '<p class="umm-message">' . __("Below is the php needed to restore the usermeta table. Save this code as a php file to the root WordPress folder, then run it in your browser.", UMM_SLUG) . '</p><strong>' . __("Backup from", UMM_SLUG) . ' ' . $budate . '</strong><br />
        <textarea onclick="this.focus();this.select();" cols="65" rows="15">' . $output . '</textarea>' . "\n";
        endif;
        break;
        
        default:
        $wpdb->query("DROP TABLE IF EXISTS  " . $table_prefix . "umm_usermeta_backup");
        $wpdb->query("CREATE  TABLE  " . $table_prefix . "umm_usermeta_backup (umeta_id bigint(20) unsigned NOT NULL AUTO_INCREMENT, user_id bigint(20) unsigned NOT NULL DEFAULT '0', meta_key varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL, meta_value longtext COLLATE utf8_unicode_ci, PRIMARY KEY (umeta_id), KEY user_id (user_id), KEY meta_key (meta_key))");
        $wpdb->query("INSERT INTO " . $table_prefix . "umm_usermeta_backup SELECT * FROM " . $wpdb->usermeta);
        umm_update_option('backup_date', date("M d, Y") . ' ' . date("g:i A"));
        $output = '<p class="umm-message">' . __("User meta data backup was successful.", UMM_SLUG) . "</p>";
        break;
    } 
    
    if($print):
      print '<div class="umm-backup-page-container">' . $back_button . $output . '</div>';
      exit;  
    endif;  
    
}

function umm_backup_page(){   
    global $wpdb;
    $budate = umm_get_option('backup_date');
    if($budate == "") $budate = __("No backup", UMM_SLUG);
    $nonce = wp_create_nonce(md5($_SERVER["REMOTE_ADDR"].$_SERVER["HTTP_USER_AGENT"]));
    $fields1 = umm_usermeta_keys_menu(true, true, true, 'csv', 'umm-csv-builder-keys');
    $fields2 = umm_usermeta_keys_menu(true, true, true, 'csv', 'umm-csv-builder-keys', false);
    $output = umm_fyi('<p>'.__('Use the following links to backup and restore user meta data.', UMM_SLUG).'</p>');  
    $output .= '<div class="umm-backup-page-container">';
    $output .= '<ul><li><a href="#" data-subpage="' . UMM_AJAX . 'umm_backup&amp;umm_user=1" title="'.__('Backup', UMM_SLUG).'" class="umm-subpage">'.__('Backup', UMM_SLUG).'</a> <strong>'.__('Last Backup:', UMM_SLUG). '</strong> ' . $budate . '</li>';  
    $output .= '<li><a href="#" data-subpage="' . UMM_AJAX . 'umm_restore_confirm&amp;umm_user=1" title="'.__('Restore', UMM_SLUG).'" class="umm-subpage">'.__('Restore', UMM_SLUG).'</a></li>
    <li><a href="#" data-subpage="' . UMM_AJAX . 'umm_backup&amp;mode=sql&amp;umm_user=1" title="'.__('Generate SQL', UMM_SLUG).'" class="umm-subpage">'.__('Generate SQL', UMM_SLUG).'</a></li>
    <li><a href="#" data-subpage="' . UMM_AJAX . 'umm_backup&amp;mode=php&amp;umm_user=1" title="'.__('Generate PHP', UMM_SLUG).'" class="umm-subpage">'.__('Generate PHP', UMM_SLUG).'</a></li>
    <li><a href="#" data-subpage="' . UMM_AJAX . 'umm_backup&amp;mode=php&amp;tofile=yes&amp;umm_user=1" title="'.__('Generate PHP Restoration File', UMM_SLUG).'" class="umm-subpage">'.__('Generate PHP Restoration File', UMM_SLUG).'</a></li>
    <li><a href="#" data-subpage="' . UMM_AJAX . 'umm_delete_backup_files" title="'.__('Delete All Backup Files', UMM_SLUG).'" class="umm-subpage">'.__('Delete All Backup Files', UMM_SLUG).'</a></li>
    </ul>';
    $output .= '<div class="umm-csv-builder"><strong class="umm-csv-builder">'.__('Generate CSV', UMM_SLUG).'</strong>'
    . "<table class='umm-csv-builder'><tr>
	<td><strong>" . __('Fields', UMM_SLUG) . ":</strong> <span>" . __("Assemble a list of meta keys to display in the CSV file.", UMM_SLUG) . "</span><input type='hidden' data-for='fields' /><div class='umm-csv-builder-fields'>" . $fields1 . " <input type='button' value=' + ' class='umm-csv-builder-fields-add  button-secondary' /></div></td>
</tr>
<tr><td><button class='umm-csv-builder-submit button-primary' data-csv_link='" . esc_url(home_url('/')) . "wp-admin/users.php?page=user-meta-manager&amp;umm_output=csv' title='".__('Get CSV', UMM_SLUG)."'>".__('Generate CSV', UMM_SLUG)."</button></td>
</tr>
</table><div id='umm-csv-builder-fields-clone' class='umm-csv-builder-fields-clone umm-hidden'><div class='umm-csv-builder-fields'>" . $fields2 . " <input type='button' value=' + ' class='button-secondary umm-csv-builder-fields-add' /> <input type='button' value=' - ' class='button-secondary umm-csv-builder-remove' /></div></div>";
    
    $output .= '</div>';
    if(umm_is_pro()):
       if(function_exists('umm_pro_backup_page')):
          $output .= umm_pro_backup_page();       
       endif; 
    endif;
    $output .= '</div>';
    print $output;
    exit;
}

function umm_button($go_to, $label=null, $css_class=null){
    $label = (!$label) ? __('<< Back', UMM_SLUG) : $label;
    $css_class = (!$css_class) ? 'button-secondary umm-button' : 'button-secondary umm-button ' . $css_class;
    switch($go_to){
        case 'home':
        $umm_button = '<button href="#" data-type="' . $go_to . '" title="' . $label . '" class="umm-homelink ' . $css_class . '">' . $label . '</button>';
        break;
        
        default:
        $umm_button = '<button href="#" data-type="subpage" data-subpage="' . UMM_AJAX . '' . $go_to . '" title="' . $label . '" class="' . $css_class . '">' . $label . '</button>';
    }
    return $umm_button;
}

function umm_column_exists($key){
   $used_columns = umm_get_columns();
   return array_key_exists($key, $used_columns);
}

function umm_get_option($which=false){
    if($umm_data = get_option('user_meta_manager_data')):
       if($which):
          $which_data = (isset($umm_data[$which])) ? $umm_data[$which] : '';
          return $which_data;
       else:
         return $umm_data;
       endif;
    else:
       return false;
    endif;
}

function umm_deactivate(){
    global $wpdb, $table_prefix; 
    $umm_data = umm_get_option();   
    $umm_settings = $umm_data['settings'];
    if(empty($umm_settings)) $umm_settings = array('retain_data' => 'yes');
    if($umm_settings['retain_data'] == 'no'):
     // Delete all saved data
     $custom_meta = $umm_data['custom_meta'];
     if(empty($custom_meta)) $custom_meta = array();
     $umm_singles_data = $umm_data['singles_data'];
     if(empty($umm_singles_data)) $umm_singles_data = array();
     $data = $wpdb->get_results("SELECT * FROM " . $wpdb->users);
     foreach($data as $user):
        foreach($umm_data as $meta_key => $value):
           delete_user_meta($user->ID, $meta_key);
        endforeach;
        foreach($umm_singles_data as $meta_key):
           delete_user_meta($user->ID, $meta_key);
        endforeach;
     endforeach;
     delete_option('user_meta_manager_data');
     $wpdb->query("DROP TABLE IF EXISTS " . $table_prefix . "umm_usermeta_backup");
     
     // Delete depreciated options if they exist
     delete_option('umm_singles_data');    
     delete_option('umm_users_columns');
     delete_option('umm_usermeta_columns');
     delete_option('umm_backup_date');
     delete_option('umm_backup_files');
     delete_option('umm_profile_fields');
     delete_option('umm_settings');
     delete_option('umm_sort_order');
     
    endif;   
}

function umm_delete_backup_files(){
    $back_button = umm_button("umm_backup_page&umm_user=1", __('<< Back', UMM_SLUG), "umm-back-button");
    if(!empty($_REQUEST['umm_confirm_backups_delete'])):
    $backups_folder = WP_PLUGIN_DIR . "/user-meta-manager/backups";    
    chmod($backups_folder, 0755);
    $backup_files = umm_get_option('backup_files');
    
    if(is_array($backup_files) && !empty($backup_files)):
    foreach($backup_files as $backup_file):
      @unlink($backup_file);
    endforeach;
    endif;
    umm_update_option('backup_files', array());   
    $output = $back_button . '<p class="umm-message">' . __('All backup files successfully deleted.', UMM_SLUG) . '</p>';
    else:
    $output = $back_button . '<p class="umm-warning"><strong>' . __('Are you sure you want to delete all backup files?', UMM_SLUG) . '</strong><br /><a href="#" data-subpage="' . UMM_AJAX . 'umm_delete_backup_files&amp;umm_confirm_backups_delete=yes" class="umm-subpage">' . __('Yes', UMM_SLUG) . '</a> <a href="#" data-subpage="' . UMM_AJAX . 'umm_backup_page" class="umm-subpage">' . __('Cancel', UMM_SLUG) . '</a></p>';
    endif;
    print $output;
    exit;
    return;
}

function umm_default_keys($user_id){
    // Sets default and posted values for custom meta upon new user registration.
    global $wpdb;
    if(!isset($user_id) || empty($user_id)):
       $data = umm_usermeta_data("ORDER BY user_id DESC LIMIT 1"); // Gets the latest user id
       $user_id = $data[0]->user_id;
    endif;   
    $umm_options = umm_get_option();
    $umm_data = $umm_options['custom_meta'];
    $profile_fields = $umm_options['profile_fields'];
    // Set default values for custom meta
    if(isset($umm_data)):
        foreach($umm_data as $key => $value):
           if((isset($profile_fields[$key]) && $profile_fields[$key]['add_to_profile'] != 'yes') || !isset($profile_fields[$key])):
           if(umm_is_pro()):
              if(isset($profile_fields[$key]['type']) && $profile_fields[$key]['type'] == 'random_string'):
                 $random_string_length = (isset($profile_fields[$key]['random_string_length']) && $profile_fields[$key]['random_string_length'] > 0) ? $profile_fields[$key]['random_string_length'] : 10;
                 $random_string_type = (isset($profile_fields[$key]['random_string_type'])) ? $profile_fields[$key]['random_string_type'] : 'mixed';
                 if($random_string_type == 'numbers'):
                    $random_string_type = 1;
                 elseif($random_string_type == 'letters'):
                    $random_string_type = 2;
                 elseif($random_string_type == 'all'):
                    $random_string_type = 4;
                 else:
                    $random_string_type = 3;
                 endif;              
                 $value = umm_random_str($random_string_length, $random_string_type);                  
              endif;
           endif;
           
           update_user_meta($user_id, $key, $value, false);
           endif;
        endforeach;
    endif;
    // Set posted values for profile fields
    umm_update_profile_fields($user_id);
}

function umm_delete_custom_meta(){
    global $wpdb;
    $data = umm_get_option('custom_meta');
    if(!empty($data)):    
    $delete_key = (!isset($_REQUEST['umm_edit_key']) || empty($_REQUEST['umm_edit_key'])) ? '' : $_REQUEST['umm_edit_key'];
    $sub_mode = (!isset($_REQUEST['sub_mode']) || empty($_REQUEST['sub_mode'])) ? '' : $_REQUEST['sub_mode'];
    if($delete_key == "" && $sub_mode != "confirm"):
    $output = umm_fyi('<p>'.__('Select from the menu a meta key to delete.').'</p>');  
    $output .= '<form id="umm_update_user_meta_form" method="post">
    <strong>'.__('Meta Key', UMM_SLUG).':</strong> <select id="umm_edit_key" name="umm_edit_key" class="umm_meta_key_menu">
    <option value="">'.__('Select A Meta Key', UMM_SLUG).'</option>
    ';

    if($data):
       foreach($data as $key => $value):
        $output .= '<option value="' . $key . '">' . $key . '</option>' . "\n";
       endforeach; 
    endif;   

    $output .= '</select> <input id="umm_delete_user_meta_submit" data-form="umm_update_user_meta_form" data-subpage="umm_update_user_meta" data-wait="'.__('Wait...', UMM_SLUG).'" class="button-primary button-delete" type="submit" value="'.__('Submit', UMM_SLUG).'" /><input name="all_users" type="hidden" value="true" /><input name="mode" type="hidden" value="" /><input name="umm_user" type="hidden" value="all" /><input name="return_page" type="hidden" value="' . UMM_AJAX . 'umm_delete_custom_meta&umm_user=0" />
    </form>  
    ';
    else:
    $output = '<form id="umm_update_user_meta_form" method="post">
    <strong>'.__('Deleting', UMM_SLUG).':</strong> ' . $delete_key . '
    <p class="umm-warning">
    '.__('Are you sure you want to delete that item?', UMM_SLUG).'<br />
    <input id="umm_delete_user_meta_submit" data-form="umm_update_user_meta_form" data-subpage="umm_update_user_meta" data-wait="'.__('Wait...', UMM_SLUG).'" class="button-primary button-delete" type="submit" value="'.__('Yes', UMM_SLUG).'" /> ';
    $output .= umm_button("umm_delete_custom_meta&umm_user=0", __('Cancel', UMM_SLUG));
    $output .= '<input name="umm_edit_key" type="hidden" value="' . $delete_key . '" />
    <input name="all_users" type="hidden" value="true" /><input name="mode" type="hidden" value="delete" /><input name="umm_user" type="hidden" value="all" /><input name="return_page" type="hidden" value="' . UMM_AJAX . 'umm_delete_custom_meta&umm_user=0" /><input name="sub_mode" type="hidden" value="confirm" /></p>
    </form>';   
    endif;
    else: // !empty($data)
    $output = __('No custom meta to delete.', UMM_SLUG);
    endif; // !empty($data)
    print $output;
    exit;
}

function umm_delete_single_key($key){
    global $wpdb;
    $umm_data = umm_get_option();
    $profile_fields = $umm_data['profile_fields'];
    $custom_meta = $umm_data['custom_meta'];    
    unset($profile_fields[$key]);
    unset($umm_data[$key]);    
    umm_update_option('profile_fields', $profile_fields);
    umm_update_option('custom_meta', $custom_meta);
    $data = $wpdb->get_results("SELECT * FROM " . $wpdb->users);
    foreach($data as $user):
      update_user_meta($user->ID, $meta_key, $meta_value, false);
    endforeach;
    $output = "<p>" . __("Meta data successfully deleted.", UMM_SLUG) . "</p>";
    print $output;
    exit;
}

function umm_delete_user_meta(){
    global $wpdb;
    $user_id = $_REQUEST['umm_user'];
    $data = umm_usermeta_data("WHERE user_id = $user_id");
    $output = umm_button('home', null, "umm-back-button") . umm_subpage_title($user_id, __('Deleting Meta Data For %s', UMM_SLUG));
    
    $all_users = (isset($_REQUEST['all_users']) && !empty($_REQUEST['all_users'])) ? $_REQUEST['all_users'] : '';
    $delete_key = (isset($_REQUEST['umm_edit_key']) && trim($_REQUEST['umm_edit_key']) != "" && trim($_REQUEST['umm_edit_key']) != "undefined") ? trim($_REQUEST['umm_edit_key']) : "";
    
    if($delete_key == ""):
    
    $output .= umm_fyi('<p>'.__('Select a <em>Meta Key</em> to delete, then press the <em>Submit</em> button. Select <em>All Users</em> to delete the selected item from all users.').'</p>', UMM_SLUG);
    $output .= '<form id="umm_update_user_meta_form" method="post">
    <strong>'.__('Meta Key', UMM_SLUG).':</strong> <select id="umm_edit_key" name="umm_edit_key" class="umm_meta_key_menu">
    <option value="">'.__('Select A Meta Key', UMM_SLUG).'</option>
    ';

    foreach($data as $d):
        $output .= '<option value="' . $d->meta_key . '">' . $d->meta_key . '</option>' . "\n";
    endforeach;

    $output .= '</select><br />
    <strong>'.__('All Users', UMM_SLUG).':</strong> <select name="all_users" size="1">
	<option value="false">'.__('No', UMM_SLUG).'</option>
	<option value="true">'.__('Yes', UMM_SLUG).'</option>
</select><br />
    <input id="umm_delete_user_meta_submit" data-form="umm_update_user_meta_form" data-subpage="umm_update_user_meta" data-wait="'.__('Wait...', UMM_SLUG).'" class="button-primary button-delete" type="submit" value="'.__('Submit', UMM_SLUG).'" />
    <input name="mode" type="hidden" value="" /><input name="umm_user" type="hidden" value="' . $user_id . '" /><input name="return_page" type="hidden" value="' . UMM_AJAX . 'umm_delete_user_meta&umm_user=' . $user_id . '" />
    </form>  
    ';
    else:
    $output = '<form id="umm_update_user_meta_form" method="post">
    <strong>'.__('Deleting', UMM_SLUG).':</strong> ' . $delete_key . '
    <p class="umm-warning">
    '.__('Are you sure you want to delete that item?', UMM_SLUG).'<br />
    <input id="umm_delete_user_meta_submit" data-form="umm_update_user_meta_form" data-subpage="umm_update_user_meta" data-wait="'.__('Wait...', UMM_SLUG).'" class="button-primary button-delete" type="submit" value="'.__('Yes', UMM_SLUG).'" /> ';
    $output .= umm_button("umm_delete_user_meta&umm_user=" . $user_id, __('Cancel', UMM_SLUG));
    $output .= '<input name="umm_edit_key" type="hidden" value="' . $delete_key . '" /><input name="all_users" type="hidden" value="' . $all_users . '" />
    <input name="mode" type="hidden" value="delete" /><input name="umm_user" type="hidden" value="' . $user_id . '" /><input name="return_page" type="hidden" value="' . UMM_AJAX . 'umm_delete_user_meta&umm_user=' . $user_id . '" /><input name="sub_mode" type="hidden" value="confirm" /></p>
    </form>';
    endif;
    print $output;
    exit;
}

function umm_edit_columns(){
    $columns = umm_get_columns();
    $output = umm_fyi('<p>'.__('Use the forms below to edit which table columns are displayed.', UMM_SLUG).'</p>');
    $output .= '<form id="umm_manage_columns_form" method="post">
    <h3>'.__('Display Columns', UMM_SLUG).'</h3>
    <table class="umm_edit_columns_table wp-list-table widefat fixed">
    <thead>
    <tr>
      <th></th>
      <th>'.__('Key', UMM_SLUG).'</th>
      <th>'.__('Label', UMM_SLUG).'</th>
    </tr>
  </thead>
  ';
  $x = 1;
  foreach($columns as $k => $v){
    $c = ($x%2) ? "" : "alternate";
    $cb = ($k != 'ID' && $k != 'user_login') ? '<input type="radio" value="'.$k.'|" name="umm_column_key" />' : '<input type="radio" value="'.$k.'|" name="umm_column_key" disabled="disabled" title="Required" />';
    $output .= '<tr class="' . $c . '"><td>' . $cb . '</td><td>' . $k . '</td><td>' . $v . '</td></tr>' . "\n";
    $x++;
  }
   $output .= '</table>
   <input id="umm_update_columns_submit" data-form="umm_manage_columns_form" data-subpage="umm_update_columns" data-wait="'.__('Wait...', UMM_SLUG).'" data-mode="delete" class="button-primary" type="submit" value="'.__('Remove Selected Column', UMM_SLUG).'" />
   <input name="mode" type="hidden" value="remove_columns" /><input name="return_page" type="hidden" value="' . UMM_AJAX . 'umm_edit_columns" />
   </form>
   <form id="umm_add_columns_form" method="post">
   <h3>'.__('Add A New Column', UMM_SLUG).'</h3>
   <strong>'.__('Key', UMM_SLUG).':</strong> <select name="umm_column_key">
   <option value="">'.__('Keys', UMM_SLUG).'</option>';
   $output .= umm_users_keys_menu(false, true); 
   $output .= umm_usermeta_keys_menu(false, true);
   $output .= '</select><br>
   <strong>'.__('Label', UMM_SLUG).':</strong> <input name="umm_column_label" type="text" value="" placeholder="'.__('Enter a label', UMM_SLUG).'" title="'.__('Enter a label which will appear in the top row of the results table.', UMM_SLUG).'" /><br />';   
   $output .= '<input id="umm_update_columns_submit" data-form="umm_add_columns_form" data-subpage="umm_update_columns" data-wait="'.__('Wait...', UMM_SLUG).'" data-mode="add" class="button-primary" type="submit" value="'.__('Add Column', UMM_SLUG).'" />
    <input name="mode" type="hidden" value="add_columns" /><input name="return_page" type="hidden" value="' . UMM_AJAX . 'umm_edit_columns" />
    </form>  
    ';
    print $output;
    exit;
}

function umm_edit_custom_meta(){
    global $wpdb;
    $data = umm_get_option('custom_meta');
    if(!$data):
       $output = __('No custom meta to edit.', UMM_SLUG); 
    else:
    $edit_key = (!isset($_REQUEST['umm_edit_key']) || empty($_REQUEST['umm_edit_key'])) ? '' : $_REQUEST['umm_edit_key'];
    if($edit_key == ""):
        $output = umm_fyi('<p>'.__('Select from the list a meta key to edit, or drag and drop to sort.', UMM_SLUG).'</p>');
        $output .= '<form id="umm_update_user_meta_form" method="post" data-error_message="' . __('Select a meta key to edit.', UMM_SLUG). '">
        <h2>'.__('Edit Key', UMM_SLUG).'</h2>
        <ul id="umm_edit_key">
        ';
        $sort_order = umm_get_option('sort_order');
        if(empty($sort_order) || !is_array($sort_order)):
          $sort_order = array();
          $x = 0;
          foreach($data as $field_name => $field_settings):
              $sort_order[$x] = $field_name;
              $x++;
          endforeach;
        endif;
        
        /* Sort the fields */
        if($sort_order):
           $new_array = array();
           foreach($sort_order as $profile_field_name):
              if(isset($data[$profile_field_name]))
              $new_array[$profile_field_name] = $data[$profile_field_name];
           endforeach;
           $data = $new_array;
        endif;
        $x = 1;
        foreach($data as $key => $value):
            $class = ($x%2) ? ' class="alternate"' : ' class="umm-draggable"';
            $output .= '<li' . $class . ' title="'.__('Drag and drop to sort.', UMM_SLUG).'"><input type="radio" name="umm_edit_key" value="'.$key.'" title="'.__('Select a key to edit.', UMM_SLUG).'" /><input name="umm_item[]" type="hidden" value="' . $key . '" /> '.$key.'</li>
            ';
            $x++;
        endforeach;    
            $output .= '</ul>';
            $output .= '<input id="umm_edit_custom_meta_submit" data-form="umm_update_user_meta_form" data-subpage="umm_update_user_meta" data-wait="'.__('Wait...', UMM_SLUG).'" class="button-primary" type="submit" value="'.__('Submit', UMM_SLUG).'" />
    <input name="mode" type="hidden" value="" /><input name="umm_user" type="hidden" value="all" /><input name="return_page" type="hidden" value="' . UMM_AJAX . 'umm_edit_custom_meta" /> 
    </form> 
    ';
    
    else:
    $profile_fields = umm_get_option('profile_fields');
    if(!$profile_fields) $profile_fields = array();
    $output = '<strong>' . __('Now Editing', UMM_SLUG) . ':</strong> <span class="umm-highlight">' . $_REQUEST['umm_edit_key'] . '</span>';
    $output .= umm_fyi('<p>'.__('Editing custom meta data here will edit the value for all new users. The value you set will become the default value for all users. New registrations will receive the custom meta key and default value.', UMM_SLUG).'</p>');
    $output .= '<form id="umm_update_user_meta_form" method="post">
    ';
    
    
    if(!$data):
       $output .= '<tr>
       <td colspan="2">' . __('No custom meta to display.', UMM_SLUG) . '</td>
       </tr>'; 
    else:
        foreach($data as $key => $value):
        if($key == $_REQUEST['umm_edit_key']):
            $output .= '<strong>' . __('Value', UMM_SLUG) . ':</strong><input name="umm_meta_key[]" type="hidden" value="' . $key . '" /><br /><textarea rows="3" cols="40" name="umm_meta_value[]"  placeholder="">' . htmlspecialchars($value) . '</textarea><br />';
            endif; 
        endforeach;
    endif;
    $output .= '<strong>' . __('Update Value For All Current Users', UMM_SLUG) . ':</strong><br /><input type="checkbox" name="all_users" value="1" title="' . __('Check the box to update the value for all current users. Leave blank to update the value for future registrations only.', UMM_SLUG) . '" /> ' . __('Yes', UMM_SLUG) . ''; 
    $output .= umm_profile_field_editor($edit_key);
    $output .= '<input id="umm_update_user_meta_submit" data-form="umm_update_user_meta_form" data-subpage="umm_update_user_meta" data-wait="'.__('Wait...', UMM_SLUG).'" class="button-primary" type="submit" value="'.__('Update', UMM_SLUG).'" />
    <input name="mode" type="hidden" value="edit" /><input name="umm_user" type="hidden" value="all" /><input name="return_page" type="hidden" value="' . UMM_AJAX . 'umm_edit_custom_meta" />
    </form>  
    ';
    endif; // edit_key
    endif; // !$data
    print $output;
    exit;
}

function umm_edit_user_meta(){  
    global $wpdb;
    $profile_fields = umm_get_option('profile_fields');
    $user_id = $_REQUEST['umm_user'];
    $data = umm_usermeta_data("WHERE user_id = $user_id");
    sort($data);
    $umm_settings = umm_get_option('settings');
    $shortcut_editing = empty($umm_settings['shortcut_editing']) ? 'no' : $umm_settings['shortcut_editing'];
    $output = umm_button('home', null, "umm-back-button") . umm_subpage_title($user_id, __('Editing Meta Data For %s', UMM_SLUG));
    $output .= umm_fyi('<p>'.__('Editing an item here will only edit the item for the selected user and not for all users.<br /><a href="#" data-subpage="' . UMM_AJAX . 'umm_edit_custom_meta&umm_user=1" data-nav_button="Edit Custom Meta" title="Edit Custom Meta" class="umm-subpage">Edit Custom Meta Data For All Users</a>', UMM_SLUG).'</p>');
    $edit_key = (isset($_REQUEST['umm_edit_key']) && !empty($_REQUEST['umm_edit_key'])) ? $_REQUEST['umm_edit_key'] : '';
    if($edit_key == "" && $shortcut_editing == 'no'):
        $output .= '<form id="umm_update_user_meta_form" method="post">
        <strong>Edit Key:</strong> <select id="umm_edit_key" name="umm_edit_key" title="' . __('Select a meta key to edit.', UMM_SLUG) . '">
        <option value="">' . __('Select A Key To Edit', UMM_SLUG) . '</option>
        ';
        foreach($data as $d):
            $output .= '<option value="'.$d->meta_key.'">'.$d->meta_key.'</option>
            ';
        endforeach;    
            $output .= '</select> 
    <input id="umm_edit_custom_meta_submit" data-form="umm_update_user_meta_form" data-subpage="umm_update_user_meta" data-wait="'.__('Wait...', UMM_SLUG).'" class="button-primary" type="submit" value="'.__('Submit', UMM_SLUG).'" />
    <input name="mode" type="hidden" value="edit" /><input name="umm_user" type="hidden" value="' . $user_id . '" /><input name="return_page" type="hidden" value="' . UMM_AJAX . 'umm_edit_user_meta&umm_user=' . $user_id . '" />
    </form> 
    ';
        
    else:
    if($shortcut_editing == 'no'):
       $output .= '<strong>' . __('Now Editing', UMM_SLUG) . ':</strong> ' . $_REQUEST['umm_edit_key'];
    endif;
    
    $output .= '<form id="umm_update_user_meta_form" method="post">
    <table class="umm_edit_table wp-list-table widefat">
    <thead>
    <tr>
      <th>'.__('Key', UMM_SLUG).'</th>
      <th>'.__('Value', UMM_SLUG).'</th>
    </tr>
  </thead>
    ';
    $x = 1;
    foreach($data as $d):
    $class = ($x%2) ? ' class="alternate"' : '';
    if($d->meta_key == $edit_key || $shortcut_editing == 'yes'):
        $output .= '<tr' . $class . '><td width="25%">' . $d->meta_key . '</td><td><input name="umm_meta_key[]" type="hidden" value="' . $d->meta_key . '" /><textarea name="umm_meta_value[]" value="" cols="40" rows="1">' . stripslashes(htmlspecialchars($d->meta_value)) . '</textarea>';
        if(array_key_exists($d->meta_key, $profile_fields) && $profile_fields[$d->meta_key]['unique'] == 'yes'):
           $output .= ' <small>*'.__('Unique Value Required', UMM_SLUG).'</small>';
               
        endif;
        $output .= '</td></tr>';
        $x++;
    endif;         
    endforeach;

    $output .= '</table>';
    if($shortcut_editing == 'yes'):
      $output .= '<input name="umm_edit_key" type="hidden" value="all" />';
    endif;
    $output .= '<input id="umm_update_user_meta_submit" data-form="umm_update_user_meta_form" data-subpage="umm_update_user_meta" data-wait="'.__('Wait...', UMM_SLUG).'" class="button-primary" type="submit" value="'.__('Update', UMM_SLUG).'" />
    <input name="mode" type="hidden" value="edit" /><input name="umm_user" type="hidden" value="' . $user_id . '" /><input name="return_page" type="hidden" value="' . UMM_AJAX . 'umm_edit_user_meta&umm_user=' . $user_id . '" />
    </form>  
    ';
    endif;
    print $output;
    exit;
}

function umm_first_run(){
    $settings['first_run'] = 'no';
    umm_update_option('settings', $settings);
}

function umm_format_text($text, $format){
    switch ($format):
       case 'ucfirst':
       $formatted = ucfirst($text);    
       break;
              
       case 'ucwords':
       $formatted = ucwords($text);  
       break;
              
       case 'uc':
       $formatted = strtoupper($text);  
       break;
              
       case 'lc':
       $formatted = strtolower($text);  
       break;
              
       default:
       $formatted = $text;
       break;  
    endswitch;        
    return $formatted;         
}

function umm_fyi($message){
    return '<div class="umm-fyi">' . $message . '</div>';
}

function umm_get_columns(){
    $umm_options = umm_get_option();
    $users_columns = (!$umm_options["users_columns"] ? array('ID' => __('ID', UMM_SLUG), 'user_login' => __('User Login', UMM_SLUG), 'user_registered' => __('Date Registered', UMM_SLUG)) : $umm_options["users_columns"]);
    $usermeta_columns = (!$umm_options["usermeta_columns"]) ? array() : $umm_options["usermeta_columns"];
    return array_merge($users_columns, $usermeta_columns);
}

function umm_get_csv(){
    $filename = 'user_meta_data_' . date('m-j-y-g-i-a') . '.csv';
    $data = umm_usermeta_data();
    $output = '"umeta_id","user_id","meta_key","meta_value"' . "\n";			
      
    header("Content-Type: application/octet-stream");
    header("Content-Disposition: attachment; filename=\"$filename\"");
    header("Expires: ".gmdate("D, d M Y H:i:s", mktime(date("H")+2, date("i"), date("s"), date("m"), date("d"), date("Y")))." GMT");
    header("Last-Modified: ".gmdate("D, d M Y H:i:s")." GMT");
    header("Cache-Control: no-cache, must-revalidate");
    header("Pragma: no-cache");
    foreach($data as $o):
      if(!empty($o->umeta_id)):
         if(isset($_REQUEST['umm_key']) && !empty($_REQUEST['umm_key'])):
            if(in_array($o->meta_key, $_REQUEST['umm_key']) || $_REQUEST['umm_key'][0] == 'all'):
               $output .= '"' . $o->umeta_id . '","'  . $o->user_id . '","' . $o->meta_key . '","' . addslashes($o->meta_value) .  '"' . "\n";
            endif;
         else:
            $output .= '"' . $o->umeta_id . '","' . $o->user_id . '","' . $o->meta_key . '","' . addslashes($o->meta_value) .  '"' . "\n";
         endif;
      endif;
    endforeach;
    print($output);
    exit;
}

function umm_get_profile_fields($output_type='array'){
    $umm_options = umm_get_option();
    $profile_fields = $umm_options['profile_fields'];
    if(empty($profile_fields) || !is_array($profile_fields)) $profile_fields = array();
    $sort_order = $umm_options['sort_order'];
    if(empty($sort_order) || !is_array($sort_order)) $sort_order = false;
    if($sort_order):
      $new_array = array();
      foreach($sort_order as $profile_field_name):
      if(isset($profile_fields[$profile_field_name]))
       $new_array[$profile_field_name] = $profile_fields[$profile_field_name];
      endforeach;
      $profile_fields = $new_array;   
    endif;
    switch($output_type){
        case "select":
         $output = '<select class="umm-profile-fields-select" name="umm_edit_key">' . "\n";
         $output .= '<option value="" selected="selected">' . __('Select A Key', UMM_SLUG) . '</option>' . "\n";
         foreach($profile_fields as $key => $settings):
            $output .= '<option value="' . $key . '">' . $key . '</option>' . "\n";
         endforeach;
         $output .= '</select>' . "\n";
         return $output;
        break;
        
        case "radio":
         foreach($profile_fields  as $key => $settings):
            $output .= '<input type="radio" name="umm_edit_key" value="' . $key . '" />' . $key . ' ' . "\n";
         endforeach;
         return $output;
        break;
        
        default: 
        // Return array
        return $profile_fields;
    }   
}

function umm_get_users($query=false){
    global $wpdb;
    $umm_settings = umm_get_option('settings');
    $m = (isset($umm_settings['max_users']) && !empty($umm_settings['max_users'])) ? $umm_settings['max_users'] : 100;
    if(umm_is_pro()):
       if(!$query):
          $query = "SELECT * FROM " . $wpdb->users;
       endif;
    else:
       if(!$query):
          $query = "SELECT * FROM " . $wpdb->users . " LIMIT 0, " . $m;
       endif;
    endif;
         
    $data = $wpdb->get_results($query);   
    if(defined('MULTISITE') && MULTISITE):
       $blog_id = get_current_blog_id();
       $user_data = array();
       foreach($data as $d):         
          if(is_user_member_of_blog($d->ID, $blog_id)):
             array_push($user_data, $d);
          endif;
       endforeach;
       $data = $user_data;   
    endif;
    return $data;
}

function umm_install(){
    $default_html_before = '<h3 class="umm-custom-fields">[section-title]</h3>
<table class="form-table umm-custom-fields">
   <tbody>';
   $default_html_during = '<tr><th>[label]</th><td>[field]</td></tr>';
   $default_html_after = '</tbody>
</table>';
    $umm_data = array();
    $original_data = get_option('user_meta_manager_data');
    
   if(!is_array($original_data)):
      if($original_data != ''):
         // Backwards compatibility    
         $new_array = array();
         $d = explode(",", $original_data);
         foreach($d as $k):
           array_push($new_array, trim(stripslashes($k)));
         endforeach;
         $original_data['custom_meta'] = $new_array;
      else:
        $original_data = array();
      endif;     
   endif;
   
   if(!array_key_exists('custom_meta', $original_data)):
      $umm_data['custom_meta'] = array();
   else:
      $umm_data['custom_meta'] = $original_data['custom_meta'];  
   endif;
   
   if(!array_key_exists('singles_data', $original_data)):
      $umm_data['singles_data'] = get_option('umm_singles_data');
      if(!is_array($umm_data['singles_data'])):
         $umm_data['singles_data'] = array();
      endif;
   else:
      $umm_data['singles_data'] = $original_data['singles_data']; 
   endif;
   
   if(!array_key_exists('users_columns', $original_data)):
      $umm_data['users_columns'] = get_option('umm_users_columns');
      if(!is_array($umm_data['users_columns'])):
         $umm_data['users_columns'] = array('ID' => __('ID', UMM_SLUG), 'user_login' => __('User Login', UMM_SLUG), 'user_registered' => __('Date Registered', UMM_SLUG));
     endif;
   else:
      $umm_data['users_columns'] = $original_data['users_columns'];
   endif;
   
   if(!array_key_exists('usermeta_columns', $original_data)):
      $umm_data['usermeta_columns'] = get_option('umm_usermeta_columns');
      if(!is_array($umm_data['usermeta_columns'])):
         $umm_data['usermeta_columns'] = array();
     endif;
   else:
      $umm_data['usermeta_columns'] = $original_data['usermeta_columns'];
   endif;
   
   if(!array_key_exists('backup_files', $original_data)):
      $umm_data['backup_files'] = get_option('umm_backup_files');
      if(!is_array( $umm_data['backup_files'])):
         $umm_data['backup_files'] = array();
     endif;
   else:
      $umm_data['backup_files'] = $original_data['backup_files'];
   endif;
   
   if(!array_key_exists('profile_fields', $original_data)):
      $umm_data['profile_fields'] = get_option('umm_profile_fields');
      if(!is_array($umm_data['profile_fields'])):
         $umm_data['profile_fields'] = array();
     endif;
   else:
      $umm_data['profile_fields'] = $original_data['profile_fields'];
   endif;
   
   if(!array_key_exists('settings', $original_data)):
      $umm_data['settings'] = get_option('umm_settings');
      if(!is_array($umm_data['settings'])):
         $umm_data['settings'] = array('retain_data' => 'yes',
                                       'max_users' => 100,
                                       'first_run' => 'yes',
                                       'shortcut_editing' => 'no',
                                       'section_title' => '',
                                       'duplicate_check_override' => 'no',
                                       'bot_field' => 'umm_forbots',
                                       'version' => UMM_VERSION,
                                       'html_before_adduser' => $default_html_before,
                                       'html_during_adduser' => $default_html_during,
                                       'html_after_adduser' => $default_html_after,
                                       'html_before_register' => $default_html_before,
                                       'html_during_register' => $default_html_during,
                                       'html_after_register' => $default_html_after,
                                       'html_before_shortcode' => $default_html_before,
                                       'html_during_shortcode' => $default_html_during,
                                       'html_after_shortcode' => $default_html_after,
                                       'html_before_profile' => $default_html_before,
                                       'html_during_profile' => $default_html_during,
                                       'html_after_profile' => $default_html_after);
     endif;
   else:
      $umm_data['settings'] = $original_data['settings'];
   endif;
   
   if(!array_key_exists('sort_order', $original_data)):
      $umm_data['sort_order'] = get_option('umm_sort_order');
      if(!is_array($umm_data['sort_order'])):
         $sort_order = array();
           foreach($umm_data['custom_meta'] as $k => $v):
              array_push($sort_order, $k);
           endforeach;
         $umm_data['sort_order'] = $sort_order;
      endif;
   else:
      $umm_data['sort_order'] = $original_data['sort_order'];
   endif;
   
   /* 
   
   v2.2.9+ Upgrades 
   
   */
   
   if((!isset($umm_data['settings']['max_users']) || empty($umm_data['settings']['max_users'])) || (isset($umm_data['settings']['max_users']) && $umm_data['settings']['max_users'] > 100)):
    $umm_data['settings']['max_users'] = 100;
   endif;
   if(!isset($umm_data['settings']['html_before_adduser']) || empty($umm_data['settings']['html_before_adduser'])):
    $umm_data['settings']['html_before_adduser'] = $default_html_before;
   endif;
   if(!isset($umm_data['settings']['html_during_adduser']) || empty($umm_data['settings']['html_during_adduser'])):
    $umm_data['settings']['html_during_adduser'] = $default_html_during;
   endif; 
   if(!isset($umm_data['settings']['html_after_adduser']) || empty($umm_data['settings']['html_after_adduser'])):
    $umm_data['settings']['html_after_adduser'] = $default_html_after;
   endif; 
   if(!isset($umm_data['settings']['html_before_register']) || empty($umm_data['settings']['html_before_register'])):
    $umm_data['settings']['html_before_register'] = $default_html_before;
   endif; 
   if(!isset($umm_data['settings']['html_during_register']) || empty($umm_data['settings']['html_during_register'])):
    $umm_data['settings']['html_during_register'] = $default_html_during;
   endif; 
   if(!isset($umm_data['settings']['html_after_register']) || empty($umm_data['settings']['html_after_register'])):
    $umm_data['settings']['html_after_register'] = $default_html_after;
   endif; 
   if(!isset($umm_data['settings']['html_before_shortcode']) || empty($umm_data['settings']['html_before_shortcode'])):
    $umm_data['settings']['html_before_shortcode'] = $default_html_before;
   endif; 
   if(!isset($umm_data['settings']['html_during_shortcode']) || empty($umm_data['settings']['html_during_shortcode'])):
    $umm_data['settings']['html_during_shortcode'] = $default_html_during;
   endif; 
   if(!isset($umm_data['settings']['html_after_shortcode']) || empty($umm_data['settings']['html_after_shortcode'])):
    $umm_data['settings']['html_after_shortcode'] = $default_html_after;
   endif; 
   if(!isset($umm_data['settings']['html_before_profile']) || empty($umm_data['settings']['html_before_profile'])):
    $umm_data['settings']['html_before_profile'] = $default_html_before;
   endif; 
   if(!isset($umm_data['settings']['html_during_profile']) || empty($umm_data['settings']['html_during_profile'])):
    $umm_data['settings']['html_during_profile'] = $default_html_during;
   endif; 
   if(!isset($umm_data['settings']['html_after_profile']) || empty($umm_data['settings']['html_after_profile'])):
    $umm_data['settings']['html_after_profile'] = $default_html_after;
   endif;   
   
   /* 
   
   End Upgrades 
   
   */
   
   // v2.2.9 adds version number to settings array
   $umm_data['settings']['version'] = UMM_VERSION;
   update_option('user_meta_manager_data', $umm_data);
}

function umm_is_duplicate($key=false, $value=false, $user=false, $echo=false){
    global $wpdb;
    
    $key = (!$key && isset($_REQUEST['umm_key'])) ? $_REQUEST['umm_key'] : $key;
    $value = (!$value && isset($_REQUEST['umm_value'])) ? $_REQUEST['umm_value'] : $value;
    $echo = (!$echo && isset($_REQUEST['echo'])) ? $_REQUEST['echo'] : $echo;
    $data = $wpdb->get_results("SELECT * FROM " . $wpdb->usermeta . " WHERE meta_key='" . $key . "' AND meta_value='" . $value . "'");
    
    if((!empty($data) && !$user) || (!empty($data) && $data[0]->user_id != $user)):
       if($echo):
          $output = '{"is_duplicate":true, "error_message":"' . __('%s is already taken by another user. Please use a different selection.', UMM_SLUG) . '"}';
          print $output;
       else:
          return true;
       endif;
    else:
       if($echo):
          $output = '{"is_duplicate":false}';
          print $output;
       else:
          return false;
       endif;
    endif;
    exit; 
}

function umm_is_pro(){
    $pro = umm_get_option('pro');
    if(isset($pro) && $pro):
       return true;
    else:
       return false;
    endif;
}

function umm_key_exists($key=false){
    global $wpdb;
    $k = (empty($key)) ? $_REQUEST['umm_meta_key'] : $key;
    $data = $wpdb->get_results("SELECT * FROM " . $wpdb->usermeta . " WHERE meta_key='" . $k . "'");
    if(!empty($data)):
       $output = '{"key_exists":true}';
       print $output;
    else:
       $output = '{"key_exists":false}';
       print $output;
    endif;
    exit;
}

function umm_load_scripts($hook) {
    if($hook && $hook == "users_page_user-meta-manager"):
       //wp_enqueue_script('');
       wp_enqueue_script('jquery');
       wp_enqueue_script('jquery-ui-core');
       wp_enqueue_script('jquery-effects-core');
       wp_enqueue_script('jquery-ui-tabs');
       wp_enqueue_script('jquery-ui-sortable');   
       wp_enqueue_script('jquery-effects-highlight');      
       wp_register_style('umm_css', plugins_url('/css/user-meta-manager.css?version='.rand(100,1000), __FILE__));
       wp_enqueue_style('umm_css');
       wp_register_script('umm_js', plugins_url('/js/user-meta-manager.js?version='.rand(100,1000), __FILE__));
       wp_enqueue_script('umm_js');
    endif;
}

function umm_profile_field_editor($umm_edit_key=null){
    $profile_fields = umm_get_option('profile_fields');
    $options_output = '';

    if(umm_is_pro()):
    $select_option_row = '<li class="umm-select-option-row"><table><tr><td><textarea rows="1" name="umm_profile_select_label[]" placeholder="'.__('Label', UMM_SLUG).'"></textarea></td><td><textarea rows="1" name="umm_profile_select_value[]" placeholder="'.__('Value', UMM_SLUG).'"></textarea></td><td><select name="umm_profile_select_state[]" size="1"><option value="unchecked">'.__('Unchecked', UMM_SLUG).'</option><option value="checked">'.__('Checked', UMM_SLUG).'</option></select></td><td><button class="umm-add-row button-secondary umm-profile-editor umm-add-option">+</button> <button class="umm-remove-row button-secondary umm-profile-editor umm-remove-option">-</button></td></tr></table></li>
'; 
    else:
      $select_option_row = '<li class="umm-select-option-row"><table><tr><td><textarea rows="1" name="umm_profile_select_label[]" placeholder="'.__('Label', UMM_SLUG).'"></textarea></td><td><textarea rows="1" name="umm_profile_select_value[]" placeholder="'.__('Value', UMM_SLUG).'"></textarea></td><td><button class="umm-add-row button-secondary umm-profile-editor umm-add-option">+</button> <button class="umm-remove-row button-secondary umm-profile-editor umm-remove-option">-</button></td></tr></table></li>
';     
    endif;
    
    
    if(!empty($umm_edit_key) && array_key_exists($umm_edit_key, $profile_fields)):
          $value = stripslashes(htmlspecialchars_decode($profile_fields[$umm_edit_key]['value']));
          $type = $profile_fields[$umm_edit_key]['type'];
          $label = stripslashes(htmlspecialchars_decode($profile_fields[$umm_edit_key]['label']));
          $class = $profile_fields[$umm_edit_key]['class'];
          $attrs = stripslashes(htmlspecialchars_decode($profile_fields[$umm_edit_key]['attrs']));
          $after = stripslashes(htmlspecialchars_decode($profile_fields[$umm_edit_key]['after']));
          $required = $profile_fields[$umm_edit_key]['required'];          
          $allow_tags = $profile_fields[$umm_edit_key]['allow_tags'];
          $add_to_profile = $profile_fields[$umm_edit_key]['add_to_profile'];
          $unique_value = $profile_fields[$umm_edit_key]['unique'];
          $options = (!is_array($profile_fields[$umm_edit_key]['options'])) ? array() : $profile_fields[$umm_edit_key]['options'];
          $allow_multi = $profile_fields[$umm_edit_key]['allow_multi'];
          $size = (!isset($profile_fields[$umm_edit_key]['size']) || empty($profile_fields[$umm_edit_key]['size'])) ? '1' : $profile_fields[$umm_edit_key]['size'];
          $roles = (!isset($profile_fields[$umm_edit_key]['roles']) || !is_array($profile_fields[$umm_edit_key]['roles'])) ? array() : $profile_fields[$umm_edit_key]['roles'];
          $random_string_length = (isset($profile_fields[$umm_edit_key]['random_string_length'])) ? $profile_fields[$umm_edit_key]['random_string_length'] : 10;
          $random_string_type = (isset($profile_fields[$umm_edit_key]['random_string_type'])) ? $profile_fields[$umm_edit_key]['random_string_type'] : 'mixed';
          $x = 1;          
          foreach($options as $option):
            $hide_button = ($x == 1) ? ' hidden' : '';
            if(!empty($option['label'])):
            
            if(umm_is_pro()):
      $options_output .= '<li class="umm-select-option-row"><table><tr><td><textarea rows="1" name="umm_profile_select_label[]" placeholder="'.__('Label', UMM_SLUG).'">' . stripslashes($option['label']) . '</textarea></td><td><textarea rows="1" name="umm_profile_select_value[]" placeholder="'.__('Value', UMM_SLUG).'">' . stripslashes(htmlspecialchars_decode($option['value'])) . '</textarea></td><td><select name="umm_profile_select_state[]" size="1">
      <option value="unchecked"';
    if($option['state'] == 'unchecked' || $option['state'] == '') $options_output .= ' selected="selected"';
    $options_output .= '>'.__('Unchecked', UMM_SLUG).'</option>
	<option value="checked"';
    if($option['state'] == 'checked') $options_output .= ' selected="selected"';
    $options_output .= '>'.__('Checked', UMM_SLUG).'</option>';
    $options_output .= '</select></td><td><button class="umm-add-row button-secondary umm-profile-editor umm-add-option">+</button></td><td><button class="umm-remove-row button-secondary umm-profile-editor umm-remove-option' . $hide_button . '">-</button></td></tr></table></li>
';
    else:
      $options_output .= '<li class="umm-select-option-row"><table><tr><td><textarea rows="1" name="umm_profile_select_label[]" placeholder="'.__('Label', UMM_SLUG).'">' . stripslashes($option['label']) . '</textarea></td><td><textarea rows="1" name="umm_profile_select_value[]" placeholder="'.__('Value', UMM_SLUG).'">' . stripslashes(htmlspecialchars_decode($option['value'])) . '</textarea></td><td><button class="umm-add-row button-secondary umm-profile-editor umm-add-option">+</button></td><td><button class="umm-remove-row button-secondary umm-profile-editor umm-remove-option' . $hide_button . '">-</button></td></tr></table></li>
';    
    endif;
                      
          endif; //!empty($option['label'])
          $x++;
          endforeach;
          
          if(empty($options_output)):
            $options_output .= $select_option_row;
          endif;
                          
        else:
        $options_output .= $select_option_row;
    endif;
    $type = (!isset($type) || empty($type)) ? '' : $type;
    $label = (!isset($label) || empty($label)) ? '' : $label;
    $attrs = (!isset($attrs) || empty($attrs)) ? '' : $attrs;
    $after = (!isset($after) || empty($after)) ? '' : $after;
    $allow_tags = (!isset($allow_tags) || empty($allow_tags)) ? 'yes' : $allow_tags;
    $unique_value = (!isset($unique_value) || empty($unique_value)) ? 'no' : $unique_value;
    $allow_multi = (!isset($allow_multi) || empty($allow_multi)) ? 'no' : $allow_multi;
    $size = (!isset($size) || empty($size)) ? '' : $size;
    $required = (!isset($required) || empty($required)) ? '' : $required;
    $add_to_profile = (!isset($add_to_profile) || empty($add_to_profile)) ? '' : $add_to_profile;
    $class = (!isset($class) || empty($class)) ? '' : $class;
    $roles = (!isset($roles) || empty($roles) || !is_array($roles)) ? array() : $roles;
    $random_string_length = (!isset($random_string_length)) ? 10 : $random_string_length;
    $random_string_type = (!isset($random_string_type) || empty($random_string_type)) ? 'mixed' : $random_string_type;
    $output = '<div class="umm-profile-field-editor">
    <strong>'.__('Field <a title="W3Schools HTML5 Input Types Reference Page" href="http://www.w3schools.com/html/html5_form_input_types.asp" target="_blank">Type</a>', UMM_SLUG).' :</strong><br /><select class="umm-profile-field-type" size="1" name="umm_profile_field_type">
    <option value="" title="'.__('Do not add to user profile.', UMM_SLUG).'"';
    if($type == '') $output .= ' selected="selected"';
    $output .= '>'.__('None', UMM_SLUG).'</option>
	<option value="text"';
    if($type == 'text') $output .= ' selected="selected"';
    $output .= '>'.__('Text', UMM_SLUG).'</option>
	<option value="color"';
    if($type == 'color') $output .= ' selected="selected"';
    $output .= '>'.__('Color', UMM_SLUG).'</option>
    <option value="date"';
    if($type == 'date') $output .= ' selected="selected"';
    $output .= '>'.__('Date', UMM_SLUG).'</option>
    <option value="datetime"';
    if($type == 'datetime') $output .= ' selected="selected"';
    $output .= '>'.__('Date-Time', UMM_SLUG).'</option>
    <option value="datetime-local"';
    if($type == 'datetime-local') $output .= ' selected="selected"';
    $output .= '>'.__('Date-Time-Local', UMM_SLUG).'</option>
    <option value="email"';
    if($type == 'email') $output .= ' selected="selected"';
    $output .= '>'.__('Email', UMM_SLUG).'</option>
    <option value="month"';
    if($type == 'month') $output .= ' selected="selected"';
    $output .= '>'.__('Month', UMM_SLUG).'</option>
    <option value="number"';
    if($type == 'number') $output .= ' selected="selected"';
    $output .= '>'.__('Number', UMM_SLUG).'</option>
    <option value="range"';
    if($type == 'range') $output .= ' selected="selected"';
    $output .= '>'.__('Range', UMM_SLUG).'</option>
    <option value="search"';
    if($type == 'search') $output .= ' selected="selected"';
    $output .= '>'.__('Search', UMM_SLUG).'</option>
    <option value="tel"';
    if($type == 'tel') $output .= ' selected="selected"';
    $output .= '>'.__('Telephone', UMM_SLUG).'</option>
    <option value="time"';
    if($type == 'time') $output .= ' selected="selected"';
    $output .= '>'.__('Time', UMM_SLUG).'</option>
    <option value="url"';
    if($type == 'url') $output .= ' selected="selected"';
    $output .= '>'.__('URL', UMM_SLUG).'</option>
    <option value="week"';
    if($type == 'week') $output .= ' selected="selected"';
    $output .= '>'.__('Week', UMM_SLUG).'</option>
    <option value="textarea"';
    if($type == 'textarea') $output .= ' selected="selected"';
    $output .= '>'.__('Textarea', UMM_SLUG).'</option>
    <option value="checkbox"';
    if($type == 'checkbox') $output .= ' selected="selected"';
    $output .= '>'.__('Checkbox', UMM_SLUG).'</option>
    <option value="checkbox_group"';
    if($type == 'checkbox_group') $output .= ' selected="selected"';
    $output .= '>'.__('Checkbox Group', UMM_SLUG).'</option>
    <option value="radio"';
    if($type == 'radio') $output .= ' selected="selected"';
    $output .= '>'.__('Radio Button Group', UMM_SLUG).'</option>
    <option value="select"';
    if($type == 'select') $output .= ' selected="selected"';
    $output .= '>'.__('Select Menu', UMM_SLUG).'</option>';
    
    if(umm_is_pro()):
       if(function_exists('umm_pro_field_types_options')):
          $output .= umm_pro_field_types_options($type);        
       endif; 
    endif;
    
    $output .= '</select>';
    
    $hidden = (empty($type)) ? ' hidden' : '';
    
    $output .= '<div class="umm-input-options' . $hidden . ' umm-profile-field-options">
    <h3>'.__('Settings', UMM_SLUG).'</h3>';
    $hidden = ($type == 'random_string' && umm_is_pro()) ? '' : ' hidden';
    $output .= '<div class="umm-random-string-options' . $hidden . '">';
    $output .= '<strong>'.__('Length', UMM_SLUG).':</strong><br />
    <input type="number" name="umm_random_string_length" value="' . $random_string_length . '" />
    <br />
    <strong>'.__('Type', UMM_SLUG).':</strong><br />
    <select size="1" name="umm_random_string_type">
    <option value=""></option>
    <option value="mixed"';
    if($random_string_type == 'mixed' || $random_string_type == '') $output .= ' selected="selected"';
    $output .= '>'.__('Mixed', UMM_SLUG).'</option>
	<option value="numbers"';
    if($random_string_type == 'numbers') $output .= ' selected="selected"';
    $output .= '>'.__('Numbers Only', UMM_SLUG).'</option>
	<option value="letters"';
    if($random_string_type == 'letters') $output .= ' selected="selected"';
    $output .= '>'.__('Letters Only', UMM_SLUG).'</option>
    <option value="all"';
    if($random_string_type == 'all') $output .= ' selected="selected"';
    $output .= '>'.__('All Characters', UMM_SLUG).'</option>
    </select>
    <br />';
    $output .= '</div>';
    $hidden = ($type == 'random_string' && umm_is_pro()) ? ' hidden' : '';
    $output .= '<div class="umm-input-options-sub' . $hidden . '">';
    $output .= '<strong>'.__('Label', UMM_SLUG).':</strong><br />
    <textarea rows="3" cols="40" name="umm_profile_field_label"  placeholder="">' . $label . '</textarea>
    <br />
    <strong>'.__('Classes', UMM_SLUG).':</strong><br />
    <textarea rows="3" cols="40" name="umm_profile_field_class"  placeholder="">' . $class . '</textarea>
    <br />
    <strong>'.__('Additional Attributes', UMM_SLUG).':</strong><br />
    <textarea rows="3" cols="40" name="umm_profile_field_attrs" type="text" placeholder="'.__('Example', UMM_SLUG).': min=&quot;1&quot; max=&quot;5&quot; title=&quot;'.__('My Title', UMM_SLUG).'&quot; placeholder=&quot;'.__('My Text', UMM_SLUG).'&quot">' . $attrs . '</textarea>
    <br />
    <strong>'.__('HTML After', UMM_SLUG).':</strong><br />
    <textarea rows="3" cols="40" name="umm_profile_field_after" placeholder="">' . $after . '</textarea>
    <br />';  
    $output .= '<strong>'.__('Required', UMM_SLUG).':</strong> <select size="1" name="umm_profile_field_required" title="' . __('Make this a required field. Not recommended for checkbox groups.', UMM_SLUG) . '">
	<option value="no"';
    if($required == 'no' || $required == '') $output .= ' selected="selected"';
    $output .= '>'.__('No', UMM_SLUG).'</option>
	<option value="yes"';
    if($required == 'yes') $output .= ' selected="selected"';
    $output .= '>'.__('Yes', UMM_SLUG).'</option>
    </select><br />
    <strong>'.__('Allow Tags', UMM_SLUG).':</strong> <select size="1" name="umm_allow_tags" title="' . __('Allow users to input HTML tags. (Not recommended)', UMM_SLUG) . '">
	<option value="no"';
    if($allow_tags == 'no' || $allow_tags == '') $output .= ' selected="selected"';
    $output .= '>'.__('No', UMM_SLUG).'</option>
    <option value="yes"';
    if($allow_tags == 'yes') $output .= ' selected="selected"';
    $output .= '>'.__('Yes', UMM_SLUG).'</option> 	
    </select><br />
    <strong>'.__('Add To Profile', UMM_SLUG).':</strong> <select size="1" name="umm_add_to_profile" title="' . __('Add this field to the user profile and registration forms.', UMM_SLUG) . '">
	<option value="yes"';
    if($add_to_profile == 'yes' || $add_to_profile == '') $output .= ' selected="selected"';
    $output .= '>'.__('Yes', UMM_SLUG).'</option>
    <option value="no"';
    if($add_to_profile == 'no') $output .= ' selected="selected"';
    $output .= '>'.__('No', UMM_SLUG).'</option>	
    </select><br />
    <strong>'.__('Unique Value', UMM_SLUG).':</strong> <select size="1" name="umm_unique_value" title="' . __('Make the value for this field unique to each user. No duplicate entries will be allowed. Only works for future submissions.', UMM_SLUG) . '">
	<option value="no"';
    if($unique_value == 'no' || $unique_value == '') $output .= ' selected="selected"';
    $output .= '>'.__('No', UMM_SLUG).'</option>
    <option value="yes"';
    if($unique_value == 'yes') $output .= ' selected="selected"';
    $output .= '>'.__('Yes', UMM_SLUG).'</option>	
    </select><br />
    <strong style="vertical-align:top;">'.__('Roles', UMM_SLUG).':</strong> <select size="3" name="umm_roles[]" title="' . __('User roles allowed to view this field.', UMM_SLUG) . '" multiple="multiple">
	<option value="all"';
    if(in_array('all', $roles) || empty($roles)) $output .= ' selected="selected"';
    $output .= '>'.__('All', UMM_SLUG).'</option>';
    foreach (get_editable_roles() as $role_name => $role_info):
       $output .= '<option value="' . $role_name . '"';
    if(in_array($role_name, $roles)) $output .= ' selected="selected"';
    $output .= '>' . ucwords($role_name) . '</option>';
    endforeach;
    $output .= '<option value="visitor"';
    if(in_array('visitor', $roles)) $output .= ' selected="selected"';
    $output .= '>'.__('Visitor/Register', UMM_SLUG).'</option>
    </select>';
    if(umm_is_pro()):
       if(function_exists('umm_pro_profile_editor_fields')):
          $edit_key = (isset($profile_fields[$umm_edit_key])) ? $profile_fields[$umm_edit_key] : false;
          $output .= umm_pro_profile_editor_fields($edit_key);
       endif; 
    endif; 
    $output .= '</div>';  
    
    
    $hidden = ($type == 'select' || $type == 'radio' || $type == 'checkbox_group') ? '' : ' hidden';
    
    $multi_hidden = ($type == 'select') ? '' : ' hidden';
    
    $output .= '
    <div class="umm-select-multi-options' . $multi_hidden . ' umm-profile-field-options">
    <strong>'.__('Allow Multiple Selections', UMM_SLUG).':</strong> <select size="1" name="umm_allow_multi" title="' . __('Allow the user to select multiple items.', UMM_SLUG) . '">
	<option value="yes"';
    if($allow_multi == 'yes') $output .= ' selected="selected"';
    $output .= '>'.__('Yes', UMM_SLUG).'</option>
    <option value="no"';
    if($allow_multi == 'no' || $allow_multi == '') $output .= ' selected="selected"';
    $output .= '>'.__('No', UMM_SLUG).'</option>	
    </select><br />
    <strong>'.__('Size', UMM_SLUG).':</strong> <input title="' . __('The number of options to display for multi-select menus.', UMM_SLUG) . '" type="number" name="umm_multi_size" min="1" value="' . $size . '">
    </div>
    
    <div class="umm-select-options' . $hidden . ' umm-profile-field-options">
    <h3>'.__('Options', UMM_SLUG).'</h3>
    <ul class="umm-select-options-table">
<li>';
    if(umm_is_pro()):
       $output .= '<table style="width: 300px;"><tr><th>Label</th><th>Value</th><th>Initial State</th><th></th></tr></table>';
    else:
      $output .= '<table style="width: 300px;"><tr><th>Label</th><th>Value</th><th></th></tr></table>';      
    endif;


$output .= '</li>

';
$output .= $options_output;

if(umm_is_pro()):
      $output .= '</ul>
<ul class="umm-select-options-clone hidden">
 <li class="umm-select-option-row"><table><tr><td><textarea rows="1" name="umm_profile_select_label[]" placeholder="'.__('Label', UMM_SLUG).'"></textarea></td><td><textarea rows="1" name="umm_profile_select_value[]" placeholder="'.__('Value', UMM_SLUG).'"></textarea></td><td><select name="umm_profile_select_state[]" size="1"><option value="unchecked">'.__('Unchecked', UMM_SLUG).'</option><option value="checked">'.__('Checked', UMM_SLUG).'</option></select></td><td><button class="umm-add-row button-secondary umm-profile-editor">+</button></td><td><button class="umm-remove-row button-secondary umm-profile-editor">-</button></td></tr></table></li>
</ul>
    </div>
    </div>'; 
    else:
      $output .= '</ul>
<ul class="umm-select-options-clone hidden">
 <li class="umm-select-option-row"><table><tr><td><textarea rows="1" name="umm_profile_select_label[]" placeholder="'.__('Label', UMM_SLUG).'"></textarea></td><td><textarea rows="1" name="umm_profile_select_value[]" placeholder="'.__('Value', UMM_SLUG).'"></textarea></td><td><button class="umm-add-row button-secondary umm-profile-editor">+</button></td><td><button class="umm-remove-row button-secondary umm-profile-editor">-</button></td></tr></table></li>
</ul>
    </div>
    </div>';     
    endif;
   $output .= '</div><!-- sub -->';   
    return $output;
}

function umm_query_shortcode($atts, $content){
    global $wpdb;
    $query = (!isset($atts['query'])) ? false : $atts['query'];
    $query_type = (!isset($atts['type'])) ? 'select' : $atts['type'];
    $where = (!isset($atts['where'])) ? ' WHERE users.ID=usermeta.user_id' : ' WHERE users.ID=usermeta.user_id and ' . $atts['where'];
    $before_result = (!isset($atts['before_result'])) ? '<section class="umm-query-result">' : $atts['before_result'];
    $after_result = (!isset($atts['after_result'])) ? '</section>' : $atts['after_result'];
    $before_item = (!isset($atts['before_item'])) ? '<ul class="umm-query-user">' : $atts['before_item'];
    $after_item = (!isset($atts['after_item'])) ? '</ul>' : $atts['after_item'];
    $item = (!isset($atts['item'])) ? '<li class="umm-query-item"><strong>%k:</strong> %v</li>' : $atts['item'];
    $key_format = (!isset($atts['key_format'])) ? 'ucwords' : $atts['key_format'];
    $value_format = (!isset($atts['value_format'])) ? 'none' : $atts['value_format'];
    $list = (!isset($atts['list'])) ? false : explode(',', $atts['list']);
    $labels = (!isset($atts['labels'])) ? '' : explode(',', $atts['labels']);
    $output = '';
    $error = false;
    
    if(!$query):
       $query = "SELECT usermeta.*, users.* FROM " . $wpdb->usermeta . " usermeta, " . $wpdb->users . " users" . $where;
       $data = $wpdb->get_results($query);
             
    else:
       
       switch ($query_type):
          case 'insert':
          $data = $wpdb->insert($query);         
          break;
          
          case 'update':
          $data = $wpdb->update($query);
          break;
          
          case 'delete':
          $data = $wpdb->delete($query);
          break;
          
          default:
          // select
          $data = $wpdb->get_results($wpdb->prepare($query));
       endswitch;
        
    endif;
    
    if($data === FALSE):
       $error = true;
    endif;
    
    if($error):
       $output = __('An error occurred. The query failed.', UMM_SLUG);
    else:
       if(empty($data) || !$data || !is_array($data)):
          $output = __('Query successful. No results to display. ', UMM_SLUG) . $query;
       else:
          $output .= $before_result;
          foreach($data as $d):
             $output .= $before_item;
             $x = 0;
             
             if(!$list):
                $list = array();
                foreach($d as $key => $value):
                   array_push($list, $key);
                endforeach;
             endif;
             foreach($list as $key):
                $k = trim($key);
                $formatted_key = umm_format_text(str_replace('_', ' ', $k), $key_format);
                $value = maybe_unserialize($d->$k);
                $val = (is_array($value)) ? implode(", ", $value) : $value;
                $formatted_value = umm_format_text(stripslashes($val), $value_format);
                $i = str_replace('%v', $formatted_value, $item);
                if(isset($labels[$x]) && !empty($labels[$x])):
                   $output .= str_replace('%k', $labels[$x], $i);
                else:
                   $output .= str_replace('%k', $formatted_key, $i);
                endif;
                $x++;
             endforeach;
             $output .= $after_item;
          endforeach;
       $output .= $after_result;
       endif;
    endif; // $error
    
    $content = do_shortcode($output);
    
    return $content;
}

function umm_random_str($number_of_digits = 1, $type = 3){
    // $type: 1 - numeric, 2 - letters, 3 - mixed, 4 - all ascii chars.
    $num = "";
    $r = 0;
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

function umm_reset(){
    global $wpdb;
    $umm_options = umm_get_option();
    $profile_fields = $umm_options['profile_fields'];
    $umm_data = $umm_options['custom_meta'];       
    foreach($umm_data as $meta_key):
       $user_data = $wpdb->get_results("SELECT * FROM " . $wpdb->users);
       foreach($user_data as $user):
          delete_user_meta($user->ID, $meta_key);
       endforeach;
    endforeach;
    umm_update_option('profile_fields', array());
    umm_update_option('custom_meta', array());
    $output = "<p>" . __("User Meta Manager data successfully reset.", UMM_SLUG) . "</p>";
    print $output;
    exit;
}

function umm_restore(){
    global $wpdb, $table_prefix;
    $wpdb->query("DELETE FROM " . $wpdb->usermeta);
    $wpdb->query("INSERT INTO " . $wpdb->usermeta . "  SELECT * FROM " . $table_prefix . "umm_usermeta_backup");
    $back_button = umm_button("umm_backup_page&umm_user=1", __('<< Back', UMM_SLUG), "umm-back-button");
    $output = $back_button . '<p class="umm-message">' . __("User meta data successfully restored.", UMM_SLUG) . "</p>";
    print $output;
    exit;
}

function umm_restore_confirm(){
    $budate = umm_get_option('backup_date');
    if($budate == ""): 
      $output = __('No backup data to restore!', UMM_SLUG);
    else:
      $back_button = umm_button("umm_backup_page&umm_user=1", __('<< Back', UMM_SLUG), "umm-back-button");
      $output = $back_button . '<p class="umm-warning"><strong>' . __('Restore all user meta data to the backup version?', UMM_SLUG) . '</strong><br /><a href="#" data-subpage="' . UMM_AJAX . 'umm_restore&umm_user=1" title="' . __('Restore', UMM_SLUG) . '" class="umm-subpage">' . __('Yes', UMM_SLUG) . '</a> <a href="#" data-subpage="' . UMM_AJAX . 'umm_backup_page&umm_user=1" title="' . __('Cancel', UMM_SLUG) . '" class="umm-subpage">' . __('Cancel', UMM_SLUG) . '</a></p>';
    endif;
    print $output;
    exit;
}

function umm_show_profile_fields($echo=true, $fields=false, $mode='profile', $form_id=false, $debug=false){
   global $current_user;
   $umm_options = umm_get_option();
   $umm_settings = $umm_options['settings'];
    $umm_data = $umm_options['custom_meta'];
    $profile_fields = $umm_options['profile_fields'];
    $sort_order = $umm_options['sort_order'];
    if(empty($sort_order) || !is_array($sort_order)) $sort_order = false;
    /* If this is a short code, $fields string is to be converted to an array. */
    $show_fields = ($fields) ?  explode(",", str_replace(", ", ",", $fields)) : false;
    //if($debug) print_r($profile_fields);
    if(!empty($profile_fields)):
    $output = "";
    /* Sort the profile fields */
    if($sort_order):
      $new_array = array();
      foreach($sort_order as $profile_field_name):
      if($debug) print_r($profile_field_name);
      if(isset($profile_fields[$profile_field_name]))
       $new_array[$profile_field_name] = $profile_fields[$profile_field_name];
      endforeach;
      $profile_fields = $new_array;   
    endif;
    /* If this is a short code reduce the array to only the fields which should be displayed */
    if($show_fields):
      $new_array = array();
      foreach($show_fields as $profile_field_name):
      if(isset($profile_fields[$profile_field_name]))
       $new_array[$profile_field_name] = $profile_fields[$profile_field_name];
      endforeach;
      $profile_fields = $new_array;   
    endif;
    
    $form_tag = (!$form_id) ? '' : ' form="' . $form_id . '"';
    
    $html_before = (!isset($umm_settings['html_before_' . $mode]) || empty($umm_settings['html_before_' . $mode])) ? '<h3 class="umm-custom-fields">[section-title]</h3>
<table class="form-table umm-custom-fields">
   <tbody>' : stripslashes(htmlspecialchars_decode($umm_settings['html_before_' . $mode]));
    $output .= $html_before;
 
    $current_user = wp_get_current_user();
    
    foreach($profile_fields as $profile_field_name => $profile_field_settings):
    if($profile_field_settings['type'] == 'random_string'):
       continue;
    endif;
    $user_can_view = false;
    if(isset($profile_field_settings['roles']) && is_array($profile_field_settings['roles'])):
       foreach($profile_field_settings['roles'] as $role):
          if((is_array($current_user->caps) && array_key_exists($role, $current_user->caps)) || (empty($current_user->caps) && $role == 'visitor') || $role == 'all'):
            $user_can_view = true; 
          endif;
       endforeach;    
    else:
       $user_can_view = true;
    endif;
    if((!$show_fields && $profile_field_settings['add_to_profile'] == 'yes' && $user_can_view) || ($show_fields && array_key_exists($profile_field_name, $umm_data) && $user_can_view)):
      $default_value = stripslashes(htmlspecialchars_decode($profile_field_settings['value']));
      $the_user = ((isset($_REQUEST['user_id']) && !empty($_REQUEST['user_id'])) && current_user_can('add_users')) ? $_REQUEST['user_id'] : $current_user->ID;
      $existing_value = get_user_meta($the_user, $profile_field_name, true);
      $value = (!is_array($existing_value)) ? stripslashes(htmlspecialchars_decode($existing_value)) : $existing_value;
      if($mode == 'register' || $mode == 'adduser') $value = $default_value;
      
      $label = stripslashes(htmlspecialchars_decode($profile_field_settings['label']));
      $unique = ($profile_field_settings['unique'] == 'yes') ? ' umm-unique' : '';
      $field_html = '';
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
            $field_html .= '<input type="' . $profile_field_settings['type'] . '" name="' . $profile_field_name;
            if($mode == 'adduser') $field_html .= '[]';
            $field_html .= '" value="' . $value . '" class="' . stripslashes(htmlspecialchars_decode($profile_field_settings['class'])) . $unique . '"';
            if($profile_field_settings['required'] == 'yes')
            $field_html .= ' required="required"';
            if(!empty($profile_field_settings['attrs']))
            $field_html .= ' ' . stripslashes(htmlspecialchars_decode($profile_field_settings['attrs']));
            $field_html .= $form_tag . " />";
            break;
            
            case 'textarea':
            $field_html .= '<textarea name="' . $profile_field_name;
            if($mode == 'adduser') $field_html .= '[]';
            $field_html .= '" class="' . stripslashes(htmlspecialchars_decode($profile_field_settings['class'])) . $unique . '"';
            if($profile_field_settings['required'] == 'yes')
            $field_html .= ' required="required"';
            if(!empty($profile_field_settings['attrs']))
            $field_html .= ' ' . stripslashes(htmlspecialchars_decode($profile_field_settings['attrs']));
            $field_html .= $form_tag . '>' . $value . '</textarea>' . "\n";
            break;
            
            case 'checkbox':                    
            $field_html .= '<input type="checkbox" name="' . $profile_field_name;
            if($mode == 'adduser') $field_html .= '[]';
            $field_html .= '" value="' . $profile_field_settings['value'] . '" class="' . stripslashes(htmlspecialchars_decode($profile_field_settings['class'])) . $unique . '"';
            if($profile_field_settings['required'] == 'yes')
              $field_html .= ' required="required"';
              
            if((($mode == 'register' || $mode == 'adduser') && (isset($profile_field_settings['initial_state']) && $profile_field_settings['initial_state'] == 'checked')) || ($value == $profile_field_settings['value'] && ($mode != 'register' && $mode != 'adduser'))):
              $field_html .= ' checked="checked" data-mode="' . $existing_value . '"';
            endif;
            if(!empty($profile_field_settings['attrs']))
              $field_html .= ' ' . stripslashes(htmlspecialchars_decode($profile_field_settings['attrs']));
            $field_html .= $form_tag . ' />' . "\n";
            break;
            
            case 'checkbox_group':
            $x = 0;
            foreach($profile_field_settings['options'] as $option => $option_settings):
            if(!empty($option_settings['label'])):
            $field_html .= '<span class="umm-checkbox-group-item"><input type="checkbox" name="' . $profile_field_name;
            $field_html .= '[]" value="' . $option_settings['value'] . '" class="' . stripslashes(htmlspecialchars_decode($profile_field_settings['class'])) . $unique . '"';
            if($profile_field_settings['required'] == 'yes')
              $field_html .= ' required="required"';
            if((is_array($value) && in_array($option_settings['value'], $value)) || (($mode == 'register' || $mode == 'adduser') && ($option_settings['state'] == 'checked')))
              $field_html .= ' checked="checked"';
            if(!empty($profile_field_settings['attrs']))
              $field_html .= ' ' . stripslashes(htmlspecialchars_decode($profile_field_settings['attrs']));
            $field_html .= $form_tag . ' />' . stripslashes($option_settings['label']) . "</span> \n";
            endif;
            $x++;
            endforeach;            
            break; 
            
            case 'radio':
            $i = 1;
            foreach($profile_field_settings['options'] as $option => $option_settings):
              if(!empty($option_settings['label'])):
              $field_html .= '<input id="umm_radio_' . $i . '" type="' . $profile_field_settings['type'] . '" name="' . $profile_field_name;
              
              $field_html .= '" value="' . $option_settings['value'] . '" class="' . stripslashes(htmlspecialchars_decode($profile_field_settings['class'])) . $unique . '"';
              if($profile_field_settings['required'] == 'yes')
              $field_html .= ' required="required"';
              if((isset($option_settings['value']) && $option_settings['value'] == $value) || (($mode == 'register' || $mode == 'adduser') && (isset($option_settings['state']) && $option_settings['state'] == 'checked')))
              $field_html .= ' checked="checked"';
              if(!empty($profile_field_settings['attrs']))
              $field_html .= ' ' . stripslashes(htmlspecialchars_decode($profile_field_settings['attrs']));
              $field_html .= $form_tag . ' /><span class="' . str_replace(" ", "-", strtolower($profile_field_name)) . '">' . $option_settings['label'] . '</span> ';
              endif;
              $i++;
            endforeach; 
            break;
            
            case 'select':
            $multi = ((isset($profile_field_settings['allow_multi']) && $profile_field_settings['allow_multi'] == 'yes') || ($mode == 'adduser')) ? '[]' : '';
            $multiple = (isset($profile_field_settings['allow_multi']) && $profile_field_settings['allow_multi'] == 'yes') ? ' multiple="multiple"' : '';
            $size = (!isset($profile_field_settings['size']) || $profile_field_settings['size'] < 1) ? ' size="1"' : ' size="' . $profile_field_settings['size'] . '"';
            $field_html .= '<select name="' . $profile_field_name . $multi . '" class="' . stripslashes(htmlspecialchars_decode($profile_field_settings['class'])) . $unique . '"';
            if($profile_field_settings['required'] == 'yes')
            $field_html .= ' required="required"';
            if(!empty($profile_field_settings['attrs']))
            $field_html .= ' ' . stripslashes(htmlspecialchars_decode($profile_field_settings['attrs']));
            $field_html .= $multiple . $size . $form_tag . '>' . "\n";
            foreach($profile_field_settings['options'] as $option => $option_settings):
            if(!empty($option_settings['label'])):
            $field_html .= '<option value="' . stripslashes($option_settings['value']) . '"';
              if((!is_array($value) && $option_settings['value'] == $value) || (is_array($value) && in_array($option_settings['value'], $value)) || (($mode == 'register' || $mode == 'adduser') && ($option_settings['state'] == 'checked'))) $field_html .= ' selected="selected"';
            $field_html .= '>'.stripslashes($option_settings['label']).'</option>
            ';
            endif;
            endforeach; 
            $field_html .= "</select>\n";           
            break;
            
            default:
            $field_html .= '<input type="text" name="' . $profile_field_name;
            if($mode == 'adduser') $field_html .= '[]';
            $field_html .= '" value="' . $value . '" class="' . stripslashes(htmlspecialchars_decode($profile_field_settings['class'])) . $unique . '"';
            if($profile_field_settings['required'] == 'yes')
            $field_html .= ' required="required"';
            if(!empty($profile_field_settings['attrs']))
            $field_html .= ' ' . stripslashes(htmlspecialchars_decode($profile_field_settings['attrs']));
            $field_html .= $form_tag . ' />';
        }
    
    if(!empty($profile_field_settings['after'])):
       $field_html .= stripslashes(htmlspecialchars_decode($profile_field_settings['after']));
    endif;
    
    $html_during = (!isset($umm_settings['html_during_' . $mode]) || empty($umm_settings['html_during_' . $mode])) ? '<tr><th>[label]</th><td>[field]</td></tr>' : $umm_settings['html_during_' . $mode];
    
    $html_during = str_replace('[label]', $label, stripslashes(htmlspecialchars_decode($html_during)));
    $html_during = str_replace('[field]', $field_html, $html_during);
    $html_during = str_replace('[field-name]', $profile_field_name, $html_during);
    $html_during = str_replace('[field-slug]', str_replace("_", "-", strtolower($profile_field_name)), $html_during);
    if(umm_is_pro() && isset($profile_field_settings['display']) && is_array($profile_field_settings['display']) && in_array('profile', $profile_field_settings['display']) && $mode == 'profile'):
       $output .= $html_during;
    elseif(umm_is_pro() && isset($profile_field_settings['display']) && is_array($profile_field_settings['display']) && in_array('register', $profile_field_settings['display']) && $mode == 'register'):
       $output .= $html_during;
    elseif($mode == 'adduser'):
       $output .= $html_during;
    elseif(!umm_is_pro() || (umm_is_pro() && !isset($profile_field_settings['display']) || $mode == 'shortcode')):
       $output .= $html_during;
    endif;

    endif; // $show_fields
    endforeach;
    $html_after = (!isset($umm_settings['html_after_' . $mode]) || empty($umm_settings['html_after_' . $mode])) ? '</tbody>
</table>' : stripslashes(htmlspecialchars_decode($umm_settings['html_after_' . $mode]));
    $umm_nonce = wp_create_nonce('umm_wp_nonce');
    $output .= $html_after . "\n" . '<input type="hidden" name="umm_nonce" value="' . $umm_nonce . '"' . $form_tag . ' />' . "\n"; 
    endif; // !empty($profile_fields)

    if(isset($output) && !empty($output)):
       $section_title = (isset($umm_settings['section_title']) && !empty($umm_settings['section_title'])) ? $umm_settings['section_title'] : ''; 
       $output = str_replace('[section-title]', stripslashes(htmlspecialchars_decode($umm_settings['section_title'])), $output);
       
       if($echo):
          echo  $output;
       else:
          return $output;
       endif;
       
    endif;   
}

function umm_shortcode($atts, $content){
    // This function can handle all of the shortcodes
    global $current_user;
    if(is_array($atts)):
        $redirect = (isset($atts['redirect']) && !empty($atts['redirect'])) ? $atts['redirect'] : home_url($_SERVER['REQUEST_URI']);
        $loading = (isset($atts['loading']) && !empty($atts['loading'])) ? $atts['loading'] : __('Loading Profile ...', UMM_SLUG);
        $profile_link = (isset($atts['link']) && !empty($atts['link'])) ? $atts['link'] : home_url('/wp-admin/profile.php');
        $login_form_args = array(
            'echo' => false,
            'redirect' => $redirect,
            'form_id' => 'loginform',
            'label_username' => __( 'Username' ),
            'label_password' => __( 'Password' ),
            'label_remember' => __( 'Remember Me' ),
            'label_log_in' => __( 'Log In' ),
            'id_username'  => 'user_login',
            'id_password'  => 'user_pass',
            'id_remember' => 'rememberme',
            'id_submit' => 'wp-submit',
            'remember' => true,
            'value_username' => NULL,
            'value_remember' => false
            );
        if(in_array('profile', $atts)):
        if($current_user->ID > 0):
            // Use jQuery to load the profile form
            wp_enqueue_script('jquery');
            $content .= '
            <!-- User Meta Manager -->
            <div class="umm-userprofile" style="display:none"></div>
            <div class="umm-userprofile-loading">' . $loading . '</div>';
            $content .= '<script type="text/javascript">
	jQuery(function($){
	   var fetch_profile = function(ele, page, obj, callback){
	       $(ele).load(page + \' \' + obj, callback);
       },
       activate_form = function(){
           ';
           if(isset($atts['hide'])):
            $content .= '$("' . $atts['hide'] . '").hide();';
           endif;
           $content .= '
	       $(document).on("submit", "form#your-profile", function(event){
	           event.preventDefault();
               var original_val = $("form#your-profile input#submit").val();
               $("form#your-profile input#submit").prop("disabled", true).val("' . __('Please Wait...', UMM_SLUG) . '");
               $.post("' . $profile_link . '", $("form#your-profile").serialize(), function(data){
                   var success = $(data).find("div.updated"),
                   error = $(data).find("div.error");                  
                   if(error.html()){
                       $("p.submit").after(error);
                       $("form#your-profile input#submit").val(original_val).prop("disabled", false);
                       $("body").find("div.error").delay(5000).hide("slow", function(){});
                   } else if(success.html()){
                       $("p.submit").after(success);
                       $("form#your-profile input#submit").val(original_val).prop("disabled", false);
                       $("body").find("div.updated").delay(5000).hide("slow", function(){});
                   } else {
                    try{ console.log("' . __('An error occurred. ', UMM_SLUG) . '"); }catch(e){}
                   }
                });
            });
            $("div.umm-userprofile-loading").fadeOut("slow");
            $("div.umm-userprofile").fadeIn("slow");
            $(".hidden").hide();
       };
       fetch_profile("div.umm-userprofile", "' . $profile_link . '", "form#your-profile", activate_form);
	});
</script>
<!-- /User Meta Manager -->
';
            $content = do_shortcode($content);
    
            return $content;  
        else:
            if(isset($atts['bounce']) && !empty($atts['bounce'])):
               // Redirect with JS?
               $content .= '<script type="text/javascript">
            location.href="' . $atts['bounce'] . '";
            </script>';
            endif;
            
            $content .= wp_login_form($login_form_args);
            $content = do_shortcode($content);
            return $content;
        endif;
        endif;
        
        if(in_array('access', $atts)):
            return umm_useraccess_shortcode($atts, $content);
        endif;
        
        if(in_array('meta', $atts)):
            return umm_usermeta_shortcode($atts, $content);
        endif;
        
        if(in_array('query', $atts)):
            return umm_query_shortcode($atts, $content);
        endif;
        
        if(in_array('loginout', $atts)):
            $content .= wp_loginout($redirect, false);
            $content = do_shortcode($content);
            return $content; 
        endif;
        
        if(in_array('login', $atts)):            
            $content .= wp_login_form($login_form_args);
            $content = do_shortcode($content);
            return $content;  
        endif;
    else:
        return $content;
    endif;
    
}

function umm_shortcode_builder(){
    $fields = umm_get_profile_fields('select');
       $output = "<table class='umm-shortcode-builder widefat umm-rounded-corners'>
<tr class='alternate'>
	<td><strong>" . __('CSS Classes', UMM_SLUG) . "</strong><br /><input type='text' data-for='class' placeholder='my-class1 my-class2' /><br /><span>" . __('(Optional) A CSS class, or classes, to add to the form.', UMM_SLUG) . "</span></td>
</tr>
<tr>
	<td><strong>" . __('Submit Button Label', UMM_SLUG) . "</strong><br /><input type='text' data-for='submit' placeholder='" . __('Submit', UMM_SLUG) . "' /><br /><span>" . __('(Optional) A label for the submit button.', UMM_SLUG) . "</span></td>
</tr>
<tr class='alternate'>
	<td><strong>" . __('Success Message', UMM_SLUG) . "</strong><br /><input type='text' data-for='success' placeholder='" . __('Submission successful!', UMM_SLUG) . "' /><br /><span>" . __('(Optional) A message to display if the form submission is successful.', UMM_SLUG) . "</span></td>
</tr>
<tr>
	<td><strong>" . __('Error Message', UMM_SLUG) . "</strong><br /><input type='text' data-for='error' placeholder='" . __('Submission failed!', UMM_SLUG) . "' /><br /><span>" . __('(Optional) A message to display if the form submission fails.', UMM_SLUG) . "</span></td>
</tr>
<tr class='alternate'>
	<td><strong>" . __('Fields', UMM_SLUG) . "</strong><input type='hidden' data-for='fields' /><div class='umm-shortcode-builder-fields'>" . $fields . " <input type='button' value=' + ' class='button-secondary umm-shortcode-builder-fields-add' /></div><span>" . __("(Required) A list of meta keys to display. Each meta key will be displayed as it's set <em>Field Type</em> in the order in which you list them.", UMM_SLUG) . "</span></td>
</tr>
<tr>
	<td><strong>" . __('Additional Variables', UMM_SLUG) . "</strong><input type='hidden' data-for='vars' />
    <div class='umm-shortcode-builder-vars'>" . __('Key', UMM_SLUG) . " <input type='text' data-for='key'  /> " . __('Value', UMM_SLUG) . " <input type='text' data-for='value'  /> <input type='button' value=' + ' class='umm-shortcode-builder-vars-add button-secondary' /></div><span>" . __('(Optional) Extra variable/value pairs you wish to pass with the form submission. Each pair will be converted to a hidden form field, and will be added to the form.', UMM_SLUG) . "</span>
    </td>
</tr>
<tr class='alternate'>
	<td><strong>" . __('Email To', UMM_SLUG) . "</strong><br /><input type='text' data-for='email_to' placeholder='" . __('you@your-domain.com', UMM_SLUG) . "' /><br /><span>" . __('(Optional) An email address to send the results of the form submission to. Leave this empty to disable this feature. If you use this option, more options will appear below.', UMM_SLUG) . "</span></td>
</tr>
<tr class='umm-shortcode-builder-email-field'>
	<td><strong>" . __('Email From', UMM_SLUG) . "</strong><br /><input type='text' data-for='email_from' placeholder='" . __('do-not-reply@your-domain.com', UMM_SLUG) . "' /><br /><span>" . __('(Used with email_to) An email address as the sender.', UMM_SLUG) . "</span></td>
</tr>
<tr class='alternate umm-shortcode-builder-email-field'>
	<td><strong>" . __('Email Subject', UMM_SLUG) . "</strong><br /><input type='text' data-for='subject' placeholder='" . __('A message from your site.', UMM_SLUG) . "' /><br /><span>" . __('(Used with email_to) An email subject.', UMM_SLUG) . "</span></td>
</tr>
<tr class='umm-shortcode-builder-email-field'>
	<td><strong>" . __('Email Message', UMM_SLUG) . "</strong><br /><textarea cols='50' rows='5' data-for='message' placeholder='" . __('Greetings,\n\n%s\n\nRegards,\nWebmaster', UMM_SLUG) . "'></textarea><br /><span>" . __('(Required with email_to) A message to send in the email. \n = line break. %s = contents of the form submission. Important: You must add %s where you want the form submission results displayed in the message.', UMM_SLUG) . "</span></td>
</tr>
<tr class='umm-shortcode-builder-shortcode'>
	<td><strong>" . __('Short Code', UMM_SLUG) . "</strong><br /><textarea cols='50' rows='5' class='umm-shortcode-builder-result umm-message' onclick='this.focus();this.select();'>
</textarea><br /><span>" . __('This is the resulting short code. Copy this short code to any Post or Page to display the form. The form will update meta data for the currently logged-in user.', UMM_SLUG) . "</span></td>
</tr>
</table><div class='umm-shortcode-builder-fields-clone umm-hidden'><div class='umm-shortcode-builder-fields'>" . $fields . " <input type='button' value=' + ' class='umm-shortcode-builder-fields-add button-secondary' /> <input type='button' value=' - ' class='umm-shortcode-builder-remove button-secondary' /></div></div><div class='umm-shortcode-builder-vars-clone umm-hidden'><div class='umm-shortcode-builder-vars'>" . __('Key', UMM_SLUG) . " <input type='text' data-for='key'  /> " . __('Value', UMM_SLUG) . " <input type='text' data-for='value'  /> <input type='button' value=' + ' class='umm-shortcode-builder-vars-add button-secondary' /> <input type='button' value=' - ' class='umm-shortcode-builder-remove button-secondary' /></div></div>";
       return $output;
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
    $output = '<h3 class="umm-subpage-title">' . sprintf($text, '<a href="' . admin_url('user-edit.php?user_id=' . $user_id) . '" target="_blank"><em>' . $nickname .  '</em></a>') . '</h3>';
    return $output;
}

function umm_switch_action(){
    if(function_exists($_REQUEST['umm_sub_action']))
       call_user_func($_REQUEST['umm_sub_action']);
}

function umm_sync_user_meta(){
    global $wpdb;
    $umm_data = umm_get_option('custom_meta');
    $data = umm_get_users();
    foreach($umm_data as $meta_key => $settings):
       foreach($data as $user):
          $test_data = $wpdb->get_results("SELECT * FROM " . $wpdb->usermeta . " WHERE meta_key = '" . $meta_key . "' AND user_id = " . $user->ID . " LIMIT 1");
          if(!$test_data[0]->meta_key):
             update_user_meta($user->ID, $meta_key, $settings['value'], false);
          endif;
       endforeach;
    endforeach;
    $output = __('Meta data successfully set for all users.', UMM_SLUG);
    print $output;
    exit;
}

function umm_ui(){
    if(!current_user_can('edit_users')):
      _e('You do not have the appropriate permission to view this content.', UMM_SLUG);
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
          $output = __('Key and label are both required. Try again.', UMM_SLUG);
        else:
          if(umm_column_exists($umm_column_key)):
            $output = __('Column already exists.', UMM_SLUG);
          else:
            switch($umm_table){
                case "users":
                $users_columns = (!umm_get_option("users_columns") ? array('ID' => __('ID', UMM_SLUG), 'user_login' => __('User Login', UMM_SLUG), 'user_registered' => __('Date Registered', UMM_SLUG)) : umm_get_option("users_columns"));
                $users_columns[$umm_column_key] = $umm_column_label;
                umm_update_option("users_columns", $users_columns);
                break;
                
    
                case "usermeta":
                $usermeta_columns = (!umm_get_option("usermeta_columns")) ? array() : umm_get_option("usermeta_columns");
                $num_users_data = $wpdb->get_results("SELECT COUNT(*) AS total_users FROM " . $wpdb->users);
                $num_users = $num_users_data[0]->total_users;
                $num_users_meta = $wpdb->get_results("SELECT COUNT(*) AS total_meta_users FROM " . $wpdb->usermeta);
                $num_meta_users = $num_users_meta[0]->total_meta_users;
                // Check if all users have this meta_key, in case another plugin is managing user meta
                if($num_users != $num_meta_users):
                   // Sync - assign all users this meta key if it does not exist for all users
                   $userdata = umm_get_users();
                   foreach($userdata as $user):
                      $test = get_user_meta($user->ID, $umm_column_key);
                      if(!$test):
                         update_user_meta($user->ID, $umm_column_key, '');
                      endif;
                   endforeach;
                endif;
                
                $usermeta_columns[$umm_column_key] = $umm_column_label;
                umm_update_option("usermeta_columns", $usermeta_columns);
                break;
            }
            $output = __('Column successfully added.', UMM_SLUG);
          endif;           
        endif;
        break;
        
        case "remove_columns":
        if(empty($_REQUEST['umm_column_key'])):
          $output = __('No key was selected. Select a key to remove from the table.', UMM_SLUG);
        else:
        $users_columns = (!umm_get_option("users_columns") ? array('ID' => __('ID', UMM_SLUG), 'user_login' => __('User Login', UMM_SLUG), 'user_registered' => __('Date Registered', UMM_SLUG)) : umm_get_option("users_columns"));
        $usermeta_columns = (!umm_get_option("usermeta_columns")) ? array() : umm_get_option("usermeta_columns");
        if(array_key_exists($umm_column_key, $users_columns)):
            unset($users_columns[$umm_column_key]);
            umm_update_option("users_columns", $users_columns);
        elseif(array_key_exists($umm_column_key, $usermeta_columns)):
            unset($usermeta_columns[$umm_column_key]);
            umm_update_option("usermeta_columns", $usermeta_columns);         
        endif;
        $output = __('Column successfully removed.', UMM_SLUG);
        endif;
        break;
        
    }
    print $output;
    exit; 
}

function umm_update_custom_meta_order(){
    umm_update_option('sort_order', $_REQUEST['umm_item']);
    $new_order = umm_get_option('sort_order');
    $output = __('Order successfully updated.', UMM_SLUG);
    print $output;
    exit;
}

function umm_update_option($key, $value){
    $umm_data = umm_get_option();
    $umm_data[$key] = $value;
    if(update_option('user_meta_manager_data', $umm_data)):
       return true;
    else:
       return false;
    endif;
}

function umm_update_profile_fields($user_id=false){
    global $current_user;
    
    $saved_profile_fields = (!umm_get_option('profile_fields')) ? array() : umm_get_option('profile_fields');
    $the_user = ((isset($_REQUEST['user_id']) && !empty($_REQUEST['user_id'])) && current_user_can('add_users')) ? $_REQUEST['user_id'] : $current_user->ID;
    if($user_id) $the_user = $user_id;
    
    if(isset($_REQUEST['umm_nonce']) && wp_verify_nonce($_REQUEST['umm_nonce'], 'umm_wp_nonce')):
    
       foreach($saved_profile_fields as $field_name => $field_settings):
       
         // Check if user role matches
         if(umm_user_can_edit_field($field_settings) && $field_settings['add_to_profile'] == 'yes'):
       
         if((isset($_REQUEST[$field_name]) && is_array($_REQUEST[$field_name])) && ($field_settings['type'] == 'checkbox_group' || ($field_settings['type'] == 'select' && (isset($field_settings['allow_multi']) && $field_settings['allow_multi'] == 'yes')))):
            // This is a checkbox group array or multi-select menu
            $field_value = array();
            $x = 0;
            foreach($field_settings['options'] as $option => $option_settings):
               $field_value[$x] = (isset($_REQUEST[$field_name][$x])) ? $_REQUEST[$field_name][$x] : '';
               $x++;           
            endforeach;          
         else:
            // If this is post'd from the Add User screen the value will always be an array.
            $f_value = (isset($_REQUEST[$field_name])) ? $_REQUEST[$field_name] : '';
            $_val = (is_array($f_value)) ? $f_value[0] : $f_value;
            $field_value = htmlspecialchars(trim($_val));
            if(!$field_settings['allow_tags']) $field_value = wp_strip_all_tags($field_value); 
         endif;      
             
         update_user_meta($the_user, $field_name, $field_value);
         
         endif; // $user_can_edit_field
       endforeach;
    endif;
}

function umm_update_profile_fields_settings($meta_key, $meta_value){
    $saved_profile_fields = umm_get_option('profile_fields'); // Array of profile fields settings
    if(empty($saved_profile_fields)) $saved_profile_fields = array(); // Backwards compatibility
    $options = array(); // Array for holding options for select menus or radio button groups
    if(isset($_POST['umm_profile_field_type']) && !empty($_POST['umm_profile_field_type'])):
       // This is a profile field
       if(!empty($_POST['umm_profile_select_label']) && ($_POST['umm_profile_field_type'] == 'select' || $_POST['umm_profile_field_type'] == 'radio' || $_POST['umm_profile_field_type'] == 'checkbox_group')):
          // Assemble options for select menus or radio button groups
           $x = 0;
           foreach($_POST['umm_profile_select_label'] as $option_label):
             if($option_label != ''):
                $options[$x] = array('label' => $option_label, 'value' => $_POST['umm_profile_select_value'][$x], 'state' => $_POST['umm_profile_select_state'][$x]);
                $x++;
             endif; 
           endforeach;
       endif;
       // Assemble profile field settings into an array 
       $_profile_field_data = array('value' => $meta_value,
                                       'unique' => $_POST['umm_unique_value'],
                                       'type' => $_POST['umm_profile_field_type'],
                                       'label' => htmlspecialchars($_POST['umm_profile_field_label']),
                                       'class' => $_POST['umm_profile_field_class'],
                                       'attrs' => htmlspecialchars($_POST['umm_profile_field_attrs']),
                                       'after' => htmlspecialchars($_POST['umm_profile_field_after']) ,
                                       'required' => $_POST['umm_profile_field_required'],
                                       'allow_tags' => $_POST['umm_allow_tags'],
                                       'add_to_profile' => $_POST['umm_add_to_profile'],
                                       'allow_multi' => $_POST['umm_allow_multi'],
                                       'size' => $_POST['umm_multi_size'],
                                       'roles' => $_POST['umm_roles'],
                                       // TODO: 'category' => $_POST['umm_category'],
                                       'options' => $options);
       if(umm_is_pro()):
            if(function_exists('umm_pro_profile_field_data')):
               $new_profile_field_data = umm_pro_profile_field_data($_profile_field_data);
            endif; 
       else:
          $new_profile_field_data = $_profile_field_data;
       endif;                                                  
       // add or update field
       $saved_profile_fields[$meta_key] = $new_profile_field_data;
       umm_update_option('profile_fields', $saved_profile_fields);
    elseif(array_key_exists($meta_key, $saved_profile_fields)):
       // Remove an existing profile field
       unset($saved_profile_fields[$meta_key]);
       umm_update_option('profile_fields', $saved_profile_fields);
    endif;            
}

function umm_update_settings(){
    if(umm_is_pro()):
       if(function_exists('umm_pro_update_settings')):
          $output = umm_pro_update_settings($_POST);
       endif;     
    else:
       umm_update_option('settings', $_POST);
       $output = __('Settings successfully saved.', UMM_SLUG);
    endif;
    
    print $output;
    exit; 
}

function umm_update_user_meta(){
    global $wpdb;    
    $mode = $_POST['mode'];
    $all_users = (isset($_POST['all_users']) && !empty($_POST['all_users'])) ? true : false;
    $field_type = (isset($_POST['umm_profile_field_type']) && !empty($_POST['umm_profile_field_type'])) ? $_POST['umm_profile_field_type'] : false;
    $u = (isset($_REQUEST['umm_user']) && !empty($_REQUEST['umm_user'])) ? $_REQUEST['umm_user'] : 'all';
    $meta_value = !empty($_POST['umm_meta_value']) ? $_POST['umm_meta_value'] : array();
    $meta_key = (!empty($_POST['umm_meta_key'])) ? $_POST['umm_meta_key'] : array();
    $meta_key_exists = false;
    $output = "";
    $umm_settings = umm_get_option('settings');
    $duplicate_override = (!isset($umm_settings['duplicate_check_override']) || empty($umm_settings['duplicate_check_override'])) ? 'no' : $umm_settings['duplicate_check_override'];
    $umm_data = umm_get_option('custom_meta'); // Array of custom meta data for all users
    $umm_singles_data = umm_get_option('singles_data'); // Array of custom key names for single users
    $umm_singles_data = (empty($umm_singles_data) || !is_array($umm_singles_data)) ? array() : $umm_singles_data; // Backwards compatibility
    $random_string_length = (isset($_POST['umm_random_string_length']) && $_POST['umm_random_string_length'] > 0) ? $_POST['umm_random_string_length'] : 10;
    $random_string_type = (isset($_POST['umm_random_string_type']) && $_POST['umm_random_string_type'] != '') ? $_POST['umm_random_string_type'] : 'mixed';
    if($random_string_type == 'numbers'):
       $random_string_type = 1;
    elseif($random_string_type == 'letters'):
       $random_string_type = 2;
    elseif($random_string_type == 'all'):
       $random_string_type = 4;
    else:
       $random_string_type = 3;
    endif;
    $sort_order = umm_get_option('sort_order');
    if(!is_array($sort_order)) $sort_order = array(); // Backwards compatibility
    if(empty($sort_order) || count($sort_order) < count($umm_data)):
        foreach($umm_data as $k => $v):
           array_push($sort_order, $k);
        endforeach;
    endif;
        
    switch($mode){       
        case "add":
        if(!empty($meta_key)):
        foreach($meta_key as $key):
        // Check if key already exists
           $data = umm_usermeta_data('WHERE meta_key="' . $key . '"');
           if(count($data) > 0):
             $meta_key_exists = true;
           endif;              
        endforeach;
        if($_REQUEST['mode'] == 'add'):
        if($meta_key_exists && $duplicate_override == 'no'):
           // Key already exists
           $output = '<span class="umm-error-message">' . __('Error: Meta key already existed. Choose a different name.', UMM_SLUG) . '</span>';
        else: // Key doesn't exist
           switch($u){
            case "all":
               $val = ($all_users) ?  maybe_serialize(trim($meta_value[0])): '';
               // Insert new key for all users and add new profile field if needed
               $data = umm_get_users();
               foreach($data as $user):
                  // Don't overwrite any existing meta data if $duplicate_override is on.
                  $exists = get_user_meta($user->ID, $meta_key[0], true);
                  if(!$exists):
                     if(umm_is_pro() && $field_type == 'random_string'):
                        $val = umm_random_str($random_string_length, $random_string_type);
                     endif;
                     update_user_meta($user->ID, $meta_key[0], $val, false);
                  endif;
               endforeach;
               // Add new meta data to custom meta array
               $umm_data[$meta_key[0]] = $meta_value[0];
               umm_update_option('custom_meta', $umm_data);
               // Update profile field settings if needed
               umm_update_profile_fields_settings($meta_key[0], $meta_value[0]);
               // Update sort order
               array_push($sort_order, $meta_key[0]);
               umm_update_option('sort_order', $sort_order);
            break;
            
            default:
               // Don't overwrite any existing meta data if $duplicate_override is on.
               $exists = get_user_meta($u, $meta_key[0], true);
               if(!$exists):
                  // Insert key for single user - no profile field settings here
                  update_user_meta($u, $meta_key[0], maybe_unserialize(trim(stripslashes($meta_value[0]))), false);
               endif;
               // Add key to singles array
               if(!in_array($meta_key[0], $umm_singles_data)):
                  array_push($umm_singles_data, $meta_key[0]);
               endif;
            break;            
           }
           $output = __('Meta data successfully added.', UMM_SLUG);                           
        endif; // !Key exists
        endif; // $_REQUEST['mode'] == 'add'
        else:
           // No meta key sent
           $output = '<span class="umm-error-message">' . __('Error: No meta key entered.', UMM_SLUG) . '</span>';
        endif; 
        break;
        
        case "edit":
           if(!empty($meta_key)):
           switch($u){
            case "all":
               // Update custom meta
               $profile_fields = umm_get_option('profile_fields');
               
               $field_settings = $profile_fields[$meta_key[0]];
               
               if(($field_settings['unique'] == 'yes' && $all_users) || ($field_settings['unique'] == 'yes' && umm_is_duplicate($meta_key[0], $meta_value[0]))):
                  $output = '<span class="umm-error-message">' . __('Error: This field is set as unique. Change the field settings first or enter a different value.', UMM_SLUG) . '</span>';
               else:
                  if($all_users):                                  
                     // Update value for all users
                     $data = umm_get_users();
                     foreach($data as $user):
                        update_user_meta($user->ID, $meta_key[0], maybe_unserialize(trim(stripslashes($meta_value[0]))), false);
                     endforeach;
                  endif;
                  // Update existing value
                  $umm_data[$meta_key[0]] = $meta_value[0];
                  umm_update_option('custom_meta', $umm_data);
                  // Update profile field settings if needed
                  umm_update_profile_fields_settings($meta_key[0], $meta_value[0]);
                  $output = __('Meta data successfully updated.', UMM_SLUG);
               endif;
            break;
            
            default: 
               // Update meta for single user
               $profile_fields = umm_get_option('profile_fields');
               $error = false;
               $x = 0;
               foreach($_POST['umm_meta_key'] as $meta_key):
                  if((isset($profile_fields[$meta_key]['unique']) && $profile_fields[$meta_key]['unique'] == 'yes') && (isset($meta_value[$x]) && umm_is_duplicate($meta_key, $meta_value[$x], $u))):
                     $error = true;
                  endif;
                  $x++;
               endforeach;
               if($error):
                  $output = '<span class="umm-error-message">' . sprintf(__('Error: %s is set as unique. The value you entered is already in use. Change the field settings or change the value.', UMM_SLUG), $meta_key) . '</span>';
               else:
                  $x = 0;
                  foreach($_POST['umm_meta_key'] as $meta_key):
                     if(empty($umm_singles_data)):
                        $umm_singles_data = array($meta_key);
                     else:
                        if(!in_array($meta_key, $umm_singles_data)):
                           array_push($umm_singles_data, $meta_key);
                        endif;
                     endif;
                     umm_update_option('singles_data', $umm_singles_data);
                     update_user_meta($u, $meta_key, maybe_unserialize(trim(stripslashes($meta_value[$x]))), false);
                     $x++;
                  endforeach;
                  $output = __('Meta data successfully updated.', UMM_SLUG);
               endif;
            break;            
           }           
        else:
           // No meta key sent
           $output = '<span class="umm-error-message">' . __('Error: No meta key selected.', UMM_SLUG) . '</span>';  
        endif;
        break;

        case "delete":
        if($_POST['umm_edit_key']):
        $meta_key = $_POST['umm_edit_key'];
        $saved_profile_fields = umm_get_option('profile_fields');
        if($all_users):
            $data = umm_get_users();
            foreach($data as $user):
                delete_user_meta($user->ID, $meta_key);
            endforeach;
            unset($umm_data[$meta_key]);
            umm_update_option('custom_meta', $umm_data);
            if(array_key_exists($meta_key, $saved_profile_fields)):
            // remove field
            unset($saved_profile_fields[$meta_key]);
            umm_update_option('profile_fields', $saved_profile_fields);
            // remove custom column
            $users_columns = (!umm_get_option("users_columns") ? array('ID' => __('ID', UMM_SLUG), 'user_login' => __('User Login', UMM_SLUG), 'user_registered' => __('Date Registered', UMM_SLUG)) : umm_get_option("users_columns"));
            $usermeta_columns = (!umm_get_option("usermeta_columns")) ? array() : umm_get_option("usermeta_columns");
            if(array_key_exists($meta_key, $users_columns)):
               unset($users_columns[$meta_key]);
               umm_update_option("users_columns", $users_columns);
            elseif(array_key_exists($meta_key, $usermeta_columns)):
               unset($usermeta_columns[$meta_key]);
               umm_update_option("usermeta_columns", $usermeta_columns);
            endif; // array_key_exists
         endif; // array_key_exists            
        else: // all_users
            delete_user_meta($_POST['umm_user'], $_POST['umm_edit_key']);
        endif;
        $output = __('Meta data successfully deleted.', UMM_SLUG);
        else:
        $output =  __('Meta Key is required!', UMM_SLUG);
        endif;
        break;
    }
    print $output;
    exit;
}

function umm_useraccess_shortcode($atts, $content) {
    global $current_user;
    $access = true;
    $key = (!isset($atts['key'])) ? '' : $atts['key'];
    $value = (!isset($atts['value'])) ? '' : $atts['value'];
    $users = (isset($atts['users'])) ? explode(" ", $atts['users']) : false;
    $message = (!isset($atts['message'])) ? '' : $atts['message'];
    $json = (!isset($atts['json'])) ? false : $atts['json'];
    $redir = (!isset($atts['url'])) ? false : $atts['url'];
    if($json):
    
      $json = '[' . $json . ']';
      $access = false;
      $json = json_decode($json);
      
      foreach($json as $k => $v):
          
          if(is_object($v)):
             $v = (array) $v;
             $_k = array_keys($v);
             $_v = array_values($v);
             $v = trim($_v[0]);
             $k = $_k[0];
          endif;
          
          $meta_value = get_user_meta($current_user->ID, $k, true);
          
          if($v == $meta_value):
             $access = true;
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
        if($message && !$redir):
            $content = $message;
        elseif($redir):
           if($message):
              $content = $message;
           else:
              $content = __('You do not have sufficient permissions to access this content.', UMM_SLUG);
           endif;
           // Bounce the user with JS
           $content .= '<script type="text/javascript">window.location.href = "' . $redir . '";</script>';
        else:
            $content = __('You do not have sufficient permissions to access this content.', UMM_SLUG);
        endif;
    endif;
    
    $content = do_shortcode($content);
        
    return $content;         
}

function umm_user_can_edit_field($field_settings){
    global $current_user;
    $user_can_edit_field = false;
    if(isset($field_settings['roles']) && is_array($field_settings['roles'])):
        foreach($field_settings['roles'] as $role):
            if((is_array($current_user->caps) && array_key_exists($role, $current_user->caps)) || (empty($current_user->caps) && $role == 'visitor') || $role == 'all'):
                 $user_can_edit_field = true;
            endif;
        endforeach;
    else:
        $user_can_edit_field = true;
    endif;
    return $user_can_edit_field;
}

function umm_usermeta_data($criteria="ORDER BY umeta_id ASC"){
    global $wpdb;
    $data = $wpdb->get_results("SELECT * FROM $wpdb->usermeta " . $criteria);
    return $data;
}

function umm_usermeta_keys_menu($select=true, $optgroup=false, $include_used=false, $umm_mode=false, $class='umm-usermeta-keys', $show_all_option=true){
    global $wpdb, $db_prefix;
    $used_columns = umm_get_columns();
    $output = '';
    if($select):
      $output .= '<select name="umm_usermeta_keys" class="' . $class . '">' . "\n";
    endif;
    if($optgroup):
      $output .= '<optgroup label="' . $db_prefix . 'usermeta">' . "\n";
    endif;
    switch($umm_mode){
        case "csv":
        if($show_all_option)
        $output .= '<option value="all" selected="selected">' . __('All Keys', UMM_SLUG) . '</option>' . "\n";
        break;
        
        default:
        //$output .= '<option value=""></option>' . "\n";
        break;
        
    }  
    $data = $wpdb->get_results("SELECT DISTINCT meta_key FROM " . $wpdb->usermeta);
    foreach($data as $d):
    if(!array_key_exists($d->meta_key, $used_columns) || (array_key_exists($d->meta_key, $used_columns) && $include_used)):
       switch($umm_mode){
        case "csv":
        $output .= '<option value="' . $d->meta_key . '">' . $d->meta_key . '</option>' . "\n";
        break;
        
        default:
        $output .= '<option value="' . $d->meta_key . '|usermeta">' . $d->meta_key . '</option>' . "\n";
        break;
        
       }        
    endif;
    endforeach;
    if($optgroup):
      $output .= '</optgroup>' . "\n";
    endif;
    $output .= '</select>' . "\n";
    return $output;    
}

function umm_usermeta_shortcode($atts, $content) {
    global $current_user, $wpdb;
    $user = !empty($atts['user']) ? $atts['user'] : $current_user->ID;
    $core = array('ID', 'user_login', 'user_nicename', 'user_email', 'user_url', 'user_registered', 'display_name');
    $content = '';
    $form = (isset($_POST['umm_form'])) ? $_POST['umm_form'] : false;
    if(isset($atts['key']) && !empty($atts['key'])):
       // Display a single key
       $key = $atts['key'];
       if(in_array($key, $core)):
          $query = sprintf("SELECT * FROM " . $wpdb->users . " WHERE ID=%d", $user);
          $data = $wpdb->get_results($query);
          $c = $data[0]->$key;
       else:
          $c = get_user_meta($user, $key, true);
       endif;
       
       $content .= (is_array($c)) ? implode(', ', $c) : $c;
    
    elseif(isset($atts['keys']) && !empty($atts['keys'])):
       // Display multiple keys
       $keys = explode(',', $atts['keys']);
       foreach($keys as $key):
          $key = trim($key);
          if(in_array($key, $core)):
             $query = sprintf("SELECT * FROM " . $wpdb->users . " WHERE ID=%d", $user);
             $data = $wpdb->get_results($query);
             $meta_data = $data[0]->$key;
          else:
             $meta_data = get_user_meta($user, $key, true);
          endif;
          $v = (is_array($meta_data)) ? implode(', ', $meta_data) : $meta_data;
          $content .= stripslashes(htmlspecialchars_decode($atts['before_key'])) . $v . stripslashes(htmlspecialchars_decode($atts['after_key'])); 
       endforeach;
    elseif(isset($atts['fields']) && $current_user->ID > 0 && !empty($atts['fields'])):   
       $umm_data = umm_get_option('custom_meta');
       $umm_settings = umm_get_option('settings');
       $profile_fields = umm_get_option('profile_fields');
       $bot_field = (empty($umm_settings['bot_field'])) ? 'umm_forbots' : $umm_settings['bot_field'];
       $class = (!empty($atts['class'])) ? $atts['class'] : 'umm-usermeta-update-form';
       $submit = (!empty($atts['submit'])) ? $atts['submit'] : __('Submit', UMM_SLUG);
       $success = (!empty($atts['success'])) ? $atts['success'] : __('Update successful!', UMM_SLUG);
       $error = (!empty($atts['error'])) ? $atts['error'] : __('Authorization required!', UMM_SLUG);
       $email_to = (!empty($atts['email_to'])) ? $atts['email_to'] : false;
       $email_from = (!empty($atts['email_from'])) ? $atts['email_from'] : __('do-not-reply', UMM_SLUG) . '@' . $_SERVER["HTTP_HOST"];
       $subject = (!empty($atts['subject'])) ? $atts['subject'] : false;
       $message = (!empty($atts['message'])) ? $atts['message'] : false;
       $vars = (!empty($atts['vars'])) ? explode('&', htmlspecialchars_decode($atts['vars'])) : array();
       $show_fields = (!empty($atts['fields'])) ?  explode(",", str_replace(", ", ",", $atts['fields'])) : array();
       $form_id = 'umm_form_' . rand(100,1000);
       $content = '<form id="' . $form_id . '" action="#" method="post" class="' . $class .  '">
       <input type="hidden" name="umm_fields" value="' . $atts['fields'] . '" />
       <input type="hidden" name="umm_form" value="' . $form_id . '" />';
       
       $umm_user = md5($_SERVER["REMOTE_ADDR"].$_SERVER["HTTP_USER_AGENT"]);
       $umm_error = false;
       $output = "";
       $error = "";
    
       /*
    
       PROCESS FORM SUBMISSION 
    
       */
    
       if((isset($_POST['umm_update_usermeta']) && isset($_POST['umm_nonce'])) && ($_POST[$bot_field] == '') && (isset($_POST['umm_fields']) && $_POST['umm_fields'] == $atts['fields'])):
       if((wp_verify_nonce($_POST['umm_nonce'], 'umm_wp_nonce') && $umm_user == $_POST['umm_update_usermeta'])):
      
    /*
    
    TEST INPUT
    
    */
    
       foreach($show_fields as $field => $field_name):
          if(array_key_exists($field_name, $umm_data)):
             $posted_field = (isset($_POST[$field_name])) ? $_POST[$field_name] : '';
             $posted_value = (!is_array($posted_field)) ? htmlspecialchars(trim($posted_field)) : $posted_field;
                if($posted_value != "" && !is_array($posted_value)):
                   $val = (is_numeric($posted_value) && floor($posted_value) == $posted_value) ? sprintf("%d", $posted_value) : sprintf("%s", $posted_value);
                elseif(is_array($posted_value)):
                   // This is a checkbox group
                   $val = array();
                   foreach($posted_value  as $key => $value):
                      $save_value = (is_numeric($value) && floor($value) == $value) ? sprintf("%d", $value) : sprintf("%s", $value);
                      $val[$key] = $save_value;
                   endforeach;
                else:
                   $val = "";
                endif;
                $val = (is_array($val)) ? implode(', ', $val) : $val;
             
             
             if($profile_fields[$field_name]['unique'] == 'yes' && $val != ""):
                if(umm_is_duplicate($field_name, $val, $current_user->ID)):
                   $umm_error = true;
                   switch ($profile_fields[$field_name]['type']):
                      case 'select':
                         $type = 'select';
                      break;
                   
                      case 'textarea':
                         $type = 'textarea';
                      break;
                   
                      default:
                      $type = 'input';
                   endswitch;
                   $error .= sprintf(__('%s is already taken by another user.', UMM_SLUG), $profile_fields[$field_name]['label']) . '<script type="text/javascript"> jQuery(function($){ $("' . $type . '[name=\'' . $field_name . '\']").addClass("umm-error-field"); }); </script> ';  
                endif;
             endif;
             
             if(umm_is_pro()):
                if(function_exists('umm_pro_validate_profile_field')):              
                   $error .= umm_pro_validate_profile_field($field_name, $profile_fields[$field_name], $val, false);
                   if(!empty($error)):
                      $umm_error = true;
                   endif;
                endif;
             endif;
             
          else:
          $umm_error = true;
          $error .= __('Error: ' . $field_name . ' is not a valid custom field. The submission was terminated. ', UMM_SLUG);
          endif;
       endforeach;
    
       /*
    
       SUCCESS - PROCESS DATA
    
       */
    
       if(!$umm_error):
          foreach($show_fields as $field => $field_name):
             if(array_key_exists($field_name, $umm_data)):
                $posted_field = (isset($_POST[$field_name])) ? $_POST[$field_name] : '';
                $posted_value = (!is_array($posted_field)) ? htmlspecialchars(trim($posted_field)) : $posted_field;
                if($posted_value != "" && !is_array($posted_value)):
                   $val = (is_numeric($posted_value) && floor($posted_value) == $posted_value) ? sprintf("%d", $posted_value) : sprintf("%s", $posted_value);
                elseif(is_array($posted_value)):
                   // This is a checkbox group
                   $val = array();
                   foreach($posted_value  as $key => $value):
                      $save_value = (is_numeric($value) && floor($value) == $value) ? sprintf("%d", $value) : sprintf("%s", $value);
                      $val[$key] = $save_value;
                   endforeach;
                else:
                   $val = "";
                endif;
                $v = (is_array($val)) ? implode(', ', $val) : $val;
                $output .= $field_name . " = " . $v . "\n";
                update_user_meta($current_user->ID, $field_name, $val);
             endif;
          endforeach;
         if($email_to):
           $email_message = sprintf($message, $output);
           mail($email_to, $subject, $email_message, "From: " . $email_from . "\n" . "X-Mailer: PHP/" . phpversion());
         endif;
         if(umm_is_pro()):
            if(function_exists('umm_pro_redirect')):
               umm_pro_redirect($atts);
            endif; 
       endif;
       endif; // !$umm_error
       
    else:
    
      /* 
      
      ERROR - BAD SUBMISSION
      
      */
      
      $umm_error = true;
      $error = __('Authorization required!', UMM_SLUG);
      
    endif;
    
    /*
    
    RETURN RESULT
    
    */
    
    if($umm_error && $form == $_POST['umm_form']):
       $content .= '<div class="umm-error-message">' . $error . '</div>' . "\n";
    elseif($form == $_POST['umm_form']):
       $content .= '<div class="umm-success-message">' . $success . '</div>' . "\n";
    endif;
    
    
    endif;
    
    /*
    
    END SUBMISSION PROCESSING 
    
    */
    
    $content .= umm_show_profile_fields(false, $atts['fields'], 'shortcode', $form_id) . '
    <button type="submit" form="' . $form_id . '">' . $submit .  '</button>' . "\n";
 
    foreach($vars as $var):
      $v = explode('=', $var);
      if(!empty($v[0]))
      $content .=  '<input type="hidden" name="' . $v[0] . '" value="' . $v[1] . '" form="' . $form_id . '" />' . "\n";  
    endforeach;
    $content .=  '<input type="hidden" name="umm_update_usermeta" value="' . $umm_user . '" form="' . $form_id . '" />
    <input type="hidden" name="' . $bot_field . '" value="" form="' . $form_id . '" /></form>' . "\n";
    endif;
    
    $content = do_shortcode($content);
    
    return $content; 
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

function umm_validate_profile_fields($errors, $update, $user) {
	$saved_profile_fields = (!umm_get_option('profile_fields')) ? array() : umm_get_option('profile_fields');
    if(isset($_REQUEST['umm_nonce']) && wp_verify_nonce($_REQUEST['umm_nonce'], 'umm_wp_nonce')):
       foreach($saved_profile_fields as $field_name => $field_settings):      
         $field_value = (isset($_REQUEST[$field_name])) ? $_REQUEST[$field_name] : '';
         $field_value = (!is_array($field_value)) ? htmlspecialchars(trim($field_value)) : $field_value;
         if(!$field_settings['allow_tags']) $field_value = wp_strip_all_tags($field_value);
         if($field_settings['unique'] == 'yes' && umm_is_duplicate($field_name, $field_value, $user->ID)):
            $errors->add( $field_name, __('<strong>ERROR</strong>: <em>' . $field_settings['label'] . '</em> is already taken by another user. Please use a different selection.', UMM_SLUG) );
         endif;
         if(umm_is_pro()):
            if(function_exists('umm_pro_validate_profile_field')):
               umm_pro_validate_profile_field($field_name, $field_settings, $field_value, $errors);
            endif; 
         endif;   
       endforeach;
    endif;
    return $errors;
}

function umm_validate_registration_fields($errors, $sanitized_user_login, $user_email) {
    $saved_profile_fields = (!umm_get_option('profile_fields')) ? array() : umm_get_option('profile_fields');
    if(isset($_REQUEST['umm_nonce']) && wp_verify_nonce($_REQUEST['umm_nonce'], 'umm_wp_nonce')):
       foreach($saved_profile_fields as $field_name => $field_settings):
         $field_value = '';
         
         if(isset($_REQUEST[$field_name]) && !is_array($_REQUEST[$field_name])):
            $field_value = htmlspecialchars(trim($_REQUEST[$field_name])); 
         elseif(isset($_REQUEST[$field_name]) && is_array($_REQUEST[$field_name])):
            $field_value = $_REQUEST[$field_name];            
         endif;
                   
         if(!$field_settings['allow_tags']) $field_value = wp_strip_all_tags($field_value);
         if($field_settings['unique'] == 'yes' &&  umm_is_duplicate($field_name, $field_value)):
            $errors->add( $field_name, __('<strong>ERROR</strong>: <em>' . $field_settings['label'] . '</em> is already taken by another user. Please use a different selection.', UMM_SLUG) );
         endif;
         
         if(umm_is_pro()):
            if(function_exists('umm_pro_validate_profile_field')):
               umm_pro_validate_profile_field($field_name, $field_settings, $field_value, $errors);
            endif; 
         endif;  
       endforeach;
    endif;  
    return $errors;
}

function umm_value_contains($key, $search_for, $exact=false, $user_id=false){
    global $current_user;
    $uid = (!$user_id) ? $current_user->ID : $user_id;
    $m = get_usermeta($uid, $key);
    if($exact):
       $pos = strpos($m, $search_for);
    else:
       $pos = stripos($m, $search_for);
    endif;  
    if($pos === false):
       return false;
    else:
       return true;
    endif;
}

function umm_value_is($key, $search_for, $user_id=false){
    global $current_user;
    $uid = (!$user_id) ? $current_user->ID : $user_id;
    $m = get_usermeta($uid, $key);
    if($m == $search_for):
       return true;
    else:
       return false;
    endif;
}

// Append profile fields to the admin 'Add New User' form. 
$umm_x = explode("user-new.", $_SERVER["REQUEST_URI"]);
if(isset($umm_x[1]) && $umm_x[1] == 'php'):
   // No known action for this, so we'll use JavaScript to inject the profile fields in the form.
   add_action('in_admin_footer', 'umm_add_user_fields');
endif;

if(isset($_REQUEST['umm_output']) && $_REQUEST['umm_output'] == 'csv'):
   add_action('init', 'umm_get_csv');
endif;

add_action('admin_menu', 'umm_admin_menu');
add_action('admin_init', 'umm_admin_init');
add_action('user_register', 'umm_default_keys');
add_action('createuser', 'umm_default_keys');
add_action('register_form', 'umm_add_registration_fields');
add_action('edit_user_profile', 'umm_show_profile_fields');
add_action('show_user_profile', 'umm_show_profile_fields');
add_action('profile_update', 'umm_update_profile_fields', 10, 2);

// All ajax admin-ajax calls pipe through umm_switch_action()
add_action('wp_ajax_umm_switch_action','umm_switch_action');

add_shortcode('usermeta', 'umm_usermeta_shortcode');
add_shortcode('useraccess', 'umm_useraccess_shortcode');
add_shortcode('ummquery', 'umm_query_shortcode');
add_shortcode('umm', 'umm_shortcode');

add_filter('contextual_help', 'umm_help', 10, 3);
add_filter('registration_errors', 'umm_validate_registration_fields', 10, 3);
add_filter('user_profile_update_errors', 'umm_validate_profile_fields', 10, 3);

register_activation_hook(__FILE__, 'umm_install');
register_deactivation_hook(__FILE__, 'umm_deactivate');

?>