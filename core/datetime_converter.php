<?php
	class Datetime_Converter
	{
		private static function to_seconds($datetime)
		{
			list($date, $time)            = explode(" ", $datetime);
			list($year, $month, $day)     = explode("-", $date);
			list($hour, $minute, $second) = explode(":", $time);
			
			$seconds = 0;
			
			$seconds += $year * (12 * 30 * 24 * 60 * 60);
			$seconds += $month * (30 * 24 * 60 * 60);
			$seconds += $day * (24 * 60 * 60);
			$seconds += $hour * (60 * 60);
			$seconds += $minute * 60;
			$seconds += $second;
			
			return $seconds;
		}
		
		private static function get_seconds_elapsed($current_datetime,
																  $source_datetime)
		{
			$current_seconds = self::to_seconds($current_datetime);
			$source_seconds  = self::to_seconds($source_datetime);
			
			return $current_seconds - $source_seconds;
		}
		
		public static function get_time_ago($source_datetime)
		{
			$current_datetime = strftime("%Y-%m-%d %H:%M:%S", time());
			$seconds_elapsed  = self::get_seconds_elapsed($current_datetime,
																		 $source_datetime);
			
			// Less than minute
			if($seconds_elapsed < 60)
			{
				return "Just now";
			}
			// 1 - 59 Minutes
			else if($seconds_elapsed < (60 * 60))
			{
				$minutes_passed = round($seconds_elapsed / 60);
				
				if($minutes_passed == 1)
					return "1 minute ago";
				else
					return "$minutes_passed minutes ago";
			}
			// 1 - 23 Hours
			else if($seconds_elapsed < (24 * 60 * 60))
			{
				$hours_passed = round($seconds_elapsed / 3600);
				
				if($hours_passed == 1)
					return "1 hour ago";
				else
					return "$hours_passed hours ago";
			}
			// 1 - 30 Days
			else if($seconds_elapsed < (30 * 24 * 60 * 60))
			{
				$days_passed = round($seconds_elapsed / 86400);
				
				if($days_passed == 1)
					return "1 day ago";
				else
					return "$days_passed days ago";
			}
			// 1 - 11 Months
			else if($seconds_elapsed < (12 * 30 * 24 * 60 * 60))
			{
				$months_passed = round($seconds_elapsed / 2592000);
				
				if($months_passed == 1)
					return "1 month ago";
				else
					return "$months_passed months ago";
			}
			// 1 - ( + ) infinity years :DD
			else
			{
				$years_passed = round($seconds_elapsed / 31104000);
				
				if($years_passed == 1)
					return "1 year ago";
				else
					return "$years_passed years ago";
			}
		}

		public static function is_datetime_older_than($source_datetime = false,
																	 $days            = 1)
		{
			$current_datetime = strftime("%Y-%m-%d %H:%M:%S", time());
			$seconds_elapsed  = self::get_seconds_elapsed($current_datetime,
																		 $source_datetime);

			return ($seconds_elapsed > $days * (60 * 60 * 24)) ? true : false;
		}
		
		public static function is_datetime_older_than_n_hours($source_datetime = false,
																				$hours           = 1)
		{
			$current_datetime = strftime("%Y-%m-%d %H:%M:%S", time());
			$seconds_elapsed  = self::get_seconds_elapsed($current_datetime,
																		 $source_datetime);
			
			return ($seconds_elapsed > $hours * 60 * 60) ? true : false;
		}

		public static function get_datetime_in_seconds($datetime)
		{
			return self::to_seconds($datetime);
		}
	}
?>