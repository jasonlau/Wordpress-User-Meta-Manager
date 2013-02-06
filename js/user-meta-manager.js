/**
 * @author Jason Lau
 * @copyright 2012
 * @package user-meta-manager
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
    
    $(document).on('click', "a.umm-subpage", function(event){
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
    
    $(document).on('click', ".umm-button", function(event){
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
    
    $(document).on('click', ".umm-subpage-go", function(event){
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
            if($("table#umm_edit_key tbody").html()){
               $("table#umm_edit_key tbody").sortable({
                stop: function(event, ui){
                    $("table#umm_edit_key").before("<div class='umm-order-updated hidden' style='padding: 10px;margin: 10px auto 10px auto;background-color: #FFFFC1;border: 1px solid #B3B300;color: #000000;-moz-border-radius: 8px;-khtml-border-radius: 8px;-webkit-border-radius: 8px;border-radius: 8px;'></div>");
                    $.post('admin-ajax.php?action=umm_switch_action&sub_action=umm_update_custom_meta_order', $("form#umm_update_user_meta_form").serialize(), function(data){
                        $('.umm-order-updated').html(data).show('slow').delay(3000).hide('slow',function(){
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
                    $('div.umm-message').html(d.message).show('slow').delay(5000).hide('slow');
                }
                $("div.umm-subpage").show('slow');
                $("div#umm-home").hide('slow');
                   
        });
        } else {
            var fdata = $("form#umm_update_user_meta_form").data();
           $('div.umm-message').html(fdata.error_message).show('slow').delay(5000).hide('slow');
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
        
        $("div.umm-subpage-loading").show('slow');
        
        $.post('admin-ajax.php?action=umm_switch_action&sub_action=' + d.subpage, $("#" + d.form).serialize(), function(data){
            
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
        
        $("div.umm-subpage-loading").show('slow');
        
        $.post('admin-ajax.php?action=umm_switch_action&sub_action=' + d.subpage, $("#" + d.form).serialize(), function(data){
            
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
           $("input[name='umm_meta_key']").effect('highlight',1000); 
        }
    });
    
    $(document).on('click', "#umm_delete_user_meta_submit", function(event){
        
        event.preventDefault();
        var edit_key = (!$("select[name='umm_edit_key'] option:selected").val()) ? $("input[name='umm_edit_key']").val() : $("select[name='umm_edit_key'] option:selected").val();
          
        if(edit_key){            
        var obj = $(this),
        d = obj.data(),
        original_value = obj.val(),
        return_page = $("#" + d.form + " input[name='return_page']").val() + '&umm_edit_key=' + edit_key;
        if($("input[name='sub_mode']").val()){
           return_page = $("#" + d.form + " input[name='return_page']").val(); 
        }
        
        obj.prop('disabled',true).val(d.wait);
        
        $("div.umm-subpage-loading").show('slow');
        
        $.post('admin-ajax.php?action=umm_switch_action&sub_action=' + d.subpage, $("#" + d.form).serialize(), function(data){
            
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
        
        $("div.umm-subpage-loading").show('slow');
        
        $.post('admin-ajax.php?action=umm_switch_action&sub_action=' + d.subpage, $("#" + d.form).serialize(), function(data){
            
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
            $('div.umm-update-settings-result').html(data).show('slow').delay(5000).hide('slow');
            obj.val(original_value).prop('disabled', false);  
        });        
    });
    
    $(document).on('change', "select.umm-profile-field-type", function(){
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
    
    $(document).on('click', ".umm-add-row", function(event){
        event.preventDefault();
        $(".umm-select-options-clone tr").clone().appendTo(".umm-select-options-table").show();
    });
        
    $(document).on('click', ".umm-remove-row", function(event){
        event.preventDefault();
        $(this).closest("tr").remove();
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
                url: 'admin-ajax.php?action=umm_switch_action&sub_action=umm_key_exists&umm_meta_key=' + current_val,
                type: "POST",
                dataType: "json"
            });
            request.done(function(data){
                if(data.key_exists){
            if(!$(".key-exists-warning").html()){
                $("input#umm_update_user_meta_submit").prop("disabled","disabled");
            obj.after('<div class="umm-warning key-exists-warning hidden">' + page_data.key_exists + '</div>');
            $(".key-exists-warning").show('slow');
            }
        } else {
            if($(".key-exists-warning").html()){
                $(".key-exists-warning").hide('slow').remove();
            }
            $("input#umm_update_user_meta_submit").prop("disabled","");
        }
            });
        //} 
        };
        
        if(invalidChars.test(new_value)){
            if(!$(".invalid-chars-warning").html()){
            obj.after('<div class="umm-warning invalid-chars-warning hidden">' + page_data.invalid_chars_warning + '</div>');
            $(".invalid-chars-warning").show('slow');
            }
        } else {
            if($(".invalid-chars-warning").html()){
                $(".invalid-chars-warning").hide('slow').remove();
            }
        check_if_exists();
           
        }        
    });
       
}); // jQuery