<?php

/*

  system_utils.php - system ENUM definitions
  ----------------
  
  This file is part of zukunft.com - calc with words

  zukunft.com is free software: you can redistribute it and/or modify it
  under the terms of the GNU General Public License as
  published by the Free Software Foundation, either version 3 of
  the License, or (at your option) any later version.
  zukunft.com is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
  GNU General Public License for more details.
  
  You should have received a copy of the GNU General Public License
  along with zukunft.com. If not, see <http://www.gnu.org/licenses/gpl.html>.
  
  To contact the authors write to:
  Timon Zielonka <timon@zukunft.com>
  
  Copyright (c) 1995-2021 zukunft.com AG, Zurich
  Heang Lor <heang@zukunft.com>
  
  http://zukunft.com
  
*/

class sys_log_level extends BasicEnum
{
    const UNDEFINED = 0;
    const INFO = 1;
    const WARNING = 2;
    const ERROR = 3;
    const FATAL = 4;

    protected static function get_description($value): string {
        $result = parent::get_description($value);

        switch ($value) {

            // system log
            case sys_log_level::WARNING:
                $result = 'Warning';
                break;
            case sys_log_level::ERROR:
                $result = 'Error';
                break;
            case sys_log_level::FATAL:
                $result = 'FATAL ERROR';
                break;
        }

        return $result;
    }
}

abstract class BasicEnum {
    private static ?array $const_cache_array = NULL;

    /**
     * @throws ReflectionException
     */
    private static function get_constants() {
        if (self::$const_cache_array == NULL) {
            self::$const_cache_array = [];
        }
        $calledClass = get_called_class();
        if (!array_key_exists($calledClass, self::$const_cache_array)) {
            $reflect = new ReflectionClass($calledClass);
            self::$const_cache_array[$calledClass] = $reflect->get_constants();
        }
        return self::$const_cache_array[$calledClass];
    }

    /**
     * @throws ReflectionException
     */
    protected static function getDescription($value): string {
        return strtolower(self::get_constants());
    }

    /**
     * @throws ReflectionException
     */
    public static function is_valid_name($name, $strict = false): bool {
        $constants = self::get_constants();

        if ($strict) {
            return array_key_exists($name, $constants);
        }

        $keys = array_map('strtolower', array_keys($constants));
        return in_array(strtolower($name), $keys);
    }

    /**
     * @throws ReflectionException
     */
    public static function is_valid_value($value, $strict = true): bool {
        $values = array_values(self::get_constants());
        return in_array($value, $values, $strict);
    }
}