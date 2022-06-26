<?php
	foreach($module_uploads as $module_upload):
?>
		<!-- Item -->
		<div class="item">
			<!-- Item wrapper -->
			<div class="item-wrapper">

				<!-- Image -->
				<img src="<?php
								load_photo($module_upload->main_photo->master_name,
											  270,
											  180);
							 ?>" width="270" height="180">
				<!-- Image END -->

				<!-- Top panel -->
				<div class="top-panel">

					<!-- Likes -->
					<div class="likes">
						<div class="count">
							<?php echo $module_upload->likes_count; ?>
						</div>

						<div class="label">
							<?php echo ($module_upload->likes_count == 1) ? "Like" : "Likes"; ?>
						</div>
					</div>
					<!-- Likes END -->

					<!-- Empty item -->
					<div class="spacer">
					</div>
					<!-- Empty item END -->

					<!-- Status -->
					<div class="item"
						  onclick="form_tools.confirmation_prompt.show('change_upload_status',
																					  'mydrive-form',
																					  'drive',
																					  'change_upload_status',
																					  'ajax<?php
							  																	echo "/" . $module_upload->id;
							  																	echo "/" . $selected_module;
							  																 ?>',
																					  this,
																					  'modal')">
						<?php
							if($module_upload->status == "enabled"):
						?>
								<div class="status-enabled-icon">
								</div>
						<?php
							else:
						?>
								<div class="status-disabled-icon">
								</div>
						<?php
							endif;
						?>
					</div>
					<!-- Status END -->

					<!-- Edit -->
					<a href="<?php public_link($selected_module . "/form/" . $module_upload->id); ?>">
						<div class="item">
							<div class="edit-icon">
							</div>
						</div>
					</a>
					<!-- Edit END -->

					<!-- Delete -->
					<div class="item">
						<div class="delete-icon"
							  onclick="form_tools.confirmation_prompt.show('delete_upload',
																						  'mydrive-form',
																						  'drive',
																						  'delete',
																						  'ajax<?php
								  																echo "/" . $module_upload->id;
																								echo "/" . $selected_module;
								  																echo "/" . $current_page;
								  																if($selected_subcategory)
																									echo "/" . $selected_subcategory->id;
								  																else if($selected_category)
																									echo "/" . $selected_category->id;
								  																 ?>',
																						  this,
																						  'modal')">
						</div>
					</div>
					<!-- Delete END -->

				</div>
				<!-- Top panel END -->

				<!-- View item link -->
				<a href="<?php render_mydrive_module_item_link($module_upload,
																			  $selected_module); ?>"
					onmouseover="html_tools.drive_list.item_over(this)"
					onmouseout="html_tools.drive_list.item_out(this)">

					<?php
						if($module_upload->moderated == "no"):
					?>
							<!-- Message -->
							<div class="middle-panel">
								<div class="item-locked-bg">
								</div>

								<div class="message">
									<div class="label">
										<?php
											if($module_upload->is_moderation_failed())
												echo "Moderation failed";
											else
												echo "Passing moderation";
										?>
									</div>

									<div class="sublabel">
										<?php
											if($module_upload->is_moderation_failed())
												echo $module_upload->moderation_fail_text;
											else
												echo "Please wait some time";
										?>
									</div>
								</div>
							</div>
							<!-- Message -->
					<?php
						else:
					?>
							<!-- Link filler -->
							<div class="link-filler">
							</div>
							<!-- Link filler END -->
					<?php
						endif;
					?>

					<!-- Bottom panel -->
					<div class="bottom-panel">

						<div class="heading">
							<div class="wrapper">
								<h3 class="trim-to-parent">
									<?php echo $module_upload->get_full_heading(); ?>
								</h3>
							</div>
						</div>

						<div class="comments">
							<div class="count">
								<?php echo $module_upload->comments_count; ?>
							</div>

							<div class="label">
								<?php echo ($module_upload->comments_count == 1) ? "comment" : "comments"; ?>
							</div>
						</div>

					</div>
					<!-- Bottom panel END -->
				</a>
				<!-- View item link -->

			</div>
			<!-- Item wrapper END -->
		</div>
		<!-- Item END -->
<?php
	endforeach;
?>

<?php
	if(!$module_uploads):
?>
		<!-- No uploads -->
		<div class="no-uploads">
			<div class="message">

				<div class="icon">
				</div>

				<div class="label">
					You haven't uploaded any <?php echo $no_uploads; ?> yet in this category.
				</div>

			</div>
		</div>
		<!-- No uploads END -->
<?php
	endif;
?>