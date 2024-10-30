<?php
/*
HD Survey Settings
 */

$opt_name1 = 'rc_qu_fb';
$opt_name2 = 'rc_qu_tw';
$opt_name3 = 'rc_qu_next';
$opt_name4 = 'rc_qu_finish';
$opt_name5 = 'rc_qu_questionName';
$opt_name6 = 'rc_qu_results';
$opt_name7 = 'rc_qu_authors';
$opt_name8 = 'rc_qu_percent';
$opt_name9 = 'rc_qu_adcode';

$hidden_field_name = 'rc_submit_hidden';
$data_field_name1 = 'rc_qu_fb';
$data_field_name2 = 'rc_qu_tw';
$data_field_name3 = 'rc_qu_next';
$data_field_name4 = 'rc_qu_finish';
$data_field_name5 = 'rc_questionName';
$data_field_name6 = 'rc_results';
$data_field_name7 = 'rc_qu_authors';
$data_field_name8 = 'rc_qu_percent';
$data_field_name9 = 'rc_qu_adcode';

//$rc_questionName = sanitize_text_field(get_option( 'rc_questionName' )); // depricated

// Read in existing option value from database
$opt_val1 = sanitize_text_field(get_option($opt_name1));
$opt_val2 = sanitize_text_field(get_option($opt_name2));
$opt_val3 = sanitize_text_field(get_option($opt_name3));
$opt_val4 = sanitize_text_field(get_option($opt_name4));
$opt_val5 = sanitize_text_field(get_option($opt_name5));
$opt_val6 = sanitize_text_field(get_option($opt_name6));
$opt_val7 = sanitize_text_field(get_option($opt_name7));
$opt_val8 = sanitize_text_field(get_option($opt_name8));
$opt_val9 = get_option($opt_name9);

// See if the user has posted us some information
if (isset($_POST['rds_about_options_nonce'])) {
    $rds_nonce = sanitize_text_field($_POST['rds_about_options_nonce']);
    if (wp_verify_nonce($rds_nonce, 'rds_about_options_nonce') != false) {
        // Read their posted value
        $opt_val1 = sanitize_text_field($_POST[$data_field_name1]);
        $opt_val2 = sanitize_text_field($_POST[$data_field_name2]);
        $opt_val3 = sanitize_text_field($_POST[$data_field_name3]);
        $opt_val4 = sanitize_text_field($_POST[$data_field_name4]);
        $opt_val5 = sanitize_text_field($_POST[$data_field_name5]);
        $opt_val6 = sanitize_text_field($_POST[$data_field_name6]);
        if (isset($_POST[$data_field_name7])) {
            $opt_val7 = sanitize_text_field($_POST[$data_field_name7]);
        } else {
            $opt_val7 = "no";
        }
        if (isset($_POST[$data_field_name8])) {
            $opt_val8 = sanitize_text_field($_POST[$data_field_name8]);
        } else {
            $opt_val8 = "no";
        }
        $opt_val9 = urlencode($_POST[$data_field_name9]);

        // Save the posted value in the database
        update_option($opt_name1, $opt_val1);
        update_option($opt_name2, $opt_val2);
        update_option($opt_name3, $opt_val3);
        update_option($opt_name4, $opt_val4);
        update_option($opt_name5, $opt_val5);
        update_option($opt_name6, $opt_val6);
        update_option($opt_name7, $opt_val7);
        update_option($opt_name8, $opt_val8);
        update_option($opt_name9, $opt_val9);
    }
}
?>
	<div id = "rds_meta_forms">
		<div id = "rds_message"></div>
		<div id = "rds_wrapper">
			<div id="rds_form_wrapper">
				<div class = "rds_tab rds_tab_active">

					<form id = "rds_settings" method="post">
						<h2 style = "display: inline-block">Settings</h2>
						<input type="submit" class="rds_button2" id="rds_save_settings" value="SAVE" style = "float:right;">
						<div class = "clear"></div>
						<input type="hidden" name="rds_submit_hidden" value="Y">
						<?php wp_nonce_field('rds_about_options_nonce', 'rds_about_options_nonce');?>
						<h3>
							Social Sharing
						</h3>
						<div class = "rds_one_half">
							<div class = "rds_row">
								<label for = "<?php echo $data_field_name1; ?>" >Facebook APP ID <span class="rds_tooltip rds_tooltip_question">?<span class="rds_tooltip_content"><span>This is needed to allow Facebook to share dynamic content - the results of the Survey. If this is not used, then Facebook will share the page without the results. </span></span></span></label>
								<input type = "text" name="<?php echo $data_field_name1; ?>" id="<?php echo $data_field_name1; ?>1" value="<?php echo $opt_val1; ?>" class = "rds_input" placeholder = "leave blank to use default sharing" />
							</div>
						</div>
						<div class = "rds_one_half rds_last">
							<div class = "rds_row">
								<label for = "<?php echo $data_field_name2; ?>" >Twitter Handle <span class="rds_tooltip rds_tooltip_question">?<span class="rds_tooltip_content"><span>This is used if you have sharing results enabled. The sent tweet will contain a mention to your account for extra exposure. </span></span></span></label>
								<input type = "text" name="<?php echo $data_field_name2; ?>" id="<?php echo $data_field_name2; ?>1" value="<?php echo $opt_val2; ?>" class = "rds_input" placeholder = "please do NOT include the @ symbol" />
							</div>
						</div>
						<div class = "clear"></div>
						<h3>
							Rename / Translate
						</h3>
						<div class = "rds_one_half">
							<div class = "rds_row">
								<label for = "<?php echo $data_field_name4; ?>" >Rename "Finish" Button</label>
								<input type = "text" name="<?php echo $data_field_name4; ?>" id="<?php echo $data_field_name4; ?>1" value="<?php echo $opt_val4; ?>" class = "rds_input" placeholder = "leave blank to use default" />
							</div>
						</div>
						<div class = "rds_one_half rds_last">
							<div class = "rds_row">
								<label for = "<?php echo $data_field_name3; ?>" >Rename "Next" Button</label>
								<input type = "text" name="<?php echo $data_field_name3; ?>" id="<?php echo $data_field_name3; ?>1" value="<?php echo $opt_val3; ?>" placeholder = "leave blank to use default" class = "rds_input"/>
							</div>
						</div>
						<div class = "clear"></div>
						<div class = "rds_one_half">
							<div class = "rds_row">
								<label for = "<?php echo $data_field_name5; ?>" >Rename "Question"</label>
								<input type = "text" name="<?php echo $data_field_name5; ?>" id="<?php echo $data_field_name5; ?>1" value="<?php echo $opt_val5; ?>" placeholder = "leave blank to use default" class = "rds_input"/>
							</div>
						</div>
						<div class = "rds_one_half rds_last">
							<div class = "rds_row">
								<label for = "<?php echo $data_field_name6; ?>" >Rename "Results"</label>
								<input type = "text"  name="<?php echo $data_field_name6; ?>" id="<?php echo $data_field_name6; ?>1" value="<?php echo $opt_val6; ?>" placeholder = "leave blank to use default" class = "rds_input"/>
							</div>
						</div>
						<div class = "clear"></div>


						<div class = "rds_one_half">
							<div class = "rds_row">
								<label for = "<?php echo $data_field_name7; ?>" >Allow Authors Role Access <span class="rds_tooltip rds_tooltip_question">?<span class="rds_tooltip_content"><span>By default, only Editors or Admins can add or edit questions. Enabling this will allow Authors to create survey as well.</span></span></span></label>

							<div class="rds_check_row">
								<div class="hdq-options-check">
									<input type="checkbox" id="<?php echo $data_field_name7; ?>" value="yes" name="<?php echo $data_field_name7; ?>" <?php if ($opt_val7 == "yes") {echo 'checked = ""';}?>>
									<label for="<?php echo $data_field_name7; ?>"></label>
								</div>
							</div>


							</div>
						</div>
						<div class = "rds_one_half rds_last">
							<div class = "rds_row">
								<label for = "<?php echo $data_field_name8; ?>" >Enable Percent Results <span class="rds_tooltip rds_tooltip_question">?<span class="rds_tooltip_content"><span>By default, survey will only show the score as a fraction (example: 9/10). Enabling this will also show the score as a percentage (example: 90%) </span></span></span></label>


							<div class="rds_check_row">
								<div class="hdq-options-check">
									<input type="checkbox" id="<?php echo $data_field_name8; ?>" value="yes" name="<?php echo $data_field_name8; ?>" <?php if ($opt_val8 == "yes") {echo 'checked = ""';}?>>
									<label for="<?php echo $data_field_name8; ?>"></label>
								</div>
							</div>

							</div>
						</div>
						<div class = "clear"></div>
						<div class = "rds_row">
							<label or = "<?php echo $data_field_name9; ?>">Adset code <span class="rds_tooltip rds_tooltip_question">?<span class="rds_tooltip_content"><span>If you are using Google Adsense or something similar, you can paste your ad code here. survey will display the ad after every 4th question.</span></span></span></label>
							<textarea class = "rds_input" id = "<?php echo $data_field_name9; ?>" placeholder = "paste ad code here" name = "<?php echo $data_field_name9; ?>"><?php echo stripcslashes(urldecode($opt_val9)); ?></textarea>
						</div>
					</form>
					
				</div>
			</div>
		</div>
	</div>
