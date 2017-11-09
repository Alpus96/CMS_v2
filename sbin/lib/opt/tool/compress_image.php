<?php
    
    //  Require the invalid path exception class.
    require_once dirname(__DIR__).'/exception/invalid_path.php';

    /**
    *   This class handels optimising image
    *   files to reduce required bandwidth.
    *
    *   @uses           InvalidPathException
    *   @uses           InvalidImageException
    *
    *   @category       Filehadling
    *   @package        dataHandlers
    *   @subpackage     imagefile
    *   @version        1.1.2
    *   @since          1.0.0
    *   @deprecated     ---
    * */
    class CompressImage {

        /**
         *  @method     Recreates an image to reduces the size.
         *
         *  @param      string        : The path to the image file to compress.
         *  @param      string        : The destimation path for the compressed image.
         *  @param      integer       : 1-100, quality compared to original image in percent.
         *  @param      boolean       : Whether or not to remove the source image when done.
         *
         *  @return     boolean       : Representation of whether the operation succeded.
         * 
         *  @throws     InvalidPathExeption
         *  @throws     InvalidImageException
         *  @throws     InvalidArgumentException
         *  @throws     OutOfRangeException
         */
        function __construct ($src, $target, $quality = 75, $rm_src = false) {
            //  Confirm passed parameters are valid.
            if (!file_exists($src))
            { throw new InvalidPathException(); }
            if ($src === $target)
            { throw new InvalidArgumentException('Source and target paths can not be same destination.'); }
            if ($quality < 1 || $quality > 100)
            { throw new OutOfRangeException('Quality out of range. (min: 1, max: 100)'); }

            //  Open source file info stream.
            $fio = finfo_open(FILEINFO_MIME_TYPE);
            //  Get mime type of source image.
            $info = finfo_file($fio, $src);
            //  Close source info stream.
            finfo_close($fio);

            //  Read the image file as mime type.
            $image = null;
            if ($info == 'image/jpeg') { $image = imagecreatefromjpeg($src); }
            else if ($info == 'image/gif') { $image = imagecreatefromgif($src); }
            else if ($info == 'image/png') { $image = imagecreatefrompng($src); }
            //  If the file was not of type defiend above throw new InvalidImageException.
            else { throw new InvalidImageException(); }
            
            //  Confirm the image file was read.
            if ($image) {
                //  Write an optimized image with set quality to target path.
                $success = imagejpeg($image, $target, $quality);
                //  Remove source image if specified and a new image was created.
                if ($success && $rm_src) { unlink($src); }
                //  Return success status of operation.
                return $success;
            }
            //  If the file was not read 
            //  return success status false.
            return false;
        }

    }

    /**
     *  NOTE:   Add support for animated gifs.
     */
    
?>