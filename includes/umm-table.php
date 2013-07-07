<?php

/**
 * @author Jason Lau
 * @copyright 2013
 * @package user-meta-manager
 */

if(!defined("UMM_PATH")) die();
    
    if(!class_exists('WP_List_Table')):
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
endif;

class UMM_UI extends WP_List_Table {  

    function __construct(){
        global $status, $page;
        parent::__construct(array(
            'singular'  => __('umm-user', 'user-meta-manager'),
            'plural'    => __('umm-users', 'user-meta-manager'),
            'ajax'      => false
        ));

        $this->title = "User Meta Manager";
        $this->slug = "user-meta-manager";
        $this->shortname = "umm_ui";
        $this->version = UMM_VERSION;
        $this->users_columns = (!umm_get_option("users_columns") ? array('ID' => __('ID', 'user-meta-manager'), 'user_login' => __('User Login', 'user-meta-manager'), 'user_registered' => __('Date Registered', 'user-meta-manager')) : umm_get_option("users_columns"));
        $this->usermeta_columns = (!umm_get_option("usermeta_columns")) ? array() : umm_get_option("usermeta_columns");
    }

    function column_default($item, $column_name){
        return $item->$column_name;
    }
    
    function column_user_login($item){
        $actions = array(
        'add_meta_data' => sprintf('<a href="#" data-subpage="admin-ajax.php?action=umm_switch_action&amp;umm_sub_action=umm_add_user_meta&u=%s" title="'.__('Edit User Meta', 'user-meta-manager').'" class="umm-subpage umm-table-link">' . __('Add Meta', 'user-meta-manager') . '</a>',$item->ID),
            'edit_meta_data' => sprintf('<a href="#" data-subpage="admin-ajax.php?action=umm_switch_action&amp;umm_sub_action=umm_edit_user_meta&u=%s" title="'.__('Edit User Meta', 'user-meta-manager').'" class="umm-subpage umm-table-link">' . __('Edit Meta', 'user-meta-manager') . '</a>',$item->ID),
            'delete_user_meta' => sprintf('<a href="#" data-subpage="admin-ajax.php?action=umm_switch_action&amp;umm_sub_action=umm_delete_user_meta&u=%s" title="'.__('Delete User Meta', 'user-meta-manager').'" class="umm-subpage umm-table-link">' . __('Delete Meta', 'user-meta-manager') . '</a>',$item->ID)
        );

        return sprintf('%1$s %2$s',
            '<a title="' . __('Edit', 'user-meta-manager') . ' ' . $item->user_login . '" href="user-edit.php?user_id=' . $item->ID . '" target="_blank">' . $item->user_login . '</a>',
            $this->row_actions($actions)
        );
    }

    function get_columns(){
        $columns = array_merge($this->users_columns, $this->usermeta_columns);
        return $columns;
    }
    
    function get_search_menu(){
        $search_mode = (!isset($_REQUEST['umm_search_mode']) || empty($_REQUEST['umm_search_mode'])) ? 'ID' :  $_REQUEST['umm_search_mode'];     
        $columns = $this->get_columns();
        $menu = "<select class=\"umm-search-mode\" name=\"umm_search_mode\">\n";
        foreach($columns as $k => $v):
          $menu .= "<option value=\"$k\"";
          if(($search_mode == 'ID' && $k == 'ID') || $search_mode == $k):
          $menu .= " selected=\"selected\"";
          endif;
          $menu .= ">" . $v . "</option>\n"; 
        endforeach;
        $menu .= "</select>\n";
        print($menu);
    }

    function get_sortable_columns(){
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
        global $wpdb, $current_site;
        $usermeta_fields = $this->usermeta_columns;
        $this->process_bulk_action();
        $per_page = (!isset($_REQUEST['per_page']) || empty($_REQUEST['per_page'])) ? 10 : $_REQUEST['per_page'];
        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();
        $this->_column_headers = array($columns, $hidden, $sortable);
        $orderby = (!isset($_REQUEST['orderby']) || empty($_REQUEST['orderby'])) ? 'ID' : $_REQUEST['orderby'];
        $order = (!isset($_REQUEST['order']) || empty($_REQUEST['order'])) ? 'ASC' : $_REQUEST['order'];
        $search = (!isset($_REQUEST['s']) || empty($_REQUEST['s'])) ? false : $_REQUEST['s'];
        $search_mode = (!isset($_REQUEST['umm_search_mode']) || empty($_REQUEST['umm_search_mode'])) ? 'ID' : $_REQUEST['umm_search_mode'];
        define("UMM_ORDERBY", $orderby);
        define("UMM_ORDER", $order);     
        $data = umm_get_users();
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
          $checkit = stristr($d->$search_mode, $search);
          if($d->$search_mode == trim($search) || !empty($checkit)):
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
      $settings = umm_get_option('settings');
      $first_run = (empty($settings['first_run'])) ? 'yes' : $settings['first_run'];
      if($first_run == 'yes'):
        $settings['first_run'] = 'no';
        umm_update_option('settings', $settings);
      endif;
    ?>
<div class="umm-wrapper" data-help_text="<?php _e('User Meta Manager Help &amp; Settings', 'user-meta-manager') ?>" data-umm_loading_image="<?php echo WP_PLUGIN_URL . "/user-meta-manager/images/umm-loading.gif" ?>" data-first_run="<?php echo $first_run ?>" data-no_spaces="<?php _e('No Spaces', 'user-meta-manager') ?>" data-invalid_chars_warning="<?php _e('Letters, numbers, and underscores only.', 'user-meta-manager') ?>" data-key_exists="<?php _e('<strong>Error:</strong> That key already exists. Choose a different name.', 'user-meta-manager') ?>" data-duplicate_override="<?php echo $settings['duplicate_check_override'];  ?>">
<div id="icon-users" class="icon32"><br/></div><!-- #icon-users .icon32 -->
<h2 class="umm-plugin-title"><?php _e('User Meta Manager', 'user-meta-manager') ?></h2><!-- .umm-plugin-title -->
<div class="umm-slogan"><?php _e('Manage User Meta Data', 'user-meta-manager') ?></div><!-- .umm-slogan -->     
<div class="umm-secondary-wrapper">
  <div class="umm-nav-wrapper">
    <div class="umm-nav">
      <button title="<?php _e('Home', 'user-meta-manager'); ?>" class="umm-homelink umm-subpage-go umm-active-link button-primary umm-nav-button"><?php _e('Home', 'user-meta-manager'); ?></button><!-- .umm-homelink .umm-nav-button -->
      <button data-subpage="admin-ajax.php?action=umm_switch_action&amp;umm_sub_action=umm_add_custom_meta&amp;width=600&amp;u=1" title="<?php _e('Add Custom Meta', 'user-meta-manager'); ?>" class="umm-subpage-go button-secondary umm-nav-button"><?php _e('Add Custom Meta', 'user-meta-manager'); ?></button><!-- .umm-nav-button -->
      <button data-subpage="admin-ajax.php?action=umm_switch_action&amp;umm_sub_action=umm_edit_custom_meta&amp;width=600&amp;u=1" title="<?php _e('Edit Custom Meta', 'user-meta-manager'); ?>" class="umm-subpage-go button-secondary umm-nav-button"><?php _e('Edit Custom Meta', 'user-meta-manager'); ?></button><!-- .umm-nav-button -->
      <button data-subpage="admin-ajax.php?action=umm_switch_action&amp;umm_sub_action=umm_delete_custom_meta&amp;width=600&amp;u=1" title="<?php _e('Delete Custom Meta', 'user-meta-manager'); ?>" class="umm-subpage-go button-secondary umm-nav-button"><?php _e('Delete Custom Meta', 'user-meta-manager'); ?></button><!-- .umm-nav-button -->
      <button data-subpage="admin-ajax.php?action=umm_switch_action&amp;umm_sub_action=umm_edit_columns&width=600&height=500&amp;u=1" title="<?php _e('Edit Columns', 'user-meta-manager'); ?>" class="umm-subpage-go button-secondary umm-nav-button"><?php _e('Edit Columns', 'user-meta-manager'); ?></button><!-- .umm-nav-button -->
      <button data-subpage="admin-ajax.php?action=umm_switch_action&amp;umm_sub_action=umm_backup_page&width=600&height=500&amp;u=1" title="<?php _e('Backup &amp; Restore', 'user-meta-manager'); ?>" class="umm-subpage-go button-secondary umm-nav-button"><?php _e('Backup &amp; Restore', 'user-meta-manager'); ?></button><!-- .umm-nav-button -->
    </div><!-- .umm-nav -->
  </div><!-- .umm-nav-wrapper -->
  <div class="umm-message hidden"></div><!-- .umm-message -->    
  <div class="umm-subpage-wrapper">
    <div class="umm-subpage-loading hidden"><img class="umm-loading" src="<?php echo WP_PLUGIN_URL . "/user-meta-manager/images/umm-loading.gif" ?>" alt="..." /></div><!-- .umm-subpage-loading -->      
    <div class="umm-subpage hidden"></div><!-- .umm-subpage -->
    <div class="umm-per-page-menu hidden"><strong><?php _e('Items Per Page', 'user-meta-manager') ?>:</strong> <input type="text" id="per-page" name="per_page" size="4" value="<?php echo $per_page ?>" /><input class="umm-go button-secondary action" type="submit" value="<?php _e('Go', 'user-meta-manager') ?>" /></div><!-- .umm-per-page-menu -->
    <div id="umm-home">
      <div class="umm-search-mode-menu hidden"> <?php $this->get_search_menu(); ?></div><!-- .umm-search-mode-menu -->
      <form id="umm-form" method="GET">
        <input class="umm-mode" type="hidden" name="umm_mode" value="<?php echo $_REQUEST['umm_mode'] ?>" />
        <input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />
        <input type="hidden" name="paged" value="<?php echo $_REQUEST['paged'] ?>" />
        <input type="hidden" id="per-page-hidden" name="per_page" value="<?php echo $per_page; ?>" />
        <div id="umm-search"><?php $this->search_box(__('Search'), 'get') ?></div>
      </form>
      <form id="umm-list-table-form" method="post">
        <?php $this->display() ?>
      </form>
    </div><!-- .umm-home -->
  </div><!-- .umm-subpage-wrapper -->  
</div><!-- .umm-secondary-wrapper -->           
<div class="umm-result-container hidden"></div><!-- .umm-result-container -->
<code>&copy;<?php
	echo date('Y');
?> <a href="http://JasonLau.biz" target="_blank">JasonLau.biz</a> <a title="WebsiteDev.biz - <?php _e('Your bridge to a strong Internet presence.&trade;', 'user-meta-manager') ?>" href="http://WebsiteDev.biz" target="_blank">WebsiteDev.biz</a></code> <code>[<?php _e($this->title . ' Version', 'user-meta-manager') ?>: <?php echo UMM_VERSION; ?> <a title="<?php _e('Help make this plugin perfect by contributing your code on GitHub', 'user-meta-manager') ?>" href="https://github.com/jasonlau/Wordpress-User-Meta-Manager" target="_blank">Contribute</a>]</code>
</div><!-- .umm-wrapper -->
<?php
  }
} // class UMM_UI
?>