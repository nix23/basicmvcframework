<!-- Item -->
<div class="view-item">
	
	<!-- Catalog back link -->
	<div class="backlink">
		<a href="<?php public_link($catalog_backlink); ?>">
			<< Back to catalog
		</a>
	</div>
	<!-- Catalog back link END -->
	
	<!-- Ajax requests token -->
	<form name='view-spot'>
		<input type='hidden' name='token[name]'  value='view-spot'>
		<input type='hidden' name='token[value]' value='<?php token('view-spot'); ?>'>
	</form>
	<!-- Ajax request token END -->
	
	<!-- Spot header -->
	<table cellspacing="0" cellpadding="0" class="header-table">
		<tr>
			<td class="heading-cell">
				<div class="heading">
					<h1>
						<?php spot_full_name($spot); ?>
					</h1>
				</div>

				<div class="panel">
					<div class="item float-left">
						Posted: <span class="highlight"><?php echo time_ago($spot->posted_on); ?></span>
					</div>

					<div class="item float-left margin-left">
						Spot date:

						<span class="highlight">
							<?php
								echo $spot->capture_year;
								echo " ";
								echo full_month($spot->capture_month);
							?>
						</span>
					</div>

					<?php
						$new_line_rendered = false;
						if(!empty($spot->location)):
					?>
							<div class="item float-left newline">
								Location: <span class="highlight"><?php echo $spot->location; ?></span>
							</div>
					<?php
							$new_line_rendered = true;
						endif;
						
						if(!empty($spot->event)):
					?>
							<div class="item float-left<?php echo ($new_line_rendered) ? " margin-left" : " newline"; ?>">
								Event: <span class="highlight"><?php echo $spot->event; ?></span>
							</div>
					<?php
						endif;
					?>
					
					<div class="item float-right">
						<span class="highlight">
							<?php echo $spot->item_views_count; ?>
						</span>

						<?php echo ($spot->item_views_count == 1) ? "View" : "Views"; ?>
					</div>
				</div>
			</td>
			
			<td class="info-cell">
				<div class="comment">
					<div class="count">
						<?php echo $spot->comments_total_count; ?>
					</div>
					
					<div class="label">
						<?php echo ($spot->comments_total_count == 1) ? "Comment" : "Comments"; ?>
					</div>
				</div>
			</td>
		</tr>
		
		<tr>
			<td colspan="2" class="separator">
			</td>
		</tr>
	</table>
	<!-- Spot header END -->
	
	<!-- Spot data and categories -->
	<div class="item-content">
		<!-- Spot data -->
		<?php $gallery_photo_number = 0; ?>
		<div id="gallery-photos" class="item-data"
			  data-viewed-item-id="<?php echo $spot->id; ?>"
			  data-collect-resolutions="yes"
			  data-module="spots"
			  data-heading="<?php full_category_name($spot); ?>"
			  data-subheading="<?php spot_full_name($spot); ?>">
			
			<!-- Overall -->
			<div class="overall">
				
				<!-- Main photo -->
				<div class="main-photo gallery-photo"
					  data-gallery-photo-number="<?php echo ++$gallery_photo_number; ?>"
					  data-photo-id="<?php echo $spot->main_photo->id; ?>"
					  data-master-photo-name="<?php echo $spot->main_photo->master_name; ?>"
					  data-upload-directory="images/<?php echo $spot->main_photo->directory; ?>"
					  data-packed-resolutions="<?php 
					  			pack_resolutions_for_gallery($spot->main_photo->lazy_clones); 
					  									?>">
					<div class="wrapper">
						
						<img src="<?php
										load_photo( $spot->main_photo->master_name,
														380,
														245);
									 ?>" width="380" height="245"
							  onclick="gallery.load(this, 'gallery-photos', 'gallery-photo')">
						
						<div class="wallpapers">
							
							<div class="count">
								<?php echo $spot->main_photo->lazy_clones_count; ?>
							</div>

							<?php
								if($spot->main_photo->lazy_clones_count == 1)
									echo "<div class='hrphotos-icon-singular'></div>";
								else
									echo "<div class='hrphotos-icon-plural'></div>";
							?>
							
							<?php
								foreach($spot->main_photo->lazy_clones as $lazy_clone_array):
									$lazy_clone = (object) $lazy_clone_array;
									
									if($lazy_clone->exists):
							?>
										<div class="wallpaper">
											<a href="<?php
															$url_segments  = "services/viewphoto/spots";
															$url_segments .= "/" . $spot->main_photo->id;
															$url_segments .= "/" . $lazy_clone->width;
															$url_segments .= "/" . $lazy_clone->height;
															public_link($url_segments);
														?>" target="_blank"
												onmouseover="html_tools.module_item.wallpaper_over(this)"
												onmouseout="html_tools.module_item.wallpaper_out(this)"
												rel="nofollow">
												<div class="wrapper">
													<div class="spacer">
													</div>
													
													<div class="size width">
														<?php echo $lazy_clone->width; ?>
													</div>
													
													<div class="size height">
														<?php echo $lazy_clone->height; ?>
													</div>
												</div>
											</a>
										</div>
							<?php
									endif;
								endforeach;
							?>
							
						</div>
						
					</div>
				</div>
				<!-- Main photo END -->
				
				<!-- Information panel -->
				<div class="info">
						
					<!-- Header -->
					<div class="header">
						
						<!-- Avatar -->
						<a href="<?php
										if($spot->user->is_current_logged_user())
											public_link("drive");
										else
											public_link("profile/view/user-{$spot->user->id}");
									?>">
							<div class="avatar">
								<?php
									if($spot->user->has_avatar()):
								?>
										<img src='<?php
															load_photo($spot->user->avatar_master_name,
																		  70,
																		  70);
													?>' width="70" height="70">
								<?php
									else:
								?>
										<div class="no-avatar">
										</div>
								<?php
									endif;
								?>

								<?php
									if($spot->user->is_current_logged_user())
										echo "<div class='you-label'></div>";
								?>
							</div>
						</a>
						<!-- Avatar END -->
						
						<!-- Legend -->
						<div class="legend">
							<div class="label">
								<div class="row">
									<a href="<?php
													if($spot->user->is_current_logged_user())
														public_link("drive");
													else
														public_link("profile/view/user-{$spot->user->id}");
												?>">
										<span class="link">
											<?php echo $spot->user->username; ?>
										</span>
									</a>
								</div>
								
								<div class="trimmer">
								</div>
							</div>

							<div class="sublabel">
								<?php
									if($spot->user->has_subname())
										echo $spot->user->subname;
									else
										time_on_site($spot->user->registred_on);
								?>
							</div>
						</div>
						<!-- Legend END -->
						
					</div>
					<!-- Header END -->
					
					<!-- Likes -->
					<div class="list-item double-margin">
						<div class="panel">
							<div class="count">
								<?php echo $spot->likes_count; ?>
							</div>
							
							<div class="<?php 
												if($spot->likes_count == 1) 
													echo "singular-caption";
												else
													echo "plural-caption"; 
											?> caption">
							</div>
						</div>
						
						<div class="legend">
							<div class="label">
								Liked
							</div>
							
							<div class="sublabel">
								this spot
							</div>
						</div>
						
						<div class="action">
							<?php
								if($authorized):
									if($spot->is_logged_user_post_author):
							?>
										<div class="button selected">
											<div class="wrapper selected-like-bg">
												Like
											</div>
										</div>
							<?php
									elseif($spot->is_liked_by_logged_user):
							?>
										<div class="button selected">
											<div class="wrapper selected-like-bg">
												Liked
											</div>
										</div>
							<?php
									else:
							?>
										<div class="button">
											<div class="wrapper like-bg"
												  onmouseover="html_tools.module_item.panel_button_over(this)"
												  onmouseout="html_tools.module_item.panel_button_out(this)"
												  onclick="ajax.process_form('view-spot',
												  									  'spots',
												  									  'add_like',
												  									  'ajax/<?php echo $spot->id; ?>',
												  									  this,
												  									  'modal')">
												Like
											</div>
										</div>
							<?php
									endif;
								else:
							?>
									<div class="button">
										<div class="wrapper like-bg"
											  onmouseover="html_tools.module_item.panel_button_over(this)"
											  onmouseout="html_tools.module_item.panel_button_out(this)"
											  onclick="form_tools.default_errors.show(new Array('Please login to add rating.'))">
											Like
										</div>
									</div>
							<?php
								endif;
							?>
						</div>
					</div>
					<!-- Likes END -->
					
					<!-- Followers -->
					<div class="list-item">
						<div class="panel">
							<div class="count">
								<?php echo $spot->author_followers_count; ?>
							</div>
							
							<div class="<?php 
												if($spot->author_followers_count == 1) 
													echo "singular-caption";
												else
													echo "plural-caption"; 
											?> caption">
							</div>
						</div>
						
						<div class="legend">
							<div class="label">
								Followed
							</div>
							
							<div class="sublabel">
								this spot author
							</div>
						</div>
						
						<div class="action">
							<?php
								if($authorized):
									if($spot->is_logged_user_post_author):
							?>
										<div class="button selected">
											<div class="wrapper selected-follow-bg">
												Follow
											</div>
										</div>
							<?php
									else:
							?>
										<div class="button">
											<div class="wrapper follow-bg"
												  onmouseover="html_tools.module_item.panel_button_over(this)"
												  onmouseout="html_tools.module_item.panel_button_out(this)"
												  onclick="ajax.process_form('view-spot',
													  								  'spots',
													  								  'change_follow',
													  								  'ajax/<?php echo $spot->user_id; ?>',
													  								  this,
													  								  'modal')">
												<?php echo ($spot->is_author_followed_by_logged_user) ? "Unfollow" : "Follow"; ?>
											</div>
										</div>
							<?php
									endif;
								else:
							?>
									<div class="button">
										<div class="wrapper follow-bg"
											  onmouseover="html_tools.module_item.panel_button_over(this)"
											  onmouseout="html_tools.module_item.panel_button_out(this)"
											  onclick="form_tools.default_errors.show(new Array('Please login to follow other users.'))">
											Follow
										</div>
									</div>
							<?php
								endif;
							?>
						</div>
					</div>
					<!-- Followers END -->
					
					<!-- Favorites -->
					<div class="list-item">
						<div class="panel">
							<div class="count">
								<?php echo $spot->favorites_count; ?>
							</div>
							
							<div class="<?php 
												if($spot->favorites_count == 1) 
													echo "singular-caption";
												else
													echo "plural-caption"; 
											?> caption">
							</div>
						</div>
						
						<div class="legend">
							<div class="label">
								Favorite
							</div>
							
							<div class="sublabel">
								post on fordrive
							</div>
						</div>
						
						<div class="action">
							<?php
								if($authorized):
									if($spot->is_logged_user_post_author):
							?>
										<div class="button selected">
											<div class="wrapper selected-favorite-bg">
												Favorite
											</div>
										</div>
							<?php
									else:
							?>
										<div class="button">
											<div class="wrapper favorite-bg"
												  onmouseover="html_tools.module_item.panel_button_over(this)"
												  onmouseout="html_tools.module_item.panel_button_out(this)"
												  onclick="ajax.process_form('view-spot',
													  								  'spots',
													  								  'change_favorite',
													  								  'ajax/<?php echo $spot->id; ?>',
													  								  this,
													  								  'modal')">
												<?php echo ($spot->is_favorite_of_logged_user) ? "Unfavorite" : "Favorite"; ?>
											</div>
										</div>
							<?php
									endif;
								else:
							?>
									<div class="button">
										<div class="wrapper favorite-bg"
											  onmouseover="html_tools.module_item.panel_button_over(this)"
											  onmouseout="html_tools.module_item.panel_button_out(this)"
											  onclick="form_tools.default_errors.show(new Array('Please login to add to favorites.'))">
											Favorite
										</div>
									</div>
							<?php
								endif;
							?>
						</div>
					</div>
					<!-- Favorites END -->
						
				</div>
				<!-- Information panel END -->
				
			</div>
			<!-- Overall END -->
			
			<!-- Article -->
			<div class="article">
				<?php
					if($spot->has_short_description()):
				?>
						<div class="heading">
							<h2>
								<?php echo $spot->short_description; ?>
							</h2>
						</div>
				<?php
					endif;
				?>

				<?php
					if($spot->has_article())
						$gallery_photo_number = parse_article_tags($spot,
											 	 								 $spot->article,
												 								 $gallery_photo_number);
					else
						$spot->remove_main_photo_from_attached_photos();
				?>
			</div>
			<!-- Article END -->
			
			<!-- Photos -->
			<div class="photos">
				<?php
					$items_count = 1;
					foreach($spot->photos as $photo):
						if($items_count <= 9):
				?>
							<div class="large gallery-photo"
								  data-gallery-photo-number="<?php echo ++$gallery_photo_number; ?>"
								  data-photo-id="<?php echo $photo->id; ?>"
								  data-master-photo-name="<?php echo $photo->master_name; ?>"
								  data-upload-directory="images/<?php echo $photo->directory; ?>"
								  data-packed-resolutions="<?php 
										pack_resolutions_for_gallery($photo->lazy_clones); 
																	?>">
								<div class="item-wrapper">
									
									<img src="<?php 
													load_photo( $photo->master_name, 
																	270, 
																	180); 
												 ?>" width="270" height="180"
										  onclick="gallery.load(this, 'gallery-photos', 'gallery-photo')">
									
									<div class="wallpapers">
										
										<div class="count">
											<?php echo $photo->lazy_clones_count; ?>
										</div>

										<?php
											if($photo->lazy_clones_count == 1)
												echo "<div class='hrphotos-icon-singular'></div>";
											else
												echo "<div class='hrphotos-icon-plural'></div>";
										?>
										
										<?php
											foreach($photo->lazy_clones as $lazy_clone_array):
												$lazy_clone = (object) $lazy_clone_array;
												
												if($lazy_clone->exists):
										?>
													<div class="wallpaper">
														<a href="<?php
																		$url_segments  = "services/viewphoto/spots";
																		$url_segments .= "/" . $photo->id;
																		$url_segments .= "/" . $lazy_clone->width;
																		$url_segments .= "/" . $lazy_clone->height;
																		public_link($url_segments);
																	?>" target="_blank"
															onmouseover="html_tools.module_item.wallpaper_over(this)"
															onmouseout="html_tools.module_item.wallpaper_out(this)"
															rel="nofollow">
															<div class="wrapper">
																<div class="spacer">
																</div>
																
																<div class="size width">
																	<?php echo $lazy_clone->width; ?>
																</div>
																
																<div class="size height">
																	<?php echo $lazy_clone->height; ?>
																</div>
															</div>
														</a>
													</div>
										<?php
												endif;
											endforeach;
										?>
										
									</div>
									
								</div>
							</div>
				<?php
						else:
				?>
							<div class="small gallery-photo"
								  data-gallery-photo-number="<?php echo ++$gallery_photo_number; ?>"
								  data-photo-id="<?php echo $photo->id; ?>"
								  data-master-photo-name="<?php echo $photo->master_name; ?>"
								  data-upload-directory="images/<?php echo $photo->directory; ?>"
								  data-packed-resolutions="<?php 
										pack_resolutions_for_gallery($photo->lazy_clones); 
																	?>">
								<div class="item-wrapper">
									
									<img src="<?php 
													load_photo( $photo->master_name, 
																	130, 
																	90); 
												 ?>" width="130" height="90"
										  onclick="gallery.load(this, 'gallery-photos', 'gallery-photo')">
									
									<div class="wallpapers-count">
										
										<div class="count">
											<?php echo $photo->lazy_clones_count; ?>
										</div>

										<?php
											if($photo->lazy_clones_count == 1)
												echo "<div class='hrphotos-icon-singular'></div>";
											else
												echo "<div class='hrphotos-icon-plural'></div>";
										?>
										
									</div>
									
								</div>
							</div>
				<?php
						endif;
						$items_count++;
					endforeach;
				?>
			</div>
			<!-- Photos END -->
			
			<?php
				if(!empty($spot->author) or !empty($spot->source)):
			?>
					<!-- Spacer -->
					<div class="source-spacer">
					</div>
					<!-- Spacer END -->
		
					<!-- Source -->
					<div class="source">
						<?php 
							if(!empty($spot->author)):
						?>
								<div class="item">
									Author: <span class="highlight"><?php echo $spot->author; ?></span>
								</div>
						<?php
							endif;

							if(!empty($spot->source)):
						?>
								<div class="item">
									Source:
									
									<a href="<?php echo $spot->source; ?>" target="_blank" rel="nofollow">
										<span class="highlight"><?php echo $spot->source; ?></span>
									</a>
								</div>
						<?php
							endif;
						?> 
					</div>
					<!-- Source END -->
					
					<!-- Spacer -->
					<div class="source-spacer">
					</div>
					<!-- Spacer END -->
			<?php
				endif;
			?>
			
			<!-- Comments -->
			<div class="comments comment-id-heading">
				
				<!-- Heading -->
				<div class="heading">
					
					<!-- Count -->
					<div class="count ajax-comments-count">
						<?php echo $spot->comments_total_count; ?>
					</div>
					<!-- Count END -->
					
					<!-- Label -->
					<div class="label">
						<div class="big">
							Comments
						</div>
						
						<div class="small">
							Create a discussion with other fordrivers.
						</div>
					</div>
					<!-- Label END -->
					
					<!-- Add button -->
					<?php
						if($authorized):
					?>
							<div class="add-button">
								<div class="wrapper"
										onclick="form_tools.newcomment.show('New comment',
																						'Share your opinion about this spot',
																						0)">
									<div class="name">
										<span class="add-char">+</span>&nbsp;Add comment
									</div>
								</div>
							</div>
					<?php
						else:
					?>
							<div class="add-button">
								<div class="wrapper"
									  onclick="form_tools.default_errors.show(new Array('Please login to write comment.'))">
									<div class="name">
										<span class="add-char">+</span>&nbsp;Add comment
									</div>
								</div>
							</div>
					<?php
						endif;
					?>
					<!-- Add button END -->
					
				</div>
				<!-- Heading END -->
				
				<!-- Controls -->
				<div class="controls">
					
					<!-- Pagination -->
					<div class="pagination ajax-comments-pagination">
							<?php include("view_spot_comments_pagination.php"); ?>
					</div>
					<!-- Pagination END -->
					
					<!-- Refresh -->
					<div class="refresh"
						  onclick="form_tools.comments.load_new(<?php echo $spot->id; ?>,
						  													 'spots')">
						
						<div class="message">
							Refresh comments
						</div>
						
						<div class="icon">
						</div>
						
						<div class="active-icon"
							  id="refresh-comments-active-icon">
							<div class="wrapper">
							</div>
						</div>
						
					</div>
					<!-- Refresh END -->
					
				</div>
				<!-- Controls END -->
				
				<!-- Items -->
				<div class="items ajax-comments-items">
					<?php include("view_spot_comments_items.php"); ?>
				</div>
				<!-- Items END -->
				
				<!-- Bottom pagination -->
				<div class="controls">
					<?php
						if($spot->comments):
					?>
							<div class="pagination ajax-comments-pagination">
								<?php include("view_spot_comments_pagination.php"); ?>
							</div>
					<?php
						endif;
					?>
				</div>
				<!-- Bottom pagination END -->
				
			</div>
			<!-- Comments END -->
			
		</div>
		<!-- Spot data END -->
		
		<!-- Spot categories -->
		<div class="item-categories">
			
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
												module_link("spots",
																"list",
																$selected_category,
																$subcategory,
																1,
																$current_sort);
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
			
			<!-- Sandbox item -->
			<?php
				if(!$selected_category and !$selected_subcategory):
			?>
					<div class="category selected">
						<span class="text">
							Sandbox
						</span>
					</div>
			<?php
				else:
			?>
					<a href="<?php
									module_link("spots",
													"list",
													false,
													false,
													1,
													$current_sort);
								?>">
						<div class="category active">
							<span class="text">
								Sandbox
							</span>
						</div>
					</a>
			<?php
				endif;
			?>
			<!-- Sandbox item END -->
			
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
										module_link("spots",
														"list",
														$category,
														false,
														1,
														$current_sort);
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
			<!-- Categories list END -->
			
		</div>
		<!-- Photoset categories END -->
	</div>
	<!-- Photoset data and categories END -->
</div>
<!-- Item END -->

<?php
	if($authorized):
?>
		<!-- Add comment -->
		<div id="newcomment-form">
			<div id="newcomment-form-spinner"
				  class="newcomment-form">
				<div class="spinner-wrapper">
					
					<form name="newcomment-form">
						
						<!-- Heading -->
						<div class="heading">
							
							<div class="info">
								<div class="label">
								</div>
								
								<div class="sublabel">
								</div>
							</div>
							
							<div class="close"
								  onclick="form_tools.newcomment.hide()">
							</div>
							
						</div>
						<!-- Heading END -->
						
						<!-- Comment -->
						<div class="item">
							
							<div class="legend">
								<div class="name">
									Comment text<span class="required">*</span>
								</div>
								
								<div class="description">
									Please follow the rules of the site.<br>
									Insulting others,advertising or spamming<br>
									will cause deletion of comment<br>
									and blocking your account.
								</div>
							</div>
							
							<div class="element">
								<textarea name="comment[comment]"
											 class="textarea"
											 id="newcomment-comment"></textarea>
							</div>
							
						</div>
						<!-- Comment END -->
						
						<!-- Spot id -->
						<input type="hidden" name="comment[spot_id]" value="<?php echo $spot->id; ?>">
						<!-- Spot id END -->
						
						<!-- Answer id -->
						<input type="hidden" name="comment[answer_id]"
													id="newcomment-answer-id" value="0">
						<!-- Answer id END -->
						
						<!-- Current page -->
						<input type="hidden" name="current_page[number]"
												   id="newcomment-current-page" value="1">
						<!-- Current page END -->
						
						<!-- Token -->
						<input type="hidden" name="token[name]"  value="newcomment-form">
						<input type="hidden" name="token[value]" value="<?php token('newcomment-form'); ?>">
						<!-- Token END -->
						
						<!-- Submit and Loading -->
						<div class="item">
							
							<div class="save">
								<button type="button" id="third-modal-form-submit" class="submit"
										  onclick="ajax.process_form( 'newcomment-form', 
																				'spots', 
																				'add_comment', 
																				'ajax',
																				false,
																				'third_modal_form',
																				'compact')">
									Add
								</button>
							</div>
							
							<div class="loading" id="third-modal-form-loading">
							</div>
							
						</div>
						<!-- Submit and Loading END -->
						
					</form>
					
				</div>
			</div>
		</div>
		<!-- Add comment END -->
<?php
	endif;
?>