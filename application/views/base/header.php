<!-- Header container -->
<div class="wrapper">
	<!-- Logo -->
	<a href="<?php public_link("main"); ?>">
		<div class="logo">
		</div>
	</a>
	<!-- Logo END -->
	
	<!-- Menu -->
	<div class="intro">
		<div class="wrapper">
			FORDRIVE.NET is an online car enthusiasts community for
			everything about cars and their owners. 
			Here you have tools to share your car photos and rate others,
			add stories about different car events where you participated.
			Also you can find here latest news about cars and car industry,
			or just write your opinion about them.
		</div>
	</div>
	<!-- Menu END -->
	
	<!-- Actions -->
	<div class="actions">
		
		<div class="enter link"
				onclick="form_tools.login.show()">
			Drive
		</div>
		or
		<div class="register link"
				onclick="form_tools.registration.show()">
			Register
		</div>

		<div class="fb-login-wrapper">
			<div class="fb-login-button" data-max-rows="1" data-size="icon" data-show-faces="false" data-auto-logout-link="false"></div>
		</div>
		
	</div>
	<!-- Actions END -->
</div>
<!-- Header container END -->

<!-- Login form -->
<div id="login-form">
	<div id="login-form-spinner"
		  class="login-form">
		<div class="spinner-wrapper">
			
			<form name="login-form">
			
				<!-- Heading -->
				<div class="heading">
					
					<div class="info">
						<div class="label">
							Authorization
						</div>
						
						<div class="sublabel">
							www.fordrive.net
						</div>
					</div>
					
					<div class="close"
						  onclick="form_tools.login.hide()">
					</div>
					
				</div>
				<!-- Heading END -->
				
				<!-- Username -->
				<div class="item">
					
					<div class="legend">
						<div class="name">
							Login<span class="required">*</span>
						</div>
						
						<div class="description">
							Please enter your account username.
						</div>
					</div>
					
					<div class="element">
						<input type="text" 
								 name="login[username]" 
								 maxlength="25" 
								 class="input" 
								 value="">
					</div>
					
				</div>
				<!-- Username END -->
				
				<!-- Password -->
				<div class="item">
					
					<div class="legend">
						<div class="name">
							Password<span class="required">*</span>
						</div>
						
						<div class="description">
							Please enter your account password.
						</div>
					</div>
					
					<div class="element">
						<input type="password" 
								 name="login[password]" 
								 maxlength="255" 
								 class="input" 
								 value="">
					</div>
					
				</div>
				<!-- Password END -->
				
				<!-- Token -->
				<input type="hidden" name="token[name]"  value="login-form">
				<input type="hidden" name="token[value]" value="<?php token('login-form'); ?>">
				<!-- Token END -->
				
				<!-- Submit and Loading -->
				<div class="item">
					
					<div class="save">
						<button type="button" id="modal-form-submit" class="submit"
								  onclick="ajax.process_form( 'login-form', 
																		'account', 
																		'login', 
																		'ajax',
																		false,
																		'modal_form',
																		'compact')">
							Enter
						</button>
					</div>
					
					<div class="loading" id="modal-form-loading">
					</div>
					
				</div>
				<!-- Submit and Loading END -->
				
			</form>
			
		</div>
	</div>
</div>
<!-- Login form END -->

<!-- Registration form -->
<div id="registration-form">
	<div id="registration-form-spinner"
		  class="registration-form">
		<div class="spinner-wrapper">
			
			<form name="register-form">
			
			<!-- Heading -->
			<div class="heading">
				
				<div class="info">
					<div class="label">
						Registration
					</div>
					
					<div class="sublabel">
						New account
					</div>
				</div>
				
				<div class="close"
					  onclick="form_tools.registration.hide()">
				</div>
				
			</div>
			<!-- Heading END -->
			
			<!-- Username -->
			<div class="item">
				
				<div class="legend">
					<div class="name">
						Login<span class="required">*</span>
					</div>
					
					<div class="description">
						Choose your account username.
					</div>
				</div>
				
				<div class="element">
					<input type="text" 
							 name="registration[username]" 
							 maxlength="25" 
							 class="input" 
							 value="">
				</div>
				
			</div>
			<!-- Username END -->
			
			<!-- Password -->
			<div class="item">
				
				<div class="legend">
					<div class="name">
						Password<span class="required">*</span>
					</div>
					
					<div class="description">
						Please choose strong password.
					</div>
				</div>
				
				<div class="element">
					<input type="password" 
							 name="registration[password]" 
							 maxlength="255" 
							 class="input" 
							 value="">
				</div>
				
			</div>
			<!-- Password END -->
			
			<!-- Repeat password -->
			<div class="item">
				
				<div class="legend">
					<div class="name">
						Repeat password<span class="required">*</span>
					</div>
					
					<div class="description">
						Lets check that password is correct.
					</div>
				</div>
				
				<div class="element">
					<input type="password" 
							 name="registration[password_confirmation]" 
							 maxlength="255" 
							 class="input" 
							 value="">
				</div>
				
			</div>
			<!-- Repeat password END -->
			
			<!-- Email -->
			<div class="item">
				
				<div class="legend">
					<div class="name">
						E-mail<span class="required">*</span>
					</div>
					
					<div class="description">
						We will send confirmation e-mail here.
					</div>
				</div>
				
				<div class="element">
					<input type="text" 
							 name="registration[email]" 
							 maxlength="255" 
							 class="input" 
							 value="">
				</div>
				
			</div>
			<!-- Email END -->
			
			<!-- License -->
			<div class="license">
				<input type="checkbox"
						 name="registration[license]"
						 value="confirmed">
				I have read and agreed with
				<a href="<?php public_link("terms"); ?>"
					target="_blank">
					<span class="highlight">terms and conditions</span>
				</a>.
			</div>
			<!-- License END -->

			<!-- Token -->
			<input type="hidden" name="token[name]"  value="registration-form">
			<input type="hidden" name="token[value]" value="<?php token('registration-form'); ?>">
			<!-- Token END -->
			
			<!-- Submit and Loading -->
			<div class="item">
				
				<div class="save">
					<button type="button" id="second-modal-form-submit" class="submit"
							  onclick="ajax.process_form( 'register-form', 
																	'account', 
																	'create', 
																	'ajax',
																	false,
																	'second_modal_form',
																	'compact')">
						Register
					</button>
				</div>
				
				<div class="loading" id="second-modal-form-loading">
				</div>
				
			</div>
			<!-- Submit and Loading END -->
			
			</form>
			
		</div>
	</div>
</div>
<!-- Registration form END -->