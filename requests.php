<?php
// Lite Requests controller.
// Copyright (C) 2015  Cyriaque Skrapits
//
// This program is free software; you can redistribute it and/or
// modify it under the terms of the GNU General Public License
// as published by the Free Software Foundation; either version 2
// of the License, or (at your option) any later version.
//
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with this program; if not, write to the Free Software
// Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.

class Requests {
    /**
     * Did we got a GET request ?
     *
     * @return boolean Yes we did, or not.
     */
    public static function get() {
        return $_SERVER['REQUEST_METHOD'] == 'GET';
    }

    /**
     * Check if the request has the specified keys.
     * Setting $empty to true also check if the key has at least a character.
     *
     * TODO: Improve this to speed up.
     *
     * @param  mixed  $keys     Key(s)
     * @param  boolean $empty   Check if empty.
     * @return boolean          Request has keys.
     */
    public static function has($keys, $empty = false) {
        // If case of array.
        if (is_array($keys)) {
            foreach ($keys as $key) {
                if (!isset($_REQUEST[$key]) || ($empty && trim($_REQUEST[$key]) == false))
                    return false;
            }
            return true;
        }

        // Otherwise...
        return isset($_REQUEST[$keys]);
    }

    /**
     * Get a key from the request.
     *
     * @param  string $key  Key name
     * @return mixed        Value
     */
    public static function key($key) {
        $value = 'undefined';

        if (self::has($key))
            $value = $_REQUEST[$key];

        return $value;
    }

    /**
     * Did we got a POST request ?
     *
     * @return boolean Yes we did, or not.
     */
    public static function post() {
        return $_SERVER['REQUEST_METHOD'] == 'POST';
    }
}
?>
