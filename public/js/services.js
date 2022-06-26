ajax = {
	callbacks: {
		data: {},
		html_object: {},

		redirect: function()
		{
			var url = php_vars.base_url + this.data.url_segments;
			window.location.href = url;
		}
	},

	parse_server_answer: function(server_answer)
	{
		var result   = server_answer.result;
		var errors   = server_answer.errors;
		var callback = server_answer.callback;
		var data     = server_answer.data;

		if(result == "ok")
		{
			if(callback != "none")
			{
				this.callbacks.data = data;
				this.callbacks[callback]();
			}
		}
		else
		{
			alert(errors);
		}
	},

	process: function(controller,
							action,
							parametrs,
							html_object)
	{
		this.callbacks.html_object = html_object;
		var url_segments           = controller + "/" + action + "/" + parametrs;

		$.ajax({
			url:      php_vars.base_url + url_segments,
			datatype: "json",
			context:  this,

			success: function (data, textStatus)
			{
				try
				{
					var server_answer = jQuery.parseJSON(data);
				}
				catch(e)
				{
					alert(data);
				}

				this.parse_server_answer(server_answer);
			}
		});
	}
}