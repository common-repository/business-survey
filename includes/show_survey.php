<?php
/**
 * The show_survey for displaying survey in front
 */

$rds_id = intval($RDserve); // RDserve ID
//echo $rds_id;die;
$rds_RDserve_options = rds_get_RDserve_options($rds_id);

$rds_results_position = sanitize_text_field($rds_RDserve_options["resultPos"]);
$rds_timer = intval($rds_RDserve_options["RDserveTimerS"]);
$rds_show_what_answers_were_right_wrong = sanitize_text_field($rds_RDserve_options["showResults"]); // highlights which selected answers were right or wrong
$rds_show_correct = sanitize_text_field($rds_RDserve_options["showResultsCorrect"]); // shows what the right answer was if user got it wrong
$rds_show_answer_text = sanitize_text_field($rds_RDserve_options["showIncorrectAnswerText"]); // show even if answe was correct

$rds_immediate_mark = @sanitize_text_field($rds_RDserve_options["immediateMark"]);
$rds_stop_answer_reselect = @sanitize_text_field($rds_RDserve_options["stopAnswerReselect"]);
if ($rds_immediate_mark == "") {
    $rds_immediate_mark = "no";
    $rds_stop_answer_reselect = "no";
}

$rds_question_order = sanitize_text_field($rds_RDserve_options["randomizeQuestions"]); // returns order for loop (menu_order, or random)
if ($rds_question_order === "no" || $rds_question_order === "" || $rds_question_order === null) {
    $rds_question_order = "menu_order";
}
$rds_random_answer_order = sanitize_text_field($rds_RDserve_options["randomizeAnswers"]);
$rds_use_pool = intval($rds_RDserve_options["pool"]);
$rds_paginate = intval($rds_RDserve_options["paginate"]);
$rds_pass_percent = intval($rds_RDserve_options["passPercent"]);
$rds_share_results = sanitize_text_field($rds_RDserve_options["shareResults"]);
$rds_twitter_handle = sanitize_text_field(get_option("rc_qu_tw"));
if ($rds_twitter_handle == "" || $rds_twitter_handle == null) {
    $rds_twitter_handle = "rcodehub";
}

// disable paginate if pool or timer is in use
if ($rds_use_pool > 0 || $rds_timer > 0) {
    $rds_paginate = 0;
}

$use_adcode = false;
$rds_adcode = get_option("rc_qu_adcode");
if ($rds_adcode != "" && $rds_adcode != null) {
    $rds_adcode = stripcslashes(urldecode($rds_adcode));
    $use_adcode = true;
}

wp_enqueue_style(
    'rds_admin_style',
    plugin_dir_url(__FILE__) . './css/rds_style.css',
    array(),
    "1.7.0"
);
wp_enqueue_script(
    'rds_admin_script',
    plugins_url('./js/rds_script.js', __FILE__),
    array('jquery'),
    '1.7.0',
    true
);

$rds_featured_image = "";
if (has_post_thumbnail()) {
    $rds_featured_image = wp_get_attachment_url(get_post_thumbnail_id(get_the_ID()), 'full');
}

// create object for localized script
$rds_local_vars = new \stdClass();
$rds_local_vars->rds_RDserve_id = $rds_id;
$rds_local_vars->rds_timer = $rds_timer;
$rds_local_vars->rds_show_what_answers_were_right_wrong = $rds_show_what_answers_were_right_wrong;
$rds_local_vars->rds_show_correct = $rds_show_correct;
$rds_local_vars->rds_show_answer_text = $rds_show_answer_text;
$rds_local_vars->rds_immediate_mark = $rds_immediate_mark;
$rds_local_vars->rds_stop_answer_reselect = $rds_stop_answer_reselect;
$rds_local_vars->rds_pass_percent = $rds_pass_percent;
$rds_local_vars->rds_share_results = $rds_share_results;
$rds_local_vars->rds_RDserve_permalink = get_the_permalink();
$rds_local_vars->rds_twitter_handle = $rds_twitter_handle;
$rds_local_vars->rds_RDserve_name = get_the_title();
$rds_local_vars->rds_ajax = admin_url('admin-ajax.php');
$rds_local_vars->rds_featured_image = $rds_featured_image;
$rds_local_vars->rds_submit = array();
do_action("rds_submit", $rds_local_vars);

$rds_local_vars = json_encode($rds_local_vars);
wp_localize_script('rds_admin_script', 'rds_local_vars', $rds_local_vars);

?>

<div class = "rds_RDserve_wrapper" id = "rds_<?php echo $rds_id; ?>">

    <div class = "rds_before">
		<?php do_action("rds_before", $rds_id);?>
	</div>


	<div class = "rds_RDserve">

<?php
if ($rds_results_position === "yes") {
    rds_get_results($rds_RDserve_options);
}

// start the query
wp_reset_postdata();
wp_reset_query();
global $post;
$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;

if ($rds_question_order == "rand" || $rds_paginate == 0) {
    $rds_paginate = "-1"; // set to infinite since we cannot paginate with rand
}
$RDservePagination = "true";
if ($rds_use_pool > 0) {
    $rds_paginate = $rds_use_pool; // set the posts-per-page to the RDserve pool amount
    $rds_question_order = "rand"; // force the RDserve to randomize the questions
    $RDservePagination = "false"; // disable WP pagination
}

// WP_Query arguments
$args = array(
    'post_type' => array('serve_type_questions'),
    'tax_query' => array(
        array(
            'taxonomy' => 'RDserve',
            'terms' => $rds_id,
        ),
    ),
    'pagination' => $RDservePagination, // true or false
    'posts_per_page' => $rds_paginate, // also used for the pool of questions
    'paged' => $paged,
    'orderby' => $rds_question_order, // defaults to menu_order
    'order' => 'ASC',
);

$query = new WP_Query($args);
$i = 0;
// figure out the starting question number
$questionNumber = 0;
if ($rds_paginate >= 1 && $paged > 1) {
    $questionNumber = ($paged * $rds_paginate) - $rds_paginate + 1;
}

// The Loop
if ($query->have_posts()) {
    while ($query->have_posts()) {
        $query->the_post();
        $i++;
        $rds_q_id = get_the_ID();
        $rds_selected = intval(get_post_meta($rds_q_id, 'hdQue_post_class2', true));
        $rds_image_as_answer = sanitize_text_field(get_post_meta($rds_q_id, 'hdQue_post_class23', true));
        $rds_question_as_title = sanitize_text_field(get_post_meta($rds_q_id, 'hdQue_post_class24', true));
        $rds_jpaginate = sanitize_text_field(get_post_meta($rds_q_id, 'hdQue_post_class25', true));
        $rds_tooltip = sanitize_text_field(get_post_meta($rds_q_id, 'hdQue_post_class12', true));
        $rds_after_answer = wp_kses_post(get_post_meta($rds_q_id, 'hdQue_post_class26', true));
        if ($rds_jpaginate === "yes") {
            rds_print_jPaginate($rds_id);
        }
        if ($rds_question_as_title === "yes") {
            $i = $i - 1; // reduce the question number
            rds_print_question_as_title($i, $rds_q_id, $rds_tooltip);
        } elseif ($rds_image_as_answer != "yes") {
            rds_print_question_normal($i, $rds_q_id, $rds_tooltip, $rds_after_answer, $rds_selected, $rds_random_answer_order);
        } elseif ($rds_image_as_answer === "yes") {
            rds_print_question_image($i, $rds_q_id, $rds_tooltip, $rds_after_answer, $rds_selected, $rds_random_answer_order);
        }

        if ($use_adcode) {
            if ($i % 4 == 0 && $i != 0) {
                echo $rds_adcode;
            }
        }
    }
}

wp_reset_postdata();
if ($query->max_num_pages > 1 || $rds_paginate != "-1") {
    if (isset($_GET['currentScore'])) {
        echo esc_html('<input type = "hidden" id = "rds_current_score" value = "' . sanitize_text_field($_GET['currentScore']) . '"/>');
    }
    if (isset($_GET['totalQuestions'])) {
        echo esc_html('<input type = "hidden" id = "rds_total_questions" value = "' . sanitize_text_field($_GET['totalQuestions']) . '"/>');
    }

    if ($rds_use_pool === 0) {
        if ($query->max_num_pages != $paged) {
            rds_print_next($rds_id, $paged);
        }
    } else {
        rds_print_finish($rds_id);
    }
} else {
    rds_print_finish($rds_id);
}

if ($query->max_num_pages == $paged) {
    rds_print_finish($rds_id);
}

if ($rds_results_position != "yes") {
    echo rds_get_results($rds_RDserve_options);
}

if ($rds_timer > 3) {
    echo '<div class = "rds_timer"></div>';
}
?>

        <div class = "rds_before">
            <?php do_action("rds_after", $rds_id);?>
        </div>

        <div class = "rds_loading_bar"></div>
	</div>
</div>
