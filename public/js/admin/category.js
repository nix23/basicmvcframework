category = {
	// Id's of categories,which children
	// already loaded
	loaded_childrens:       new Array(),
	clicked_category_row:   null,
	subcategories_list_row: null,
	
	load_children: function(id)
	{
		// Loads subcategories
		ajax.process(
			'categories',
			'load_children_html',
			'ajax/' + id,
			'',
			'modal_no_confirmation'
		); 
		
		this.loaded_childrens.push(id);
	},
	
	set_children_html_and_toggle: function(html)
	{
		$(this.subcategories_list_row).html(html);
		
		this.toogle_loaded_children();
	},
	
	toggle_children: function(html_object, id)
	{
		// Loading table rows
		this.clicked_category_row   = $(html_object).closest("tr");
		this.subcategories_list_row = $(this.clicked_category_row).next("tr");
		
		// Load subcategories,if it's first this list toggle
		if(jQuery.inArray(id, this.loaded_childrens) == -1)
		{
			this.load_children(id); 
		}
		// Else they are loaded,toggle the list
		else
		{
			this.toogle_loaded_children();
		}
	},
	
	toogle_loaded_children: function()
	{
		// Toggle children list,only if it's loaded
		if($(this.subcategories_list_row).is(':visible'))
		{
			$(this.clicked_category_row).removeClass("active"); 
		}
		else
		{
			$(this.clicked_category_row).addClass("active"); 
		}
		
		$(this.subcategories_list_row).toggle();
	},
	
	delete_with_children: function(html_object)
	{
		var closest_table = $(html_object).closest("table");
			
		// If it is root category, deleting subcategories
		if($(closest_table).attr("class") == "categories-table")
		{
			$(html_object).closest("tr").next("tr").remove();
		}
		else
		{
			// Else decreasing subcategories count
			var category_row = $(closest_table).closest("tr").prev(".category");
			var count_span   = $(category_row).find(".subcategories-count").find(".count");
			var count        = parseInt($(count_span).html());
			$(count_span).html(--count);

			// Informing,if last subcategory was deleted
			if(count == 0)
			{
				var html  = "";
				html     += "<td colspan='4' class='no-subcategories'>";
				html     += "	This category is empty. Please add some subcategories.";
				html     += "</td>";

				$(closest_table).html(html);
			}
		}
		
		// Deleting category row
		$(html_object).closest("tr").remove();
	}
}