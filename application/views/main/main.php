<div id="main">
	<!-- Most active posts -->
	<div class="most-active-posts">

		<div class="left-panel">
			<?php
				if($most_active_post):
			?>
					<a href="<?php render_most_active_module_item_link($most_active_post["post"],
																						$most_active_post["module_name"]); ?>"
						onmouseover="html_tools.main.most_active_post_over(this)"
						onmouseout="html_tools.main.most_active_post_out(this)">
						<div class="image trim-divs">
							<img src="<?php
											load_photo($most_active_post["post"]->main_photo->master_name,
														  330,
														  210);
										 ?>" width="330" height="210" onclick="html_tools.ie7_image_inside_link_click_fix(this)">

							<div class="module">
								<?php echo ucfirst($most_active_post["module_name"]); ?>
							</div>

							<div class="most-active-now">
								<div class="label">
									Most active
								</div>

								<div class="sublabel">
									NOW
								</div>
							</div>

							<div class="bottom-panel">
								<div class="heading">
									<div class="wrapper">
										<h3 class="trim-to-parent"><?php echo $most_active_post["post"]->get_full_heading(); ?></h3>
									</div>
								</div>

								<div class="comments">
									<div class="count">
										<?php echo $most_active_post["post"]->comments_total_count; ?>
									</div>

									<div class="label">
										<?php echo ($most_active_post["post"]->comments_total_count == 1) ? "comment" : "comments"; ?>
									</div>
								</div>
							</div>
						</div>
					</a>
			<?php
				endif;
			?>
		</div>

		<div class="right-panel">
			<div class="images">
				<?php
					foreach($most_active_posts_first_set as $most_active_post):
				?>
						<a href="<?php render_most_active_module_item_link($most_active_post["post"],
																							$most_active_post["module_name"]); ?>"
							onmouseover="html_tools.main.most_small_active_post_over(this, 'trim-div-<?php echo $most_active_post["post"]->id; ?>')"
							onmouseout="html_tools.main.most_small_active_post_out(this)">
							<div class="image trim-div-<?php echo $most_active_post["post"]->id; ?>">
								<img src="<?php
												load_photo($most_active_post["post"]->main_photo->master_name,
															  135,
															  100);
											 ?>" width="135" height="100" onclick="html_tools.ie7_image_inside_link_click_fix(this)">

								<div class="module">
									<?php echo ucfirst($most_active_post["module_name"]); ?>
								</div>

								<div class="heading">
									<div class="wrapper">
										<h4 class="trim-to-parent"><?php echo $most_active_post["post"]->get_full_heading(); ?></h4>
									</div>
								</div>

								<div class="comments">
									<span class="wrapper">
										<?php echo $most_active_post["post"]->comments_total_count; ?>
										<?php echo ($most_active_post["post"]->comments_total_count == 1) ? "comment" : "comments"; ?>
									</span>
								</div>
							</div>
						</a>
				<?php
					endforeach;
				?>
			</div>

			<div class="images">
				<?php
					foreach($most_active_posts_second_set as $most_active_post):
				?>
						<a href="<?php render_most_active_module_item_link($most_active_post["post"],
																							$most_active_post["module_name"]); ?>"
							onmouseover="html_tools.main.most_small_active_post_over(this, 'trim-div-<?php echo $most_active_post["post"]->id; ?>')"
							onmouseout="html_tools.main.most_small_active_post_out(this)">
							<div class="image trim-div-<?php echo $most_active_post["post"]->id; ?>">
								<img src="<?php
												load_photo($most_active_post["post"]->main_photo->master_name,
															  135,
															  100);
											 ?>" width="135" height="100" onclick="html_tools.ie7_image_inside_link_click_fix(this)">

								<div class="module">
									<?php echo ucfirst($most_active_post["module_name"]); ?>
								</div>

								<div class="heading">
									<div class="wrapper">
										<h4 class="trim-to-parent"><?php echo $most_active_post["post"]->get_full_heading(); ?></h4>
									</div>
								</div>

								<div class="comments">
									<span class="wrapper">
										<?php echo $most_active_post["post"]->comments_total_count; ?>
										<?php echo ($most_active_post["post"]->comments_total_count == 1) ? "comment" : "comments"; ?>
									</span>
								</div>
							</div>
						</a>
				<?php
					endforeach;
				?>
			</div>
		</div>

	</div>
	<!-- Most active posts END -->

	<!-- Modules row -->
	<div class="modules-row">

		<!-- Photosets -->
		<div class="module">

			<!-- Heading -->
			<div class="heading">
				<!-- Legend -->
				<div class="wrapper">
					<div class="label">
						Photosets
					</div>

					<div class="sublabel">
						Newest cars on fordrive
					</div>
				</div>
				<!-- Legend END -->

				<!-- Add button -->
				<?php
					if($authorized):
				?>
						<div class="add-button button-margin">
							<a href="<?php public_link("photos/form"); ?>">
								<div class="wrapper">
									<div class="name">
										<span class="add-char">+</span>&nbsp;Add photoset
									</div>
								</div>
							</a>
						</div>
				<?php
					else:
				?>
						<div class="add-button button-margin">
							<div class="wrapper"
								  onclick="form_tools.default_errors.show(new Array('Please login to upload your photoset.'))">
								<div class="name">
									<span class="add-char">+</span>&nbsp;Add photoset
								</div>
							</div>
						</div>
				<?php
					endif;
				?>
				<!-- Add button END -->
			</div>
			<!-- Heading END -->

			<!-- Images small -->
			<div class="images-small">
				<?php
					$count = 1;
					foreach($photosets as $photoset):
				?>
						<div class="image<?php if($count != 1) echo " image-margin-left"; ?>">
							<a href="<?php
											photoset_item_link($photoset,
																	 $photoset->category,
																	 $photoset->subcategory);
										?>"
								onmouseover="html_tools.main.module_images_small_image_over(this, 'trim-div-<?php echo $photoset->id; ?>')"
								onmouseout="html_tools.main.module_images_small_image_out(this)">
								<img src="<?php
												load_photo($photoset->main_photo_master_name,
															  100,
															  75);
											 ?>" width="100" height="75">

								<div class="header trim-div-<?php echo $photoset->id; ?>">
									<div class="header-wrapper">
										<h4 class="trim-to-parent"><?php echo $photoset->get_full_heading(); ?></h4>
									</div>

									<div class="comments">
										<span class="comments-wrapper">
											<?php echo $photoset->comments_count; ?>
											<?php echo ($photoset->comments_count == 1) ? "comment" : "comments"; ?>
										</span>
									</div>
								</div>
							</a>
						</div>
				<?php
						$count++;
						if($count == 5)
						{
							echo "<div class='vertical-spacer'></div>";
							$count = 1;
						}
					endforeach;
				?>
			</div>
			<!-- Images small END -->

		</div>
		<!-- Photosets END -->

		<!-- Speeds -->
		<div class="module">

			<!-- Heading -->
			<div class="heading">
				<!-- Legend -->
				<div class="wrapper">
					<div class="label">
						Speed
					</div>

					<div class="sublabel">
						Latest in car industry
					</div>
				</div>
				<!-- Legend END -->

				<!-- Add button -->
				<?php
					if($authorized):
				?>
						<div class="add-button">
							<a href="<?php public_link("speed/form"); ?>">
								<div class="wrapper">
									<div class="name">
										<span class="add-char">+</span>&nbsp;Add speed
									</div>
								</div>
							</a>
						</div>
				<?php
					else:
				?>
						<div class="add-button">
							<div class="wrapper"
								  onclick="form_tools.default_errors.show(new Array('Please login to upload your speed.'))">
								<div class="name">
									<span class="add-char">+</span>&nbsp;Add speed
								</div>
							</div>
						</div>
				<?php
					endif;
				?>
				<!-- Add button END -->
			</div>
			<!-- Heading END -->

			<!-- Posts -->
			<div class="posts">
				<?php
					$count = 1;
					foreach($speeds as $speed):
				?>
						<!-- Post top -->
						<div class="post-top">
							<div class="image">
								<a href="<?php
												speed_item_link($speed,
																	 $speed->category,
																	 $speed->subcategory);
											?>">
									<img src="<?php
													load_photo($speed->main_photo_master_name,
																  100,
																  75);
												 ?>" width="100" height="75">
								</a>
							</div>

							<div class="legend">
								<div class="label">
									<a href="<?php
													speed_item_link($speed,
																		 $speed->category,
																		 $speed->subcategory);
												?>" class="link">
										<?php echo $speed->heading; ?>
									</a>
								</div>

								<div class="sublabel">
									<?php echo $speed->short_description; ?>
								</div>
							</div>
						</div>
						<!-- Post top END -->

						<!-- Small post spacer -->
						<div class="post-spacer-small">
						</div>
						<!-- Small post spacer END -->

						<!-- Post bottom -->
						<div class="post-bottom">
							<?php
								$time_ago_parts = time_ago_splitted($speed->posted_on);
							?>

							<div class="item">
								<span class="highlight"><?php echo $time_ago_parts[0]; ?></span>
								<?php echo mb_strtolower($time_ago_parts[1], "UTF-8"); ?>
							</div>

							<div class="item">
								<span class="highlight"><?php echo $speed->comments_count; ?></span>
								<?php echo ($speed->comments_count == 1) ? "comment" : "comments"; ?>
							</div>

							<div class="item">
								<span class="highlight"><?php echo $speed->likes_count; ?></span>
								<?php echo ($speed->likes_count == 1) ? "like" : "likes"; ?>
							</div>

							<div class="item">
								<span class="highlight"><?php echo $speed->views_count; ?></span>
								<?php echo (($speed->views_count) == 1) ? "read" : "reads"; ?>
							</div>
						</div>
						<!-- Post bottom END -->
				<?php
						$count++;
						if($count <= 5)
							echo "<div class='post-spacer'></div>";
					endforeach;
				?>
			</div>
			<!-- Posts END -->

		</div>
		<!-- Speeds END -->

	</div>
	<!-- Modules row END -->

	<!-- Modules spacer -->
	<div class="modules-spacer">
	</div>
	<!-- Modules spacer END -->

	<!-- Modules row -->
	<div class="modules-row">

		<!-- Spots -->
		<div class="module">

			<!-- Heading -->
			<div class="heading">
				<!-- Legend -->
				<div class="wrapper">
					<div class="label">
						Spots
					</div>

					<div class="sublabel">
						Latest user car spots
					</div>
				</div>
				<!-- Legend END -->

				<!-- Add button -->
				<?php
					if($authorized):
				?>
						<div class="add-button button-margin">
							<a href="<?php public_link("spots/form"); ?>">
								<div class="wrapper">
									<div class="name">
										<span class="add-char">+</span>&nbsp;Add spot
									</div>
								</div>
							</a>
						</div>
				<?php
					else:
				?>
						<div class="add-button button-margin">
							<div class="wrapper"
								  onclick="form_tools.default_errors.show(new Array('Please login to upload your spot.'))">
								<div class="name">
									<span class="add-char">+</span>&nbsp;Add spot
								</div>
							</div>
						</div>
				<?php
					endif;
				?>
				<!-- Add button END -->
			</div>
			<!-- Heading END -->

			<!-- Images small -->
			<div class="images-small">
				<?php
					$count = 1;
					foreach($spots as $spot):
				?>
						<div class="image<?php if($count != 1) echo " image-margin-left"; ?>">
							<a href="<?php
											spot_item_link($spot,
																$spot->category,
																$spot->subcategory);
										?>"
								onmouseover="html_tools.main.module_images_small_image_over(this, 'trim-div-<?php echo $spot->id; ?>')"
								onmouseout="html_tools.main.module_images_small_image_out(this)">
								<img src="<?php
												load_photo($spot->main_photo_master_name,
															  100,
															  75);
											 ?>" width="100" height="75">

								<div class="header trim-div-<?php echo $spot->id; ?>">
									<div class="header-wrapper">
										<h4 class="trim-to-parent"><?php echo $spot->get_full_heading(); ?></h4>
									</div>

									<div class="comments">
										<span class="comments-wrapper">
											<?php echo $spot->comments_count; ?>
											<?php echo ($spot->comments_count == 1) ? "comment" : "comments"; ?>
										</span>
									</div>
								</div>
							</a>
						</div>
				<?php
						$count++;
						if($count == 5)
						{
							echo "<div class='vertical-spacer'></div>";
							$count = 1;
						}
					endforeach;
				?>
			</div>
			<!-- Images small END -->

		</div>
		<!-- Spots END -->

		<!-- Videos -->
		<div class="module">

			<!-- Heading -->
			<div class="heading">
				<!-- Legend -->
				<div class="wrapper">
					<div class="label">
						Videos
					</div>

					<div class="sublabel">
						Latest auto events
					</div>
				</div>
				<!-- Legend END -->

				<!-- Add button -->
				<?php
					if($authorized):
				?>
						<div class="add-button">
							<a href="<?php public_link("videos/form"); ?>">
								<div class="wrapper">
									<div class="name">
										<span class="add-char">+</span>&nbsp;Add video
									</div>
								</div>
							</a>
						</div>
				<?php
					else:
				?>
						<div class="add-button">
							<div class="wrapper"
								  onclick="form_tools.default_errors.show(new Array('Please login to upload your video.'))">
								<div class="name">
									<span class="add-char">+</span>&nbsp;Add video
								</div>
							</div>
						</div>
				<?php
					endif;
				?>
				<!-- Add button END -->
			</div>
			<!-- Heading END -->

			<!-- Posts -->
			<div class="posts">
				<?php
					$count = 1;
					foreach($videos as $video):
				?>
						<!-- Post top -->
						<div class="post-top">
							<div class="image">
								<a href="<?php
												video_item_link($video,
																	 $video->category,
																	 $video->subcategory);
											?>">
									<img src="<?php
													load_photo($video->main_photo_master_name,
																  100,
																  75);
												 ?>" width="100" height="75">
								</a>

								<div class="video-icon">
								</div>
							</div>

							<div class="legend">
								<div class="label">
									<a href="<?php
													video_item_link($video,
																		 $video->category,
																		 $video->subcategory);
												?>" class="link">
										<?php echo $video->heading; ?>
									</a>
								</div>

								<div class="sublabel">
									<?php echo $video->short_description; ?>
								</div>
							</div>
						</div>
						<!-- Post top END -->

						<!-- Small post spacer -->
						<div class="post-spacer-small">
						</div>
						<!-- Small post spacer END -->

						<!-- Post bottom -->
						<div class="post-bottom">
							<?php
								$time_ago_parts = time_ago_splitted($video->posted_on);
							?>

							<div class="item">
								<span class="highlight"><?php echo $time_ago_parts[0]; ?></span>
								<?php echo mb_strtolower($time_ago_parts[1], "UTF-8"); ?>
							</div>

							<div class="item">
								<span class="highlight"><?php echo $video->comments_count; ?></span>
								<?php echo ($video->comments_count == 1) ? "comment" : "comments"; ?>
							</div>

							<div class="item">
								<span class="highlight"><?php echo $video->likes_count; ?></span>
								<?php echo ($video->likes_count == 1) ? "like" : "likes"; ?>
							</div>

							<div class="item">
								<span class="highlight"><?php echo $video->views_count; ?></span>
								<?php echo (($video->views_count) == 1) ? "view" : "views"; ?>
							</div>
						</div>
						<!-- Post bottom END -->
				<?php
						$count++;
						if($count <= 5)
							echo "<div class='post-spacer'></div>";
					endforeach;
				?>
			</div>
			<!-- Posts END -->

		</div>
		<!-- Videos END -->

	</div>
	<!-- Modules row END -->

	<!-- Modules spacer -->
	<div class="modules-spacer">
	</div>
	<!-- Modules spacer END -->

	<!-- Modules row -->
	<div class="modules-row">

		<!-- Fordrivers -->
		<div class="module">

			<!-- Heading -->
			<div class="heading">
				<div class="wrapper">
					<div class="label">
						Fordrivers
					</div>

					<div class="sublabel">
						Most active users
					</div>
				</div>
			</div>
			<!-- Heading END -->

			<!-- Users -->
			<div class="users">
				<?php
					$count = 0;
					foreach($top_users as $top_user):
				?>
						<!-- User -->
						<div class="user">

							<!-- Last uploads -->
							<div class="last-uploads">
								<?php
									foreach($top_user->module_items as $module_item):
								?>
										<a href="<?php
														render_main_module_item_link($module_item,
																							  $module_item->module);
													?>">
											<div class="module-item">
												<img src="<?php
																load_photo($module_item->main_photo_master_name,
																			  40,
																			  30);
															 ?>" width="40" height="30"
													  onclick="html_tools.ie7_image_inside_link_click_fix(this)">
											</div>
										</a>
								<?php
									endforeach;
								?>
							</div>
							<!-- Last uploads END -->

							<!-- Avatar -->
							<a href="<?php
											if($top_user->is_current_logged_user())
												public_link("drive");
											else
												public_link("profile/view/user-{$top_user->id}");
										?>">
								<div class="avatar">
									<?php
										if($top_user->has_avatar()):
									?>
											<img src='<?php
																load_photo($top_user->avatar_master_name,
																			  60,
																			  60);
														?>' width="60" height="60"
													onclick="html_tools.ie7_image_inside_link_click_fix(this)">
									<?php
										else:
									?>
											<div class="no-avatar">
											</div>
									<?php
										endif;
									?>

									<?php
										if($top_user->is_current_logged_user())
											echo "<div class='you-label'></div>";
									?>
								</div>
							</a>
							<!-- Avatar END -->

							<!-- Legend -->
							<div class="legend">
								<div class="header">
									<div class="row">
										<a href="<?php
														if($top_user->is_current_logged_user())
															public_link("drive");
														else
															public_link("profile/view/user-{$top_user->id}");
													?>" class="link">
											<?php echo $top_user->username; ?>
										</a>
									</div>
									
									<div class="trimmer">
									</div>
								</div>

								<div class="subheader">
									<?php
										if($top_user->has_subname())
											echo $top_user->subname;
										else
											time_on_site($top_user->registred_on);
									?>
								</div>
							</div>
							<!-- Legend END -->

							<!-- Rating -->
							<div class="rating">
								<div class="number">
									<?php echo $top_user->rank; ?>
								</div>

								<div class="label">
									Fordriver
								</div>
							</div>
							<!-- Rating END -->

						</div>
						<!-- User END -->
				<?php
						$count++;
						if($count != 5)
							echo "<div class='user-spacer'></div>";
					endforeach;
				?>
			</div>
			<!-- Users END -->

		</div>
		<!-- Fordrivers END -->

		<!-- Activity -->
		<div class="module">

			<!-- Heading -->
			<div class="heading">
				<div class="wrapper">
					<div class="label">
						Activity
					</div>

					<div class="sublabel">
						Latest discussions on fordrive
					</div>
				</div>
			</div>
			<!-- Heading END -->

			<!-- Activities -->
			<div class="activities">
				<?php
					$count = 1;
					foreach($last_activities as $last_activity):
				?>
						<!-- Post top -->
						<div class="post-top">

							<!-- Item -->
							<div class="item">
								<a href="<?php
												render_main_activity_module_item_link($last_activity);
											?>">
									<img src="<?php
													load_photo($last_activity->main_photo->master_name,
																  80,
																  60);
												 ?>" width="80" height="60"
											onclick="html_tools.ie7_image_inside_link_click_fix(this)">

									<div class="module-name">
										<div class="<?php echo $last_activity->module; ?>-module-icon">
										</div>
									</div>
								</a>
							</div>
							<!-- Item END -->

							<!-- Avatar -->
							<a href="<?php
											if($last_activity->user->is_current_logged_user())
												public_link("drive");
											else
												public_link("profile/view/user-{$last_activity->user->id}");
										?>">
								<div class="avatar">
									<?php
										if($last_activity->user->has_avatar()):
									?>
											<img src='<?php
																load_photo($last_activity->user->avatar_master_name,
																			  60,
																			  60);
														?>' width="60" height="60"
													onclick="html_tools.ie7_image_inside_link_click_fix(this)">
									<?php
										else:
									?>
											<div class="no-avatar">
											</div>
									<?php
										endif;
									?>

									<?php
										if($last_activity->user->is_current_logged_user())
											echo "<div class='you-label'></div>";
									?>
								</div>
							</a>
							<!-- Avatar END -->

							<!-- Legend -->
							<div class="legend">
								<div class="label">
									<a href="<?php
													render_main_activity_module_item_link($last_activity);
												?>" class="link">
										<?php echo parse_main_activity_event_header($last_activity); ?>
									</a>
								</div>

								<div class="sublabel">
									<a href="<?php
													render_main_activity_module_item_link($last_activity);
												?>" class="link">
										<?php render_main_activity_full_name($last_activity); ?>
									</a>
								</div>

								<div class="text">
									<?php echo $last_activity->user->username; ?>
									wrote: <?php echo trim_text($last_activity->text, 150); ?>
								</div>
							</div>
							<!-- Legend END -->

						</div>
						<!-- Post top END -->

						<!-- Small post spacer -->
						<div class="post-spacer-small">
						</div>
						<!-- Small post spacer END -->

						<!-- Post bottom -->
						<div class="post-bottom">
							<?php
								$time_ago_parts = time_ago_splitted($last_activity->posted_on);
							?>

							<div class="item">
								<span class="highlight"><?php echo $time_ago_parts[0]; ?></span>
								<?php echo mb_strtolower($time_ago_parts[1], "UTF-8"); ?>
							</div>

							<div class="item">
								<span class="highlight"><?php echo $last_activity->comments_count; ?></span>
								<?php echo ($last_activity->comments_count == 1) ? "comment" : "comments"; ?>
							</div>

							<div class="item">
								<span class="highlight"><?php echo $last_activity->likes_count; ?></span>
								<?php echo ($last_activity->likes_count == 1) ? "like" : "likes"; ?>
							</div>

							<div class="item">
								<span class="highlight"><?php echo $last_activity->views_count; ?></span>
								<?php echo ($last_activity->views_count == 1) ? "view" : "views"; ?>
							</div>
						</div>
						<!-- Post bottom END -->
				<?php
						$count++;
						if($count <= 5)
							echo "<div class='post-spacer'></div>";
					endforeach;
				?>
			</div>
			<!-- Activities END -->

		</div>
		<!-- Activity END -->

	</div>
	<!-- Modules row END -->
</div>