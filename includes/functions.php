<?php
// general HDQ functions

// Gutenberg
function rds_register_block_box()
{
    if (!function_exists('register_block_type')) {
        // Gutenberg is not active.
        return;
    }
    wp_register_script(
        'hdq-block-RDserve',
        plugin_dir_url(__FILE__) . '/js/rds_block.js',
        array('wp-blocks', 'wp-i18n', 'wp-element', 'wp-editor'),
        plugin_dir_url(__FILE__) . '/js/rds_block.js'
    );
}
add_action('init', 'rds_register_block_box');

/* Get Survey list
 * used for the gutenberg block
------------------------------------------------------- */
function rds_get_RDserve_list()
{
    $taxonomy = 'RDserve';
    $term_args = array(
        'hide_empty' => false,
        'orderby' => 'name',
        'order' => 'ASC',
    );
    $tax_terms = get_terms($taxonomy, $term_args);
    $serves = array();
    if (!empty($tax_terms) && !is_wp_error($tax_terms)) {
        foreach ($tax_terms as $tax_terms) {
            $RDserve = new stdClass;
            $RDserve->value = $tax_terms->term_id;
            $RDserve->label = $tax_terms->name;
            array_push($serves, $RDserve);
        }
    }
    echo json_encode($serves);
    die();
}
add_action('wp_ajax_rds_get_RDserve_list', 'rds_get_RDserve_list');

/* Check acccess level
 * check if authors should be granted access
------------------------------------------------------- */
function rds_user_permission()
{
    $hasPermission = false;
    $authorsCan = sanitize_text_field(get_option("rc_qu_authors"));
    if ($authorsCan == "yes") {
        if (current_user_can('publish_posts')) {
            $hasPermission = true;
        }
    } else {
        if (current_user_can('edit_others_pages')) {
            $hasPermission = true;
        }
    }
    return $hasPermission;
}

/* Get Question Answer Meta
 * Returns array metaID, answer text, [featuredImageURL or ID]
------------------------------------------------------- */
function rds_get_answers($rds_id)
{

    $allowed_html = array(
        'strong' => array(),
        'em' => array(),
        'code' => array(),
        'sup' => array(),
        'sub' => array(),
    );

    $data = array();
    $rds_1_answer = wp_kses(get_post_meta($rds_id, 'hdQue_post_class1', true), $allowed_html);
    $rds_1_image = sanitize_text_field(get_post_meta($rds_id, 'hdQue_post_class13', true));
    array_push($data, array(1, $rds_1_answer, $rds_1_image));
    $rds_2_answer = wp_kses(get_post_meta($rds_id, 'hdQue_post_class3', true), $allowed_html);
    $rds_2_image = sanitize_text_field(get_post_meta($rds_id, 'hdQue_post_class14', true));
    array_push($data, array(3, $rds_2_answer, $rds_2_image));
    $rds_3_answer = wp_kses(get_post_meta($rds_id, 'hdQue_post_class4', true), $allowed_html);
    $rds_3_image = sanitize_text_field(get_post_meta($rds_id, 'hdQue_post_class15', true));
    array_push($data, array(4, $rds_3_answer, $rds_3_image));
    $rds_4_answer = wp_kses(get_post_meta($rds_id, 'hdQue_post_class5', true), $allowed_html);
    $rds_4_image = sanitize_text_field(get_post_meta($rds_id, 'hdQue_post_class16', true));
    array_push($data, array(5, $rds_4_answer, $rds_4_image));
    $rds_5_answer = wp_kses(get_post_meta($rds_id, 'hdQue_post_class6', true), $allowed_html);
    $rds_5_image = sanitize_text_field(get_post_meta($rds_id, 'hdQue_post_class17', true));
    array_push($data, array(6, $rds_5_answer, $rds_5_image));
    $rds_6_answer = wp_kses(get_post_meta($rds_id, 'hdQue_post_class7', true), $allowed_html);
    $rds_6_image = sanitize_text_field(get_post_meta($rds_id, 'hdQue_post_class18', true));
    array_push($data, array(7, $rds_6_answer, $rds_6_image));
    $rds_7_answer = wp_kses(get_post_meta($rds_id, 'hdQue_post_class8', true), $allowed_html);
    $rds_7_image = sanitize_text_field(get_post_meta($rds_id, 'hdQue_post_class19', true));
    array_push($data, array(8, $rds_7_answer, $rds_7_image));
    $rds_8_answer = wp_kses(get_post_meta($rds_id, 'hdQue_post_class9', true), $allowed_html);
    $rds_8_image = sanitize_text_field(get_post_meta($rds_id, 'hdQue_post_class20', true));
    array_push($data, array(9, $rds_8_answer, $rds_8_image));
    $rds_9_answer = wp_kses(get_post_meta($rds_id, 'hdQue_post_class10', true), $allowed_html);
    $rds_9_image = sanitize_text_field(get_post_meta($rds_id, 'hdQue_post_class21', true));
    array_push($data, array(10, $rds_9_answer, $rds_9_image));
    $rds_10_answer = wp_kses(get_post_meta($rds_id, 'hdQue_post_class11', true), $allowed_html);
    $rds_10_image = sanitize_text_field(get_post_meta($rds_id, 'hdQue_post_class22', true));
    array_push($data, array(11, $rds_10_answer, $rds_10_image));
    return $data;
}

function rds_get_featured_image_container($image)
{
    $image_ar = rds_get_featured_image($image);
    $data = '<div class = "rds_featured_image" data-id = "' . $image_ar[0] . '"><img src = "' . $image_ar[1] . '" alt = ""/></div>';
    return $data;
}

function rds_get_featured_image($image)
{
    $data = array();
    if (is_numeric($image)) {
        // if this uses image ID instead of URL
        $hdc_featured_image = wp_get_attachment_image_src($image, "thumbnail", false);
        $hdc_featured_image = $hdc_featured_image[0];
        $data = array($image, $hdc_featured_image);
    } else {
        // Created with old version of HD Survey
        if ($image != null && $image != "") {
            $data = array(0, $image);
        } else {
            $data = array(0, "https://via.placeholder.com/150x150?text=UPLOAD");
        }
    }
    return $data;
}

/* Returns object of all survey options
------------------------------------------------------- */
function rds_get_RDserve_options($rds_id)
{
    $term_meta = get_option("taxonomy_term_$rds_id");
    return $term_meta;
}

function rds_get_answer_image_url($image)
{
    if (is_numeric($image)) {
        // if this uses image ID instead of URL
        $image_url = wp_get_attachment_image_src($image, "rc_qu_size2", false);
        if ($image_url[0] == "" || $image_url[0] == null) {
            $image_url = wp_get_attachment_image_src($image, "thumbnail", false);
        }
        $image = $image_url[0];
        return $image;
    } else {
        // figure out what the original custom image size was
        // get the extention -400x400
        $image_parts = explode(".", $image);
        $image_extention = end($image_parts);
        unset($image_parts[count($image_parts) - 1]);
        $image_url = implode(".", $image_parts);
        $image_url = $image_url . '-400x400.' . $image_extention;
        return $image_url;
    }
}

/* Prints the result divs
------------------------------------------------------- */
function rds_get_results($rds_RDserve_options)
{
    $resultsPercent = sanitize_text_field(get_option("rc_qu_percent"));
    $pass = stripslashes(wp_kses_post($rds_RDserve_options["passText"]));
    $pass = apply_filters('the_content', $pass);
    $fail = stripslashes(wp_kses_post($rds_RDserve_options["failText"]));
    $fail = apply_filters('the_content', $fail);
    $result_text = sanitize_text_field(get_option("rc_qu_results"));
    $fb_appId = sanitize_text_field(get_option("rc_qu_fb"));
    if ($result_text == null || $result_text == "") {
        $result_text = "Results";
    }
    $shareResults = sanitize_text_field($rds_RDserve_options["shareResults"]);?>


	<div class = "rds_results_wrapper rds_question">
		<div class = "rds_results_inner">
			<h2><?php echo $result_text; ?></h2>
			<div class = "rds_result"><?php if ($resultsPercent == "yes") {echo ' - <span class = "rds_result_percent"></span>';}?></div>
			<div class = "rds_result_pass"><?php echo $pass; ?></div>
			<div class = "rds_result_fail"><?php echo $fail; ?></div>
			<?php
if ($shareResults === "yes") {
        ?>
					<div class = "rds_share">
						<?php
if ($fb_appId == "" || $fb_appId == null) {
            ?>
						<div class = "rds_social_icon">
							<a href="http://www.facebook.com/sharer/sharer.php?u=<?php echo the_permalink(); ?>&amp;title=Serve" target="_blank" class = "rds_facebook">
								<img src="<?php echo plugins_url('/images/fbshare.png', __FILE__); ?>" alt="Share your score!">
							</a>
						</div>
						<?php
} else {
            rc_get_fb_app_share($fb_appId);
        }
        ?>
						<div class = "rds_social_icon">
							<a href="#" target="_blank" class = "rds_twitter"><img src="<?php echo plugins_url('/images/twshare.png', __FILE__); ?>" alt="Tweet your score!"></a>
						</div>
					</div>
				<?php
}?>
		</div>
	</div>

	<?php
}

function rc_get_fb_app_share($fb_appId)
{
    ?>


	<script>
	  window.fbAsyncInit = function() {
		FB.init({
		  appId            : '<?php echo $fb_appId; ?>',
		  autoLogAppEvents : true,
		  xfbml            : true,
		  version          : 'v3.2'
		});
	  };

	  (function(d, s, id){
		 var js, fjs = d.getElementsByTagName(s)[0];
		 if (d.getElementById(id)) {return;}
		 js = d.createElement(s); js.id = id;
		 js.src = "https://connect.facebook.net/en_US/sdk.js";
		 fjs.parentNode.insertBefore(js, fjs);
	   }(document, 'script', 'facebook-jssdk'));

	</script>


	<div class = "rds_social_icon">
			<img id = "rds_fb_sharer" src="<?php echo plugins_url('/images/fbshare.png', __FILE__); ?>" alt="Share your score!">
	</div>


<?php
}

function rds_print_question_as_title($i, $rds_q_id, $rds_tooltip)
{
    $rds_answers = rds_get_answers($rds_q_id);?>
				<div class = "rds_question rds_question_title">
					<?php
if (has_post_thumbnail()) {
        echo '<div class = "rds_question_featured_image">';
        the_post_thumbnail();
        echo '</div>';
    }?>
					<h3><?php echo get_the_title($rds_q_id); ?></h3>
				</div>
	<?php
}

function rds_print_question_normal($i, $rds_q_id, $rds_tooltip, $rds_after_answer, $rds_selected, $rds_random_answer_order)
{
    $rds_answers = rds_get_answers($rds_q_id);
    // add the 'correct' to array in case we randomize
    @array_push($rds_answers[$rds_selected - 1], "checked");
    if ($rds_random_answer_order === "yes") {
        shuffle($rds_answers);
    }?>

				<div class = "rds_question" id = "rds_question_<?php echo $rds_q_id; ?>">
					<?php
if (has_post_thumbnail()) {
        echo '<div class = "rds_question_featured_image">';
        the_post_thumbnail();
        echo '</div>';
    }?>
					<h3>
                        <?php
$question_number = rds_get_paginate_question_number($i);
    echo '<span class = "rds_question_number">Q. ' . $question_number . ": </span> " . get_the_title($rds_q_id);
    if ($rds_tooltip != "" && $rds_tooltip != null) {
        echo '<span class="rds_tooltip rds_tooltip_question">?<span class="rds_tooltip_content"><span>' . $rds_tooltip . '</span></span></span>';
    }?>



    <?php 
    //echo $i."==".$rds_q_id."==".$rds_tooltip."==".$rds_after_answer."==".$rds_selected."==".$rds_random_answer_order; 
    //Added by ->Ramlal
    $term_obj_list = get_the_terms( $rds_q_id, 'RDserveCategory' );
    $terms_string  = join(', ', wp_list_pluck($term_obj_list, 'name'));
    //print_r($terms_string);
    ?>
    [Category : <?php echo $terms_string; ?>]
                    </h3>

                    
					<?php
$x = 0;
    foreach ($rds_answers as $answer) {
        if ($answer[1] != "" && $answer[1] != null) {
            $x = $x + 1;
            $rds_is_correct = "";
            if (array_key_exists(3, $answer)) {
                $rds_is_correct = "1";
            } else {
                $rds_is_correct = "0";
            }?>
								<div class = "rds_row">
									<label class="rds_label_answer" data-type = "radio" data-id = "rds_question_<?php echo $rds_q_id; ?>" for="rds_option_<?php echo $i . '_' . $x; ?>">
										<div class="hdq-options-check">
											<input type="checkbox" class="rds_option rds_check_input" value="<?php echo $rds_is_correct; ?>" name="rds_option_<?php echo $i . '_' . $x; ?>" id="rds_option_<?php echo $i . '_' . $x; ?>">
											<label for="rds_option_<?php echo $i . '_' . $x; ?>"></label>
										</div>
										<?php echo $answer[1]; ?>
									</label>
								</div>
							<?php
}
    }
    if ($rds_after_answer != "" && $rds_after_answer != null) {
        echo '<div class = "rds_question_after_text">';
        echo apply_filters('the_content', $rds_after_answer);
        echo '</div>';
    }?>
				</div>


	<?php
}

function rds_print_question_image($i, $rds_q_id, $rds_tooltip, $rds_after_answer, $rds_selected, $rds_random_answer_order)
{
    $rds_answers = rds_get_answers($rds_q_id);
    // add the 'correct' to array in case we randomize
    array_push($rds_answers[$rds_selected - 1], "checked");
    if ($rds_random_answer_order === "yes") {
        shuffle($rds_answers);
    }?>

				<div class = "rds_question rds_question_images" id = "rds_question_<?php echo $rds_q_id; ?>">
					<?php
if (has_post_thumbnail()) {
        echo '<div class = "rds_question_featured_image">';
        the_post_thumbnail();
        echo '</div>';
    }?>
					<h3>
						<?php
$question_number = rds_get_paginate_question_number($i);
    echo '<span class = "rds_question_number">#' . $question_number . "</span> " . get_the_title($rds_q_id);
    if ($rds_tooltip != "" && $rds_tooltip != null) {
        echo '<span class="rds_tooltip rds_tooltip_question">?<span class="rds_tooltip_content"><span>' . $rds_tooltip . '</span></span></span>';
    }?>
					</h3>
					<?php
$x = 0;
    foreach ($rds_answers as $answer) {
        if ($answer[1] != "" && $answer[1] != null) {
            $answer_image = rds_get_answer_image_url($answer[2]);
            $x = $x + 1;
            $rds_is_correct = "";
            if (array_key_exists(3, $answer)) {
                $rds_is_correct = "1";
            } else {
                $rds_is_correct = "0";
            }
            if ($x % 2 != 0) {
                echo '<div class = "rds_one_half">';
            } else {
                echo '<div class = "rds_one_half rds_last">';
            }?>

								<div class = "rds_row">
									<label class="rds_label_answer" data-type = "image" data-id = "rds_question_<?php echo $rds_q_id; ?>" for="rds_option_<?php echo $i . '_' . $x; ?>">
										<img src = "<?php echo $answer_image; ?>" alt = ""/>
										<div class="hdq-options-check">
											<input type="checkbox" class="rds_option rds_check_input" value="<?php echo $rds_is_correct; ?>" name="rds_option_<?php echo $i . '_' . $x; ?>" id="rds_option_<?php echo $i . '_' . $x; ?>">
											<label for="rds_option_<?php echo $i . '_' . $x; ?>"></label>
										</div>
										<?php echo $answer[1]; ?>
									</label>
								</div>
						</div>
							<?php
if ($x % 2 == 0) {
                echo '<div class = "clear"></div>';
            }
        }
    }
    if ($rds_after_answer != "" && $rds_after_answer != null) {
        echo '<div class = "rds_question_after_text">';
        echo apply_filters('the_content', $rds_after_answer);
        echo '</div>';
    }?>
				</div>


	<?php
}

function rds_print_jPaginate($rds_id)
{
    $next_text = sanitize_text_field(get_option("rc_qu_next"));
    if ($next_text == "" || $next_text == null) {
        $next_text = "next";
    }
    echo '<div class = "rds_jPaginate"><div class = "rds_next_button rds_button" data-id = "' . $rds_id . '">' . $next_text . '</div></div>';
}

function rds_print_finish($rds_id)
{
    $finish_text = sanitize_text_field(get_option("rc_qu_finish"));
    if ($finish_text == "" || $finish_text == null) {
        $finish_text = "finish";
    }
    echo '<div class = "rds_finish rds_jPaginate"><div class = "rds_finsh_button rds_button" data-id = "' . $rds_id . '">' . $finish_text . '</div></div>';
}

function rds_print_next($rds_id, $page_num)
{
    $next_text = sanitize_text_field(get_option("rc_qu_next"));
    if ($next_text == "" || $next_text == null) {
        $next_text = "next";
    }
    $page_num = $page_num + 1;
    $next_page_data = get_the_permalink();
    $next_page_data = $next_page_data . 'page/' . $page_num . '?currentScore=';
    echo esc_html('<div class = "rds_next_page"><a class = "rds_next_page_button rds_button" data-id = "' . $rds_id . '" href = "' . $next_page_data . '">' . $next_text . '</a></div>');
}

function rds_get_paginate_question_number($i)
{
    if (isset($_GET['totalQuestions'])) {
        return intval(sanitize_text_field($_GET['totalQuestions']) + $i);
    } else {
        return $i;
    }
}

function rds_save_question()
{
    if (rds_user_permission()) {
        $rds_nonce = sanitize_text_field($_POST['rds_serves_nonce']);
        if (wp_verify_nonce($rds_nonce, 'rds_serves_nonce') != false) {
            // permission granted
            $RDserve_ids = sanitize_text_field($_POST['RDserve_ids']);
            $RDserve_ids2 = $RDserve_ids;
            $RDserve_ids = array();
            foreach ($RDserve_ids2 as $q) {
                array_push($RDserve_ids, intval($q));
            }
            $question_id            = intval(sanitize_text_field($_POST['question_id']));
            $title                  = sanitize_text_field($_POST['title']);
            $image_based_answers    = sanitize_text_field($_POST['image_based_answers']);
            $question_as_title      = sanitize_text_field($_POST['question_as_title']);
            $paginate               = sanitize_text_field($_POST['paginate']);
            $answers                = sanitize_text_field($_POST['answers']);
            $answer_correct         = intval(sanitize_text_field($_POST['answer_correct']));
            $featured_image         = intval(sanitize_text_field($_POST['featured_image']));
            $tooltip                = sanitize_text_field($_POST['tooltip']);
            $extra_text             = wp_kses_post($_POST['extra_text']);
            $allowed_html = array(
                'strong' => array(),
                'em'    => array(),
                'code'  => array(),
                'sup'   => array(),
                'sub'   => array(),
            );

            if ($question_id == 0 || $question_id == "" || $question_id == null) {
                // get total count to set initial menu_order
                $men_order = get_term($RDserve_ids[0]);
                $men_order = $men_order->count + 1;

                $post_information = array(
                    'post_title' => $title,
                    'post_content' => '', // need to set as blank
                    'post_type' => 'serve_type_questions',
                    'post_status' => 'publish',
                    'menu_order' => intval($men_order),
                );
                $question_id = wp_insert_post($post_information);
            }

            $answers = json_decode(html_entity_decode(stripslashes($answers)), false);
            foreach ($answers as $answer) {
                $meta_key = sanitize_text_field($answer[0]);
                $meta_value = wp_kses($answer[1], $allowed_html);
                $meta_image_key = sanitize_text_field($answer[2]);
                $meta_image_value = sanitize_text_field($answer[3]);
                if ($meta_key != "" && $meta_key != null) {
                    $meta_key2 = str_replace("-", "_", $meta_key);
                    update_post_meta($question_id, $meta_key2, $meta_value, false);
                }
                if ($meta_image_key != "" && $meta_image_key != null) {
                    $meta_image_key2 = str_replace("-", "_", $meta_image_key);
                    update_post_meta($question_id, $meta_image_key2, $meta_image_value, false);
                }

            }
            update_post_meta($question_id, 'hdQue_post_class23', $image_based_answers, false);
            update_post_meta($question_id, 'hdQue_post_class24', $question_as_title, false);
            update_post_meta($question_id, 'hdQue_post_class25', $paginate, false);
            update_post_meta($question_id, 'hdQue_post_class2', $answer_correct, false);
            update_post_meta($question_id, 'hdQue_post_class12', $tooltip, false);
            update_post_meta($question_id, 'hdQue_post_class26', $extra_text, false);
            if ($featured_image > 0) {
                set_post_thumbnail($question_id, $featured_image);
            }

            // update post title too
            $rds_post = array(
                'ID' => $question_id,
                'post_title' => $title,
            );
            wp_update_post($rds_post);

            // set categoires
            $test = wp_set_post_terms($question_id, $RDserve_ids, "RDserve");
            echo 'updated|' . $question_id;

        } else {
            echo 'error: Nonce failed to validate'; // failed nonce
        }
    } else {
        echo 'error: You have insufficient user privilege'; // insufficient user privilege
    }
    die();
}
add_action('wp_ajax_rds_save_question', 'rds_save_question');

function rds_delete_question(){
    if (rds_user_permission()) {
        $rds_nonce = sanitize_text_field($_POST['rds_serves_nonce']);
        if (wp_verify_nonce($rds_nonce, 'rds_serves_nonce') != false) {
            // permission granted
            $question_id = intval(sanitize_text_field($_POST['question_id']));
            wp_delete_post($question_id); // will move to trash
        } else {
            echo 'error: Nonce failed to validate'; // failed nonce
        }
    } else {
        echo 'error: You have insufficient user privilege'; // insufficient user privilege
    }
    die();
}
add_action('wp_ajax_rds_delete_question', 'rds_delete_question');

function rds_add_new_RDserve(){
    if (rds_user_permission()) {
        $rds_nonce = sanitize_text_field($_POST['rds_serves_nonce']);
        if (wp_verify_nonce($rds_nonce, 'rds_serves_nonce') != false) {
            $rds_new_RDserve = sanitize_text_field($_POST['rds_new_RDserve']);
            $rds_new_RDserve = wp_insert_term(
                $rds_new_RDserve, // the term
                'RDserve' // the taxonomy
            );
            echo $rds_new_RDserve["term_id"];
        } else {
            echo 'permission denied';
        }
    } else {
        echo 'permission denied';
    }
    die();
}
add_action('wp_ajax_rds_add_new_RDserve', 'rds_add_new_RDserve');

function rds_print_RDserve_settings($RDserve_id)
{
    $RDserve_id = intval($RDserve_id);
    $term_meta = get_option("taxonomy_term_$RDserve_id");
    $term_meta = rds_return_RDserve_options($term_meta);
    ?>

		<h3>General Serve Options</h3>
		<div class = "rds_row">
			<label for = "rds_RDserve_pass_percent">Survey Pass Percentage</label>
			<input type = "number" name = "rds_RDserve_pass_percent" id = "rds_RDserve_pass_percent" class = "rds_input" min = "1" max = "100" value = "<?php echo $term_meta->passPercent; ?>"/>
		</div>

		<div class = "rds_row">
			<label for = "rds_RDserve_pass_text">Survey Pass Text</label>
			<?php wp_editor($term_meta->passText, "rc_RDserve_term_meta_passText", array('textarea_name' => 'rc_RDserve_term_meta_passText', 'teeny' => false, 'media_buttons' => true, 'textarea_rows' => 10, 'quicktags' => true));?>
		</div>

		<div class = "rds_row">
			<label for = "rds_RDserve_fail_text">Survey Fail Text</label>
			<?php wp_editor($term_meta->failText, "rc_RDserve_term_meta_failText", array('textarea_name' => 'rc_RDserve_term_meta_failText', 'teeny' => false, 'media_buttons' => true, 'textarea_rows' => 10, 'quicktags' => true));?>
		</div>

		<h3>Once Survey Has Been Completed</h3>

		<div class = "rds_one_half">
			<div class="rds_row rds_checkbox">
				<label class="rds_label_title" for="rds_share_results"> Share Survey Results</label>
				<div class="rds_check_row">
					<div class="hdq-options-check">
						<input type="checkbox" id="rds_share_results" value="yes" name="rds_share_results" <?php if ($term_meta->shareResults === "yes") {echo 'checked';}?>>
						<label for="rds_share_results"></label>
					</div>
				</div>
				<p>
					This option shows or hides the Facebook and Twitter share buttons that appears when a user completes the Survey.
				</p>
			</div>
		</div>
		<div class = "rds_one_half rds_last">
			<div class="rds_row rds_checkbox">
				<label class="rds_label_title" for="rds_results_position"> Show Results Above Survey</label>
				<div class="rds_check_row">
					<div class="hdq-options-check">
						<input type="checkbox" id="rds_results_position" value="yes" name="rds_results_position" <?php if ($term_meta->resultPos === "yes") {echo 'checked';}?>>
						<label for="rds_results_position"></label>
					</div>
				</div>
				<p>
					The site will automatically scroll to the position of the results.<br/>
				</p>
			</div>
		</div>
		<div class = "clear"></div>

		<hr/>
		<br/>

		<div class = "rds_one_half">
			<div class="rds_row rds_checkbox">
				<label class="rds_label_title" for="rds_show_results"> Highlight correct / incorrect selected answers on completion</label>
				<div class="rds_check_row">
					<div class="hdq-options-check">
						<input type="checkbox" id="rds_show_results" value="yes" name="rds_show_results" <?php if ($term_meta->showResults === "yes") {echo 'checked';}?>>
						<label for="rds_show_results"></label>
					</div>
				</div>
				<p>
					This will show the user which questions they got right, and which they got wrong.
				</p>
			</div>
		</div>
		<div class = "rds_one_half rds_last">
			<div class="rds_row rds_checkbox">
				<label class="rds_label_title" for="rds_show_results_correct"> Show the correct answers on completion</label>
				<div class="rds_check_row">
					<div class="hdq-options-check">
						<input type="checkbox" id="rds_show_results_correct" value="yes" name="rds_show_results_correct" <?php if ($term_meta->showResultsCorrect === "yes") {echo 'checked';}?>>
						<label for="rds_show_results_correct"></label>
					</div>
				</div>
				<p>
					This feature goes the extra step and shows what the correct answer was, in the case that the user selected the wrong one.
				</p>
			</div>
		</div>
		<div class = "clear"></div>

		<hr/>
		<br/>

		<div class="rds_row rds_checkbox">
			<label class="rds_label_title" for="rds_show_extra_text"> Always Show Incorrect Answer Text</label>
			<div class="rds_check_row">
				<div class="hdq-options-check">
					<input type="checkbox" id="rds_show_extra_text" value="yes" name="rds_show_extra_text" <?php if ($term_meta->showIncorrectAnswerText === "yes") {echo 'checked';}?>>
					<label for="rds_show_extra_text"></label>
				</div>
			</div>
			<p>
				Each indivdual question can have accompanying text that will show if the user selects the wrong answer. Enabling this feature will force this text to show even if the selected answer was correct.
			</p>
		</div>

		<h3>Extra Survey Options</h3>


		<div class = "rds_one_half">
			<div class="rds_row rds_checkbox">
				<label class="rds_label_title" for="rds_immediate_mark"> Immediately mark answer as correct or incorrect</label>
				<div class="rds_check_row">
					<div class="hdq-options-check">
						<input type="checkbox" id="rds_immediate_mark" value="yes" name="rds_immediate_mark" <?php if ($term_meta->immediateMark === "yes") {echo 'checked';}?>>
						<label for="rds_immediate_mark"></label>
					</div>
				</div>
				<p>
					Enabling this will show if the answer was right or wrong as soon as an answer has been selected.
				</p>
			</div>
		</div>

		<div class = "rds_one_half rds_last">
			<div class="rds_row rds_checkbox">
				<label class="rds_label_title" for="rds_stop_answer_reselect"> Stop users from changing their answers</label>
				<div class="rds_check_row">
					<div class="hdq-options-check">
						<input type="checkbox" id="rds_stop_answer_reselect" value="yes" name="rds_stop_answer_reselect" <?php if ($term_meta->stopAnswerReselect === "yes") {echo 'checked';}?>>
						<label for="rds_stop_answer_reselect"></label>
					</div>
				</div>
				<p>
					Enabling this will stop users from being able to change their answer once one has been selected.
				</p>
			</div>
		</div>

		<div class = "clear"></div>

		<hr/>
		<br/>


		<div class="rds_row">
			<label class="rds_label_title" for="rds_RDserve_timer"> Timer / Countdown</label>
			<input type="number" id="rds_RDserve_timer" name="rds_RDserve_timer" class = "rds_input" value = "<?php echo $term_meta->RDserveTimerS; ?>" min = "0" placeholder = "leave blank to disable"/>
			<p>
				Enter how many seconds total. So 3 minutes would be 180. Please note that the timer will NOT work if the below WP Pagination feature is being used.
			</p>
		</div>
		<hr/>
		<br/>

		<div class = "rds_one_half">
			<div class="rds_row rds_checkbox">
				<label class="rds_label_title" for="rds_randomize_question_order"> Randomize <u>Question</u> Order</label>
				<div class="rds_check_row">
					<div class="hdq-options-check">
						<input type="checkbox" id="rds_randomize_question_order" value="yes" name="rds_randomize_question_order" <?php if ($term_meta->randomizeQuestions === "yes") {echo 'checked';}?>>
						<label for="rds_randomize_question_order"></label>
					</div>
				</div>
				<p>
					Please note that randomizing the questions is NOT possible if the below WP Pagination feature is being used.<br/>
					<small>and also not a good idea to use this if you are using the "questions as title" option for any questions attached to this Survey</small>
				</p>
			</div>
		</div>
		<div class = "rds_one_half rds_last">
			<div class="rds_row rds_checkbox">
				<label class="rds_label_title" for="rds_randomize_answer_order"> Randomize <u>Answer</u> Order</label>
				<div class="rds_check_row">
					<div class="hdq-options-check">
						<input type="checkbox" id="rds_randomize_answer_order" value="yes" name="rds_randomize_answer_order" <?php if ($term_meta->randomizeAnswers === "yes") {echo 'checked';}?>>
						<label for="rds_randomize_answer_order"></label>
					</div>
				</div>
				<p>
					This feature will randomize the order that each answer is displayed and is compatible with WP Pagination.
				</p>
			</div>
		</div>
		<div class = "clear"></div>

		<hr/>
		<br/>

		<div class = "rds_one_half">
			<div class="rds_row">
				<label class="rds_label_title" for="rds_pool_of_questions"> Use Pool of Questions</label>
				<input type="number" min = "0" max = "100" class = "rds_input" id="rds_pool_of_questions" name="rds_pool_of_questions" value = "<?php echo $term_meta->pool; ?>">
				<p>
					If you want each Survey to randomly grab a number of questions from the Survey, then enter that amount here. So, for example, you might have 100 questions attached to this Survey, but entering 20 here will make the Survey randomly grab 20 of the questions on each load.
				</p>
			</div>
		</div>
		<div class = "rds_one_half rds_last">
			<div class="rds_row">
				<label class="rds_label_title" for="rds_wp_paginate"> WP Pagination</label>
				<input type="number" min = "0" max = "100" class = "rds_input" id="rds_wp_paginate" name="rds_wp_paginate" value = "<?php echo $term_meta->paginate; ?>">
				<p>
					NOTE: It is recommended to not use this feature unless necessary.<br/>
					<small>WP Paginate will force this number of questions per page, and force new page loads for each new question group. The <em>only</em> benefit of this is for additional ad views. The downside is reduced compatibility of features. It is recommended to use the "paginate" option on each question instead.</small>
				</p>
			</div>
		</div>
		<div class = "clear"></div>
		<p style = "text-align:center">
			<strong>Save these settings by selecting "SAVE Survey" located at the top of this page</strong>
		</p>
	<?php
}

function rds_return_RDserve_options($term_meta)
{
    $rds_settings = new \stdClass();

    $passPercent = intval($term_meta['passPercent']);

    if ($passPercent == 0 || $passPercent == null) {
        // since this isn't set, we know that the user has never saved the survey before
        // set all values to default
        $passPercent = 70;
        $passText = "";
        $failText = "";
        $shareResults = "yes";
        $resultPos = "yes";
        $showResults = "yes";
        $showResultsCorrect = "no";
        $showIncorrectAnswerText = "no";
        $RDserveTimerS = 0;
        $randomizeQuestions = "menu_order";
        $randomizeAnswers = "no";
        $pool = 0;
        $paginate = 0;
		$immediateMark = "no";
		$stopAnswerReselect = "no";
    } else {
        // continue getting data
        $passText = stripslashes(wp_kses_post($term_meta['passText']));
        $failText = stripslashes(wp_kses_post($term_meta['failText']));
        $shareResults = sanitize_text_field($term_meta['shareResults']);
        $resultPos = sanitize_text_field($term_meta['resultPos']);
        $showResults = sanitize_text_field($term_meta['showResults']);
        $showResultsCorrect = sanitize_text_field($term_meta['showResultsCorrect']);
        $showIncorrectAnswerText = sanitize_text_field($term_meta['showIncorrectAnswerText']);
        $RDserveTimerS = intval($term_meta['RDserveTimerS']);
        $randomizeQuestions = sanitize_text_field($term_meta['randomizeQuestions']);
        $randomizeAnswers = sanitize_text_field($term_meta['randomizeAnswers']);
        $pool = intval($term_meta['pool']);
        $paginate = intval($term_meta['paginate']);
		$immediateMark = sanitize_text_field($term_meta['immediateMark']);
		$stopAnswerReselect = sanitize_text_field($term_meta['stopAnswerReselect']);		
    }

    $rds_settings->passPercent = $passPercent;
    $rds_settings->passText = $passText;
    $rds_settings->failText = $failText;
    $rds_settings->shareResults = $shareResults;
    $rds_settings->resultPos = $resultPos;
    $rds_settings->showResults = $showResults;
    $rds_settings->showResultsCorrect = $showResultsCorrect;
    $rds_settings->showIncorrectAnswerText = $showIncorrectAnswerText;
    $rds_settings->RDserveTimerS = $RDserveTimerS;
    $rds_settings->randomizeQuestions = $randomizeQuestions;
    $rds_settings->randomizeAnswers = $randomizeAnswers;
    $rds_settings->pool = $pool;
    $rds_settings->paginate = $paginate;
	$rds_settings->immediateMark = $immediateMark;
	$rds_settings->stopAnswerReselect = $stopAnswerReselect;

    return $rds_settings;
}