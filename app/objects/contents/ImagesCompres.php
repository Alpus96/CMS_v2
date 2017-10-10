<?php

    class ImagesCompres {

        function __construct () ($source_url, $destination_url, $quality = 85) {

            $info = getimagesize($source_url);

            if ($info['mime'] == 'image/jpeg') {
                $image = imagecreatefromjpeg($source_url);
            } else if ($info['mime'] == 'image/gif') {
                $image = imagecreatefromgif($source_url);
            } else if ($info['mime'] == 'image/png') {
                $image = imagecreatefrompng($source_url);
            }

            imagejpeg($image, $destination_url, $quality);
            return $destination_url;
        }

    }


?>