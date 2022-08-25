<?php

/**
 * Contains all validators,which are used
 * to check models attributes
 **/
class Validator
{
    // Checks,if attribute is empty
    public static function required($value)
    {
        if (mb_strlen($value, 'UTF-8') > 0) {
            return array(true, $value);
        } else {
            return array(false, $value);
        }
    }

    // Checks if two attrs have same value
    public static function equals($value, $attribute_to_match_value)
    {
        if ($value == $attribute_to_match_value) {
            return array(true, $value);
        } else {
            return array(false, $value);
        }
    }

    // Check attribute with regular expression
    public static function regex($value, $expression)
    {
        if (preg_match("~{$expression}~ui", $value)) {
            return array(true, $value);
        } else {
            return array(false, $value);
        }
    }

    // Here we should specify all symbols in all
    // languages,which are used in application
    private static function get_letters()
    {
        $english_letters = "A-Za-z";
        $russian_letters = "А-Яа-яЁё";

        return $english_letters . $russian_letters;
    }

    // Attribute value should contain only letters
    public static function only_letters($value)
    {
        $letters_to_match = self::get_letters();
        $expression = "^[{$letters_to_match}]+$";

        return self::regex($value, $expression);
    }

    // Match only with english letters(for username,etc.)
    public static function only_letters_digits_and_underscore($value)
    {
        $letters_to_match = "A-Za-z";
        $expression = "^[_0-9$letters_to_match]+$";

        return self::regex($value, $expression);
    }

    // Only digits
    public static function only_digits($value)
    {
        $expression = "^\d+$";

        return self::regex($value, $expression);
    }

    // Attribute value should be smaller then $length
    public static function min_length($value, $length)
    {
        if (mb_strlen($value, "UTF-8") >= $length) {
            return array(true, $value);
        } else {
            return array(false, $value);
        }
    }

    // Attribute value should be bigger then $length
    public static function max_length($value, $length)
    {
        if (mb_strlen($value, "UTF-8") <= $length) {
            return array(true, $value);
        } else {
            return array(false, $value);
        }
    }

    // Attribute value should be equal to $length
    public static function exact_length($value, $length)
    {
        if (mb_strlen($value, "UTF-8") == $length) {
            return array(true, $value);
        } else {
            return array(false, $value);
        }
    }

    // Attribute should consists of one value of $set values
    public static function belongs_to($value, $set)
    {
        $set_elements = explode("|", $set);

        if (in_array($value, $set_elements)) {
            return array(true, $value);
        } else {
            return array(false, $value);
        }
    }
}

?>