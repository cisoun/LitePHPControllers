<?php
// Lite Response controller.
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

class Response {
    /**
     * JSON response
     *
     * @param mixed $code     Code, may be int, bool, ...
     * @param mixed $messages Message or datas
     * @param mixed $default  Message by default if not found
     */
    public static function JSON($code, $messages, $default = 'Undefined') {
        header('Content-Type: application/json');
        exit(json_encode(array('code' => $code, 'message' => $message)));
    }
}
?>
