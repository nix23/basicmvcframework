<!-- My drive -->
<div id="mydrive">

	<!-- Ajax requests token -->
	<form name='mydrive-form'>
		<input type='hidden' name='token[name]'  value='mydrive-form'>
		<input type='hidden' name='token[value]' value='<?php token('mydrive-form'); ?>'>
	</form>
	<!-- Ajax request token END -->

	<!-- Heading -->
	<div class="heading">
		<div class="wrapper">
			
			<!-- Legend -->
			<div class="legend">
				
				<div class="label">
					My Drive
				</div>
				
				<div class="sublabel">
					Manage your profile and uploads.
				</div>
				
			</div>
			<!-- Legend END -->
			
			<!-- Actions -->
			<div class="actions">
				
				<a href="<?php public_link("drive/form"); ?>">
					<div class="edit-button">
						<div class="name">
							Edit profile
						</div>
					</div>
				</a>
				
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
					<img src="<?php load_photo($user->avatar_master_name, 85, 85); ?>"
							width="85" height="85">
			<?php
				else:
			?>
					<div class="no-avatar"></div>
			<?php
				endif;
			?>

			<div class="you-label"></div>
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

			<div class="stats"
				  id="ajax-update-rating-stats">
				<?php include("mydrive_rating_stats.php"); ?>
			</div>
		</div>
		<!-- Rating END -->

	</div>
	<!-- User info END -->

	<!-- Controls -->
	<div class="controls">
		
		<!-- Pagination -->
		<div class="pagination ajax-update-pagination">
			<?php include("mydrive_pagination.php"); ?>
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
								
								<div class="count"
									  id="ajax-update-items-count">
									<?php echo views_to_compact_form($module->count); ?>
								</div>
								
							</div>
						</div>
			<?php
					else:
			?>
						<a href="<?php
										drive_link( $module->name,
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
		<div class="items-wrapper">
			<div class="items trim-divs"
				  id="ajax-update-items">
				<?php include("mydrive_items.php"); ?>
			</div>

			<div class="bottom-controls">
				<!-- Pagination -->
				<div class="pagination ajax-update-pagination">
					<?php include("mydrive_pagination.php"); ?>
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
												drive_link( $selected_module,
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
									drive_link( $selected_module,
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
										drive_link( $selected_module,
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
<!-- My drive END -->

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