debug = {
	rows:        new Array(),
	current_row: null,
	initialized: false,
	
	show: function()
	{
		this.rows.push(this.current_row);
		
		for(i = 0; i < this.rows.length; i++)
		{
			var row_html = "";
			var row      = this.rows[i];
			
			row_html += "<div class='row'>";
			row_html += "	<div class='content'>";
			
			for(key in this.rows[i])
			{ 
				row_html += "<div>";
				row_html += key + ": " + row[key];
				row_html += "</div>";
			}
			
			row_html += "	</div>";
			row_html += "</div>";
			
			$("#debugger-content").html($("#debugger-content").html() + row_html); 
		}
		
		$("#debugger").css("display", "block");
	},
	
	hide: function()
	{
		$("#debugger-content").html("");
		$("#debugger").css("display", "none");
	},
	
	new_row: function()
	{
		if(this.initialized)
		{
			this.rows.push(this.current_row);
		}
		
		this.current_row = new Object(); 
		this.initialized = true;
	},
	
	add: function(key, value)
	{
		this.current_row[key] = value;
	}
}