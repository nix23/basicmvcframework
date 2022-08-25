<?php

class Profiler
{
    private static $start_time;
    private static $end_time;
    private static $block_name;
    private static $results_file;

    public static function init()
    {
        self::$results_file = fopen(ROOT . DS . "profiling.txt", "a");
    }

    public static function destroy()
    {
        fclose(self::$results_file);
    }

    private static function get_time()
    {
        $part_time = explode(' ', microtime());
        $real_time = $part_time[1] . substr($part_time[0], 1);

        return $real_time;
    }

    public static function start($block_name = false)
    {
        self::$block_name = $block_name;
        self::$start_time = self::get_time();
    }

    public static function stop($output_in_browser = false)
    {
        self::$end_time = self::get_time();
        $time_difference = bcsub(self::$end_time,
            self::$start_time,
            2);

        if ($output_in_browser) {
            echo $time_difference;
        } else {
            $timestampt = strftime("%Y-%m-%d %H:%M:%S", time());
            $line = "{$timestampt} ***** ";

            if (self::$block_name) {
                $line .= self::$block_name . " profiling results: {$time_difference}s.";
            } else {
                $line .= "Unknown_block_name profiling results: {$time_difference}s.";
            }

            fputs(self::$results_file, $line . PHP_EOL);
        }
    }
}

?>