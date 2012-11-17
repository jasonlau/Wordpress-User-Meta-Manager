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
 
jQuery(function($){
        var page_data = $("div.umm-wrapper").data();
        $("div.actions").first().prepend($("div.umm-per-page-menu").html());
        $("div.umm-per-page-menu").html('');
        $("#get-search-input").after($("div.umm-search-mode-menu").html());
       
       $(".umm-go").each(function(){
        $(this).bind('mouseup',function(){
            $("#per-page-hidden").val($("#per-page").val());
            $("#umm-form").submit();
        }); 
       });
    
    $("a.umm-subpage").live('click', function(event){
        event.preventDefault();
        var obj = $(this),
        d = obj.data();
        if(d.nav_button){
           $(".umm-nav .umm-subpage-go").toggleClass('button-primary', false).toggleClass('button-secondary', true);
           $(".umm-nav .umm-subpage-go:contains('" + d.nav_button + "')").toggleClass('button-primary', true); 
        }
        $("div.umm-subpage-loading").show('slow');
        $("div.umm-subpage").load(d.subpage, function(){
            $("div.umm-subpage").show('slow');
            $("div.umm-subpage-loading").hide('slow');
            $("div#umm-home").hide('slow');
            if(d.message){
                $('div.umm-message').html(data.message).show('slow').delay(5000).hide('slow');
            }         
        });
    });
    
    $(".umm-button").live('click', function(event){
        event.preventDefault();
        var obj = $(this),
        d = obj.data(),
        loading_image = '<img class="umm-loading" src="' + $("div.umm-wrapper").data().umm_loading_image + '" alt="..." />';
        $("div.umm-subpage-loading").show('slow');
        
        if(obj.hasClass('umm-homelink')){
            $("div.umm-subpage").hide('slow');
            $("div.umm-subpage-loading").hide('slow');
            $("div#umm-home").show('slow');
        } else {
           $("div.umm-subpage").load(d.subpage, function(){
            $("div.umm-subpage-loading").hide('slow');
            $("div.umm-subpage").show('slow'); 
            if(d.message){
                $('div.umm-message').html(data.message).show('slow').delay(5000).hide('slow');
            }
           }); 
        }      
    });
    
    $(".umm-subpage-go").live('click', function(event){
        event.preventDefault();
        var obj = $(this),
        d = obj.data();
        
        $(".umm-subpage-go").toggleClass('umm-active-link', false);
        
        $(".umm-nav .umm-subpage-go").toggleClass('button-secondary', true);
        $(".umm-nav .umm-subpage-go").toggleClass('button-primary', false);
        $("div#umm-home").hide('slow');
        $("div.umm-subpage").hide('slow');
        $("div.umm-subpage-loading").show('slow');
        if((obj.hasClass('button-primary') || obj.hasClass('button-secondary')) && !obj.hasClass('umm-go-back-button')){
            $(".umm-nav .umm-subpage-go").toggleClass('button-primary', false);
            $(".umm-nav .umm-subpage-go").toggleClass('button-secondary', true);
            obj.toggleClass('button-primary', true);
        }           
        obj.toggleClass('umm-active-link', true);
        if(obj.hasClass('umm-homelink')){
            $("div.umm-subpage-loading").hide('slow');
            $("div#umm-home").show('slow');
        } else {
            
        $("div.umm-subpage").load(d.subpage, function(){
            $("div.umm-subpage-loading").hide('slow');
            $("div.umm-subpage").show('slow'); 
            if(d.message){
                $('div.umm-message').html(data.message).show('slow').delay(5000).hide('slow');
            }        
        });
        }       
    });
    
    $("#umm_edit_custom_meta_submit").live('click', function(event){
        event.preventDefault();
        if($("#umm_edit_key").val() != ""){
            var obj = $(this),
        d = obj.data(),
        original_value = obj.val(),
        return_page = $("#" + d.form + " input[name='return_page']").val() + '&umm_edit_key=' + $("#umm_edit_key").val();
        obj.prop('disabled',true).val(d.wait);
        $("div.umm-subpage").load(return_page, function(){
                if(d.message){
                    $('div.umm-message').html(data.message).show('slow').delay(5000).hide('slow');
                }
                $("div.umm-subpage").show('slow');
                $("div#umm-home").hide('slow');    
        });
        } else {
           $("#umm_edit_key").effect('highlight',1000); 
        }        
    });

    $("#umm_update_user_meta_submit").live('click', function(event){
        event.preventDefault();
        if($("#umm_edit_key").val() != ""){
        var obj = $(this),
        d = obj.data(),
        original_value = obj.val(),
        edit_key = ($("#umm_edit_key").val() == undefined) ? '' : $("#umm_edit_key").val(),
        return_page = $("#" + d.form + " input[name='return_page']").val() + '&umm_edit_key=' + edit_key;
        obj.prop('disabled',true).val(d.wait);
        
        $("div.umm-subpage-loading").show('slow');
        $.post('admin-ajax.php?action=' + d.subpage, $("#" + d.form).serialize(), function(data){
            $("div.umm-result-container").load(location.href + " div#umm-home", function(){                
                $("table.umm-users").replaceWith($("div.umm-result-container table.umm-users"));
                $("div#umm-search select.umm-search-mode").replaceWith($("div.umm-result-container select.umm-search-mode"));
                $("div.umm-result-container").html('');
            });
            $("div.umm-subpage").load(return_page, function(){
                if(data){
                    $('div.umm-message').html(data).show('slow').delay(5000).hide('slow');
                } 
               $("div.umm-subpage").show('slow'); 
               $("div#umm-home").hide('slow');
               $("div.umm-subpage-loading").hide('slow');          
            });
        });
        } else {
           $("#umm_edit_key").effect('highlight',1000); 
        }
    });
    
    $("select.umm-profile-field-type").live('change', function(){
        $(".umm-profile-field-options").hide('slow');
        switch($(this).val()){
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
            case 'textarea':
            case 'checkbox':
            $(".umm-input-options").show('slow');
            break;
                       
            case 'radio':
            case 'select':
            $(".umm-input-options").show('slow');
            $(".umm-select-options").show('slow');
            $(".umm-remove-option:first").hide();
            break;
            
            default:
            $(".umm-profile-field-options").hide('slow');
        }
    });
    
    switch($("select.umm-profile-field-type").val()){
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
            case 'textarea':
            case 'checkbox':
            $(".umm-input-options").show('slow');
            break;
                      
            case 'radio':
            case 'select':
            $(".umm-input-options").show('slow');
            $(".umm-select-options").show('slow');
            $(".umm-remove-option:first").hide();
            break;
            
            default:
            $(".umm-profile-field-options").hide('slow');
        }
    
    $(".umm-add-row").live('click', function(event){
        event.preventDefault();
        $(".umm-select-options-clone tr").clone().appendTo(".umm-select-options-table").show();
    });
        
    $(".umm-remove-row").live('click', function(event){
        event.preventDefault();
        $(this).closest("tr").remove();
    });
       
    $("#contextual-help-link").html(page_data.help_text).delay(1500).effect('highlight', 2000);
}); // jQuery