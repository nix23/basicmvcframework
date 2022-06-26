<?php
	// Here are located frontend routes.
	// (Backend routes are located in admin_routes.php file)
	$routes = array();
	
	// **********
	// *** drive
	// **********
	$module_types = array(
		"photos", "spots", "speed", "videos"
	);
	
	$module   = "(" . implode("|", $module_types) . ")";
	$page     = "page-(\d+)";
	$category = "[^/]+-(\d+)";
	
	$routes[] = array(
		"drive/list/$module/$page/$category" => "drive/index/$1/$2/$3",
		"drive/list/$module/$page"           => "drive/index/$1/$2",
		"drive/list"                         => "drive/index"
	);

	// **********
	// *** profile
	// **********
	$module_types = array(
		"photos", "spots", "speed", "videos"
	);

	$user     = "user-(\d+)";
	$module   = "(" . implode("|", $module_types) . ")";
	$page     = "page-(\d+)";
	$category = "[^/]+-(\d+)";

	$routes[] = array(
		"profile/view/$user/$module/$page/$category" => "profile/index/$1/$2/$3/$4",
		"profile/view/$user/$module/$page"           => "profile/index/$1/$2/$3",
		"profile/view/$user"                         => "profile/index/$1"
	);

	// ************
	// *** activity
	// ************
	$page          = "page-(\d+)";
	$days_to_fetch = "days-(1|7|30)";

	$routes[] = array(
		"activity/list/$page/$days_to_fetch" => "activity/index/$1/$2",
		"activity/list"                      => "activity/index"
	);
	
	// **********
	// *** follow
	// **********
	$page          = "page-(\d+)";
	$days_to_fetch = "days-(1|7|30)";
	
	$routes[] = array(
		"follow/list/$page/$days_to_fetch" => "follow/index/$1/$2",
		"follow/list"                      => "follow/index"
	);

	// **********
	// *** favorites
	// **********
	$module_types = array(
		"photos", "spots", "speed", "videos"
	);

	$module   = "(" . implode("|", $module_types) . ")";
	$page     = "page-(\d+)";

	$routes[] = array(
		"favorites/list/$module/$page" => "favorites/index/$1/$2",
		"favorites/list"               => "favorites/index"
	);
	
	// **********
	// *** photos
	// **********
	$sort_types = array(
		"postdate-asc", "postdate-desc",
		"year-asc",     "year-desc",
		"activity-asc", "activity-desc"
	);
	
	$category = "[^/]+-(\d+)";
	$page     = "page-(\d+)";
	$sort     = "sort-(" . implode("|", $sort_types) . ")";
	
	$photo_and_category = "[^/]+-(\d+)-(\d+)";
	
	$routes[] = array(
		"photos/list/{$category}/{$page}/{$sort}" => "photos/index/$2/$3/$1",
		"photos/list/{$page}/{$sort}"             => "photos/index/$1/$2",
		"photos/list"                             => "photos/index",
		"photos/{$photo_and_category}"            => "photos/view/$1/$2"
	); 
	
	// **********
	// *** spots
	// **********
	$sort_types = array(
		"postdate-asc",    "postdate-desc",
		"capturedate-asc", "capturedate-desc",
		"activity-asc",    "activity-desc"
	);
	
	$category = "[^/]+-(\d+)";
	$page     = "page-(\d+)";
	$sort     = "sort-(" . implode("|", $sort_types) . ")";
	
	$spot_and_category = "[^/]+-(\d+)-(\d+)";
	
	$routes[] = array(
		"spots/list/{$category}/{$page}/{$sort}" => "spots/index/$2/$3/$1",
		"spots/list/{$page}/{$sort}"             => "spots/index/$1/$2",
		"spots/list"                             => "spots/index",
		"spots/{$spot_and_category}"             => "spots/view/$1/$2"
	);
	
	// **********
	// *** speed
	// **********
	$sort_types = array(
		"postdate-asc", "postdate-desc",
		"views-asc",    "views-desc",
		"activity-asc", "activity-desc"
	);
	
	$category = "[^/]+-(\d+)";
	$page     = "page-(\d+)";
	$sort     = "sort-(" . implode("|", $sort_types) . ")";
	
	$speed_and_category = "[^/]+-(\d+)-(\d+)";
	
	$routes[] = array(
		"speed/list/{$category}/{$page}/{$sort}" => "speed/index/$2/$3/$1",
		"speed/list/{$page}/{$sort}"             => "speed/index/$1/$2",
		"speed/list"                             => "speed/index",
		"speed/{$speed_and_category}"            => "speed/view/$1/$2"
	);
	
	// **********
	// *** videos
	// **********
	$sort_types = array(
		"postdate-asc", "postdate-desc",
		"views-asc",    "views-desc",
		"activity-asc", "activity-desc"
	);
	
	$category = "[^/]+-(\d+)";
	$page     = "page-(\d+)";
	$sort     = "sort-(" . implode("|", $sort_types) . ")";
	
	$video_and_category = "[^/]+-(\d+)-(\d+)";
	
	$routes[] = array(
		"videos/list/{$category}/{$page}/{$sort}" => "videos/index/$2/$3/$1",
		"videos/list/{$page}/{$sort}"             => "videos/index/$1/$2",
		"videos/list"                             => "videos/index",
		"videos/{$video_and_category}"            => "videos/view/$1/$2"
	);

	// **********
	// *** footer
	// **********
	$routes[] = array(
		"about"          => "footer/about",
		"terms"          => "footer/terms",
		"privacy-policy" => "footer/privacypolicy",
		"support"        => "footer/support"
	);
	
	// Sitemap
	$routes[] = array(
		"sitemap/sitemap.xml" => "services/show_sitemap"
	);

	return $routes;
?>