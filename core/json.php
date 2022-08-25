<?php
    /** 
    * Json methods.(For PHP < 5.2)
    **/
    class Json
    {
        // Converts from Json(Php 4.1+)
        public static function from_json($json, $assoc = false)
        {
            $matchString = '/".*?(?<!\\\\)"/';

            $t = preg_replace( $matchString, '', $json );
            $t = preg_replace( '/[,:{}\[\]0-9.\-+Eaeflnr-u \n\r\t]/', '', $t );
            if ($t != '') { return null; }

            $s2m = array();
            $m2s = array();
            preg_match_all( $matchString, $json, $m );
            foreach ($m[0] as $s)
            {
                $hash = '"' . md5( $s ) . '"';
                $s2m[$s] = $hash;
                $m2s[$hash] = str_replace( '$', '\$', $s );
            }

            $json = strtr( $json, $s2m );

            $a = ($assoc) ? '' : '(object) ';
            $json = strtr( $json,
            array(
                ':' => '=>',
                '[' => 'array(',
                '{' => "{$a}array(",
                ']' => ')',
                '}' => ')'
            )
            );

            $json = preg_replace( '~([\s\(,>])(-?)0~', '$1$2', $json );
            $json = strtr( $json, $m2s );

            $f = @create_function( '', "return {$json};" );
            $r = ($f) ? $f() : null;
            unset( $s2m ); unset( $m2s ); unset( $f );

            return $r;
        }

        // Converts data to Json(PHP 4.1+)
        public static function to_json($value)
        {
            if ($value === null) { return 'null'; };
            $out = '';
            $esc = "\"\\/\n\r\t" . chr( 8 ) . chr( 12 );
            $l = '.';

            switch ( gettype( $value ) )
            {
                case 'boolean':
                    $out .= $value ? 'true' : 'false';
                    break;

                case 'float':
                case 'double':

                $l = localeconv();
                $l = $l['decimal_point'];

                case 'integer':
                    $out .= str_replace( $l, '.', $value );
                break;

                case 'array':
                    for ($i = 0; ($i < count( $value ) && isset( $value[$i]) ); $i++);
                        if ($i === count($value))
                        {
                            $out .= '[' . implode(',', array_map(array("Json", "to_json"), $value)) . ']';
                            break;
                        }

                case 'object':
                    $arr = is_object($value) ? get_object_vars($value) : $value;
                    $b = array();
                    foreach ($arr as $k => $v)
                    {
                        $b[] = '"' . addcslashes($k, $esc) . '":' . Json::to_json($v);
                    }
                    $out .= '{' . implode( ',', $b ) . '}';
                break;

                default:
                    return '"' . addcslashes($value, $esc) . '"';
                break;
            }

            return $out;
        }
    }
?>