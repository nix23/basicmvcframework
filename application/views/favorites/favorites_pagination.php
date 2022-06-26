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
								public_link("favorites/list/$selected_module/page-$page");
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