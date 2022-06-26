<!-- Videos table -->
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
	
	<!-- Videos -->
	<?php
		$highlight = 1;
		foreach($videos as $video):
	?>
			<tr class="item<?php if($highlight % 2 == 0) echo " highlight"; ?>">
				<!-- Overall -->
				<td class="overall-cell">
					<div class="wrapper">
						
						<div class="photo">
							<img src="<?php
											load_photo($video->main_photo->master_name,
														  80,
														  60);
										 ?>" width="80" height="60">
							
							<div class="adddate">
								<?php echo date("d/m/Y H:i", strtotime($video->posted_on)); ?>
							</div>
							
							<div class="video">
							</div>
						</div>
						
						<div class="overall">
							<div class="heading">
								<a href="<?php 
												video_item_link($video,
																	 $video->category,
																	 $video->subcategory);
											 ?>">
									<h3 class="trim-to-parent">
										<?php 
											echo $video->heading;
										?>
									</h3>
								</a>
							</div>
							
							<div class="info">
								<span class="label">
									Moderated:
								</span>
								
								<span class="message"
										onclick="ajax.process_form('videos-list',
																			'videos',
																			'change_moderation',
																			'ajax/<?php echo $video->id; ?>',
																			this,
																			'modal')">
									<?php echo ucfirst($video->moderated); ?>
								</span>
								
								<span class="label">
									Comments:
								</span>
								
								<span class="message">
									<?php echo $video->comments_count; ?>
								</span>

								<span class="label">
									Views:
								</span>

								<span class="message">
									<?php echo $video->item_views_count; ?>
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
								<?php echo $video->likes_count; ?>
							</div>
							
							<div class="label">
								<?php
									if($video->likes_count == 1):
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
							onclick="ajax.process_form('videos-list',
																'videos',
																'change_status',
																'ajax/<?php echo $video->id; ?>',
																this,
																'modal')">
						<?php echo ucfirst($video->status); ?>
					</span>
				</td>
				<!-- Status END -->
				
				<!-- Actions -->
				<td class="actions-cell">
					<div class="wrapper">
						
						<div class="edit">
							<a href="<?php 
											if($selected_subcategory)
												admin_link("videos/form/{$video->id}/{$selected_subcategory->id}");
											else if($selected_category)
												admin_link("videos/form/{$video->id}/{$selected_category->id}");
											else
												admin_link("videos/form/{$video->id}");
										 ?>">
								Edit
							</a>
						</div>

						<div class="delete"
							  onclick="form_tools.delete_confirmation.show('videos-list',
																						  'videos',
																						  'delete',
																						  'ajax/<?php
																								echo $video->id;
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
	<!-- Videos END -->
	
	<!-- Token -->
	<form name='videos-list'>
		<input type='hidden' name='token[name]'  value='videos-list'>
		<input type='hidden' name='token[value]' value='<?php token('videos-list'); ?>'>
	</form>
	<!-- Token END -->
	
	<?php
		if(!$videos):
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
<!-- Videos table END -->