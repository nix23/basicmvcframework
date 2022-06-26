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
							$link  = "users/list/";
							$link .= "page-$page/";
							$link .= "sort-$selected_sort/";
							$link .= "prefix-$current_prefix";
							admin_link($link);
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