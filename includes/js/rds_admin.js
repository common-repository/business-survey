/*
	Business Survey main admin script
*/

let rds_has_changed_question_order = false;

jQuery(window).load(function() {
    rds_start();
});

function rds_start() {
    rds_load_active_tab();
	if(jQuery("body").hasClass("post-type-serve_type_questions") && jQuery("body").hasClass("taxonomy-RDserve")){
		// add warning to RDserve taxonomy page
		//let warning = '<div id = "rds_RDserve_tax_warning"><h2>WARNING</h2><p>Please note that deleting a RDserve here will NOT delete any attached questions to it. You can delete questions in bulk by clicking the following button</p><a href = "./edit.php?post_type=serve_type_questions" class = "rds_button4">DELETE QUESTIONS</a></div>';
		//jQuery(".form-wrap").append(warning)
	}
}

// show the default tab on load
function rds_load_active_tab() {
    var activeTab = jQuery("#rds_tabs .rds_active_tab").attr("data-hdq-content");
    jQuery("#" + activeTab).addClass("rds_tab_active");
    jQuery(".rds_tab_active").slideDown(500);
}

jQuery(".rds_accordion h3").click(function() {
    jQuery(this).next("div").toggle(600);
});

/* Tab navigation
------------------------------------------------------- */
jQuery("#rds_form_wrapper").on("click", "#rds_tabs li", function(event) {
    jQuery('#rds_tabs li').removeClass("rds_active_tab");
    jQuery(this).addClass("rds_active_tab");
    var hdqContent = jQuery(this).attr("data-hdq-content");
    jQuery(".rds_tab_active").fadeOut();
    jQuery(".rds_tab").removeClass("rds_tab_active");
    jQuery("#" + hdqContent).delay(250).fadeIn();
    jQuery("#" + hdqContent).addClass("rds_tab_active");
})


/* Show or hide answer images
------------------------------------------------------- */
jQuery("#rds_form_wrapper").on("click", "#hdQue-post-class23", function(event) {	
    jQuery(".rds_answer_as_image").toggleClass("rds_use_image_as_answer");
});

/* Show or hide answers
------------------------------------------------------- */
jQuery("#rds_form_wrapper").on("click", "#hdQue-post-class24", function(event) {		
    jQuery("#rds_tab_wrapper").fadeToggle();
});

/* For now, only allow 1 correct answer at a time
------------------------------------------------------- */
jQuery("#rds_form_wrapper").on("click", ".rds_correct", function(event) {	
    jQuery(".rds_correct").prop('checked', false);
    jQuery(this).prop('checked', true);
    //get index hdQue-post-class2
    let rds_selected_val = jQuery(".rds_correct").index(this) + 1;
    jQuery("#hdQue-post-class2").val(rds_selected_val);
});

/* Upload a feature answer image
------------------------------------------------------- */
let rds_file_frame_featured_image;
let rds_file_frame_input = "";
jQuery("#rds_form_wrapper").on("click", ".rds_featured_image", function(event) {	

	let rds_file_title = "Upload an image";
	let rds_file_button = "SET IMAGE";
	
    // get the input to update
    rds_file_frame_input = jQuery(this).next("input");
    rds_file_frame_image = jQuery(".rds_featured_image").index(this);
	
    // If the media frame already exists, reopen it.
    if (rds_file_frame_featured_image) {
        rds_file_frame_featured_image.open();
        return;
    }	
    // Create the media frame.
    rds_file_frame_featured_image = wp.media.frames.file_frame = wp.media({
        title: rds_file_title,
        button: {
            text: rds_file_button,
        },
        multiple: false
    });

    // When an image is selected, run a callback.
    rds_file_frame_featured_image.on('select', function() {
        attachment = rds_file_frame_featured_image.state().get('selection').first().toJSON();
        imgURL = attachment.sizes.thumbnail.url;
		imgURLfull = attachment.sizes.full.url;
		image_to_update = jQuery('.rds_featured_image img').eq(rds_file_frame_image);
		if(jQuery(image_to_update).hasClass("rds_question_featured")){
			jQuery(image_to_update).attr("src", imgURLfull);
		} else {
        	jQuery(image_to_update).attr("src", imgURL);
		}
		jQuery(rds_file_frame_input).val(attachment.id);		
		if(jQuery("body").hasClass("toplevel_page_rds_serves")){
			jQuery(rds_file_frame_input).prev().attr("data-id", attachment.id)
		}
		if(typeof(jQuery(rds_file_frame_input).attr("id")) === "undefined"){
			// looks like this is the featured image
			jQuery("#rds_featured_image").attr("data-id", attachment.id)
		}
    });
    rds_file_frame_featured_image.open();
});

function rds_scroll_to_top(){
	jQuery('html').animate({
    	scrollTop: 0
	}, 'slow');	
}

// start loading stuff
function rds_start_load(){
	jQuery("#rds_message").fadeOut();
	jQuery("#rds_loading ").fadeIn();
}
// after stuff has loaded
function rds_after_load(editor = false){
	
	jQuery("#rds_loading ").delay(600).fadeOut();	
	rds_has_changed_question_order = false;
	rds_load_active_tab();
	rds_scroll_to_top();	
	hdf_start_sortable();
	if(editor){
		tinyMCE.execCommand('mceRemoveEditor', false, 'hdQue-post-class26');
		tinyMCE.execCommand('mceRemoveEditor', false, 'rc_RDserve_term_meta_passText');
		tinyMCE.execCommand('mceRemoveEditor', false, 'rc_RDserve_term_meta_failText');		
		setTimeout(function() {			
			// there HAS to be a better way... right?
			tinyMCE.init({
				mode: "textareas",
				theme: "modern",
				skin: "lightgray",
				language: "en",
				formats: {
					alignleft: [{
						selector: "p,h1,h2,h3,h4,h5,h6,td,th,div,ul,ol,li",
						styles: {
							textAlign: "left"
						}
					}, {
						selector: "img,table,dl.wp-caption",
						classes: "alignleft"
					}],
					aligncenter: [{
						selector: "p,h1,h2,h3,h4,h5,h6,td,th,div,ul,ol,li",
						styles: {
							textAlign: "center"
						}
					}, {
						selector: "img,table,dl.wp-caption",
						classes: "aligncenter"
					}],
					alignright: [{
						selector: "p,h1,h2,h3,h4,h5,h6,td,th,div,ul,ol,li",
						styles: {
							textAlign: "right"
						}
					}, {
						selector: "img,table,dl.wp-caption",
						classes: "alignright"
					}],
					strikethrough: {
						inline: "del"
					}
				},
				relative_urls: false,
				remove_script_host: false,
				convert_urls: false,
				browser_spellcheck: true,
				fix_list_elements: true,
				entities: "38,amp,60,lt,62,gt",
				entity_encoding: "raw",
				keep_styles: false,
				resize: true,
				menubar: false,
				branding: false,
				preview_styles: "font-family font-size font-weight font-style text-decoration text-transform",
				end_container_on_empty_block: true,
				wpeditimage_html5_captions: true,
				wp_lang_attr: "en-US",
				wp_keep_scroll_position: true,
				wp_shortcut_labels: {
					"Heading 1": "access1",
					"Heading 2": "access2",
					"Heading 3": "access3",
					"Heading 4": "access4",
					"Heading 5": "access5",
					"Heading 6": "access6",
					"Paragraph": "access7",
					"Blockquote": "accessQ",
					"Underline": "metaU",
					"Strikethrough": "accessD",
					"Bold": "metaB",
					"Italic": "metaI",
					"Code": "accessX",
					"Align center": "accessC",
					"Align right": "accessR",
					"Align left": "accessL",
					"Justify": "accessJ",
					"Cut": "metaX",
					"Copy": "metaC",
					"Paste": "metaV",
					"Select all": "metaA",
					"Undo": "metaZ",
					"Redo": "metaY",
					"Bullet list": "accessU",
					"Numbered list": "accessO",
					"Insert\/edit image": "accessM",
					"Remove link": "accessS",
					"Toolbar Toggle": "accessZ",
					"Insert Read More tag": "accessT",
					"Insert Page Break tag": "accessP",
					"Distraction-free writing mode": "accessW",
					"Keyboard Shortcuts": "accessH"
				},			
				plugins: "charmap,colorpicker,hr,lists,media,paste,tabfocus,textcolor,fullscreen,wordpress,wpautoresize,wpeditimage,wpemoji,wpgallery,wplink,wpdialogs,wptextpattern,wpview",			
				wpautop: true,
				indent: false,
				toolbar1: "formatselect,bold,italic,bullist,numlist,blockquote,alignleft,aligncenter,alignright,link,wp_more,spellchecker,dfw,wp_adv",
				toolbar2: "strikethrough,hr,forecolor,pastetext,removeformat,charmap,outdent,indent,undo,redo,wp_help",
				toolbar3: "",
				toolbar4: "",
				tabfocus_elements: "content-html,save-post",
				wp_autoresize_on: true,
				add_unload_trigger: false,
				block_formats: "Paragraph=p;Heading 2=h2;Heading 3=h3;Heading 4=h4;Code=code"
			});				
		}, 1200); // give it time to re-init
	}
}

// show message box
function rds_show_message(message){
		jQuery("#rds_message").html(message);		
		jQuery("#rds_message").fadeIn();	
}

// hide message
jQuery("#rds_wrapper").on("click", "#rds_message", function(event) {
	jQuery("#rds_message").fadeOut();
});


function hdf_start_sortable() {
    jQuery("#rds_RDserve_question_list").sortable({
        placeholder: "sorting_placeholder",
        items: ".rds_RDserve_question",
        distance: 15, // sets the drag tolerance to something more acceptable
		update: function(event, ui) {
			rds_has_changed_question_order = true;
		}
    });
}

// view RDserve
jQuery("#rds_form_wrapper").on("click", ".rds_RDserve_term, #rds_back_to_RDserve", function(event) {
	if (event.target !== this){
		// allow users to copy / paste shortcode
    	return;	
	}	
	rds_start_load();
	let RDserve_id = jQuery(this).attr("data-id");

    jQuery.ajax({
        type: 'POST',
        data: {
            action: "rds_view_RDserve",
            rds_serves_nonce: jQuery("#rds_serves_nonce").val(),
			RDserve_id: RDserve_id
        },
        url: ajaxurl,
        success: function(data) {
            jQuery("#rds_form_wrapper").html(data);
        },
        error: function() {
            console.log("Permission denied");
        },
        complete: function() {
			rds_after_load(true);
        }
    });
});


// update RDserve
jQuery("#rds_form_wrapper").on("click", "#rds_save_RDserve", function(event) {	
	rds_start_load();
	
	let RDserve_id = jQuery(this).attr("data-id");
	
	// get list of all questions and their menu_order
	let questions = [];
	let menu_number = 0;
	if(rds_has_changed_question_order){
		jQuery(".rds_RDserve_question").each(function(){
			let question = [];
			let id = jQuery(this).attr("data-id");
			question = [id, menu_number];
			questions.push(question);
			menu_number = menu_number + 1;
		});	
	}
	questions = JSON.stringify(questions);
	
	// get settings data
	tinyMCE.triggerSave();
	let passPercent = jQuery("#rds_RDserve_pass_percent").val();
	let passText = jQuery("#rc_RDserve_term_meta_passText").val();
	let failText = jQuery("#rc_RDserve_term_meta_failText").val();
	let rds_share_results = jQuery("#rds_share_results").prop("checked");
	let rds_results_position = jQuery("#rds_results_position").prop("checked");
	let rds_show_results = jQuery("#rds_show_results").prop("checked");
	let rds_show_results_correct = jQuery("#rds_show_results_correct").prop("checked");
	let rds_show_extra_text = jQuery("#rds_show_extra_text").prop("checked");
	let rds_RDserve_timer = jQuery("#rds_RDserve_timer").val();
	let rds_randomize_question_order = jQuery("#rds_randomize_question_order").prop("checked");
	let rds_randomize_answer_order = jQuery("#rds_randomize_answer_order").prop("checked");
	let rds_pool_of_questions = jQuery("#rds_pool_of_questions").val();
	let rds_wp_paginate = jQuery("#rds_wp_paginate").val();
	let rds_immediate_mark = jQuery("#rds_immediate_mark").prop("checked");
	let rds_stop_answer_reselect = jQuery("#rds_stop_answer_reselect").prop("checked");
	
	// set defaults for checkboxes so older versions are compatible	
	if(rds_share_results){
		rds_share_results = "yes";
	} else {
		rds_share_results = "no";
	}
	if(rds_results_position){
		rds_results_position = "yes";
	} else {
		rds_results_position = "no";
	}
	if(rds_show_results){
		rds_show_results = "yes";
	} else {
		rds_show_results = "no";
	}
	if(rds_show_results_correct){
		rds_show_results_correct = "yes";
	} else {
		rds_show_results_correct = "no";
	}
	if(rds_show_extra_text){
		rds_show_extra_text = "yes";
	} else {
		rds_show_extra_text = "no";
	}
	if(rds_randomize_question_order){
		rds_randomize_question_order = "yes";
	} else {
		rds_randomize_question_order = "no";
	}
	if(rds_randomize_answer_order){
		rds_randomize_answer_order = "yes";
	} else {
		rds_randomize_answer_order = "no";
	}	
	if(rds_immediate_mark){
		rds_immediate_mark = "yes";
	} else {
		rds_immediate_mark = "no";
	}
	if(rds_stop_answer_reselect){
		rds_stop_answer_reselect = "yes";
	} else {
		rds_stop_answer_reselect = "no";
	}
	
	
    jQuery.ajax({
        type: 'POST',
        data: {
            action: "rds_save_RDserve",
            rds_serves_nonce: jQuery("#rds_serves_nonce").val(),
			RDserve_id: RDserve_id,
			questions: questions,
			passPercent: passPercent,
			passText: passText,
			failText: failText,
			rds_share_results: rds_share_results,
			rds_results_position: rds_results_position,
			rds_show_results: rds_show_results,
			rds_show_results_correct: rds_show_results_correct,
			rds_show_extra_text: rds_show_extra_text,
			rds_RDserve_timer: rds_RDserve_timer,
			rds_randomize_question_order: rds_randomize_question_order,
			rds_randomize_answer_order: rds_randomize_answer_order,
			rds_pool_of_questions: rds_pool_of_questions,
			rds_wp_paginate: rds_wp_paginate,
			rds_immediate_mark: rds_immediate_mark,
			rds_stop_answer_reselect:rds_stop_answer_reselect
        },
        url: ajaxurl,
        success: function(data) {
            if(data == "done"){
				rds_show_message("<p>This RDserve has been successfully updated.</p>");
			} else {
				rds_show_message("<p>There was an issue updating this RDserve</p>");
			}
        },
        error: function() {
            console.log("Permission denied");
			rds_show_message("<p>There was an issue updating this RDserve</p>");
        },
        complete: function() {
			rds_after_load();
        }
    });	
});

// view question
jQuery("#rds_form_wrapper").on("click", ".rds_RDserve_question, #rds_add_question", function(event) {	
	rds_start_load();
	let question_id = jQuery(this).attr("data-id");
	let RDserve_id = jQuery(this).attr("data-RDserve-id");
    jQuery.ajax({
        type: 'POST',
        data: {
            action: "rds_view_question",
            rds_serves_nonce: jQuery("#rds_serves_nonce").val(),
			question_id: question_id,
			RDserve_id: RDserve_id
        },
        url: ajaxurl,
        success: function(data) {
            jQuery("#rds_form_wrapper").html(data);
        },
        error: function() {
            console.log("Permission denied");
        },
        complete: function() {
			rds_after_load(true);
			let RDserve_id = jQuery("#rds_back_to_RDserve").attr("data-id");
			jQuery('#term_' + RDserve_id).prop("checked", true);
			
        }
    });
});

// add new RDserve
let rds_enter_notification = false;

	jQuery( "#save_serve_btn" ).click(function() {
	//alert('dddd');
	//e.preventDefault();
    //if(e.which == 13) {
    	let rds_RDserve_name = jQuery("#rds_new_RDserve_name").val();
    	if(rds_RDserve_name!=''){
		rds_enter_notification = false;
		rds_start_load();
		let rds_RDserve_name = jQuery("#rds_new_RDserve_name").val();
		if(rds_RDserve_name.length > 1){
			jQuery(".rds_input_notification").fadeOut();
			jQuery.ajax({
				type: 'POST',
				data: {
					action: "rds_add_new_RDserve",
					rds_serves_nonce: jQuery("#rds_serves_nonce").val(),
					rds_new_RDserve: rds_RDserve_name,
				},
				url: ajaxurl,
				success: function(data) {
					let new_RDserve = '<div class="rds_RDserve_item rds_RDserve_term" data-id="'+data+'">'+rds_RDserve_name+' <code>[HDRDserve RDserve = "'+data+'"]</code></div>';
					jQuery("#rds_list_serves").prepend(new_RDserve);
					jQuery("#rds_new_RDserve_name").val("");
					rds_show_message("<p>"+rds_RDserve_name+" has been added. Please select it below to edit RDserve settings and add questions.</p>");
				},
				error: function() {
					console.log("Permission denied");
				},
				complete: function() {
					rds_after_load(true);
				}
			});
		}
	} else {
		let content = jQuery("#rds_new_RDserve_name").val();
		if (content != "" && content != null) {
			rds_press_enter_notificiation("#rds_new_RDserve_name");
		} else {
			jQuery(".rds_input_notification").fadeOut();
			rds_enter_notification = false;
		}
	}
});


function rds_press_enter_notificiation(elem) {
	if (!rds_enter_notification) {
		rds_enter_notification = true;
		setTimeout(function() {
			let content = jQuery(elem).val();
			if (content != "" && content != null) {
				jQuery(elem).next(".rds_input_notification").fadeIn();
			}
		}, 3000);
	}
}

/* Save a question
------------------------------------------------------- */

// if save_current_question is clicked on
jQuery("#rds_wrapper").on("click", "#rds_save_question", function(){
	// check if a title has been entered
	if(jQuery("#rds_question_title").val() != "" && jQuery("#rds_question_title").val() != null){
		rds_get_question_values(false);
	} else {
		alert("please enter a title before saving")
	}
});

// if rds_delete_question is clicked on
jQuery("#rds_wrapper").on("click", "#rds_delete_question", function(){
	// check if a title has been entered
	let questionID = jQuery("#rds_delete_question").attr("data-id");
	data = '<p><strong>WARNING</strong>: This will permanently delete this question.\
	<div class="rds_button4" data-id="'+questionID+'" id="rds_delete_question_final">\
			<span class="dashicons dashicons-sticky"></span> DELETE\
		</div>\
	</p>';
	rds_show_message(data);
});
jQuery("#rds_wrapper").on("click", "#rds_delete_question_final", function(){
	let question_id = jQuery("#question_id").val();
	jQuery.ajax({
        type: 'POST',
        data: {
            action: "rds_delete_question",
            rds_serves_nonce: jQuery("#rds_serves_nonce").val(),
			question_id: question_id
        },
        url: ajaxurl,
        success: function(data) {
			jQuery("#rds_back_to_RDserve").click();
        },
        error: function() {
            console.log("Permission denied");
        },
        complete: function() {
			rds_after_load(true);
        }
    });	
});

// get array of attached serves
function get_RDserve_ids(){
	let RDserve_ids = [];
	jQuery("#rds_category_list input").each(function(){
		if(jQuery(this).prop('checked')){
			RDserve_ids.push(jQuery(this).attr("data-term"));
		}
	});
	return RDserve_ids;
}	


// get all values of the question
function rds_get_question_values(isNew){
	tinyMCE.triggerSave();
	let RDserve_ids = get_RDserve_ids();		
	let question_id = jQuery("#question_id").val();
	let title = jQuery("#rds_question_title").val();
	let image_based_answers = jQuery("#hdQue-post-class23").prop("checked");	
	if(image_based_answers){
		image_based_answers = "yes";
	} else {
		image_based_answers = "no";
	}
	let question_as_title = jQuery("#hdQue-post-class24").prop("checked");
	if(question_as_title){
		question_as_title = "yes";
	} else {
		question_as_title = "no";
	}
	let paginate = jQuery("#hdQue-post-class25").prop("checked");
	if(paginate){
		paginate = "yes";
	} else {
		paginate = "no";
	}
	let answers = [];
	jQuery("#rds_tab_content .rds_table tr").each(function(){		
		let answer_id = jQuery(this).children("td").children(".rds_input").attr("id");
		let answer_val = jQuery(this).children("td").children(".rds_input").val();
		let answer_image_meta = "";
		let answer_image_val = "";
		if(image_based_answers === "yes"){
			answer_image_val = jQuery(this).children("td").children(".rds_featured_image").attr("data-id");
			if(answer_image_val == 0){
				answer_image_val = jQuery(this).children("td.rds_use_image_as_answer").children("input").val();
			}
			answer_image_meta = jQuery(this).children("td.rds_use_image_as_answer").children("input").attr("id");
			if( typeof(answer_image_meta) === "undefined"){
				answer_image_meta = "";
			}
		}		
		answers.push([answer_id, answer_val, answer_image_meta, answer_image_val]);				
	});
	
	answers = JSON.stringify(answers);
	let answer_correct = jQuery("#hdQue-post-class2").val();
	let featured_image = jQuery("#rds_featured_image").attr("data-id");
	let tooltip = jQuery("#hdQue-post-class12").val();
	let extra_text = jQuery("#hdQue-post-class26").val();	
	
	jQuery.ajax({
        type: 'POST',
        data: {
            action: "rds_save_question",
            rds_serves_nonce: jQuery("#rds_serves_nonce").val(),
			question_id: question_id,
			RDserve_ids: RDserve_ids,
			title: title,
			image_based_answers: image_based_answers,
			question_as_title: question_as_title,
			paginate: paginate,
			answers: answers,
			answer_correct: answer_correct,
			featured_image: featured_image,
			tooltip: tooltip,
			extra_text: extra_text
        },
        url: ajaxurl,
        success: function(data) {
			let dataSuccess = data.split("|");
			if (typeof(dataSuccess) == "undefined"){
				console.log(data``)
			} else {
            	if(dataSuccess[0] == "updated"){
					data = '<p>This question has been successfully updated</p>';
					rds_show_message(data);
					// set the question ID so saving again doesn't create a new question
					jQuery("#rds_save_question").attr("data-id", dataSuccess[1]);
					jQuery("#question_id").val(dataSuccess[1]);
				} else {
					console.log(data);
				}
			}
        },
        error: function() {
            console.log("Permission denied");
        },
        complete: function() {
			rds_after_load(true);
        }
    });
}