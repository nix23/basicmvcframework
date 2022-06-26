<!-- Photosets table -->
<table cellspacing="0" cellpadding="0" class="items-table trim-divs">
	<!-- Heading -->
	<tr id="heading">
		<th id="overall">
			Overall
		</th>
		
		<th id="likes">
			Likes
		</th>
		
		<th id="status">
			Status
		</th>
		
		<th id="actions">
			Actions
		</th>
	</tr>
	<!-- Heading END -->
	
	<!-- Photosets -->
	<?php
		$highlight = 1;
		foreach($photosets as $photoset):
	?>
			<tr class="item<?php if($highlight % 2 == 0) echo " highlight"; ?>">
				<!-- Overall -->
				<td class="overall-cell">
					<div class="wrapper">
						
						<div class="photo">
							<img src="<?php
											load_photo($photoset->main_photo->master_name,
														  80,
														  60);
										 ?>" width="80" height="60">
							
							<div class="adddate">
								<?php echo date("d/m/Y H:i", strtotime($photoset->posted_on)); ?>
							</div>
						</div>
						
						<div class="overall">
							<div class="heading">
								<a href="<?php 
												photoset_item_link($photoset,
																		 $photoset->category,
																		 $photoset->subcategory);
											 ?>">
									<h3 class="trim-to-parent">
										<?php 
											stringify(array(
												$photoset->year,
												$photoset->category_name,
												$photoset->subcategory_name,
												$photoset->name
											));  
										?>
									</h3>
								</a>
							</div>
							
							<div class="info">
								<span class="label">
									Moderated:
								</span>
								
								<span class="message"
										onclick="ajax.process_form('photosets-list',
																			'photos',
																			'change_moderation',
																			'ajax/<?php echo $photoset->id; ?>',
																			this,
																			'modal')">
									<?php echo ucfirst($photoset->moderated); ?>
								</span>
								
								<span class="label">
									Comments:
								</span>
								
								<span class="message">
									<?php echo $photoset->comments_count; ?>
								</span>

								<span class="label">
									Views:
								</span>

								<span class="message">
									<?php echo $photoset->item_views_count; ?>
								</span>
							</div>
						</div>
						
					</div>
				</td>
				<!-- Overall END -->
				
				<!-- Likes -->
				<td class="likes-cell">
					<div class="likes">
						
						<div class="wrapper">
							<div class="count">
								<?php echo $photoset->likes_count; ?>
							</div>
							
							<div class="label">
								<?php
									if($photoset->likes_count == 1):
										echo ucfirst("like");
									else:
										echo ucfirst("likes");
									endif;
								?>
							</div>
						</div>
						
					</div>
				</td>
				<!-- Likes END -->
				
				<!-- Status -->
				<td class="status-cell">
					<span class="status"
							onclick="ajax.process_form('photosets-list',
																'photos',
																'change_status',
																'ajax/<?php echo $photoset->id; ?>',
																this,
																'modal')">
						<?php echo ucfirst($photoset->status); ?>
					</span>
				</td>
				<!-- Status END -->
				
				<!-- Actions -->
				<td class="actions-cell">
					<div class="wrapper">
						
						<div class="edit">
							<a class="editlink" href="<?php 
											if($selected_subcategory)
												admin_link("photos/form/{$photoset->id}/{$selected_subcategory->id}");
											else if($selected_category)
												admin_link("photos/form/{$photoset->id}/{$selected_category->id}");
											else
												admin_link("photos/form/{$photoset->id}");
										 ?>">
								Edit
							</a>
						</div>
						
						<div class="delete"
							  onclick="form_tools.delete_confirmation.show('photosets-list',
																						  'photos',
																						  'delete',
																						  'ajax/<?php
																								echo $photoset->id;
																								echo "/" . $current_page;
																								echo "/" . $selected_sort;
																								if($selected_subcategory)
																									echo "/" . $selected_subcategory->id;
																								else if($selected_category)
																									echo "/" . $selected_category->id;
																									?>',
																						  false,
																						  'modal')">
							Delete
						</div>
						
					</div>
				</td>
				<!-- Actions END -->
			</tr>
	<?php
			$highlight++;
		endforeach;
	?>
	<!-- Photosets END -->
	
	<!-- Token -->
	<form name='photosets-list'>
		<input type='hidden' name='token[name]'  value='photosets-list'>
		<input type='hidden' name='token[value]' value='<?php token('photosets-list'); ?>'>
	</form>
	<!-- Token END -->
	
	<?php
		if(!$photosets):
	?>
			<tr class="no-items">
				<td colspan="4">
					
					<div>
						No items attached to this category. Please add some.
					</div>
					
				</td>
			</tr>
	<?php
		endif;
	?>
</table>
<!-- Photosets table END -->