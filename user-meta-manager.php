<?php

/**
 * Plugin Name: User Meta Manager
 * Plugin URI: http://websitedev.biz
 * Description: Add, edit, or delete user meta data with this handy plugin. Easily restrict access or insert user meta data into posts or pages.
 * Version: 1.5.7
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

define('UMM_VERSION', '1.5.7');
define("UMM_PATH", plugin_dir_path(__FILE__) . '/');

if(!class_exists('WP_List_Table')):
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
endif;

class UMM_UI extends WP_List_Table {  

    function __construct(){
        global $status, $page;
        parent::__construct(array(
            'singular'  => __('user', 'user-meta-manager'),
            'plural'    => __('users', 'user-meta-manager'),
            'ajax'      => false
        ));

        $this->title = "User Meta Manager";
        $this->slug = "user-meta-manager";
        $this->shortname = "umm_ui";
        $this->version = UMM_VERSION;
        $this->users_columns = (!get_option("umm_users_columns") ? array('ID' => __('ID', 'user-meta-manager'), 'user_login' => __('User Login', 'user-meta-manager'), 'user_registered' => __('Date Registered', 'user-meta-manager')) : get_option("umm_users_columns"));
        $this->usermeta_columns = (!get_option("umm_usermeta_columns")) ? array() : get_option("umm_usermeta_columns");
    }

    function column_default($item, $column_name){
        return $item->$column_name;
    }
    
    function column_user_login($item){
        $actions = array(
            'edit_meta_data' => sprintf('<a href="admin-ajax.php?action=umm_edit_user_meta&width=600&height=500&u=%s" title="'.__('Edit User Meta', 'user-meta-manager').'" class="thickbox">' . __('Edit Meta Data', 'user-meta-manager') . '</a>',$item->ID),
            'add_user_meta' => sprintf('<a href="admin-ajax.php?action=umm_add_user_meta&width=600&height=500&u=%s" title="'.__('Add User Meta', 'user-meta-manager').'" class="thickbox">' . __('Add Meta Data', 'user-meta-manager') . '</a>',$item->ID),
            'delete_user_meta' => sprintf('<a href="admin-ajax.php?action=umm_delete_user_meta&width=600&height=500&u=%s" title="'.__('Delete User Meta', 'user-meta-manager').'" class="thickbox">' . __('Delete Meta Data', 'user-meta-manager') . '</a>',$item->ID)
        );

        return sprintf('%1$s %2$s',
            $item->user_login,
            $this->row_actions($actions)
        );
    }

    function get_columns(){
        $columns = array_merge($this->users_columns, $this->usermeta_columns);
        return $columns;
    }
    
    function get_search_menu(){
        $columns = $this->get_columns();
        $menu = "<select class=\"um-search-mode\" name=\"umm_search_mode\">\n";
        foreach($columns as $k => $v):
          $menu .= "<option value=\"$k\"";
          if(((!$_REQUEST['umm_search_mode'] || $_REQUEST['umm_search_mode'] == 'ID') && $k == 'ID') || $_REQUEST['umm_search_mode'] == $k):
          $menu .= " selected=\"selected\"";
          endif;
          $menu .= ">" . $v . "</option>\n"; 
        endforeach;
        $menu .= "</select>\n";
        print($menu);
    }

    function get_sortable_columns($extra_fields){
        $columns = array_merge($this->users_columns, $this->usermeta_columns);
        $sortable_columns = array();
        foreach($columns as $k => $v):
          $sortable_columns[$k] = array($k, false);
        endforeach;
        return $sortable_columns;
    }

    function get_bulk_actions() {
        $actions = array();
        return $actions;
    }

    function process_bulk_action(){
        global $wp_rewrite, $wpdb;
        if('edit_meta_data' === $this->current_action()):
            $output = "<div id=\"umm-status\" class=\"updated\">
            <input type=\"button\" class=\"umm-close-icon button-secondary\" title=\"" . __('Close', 'user-meta-manager') . "\" value=\"x\" />"; 
            $output .= "</p></div>\n";
            define("UMM_STATUS", $output);       
        endif;       
    }

    function close_icon(){
        echo '<input type="button" class="umm-close-icon button-secondary" title="' . __('Close', 'user-meta-manager') . '" value="x" />';
    }

    function prepare_items() {
        global $wpdb;
        $usermeta_fields = $this->usermeta_columns;
        $this->process_bulk_action();
        $per_page = (!$_REQUEST['per_page']) ? 10 : $_REQUEST['per_page'];
        $columns = $this->get_columns($extra_fields);
        $hidden = array();
        $sortable = $this->get_sortable_columns($extra_fields);
        $this->_column_headers = array($columns, $hidden, $sortable);
        $orderby = (!$_REQUEST['orderby']) ? 'ID' : $_REQUEST['orderby'];
        $order = (!$_REQUEST['order']) ? 'ASC' : $_REQUEST['order'];
        $search = (!$_REQUEST['s']) ? false : $_REQUEST['s'];
        $search_mode = (!$_REQUEST['umm_search_mode']) ? 'ID' : $_REQUEST['umm_search_mode'];
        define("UMM_ORDERBY", $orderby);
        define("UMM_ORDER", $order);
        $query = "SELECT * FROM $wpdb->users";       
        $data = $wpdb->get_results($query);
        $x = 0;
        if(count($this->usermeta_columns) > 0):
        foreach($data as $d):
            foreach($this->usermeta_columns as $k => $v):
              $f_data = get_user_meta($d->ID, $k, true);
              $data[$x]->$k = $f_data;
            endforeach;
            $x++;
        endforeach;
        endif;
        uasort($data, "umm_sort");        
        if($search):
            $search_results = array();
          foreach($data as $d):
          if($d->$search_mode == trim($search) || eregi($search, $d->$search_mode)):
            array_push($search_results, $d);
          endif;
          endforeach;
          $data = $search_results;
        endif;       
        $current_page = $this->get_pagenum();
        $total_items = count($data);
        $data = array_slice($data,(($current_page-1)*$per_page),$per_page);
        $this->items = $data;
        $this->set_pagination_args(array('total_items' => $total_items, 'per_page' => $per_page, 'total_pages' => ceil($total_items/$per_page)));
        return $per_page;
    }
    
    function display_module(){
      $per_page = $this->prepare_items();
    ?>
    <div class="wrap">
      <div id="icon-users" class="icon32"><br/></div>
        <h2><?php _e('User Meta Manager', 'user-meta-manager') ?></h2>
        <div class="umm-slogan"><?php _e('Manage User Meta Data', 'user-meta-manager') ?></div>
        <div class="umm-info hidden"><br />
        <input type="button" class="umm-close-info-icon button-secondary" title="<?php _e('Close', 'user-meta-manager') ?>" value="x" />
            <p><?php _e('What is <em>User Meta</em>? <em>User Meta</em> is user-specific data which is stored in the <em>wp_usermeta</em> database table. This data is stored by WordPress and various and sundry plugins, and can consist of anything from profile information to membership levels.', 'user-meta-manager') ?></p>
            <p><?php _e('This plugin gives you the tools to manage the data which is stored for each user. Not only can you manage existing meta data, but you can also create new custom meta data for each user, or for all users.', 'user-meta-manager') ?></p>

            <p><?php _e('Follow the steps below to manage user meta data.', 'user-meta-manager') ?></p>
            <ol start="1">
       <li><?php _e('Always backup your data before making changes to your website.', 'user-meta-manager') ?></li>     
	<li><?php _e('Locate from the list which User you want to work with, and place your mouse over that item. Action links will appear as your mouse moves over each user.', 'user-meta-manager') ?>
    <ol>
    <li><?php _e('<strong>Edit Meta Data:</strong> Edit existing meta data for each member.', 'user-meta-manager') ?></li>
    <li><?php _e('<strong>Add Meta Data:</strong> Add new, custom meta data for each user, or for <em>All Users</em>. If the meta data is added to <em>All Users</em>, new registrations will automatically receive the meta key and default value. Only use letters, numbers, and underscores while adding and naming new meta keys. Meta values can consist of any characters.', 'user-meta-manager') ?></li>
    <li><?php _e('<strong>Delete Meta Data:</strong> Delete individual meta keys for a single user or for <em>All Users</em>. You can select which meta data to delete from the drop menu.', 'user-meta-manager') ?></li>
    </ol>
    </li>
    <li><?php _e('<strong>Bulk Meta Data Management:</strong><p>The <em>Edit Custom Meta Data</em>, <em>Add Custom Meta Data</em>, <em>Delete Custom Meta Data</em> links, which are located at the top of the page, are for managing meta data for all users. Use those links to add, edit, or delete meta data for all users at once.</p>', 'user-meta-manager') ?></li>
    <li><?php _e('<strong>Display Table Management:</strong><p>The <em>Edit Columns</em> link, which is located at the top of the page, is for managing which table columns are displayed for each user. For example, if you want to add a particular meta key to this plugin\'s display table, this is where you would do it. The ID and User Login columns are required. Any columns you add become searchable.</p>', 'user-meta-manager') ?></li>
    <li><?php _e('<strong>Shorttags:</strong><p>Shorttags can be inserted into Posts or Pages to display user meta data or to restrict access to content.</p>
    <strong>Display data for a particular user:</strong>
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
    <pre>[useraccess json=\'{"access_level":"gold","sub_level":"silver"}\' message="You do not have permission to view this content."]Restricted content goes here.[/useraccess]</pre>
    The <em>json</em> attribute is used to define a list of meta keys and values. The list must be JSON encoded, as seen in the example above. Users with matching meta keys and values will be granted access to restricted content.<br/><br/>
    JSON formatting -
    <pre>{"meta_key":"meta_value", "meta_key":"meta_value", "meta_key":"meta_value"}</pre>
    Additionally, you could repeat the same meta key multiple times.
    <pre>json=\'{"access_level":"gold", "sub_level":"silver", "sub_level":"bronze", "sub_level":"aluminum-foil"}\'</pre>', 'user-meta-manager') ?></li>
</ol>
<br /> 
        </div>

        <div class="umm-top-links"><span class="edit_custom_meta_data"><a href="admin-ajax.php?action=umm_edit_custom_meta&amp;width=600&amp;height=500&amp;u=1" title="Edit Custom Meta" class="thickbox">Edit Custom Meta Data</a> | </span><span class="add_custom_meta"><a href="admin-ajax.php?action=umm_add_custom_meta&amp;width=600&amp;height=500&amp;u=1" title="Add Custom Meta" class="thickbox">Add Custom Meta Data</a> | </span><span class="delete_custom_meta"><a href="admin-ajax.php?action=umm_delete_custom_meta&amp;width=600&amp;height=500&amp;u=1" title="Delete Custom Meta" class="thickbox">Delete Custom Meta Data</a> | </span><span class="umm_edit_columns_link"><a href="admin-ajax.php?action=umm_edit_columns&width=600&height=500&u=%s" title="<?php _e('Edit Columns', 'user-meta-manager'); ?>" class="thickbox"><?php _e('Edit Columns', 'user-meta-manager'); ?></a></span></div>

        <div class="umm-per-page-menu hidden"><strong><?php _e('Items Per Page', 'user-meta-manager') ?>:</strong> <input type="text" id="per-page" size="4" value="<?php echo $per_page ?>" /><input class="umm-go button-secondary action" type="submit" value="<?php _e('Go', 'user-meta-manager') ?>" /></div>
       
        <?php
        if(defined("UMM_STATUS")) echo UMM_STATUS;
        ?>
        <div id="umm-left-panel" class="alignleft">
        <div class="umm-search-mode-menu hidden"> <?php $this->get_search_menu(); ?></div>
        <form id="umm-form" method="get">
        <input class="umm-mode" type="hidden" name="umm_mode" value="<?php echo $_REQUEST['umm_mode'] ?>" />
            <input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />
            <input type="hidden" name="paged" value="<?php echo $_REQUEST['paged'] ?>" />
            <input type="hidden" id="per-page-hidden" name="per_page" value="<?php echo $per_page; ?>" />
            <div id="umm-search"><?php $this->search_box(__('Search'), 'get') ?></div>
        </form>
        <form id="umm-list-table-form" method="post">
            <?php $this->display() ?>
        </form>
        <div class="umm-result-container"></div>
<code>&copy;<a href="http://JasonLau.biz" target="_blank">JasonLau.biz</a></code> <code>[<?php _e($this->title . ' Version', 'user-meta-manager') ?>: <?php echo UMM_VERSION; ?>]</code>
</div>
</div>
<?php
}

} // class UMM_UI

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

function umm_admin_menu(){
  add_submenu_page('users.php', 'User Meta Manager', 'User Meta Manager', 'publish_pages', 'user-meta-manager', 'umm_ui');
 add_action('admin_enqueue_scripts', 'umm_load_scripts');
}

function umm_admin_init(){
    if(function_exists('load_plugin_textdomain')) {
      load_plugin_textdomain( 'user-meta-manager', false, dirname( plugin_basename( __FILE__ ) ) . '/language/' );
 }
}

function umm_load_scripts($hook) {
    if($hook && $hook == "users_page_user-meta-manager"):
       wp_enqueue_script('jquery');
       // wp_enqueue_script('scriptaculous');
       // wp_enqueue_script('scriptaculous-effects');
       add_thickbox();
       wp_register_style('umm_css', plugins_url('/css/user-meta-manager.css', __FILE__));
       wp_enqueue_style('umm_css');
       wp_register_script('umm_js', plugins_url('/js/user-meta-manager.js?version='.rand(100,1000), __FILE__));
       wp_enqueue_script('umm_js');
    endif;
}

function umm_usermeta_keys_menu($select=true,$optgroup=false,$include_used=false){
    global $wpdb;
    $used_columns = umm_get_columns();
    $output = '';
    if($select):
      $output .= '<select name="umm_usermeta_keys">'."\n";
    endif;
    if($optgroup):
      $output .= '<optgroup label="wp_usermeta">'."\n";
    endif;  
    $data = $wpdb->get_results("SELECT DISTINCT meta_key FROM $wpdb->usermeta");
    foreach($data as $d):
    if(!array_key_exists($d->meta_key, $used_columns) || (array_key_exists($d->meta_key, $used_columns) && $include_used)):
        $output .= "<option value=\"".$d->meta_key ."|usermeta\">".$d->meta_key ."</option>\n";         
    endif;
    endforeach;
    if($optgroup):
      $output .= '</optgroup>'."\n";
    endif;
    $output .= "</select>\n";
    return $output;    
}

function umm_users_keys_menu($select=true,$optgroup=false,$include_used=false){
    global $wpdb;
    $used_columns = umm_get_columns();
    $output = '';
    if($select):
      $output .= '<select name="umm_users_keys">'."\n";
    endif;
    if($optgroup):
      $output .= '<optgroup label="wp_users">'."\n";
    endif;
    $data = $wpdb->get_results("SELECT * FROM $wpdb->users LIMIT 1");
    foreach($data as $k):
    $k = (array) $k;
    foreach($k as $kk => $vv):
        if(!array_key_exists($kk, $used_columns)):
        $output .= "<option value=\"".$kk ."|users\">".$kk."</option>\n";
        endif;
    endforeach;                
    endforeach;
    if($optgroup):
      $output .= '</optgroup>'."\n";
    endif;
    if($select):
      $output .= "</select>\n";
    endif;
    return $output; 
}

function umm_column_exists($key){
   $used_columns = umm_get_columns();
   return array_key_exists($key, $used_columns);
}

function umm_get_columns(){
    $users_columns = (!get_option("umm_users_columns") ? array('ID' => __('ID', 'user-meta-manager'), 'user_login' => __('User Login', 'user-meta-manager'), 'user_registered' => __('Date Registered', 'user-meta-manager')) : get_option("umm_users_columns"));
    $usermeta_columns = (!get_option("umm_usermeta_columns")) ? array() : get_option("umm_usermeta_columns");
    return array_merge($users_columns, $usermeta_columns);
}

function umm_edit_columns(){
    $columns = umm_get_columns();
    $output = '<form id="umm_manage_columns_form" method="post">
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
   <div class="umm_update_user_meta-result hidden"></div>
   <input id="umm_update_user_meta_submit" data-form="umm_manage_columns_form" data-action="umm_update_columns" data-wait="'.__('Wait...', 'user-meta-manager').'" class="button-primary" type="submit" value="'.__('Remove Selected Column', 'user-meta-manager').'" />
   <input name="mode" type="hidden" value="remove_columns" /><input name="return_page" type="hidden" value="admin-ajax.php?action=umm_edit_columns&width=600&height=500" />
   </form>
   <form id="umm_add_columns_form" method="post">
   <h3>'.__('Add A New Column', 'user-meta-manager').'</h3>
   <strong>'.__('Key', 'user-meta-manager').':</strong> <select name="umm_column_key">
   <option value="">'.__('Keys', 'user-meta-manager').'</option>';
   $output .= umm_users_keys_menu(false, true); 
   $output .= umm_usermeta_keys_menu(false, true);
   $output .= '</select><br>
   <strong>'.__('Label', 'user-meta-manager').':</strong> <input name="umm_column_label" type="text" value="" placeholder="'.__('Enter a label', 'user-meta-manager').'" title="'.__('Enter a label which will appear in the top row of the results table.', 'user-meta-manager').'" /><br />';   
   $output .= '<div class="umm_update_user_meta-result hidden"></div>
    <input id="umm_update_user_meta_submit" data-form="umm_add_columns_form" data-action="umm_update_columns" data-wait="'.__('Wait...', 'user-meta-manager').'" class="button-primary" type="submit" value="'.__('Add Column', 'user-meta-manager').'" />
    <input name="mode" type="hidden" value="add_columns" /><input name="return_page" type="hidden" value="admin-ajax.php?action=umm_edit_columns&width=600&height=500" />
    </form>  
    ';
    $output .= '<br/><hr><h3>'.__('FYI', 'user-meta-manager').'</h3><p>'.__('This form controls which columns are displayed in the results table. The list on top displays the columns which are currently in use. By selecting an item from the list, and pressing the <em>Remove Selected Column</em> button, columns can be removed from the results table, except the <em>ID</em> and <em>User Login</em> columns, which are required. Columns can be added to the results table using the bottom form. To add a column, select a <em>Key</em> from the menu, enter a <em>Label</em> for the column, and press the <em>Add Column</em> button. The new column will then be added to the results table, and will become searchable. The <em>Label</em> is displayed at the top of the column for identification purposes.').'</p>';
    print $output;
    exit;
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
        if(!$_REQUEST['umm_column_key'] || $_REQUEST['umm_column_key'] == ''):
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

function umm_edit_user_meta(){
    global $wpdb;
    $user_id = $_REQUEST['u'];
    $data = $wpdb->get_results("SELECT * FROM $wpdb->usermeta WHERE user_id = $user_id");
    $output = '<form id="umm_update_user_meta_form" method="post">
    <table class="umm_edit_table">
    <thead>
    <tr>
      <th>'.__('Key', 'user-meta-manager').'</th>
      <th>'.__('Value', 'user-meta-manager').'</th>
    </tr>
  </thead>
    ';
    
    foreach($data as $d):
        $output .= "<tr><td>".$d->meta_key ."</td><td><input name=\"meta_key[]\" type=\"hidden\" value=\"". $d->meta_key ."\" /><input name=\"meta_value[]\" type=\"text\" value=\"". htmlspecialchars($d->meta_value) ."\" size=\"40\" /></td></tr>";         
    endforeach;

    $output .= '</table>
    <div class="umm_update_user_meta-result hidden"></div>
    <input id="umm_update_user_meta_submit" data-form="umm_update_user_meta_form" data-action="umm_update_user_meta" data-wait="'.__('Wait...', 'user-meta-manager').'" class="button-primary" type="submit" value="'.__('Update', 'user-meta-manager').'" />
    <input name="mode" type="hidden" value="edit" /><input name="u" type="hidden" value="' . $user_id . '" /><input name="return_page" type="hidden" value="admin-ajax.php?action=umm_edit_user_meta&width=600&height=500&u=' . $user_id . '" />
    </form>  
    ';
    $output .= '<br/><hr><h3>'.__('FYI', 'user-meta-manager').'</h3><p>'.__('Be careful when editing items. Editing an item here will only edit the item for the selected user and not for all users. Not all users share the same meta keys.<br /><a href="admin-ajax.php?action=umm_edit_custom_meta&amp;width=600&amp;height=500&amp;u=1" title="Edit Custom Meta" class="thickbox">Edit Custom Meta Data For All Users</a>').'</p>';
    print $output;
    exit;
}

function umm_add_user_meta(){
    global $wpdb;
    $user_id = $_REQUEST['u'];
    $output = '<form id="umm_update_user_meta_form" method="post">
    <table class="umm_add_table">
    <thead>
    <tr>
      <th>'.__('Key', 'user-meta-manager').'</th>
      <th>'.__('Default Value', 'user-meta-manager').'</th>
    </tr>
  </thead>
    <tr><td><input name="meta_key" type="text" value="" placeholder="'.__('Meta Key', 'user-meta-manager').'" /></td><td><input name="meta_value" type="text" value=\'\' size="40" placeholder="'.__('Meta Default Value', 'user-meta-manager').'" /> </td></tr>
    <tr><td>'.__('All Users', 'user-meta-manager').'</td><td><select name="all_users" size="1">
	<option value="false">'.__('No', 'user-meta-manager').'</option>
	<option value="true">'.__('Yes', 'user-meta-manager').'</option>
</select></td></tr>';
    $output .= '</table>
    <div class="umm_update_user_meta-result hidden"></div>
    <input id="umm_update_user_meta_submit" data-form="umm_update_user_meta_form" data-action="umm_update_user_meta" data-wait="'.__('Wait...', 'user-meta-manager').'" class="button-primary" type="submit" value="'.__('Update', 'user-meta-manager').'" />
    <input name="mode" type="hidden" value="add" /><input name="u" type="hidden" value="' . $user_id . '" /><input name="return_page" type="hidden" value="admin-ajax.php?action=umm_add_user_meta&width=600&height=500&u=' . $user_id . '" />
    </form>  
    ';
    $output .= '<br/><hr><h3>'.__('FYI', 'user-meta-manager').'</h3><p>'.__('Insert a meta key and default value and press <em>Update</em>. Selecting <em>All Users</em> will add the meta key and default value to all users. New registrations will then receive the new meta key and default value. Otherwise, the new meta data will be applied to this user only, and can only be managed via the table actions, and not via the bulk actions.').'</p>';
    print $output;
    exit;
}

function umm_delete_user_meta(){
    global $wpdb;
    $user_id = $_REQUEST['u'];
    $data = $wpdb->get_results("SELECT * FROM $wpdb->usermeta WHERE user_id = $user_id");
    $output = '<form id="umm_update_user_meta_form" method="post">
    <strong>'.__('Meta Key', 'user-meta-manager').':</strong> <select name="meta_key" class="umm_meta_key_menu">
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
    <div class="umm_update_user_meta-result hidden"></div>
    <input id="umm_update_user_meta_submit" data-form="umm_update_user_meta_form" data-action="umm_update_user_meta" data-wait="'.__('Wait...', 'user-meta-manager').'" class="button-primary button-delete" type="submit" value="'.__('Delete', 'user-meta-manager').'" />
    <input name="mode" type="hidden" value="delete" /><input name="u" type="hidden" value="' . $user_id . '" /><input name="return_page" type="hidden" value="admin-ajax.php?action=umm_delete_user_meta&width=600&height=500&u=' . $user_id . '" />
    </form>  
    ';
    $output .= '<br/><hr><h3>'.__('FYI', 'user-meta-manager').'</h3><p>'.__('Be careful when selecting items to delete. This cannot be undone. Select <em>All Users</em> to delete the selected item from all users.').'</p>';
    print $output;
    exit;
}

function umm_edit_custom_meta(){
    global $wpdb;
    $data = get_option('user_meta_manager_data');
    $output = '<form id="umm_update_user_meta_form" method="post">
    <table class="umm_edit_table">
    <thead>
    <tr>
      <th>'.__('Key', 'user-meta-manager').'</th>
      <th>'.__('Value', 'user-meta-manager').'</th>
    </tr>
  </thead>
    ';

    if(!$data):
       $output .= "<tr><td colspan=\"2\">".__('No custom meta to display.', 'user-meta-manager')."</td></tr>"; 
    else:
        foreach($data as $key => $value):
            $output .= "<tr><td>".$key ."</td><td><input name=\"meta_key[]\" type=\"hidden\" value=\"". $key ."\" /><input name=\"meta_value[]\" type=\"text\" value=\"". htmlspecialchars($value) ."\" size=\"40\" /> [<a href=\"javascript:void(0)\" class=\"umm-remove-row\" title=\"Click if you do not want to edit this item.\">X</a>]</td></tr>";
        endforeach;
    endif;  

    $output .= '</table>
    <div class="umm_update_user_meta-result hidden"></div>
    <input id="umm_update_user_meta_submit" data-form="umm_update_user_meta_form" data-action="umm_update_user_meta" data-wait="'.__('Wait...', 'user-meta-manager').'" class="button-primary" type="submit" value="'.__('Update', 'user-meta-manager').'" />
    <input name="mode" type="hidden" value="edit" /><input name="u" type="hidden" value="all" /><input name="return_page" type="hidden" value="admin-ajax.php?action=umm_edit_custom_meta&width=600&height=500&u=' . $user_id . '" />
    </form>  
    ';
    $output .= '<br/><hr><h3>'.__('FYI', 'user-meta-manager').'</h3><p>'.__('Editing custom meta data here will edit the value for all existing users. The value you set will become the default value for all users. New registrations will receive the custom meta key and default value.', 'user-meta-manager').'</p>';
    print $output;
    exit;
}

function umm_add_custom_meta(){
    global $wpdb;
    $user_id = $_REQUEST['u'];
    $output = '<form id="umm_update_user_meta_form" method="post">
    <table class="umm_add_table">
    <thead>
    <tr>
      <th>'.__('Key', 'user-meta-manager').'</th>
      <th>'.__('Default Value', 'user-meta-manager').'</th>
    </tr>
  </thead>
    <tr><td><input name="meta_key" type="text" value="" placeholder="'.__('Meta Key', 'user-meta-manager').'" /></td><td><input name="meta_value" type="text" value=\'\' size="40" placeholder="'.__('Meta Default Value', 'user-meta-manager').'" /> </td></tr>
    ';
    $output .= '</table>
    <div class="umm_update_user_meta-result hidden"></div>
    <input id="umm_update_user_meta_submit" data-form="umm_update_user_meta_form" data-action="umm_update_user_meta" data-wait="'.__('Wait...', 'user-meta-manager').'" class="button-primary" type="submit" value="'.__('Update', 'user-meta-manager').'" />
    <input name="all_users" type="hidden" value="true" /><input name="mode" type="hidden" value="add" /><input name="u" type="hidden" value="all" /><input name="return_page" type="hidden" value="admin-ajax.php?action=umm_add_custom_meta&width=600&height=500&u=0" />
    </form>  
    ';
    $output .= '<br/><hr><h3>'.__('FYI', 'user-meta-manager').'</h3><p>'.__('Adding custom meta data will add the meta key and value to all existing users. The value you set will become the default value for all users. New registrations will receive the custom meta key and default value.', 'user-meta-manager').'</p>';
    print $output;
    exit;
}

function umm_delete_custom_meta(){
    global $wpdb;
    $data = get_option('user_meta_manager_data');
    $output = '<form id="umm_update_user_meta_form" method="post">
    <strong>'.__('Meta Key', 'user-meta-manager').':</strong> <select name="meta_key" class="umm_meta_key_menu">
    <option value="">'.__('Select A Meta Key', 'user-meta-manager').'</option>
    ';

    if($data):
       foreach($data as $key => $value):
        $output .= "<option value=\"".$key ."\">".$key ."</option>";
       endforeach; 
    endif;   

    $output .= '</select><br />
    <div class="umm_update_user_meta-result hidden"></div>
    <input id="umm_update_user_meta_submit" data-form="umm_update_user_meta_form" data-action="umm_update_user_meta" data-wait="'.__('Wait...', 'user-meta-manager').'" class="button-primary button-delete" type="submit" value="'.__('Delete', 'user-meta-manager').'" />
    <input name="all_users" type="hidden" value="true" /><input name="mode" type="hidden" value="delete" /><input name="u" type="hidden" value="all" /><input name="return_page" type="hidden" value="admin-ajax.php?action=umm_delete_custom_meta&width=600&height=500&u=0" />
    </form>  
    ';
    $output .= '<br/><hr><h3>'.__('FYI', 'user-meta-manager').'</h3><p>'.__('Deleting custom meta data here will delete the meta key and value for all existing users. New registrations will no longer receive the custom meta key and default value.', 'user-meta-manager').'</p>';
    print $output;
    exit;
}

function umm_update_user_meta(){
    global $wpdb;
    $all_users = ($_POST['all_users'] == "true") ? true : false;
    $umm_data = get_option('user_meta_manager_data');

    switch($_POST['mode']){
        
        case "add":
        if($all_users):
            $data = $wpdb->get_results("SELECT * FROM $wpdb->users");
            foreach($data as $user):
                update_user_meta($user->ID, $_POST['meta_key'], $_POST['meta_value'], false);
            endforeach;
            $umm_data[$_POST['meta_key']] = $_POST['meta_value'];
            update_option('user_meta_manager_data', $umm_data);
        else:
            update_user_meta($_POST['u'], $_POST['meta_key'], $_POST['meta_value'], false);
        endif;
        $output = __('Meta data successfully added.', 'user-meta-manager');
        break;

        case "edit":
        $x = 0;
        if($_POST['u'] == 'all'):
            $data = $wpdb->get_results("SELECT * FROM $wpdb->users");
            foreach($data as $user):
               foreach($_POST['meta_key'] as $key):
                update_user_meta($user->ID, $key, maybe_unserialize(trim(stripslashes($_POST['meta_value'][$x]))));
                $x++;
               endforeach;
               $x = 0; 
            endforeach;

            foreach($_POST['meta_key'] as $key):
                $umm_data[$key] = $_POST['meta_value'][$x];
                $x++;
            endforeach;
            update_option('user_meta_manager_data', $umm_data);
        else:
            foreach($_POST['meta_key'] as $key):
                update_user_meta($_POST['u'], $key, maybe_unserialize(trim(stripslashes($_POST['meta_value'][$x]))));
                $x++;
            endforeach;
        endif;
        $output = __('Meta data successfully updated.', 'user-meta-manager');
        break;

        case "delete":
        if($all_users):
            $data = $wpdb->get_results("SELECT * FROM $wpdb->users");
            foreach($data as $user):
                delete_user_meta($user->ID, $_POST['meta_key']);
            endforeach;
            $ud = array();
            if(is_array($umm_data)):               
                foreach($umm_data as $key => $value):
                    if($key != $_POST['meta_key']) $ud[$key] = $value;
                endforeach;                
            endif;
            update_option('user_meta_manager_data', $ud);
        else:
            delete_user_meta($_POST['u'], $_POST['meta_key']);
        endif;
        $output = __('Meta data successfully deleted.', 'user-meta-manager');
        break;
    }
    print $output;
    exit;
}

function umm_ui(){
    if(!current_user_can('edit_users')):
    _e("You do not have the appropriate permission to view this content.", "user-meta-manager");
    else:
    $_UMM_UI = new UMM_UI();
    $_UMM_UI->display_module();
    endif;
}

function umm_usermeta_shorttag($atts, $content) {
    global $current_user;
    $key = $atts['key'];
    $user = ($atts['user']) ? $atts['user'] : $current_user->ID;
    
    if($key):
    $content = get_user_meta($user, $key, true);
    return $content; 
    endif;         
}

function umm_useraccess_shorttag($atts, $content) {
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

function umm_default_keys(){
    global $wpdb;
    $data = $wpdb->get_results("SELECT * FROM $wpdb->usermeta ORDER BY user_id DESC LIMIT 1");
    $umm_data = get_option('user_meta_manager_data');
    if($umm_data):
        foreach($umm_data as $key => $value):
            update_user_meta($data[0]->user_id, $key, $value, false);
        endforeach;
    endif;
}

function umm_install(){
   add_option('user_meta_manager_data', '');
   update_option('umm_users_columns', array('ID' => __('ID', 'user-meta-manager'), 'user_login' => __('User Login', 'user-meta-manager'), 'user_registered' => __('Date Registered', 'user-meta-manager')));
   update_option('umm_usermeta_columns', array());
}

function umm_deactivate(){
    // Preserve data
    // delete_option('user_meta_manager_data');
    // delete_option('umm_users_columns');
    // delete_option('umm_usermeta_columns');
}

add_action('admin_menu', 'umm_admin_menu');
add_action('admin_init', 'umm_admin_init');
add_action('wp_ajax_umm_edit_user_meta','umm_edit_user_meta');
add_action('wp_ajax_umm_add_user_meta','umm_add_user_meta');
add_action('wp_ajax_umm_delete_user_meta','umm_delete_user_meta');
add_action('wp_ajax_umm_edit_custom_meta','umm_edit_custom_meta');
add_action('wp_ajax_umm_add_custom_meta','umm_add_custom_meta');
add_action('wp_ajax_umm_delete_custom_meta','umm_delete_custom_meta');
add_action('wp_ajax_umm_update_user_meta','umm_update_user_meta');
add_action('wp_ajax_umm_edit_columns','umm_edit_columns');
add_action('wp_ajax_umm_update_columns','umm_update_columns');
add_shortcode('usermeta', 'umm_usermeta_shorttag');
add_shortcode('useraccess', 'umm_useraccess_shorttag');
add_action('user_register', 'umm_default_keys');
register_activation_hook(__FILE__, 'umm_install');
register_deactivation_hook(__FILE__, 'umm_deactivate');

?>