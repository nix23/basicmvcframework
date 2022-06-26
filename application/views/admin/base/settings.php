<div class="settings">
	<div class="item-big">
		<div class="name">
			New fordrivers
		</div>

		<div class="subname">
			<?php
				echo $activated_users_today_count;
				echo "/";
				echo $registred_users_today_count;
				echo " today";
			?>
		</div>
	</div>

	<div class="item item-big">
		<div class="name"
			  onclick="form_tools.confirmation_prompt.show('recalculate_rating',
																		  'settings-form',
																		  'settings',
																		  'recalculate_users_rank',
																		  'ajax',
																		  this,
																		  'modal')"
			  onmouseover="html_tools.settings.item_over(this)"
			  onmouseout="html_tools.settings.item_out(this)">
			Last rating update
		</div>

		<div class="subname"
			  onclick="form_tools.confirmation_prompt.show('recalculate_rating',
																		  'settings-form',
																		  'settings',
																		  'recalculate_users_rank',
																		  'ajax',
																		  this,
																		  'modal')"
			  onmouseover="html_tools.settings.item_over(this)"
			  onmouseout="html_tools.settings.item_out(this)">
			<?php
				$time_ago_parts = time_ago_splitted($settings->last_rating_update);
				echo $time_ago_parts[0] . " " . $time_ago_parts[1];
			?>
		</div>
	</div>

	<div class="item item-small">
		<div class="name"
			  onclick="form_tools.confirmation_prompt.show('change_site_mode',
																		  'settings-form',
																		  'settings',
																		  'change_site_status',
																		  'ajax',
																		  this,
																		  'modal')"
			  onmouseover="html_tools.settings.item_over(this)"
			  onmouseout="html_tools.settings.item_out(this)">
			Site is
		</div>

		<div class="subname"
			  onclick="form_tools.confirmation_prompt.show('change_site_mode',
																		  'settings-form',
																		  'settings',
																		  'change_site_status',
																		  'ajax',
																		  this,
																		  'modal')"
			  onmouseover="html_tools.settings.item_over(this)"
			  onmouseout="html_tools.settings.item_out(this)">
			<?php
				echo ($settings->mode == "enabled") ? "online" : "offline";
			?>
		</div>
	</div>
</div>

<!-- Token -->
<form name='settings-form'>
	<input type='hidden' name='token[name]'  value='settings'>
	<input type='hidden' name='token[value]' value='<?php token('settings'); ?>'>
</form>
<!-- Token END -->