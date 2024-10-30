/*
	Business Survey main script
*/

rds_local_vars = JSON.parse(rds_local_vars);
let rds_pass_percent = rds_local_vars.rds_pass_percent;
let rds_RDserve_id = rds_local_vars.rds_RDserve_id;
let rds_RDserve_name = rds_local_vars.rds_RDserve_name;
let rds_RDserve_permalink = rds_local_vars.rds_RDserve_permalink;
let rds_share_results = rds_local_vars.rds_share_results;
let rds_show_answer_text = rds_local_vars.rds_show_answer_text;
let rds_show_correct = rds_local_vars.rds_show_correct;
let rds_show_what_answers_were_right_wrong = rds_local_vars.rds_show_what_answers_were_right_wrong;
let rds_timer = rds_local_vars.rds_timer;
let rds_twitter_handle = rds_local_vars.rds_twitter_handle;
let rds_immediate_mark = rds_local_vars.rds_immediate_mark;
let rds_stop_answer_reselect = rds_local_vars.rds_stop_answer_reselect;
let rds_featured_image = rds_local_vars.rds_featured_image;
let rds_score = [];

let jPage = 0;
let rds_nextlink = "";
let rds_active_timer = false;
let rds_top = 0;
let rds_pass_or_fail = "";

jQuery(window).load(function() {
	rds_start();
});

function rds_start() {
	jQuery(".rds_results_wrapper").hide();
	let firstjpage = jQuery(".rds_jPaginate")[0];
	if (!jQuery(firstjpage).hasClass("rds_finish")) {
		jQuery(".rds_finish").hide();
		jQuery(".rds_jPaginate")
			.first()
			.fadeIn();
		jQuery(".rds_jPaginate")
			.nextAll(".rds_question")
			.hide();
	} else {
		jQuery(".rds_finish").show();
	}
	if (rds_timer > 3) {
		rds_active_timer = true;
		rds_start_timer();
	}
}

/* Survey Timer
------------------------------------------------------- */
function rds_start_timer() {
	function rds_decrease_timer() {
		if (rds_timer > 0 && rds_active_timer == true) {
			let minutes = parseInt(rds_timer / 60);
			minutes = minutes < 10 ? "0" + minutes : minutes;
			let seconds = rds_timer % 60;
			seconds = seconds < 10 ? "0" + seconds : seconds;
			let t = minutes + ":" + seconds;
			jQuery(".rds_timer").html(t);
			if (rds_timer > 10 && rds_timer < 30) {
				jQuery(".rds_timer").addClass("rds_timer_warning");
			} else if (rds_timer <= 10) {
				jQuery(".rds_timer").removeClass("rds_timer_warning");
				jQuery(".rds_timer").addClass("rds_timer_danger");
			}
			rds_timer = rds_timer - 1;
			setTimeout(rds_decrease_timer, 1000);
		} else {
			if (rds_active_timer == true) {
				// uh oh! Out of time
				jQuery(".rds_timer").html("0");
				jQuery(".rds_timer").removeClass("rds_timer_danger");
				jQuery(".rds_finsh_button").click();
				rds_active_timer = false;
			} else {
				// user finished in time
				jQuery(".rds_timer").removeClass("rds_timer_danger");
				jQuery(".rds_timer").removeClass("rds_timer_warning");
			}
		}
	}
	rds_decrease_timer();
}

/* 0nly allow 1 correct answer for a question
------------------------------------------------------- */
jQuery(".rds_label_answer").click(function() {
	// get the parent id
	rds_question_id = jQuery(this).attr("data-id");
	if (
		jQuery("#" + rds_question_id + " .rds_label_answer")
			.children(".hdq-options-check")
			.children(".rds_check_input")
			.is(":enabled")
	) {
		jQuery("#" + rds_question_id + " .rds_label_answer")
			.children(".hdq-options-check")
			.children(".rds_check_input")
			.prop("checked", false);
		jQuery(this)
			.children(".hdq-options-check")
			.children(".rds_check_input")
			.prop("checked", true);

		if (rds_stop_answer_reselect === "yes" || rds_immediate_mark === "yes") {
			// disable all inputs
			jQuery(
				jQuery("#" + rds_question_id + " .rds_label_answer")
					.children(".hdq-options-check")
					.children(".rds_check_input")
			).each(function() {
				this.disabled = true;
			});
		}

		if (rds_immediate_mark === "yes") {
			let inp = null;
			if (jQuery("#" + rds_question_id).hasClass("rds_question_images")) {
				inp = jQuery(jQuery(this)[0]).find(".rds_check_input")[0];
			} else {
				inp = jQuery(this)[0].children[0].children[0];
			}
			if (inp.value == 1) {
				jQuery(this).addClass("rds_correct");
			} else {
				jQuery(this).addClass("rds_wrong");
			}

			if (rds_show_what_answers_were_right_wrong === "yes") {
				jQuery(
					jQuery("#" + rds_question_id + " .rds_label_answer")
						.children(".hdq-options-check")
						.children(".rds_check_input")
				).each(function() {
					if (this.value == 1 && !this.parentNode.parentNode.classList.contains("rds_correct")) {
						this.parentNode.parentNode.classList.add("rds_correct_not_selected");
					}
				});
			}

			jQuery(jQuery("#" + rds_question_id))
				.find(".rds_question_after_text")
				.fadeIn();
		}
	}
});

jQuery(window).load(function() {
	// when an answer is selected
	jQuery(".rds_label_answer2").click(function() {
		// check if this question has already been answered
		let p = jQuery(this)
			.parent()
			.parent();
		if (jQuery(p).hasClass("rds_answered")) {
			return false;
		} else {
			jQuery(p).addClass("rds_answered");
			// check to see if answer was right
			if (jQuery(this)[0].children[0].children[0].value == 1) {
				jQuery(p).addClass("rds_answered_correct");
				jQuery(this).addClass("rds_correct");
			} else {
				jQuery(p).addClass("rds_answered_incorrect");
				jQuery(this).addClass("rds_wrong");
			}
			jQuery(p)
				.find(".rds_question_after_text")
				.fadeIn();

			let inp = jQuery(p).find(".rds_check_input");
			for (let i = 0; i < inp.length; i++) {
				inp[i].disabled = true;
				if (inp[i].value == 1 && !inp[i].classList.contains("rds_correct")) {
					inp[i].parentNode.parentNode.classList.add("rds_correct_not_selected");
				}
			}
		}
	});
});

/* WP-Pagination
------------------------------------------------------- */
jQuery(".rds_next_page_button").click(function(e) {
	jQuery(this).fadeOut();
	// update the next page link and attributes
	let rds_current_score = jQuery("#rds_current_score").val(); // get page load values
	let rds_total_questions = jQuery("#rds_total_questions").val(); // get page load values
	if (!rds_current_score) {
		rds_current_score = 0;
	}
	if (!rds_total_questions) {
		rds_total_questions = 0;
	}
	// add any correct answer to score
	jQuery(".rds_option").each(function() {
		if (jQuery(this).val() == 1 && jQuery(this).prop("checked")) {
			rds_current_score = parseInt(rds_current_score) + 1;
		}
	});

	// get how many new questions are on this page, excluding titles
	let total_questions_on_page = jQuery(".rds_question").length - jQuery(".rds_question_title").length - 1;
	rds_total_questions = parseInt(rds_total_questions) + parseInt(total_questions_on_page);

	let rds_nextlink = jQuery(".rds_next_page_button").attr("href");
	jQuery(".rds_next_page_button").attr(
		"href",
		rds_nextlink + rds_current_score + "&totalQuestions=" + rds_total_questions
	);
});

/* jPagination
------------------------------------------------------- */
jQuery(".rds_jPaginate .rds_next_button").click(function() {
	let rds_form_id = jQuery(this).attr("data-id");
	jQuery(".rds_jPaginate .rds_next_button").removeClass("rds_next_selected");
	jQuery(this).addClass("rds_next_selected");

	jQuery("#rds_" + rds_form_id + " .rds_jPaginate:visible")
		.prevAll("#rds_" + rds_form_id + " .rds_question")
		.hide();
	jQuery("#rds_" + rds_form_id + " .rds_jPaginate:eq(" + parseInt(jPage) + ")")
		.nextUntil("#rds_" + rds_form_id + " .rds_jPaginate ")
		.show();
	jPage = parseInt(jPage + 1);

	jQuery(this)
		.parent()
		.hide();
	jQuery("#rds_" + rds_form_id + " .rds_jPaginate:eq(" + parseInt(jPage) + ")").show();
	setTimeout(function() {
		let rds_RDserve_container = document.querySelector("#rds_" + rds_form_id);
		rds_RDserve_container = jQuery(rds_get_RDserve_parent_container(rds_RDserve_container));

		if (rds_RDserve_container[0].tagName === "DIV") {
			rds_top =
				jQuery(rds_RDserve_container).scrollTop() +
				jQuery(".rds_question:visible").offset().top -
				jQuery(".rds_question:visible").height() / 2 -
				40;
			jQuery(rds_RDserve_container).animate(
				{
					scrollTop: rds_top
				},
				550
			);
		} else {
			let overflowH = jQuery("html").css("overflow");
			let overflowB = jQuery("body").css("overflow");
			let rest = false;
			if (overflowH.indexOf("hidden") >= 0 || overflowB.indexOf("hidden") >= 0) {
				rest = true;
			}

			jQuery("html,body").css("overflow", "initial");

			jQuery("html,body").animate(
				{
					scrollTop: jQuery(".rds_question:visible").offset().top - 40
				},
				550
			);

			if (rest) {
				setTimeout(function() {
					jQuery("html").css("overflow", overflowH);
					jQuery("body").css("overflow", overflowB);
				}, 550);
			}
		}
	}, 50);
});

function rds_get_RDserve_parent_container(element, includeHidden) {
	var style = getComputedStyle(element);
	var excludeStaticParent = style.position === "absolute";
	var overflowRegex = includeHidden ? /(auto|scroll|hidden)/ : /(auto|scroll)/;

	if (style.position === "fixed") return document.body;
	for (var parent = element; (parent = parent.parentElement); ) {
		style = getComputedStyle(parent);
		if (excludeStaticParent && style.position === "static") {
			continue;
		}
		if (overflowRegex.test(style.overflow + style.overflowY + style.overflowX)) return parent;
	}
	return document.body;
}

/* FINISH
------------------------------------------------------- */
jQuery(".rds_finsh_button").click(function() {
	rds_active_timer = false;
	let rds_form_id = jQuery(this).attr("data-id");
	jQuery(this).fadeOut();
	jQuery(".rds_loading_bar").addClass("rds_animate");

	function rds_calculate_total(rds_form_id) {
		// first, calculate the total
		let total_score = jQuery("#rds_current_score").val(); // get page load values
		let total_questions = jQuery("#rds_total_questions").val(); // get page load values
		if (!total_score) {
			total_score = 0;
		}
		if (!total_questions) {
			total_questions = 0;
		}
		// get score
		jQuery("#rds_" + rds_form_id + " .rds_option").each(function() {
			if (jQuery(this).val() == 1 && jQuery(this).prop("checked")) {
				total_score = parseInt(total_score) + 1;
			}
		});

		// get total questions
		total_questions =
			parseInt(jQuery("#rds_" + rds_form_id + " .rds_question").length) -
			parseInt(jQuery("#rds_" + rds_form_id + " .rds_question_title").length) -
			1 +
			parseInt(total_questions);

		let data = total_score + " / " + total_questions;
		rds_score = [total_score, total_questions];

		if (jQuery("#rds_" + rds_form_id + " .rds_results_inner .rds_result .rds_result_percent")[0]) {
			let rds_results_percent = (parseFloat(total_score) / parseFloat(total_questions)) * 100;
			rds_results_percent = Math.ceil(rds_results_percent);
			data =
				'<span class = "rds_result_fraction">' +
				data +
				'</span> - <span class = "rds_result_percent">' +
				rds_results_percent +
				"%</span>";
		}

		jQuery("#rds_" + rds_form_id + " .rds_results_inner .rds_result").html(data);
		let pass_percent = 0;
		pass_percent = total_score / total_questions;
		pass_percent = pass_percent * 100;
		if (pass_percent >= rds_pass_percent) {
			jQuery("#rds_" + rds_form_id + " .rds_result_pass").show();
			rds_pass_or_fail = "pass";
		} else {
			jQuery("#rds_" + rds_form_id + " .rds_result_fail").show();
			rds_pass_or_fail = "fail";
		}

		if (rds_share_results === "yes") {
			rds_create_share_link(rds_form_id, total_score, total_questions);
		}

		jQuery("#rds_" + rds_form_id + " .rds_results_wrapper").fadeIn();
		if (rds_show_what_answers_were_right_wrong === "yes" || rds_show_correct === "yes") {
			rds_show_all_questions(rds_form_id);
			if (rds_show_what_answers_were_right_wrong === "yes") {
				rds_set_correct_incorrect(rds_form_id);
			}
		} else {
			rds_scroll_to_results(rds_form_id);
		}

		rds_show_extra_question_info(rds_form_id);

		// Action onSubmit Survey: TODO: Async Await this for better compatibility
		if (typeof rds_local_vars.rds_submit != undefined && rds_local_vars.rds_submit != null) {
			for (let i = 0; i < rds_local_vars.rds_submit.length; i++) {
				rds_onSubmit(rds_local_vars.rds_submit[i]);
			}
		}
	}

	function rds_create_share_link(rds_form_id, total_score, total_questions) {
		function rds_create_twitter_share(rds_form_id, total_score, total_questions) {
			let baseURL = "https://twitter.com/intent/tweet?screen_name=";
			let shareText =
				total_score + "/" + total_questions + " on the " + rds_RDserve_name + " RDserve. Can you beat me? ";
			shareText = encodeURI(shareText);
			let shareLink = baseURL + rds_twitter_handle + "&text=" + shareText + encodeURI(rds_RDserve_permalink);
			jQuery("#rds_" + rds_form_id + " .rds_twitter").attr("href", shareLink);
		}
		rds_create_twitter_share(rds_form_id, total_score, total_questions);
	}

	function rds_show_extra_question_info(rds_form_id) {
		// only run if there is a question with extra info
		if (jQuery("#rds_" + rds_form_id + " .rds_question_after_text")[0]) {
			if (rds_show_answer_text === "yes") {
				jQuery("#rds_" + rds_form_id + " .rds_question_after_text").fadeIn();
			} else {
				jQuery("#rds_" + rds_form_id + " .rds_option").each(function() {
					if (jQuery(this).prop("checked") && jQuery(this).val() != 1) {
						let rds_parent_question = jQuery(this).closest(".rds_question");
						if (jQuery(rds_parent_question).children(".rds_question_after_text")[0]) {
							jQuery(rds_parent_question)
								.children(".rds_question_after_text")
								.fadeIn();
						}
					}
				});
			}
		}
	}

	function rds_set_correct_incorrect(rds_form_id) {
		jQuery("#rds_" + rds_form_id + " .rds_option").each(function() {
			if (jQuery(this).prop("checked")) {
				if (jQuery(this).val() == 1) {
					jQuery(this)
						.closest(".rds_label_answer")
						.addClass("rds_correct");
				} else {
					jQuery(this)
						.closest(".rds_label_answer")
						.addClass("rds_wrong");
				}
			} else {
				if (rds_show_correct === "yes") {
					if (jQuery(this).val() == 1) {
						jQuery(this)
							.closest(".rds_label_answer")
							.addClass("rds_correct_not_selected");
					}
				}
			}
		});
	}

	function rds_show_all_questions(rds_form_id) {
		jQuery("#rds_" + rds_form_id + " .rds_question").fadeIn();
		setTimeout(function() {
			rds_scroll_to_results(rds_form_id);
		}, 1000);
	}

	function rds_scroll_to_results(rds_form_id) {
		console.log("rds_scroll_to_results called");
		// this is super not accurate, but covers most themes.
		setTimeout(function() {
			let rds_RDserve_container = document.querySelector("#rds_" + rds_form_id);
			rds_RDserve_container = jQuery(rds_get_RDserve_parent_container(rds_RDserve_container));
			console.log("container:");
			console.log(rds_RDserve_container);

			if (rds_RDserve_container[0].tagName === "DIV") {
				rds_top =
					jQuery(rds_RDserve_container).scrollTop() +
					jQuery(".rds_results_wrapper").offset().top -
					jQuery(".rds_results_wrapper").height() / 2 -
					80;
				console.log("rds_top: " + rds_top);
				jQuery(rds_RDserve_container).animate(
					{
						scrollTop: rds_top
					},
					550
				);
				jQuery("html,body").animate(
					{
						scrollTop: rds_top
					},
					550
				);
			} else {
				let overflowH = jQuery("html").css("overflow");
				let overflowB = jQuery("body").css("overflow");
				let rest = false;
				if (overflowH.indexOf("hidden") >= 0 || overflowB.indexOf("hidden") >= 0) {
					rest = true;
				}

				jQuery("html,body").css("overflow", "initial");

				jQuery("html,body").animate(
					{
						scrollTop: jQuery(".rds_question:visible").offset().top - 40
					},
					550
				);

				if (rest) {
					setTimeout(function() {
						jQuery("html").css("overflow", overflowH);
						jQuery("body").css("overflow", overflowB);
					}, 550);
				}
			}
		}, 50);
	}

	rds_calculate_total(rds_form_id);

	function rds_onSubmit(action) {
		console.log(action);
		let data = {};
		// if this is also a JS function, store data
		if (typeof window[action] !== "undefined") {
			data.extra = window[action]();
		}

		// small delay since this isn't syncronous
		setTimeout(function() {
			data.RDserveID = rds_form_id;
			data.score = rds_score;
			// send data to admin-ajax
			jQuery.ajax({
				type: "POST",
				data: {
					action: action,
					data: data
				},
				url: rds_local_vars.rds_ajax,
				success: function(res) {
					console.log(res);
				}
			});
		}, 50);
	}
});

/* FB APP */
jQuery("#rds_fb_sharer").click(function() {
	let rds_score = jQuery(".rds_result").text();
	// check if there was an image added to pass or fail text

	/*
	// Facebook no longer allows us to send custom images :()
	let rds_share_image = jQuery(".rds_result_" + rds_pass_or_fail)
		.find("img")
		.attr("src");
	if (rds_share_image != "" && rds_share_image != null) {
		// for things like jetpack proton images
		if (rds_share_image.startsWith("//")) {
			console.log("image starts with // : fixing");
			rds_share_image = "http:" + rds_share_image;
		}
	} else {
		// no images in success or fail area
		rds_share_image = rds_featured_image;
	}
	*/

	FB.ui(
		{
			method: "share",
			href: rds_RDserve_permalink,
			hashtag: "#hdRDserve",
			quote: "I scored " + rds_score + " on " + rds_RDserve_name + ". Can you beat me?"
		},
		function(res) {}
	);
});
