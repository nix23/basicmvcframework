<!-- Profile -->
<div id="profile">

	<!-- Ajax requests token -->
	<form name='view-profile'>
		<input type='hidden' name='token[name]'  value='view-profile'>
		<input type='hidden' name='token[value]' value='<?php token('view-profile'); ?>'>
	</form>
	<!-- Ajax request token END -->

	<!-- Heading -->
	<div class="heading">
		<div class="wrapper">

			<!-- Legend -->
			<div class="legend">

				<div class="label">
					Profile
				</div>

				<div class="sublabel">
					Viewing <?php echo $user->username; ?> profile.
				</div>

			</div>
			<!-- Legend END -->

			<!-- Actions -->
			<div class="actions">

				<?php
					if($authorized):
				?>
						<div class="follow-button"
							  onclick="ajax.process_form('view-profile',
							  									  'profile',
							  									  'change_follow_status',
							  									  'ajax/<?php echo $user->id; ?>',
								  								  this,
								  								  'modal')">
							<div class="name">
								<?php echo ($is_user_followed_by_viewer) ? "Unfollow" : "Follow"; ?>
							</div>
						</div>
				<?php
					else:
				?>
						<div class="follow-button"
							  onclick="form_tools.default_errors.show(new Array('Please login to follow this user.'))">
							<div class="name">
								Follow
							</div>
						</div>
				<?php
					endif;
				?>

			</div>
			<!-- Actions END -->

		</div>
	</div>
	<!-- Heading END -->

	<!-- User info -->
	<div class="info">

		<!-- Avatar -->
		<div class="avatar">
			<?php
				if($user->has_avatar()):
			?>
					<img src="<?php load_photo($user->avatar_master_name, 85, 85); ?>">
			<?php
				else:
			?>
					<div class="no-avatar"></div>
			<?php
				endif;
			?>
		</div>
		<!-- Avatar END -->

		<!-- Legend -->
		<div class="legend">
			<div class="label">
				<div class="row">
					<?php echo $user->username; ?>
				</div>
				
				<div class="trimmer">
				</div>
			</div>

			<div class="sublabel">
				<?php
					if($user->has_subname())
						echo $user->subname;
					else
						time_on_site($user->registred_on);
				?>
			</div>
		</div>
		<!-- Legend END -->

		<!-- Description -->
		<div class="description">
			<div class="label">
				about
			</div>

			<div class="sublabel">
				<?php
					if($user->has_description()):
						if(mb_strlen($user->description, "UTF-8") < 115):
							echo $user->description;
						else:
				?>
							<span class="trimmed-description"
									onclick="form_tools.description.show()">
								<?php echo mb_substr($user->description, 0, 112, "UTF-8") . "..."; ?>
							</span>
				<?php
						endif;
					else:
						echo "This user hasn't added any description yet.";
					endif;
				?>
			</div>
		</div>
		<!-- Description END -->

		<!-- Rating -->
		<div class="rating">
			<div class="rank">
				<div class="number">
					<?php echo $user->rank; ?>
				</div>

				<div class="label">
					Fordriver
				</div>
			</div>

			<div class="stats">
				<div class="item">
					<div class="left">
						Views
					</div>

					<div class="right">
						<?php echo $user->user_posts_views_count; ?>
					</div>
				</div>

				<div class="item">
					<div class="left">
						Comments
					</div>

					<div class="right">
						<?php echo $user->comments_count_at_user_posts; ?>
					</div>
				</div>

				<div class="item">
					<div class="left">
						Likes
					</div>

					<div class="right">
						<?php echo $user->likes_count_at_user_posts; ?>
					</div>
				</div>

				<div class="item">
					<div class="left">
						Followers
					</div>

					<div class="right"
						  id="ajax-update-followers-count">
						<?php echo $user->user_followers_count; ?>
					</div>
				</div>
			</div>
		</div>
		<!-- Rating END -->

	</div>
	<!-- User info END -->

	<!-- Controls -->
	<div class="controls">

		<!-- Pagination -->
		<div class="pagination">
			<div class="wrapper">
				<?php
					foreach($pages as $page):
						if($page == $current_page):
				?>
							<div class="page selected">

								<span class="number">
									<?php echo $page; ?>
								</span>

							</div>
				<?php
						else:
				?>
							<a href="<?php
											profile_link($user->id,
														    $selected_module,
															 $page,
															 $selected_category,
															 $selected_subcategory);
										?>">
								<div class="page active">

									<span class="number">
										<?php echo $page; ?>
									</span>

								</div>
							</a>
				<?php
						endif;
					endforeach;
				?>
			</div>
		</div>
		<!-- Pagination END -->

		<!-- Modules list -->
		<div class="module-select">

			<div class="heading">
				View:
			</div>

			<?php
				foreach($modules as $module):
					if($module->selected):
			?>
						<div class="wrapper">
							<div class="item selected">

								<div class="label">
									<?php echo $module->label; ?>
								</div>

								<div class="count">
									<?php echo views_to_compact_form($module->count); ?>
								</div>

							</div>
						</div>
			<?php
					else:
			?>
						<a href="<?php
										profile_link($user->id,
														 $module->name,
														 1,
														 false,
														 false);
									?>">
							<div class="wrapper">
								<div class="item">

									<div class="label">
										<?php echo $module->label; ?>
									</div>

									<div class="count">
										<?php echo views_to_compact_form($module->count); ?>
									</div>

								</div>
							</div>
						</a>
			<?php
					endif;
				endforeach;
			?>

		</div>
		<!-- Modules list END -->

	</div>
	<!-- Controls END -->

	<!-- Content -->
	<div class="content">

		<!-- Items -->
		<div class="items trim-divs">
			<?php
				foreach($module_uploads as $module_upload):
			?>
					<!-- Item -->
					<div class="item">
						<a href="<?php render_profile_module_item_link($module_upload,
																					  $selected_module); ?>"
							onmouseover="html_tools.profile_list.item_over(this)"
							onmouseout="html_tools.profile_list.item_out(this)">
							<!-- Item wrapper -->
							<div class="item-wrapper">

								<!-- Image -->
								<img src="<?php
												load_photo($module_upload->main_photo->master_name,
															  270,
															  180);
											 ?>">
								<!-- Image END -->

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

							</div>
							<!-- Item wrapper END -->
						</a>
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
								This user hasn't uploaded any <?php echo $no_uploads; ?> yet.
							</div>

						</div>
					</div>
					<!-- No uploads END -->
			<?php
				endif;
			?>
			
			<div class="bottom-controls">
				<!-- Pagination -->
				<div class="pagination">
					<div class="wrapper">
						<?php
							foreach($pages as $page):
								if($page == $current_page):
						?>
									<div class="page selected">

										<span class="number">
											<?php echo $page; ?>
										</span>

									</div>
						<?php
								else:
						?>
									<a href="<?php
													profile_link($user->id,
																    $selected_module,
																	 $page,
																	 $selected_category,
																	 $selected_subcategory);
												?>">
										<div class="page active">

											<span class="number">
												<?php echo $page; ?>
											</span>

										</div>
									</a>
						<?php
								endif;
							endforeach;
						?>
					</div>
				</div>
				<!-- Pagination END -->
			</div>
		</div>
		<!-- Items END -->

		<!-- Categories list -->
		<div class="categories">
			<!-- Top separator -->
			<div class="separator-light">
			</div>
			<!-- Top separator END -->

			<!-- Subcategories list -->
			<?php
				if($selected_category
						and
					$selected_category->subcategories):
						foreach($selected_category->subcategories as $subcategory):
							if($selected_subcategory
									and
								$selected_subcategory->id == $subcategory->id):
			?>
								<div class="category selected">
									<span class="text">
										<?php echo $subcategory->name; ?>
									</span>
								</div>
			<?php
							else:
			?>
								<a href="<?php
												profile_link($user->id,
																 $selected_module,
																 1,
																 $selected_category,
																 $subcategory);
											?>">
									<div class="category active highlight">
										<span class="text">
											<?php echo $subcategory->name; ?>
										</span>
									</div>
								</a>
			<?php
							endif;
						endforeach;
			?>
								<!-- Separator -->
								<div class="separator">
								</div>
								<!-- Separator END -->
			<?php
				endif;
			?>
			<!-- Subcategories list END -->

			<!-- All item -->
			<?php
				if(!$selected_category and !$selected_subcategory):
			?>
					<div class="category selected">
						<span class="text">
							All
						</span>
					</div>
			<?php
				else:
			?>
					<a href="<?php
									profile_link($user->id,
													 $selected_module,
													 1,
													 false,
													 false);
								?>">
						<div class="category active">
							<span class="text">
								All
							</span>
						</div>
					</a>
			<?php
				endif;
			?>
			<!-- All item END -->

			<!-- Categories list -->
			<?php
				$highlight = 0;
				foreach($categories as $category):
					if($selected_category
							and
						$selected_category->id == $category->id):
			?>
						<div class="category selected">
							<span class="text">
								<?php echo $category->name; ?>
							</span>
						</div>
			<?php
					else:
			?>
						<a href="<?php
										profile_link($user->id,
														 $selected_module,
														 1,
														 $category,
														 false);
									?>">
							<div class="category active<?php if($highlight % 2 == 0) echo " highlight"; ?>">
								<span class="text">
									<?php echo $category->name; ?>
								</span>
							</div>
						</a>
			<?php
					endif;
					$highlight++;
				endforeach;
			?>
			<!-- Categories END -->

		</div>
		<!-- Categories list END -->

	</div>
	<!-- Content END -->

</div>
<!-- Profile END -->

<!-- Description form -->
<div id="description-form">
	<div id="description-form-spinner"
		  class="description-form">
		<div class="spinner-wrapper">
			
			<!-- Heading -->
			<div class="form-heading">
				
				<div class="info">
					<div class="label">
						Description
					</div>
					
					<div class="sublabel">
						About <?php echo $user->username; ?>.
					</div>
				</div>
				
				<div class="close"
					  onclick="form_tools.description.hide()">
				</div>
				
			</div>
			<!-- Heading END -->
			
			<!-- Content -->
			<div class="content">
				<?php echo replace_new_lines($user->description); ?>
				
				<div class="spacer">
				</div>
			</div>
			<!-- Content END -->
			
		</div>
	</div>
</div>
<!-- Description form END -->