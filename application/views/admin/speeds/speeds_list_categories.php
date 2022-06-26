<!-- Categories table -->
<table cellspacing="0" cellpadding="0" class="categories">
	<!-- Heading -->
	<tr id="heading">
		<th id="categories">
			Categories
		</th>
	</tr>
	<!-- Heading END -->
	
	<!-- Subcategories -->
	<?php 
		if($selected_category
				and
			$selected_category->subcategories):
				foreach($selected_category->subcategories as $subcategory):
					if($selected_subcategory
							and
						$selected_subcategory->id == $subcategory->id):
	?>
						<tr class="item">
							<td class="subcategory-cell">
								
								<div class="wrapper selected">
									<span class="text">
										<?php echo $subcategory->name; ?>
									</span>
								</div>
								
							</td>
						</tr>
	<?php
					else:
	?>
						<tr class="item">
							<td class="subcategory-cell">
								
								<a href="<?php 
												admin_module_link("speed",
																		"index",
																		$selected_category,
																		$subcategory,
																		1,
																		$selected_sort);
											?>">
									<div class="wrapper active">
										<span class="text">
												<?php echo $subcategory->name; ?>
										</span>
									</div>
								</a>
								
							</td>
						</tr>
	<?php
					endif;
				endforeach;
	?>
						<!-- Separator -->
						<tr class="item">
							<td class="subcategory-cell">
								
								<div class="separator">
								</div>
								
							</td>
						</tr>
						<!-- Separator END -->
	<?php
		endif;
	?>
	<!-- Subcategories END -->
	
	<!-- Categories -->
	<?php
		$highlight = 1;
		foreach($categories as $category):
			if($selected_category
					and
				$selected_category->id == $category->id):
	?>
				<tr class="item">
					<td class="category-cell">
						
						<div class="wrapper selected">
							<span class="text">
								<?php echo $category->name; ?>
							</span>
						</div>
						
					</td>
				</tr>
	<?php
			else:
	?>
				<tr class="item<?php if($highlight % 2 == 0) echo " highlight"; ?>">
					<td class="category-cell">
						
						<a href="<?php
										admin_module_link("speed",
																"index",
																$category,
																false,
																1,
																$selected_sort);
									?>">
							<div class="wrapper active">
								<span class="text">
										<?php echo $category->name; ?>
								</span>
							</div>
						</a>
						
					</td>
				</tr>
	<?php
			endif;
			$highlight++;
		endforeach;
	?>
	<!-- Categories END -->
</table>
<!-- Categories table END -->