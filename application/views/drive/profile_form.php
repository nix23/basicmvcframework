<!-- Form -->
<div class="form">
	
	<!-- Add photo form -->
	<div id="photo-upload-spinner" class="photo-upload-form">
		<div class="spinner-wrapper">
			
			<!-- Header -->
			<div class="header">
				<h2>Avatar Upload</h2>
				
				<div class="close"
					  onclick="effects.spinner.toggle('photo-upload-spinner')">
				</div>
			</div>
			<!-- Header END -->
			
			<!-- File select -->
			<div class="file-select">
				
				<form enctype="multipart/form-data" method="post" target="ajax-iframe">
					<input type="file" name="upload-file">
				</form>
				
			</div>
			<!-- File select END -->
			
			<!-- Description -->
			<div class="description">
				<p>Minimal avatar dimensions are: 100 * 100px. You can upload your photo or avatar here.
					Please remember, that photo will be adjusted to square mode.</p>
			</div>
			<!-- Description END -->
			
			<!-- Actions -->
			<div class="actions">
				
				<div class="uploading">
					<div class="icon">
					</div>
				</div>
				
				<div class="upload">
					<button type="button" class="button"
							  onclick="ajax_file_uploader.upload('photo-upload-spinner',
																			 'avatar-photos',
																			 'drive',
																			 'upload_avatar')">
						Upload
					</button>
				</div>
				
			</div>
			<!-- Actions END -->
			
			<!-- Footer -->
			<div class="footer">
			</div>
			<!-- Footer END -->
			
		</div>
	</div>
	<!-- Add photo form END -->
	
	<!-- Profile form -->
	<form name="profile-form">
	
	<!-- Backlink -->
	<div class="backlink">
		<a href="<?php public_link("drive"); ?>">
			<< Back to drive
		</a>
	</div>
	<!-- Backlink END -->
	
	<!-- Heading -->
	<div class="heading">
		Edit profile
	</div>
	<!-- Heading END -->
	
	<!-- E-mail -->
	<div class="item">
		
		<div class="legend">
			<div class="name">
				E-mail<span class="required">*</span>
			</div>
			
			<div class="description">
				Your e-mail will be used for service functions.
			</div>
		</div>
		
		<div class="element">
			<input type="text" name="display_only[email]" maxlength="255" class="input long"
					 value="<?php echo $user->email; ?>" disabled>
		</div>
		
	</div>
	<!-- E-mail END -->
	
	<!-- Login -->
	<div class="item">
		
		<div class="legend">
			<div class="name">
				Login<span class="required">*</span>
			</div>
			
			<div class="description">
				Your login will be displayed near all your activity at site.<br>
				(Posts,comments,likes,etc.)
			</div>
		</div>
		
		<div class="element">
			<input type="text" name="display_only[username]" maxlength="255" class="input"
					 value="<?php echo $user->username; ?>" disabled>
		</div>
		
	</div>
	<!-- Login END -->
	
	<!-- Subname -->
	<div class="item">
		
		<div class="legend">
			<div class="name">
				Subname
			</div>
			
			<div class="description">
				You can enter here your name and surname,or some custom caption.<br>
				Subname will be displayed everywhere under login.
			</div>
		</div>
		
		<div class="element">
			<input type="text" name="account[subname]" maxlength="40" class="input"
					 value="<?php echo $user->subname; ?>">
		</div>
		
	</div>
	<!-- Subname END -->
	
	<!-- Avatar -->
	<div class="item" id="update-photo-upload-spinner-top">
		
		<div class="legend">
			<div class="name">
				Avatar
			</div>
			
			<div class="description">
				Upload your photo or avatar picture.
			</div>
		</div>
		
		<div class="element">
			<div id="avatar-photos" class="single-photo">
				
				<!-- Upload button -->
				<div class="add"
					  onclick="effects.spinner.toggle('photo-upload-spinner',
					  											 true,
					  											 'update-photo-upload-spinner-top',
					  											 this)">
					Upload
				</div>
				<!-- Upload button END -->
				
				<!-- Avatar image -->
				<div class="photo-wrapper">
					<?php
						if(!empty($user->avatar_master_name)):
					?>
							<img src="<?php load_photo($user->avatar_master_name, 75, 75); ?>"
								  width="75" height="75">
							<div  class="delete"
									onclick="form_tools.single_photo.remove(this)">
							</div>
					<?php
						else:
					?>
							<div class="no-avatar">
							</div>
					<?php
						endif;
					?>
				</div>
				<!-- Avatar image END -->
				
				<!-- Image frames -->
				<span class="frames">
					<?php
						if(!empty($user->avatar_master_name)):
					?>
						<input type="hidden" name="avatar-photos[0][frame]"
													id="<?php echo $user->avatar_master_name; ?>"
													value="<?php echo $user->avatar_master_name; ?>">
					<?php
						endif;
					?>
				</span>
				<!-- Image frames END -->
				
			</div>
		</div>
		
	</div>
	<!-- Avatar END -->
	
	<!-- Description -->
	<div class="item">
		
		<div class="legend">
			<div class="name">
				Description
			</div>
			
			<div class="description">
				Write something about yourself.<br>
				Other users will see this at your profile page.
			</div>
		</div>
		
		<div class="element">
			<textarea name="account[description]"
						 cols="30" rows="5"
						 class="textarea"><?php echo $user->description; ?></textarea>
		</div>
		
	</div>
	<!-- Description END -->
	
	<!-- Created -->
	<div class="item">
		
		<div class="legend">
			<div class="name">
				Created<span class="required">*</span>
			</div>
			
			<div class="description">
				How long you are on fordrive?
			</div>
		</div>
		
		<div class="element">
			<div class="text">
				<?php echo time_ago($user->registred_on); ?>
			</div>
		</div>
		
	</div>
	<!-- Created END -->
	
	<!-- Token -->
	<input type="hidden" name="token[name]"  value="profile-form">
	<input type="hidden" name="token[value]" value="<?php token('profile-form'); ?>">
	<!-- Token END -->
	
	<!-- Submit and Loading -->
	<div class="item">
		
		<div class="save">
			<button type="button" id="form-submit" class="submit"
					  onclick="ajax.process_form('profile-form', 'drive', 'save', 'ajax')">
				Save
			</button>
		</div>
		
		<div class="loading" id="form-loading">
		</div>
		
	</div>
	<!-- Submit and Loading END -->
	
	</form>
	<!-- Profile form END -->
	
</div>
<!-- Form END -->