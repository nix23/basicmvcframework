<!-- Page controls -->
<div class="page-controls">
	
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
										module_link("videos",
														"list",
														$selected_category,
														$selected_subcategory,
														$page,
														$selected_sort);
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
	
	<!-- Sorting -->
	<div class="sorting">
		<div class="heading">
			Sort by:
		</div>
		
		<?php
			foreach($sort_items as $sort_item):
		?>
				<div class="wrapper">
					<a href="<?php
									module_link("videos",
													"list",
													$selected_category,
													$selected_subcategory,
													$current_page,
													$sort_item->sort);
								?>">
						<div class="<?php
											if($sort_item->selected)
												echo "item selected";
											else
												echo "item active";
										?>">
							<?php echo ucfirst($sort_item->label); ?>
						</div>
					</a>
				</div>
		<?php
			endforeach;
		?>
	</div>
	<!-- Sorting END -->
	
	<!-- Add button -->
	<?php
		if($authorized):
	?>
			<div class="add-button">
				<a href="<?php 
								if($selected_subcategory)
									public_link("videos/form/add/" . $selected_subcategory->id);
								else if($selected_category)
									public_link("videos/form/add/" . $selected_category->id);
								else
									public_link("videos/form");
							?>">
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
<!-- Page controls END -->

<!-- Videos and categories -->
<div class="page-content">
	
	<!-- Videos -->
	<div class="module-items trim-divs">
		<?php
			foreach($videos as $video):
		?>
				<div class="item">
					<a href="<?php
									video_item_link($video,
														 $video->category,
														 $video->subcategory);
								?>"
						onmouseover="html_tools.module_list.item_over(this)" 
						onmouseout="html_tools.module_list.item_out(this)">
						<div class="item-wrapper">
							
							<img src="<?php 
											load_photo( $video->main_photo->master_name, 
															270, 
															180); 
										 ?>" width="270" height="180"
									onclick="html_tools.ie7_image_inside_link_click_fix(this)">
							
							<div class="likes">
								<div class="count">
									<?php echo $video->likes_count; ?>
								</div>

								<div class="label">
									<?php
										if($video->likes_count == 1)
											echo "Like";
										else
											echo "Likes";
									?>
								</div>
							</div>
							
							<div class="info-small">
								<div class="top">
									<?php echo views_to_compact_form($video->views_count); ?>
								</div>

								<div class="bottom">
									<?php echo ($video->views_count == 1) ? "View" : "Views"; ?>
								</div>
							</div>
							
							<div class="info">
								
								<div class="heading">
									<div class="wrapper">
										<h3 class="trim-to-parent">
											<?php
												echo $video->heading;
											?>
										</h3>
									</div>
								</div>
								
								<div class="comments">
									<div class="count">
										<?php echo $video->comments_count; ?>
									</div>

									<div class="label">
										<?php echo ($video->comments_count == 1) ? "comment" : "comments"; ?>
									</div>
								</div>
								
							</div>
							
						</div>
					</a>
				</div>
		<?php
			endforeach;
		?>

		<!-- Bottom controls -->
		<div class="bottom-controls">
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
												module_link("videos",
																"list",
																$selected_category,
																$selected_subcategory,
																$page,
																$selected_sort);
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
		</div>
		<!-- Bottom controls END -->
	</div>
	<!-- Videos END -->
	
	<!-- Categories -->
	<div class="module-categories">
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
											module_link("videos",
															"list",
															$selected_category,
															$subcategory,
															1,
															$selected_sort);
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
								module_link("videos",
												"list",
												false,
												false,
												1,
												$selected_sort);
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
		<!-- Sandbox END -->
		
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
									module_link("videos",
													"list",
													$category,
													false,
													1,
													$selected_sort);
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
<!-- Videos and categories END -->