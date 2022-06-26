<?php
	// Here are located backend routes.
	// (Frontend routes are located in routes.php file)
	$routes = array();
	
	// *********
	// *** login
	// *********
	$routes[] = array(
		"login" => "authorization"
	);

	// *********
	// *** dashboard
	// *********
	$events_to_show = array(
		"all", "uploads", "comments", "likes",
		"follows", "favorites", "users"
	);

	$page   = "page-(\d+)";
	$days   = "days-(1|7|30)";
	$events = "events-(" . implode("|", $events_to_show) . ")";

	$routes[] = array(
		"dashboard/list/$page/$days/$events" => "dashboard/index/$1/$2/$3",
		"dashboard/list"                     => "dashboard/index"
	);

	// *********
	// *** users
	// *********
	$sort_types = array(
		"username-asc", "username-desc",
		"registration-asc", "registration-desc",
		"rank-asc", "rank-desc"
	);

	$username_prefixes   = array();
	$username_prefixes[] = "all";
	$username_prefixes[] = "other";

	for($char = "a"; $char <= "z"; $char++)
	{
		$username_prefixes[] = $char;
		if($char == "z") break;
	}

	$page   = "page-(\d+)";
	$sort   = "sort-(" . implode("|", $sort_types) . ")";
	$prefix = "prefix-(" . implode("|", $username_prefixes) . ")";

	$routes[] = array(
		"users/list/$page/$sort/$prefix" => "users/index/$1/$2/$3",
		"users/list"                     => "users/index"
	);
	
	// **********
	// *** photos
	// **********
	$sort_types = array(
		"moderated-asc", "moderated-desc",
		"year-asc",      "year-desc",
		"activity-asc",  "activity-desc"
	);
	
	$category = "[^/]+-(\d+)";
	$page     = "page-(\d+)";
	$sort     = "sort-(" . implode("|", $sort_types) . ")";
	
	$routes[] = array(
		"photos/index/{$category}/{$page}/{$sort}" => "photos/index/$2/$3/$1",
		"photos/index/{$page}/{$sort}"             => "photos/index/$1/$2"
	); 
	
	// *********
	// *** spots
	// *********
	$sort_types = array(
		"moderated-asc", "moderated-desc",
		"date-asc",      "date-desc",
		"activity-asc",  "activity-desc"
	);
	
	$category = "[^/]+-(\d+)";
	$page     = "page-(\d+)";
	$sort     = "sort-(" . implode("|", $sort_types) . ")";
	
	$routes[] = array(
		"spots/index/{$category}/{$page}/{$sort}" => "spots/index/$2/$3/$1",
		"spots/index/{$page}/{$sort}"             => "spots/index/$1/$2"
	);
	
	// *********
	// *** speed
	// *********
	$sort_types = array(
		"moderated-asc", "moderated-desc",
		"views-asc",     "views-desc",
		"activity-asc",  "activity-desc"
	);
	
	$category = "[^/]+-(\d+)";
	$page     = "page-(\d+)";
	$sort     = "sort-(" . implode("|", $sort_types) . ")";
	
	$routes[] = array(
		"speed/index/{$category}/{$page}/{$sort}" => "speed/index/$2/$3/$1",
		"speed/index/{$page}/{$sort}"             => "speed/index/$1/$2"
	);
	
	// **********
	// *** videos
	// **********
	$sort_types = array(
		"moderated-asc", "moderated-desc",
		"views-asc",     "views-desc",
		"activity-asc",  "activity-desc"
	);
	
	$category = "[^/]+-(\d+)";
	$page     = "page-(\d+)";
	$sort     = "sort-(" . implode("|", $sort_types) . ")";
	
	$routes[] = array(
		"videos/index/{$category}/{$page}/{$sort}" => "videos/index/$2/$3/$1",
		"videos/index/{$page}/{$sort}"             => "videos/index/$1/$2"
	);
	
	return $routes;
?>