<?php
// Lite Image controller.
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

class Images {

    //
    // Private
    //

    /**
     * Open a file as a ressource file in order to work on it with GD.
     *
     * Usage :
     * 	list($image, $width, $height, $type) = self::_open($file);
     *
     * Return values :
     * 	false :     Failed to recognize the file.
     * 	list() :    Images and properties
     *
     * @param  string $file     Path to local file.
     * @return mixed            False or list.
     */
    private static function _open($file) {
        // Get images properties.
        list($width, $height, $type) = getimagesize($file);

        switch ($type) {
            case IMAGETYPE_GIF:
                $image = imagecreatefromgif($file);
                break;
            case IMAGETYPE_JPEG:
                $image = imagecreatefromjpeg($file);
                break;
            case IMAGETYPE_PNG:
                $image = imagecreatefrompng($file);
                break;
            default:
                return false; // Fail
        }

        // Success. Return the image and the properties.
        return array($image, $width, $height, $type);
    }

    //
    // Public
    //

    /**
     * Resize a picture and save it.
     *
     * @param  string $source       Path to local file.
     * @param  string $destination  Destination to the new file.
     * @param  int $newWidth        Desired new width.
     * @param  int $newHeight       Desired new height.
     * @return boolean              Status.
     */
    public static function resize($source, $destination, $newWidth, $newHeight, $format = IMAGETYPE_PNG) {
        // Open it.
        if (!(list($image, $width, $height, $type) = self::_open($source)))
            return false;

        // Load, resize and save.
        $newImage = imagecreatetruecolor($newWidth, $newHeight);
        imagecopyresampled($newImage, $image, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
        switch ($type) {
            case IMAGETYPE_GIF:
                $saved = imagegif($newImage, $destination);
                break;
            case IMAGETYPE_JPEG:
                $saved = imagejpeg($newImage, $destination);
                break;
            case IMAGETYPE_PNG:
                $saved = imagepng($newImage, $destination);
                break;
            default:
                return false; // Fail
        }

        // Free memory
        imagedestroy($newImage);

        return $saved;
    }
}
?>
