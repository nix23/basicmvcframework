<!-- Top spacer -->
<div class="top-spacer">
</div>
<!-- Top spacer END -->

<!-- Users table -->
<table cellspacing="0" cellpadding="0" class="users-table trim-divs">
	<!-- Heading -->
	<tr id="heading">
		<th id="overall">
			Overall
		</th>

		<th id="statistics">
			Statistics
		</th>

		<th id="registred">
			Registred
		</th>

		<th id="actions">
			Actions
		</th>

		<th id="rank">
			Rank
		</th>
	</tr>
	<!-- Heading END -->

	<!-- Users -->
	<?php
		$highlight = 1;
		foreach($users as $user):
	?>
			<tr class="item<?php if($highlight % 2 == 0) echo " highlight-row"; ?>">
				<!-- Overall -->
				<td class="overall-cell">
					<div class="wrapper">

						<div class="avatar">
							<?php
								if($user->has_avatar()):
							?>
									<img src="<?php
													load_photo($user->avatar_master_name,
																  85,
																  85);
												 ?>" width="80" height="80">
							<?php
								else:
							?>
									<div class="no-avatar"></div>
							<?php
								endif;
							?>

							<div class="status">
								<?php
									if($user->activated == "yes")
										echo "<div class='activated-icon'></div>";
								   else
										echo "<div class='waiting-icon'></div>";
								?>
							</div>
						</div>

						<div class="overall">
							<div class="heading">
								<a href="<?php
												public_link("profile/view/user-$user->id");
											 ?>">
									<h3 class="trim-to-parent">
										<?php echo $user->username; ?>
									</h3>
								</a>
							</div>

							<div class="subheading">
								<?php echo $user->email; ?>
							</div>
						</div>

					</div>
				</td>
				<!-- Overall END -->

				<!-- Statistics -->
				<td class="statistics-cell">
					<div class="item">
						<span class='count'>
							<?php echo $user->drives_count; ?>
						</span>

						<?php
							echo ($user->drives_count == 1) ? "Drive" : "Drives";
						?>
					</div>

					<div class="spacer">
					</div>

					<div class="item">
						<span class='count'>
							<?php echo $user->followers_count; ?>
						</span>

						<?php
							echo ($user->followers_count == 1) ? "Follower" : "Followers";
						?>
					</div>

					<div class="spacer">
					</div>

					<div class="item">
						<span class='count'>
							<?php echo $user->favorites_count; ?>
						</span>

						<?php
							echo ($user->favorites_count == 1) ? "Favorite" : "Favorites";
						?>
					</div>
				</td>
				<!-- Statistics END -->

				<!-- Registred -->
				<td class="registred-cell">
					<?php echo time_ago($user->registred_on); ?>
				</td>
				<!-- Registred END -->

				<!-- Actions -->
				<td class="actions-cell">
					<div class="left">
						<span class="action"
								onclick="ajax.process_form('users-list',
																	'users',
																	'change_user_status',
																	'ajax/<?php echo $user->id; ?>',
																	this,
																	'modal')">
							<?php echo ($user->is_account_blocked()) ? "Unblock" : "Block"; ?>
						</span>
					</div>

					<div class="right">
						<span class="action"
								onclick="form_tools.delete_confirmation.show('users-list',
																							'users',
																							'delete_user',
																							'ajax/<?php
																								 echo $user->id;
																								 echo "/" . $current_page;
																								 echo "/" . $selected_sort;
																								 echo "/" . $current_prefix;
																									?>',
																							this,
																							'modal')">
							Delete
						</span>
					</div>
				</td>
				<!-- Actions END -->

				<!-- Rank -->
				<td class="rank-cell">
					<div class="number">
						<?php echo $user->rank; ?>
					</div>

					<div class="label">
						Fordriver
					</div>
				</td>
				<!-- Rank END -->
			</tr>
	<?php
			$highlight++;
		endforeach;
	?>
	<!-- Users END -->

</table>
<!--Users table END -->

<?php
	if(!$users):
?>
		<!-- No users -->
		<div class="no-users">
			<div class="message">

				<div class="icon">
				</div>

				<div class="label">
					No users with selected prefix found.
				</div>

			</div>
		</div>
		<!-- No users END -->
<?php
	endif;
?>