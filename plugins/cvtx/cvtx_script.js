jQuery(document).ready(function($){
    var target1 = window.location.hash.replace("#", "");
    if (target1 != ''){
        showTarget(target1);
    } else{
        showTarget('cvtx_tool');
    }
    
    // hide preview button for post_type top and application
    if ($("#post_type").val() == "cvtx_top" || $("#post_type").val() == "cvtx_application") {
        $("#preview-action").remove();
        $("#view-post-btn").remove();
    }
    
    // edit top
    if ($("#post_type").val() == "cvtx_top") {
        cvtx_fields = Array({"key": "cvtx_top_ord", "empty": false, "unique": true},
                            {"key": "cvtx_top_short", "empty": false, "unique": true});
        cvtx_validate(Array("cvtx_top_ord"));
        cvtx_validate(Array("cvtx_top_short"));
        $("#cvtx_top_ord_field").keyup(function() { cvtx_validate(Array("cvtx_top_ord")); });
        $("#cvtx_top_short_field").keyup(function() { cvtx_validate(Array("cvtx_top_short")); });
    }
    
    // edit application
    if ($("#post_type").val() == "cvtx_application") {
        cvtx_fields = Array({"key": "cvtx_application_ord", "empty": false, "unique": true});
        cvtx_validate(Array("cvtx_application_ord"));
        $("#cvtx_application_top_select").change(function() { cvtx_validate(Array("cvtx_application_ord")); });
        $("#cvtx_application_ord_field").keyup(function() { cvtx_validate(Array("cvtx_application_ord")); });
    }
    
    // edit reader
    if ($("#post_type").val() == "cvtx_reader") {
        $("#cvtx_reader_event_select").change(function() { cvtx_change_reader_event(); });
    }
    
    // edit antrag
    if ($("#post_type").val() == "cvtx_antrag") {
        cvtx_fields = Array({"key": "cvtx_antrag_ord", "empty": false, "unique": true, "shouldbenotempty": false});
                cvtx_validate(Array("cvtx_antrag_ord"));
        $("#cvtx_antrag_event_select").change(function() { cvtx_validate(Array("cvtx_antrag_ord")); });
        $("#cvtx_antrag_top_select").change(function() { cvtx_validate(Array("cvtx_antrag_ord")); });
        $("#cvtx_antrag_ord_field").keyup(function() { cvtx_validate(Array("cvtx_antrag_ord")); });
        $("#cvtx_antrag_event_select").change(function() { cvtx_change_top_event(); });
    }

    // edit aeantrag
    if ($("#post_type").val() == "cvtx_aeantrag") {
        cvtx_fields = Array({"key": "cvtx_aeantrag_zeile", "empty": false, "unique": true});
        cvtx_validate("cvtx_aeantrag_zeile");
        $("#cvtx_aeantrag_antrag_select").change(function() { cvtx_validate(Array("cvtx_aeantrag_zeile")); });
        $("#cvtx_aeantrag_zeile_field").keyup(function() { cvtx_validate(Array("cvtx_aeantrag_zeile")); });
    }
    
    // check all child-checkboxes
    $("#cvtx_reader_toc a.select_all").click(function() {
        $(this).parent().find(":checkbox").attr("checked", true);
    });
    
    // uncheck all child-checkboxes
    $("#cvtx_reader_toc a.select_none").click(function() {
        $(this).parent().find(":checkbox").attr("checked", false);
    });
    
    $("#cvtx_navi a").click(function() {
        var target = $(this).attr("href").replace("#", "");
        if(target == '') {
            $("li#cvtx_tool").show();
            $("h2.nav-tab-wrapper a.first").addClass("nav-tab-active");
        }
        else {
            showTarget(target);
        }
    });

    function showTarget(target) {
        $("h2.nav-tab-wrapper a").each(function() {
            $(this).removeClass("nav-tab-active");
        });
        $("ul#cvtx_options li.active").hide();
        $("#cvtx_navi a."+target).addClass("nav-tab-active");
        $("#"+target).fadeIn();
        $("#"+target).addClass("active");
        $('html,body').animate({scrollTop: 0}, 1);
    }
    
    function cvtx_change_top_event() {
      var eventID = $('#cvtx_antrag_event_select').val();
      var postType = $('#post_type').val();
      var currTop = $('#cvtx_antrag_top_select').val();
      if(currTop == undefined) {
        currTop = -1;
      }
      data = {'action' : 'cvtx_top_for_event',
              'cookie' : encodeURIComponent(document.cookie),
              'event_id' : eventID, 
              'post_type' : postType,
              'current_top' : currTop};
      $.post(ajaxurl, data, function(response) {
        if(response == 'ERR') {
          response = '<div id="cvtx_antrag_top_select">Keine TOPs für diese Veranstaltung vorhanden.</div>';
        }
        $('#cvtx_antrag_top_select').replaceWith(response);
      });
    }
    
    function cvtx_change_reader_event() {
      var eventID = $('#cvtx_reader_event_select').val();
      data = {'action' : 'cvtx_reader_from_event',
              'cookie' : encodeURIComponent(document.cookie),
              'event_id' : eventID};
      $('<div class="loading-cvtx"></div>').insertAfter('#cvtx_reader_event_select');
      $.post(ajaxurl, data, function(response) {
        $('#cvtx_reader_contents .inside').replaceWith('<div class="inside">'+response+'</div>');
//         $('#cvtx_reader_event').find('.loading-cvtx').remove();
      });
    }
    
    /**
     * checks wheater field is empty and/or input is unique
     */
    function cvtx_validate(meta_key) {
        // get value of post_meta field
        meta_value = new Array(meta_key.length);
        for(var i = 0; i < meta_key.length; i++) {
          meta_value[i] = $("#" + meta_key[i] + "_field").val().trim();
        }
        
        // update status
        for (var i = 0; i < cvtx_fields.length; i++) {
            for (var j = 0; j < meta_key.length; j++) {
                if (cvtx_fields[i].key == meta_key[j]) {
                    cvtx_fields[i].empty = !(meta_value[j] && meta_value[j].length > 0);
                    cvtx_fields[i].unique = !(meta_value[j] && meta_value[j].length > 0) || cvtx_fields[i].unique;
                }
            }
        }
        
        // value specified? check for unique
        if (meta_key[0] == "cvtx_antrag_ord" && meta_key.length == 1) {
            query = {"action"    : "cvtx_validate_antrag_ord",
                     "cookie"    : encodeURIComponent(document.cookie),
                     "post_type" : $("#post_type").val(),
                     "post_id"   : $("#post_ID").val(),
                     "top_id"    : $("#cvtx_antrag_top_select").val(),
                     "event_id"  : $("#cvtx_antrag_event_select option:selected:last").val(),
                     "antrag_ord": meta_value[0]
                    };
            // fetch info
            $.post(ajaxurl, query, function (str) {
                                       for (var i = 0; i < cvtx_fields.length; i++) {
                                          for (var j = 0; j < meta_key.length; j++) {
                                             if (cvtx_fields[i].key == meta_key[j]) cvtx_fields[i].unique = (str == "+OK");
                                          }
                                       }
                                       cvtx_toggle_buttons();
                                   });
        }
        else if (meta_value && meta_value.length > 0) {
            query = {"action"    : "cvtx_validate",
                     "cookie"    : encodeURIComponent(document.cookie),
                     "post_type" : $("#post_type").val(),
                     "post_id[0]": $("#post_ID").val(),
                     "args"      : new Array()
                    };

            for(var i = 0; i < meta_key.length; i++) {
                query.args.push({"key"    : meta_key[i],
                                 "value"  : meta_value[i],
                                 "compare": "="});
                // special arguments for post_types
                if (meta_key[i] == "cvtx_antrag_ord") {
                    query.args.push({"key"     : "cvtx_antrag_top",
                                     "value"   : $("#cvtx_antrag_top_select").val(),
                                     "compare" : "="});
                    query.args.push({"key"     : "cvtx_antrag_event",
                                     "value"   : $("#cvtx_antrag_event_select option:selected:last").val(),
                                     "compare" : "="});
                } else if (meta_key[i] == "cvtx_aeantrag_zeile" || meta_key[i] == "cvtx_aeantrag_ord") {
                    query.args.push({"key"     : "cvtx_aeantrag_antrag",
                                     "value"   : $("#cvtx_aeantrag_antrag_select").val(),
                                     "compare" : "="});
                } else if (meta_key[i] == "cvtx_application_ord") {
                    query.args.push({"key"     : "cvtx_application_top",
                                     "value"   : $("#cvtx_application_top_select").val(),
                                     "compare" : "="});
                }
            }

            // fetch info
            $.post(ajaxurl, query, function (str) {
                for (var i = 0; i < cvtx_fields.length; i++) {
                    for (var j = 0; j < meta_key.length; j++) {
                        if (cvtx_fields[i].key == meta_key[j]) cvtx_fields[i].unique = (str == "+OK");
                    }
                }
                cvtx_toggle_buttons();
            });
        }
        
        // update buttons
        cvtx_toggle_buttons();
    }
    
    /**
     * show/hide save and publish buttons and errorbox
     */
    function cvtx_toggle_buttons() {
        empty = 0; notunique = 0;
        
        // fetch status
        for (var i = 0; i < cvtx_fields.length; i++) {
            if (cvtx_fields[i].shouldbenotempty !== false && cvtx_fields[i].empty)   empty++;
            if (!cvtx_fields[i].unique) notunique++;
        }
        
        // update buttons
        $("#save-post").attr("disabled", notunique > 0);
        $("#save").attr("disabled", notunique > 0);
        if (notunique > 0 || empty > 0) {
            $("#preview-action").hide();
            $("#publish").attr("disabled", true);
            cvtx_toggle_errorbox();
            $("#admin_message").fadeIn();
        } else {
            $("#preview-action").show();
            $("#publish").attr("disabled", false);
            $("#admin_message").fadeOut("normal", cvtx_toggle_errorbox);
        }
    }
    
    /**
     * updates error messages
     */
    function cvtx_toggle_errorbox() {
        for (var i = 0; i < cvtx_fields.length; i++) {
            // field empty?
            if (cvtx_fields[i].empty) {
                $("#empty_error_" + cvtx_fields[i].key).css("display", "block");
            } else {
                $("#empty_error_" + cvtx_fields[i].key).css("display", "none");
            }
            
            // input unique?
            if (!cvtx_fields[i].unique) {
                $("#" + cvtx_fields[i].key + "_field").addClass("error");
                $("#unique_error_" + cvtx_fields[i].key).css("display", "block");
            } else {
                $("#" + cvtx_fields[i].key + "_field").removeClass("error");
                $("#unique_error_" + cvtx_fields[i].key).css("display", "none");
            }
        }
    }
});

