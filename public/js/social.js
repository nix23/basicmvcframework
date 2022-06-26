social = {
	facebook: {
		init: function() {
		  if(!php_vars.isLoggedIn)
		  	this.initLoggedOutFunctionality();
		  else
		  	this.initLoggedInFunctionality();
		},

		initLoggedOutFunctionality: function()
		{
		  window.fbAsyncInit = function() {
		    FB.init({
		      appId: '1417384288501099',
		      cookie: true,
		      xfbml: true,
		      oauth: true
		    });
		    
		    FB.Event.subscribe('auth.authResponseChange', function(response) {
		    	if(response.status === "connected") {
		    		ajax.process_form("login-form", "account", "facebookLogin", "ajax", false, "modal_wait_for_redirect");
		    	}
		    });
		  };
		  (function() {
		    var e = document.createElement('script'); e.async = true;
		    e.src = document.location.protocol +
		      '//connect.facebook.net/en_US/all.js';
		    document.getElementById('fb-root').appendChild(e);
		    var $fbLoginButton = $("#header").find(".actions").find(".fb-login-wrapper").children();
		  }());
		},

		initLoggedInFunctionality: function()
		{
		  $("body").find(".logout").on("click", function(event) {
			  window.fbAsyncInit = function() {
			    FB.init({
			      appId: '1417384288501099',
			      cookie: true,
			      xfbml: true,
			      oauth: true
			    });
					FB.getLoginStatus(function(response) {
					  if (response.status === 'connected') {
				    	FB.logout(function() {
				    		ajax.process('account',
										 'logout',
										 'ajax',
										 '',
										 'logout');
				    	});
					  }
					  else {
				    		ajax.process('account',
										 'logout',
										 'ajax',
										 '',
										 'logout');
					  }
					});
			    
			  };
			  (function() {
			    var e = document.createElement('script'); e.async = true;
			    e.src = document.location.protocol +
			      '//connect.facebook.net/en_US/all.js';
			    document.getElementById('fb-root').appendChild(e);
			    var $fbLoginButton = $("#header").find(".actions").find(".fb-login-wrapper").children();
			  }());
		  });
		}
	}
}