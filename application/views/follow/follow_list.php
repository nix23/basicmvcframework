<!-- Following list -->
<div id="follow">
	
	<!-- Heading -->
	<div class="heading">
		<div class="wrapper">
			
			<!-- Legend -->
			<div class="legend">
				
				<div class="label">
					Following
				</div>
				
				<div class="sublabel">
					Latest activities of people you follow.
				</div>
				
			</div>
			<!-- Legend END -->
			
			<!-- Actions -->
			<div class="actions">
				
				<div class="edit-button">
					<div class="name" onclick="ajax.process_form('followed-users-form',
																				'follow',
																				'load_form',
																				'ajax',
																				false,
																				'modal_update_overlay')">
						Edit followers
					</div>
				</div>
				
			</div>
			<!-- Actions END -->
			
		</div>
	</div>
	<!-- Heading END -->
	
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
											public_link("follow/list/page-$page/days-$current_days_to_fetch");
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
		
		<!-- Select list -->
		<div class="select-list">
			
			<div class="heading">
				Last:
			</div>
			
			<?php
				foreach($days_to_fetch_items as $days_to_fetch_item):
					if($days_to_fetch_item->selected):
			?>
						<div class="wrapper">
							<div class="item selected">
								<?php echo $days_to_fetch_item->label; ?>
							</div>
						</div>
			<?php
					else:
			?>
						<a href="<?php
										public_link("follow/list/page-1/days-$days_to_fetch_item->value");
									?>">
							<div class="wrapper">
								<div class="item">
									<?php echo $days_to_fetch_item->label; ?>
								</div>
							</div>
						</a>
			<?php
					endif;
				endforeach;
			?>
			
		</div>
		<!-- Select list END -->
		
	</div>
	<!-- Controls END -->
	
	<!-- Content -->
	<div class="content">
		<?php
			$count = 1;
			foreach($followed_posts as $post):
		?>
				<!-- Post -->
				<div class="post<?php if($count % 2 == 0) echo " row-highlight"; ?>">
					<div class="item-separator">
					</div>

					<div class="item-wrapper">

						<!-- Left -->
						<div class="left">
							<div class="module-photo">
								<a href="<?php render_follow_post_module_item_link($post); ?>">
									<img src="<?php
													load_photo($post->main_photo->master_name,
																  145,
																  95);
												 ?>" width="145" height="95">
								</a>

								<div class="photo-label">
									<span><?php echo ucfirst($post->module); ?></span>
								</div>
							</div>

							<a href="<?php public_link("profile/view/user-{$post->user->id}"); ?>">
								<?php
									if($post->user->has_avatar()):
								?>
										<div class="avatar">
											<img src="<?php
															load_photo($post->user->avatar_master_name,
																		  95,
																		  95);
												?>" width="95" height="95">
										</div>
								<?php
									else:
								?>
										<div class="no-avatar">
										</div>
								<?php
									endif;
								?>
							</a>
						</div>
						<!-- Left END -->

						<!-- Right -->
						<div class="right">
							<div class="wrapper">

								<div class="header">
									<a href="<?php render_follow_post_module_item_link($post); ?>">
										<?php
											echo $post->user->username . " ";
											echo parse_follow_post_type($post->type) . " ";
											echo parse_follow_post_module($post->module);
										?>
									</a>
								</div>

								<div class="subheader">
									<a href="<?php render_follow_post_module_item_link($post); ?>">
										<?php render_follow_post_full_name($post); ?>
									</a>
								</div>

								<?php
									if($post->type == "comment"):
								?>
										<div class="comment">
											<?php
												echo $post->user->username;
												echo " wrote: ";
												echo trim_text($post->text, 300);
											?>
										</div>
								<?php
									endif;
								?>

								<div class="footer">
									<div class="panel-item no-margin">
										<?php
											$time_ago = time_ago($post->posted_on);

											if($time_ago == "Just now"):
												list($first_part, $second_part) = explode(" ", $time_ago);
												echo "<span class='highlight'>$first_part</span> $second_part";
											else:
												echo $time_ago;
											endif;
										?>
									</div>

									<div class="panel-item">
										<span class="highlight">
											<?php echo $post->likes_count; ?>
										</span>

										<?php echo ($post->likes_count == 1) ? "like" : "likes"; ?>
									</div>

									<div class="panel-item">
										<span class="highlight">
											<?php echo $post->comments_count; ?>
										</span>

										<?php echo ($post->comments_count == 1) ? "comment" : "comments"; ?>
									</div>

									<div class="panel-item">
										<span class="highlight">
											<?php echo $post->views_count; ?>
										</span>

										<?php echo ($post->views_count == 1) ? "view" : "views"; ?>
									</div>
								</div>

							</div>
						</div>
						<!-- Right END -->

					</div>

					<div class="item-separator">
					</div>
				</div>
				<!-- Post END -->
		<?php
				$count++;
			endforeach;
		?>
		
		<?php
			if(!$followed_posts):
		?>
				<!-- No followed posts -->
				<div class="no-posts">
					<div class="message">

						<div class="icon">
						</div>

						<div class="label">
							No followed posts yet.
						</div>

					</div>
				</div>
				<!-- No followed posts END -->
		<?php
			endif;
		?>
	</div>
	<!-- Content END -->

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
											public_link("follow/list/page-$page/days-$current_days_to_fetch");
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
	<!-- Controls END -->
	
</div>
<!-- Following list END -->

<!-- Followed users form -->
<div id="followed-users-form">
	<div id="followed-users-form-spinner"
		  class="followed-users-form">
		<div class="spinner-wrapper">
			
			<!-- Heading -->
			<div class="form-heading">
				
				<div class="info">
					<div class="label">
						People you follow
					</div>
					
					<div class="sublabel">
						You follow <span class="count">0</span> <span class="caption">users</span>.
					</div>
				</div>
				
				<div class="close"
					  onclick="form_tools.followed_users.hide()">
				</div>
				
				<div class="loading"
					  id="followed-users-form-loading">
				</div>
				
			</div>
			<!-- Heading END -->
			
			<!-- Content -->
			<div class="content trim-divs">
			</div>
			<!-- Content END -->
				
			<form name="followed-users-form">
				<!-- Token -->
				<input type="hidden" name="token[name]"  value="followed-users-form">
				<input type="hidden" name="token[value]" value="<?php token('followed-users-form'); ?>">
				<!-- Token END -->
			</form>
			
		</div>
	</div>
</div>
<!-- Followed users form END -->