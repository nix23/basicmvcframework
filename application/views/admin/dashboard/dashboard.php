<!-- Page heading -->
<div class="page-heading">
	<div class="wrapper">

		<div class="legend">
			<div class="name">
				Dashboard
			</div>

			<div class="subname">
				What's new on fordrive
			</div>
		</div>

		<?php echo $settings; ?>
		
	</div>
</div>
<!-- Page heading END -->

<!-- Page controls -->
<div class="page-controls">

	<!-- Pagination -->
	<div class="pagination">
		<div class="wrapper ajax-pagination">
			<?php include("dashboard_pagination.php"); ?>
		</div>
	</div>
	<!-- Pagination END -->

	<!-- Sorting -->
	<div class="sorting">
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
					<div class="wrapper">
						<a href="<?php
										$link  = "dashboard/list/";
										$link .= "page-$current_page/";
										$link .= "days-$days_to_fetch_item->value/";
										$link .= "events-$selected_events_to_show";
										admin_link($link);
									?>">
							<div class="item active">
								<?php echo ucfirst($days_to_fetch_item->label); ?>
							</div>
						</a>
					</div>
		<?php
				endif;
			endforeach;
		?>
	</div>
	<!-- Sorting END -->

	<!-- Events to show -->
	<div class="events-to-show">
		<div class="select-button"
			  onmouseover="html_tools.dashboard.show_events_to_show_list()">
			<div class="icon">
			</div>

			<div class="label">
				<?php echo ucfirst($selected_events_to_show); ?>
			</div>
		</div>
	</div>
	<!-- Events to show END -->

	<!-- Events to show select list -->
	<div id="events-to-show-list">

		<?php
			$highlight_column = 2;
			foreach($events_to_show_items as $events_to_show_item):
				if($events_to_show_item->selected):
		?>
					<div class="item<?php if($highlight_column == 3 or $highlight_column == 4) echo " highlight"; ?>">

						<div class="spacer">
						</div>

						<div class="legend">
							<div class="heading selected">
								<?php echo $events_to_show_item->label; ?>
							</div>

							<div class="subheading">
								<?php echo $events_to_show_item->sublabel; ?>
							</div>
						</div>

					</div>
		<?php
				else:
		?>
					<a href="<?php
									$link  = "dashboard/list/";
									$link .= "page-1/";
									$link .= "days-$current_days_to_fetch/";
									$link .= "events-$events_to_show_item->value";
									admin_link($link);
								?>">
						<div class="item<?php if($highlight_column == 3 or $highlight_column == 4) echo " highlight"; ?>">

							<div class="spacer">
							</div>

							<div class="legend">
								<div class="heading">
									<?php echo $events_to_show_item->label; ?>
								</div>

								<div class="subheading">
									<?php echo $events_to_show_item->sublabel; ?>
								</div>
							</div>

						</div>
					</a>
		<?php
				endif;
				$highlight_column++;
				if($highlight_column == 5)
					$highlight_column = 1;
			endforeach;
		?>

	</div>
	<!-- Events to show select list END -->

</div>
<!-- Page controls END -->

<!-- Token -->
<form name='dashboard-events'>
	<input type='hidden' name='token[name]'  value='dashboard-events'>
	<input type='hidden' name='token[value]' value='<?php token('dashboard-events'); ?>'>
</form>
<!-- Token END -->

<!-- Dashboard events -->
<div id="dashboard"
	  class="ajax-dashboard-events">
	<?php include("dashboard_events.php"); ?>
</div>
<!-- Dashboard events END -->

<!-- Page controls -->
<div class="page-controls">

	<!-- Pagination -->
	<div class="pagination">
		<div class="wrapper ajax-pagination">
			<?php include("dashboard_pagination.php"); ?>
		</div>
	</div>
	<!-- Pagination END -->

</div>
<!-- Page controls END -->