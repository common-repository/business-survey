<?php
/*
    HD Survey Meta Data and Pages
    This creates all of the custom meta for Survey,
    and creates the serve_type_questions pages
*/

/* Add custom metabox to serve_type_questions pages
------------------------------------------------------- */
function rds_create_rds_form_page()
{
    function rds_meta_RDserve_setup()
    {
        add_action('add_meta_boxes', 'rds_add_meta_RDserve');
        add_action('save_post', 'rds_save_RDserve_meta', 10, 2);
    }
    add_action('load-post.php', 'rds_meta_RDserve_setup');
    add_action('load-post-new.php', 'rds_meta_RDserve_setup');

    function rds_add_meta_RDserve()
    {
        add_meta_box(
            'rds_meta_RDserve',
            esc_html__('Business Survey', 'example'),
            'rds_meta_RDserve',
            'serve_type_questions',
            'normal',
            'default'
        );
    }
}
rds_create_rds_form_page();

/* Business Survey page meta
------------------------------------------------------- */
function rds_meta_RDserve($object, $box)
{
    wp_nonce_field('rds_meta_RDserve_nonce', 'rds_meta_RDserve_nonce');
    $rds_id =  get_the_ID();
    $rds_selected = intval(get_post_meta($rds_id, 'hdQue_post_class2', true));
    $rds_image_as_answer = sanitize_text_field(get_post_meta($rds_id, 'hdQue_post_class23', true));
    $rds_question_as_title = sanitize_text_field(get_post_meta($rds_id, 'hdQue_post_class24', true));
    $rds_paginate = sanitize_text_field(get_post_meta($rds_id, 'hdQue_post_class25', true));
    $rds_tooltip = sanitize_text_field(get_post_meta($rds_id, 'hdQue_post_class12', true));
    $rds_after_answer = wp_kses_post(get_post_meta($rds_id, 'hdQue_post_class26', true));
    $rds_answers = rds_get_answers($rds_id); 
?>
<input type = "hidden" name="hdQue-post-class2" id="hdQue-post-class2" value="<?php echo $rds_selected; ?>" />
<div id = "rds_message"></div>
	<div id = "rds_wrapper">
		<div id="rds_form_wrapper">
			<div class = "rds_one_third">
				<div class="rds_row rds_checkbox">
					<label class="rds_label_title" for="hdQue-post-class23"> Image Based Answers &nbsp;&nbsp;<span class="rds_tooltip rds_tooltip_question">?<span class="rds_tooltip_content"><span>Enable this if you want a user to select an image as their answer.</span></span></span></label>
					<div class="rds_check_row"><label class="non-block" for="hdQue-post-class23"></label>
						<div class="hdq-options-check">
							<input type="checkbox" id="hdQue-post-class23" value="yes" name="hdQue-post-class23" <?php if ($rds_image_as_answer == "yes") {
        echo 'checked';
    } ?>/>
							<label for="hdQue-post-class23"></label>
						</div>
					</div>
				</div>
			</div>
			<div class = "rds_one_third">
				<div class="rds_row rds_checkbox">
					<label class="rds_label_title" for="hdQue-post-class24"> Question as Title &nbsp;&nbsp;<span class="rds_tooltip rds_tooltip_question">?<span class="rds_tooltip_content"><span>Enable this if you want to use this question as a title or heading.</span></span></span></label>
					<div class="rds_check_row"><label class="non-block" for="hdQue-post-class24"></label>
						<div class="hdq-options-check">
							<input type="checkbox" id="hdQue-post-class24" value="yes" name="hdQue-post-class24" <?php if ($rds_question_as_title == "yes") {
        echo 'checked';
    } ?>>
							<label for="hdQue-post-class24"></label>
						</div>
					</div>
				</div>
			</div>
			<div class = "rds_one_third rds_last">
				<div class="rds_row rds_checkbox">
					<label class="rds_label_title" for="hdQue-post-class25"> Paginate &nbsp;&nbsp;<span class="rds_tooltip rds_tooltip_question">?<span class="rds_tooltip_content"><span>Start a new page with this question (jQuery pagination) (user will need to select "next" to see this question or ones below it)</span></span></span></label>
					<div class="rds_check_row"><label class="non-block" for="hdQue-post-class25"></label>
						<div class="hdq-options-check">
							<input type="checkbox" id="hdQue-post-class25" value="yes" name="hdQue-post-class25" <?php if ($rds_paginate == "yes") {
        echo 'checked';
    } ?>>
							<label for="hdQue-post-class25"></label>
						</div>
					</div>
				</div>
			</div>
			<div class = "clear"></div>

			<br/>
			<div id = "rds_tab_wrapper" style = "<?php if ($rds_question_as_title == "yes") {
        echo 'display: none;';
    } ?>">


			<div id="rds_tabs">
				<ul>
					<li class="rds_active_tab" data-hdq-content="rds_tab_content">Main</li>
					<li data-hdq-content="rds_tab_extra">Extra</li>
				</ul>
				<div class="clear"></div>
			</div>
			<div id = "rds_tab_content" class = "rds_tab rds_tab_active">
				<?php
                    if ($rds_image_as_answer === "yes") {
                        $rds_image_as_answer = "rds_use_image_as_answer";
                    } else {
                        $rds_image_as_answer = "";
                    } ?>
				<table class="rds_table">
					<thead>
						<tr>
							<th>#</th>
							<th>Options</th>
							<th width = "150" class = "rds_answer_as_image <?php echo $rds_image_as_answer; ?>">Featured Image</th>
							<th width="30">Correct</th>
						</tr>
					</thead>
					<tbody>
						<?php
                            // print the rows we have data for
                            $x = 0;
    foreach ($rds_answers as $answer) {
        $x = $x + 1; ?>
						<tr>
							<td width = "1"><?php echo $x; ?></td>
							<td>
								<input class="rds_input" type="text" name="hdQue-post-class<?php echo $answer[0]; ?>" id="hdQue-post-class<?php echo $answer[0]; ?>" value="<?php echo $answer[1]; ?>" />
							</td>
							<td class="textCenter rds_answer_as_image <?php echo $rds_image_as_answer; ?>">
								<?php echo rds_get_featured_image_container($answer[2]); ?>
								<input type = "hidden" id = "hdQue-post-class<?php echo $x + 12; ?>" name = "hdQue-post-class<?php echo $x + 12; ?>" value = "<?php echo $answer[2]; ?>"/>
							</td>
							<td class="textCenter">
								<div class="hdq-options-check">
									<input type="checkbox" class="rds_correct" value="yes" name="rds_correct_<?php echo $x; ?>" id = "rds_correct_<?php echo $x; ?>" <?php if ($rds_selected === $x) {
            echo 'checked';
        } ?>/>
									<label for="rds_correct_<?php echo $x; ?>"></label>
								</div>
							</td>
						</tr>
						<?php
    } ?>

					</tbody>
				</table>

			</div>
			<div id = "rds_tab_extra" class = "rds_tab">
				<h3>Extra Question Options</h3>
				<div class = "rds_row">
					<label for = "hdQue-post-class12">Tooltip Text &nbsp;&nbsp;<span class="rds_tooltip rds_tooltip_question">?<span class="rds_tooltip_content"><span>This bubble is an example of a tool tip :) </span></span></span></label>
					<input class="rds_input" type="text" name="hdQue-post-class12" id="hdQue-post-class12" value="<?php echo $rds_tooltip; ?>" />
				</div>

				<div class = "rds_row">
					<label for ="hdQue-post-class26">Text that appears if answer was wrong</label>
					<?php
                        wp_editor($rds_after_answer, "hdQue-post-class26", array('textarea_name' => 'hdQue-post-class26','teeny' => true, 'media_buttons' => false, 'textarea_rows' => 3, 'quicktags' => false)); ?>

				</div>
			</div>
		</div>
			</div>
	</div>
<?php
}



function rds_save_RDserve_meta($post_id, $post)
{
    if (isset($_POST[ 'rds_meta_RDserve_nonce' ])) {
        $rds_nonce = $_POST[ 'rds_meta_RDserve_nonce' ];
        if (wp_verify_nonce($rds_nonce, 'rds_meta_RDserve_nonce') != false) {
            $post_type = get_post_type_object($post->post_type);
            if (current_user_can($post_type->cap->edit_post, $post_id)) {
                $new_meta_value = array();
                $meta_key = array();
                $new_meta_value = array();
                for ($i=1; $i<=26; $i++) {
                    $new_meta_value[$i] = $_POST['hdQue-post-class'.$i];
                    if ($i < 26) {
                        $new_meta_value[$i] = sanitize_text_field($new_meta_value[$i]);
                    } else {
                        // for hdQue-post-class26 -> the editor
                        $new_meta_value[$i] = wp_kses_post($new_meta_value[$i]);
                    }
                    $meta_key[$i] = 'hdQue_post_class'.$i;
                    $meta_value[$i] = get_post_meta($post_id, $meta_key[$i], true);
                    if ($new_meta_value[$i] && '' == $meta_value[$i]) {
                        add_post_meta($post_id, $meta_key[$i], $new_meta_value[$i], true);
                    } elseif ($new_meta_value[$i] && $new_meta_value[$i] != $meta_value[$i]) {
                        update_post_meta($post_id, $meta_key[$i], $new_meta_value[$i]);
                    } elseif ('' == $new_meta_value[$i] && $meta_value[$i]) {
                        delete_post_meta($post_id, $meta_key[$i], $meta_value[$i]);
                    }
                }
            }
        }
    }
}
?>
