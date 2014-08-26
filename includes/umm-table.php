<?php

/**
 * @author Jason Lau
 * @copyright 2013+
 * @package user-meta-manager
 */

if(!defined("UMM_PATH")) die();
    
if(!class_exists('UMM_List_Table')):
    require_once(UMM_PATH . '/includes/class-umm-list-table.php');
endif;

class UMM_UI extends UMM_List_Table {  

    function __construct(){
        global $status, $page;
        parent::__construct(array(
            'singular'  => __('umm-user', UMM_SLUG),
            'plural'    => __('umm-users', UMM_SLUG),
            'ajax'      => false
        ));

        $this->title = "User Meta Manager";
        $this->slug = "user-meta-manager";
        $this->shortname = "umm_ui";
        $this->version = UMM_VERSION;
        $this->users_columns = (!umm_get_option("users_columns") ? array('ID' => __('ID', UMM_SLUG), 'user_login' => __('User Login', UMM_SLUG), 'user_registered' => __('Date Registered', UMM_SLUG)) : umm_get_option("users_columns"));
        $this->usermeta_columns = (!umm_get_option("usermeta_columns")) ? array() : umm_get_option("usermeta_columns");
    }
    
    function column_default($item, $column_name){
        return stripslashes($item->$column_name);
    }
    
    function column_user_login($item){
        $actions = array(
        'add_meta_data' => sprintf('<a href="#" data-subpage="admin-ajax.php?action=umm_switch_action&amp;umm_sub_action=umm_add_user_meta&umm_user=%s" title="'.__('Edit User Meta', UMM_SLUG).'" class="umm-subpage umm-table-link">' . __('Add Meta', UMM_SLUG) . '</a>',$item->ID),
            'edit_meta_data' => sprintf('<a href="#" data-subpage="admin-ajax.php?action=umm_switch_action&amp;umm_sub_action=umm_edit_user_meta&umm_user=%s" title="'.__('Edit User Meta', UMM_SLUG).'" class="umm-subpage umm-table-link">' . __('Edit Meta', UMM_SLUG) . '</a>',$item->ID),
            'delete_user_meta' => sprintf('<a href="#" data-subpage="admin-ajax.php?action=umm_switch_action&amp;umm_sub_action=umm_delete_user_meta&umm_user=%s" title="'.__('Delete User Meta', UMM_SLUG).'" class="umm-subpage umm-table-link">' . __('Delete Meta', UMM_SLUG) . '</a>',$item->ID)
        );

        return sprintf('%1$s %2$s',
            '<a title="' . __('Edit', UMM_SLUG) . ' ' . $item->user_login . '" href="user-edit.php?user_id=' . $item->ID . '" target="_blank">' . $item->user_login . '</a>',
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
            <input type=\"button\" class=\"umm-close-icon button-secondary\" title=\"" . __('Close', UMM_SLUG) . "\" value=\"x\" />"; 
            $output .= "</p></div>\n";
            define("UMM_STATUS", $output);       
        endif;       
    }

    function close_icon(){
        echo '<input type="button" class="umm-close-icon button-secondary" title="' . __('Close', UMM_SLUG) . '" value="x" />';
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
                 $fdata = maybe_unserialize($f_data);
                 if(is_array($fdata)):
                    $new_fdata = '';
                    foreach($fdata as $field_data):
                       if($field_data != '')
                       $new_fdata .= $field_data . ', ';
                    endforeach;
                    $fdata = rtrim($new_fdata, ', ');
                 endif;
                 $data[$x]->$k = $fdata;             
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
      $settings = umm_get_option('settings');
      $is_pro = false;
      if(umm_is_pro()):
         $is_pro = true;   
         if(function_exists('umm_pro_prepare_items')):
            $per_page = umm_pro_prepare_items($this);
         else:
            $per_page = $this->prepare_items(); 
         endif;
      else:
         $per_page = $this->prepare_items();
      endif;     
      $first_run = (empty($settings['first_run'])) ? 'yes' : $settings['first_run'];
      if($first_run == 'yes'):
        umm_first_run();
      endif;
    ?>
    <!-- #### UMM VERSION <?php echo UMM_VERSION; ?> #### -->
<div class="umm-wrapper" data-sub_action="<?php echo $_REQUEST['umm_sub_action']; ?>" data-help_text="<?php _e('User Meta Manager Help &amp; Settings', UMM_SLUG) ?>" data-umm_loading_image="<?php echo WP_PLUGIN_URL . "/user-meta-manager/images/umm-loading.gif" ?>" data-first_run="<?php echo $first_run ?>" data-no_spaces="<?php _e('No Spaces', UMM_SLUG) ?>" data-invalid_chars_warning="<?php _e('Letters, numbers, and underscores only.', UMM_SLUG) ?>" data-key_exists="<?php _e('<strong>Error:</strong> That key already exists. Choose a different name.', UMM_SLUG) ?>" data-duplicate_override="<?php echo $settings['duplicate_check_override'];  ?>">
<div id="icon-users" class="icon-users icon16 umm-icon"><br/></div><!-- #icon-users .icon32 -->
<h2 class="umm-plugin-title"><?php _e('User Meta Manager', UMM_SLUG);
if(umm_is_pro()):
_e(' Pro', UMM_SLUG); echo ' <i class="dashicons dashicons-awards"></i>';
endif; ?></h2><!-- .umm-plugin-title -->
<div class="umm-slogan"><?php _e('Manage User Meta Data', UMM_SLUG) ?></div><!-- .umm-slogan -->     
<div class="umm-secondary-wrapper">
  <div class="umm-nav-wrapper">
    <div class="umm-nav">
      <button title="<?php _e('Home', UMM_SLUG); ?>" class="umm-homelink umm-subpage-go umm-active-link button-primary umm-nav-button"><?php _e('Home', UMM_SLUG); ?></button><!-- .umm-homelink .umm-nav-button -->
      <button data-subpage="admin-ajax.php?action=umm_switch_action&amp;umm_sub_action=umm_add_custom_meta&amp;umm_user=1" title="<?php _e('Add Custom Meta', UMM_SLUG); ?>" class="umm-subpage-go button-secondary umm-nav-button umm_add_custom_meta"><?php _e('Add Custom Meta', UMM_SLUG); ?></button><!-- .umm-nav-button -->
      <button data-subpage="admin-ajax.php?action=umm_switch_action&amp;umm_sub_action=umm_edit_custom_meta&amp;umm_user=1" title="<?php _e('Edit Custom Meta', UMM_SLUG); ?>" class="umm-subpage-go button-secondary umm-nav-button umm_edit_custom_meta"><?php _e('Edit Custom Meta', UMM_SLUG); ?></button><!-- .umm-nav-button -->
      <button data-subpage="admin-ajax.php?action=umm_switch_action&amp;umm_sub_action=umm_delete_custom_meta&amp;umm_user=1" title="<?php _e('Delete Custom Meta', UMM_SLUG); ?>" class="umm-subpage-go button-secondary umm-nav-button umm_delete_custom_meta"><?php _e('Delete Custom Meta', UMM_SLUG); ?></button><!-- .umm-nav-button -->
      <button data-subpage="admin-ajax.php?action=umm_switch_action&amp;umm_sub_action=umm_edit_columns&amp;umm_user=1" title="<?php _e('Edit Columns', UMM_SLUG); ?>" class="umm-subpage-go button-secondary umm-nav-button umm_edit_columns"><?php _e('Edit Columns', UMM_SLUG); ?></button><!-- .umm-nav-button -->
      <button data-subpage="admin-ajax.php?action=umm_switch_action&amp;umm_sub_action=umm_backup_page&amp;umm_user=1" title="<?php _e('Backup &amp; Restore', UMM_SLUG); ?>" class="umm-subpage-go button-secondary umm-nav-button umm_backup_page"><?php _e('Backup &amp; Restore', UMM_SLUG); ?></button><!-- .umm-nav-button -->
      
<?php
if(!umm_is_pro()):
?>
<button onclick="location.href='http://jasonlau.biz/home/membership-options#umm-pro'" title="<?php _e('Get The Pro Plugin', UMM_SLUG); ?>" class="button-secondary umm-pro-button"><i class="dashicons dashicons-awards"></i><?php _e('Go Pro!', UMM_SLUG); ?></button><!-- .umm-nav-button -->
<?php           
endif;
?>
      
    </div><!-- .umm-nav -->
  </div><!-- .umm-nav-wrapper -->
  <div class="umm-message hidden"><?php
         $output = '';
         if(umm_is_pro()):
            if(function_exists('umm_pro_before_table')):
               $output .= call_user_func('umm_pro_before_table');
            endif; 
         endif;
         
         if(isset($_REQUEST['umm_message']) && !empty($_REQUEST['umm_message'])):
            $output .= $_REQUEST['umm_message'];
         endif;
         
         echo $output;
?></div><!-- .umm-message -->    
  <div class="umm-subpage-wrapper">
    <div class="umm-subpage-loading hidden"><img class="umm-loading" src="<?php echo WP_PLUGIN_URL . "/user-meta-manager/images/umm-loading.gif" ?>" alt="..." /></div><!-- .umm-subpage-loading -->      
    <div class="umm-subpage hidden"></div><!-- .umm-subpage -->
    <div class="umm-per-page-menu hidden"><strong><?php _e('Items Per Page', UMM_SLUG) ?>:</strong> <input type="text" id="per-page" name="per_page" size="4" value="<?php echo $per_page ?>" /><input class="umm-go button-secondary action" type="submit" value="<?php _e('Go', UMM_SLUG) ?>" /></div><!-- .umm-per-page-menu -->
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
</div><!-- .umm-wrapper -->
<?php
  }
} // class UMM_UI
?>