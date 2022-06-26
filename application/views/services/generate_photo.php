<!DOCTYPE html>
<html>
	<head>
		<meta charset='utf-8'>

		<?php
			uncached_css('services/services');
		?>

		<script>
			php_vars = {
				base_url: "<?php echo Url::get_base_url(); ?>"
			}
		</script>

		<?php
			uncached_js('jquery');
			uncached_js('services');
		?>

		<script>
			$(document).ready(function(){
				$.ajaxSetup({
					type: "POST"
				});

				ajax.process("services",
								 "generate_photo",
								 "ajax/<?php
											echo $module . "/";
											echo $photo_id . "/";
											echo $photo_width . "/";
											echo $photo_height;
										 ?>",
								 false);
			});
		</script>
	</head>

	<body id="generate-photo">
		<div class="prompt">
			<div class="loading-animation">
			</div>

			<div class="logo">
			</div>

			<div class="legend">
				Generating photo. Please wait a moment...
			</div>
		</div>
	</body>
</html>