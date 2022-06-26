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
							admin_module_link("videos",
													"index",
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