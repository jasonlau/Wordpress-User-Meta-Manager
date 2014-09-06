/**
 * @author Jason Lau
 * @copyright 2013
 * @package user-meta-manager
 */
 
 jQuery(function($){
        var page_data = $("div.umm-wrapper").data(),
        reload_home = function(){
            /* v 3.3.8 */
            $("div#umm-home").load(location.href + " div#umm-home", function(){                
                $("div#umm-home div.tablenav.top div.actions").html($("div.umm-per-page-menu").html());
                $("div#umm-search select.umm-search-mode").replaceWith($("div#umm-home select.umm-search-mode"));
            });
            
        };
        $("div.actions").first().prepend($("div.umm-per-page-menu").html());
        //$("div.umm-per-page-menu").html(''); // depreciated v 3.3.8
        $("#get-search-input").after($("div.umm-search-mode-menu").html());
       
       $(".umm-go").each(function(){
        $(this).bind('mouseup',function(){
            $("#per-page-hidden").val($("#per-page").val());
            $("#umm-form").submit();
        }); 
       });
       
       $(".pagination-links a").each(function(){
        var t = $(this).attr('href').split('per_page');
        if(!t[1]){
          var h = $(this).attr('href') + '&per_page=' + $("#per-page").val();
          $(this).attr('href', h);  
        }        
       });
    
    $(document).on('click', "a.umm-subpage", function(event){
        event.preventDefault();
        var obj = $(this),
        d = obj.data();
        if(d.nav_button){
           $(".umm-nav .umm-subpage-go").toggleClass('button-primary', false).toggleClass('button-secondary', true);
           $(".umm-nav .umm-subpage-go:contains('" + d.nav_button + "')").toggleClass('button-primary', true); 
        }
        $("div.umm-subpage-loading").fadeIn('slow');
        $("div.umm-subpage").load(d.subpage, function(){           
            $("div.umm-subpage-loading").fadeOut('slow');
            $("div#umm-home").fadeOut('slow', function(){
             $("div.umm-subpage").fadeIn('slow');   
            });
            if(d.message){
                $('div.umm-message').html(data.message).fadeIn('slow').delay(5000).fadeOut('slow');
            }         
        });
    });
    
    $(document).on('click', ".umm-button", function(event){
        event.preventDefault();
        var obj = $(this),
        d = obj.data(),
        loading_image = '<img class="umm-loading" src="' + $("div.umm-wrapper").data().umm_loading_image + '" alt="..." />';
        $("div.umm-subpage-loading").fadeIn('slow');
        
        if(obj.hasClass('umm-homelink')){
            
            $("div.umm-subpage").fadeOut('slow', function(){
                $("div.umm-subpage-loading").fadeOut('slow', function(){
                $("div#umm-home").fadeIn('slow');
                
            });
                
            });
            
            
        } else {
           $("div.umm-subpage").load(d.subpage, function(){
            $("div.umm-subpage-loading").fadeOut('slow');
            $("div.umm-subpage").fadeIn('slow'); 
            if(d.message){
                $('div.umm-message').html(data.message).fadeIn('slow').delay(5000).fadeOut('slow');
            }
           }); 
        }      
    });
    
    $(document).on('click', ".umm-subpage-go", function(event){
        event.preventDefault();
        var obj = $(this),
        d = obj.data();
        
        $(".umm-subpage-go").toggleClass('umm-active-link', false);
        
        $(".umm-nav .umm-subpage-go").toggleClass('button-secondary', true);
        $(".umm-nav .umm-subpage-go").toggleClass('button-primary', false);
        $("div#umm-home").fadeOut('slow');
        $("div.umm-subpage").fadeOut('slow');
        $("div.umm-subpage-loading").fadeIn('slow');
        if((obj.hasClass('button-primary') || obj.hasClass('button-secondary')) && !obj.hasClass('umm-go-back-button')){
            $(".umm-nav .umm-subpage-go").toggleClass('button-primary', false);
            $(".umm-nav .umm-subpage-go").toggleClass('button-secondary', true);
            obj.toggleClass('button-primary', true);
        }           
        obj.toggleClass('umm-active-link', true);
        if(obj.hasClass('umm-homelink')){
            $("div.umm-subpage-loading").fadeOut('slow');
            $("div.umm-subpage").fadeOut('slow', function(){
                $("div.umm-subpage-loading").fadeOut('slow', function(){
                $("div#umm-home").fadeIn('slow');
              });  
            });
        } else {
            
        $("div.umm-subpage").load(d.subpage, function(){
            $("div.umm-subpage-loading").fadeOut('slow');
            $("div.umm-subpage").fadeIn('slow'); 
            if(d.message){
                $('div.umm-message').html(data.message).fadeIn('slow').delay(5000).fadeOut('slow');
            }
            $(".umm-csv-builder-fields-add").hide();
            if($("ul#umm_edit_key").html()){
               $("ul#umm_edit_key").sortable({
                stop: function(event, ui){
                    $("ul#umm_edit_key").before("<div class='umm-order-updated hidden' style='padding: 10px;margin: 10px auto 10px auto;background-color: #FFFFC1;border: 1px solid #B3B300;color: #000000;-moz-border-radius: 8px;-khtml-border-radius: 8px;-webkit-border-radius: 8px;border-radius: 8px;'></div>");
                    $.post('admin-ajax.php?action=umm_switch_action&umm_sub_action=umm_update_custom_meta_order', $("form#umm_update_user_meta_form").serialize(), function(data){
                        $('.umm-order-updated').html(data).fadeIn('slow').delay(3000).hide('slow',function(){
                            $(this).remove();
                        });
                    });
                }
                }); 
            }                 
        });
        }       
    });
    
    $(document).on('click', "#umm_edit_custom_meta_submit", function(event){
        event.preventDefault();
        var submit_form = false, edit_key = '';
        if($("input[name='umm_edit_key']:checked").length > 0 || $("select#umm_edit_key option:selected").val()){
                
                submit_form = true;
                edit_key = (!$("input[name='umm_edit_key']:checked").val()) ? $("select#umm_edit_key option:selected").val() : $("input[name='umm_edit_key']:checked").val();
        } 

        if(submit_form){
            var obj = $(this),
        d = obj.data(),
        original_value = obj.val(),
        return_page = $("#" + d.form + " input[name='return_page']").val() + '&umm_edit_key=' + edit_key;
        obj.prop('disabled',true).val(d.wait);
        $("div.umm-subpage").load(return_page, function(){
                if(d.message){
                    $('div.umm-message').html(d.message).fadeIn('slow').delay(5000).fadeOut('slow');
                }
                $("div.umm-subpage").fadeIn('slow');
                $("div#umm-home").fadeOut('slow');
                   
        });
        } else {
            var fdata = $("form#umm_update_user_meta_form").data();
           $('div.umm-message').html(fdata.error_message).fadeIn('slow').delay(5000).fadeOut('slow');
           $("input[name='umm_edit_key']").parent().effect('highlight',2500).css({'border':'1px dashed red'}); 
        }        
    });

    $(document).on('click', "#umm_update_user_meta_submit", function(event){
        event.preventDefault();
        var edit_key = (!$("input[name='umm_edit_key']:selected").val()) ? $("select[name='umm_edit_key'] option:selected").val() : $("input[name='umm_edit_key']:selected").val();
        edit_key = (!edit_key) ? $("input[name='umm_edit_key']").val() : edit_key;
        edit_key = (!edit_key) ? $("input[name='umm_meta_key[]']").val() : edit_key;
        if(edit_key){            
        var obj = $(this),
        d = obj.data(),
        original_value = obj.val(),
        return_page = $("#" + d.form + " input[name='return_page']").val() + '&umm_edit_key=' + edit_key;
        obj.prop('disabled',true).val(d.wait);
        
        $("div.umm-subpage-loading").fadeIn('slow');
        
        $.post('admin-ajax.php?action=umm_switch_action&umm_sub_action=' + d.subpage, $("#" + d.form).serialize(), function(data){
            
            reload_home(); /* v 3.3.8 */
            
            $("div.umm-subpage").load(return_page, function(){
                
                if(data){
                    $('div.umm-message').html(data).fadeIn('slow').delay(5000).fadeOut('slow');
                } 
               $("div.umm-subpage").fadeIn('slow'); 
               $("div#umm-home").fadeOut('slow');
               $("div.umm-subpage-loading").fadeOut('slow');          
            });
        });
        } else {
           $("input[name='umm_edit_key']").parent().effect('highlight',1000); 
        }
    });
    
    $(document).on('click', "#umm_add_user_meta_submit", function(event){
        event.preventDefault();
        var add_key = (!$("input[name='umm_meta_key[]']").val()) ? '' : $("input[name='umm_meta_key[]']").val();
        
        if(add_key){            
        var obj = $(this),
        d = obj.data(),
        original_value = obj.val(),
        return_page = $("#" + d.form + " input[name='return_page']").val();
        obj.prop('disabled',true).val(d.wait);
        
        $("div.umm-subpage-loading").fadeIn('slow');
        
        $.post('admin-ajax.php?action=umm_switch_action&umm_sub_action=' + d.subpage, $("#" + d.form).serialize(), function(data){
            
            reload_home(); /* v 3.3.8 */
            
            $("div.umm-subpage").load(return_page, function(){
                if(data){
                    $('div.umm-message').html(data).fadeIn('slow').delay(5000).fadeOut('slow');
                } 
               $("div.umm-subpage").fadeIn('slow'); 
               $("div#umm-home").fadeOut('slow');
               $("div.umm-subpage-loading").fadeOut('slow');          
            });
        });
        } else {
           $("input[name='umm_meta_key']").effect('highlight',1000); 
        }
    });
    
    $(document).on('click', "#umm_delete_user_meta_submit", function(event){
        
        event.preventDefault();
        var edit_key = (!$("select#umm_edit_key option:selected").val()) ? $("input[name='umm_edit_key']").val() : $("select#umm_edit_key option:selected").val();
        if(edit_key){            
        var obj = $(this),
        d = obj.data(),
        original_value = obj.val(),
        return_page = $("#" + d.form + " input[name='return_page']").val() + '&umm_edit_key=' + edit_key;
        if($("input[name='sub_mode']").val()){
           return_page = $("#" + d.form + " input[name='return_page']").val(); 
        }
        
        obj.prop('disabled',true).val(d.wait);
        
        $("div.umm-subpage-loading").fadeIn('slow');
        
        $.post('admin-ajax.php?action=umm_switch_action&umm_sub_action=' + d.subpage, $("#" + d.form).serialize(), function(data){
            
            reload_home(); /* v 3.3.8 */
            
            $("div.umm-subpage").load(return_page, function(){
                
                if(data){
                    $('div.umm-message').html(data).fadeIn('slow').delay(5000).fadeOut('slow');
                } 
               $("div.umm-subpage").fadeIn('slow'); 
               $("div#umm-home").fadeOut('slow');
               $("div.umm-subpage-loading").fadeOut('slow');          
            });
        });
        } else {
           return false; 
        }
    });
    
    $(document).on('click', "#umm_update_columns_submit", function(event){
        
        event.preventDefault();
        var obj = $(this),
        d = obj.data();  
        if((d.mode == "add" && $("select[name='umm_column_key'] option:selected").val()) || ($("input[name='umm_column_key']:checked").val() && d.mode == "delete")){            
        var original_value = obj.val(),
        return_page = $("#" + d.form + " input[name='return_page']").val();
        
        obj.prop('disabled',true).val(d.wait);
        
        $("div.umm-subpage-loading").fadeIn('slow');
        
        $.post('admin-ajax.php?action=umm_switch_action&umm_sub_action=' + d.subpage, $("#" + d.form).serialize(), function(data){
            
            reload_home(); /* v 3.3.8 */
            
            $("div.umm-subpage").load(return_page, function(){               
                if(data){
                    $('div.umm-message').html(data).fadeIn('slow').delay(5000).fadeOut('slow');
                } 
               $("div.umm-subpage").fadeIn('slow'); 
               $("div#umm-home").fadeOut('slow');
               $("div.umm-subpage-loading").fadeOut('slow');          
            });
        });
        } else {
           return false; 
        }
    });
    
    $(document).on('click', ".umm-update-settings-submit", function(event){
        event.preventDefault();
        var obj = $(this),
        d = obj.data(),
        original_value = obj.val(),
        return_page = $("#" + d.form).attr('action');
        obj.prop('disabled', true).val(d.wait);
        $.post(return_page, $("#" + d.form).serialize(), function(data){
            $('div.umm-update-settings-result').html(data).fadeIn('slow').delay(5000).fadeOut('slow');
            obj.val(original_value).prop('disabled', false);  
        });        
    });
    
    $(document).on('change', "select.umm-profile-field-type", function(){
        $(".umm-profile-field-options, .umm-input-options-sub").fadeOut('slow');
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
            $(".umm-input-options, .umm-input-options-sub").fadeIn('slow');
            $(".umm-checkbox-options, .umm-random-string-options").fadeOut();
            break;
            
            case 'checkbox':
            $(".umm-input-options, .umm-input-options-sub").fadeIn('slow');
            $(".umm-random-string-options").fadeOut();
            $(".umm-checkbox-options").fadeIn('slow');
            break;
                       
            case 'radio':
            case 'checkbox_group':
            $(".umm-input-options, .umm-input-options-sub").fadeIn('slow');
            $(".umm-checkbox-options, .umm-random-string-options").fadeOut();
            $(".umm-select-options").fadeIn('slow');
            $(".umm-remove-option:first").hide();
            break;
            
            case 'select':
            $(".umm-input-options, .umm-input-options-sub").fadeIn('slow');
            $(".umm-checkbox-options, .umm-random-string-options").fadeOut();
            $(".umm-select-multi-options").fadeIn('slow');
            $(".umm-select-options").fadeIn('slow');
            $(".umm-remove-option:first").hide();
            break;
            
            case 'random_string':
            $(".umm-input-options").fadeIn('slow');
            $(".umm-random-string-options").fadeIn('slow');
            $(".umm-input-options-sub").fadeOut();
            $("select[name='umm_add_to_profile'] option[value='no']").prop('selected', true);
            break;
            
            default:
            $(".umm-checkbox-options, .umm-random-string-options").fadeOut();
            $(".umm-profile-field-options").fadeOut('slow');         
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
            $(".umm-input-options").fadeIn('slow');
            break;
                      
            case 'radio':
            case 'checkbox_group':
            $(".umm-input-options").fadeIn('slow');
            $(".umm-select-options").fadeIn('slow');
            $(".umm-remove-option:first").hide();
            break;
            
            case 'select':
            $(".umm-input-options").fadeIn('slow');
            $(".umm-select-multi-options").fadeIn('slow');
            $(".umm-select-options").fadeIn('slow');
            $(".umm-remove-option:first").hide();
            break;
            
            default:
            $(".umm-profile-field-options").fadeOut('slow');
        }
    
    $(".umm-select-options-table").sortable();
    
    $(document).on('click', ".umm-add-row", function(event){
        event.preventDefault();
        $(this).parent().parent().parent().parent().parent().after($(".umm-select-options-clone li").clone().show());
        $(".umm-select-options-table").sortable();
    });
        
    $(document).on('click', ".umm-remove-row", function(event){
        event.preventDefault();
        $(this).closest("li").remove();
    });
       
    $("#contextual-help-link").html(page_data.help_text).delay(1500).effect('highlight', 2000);
    
    if(page_data.first_run == 'yes'){
        $(document).bind('ready', function(){
            $('a#contextual-help-link').trigger('click');
        });
    }
    
    $(document).on('keyup change', "input[name='umm_meta_key[]']", function(event){
       $("input#umm_update_user_meta_submit").prop("disabled","disabled"); 
       var obj = $(this),
        original_value = obj.val(),
        new_value = original_value.replace(' ', '_').replace(/\W/g, ''),
        invalidChars = /\W/; // letters, numbers, and underscores
       
        if(new_value.replace('_', '') == ''){
            new_value = '';
            obj.attr('placeholder', page_data.no_spaces);
        }
        
        if(new_value != original_value){
            obj.effect('highlight', 2000);
            obj.val(new_value);
        }
        
        var current_val = obj.val(),
        check_if_exists = function(){
            //if(event.type == 'change'){
            var request = $.ajax({
                url: 'admin-ajax.php?action=umm_switch_action&umm_sub_action=umm_key_exists&umm_meta_key=' + current_val,
                type: "POST",
                dataType: "json"
            });
            request.done(function(data){
                if(data.key_exists && page_data.duplicate_override != 'yes'){
            if(!$(".key-exists-warning").html()){
                $("input#umm_update_user_meta_submit").prop("disabled","disabled");
            obj.after('<div class="umm-warning key-exists-warning hidden">' + page_data.key_exists + '</div>');
            $(".key-exists-warning").fadeIn('slow');
            }
        } else {
            if($(".key-exists-warning").html()){
                $(".key-exists-warning").fadeOut('slow').remove();
            }
            $("input#umm_update_user_meta_submit").prop("disabled","");
        }
            });
        //} 
        };
        
        if(invalidChars.test(new_value)){
            if(!$(".invalid-chars-warning").html()){
            obj.after('<div class="umm-warning invalid-chars-warning hidden">' + page_data.invalid_chars_warning + '</div>');
            $(".invalid-chars-warning").fadeIn('slow');
            }
        } else {
            if($(".invalid-chars-warning").html()){
                $(".invalid-chars-warning").fadeOut('slow').remove();
            }
        check_if_exists();
           
        }        
    });
    
    // Short Code Builder
    
    $(document).on('click', "input.umm-shortcode-builder-fields-add", function(event){
        event.preventDefault();
        $("div.umm-shortcode-builder-fields-clone div").clone().appendTo("div.umm-shortcode-builder-fields:first").show();
    });
    
    $(document).on('click', "table.umm-shortcode-builder input.umm-shortcode-builder-vars-add", function(event){
        event.preventDefault();
        $(".umm-shortcode-builder-vars-clone div").clone().appendTo(".umm-shortcode-builder-vars:first").show();
    });
        
    $(document).on('click', ".umm-shortcode-builder-remove", function(event){
        event.preventDefault();
        $(this).closest("div").remove();
        $("table.umm-shortcode-builder input").trigger('change');
    });
    
    var umm_update_shortcode = function(){
        var f = Array(), o = Array(), vars = '';
        $('table.umm-shortcode-builder select.umm-profile-fields-select option:selected').each(function(k,v){
            if($(this).val()){
               f[k] = $(this).val(); 
            }
            
        });
        
        $("table.umm-shortcode-builder input[data-for='value']").each(function(k,v){
            o[k] = $(this).val();            
        });
        
        $("table.umm-shortcode-builder input[data-for='key']").each(function(k,v){
            if($(this).val()){
                if(k == 0){
                  vars += $(this).val() + '=' + o[k];  
                } else {
                  vars += '&amp;' + $(this).val() + '=' + o[k];   
                }
                
            }            
        });
        
        if($("table.umm-shortcode-builder input[data-for='email_to']").val() != ''){
            if(!$(".umm-shortcode-builder-email-field").is(':visible')){
              $(".umm-shortcode-builder-email-field").fadeIn('slow').effect('highlight', 2000); 
              $(".umm-shortcode-builder-shortcode").toggleClass('alternate', true); 
            }
            
        } else {
            $(".umm-shortcode-builder-email-field").fadeOut('slow');
            $(".umm-shortcode-builder-shortcode").toggleClass('alternate', false);
        }
        
        $("table.umm-shortcode-builder input[data-for='fields']").val($('table.umm-shortcode-builder select.umm-profile-fields-select').val());
        var output = '[usermeta class="' + $("table.umm-shortcode-builder input[data-for='class']").val() + '" submit="' + $("table.umm-shortcode-builder input[data-for='submit']").val() + '" success="' + $("table.umm-shortcode-builder input[data-for='success']").val() + '" error="' + $("table.umm-shortcode-builder input[data-for='error']").val() + '" fields="' + f.join() + '" vars="' + vars + '" email_to="' + $("table.umm-shortcode-builder input[data-for='email_to']").val() + '" email_from="' + $("table.umm-shortcode-builder input[data-for='email_from']").val() + '" subject="' + $("table.umm-shortcode-builder input[data-for='subject']").val() + '" message="' + $("table.umm-shortcode-builder textarea[data-for='message']").val() + '"]';
        $(".umm-shortcode-builder-result").val(output);
    }
    
    $(document).on('keyup change', "table.umm-shortcode-builder input, table.umm-shortcode-builder select, table.umm-shortcode-builder textarea", function(){
        umm_update_shortcode();
    });
    
    $(".umm-shortcode-builder-email-field").hide();
    
    /* CSV Builder */
    
    $(document).on('change', "select.umm-csv-builder-keys", function(event){
        event.preventDefault();
        if($(this).val() != 'all'){
           $(".umm-csv-builder-fields-add").fadeIn('slow'); 
        } else {
            $(".umm-csv-builder-fields-add").fadeOut('slow');
            $("div.umm-csv-builder-fields").not("div.umm-csv-builder-fields:last").not("div.umm-csv-builder-fields:first").remove();
        }
    });
    
    $(document).on('click', "input.umm-csv-builder-fields-add", function(event){
        event.preventDefault();
        $("div.umm-csv-builder-fields-clone div").clone().appendTo("div.umm-csv-builder-fields:first").show();
    });
        
    $(document).on('click', ".umm-csv-builder-remove", function(event){
        event.preventDefault();
        $(this).closest("div").remove();
    });
    
    $(document).on('click', "a.umm-csv-builder-remove", function(event){
        event.preventDefault();
        $(this).closest("div").remove();
    });
    
    $(document).on('click', "button.umm-csv-builder-submit", function(event){
        event.preventDefault();
        var q = $(this).data().csv_link;       
        $("select.umm-csv-builder-keys option:selected").not("select.umm-csv-builder-keys option:selected:last").each(function(){
           q += '&umm_key[]=' + $(this).val(); 
        });
        window.open(q);
    });
    
    $('#umm-tabs').tabs();
    
    if((page_data.sub_action != '')){
        $('button.' + page_data.sub_action).trigger('click');
    }
    
    if(($('div.umm-message').html() != '')){
        $('div.umm-message').fadeIn('slow').delay(5000).fadeOut('slow');
    }
    
       
}); // jQuery