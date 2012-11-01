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
 
jQuery.cookie = function(name, value, options) {
    if (typeof value != 'undefined') {
        options = options || {};
        if (value === null) {
            value = '';
            options.expires = -1;
        }
        var expires = '';
        if (options.expires && (typeof options.expires == 'number' || options.expires.toUTCString)) {
            var date;
            if (typeof options.expires == 'number') {
                date = new Date();
                date.setTime(date.getTime() + (options.expires * 24 * 60 * 60 * 1000));
            } else {
                date = options.expires;
            }
            expires = '; expires=' + date.toUTCString();
        }
        var path = options.path ? '; path=' + (options.path) : '';
        var domain = options.domain ? '; domain=' + (options.domain) : '';
        var secure = options.secure ? '; secure' : '';
        document.cookie = [name, '=', encodeURIComponent(value), expires, path, domain, secure].join('');
    } else {
        var cookieValue = null;
        if (document.cookie && document.cookie != '') {
            var cookies = document.cookie.split(';');
            for (var i = 0; i < cookies.length; i++) {
                var cookie = jQuery.trim(cookies[i]);
                if (cookie.substring(0, name.length + 1) == (name + '=')) {
                    cookieValue = decodeURIComponent(cookie.substring(name.length + 1));
                    break;
                }
            }
        }
        return cookieValue;
    }
};
    
    jQuery(function($){
        $("div.actions").first().prepend($("div.umm-per-page-menu").html()).append('<input class="umm-help button-secondary hidden" type="button" value="?" title="Info" />');
        $("div.umm-per-page-menu").html('');
        $("#get-search-input").after($("div.umm-search-mode-menu").html());
        $('.umm-help').css('border-color','#FFFF00');
        $(".umm-mode").each(function(){
            $(this).bind('mouseup',function(){
                try{
                    $("#umm-list-table-form input[type='checkbox']").prop('checked',false);
                    $("#umm-form select").val('');
                }catch(e){}
                $(".umm-mode").val($(this).attr('rel'));
                $("#umm-form").submit();
            });
       });
       
       $(".umm-go").each(function(){
        $(this).bind('mouseup',function(){
            $("#per-page-hidden").val($("#per-page").val());
            $("#umm-form").submit();
        }); 
       });

       $(".umm-help").bind('mouseup',function(){
        $(".umm-info").show('slow');
	    $(this).hide('slow');
        $.cookie('umminfo',1);
	   });

       if($.cookie('umminfo') == 1){
        $(".umm-info").show();
        $(".umm-help").hide();
       } else {
        $(".umm-info").hide();
        $(".umm-help").show();
       }

       $(".umm-close-info-icon").css('text-decoration','none').click(function(){
       $(this).parent().hide('slow');
       $(".umm-help").show('slow');
       $.cookie('umminfo',0);
    });

    $(".umm-close-icon").css('text-decoration','none').click(function(){
       $(this).parent().hide('slow');
    });

    $("div.actions:last").css({
        'margin': '0px 0px 0px 0px !important'
    });

    $("#umm_update_user_meta_submit").live('click', function(event){
        event.preventDefault();
        var obj = $(this),
        d = obj.data(),
        original_value = $(this).val(),
        return_page = $("#" + d.form + " input[name='return_page']").val();
        obj.prop('disabled',true).val(d.wait);
        $.post('admin-ajax.php?action=' + d.action + '&width=600&height=500', $("#" + d.form).serialize(), function(data){
            $("div.umm-result-container").load(location.href + " div#umm-left-panel", function(){
                
                $("table.wp-list-table:first").replaceWith($("div.umm-result-container table.wp-list-table"));
                $("div#umm-search select.um-search-mode").replaceWith($("div.umm-result-container select.um-search-mode"));
                $("div.umm-result-container").html('');
            });
            $("#TB_ajaxContent").load(return_page, function(){
                // new Effect.Highlight("TB_ajaxContent", { startcolor: '#ffff99', endcolor: '#ffffff' });
            $('#' + d.form + ' div.umm_update_user_meta-result').html(data).show('slow').delay(5000).hide('slow');
            });
        });
    });
    
    $(".umm-remove-row").live('click', function(event){
        $(this).closest("tr").remove();
    });
    }); // jQuery