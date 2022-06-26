<!DOCTYPE html>
<html>
	<head>
		<title>Fordrive / Backend</title>
		<meta charset='utf-8'>
		<link rel="shortcut icon" href="<?php echo get_base_url(); ?>img/favicon.ico">
		
		<!-- Connecting stylesheets -->
		<?php
			uncached_css("base/reset");
			uncached_css("base/layout");
			uncached_css("base/header");
			uncached_css("base/shared");
			uncached_css("base/form");
			uncached_css("categories/categories_list");
			uncached_css("dashboard/dashboard");
			uncached_css("users/users");
		?>
		
		<!-- Connecting javascripts -->
		<?php
			admin_php_to_js();
			
			js("jquery");
			uncached_js("debug");
			uncached_js("effects");
			uncached_js("library");
			uncached_js("ajax");
			uncached_js("ajax_file_uploader");
			uncached_js("overlay");
			uncached_js("modal_loading");
			uncached_js("gallery");
			uncached_js("form_tools");
			uncached_js("html_tools");
			uncached_js("category");
			uncached_js("initialize");
		?>
	</head>
	
	<body>
		<!-- Page container -->
		<div id="body-wrapper">
			
			<!-- Debugger -->
			<div id="debugger">
				
				<div class="header">
					<h2>Debugger</h2>
					
					<div class="close"
						  onclick="debug.hide()">
					</div>
				</div>
				
				<div id="debugger-content">
				</div>
				
			</div>
			<!-- Debugger END -->
			
			<!-- Overlay -->
			<div id="overlay">
			</div>
			<!-- Overlay END -->

			<!-- Settings form -->
			<div id="settings-form">
				<div id="settings-form-spinner"
					  class="settings-form">
					<div class="wrapper">
						<form name="settings-form">

						<!-- Heading -->
						<div class="heading">

							<div class="info">
								<div class="label">
									Settings
								</div>

								<div class="sublabel">
									Service functions
								</div>
							</div>

							<div class="close"
								  onclick="form_tools.settings.hide()">
							</div>

						</div>
						<!-- Heading END -->

						<!-- Content -->
						<div class="content">

							<!-- Setting -->
							<div class="settings-row">
								<div class="legend">
									<div class="label">
										Clear 'ajax' directory
									</div>

									<div class="sublabel">
										Deletes all images from temporary 'uploads/ajax'<br> directory.
										Now it contains <span class="ajaxdir-files-count"><?php echo $ajax_directory_files_count; ?></span>
										<?php echo ($ajax_directory_files_count == 1) ? "file" : "files"; ?>.
									</div>
								</div>

								<div class="action">
									<div class="process">
										<button type="button" class="submit"
												  onclick="ajax.process_form( 'settings-form',
																						'settings',
																						'clear_ajax_directory',
																						'ajax',
																						this,
																						'settings_form')">
											Clear
										</button>
									</div>

									<div class="loading">
									</div>
								</div>
							</div>
							<!-- Setting END -->

							<!-- Setting -->
							<div class="settings-row">
								<div class="legend">
									<div class="label">
										Clear 'cache' directory
									</div>

									<div class="sublabel">
										Deletes all images from temporary 'uploads/cache'<br> directory.
										Now it contains <span class="ajaxdir-files-count"><?php echo $cache_directory_files_count; ?></span>
										<?php echo ($cache_directory_files_count == 1) ? "file" : "files"; ?>.
									</div>
								</div>

								<div class="action">
									<div class="process">
										<button type="button" class="submit"
												  onclick="ajax.process_form( 'settings-form',
																						'settings',
																						'clear_cache_directory',
																						'ajax',
																						this,
																						'settings_form')">
											Clear
										</button>
									</div>

									<div class="loading">
									</div>
								</div>
							</div>
							<!-- Setting END -->

							<!-- Setting -->
							<div class="settings-row">
								<div class="legend">
									<div class="label">
										Delete all unactivated accounts
									</div>

									<div class="sublabel">
										Deletes all unactivated accounts, which were registred more than 3 days ago.
										Number of that accounts: <span class="ajaxdir-files-count"><?php echo $unactivated_users_count; ?></span>
									</div>
								</div>

								<div class="action">
									<div class="process">
										<button type="button" class="submit"
												  onclick="ajax.process_form( 'settings-form',
																						'settings',
																						'delete_unactivated_accounts',
																						'ajax',
																						this,
																						'settings_form')">
											Delete
										</button>
									</div>

									<div class="loading">
									</div>
								</div>
							</div>
							<!-- Setting END -->

							<!-- Setting -->
							<div class="settings-row">
								<div class="legend">
									<div class="label">
										Compile CSS & JS
									</div>

									<div class="sublabel">
										Compiles all css in js file into one file.<br>
										They should be marked in layout file.
									</div>
								</div>

								<div class="action">
									<div class="process">
										<button type="button" class="submit"
												  onclick="ajax.process_form( 'settings-form',
																						'settings',
																						'compile_resources',
																						'ajax',
																						this,
																						'settings_form')">
											Compile
										</button>
									</div>

									<div class="loading">
									</div>
								</div>
							</div>
							<!-- Setting END -->

						</div>
						<!-- Content END -->

						<!-- Token -->
						<input type="hidden" name="token[name]"  value="settings-form">
						<input type="hidden" name="token[value]" value="<?php token('settings-form'); ?>">
						<!-- Token END -->

						</form>
					</div>
				</div>
			</div>
			<!-- Settings form END -->

			<!-- Gallery -->
			<div id="gallery">
				
				<div class="photo">
					
					<span class="image">
					</span>
					
					<div class="previous">
					</div>
					
					<div class="next">
					</div>
					
				</div>
				
				<div class="panel">
					
					<div class="current">
						<span class="count"></span>
						<span class="of">of</span>
					</div>
					
					<div class="total">
					</div>
					
					<div class="description">
						<div class="wrapper">
							
								<div class="heading">
									Photo preview
								</div>
								
								<div class="subheading">
									Depending on module, images with all available resolutions
									will be created.
								</div>
								
						</div>
					</div>
					
					<div class="content">
					</div>
					
					<div class="close">
					</div>
					
				</div>
				
			</div>
			<!-- Gallery END -->

			<!-- Delete confirmation -->
			<div id="delete-confirmation">

				<div class="top">
					<div class="delete-icon">
					</div>

					<div class="message">
						<div class="wrapper">
							Are you sure that you want delete this record?
						</div>
					</div>
				</div>

				<div class="bottom">
					<div class="item delete"
							onclick="form_tools.delete_confirmation.process_delete()">
						Delete
					</div>

					<div class="item cancel"
							onclick="form_tools.delete_confirmation.cancel_delete()">
						Cancel
					</div>
				</div>

			</div>
			<!-- Delete confirmation END -->

			<!-- Confirmation prompt -->
			<div id="confirmation-prompt">

				<div class="top">
					<div class="prompt-icon">
					</div>

					<div class="message">
						<div class="wrapper">
						</div>
					</div>
				</div>

				<div class="bottom">
					<div class="item process"
							onclick="form_tools.confirmation_prompt.process()">
					</div>

					<div class="item cancel"
							onclick="form_tools.confirmation_prompt.cancel()">
					</div>
				</div>

			</div>
			<!-- Confirmation prompt END -->

			<!-- Header -->
			<div id="header">
				<?php echo $header; ?>
			</div>
			<!-- Header END -->

			<!-- Content -->
			<div id="content">
				
				<!-- Modal ajax loading -->
				<div id="overlay-loading">
					
					<div class="wrapper">
						<div class="message">
						</div>
						
						<div class="icon">
						</div>
					</div>
					
				</div>
				<!-- Modal ajax loading END -->
				
				<?php echo $content; ?>
				
			</div>
			<!-- Content END -->
			
		</div>
		<!-- Page container END -->
	</body>
</html>