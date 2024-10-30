<?php
/*
HD Survey Page
 */
wp_nonce_field('rds_serves_nonce', 'rds_serves_nonce');
?>

<div id = "rds_loading">
	<div id = "rds_loading_inner">
		<p>
			...
		</p>
	</div>

</div>
<div id = "rds_meta_forms">
	<div id = "rds_wrapper" class = "rds_serves_admin">
		<div id = "rds_message">
			<p></p>
		</div>
		<div id="rds_form_wrapper">
			<h1 id = "rds_h1_title">
				 Survey Dashboard
			</h1>
			<br/>


			<a href = "./edit-tags.php?taxonomy=RDserveCategory&post_type=serve_type_questions" class = "rds_button4"><span class="dashicons dashicons-add"></span> Add/Edit Survey Category</a>

			<a href = "./edit-tags.php?taxonomy=RDserve&post_type=serve_type_questions" class = "rds_button4"><span class="dashicons dashicons-add"></span> Add/Edit Survey</a>


			<a href = "./edit.php?post_type=serve_type_questions" class = "rds_button4"></span> <span class="dashicons dashicons-add"></span> Add/Edit Survey Questions</a>

		</div>
	</div>
</div>