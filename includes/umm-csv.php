<?php

/**
 * @author Jason Lau
 * @copyright 2013
 * @package user-meta-manager
 */

require('../../../../wp-load.php');

function umm_csv_user_meta_data($criteria="ORDER BY umeta_id ASC"){
    global $wpdb;
    $data = $wpdb->get_results("SELECT * FROM $wpdb->usermeta " . $criteria);
    return $data;
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

if(is_user_logged_in() and wp_verify_nonce($_REQUEST['umm_nonce'],md5($_SERVER["REMOTE_ADDR"].$_SERVER["HTTP_USER_AGENT"]))):
    umm_get_csv();
else:
    die();  
endif; 
?>