<?php
/**
 * Image manipulation support. Allows images to be resized, cropped, etc.
 *
 * @package    Kohana/Image
 * @category   Base
 * @author     Kohana Team
 * @copyright  (c) 2008-2009 Kohana Team
 * @license    http://kohanaphp.com/license.html
 */
abstract class ImageK {

    // Resizing constraints
    const NONE    = 0x01;
    const WIDTH   = 0x02;
    const HEIGHT  = 0x03;
    const AUTO    = 0x04;
    const INVERSE = 0x05;

    // Flipping directions
    const HORIZONTAL = 0x11;
    const VERTICAL   = 0x12;

    /**
     * @var  string  default driver: GD, ImageMagick, etc
     */
    public static $default_driver = 'GD';

    // Status of the driver check
    protected static $_checked = FALSE;

    /**
     * Loads an image and prepares it for manipulation.
     *
     *     $image = Image::factory('upload/test.jpg');
     *
     * @param   string   ImageK file path
     * @param   string   driver type: GD, ImageMagick, etc
     * @return  ImageK
     * @uses    Image::$default_driver
     */
    public static function factory($file, $driver = NULL)
    {
        if ($driver === NULL)
        {
            // Use the default driver
            $driver = ImageK::$default_driver;
        }

        // Set the class name
        $class = 'Image_'.$driver;

        return new $class($file);
    }

    /**
     * @var  string  image file path
     */
    public $file;

    /**
     * @var  integer  image width
     */
    public $width;

    /**
     * @var  integer  image height
     */
    public $height;

    /**
     * @var  integer  one of the IMAGETYPE_* constants
     */
    public $type;

    /**
     * @var  string  mime type of the image
     */
    public $mime;

    /**
     * Loads information about the image. Will throw an exception if the image
     * does not exist or is not an image.
     *
     * @param   string   ImageK file path
     * @return  void
     * @throws  Kohana_Exception
     */
    public function __construct($file)
    {
        try
        {
            // Get the real path to the file
            $file = realpath($file);

            // Get the image information
            $info = getimagesize($file);
        }
        catch (Exception $e)
        {
            // Ignore all errors while reading the image
        }

        if (empty($file) OR empty($info))
        {
            echo "";
        }

        // Store the image information
        $this->file   = $file;
        $this->width  = $info[0];
        $this->height = $info[1];
        $this->type   = $info[2];
        $this->mime   = image_type_to_mime_type($this->type);
    }

    /**
     * Render the current image.
     *
     *     echo $image;
     *
     * [!!] The output of this function is binary and must be rendered with the
     * appropriate Content-Type header or it will not be displayed correctly!
     *
     * @return  string
     */
    public function __toString()
    {
        try
        {
            // Render the current image
            return $this->render();
        }
        catch (Exception $e)
        {


            // Showing any kind of error will be "inside" image data
            return '';
        }
    }

    /**
     * Resize the image to the given size. Either the width or the height can
     * be omitted and the image will be resized proportionally.
     * @param string $img - relative path of the picture /images/mod_catalog_prod/24094/12984541610.jpg
     * @param string $size - can be:
     *        'size_width=XXX' - proportionally resize image to given width.
     *        'size_height=XXX' - proportionally resize image to given height.
     *        'size_auto=XXX' - proportionally resize image to given size of square
     *                          (autoresize to given suare).
     *        'size_square=XXX' - Image Resizing by scaling to given given size
     *                            of square and, if necessary, cut the image
     *                            from center if it proportionally
     *                            not fit to given size of square.
     *        'size_rect=XXXxYYY' Image Resizing by scaling to given width XXX
     *                            and height YYY and, if necessary, cut the image from center if it
     *                            proportionally not fit to given width XXX and height YYY.
     *                            where XXX and YYY number in pixels
     * @param integer $quality quality of image to store
     * @param boolen $wtm - set of values. Can be: 'img' - using image as waterwark
     * @return  reletive path to resized img
     */
    static function getResizedImg($img = NULL, $size = NULL, $quality = NULL, $wtm = NULL)
    {
        $size_auto = NULL;
        $size_width = NULL;
        $size_height = NULL;
        $size_square = NULL;
        $size_rect = NULL;
        $widthNew = NULL;
        $heightNew = NULL;

        //echo '<br>img='.$img;
        $rpos = strrpos($img, '/');
        if ($rpos > 0) {
            $settings_img_path = substr($img, 0, $rpos);
            $img_name = substr($img, $rpos + 1, strlen($img) - $rpos);
            $img_with_path = $img;
        }else {
            return false;
        }
        //echo '<br>$img_with_path='.$img_with_path;
        //echo '<br>$img_name='.$img_name;

        if (!file_exists(SITE_PATH .$img_with_path)){
            $img = DEFAULT_NO_IMAGE_PATH;
            $img_with_path = $img;
            $img_name = substr($img, strrpos($img, '/') + 1, strlen($img) - strrpos($img, '/'));
        }

        $mas_img_name = explode(".", $img_with_path);
        $count_mas_img_name = count($mas_img_name);
        $exp = $mas_img_name[$count_mas_img_name-1];
        unset($mas_img_name[$count_mas_img_name-1]);
        $img_name_from_array = implode(".",$mas_img_name);
        if(strstr($size, 'size_width')) {
            $size_width = substr($size, strrpos($size, '=') + 1, strlen($size));
            $widthNew = $size_width;
            $heightNew = $size_width;
            $resizeType = ImageK::WIDTH;
            $img_name_new = $img_name_from_array . ADDITIONAL_FILES_TEXT . 'width_' . $size_width . '.' . $exp;
        }elseif(strstr($size, 'size_auto')) {
            $size_auto = substr($size, strrpos($size, '=') + 1, strlen($size));
            $widthNew = $size_auto;
            $heightNew = $size_auto;
            $resizeType = ImageK::AUTO;
            $img_name_new = $img_name_from_array . ADDITIONAL_FILES_TEXT . 'auto_' . $size_auto . '.' . $exp;
        }elseif(strstr($size, 'size_height')) {
            $size_height = substr($size, strrpos($size, '=') + 1, strlen($size));
            $widthNew = $size_height;
            $heightNew = $size_height;
            $resizeType = ImageK::HEIGHT;
            $img_name_new = $img_name_from_array . ADDITIONAL_FILES_TEXT . 'height_' . $size_height . '.' . $exp;
        }elseif(strstr($size, 'size_square')) {
            $size_square = substr($size, strrpos($size, '=') + 1, strlen($size));
            $widthNew = $size_square;
            $heightNew = $size_square;
            $resizeType = ImageK::AUTO;
            $img_name_new = $img_name_from_array . ADDITIONAL_FILES_TEXT . 'square_' . $size_square . '.' . $exp;
        }elseif(strstr($size, 'size_rect')) {
            $sss = explode('=', $size);
            if(isset($sss[1]) AND !empty($sss[1])){
                $sss2 = explode('x', $sss[1]);
                if(isset($sss2[0]) AND !empty($sss2[0])){
                    $widthNew = $sss2[0];
                }
                if(isset($sss2[1]) AND !empty($sss2[1])){
                    $heightNew = $sss2[1];
                }
            }
            $size_rect = $widthNew.'x'.$heightNew;
            $resizeType = ImageK::AUTO;
            $img_name_new = $img_name_from_array . ADDITIONAL_FILES_TEXT . 'rect_' . $size_rect . '.' . $exp;
        }elseif(empty($size)){
            $img_name_new = $img_name_from_array . '.' . $exp;
        }
//        echo '<br>$img_name_new='.$img_name_new;
        $img_full_path = SITE_PATH . $img_name_new;
        //echo '<br/>$img_full_path ='.$img_full_path;

        //if exist local small version of the image then use it
        if(file_exists($img_full_path)){
            //echo 'exist';
            $str = $img_name_new;
            $flag_to_save = false;
        }
        //else use original image on the server SITE_PATH and make small version on local server
        else{
            //echo 'Not  exist';
            $img_full_path = SITE_PATH . $img_with_path; // like z:/home/speakers/www/uploads/45/R1800TII_big.jpg
            //echo '<br> $img_full_path='.$img_full_path.'<br> $size_auto='.$size_auto;

            $ImageK = ImageK::factory($img_full_path);
            $src_x = $ImageK->width;
            $src_y = $ImageK->height;

            /*
            $ImageK_Test = ImageK::factory($img_full_path);
            if($size_square OR $size_rect){
                $ImageK_Test->resize_and_crop($widthNew, $heightNew);
            }else{
                $ImageK_Test->resize($widthNew, $heightNew, $resizeType);
            }
             *
             */

            //echo '<br>$widthNew='.$widthNew.' $heightNew='.$heightNew.'$resizeType='.$resizeType.' $src_x='.$src_x.' $src_y='.$src_y.' $ImageK_Test->width='.$ImageK_Test->width.' $ImageK_Test->height='.$ImageK_Test->height;

            //if original image smaller than thumbnail then use original image and don't create thumbnail
            //if ($src_x <= $ImageK_Test->width OR $src_y <= $ImageK_Test->height) {
            if ((strstr($size, 'size_width') AND $src_x <= $widthNew)
                or (strstr($size, 'size_height') AND $src_y <= $heightNew)
                or ($src_x <= $widthNew AND $src_y <= $heightNew AND !strstr($size, 'size_width') AND !strstr($size, 'size_height'))) {
                $img_full_path = $settings_img_path . '/' . $img_name;
//                echo '<br>$settings_img_path='.$settings_img_path.' $img_full_path='.$img_full_path;
                $str = $img_full_path;
                $flag_to_save = false;
            }else {
                $mas_img_name = explode(".", $img_with_path);
                //$img_name_new = $mas_img_name[0].ADDITIONAL_FILES_TEXT.intval($thumb->img['x_thumb']).'x'.intval($thumb->img['y_thumb']).'.'.$mas_img_name[1];
                if (!empty($size_width)){
                    $img_name_new = $mas_img_name[0] . ADDITIONAL_FILES_TEXT . 'width_' . $size_width . '.' . $mas_img_name[1];
                }elseif (!empty($size_auto)){
                    $img_name_new = $mas_img_name[0] . ADDITIONAL_FILES_TEXT . 'auto_' . $size_auto . '.' . $mas_img_name[1];
                }elseif (!empty($size_height)){
                    $img_name_new = $mas_img_name[0] . ADDITIONAL_FILES_TEXT . 'height_' . $size_height . '.' . $mas_img_name[1];
                }elseif (!empty($size_square)){
                    $img_name_new = $mas_img_name[0] . ADDITIONAL_FILES_TEXT . 'square_' . $size_square . '.' . $mas_img_name[1];
                }

                if($size_square OR $size_rect){
                    $ImageK->resize_and_crop($widthNew, $heightNew);
                }else{
                    $ImageK->resize($widthNew, $heightNew, $resizeType);
                }
                $str = $img_name_new;
                $flag_to_save = true;
            }//end else
        }//end else

        //add watermark on the image
        if($wtm == 'img') {
            //if not exist
            if(!isset($src_x) OR !isset($src_y)){
                $ImageK = ImageK::factory($img_full_path);
                $src_x = $ImageK->width;
                $src_y = $ImageK->height;
            }
            //prepare new image name with adding sufix "_WTM_"
            $img_name = substr($str, 0, strrpos($str, '.'));
            if(!strstr($img_name, ADDITIONAL_FILES_TEXT)){
                $img_name .= ADDITIONAL_FILES_TEXT;
            }
            $img_ext = substr($str, strrpos($str, '.'));
            //echo '<br>$img_name='.$img_name.' $img_ext='.$img_ext;
            $str = $img_name . '_WTM_' . $img_ext;

            //if don't exist file with watermark then resize watermark for
            //best using on current image.
            if (!file_exists(SITE_PATH.$str)){
                $size_wtm = min($src_x, $ImageK->width, $src_y, $ImageK->height);
                //echo '<br>$size_wtm='.$size_wtm;
                $wtm = SITE_PATH . ImageK::getResizedImg(WATERMARK_IMG_PATH, 'size_auto='.$size_wtm, 85);
                $mark = ImageK::factory($wtm);
                //$ImageK->watermark($mark);
                //batman
                $flag_to_save = true;
            }else{
                $flag_to_save = false;
            }
        }

        if($flag_to_save){
            $img_full_path_new = SITE_PATH . $str;
            //echo '<br>$img_full_path_new='.$img_full_path_new;
            if (!file_exists($img_full_path_new)){
                $rpos = strrpos($img_with_path, '/');
                //echo '<br />$img_with_path='.$img_with_path.' $rpos='.$rpos;
                if ($rpos > 0){
                    $uploaddir = SITE_PATH . substr($img_with_path, 0, $rpos);
                }else{
                    return false;
                }
                if (file_exists($uploaddir)){
                    @chmod($uploaddir, 0777);
                }else{
                    mkdir($uploaddir, 0777);
                }
                $ImageK->save($img_full_path_new, $quality);
                @chmod($uploaddir, 0755);
            }
        }
        return $str;
    }

    /**
     * Resize the image to the given size. Either the width or the height can
     * be omitted and the image will be resized proportionally.
     *
     *     // Resize to 200 pixels on the shortest side
     *     $image->resize(200, 200);
     *
     *     // Resize to 200x200 pixels, keeping aspect ratio
     *     $image->resize(200, 200, Image::INVERSE);
     *
     *     // Resize to 500 pixel width, keeping aspect ratio
     *     $image->resize(500, NULL);
     *
     *     // Resize to 500 pixel height, keeping aspect ratio
     *     $image->resize(NULL, 500);
     *
     *     // Resize to 200x500 pixels, ignoring aspect ratio
     *     $image->resize(200, 500, Image::NONE);
     *
     * @param   integer  new width
     * @param   integer  new height
     * @param   integer  master dimension
     * @return  $this
     * @uses    Image::_do_resize
     */
    public function resize($width = NULL, $height = NULL, $master = NULL)
    {
        if ($master === NULL)
        {
            // Choose the master dimension automatically
            $master = ImageK::AUTO;
        }
        // Image::WIDTH and Image::HEIGHT deprecated. You can use it in old projects,
        // but in new you must pass empty value for non-master dimension
        elseif ($master == ImageK::WIDTH AND ! empty($width))
        {
            $master = ImageK::AUTO;

            // Set empty height for backward compatibility
            $height = NULL;
        }
        elseif ($master == ImageK::HEIGHT AND ! empty($height))
        {
            $master = ImageK::AUTO;

            // Set empty width for backward compatibility
            $width = NULL;
        }

        if (empty($width))
        {
            if ($master === ImageK::NONE)
            {
                // Use the current width
                $width = $this->width;
            }
            else
            {
                // If width not set, master will be height
                $master = ImageK::HEIGHT;
            }
        }

        if (empty($height))
        {
            if ($master === ImageK::NONE)
            {
                // Use the current height
                $height = $this->height;
            }
            else
            {
                // If height not set, master will be width
                $master = ImageK::WIDTH;
            }
        }

        switch ($master)
        {
            case ImageK::AUTO:
                // Choose direction with the greatest reduction ratio
                $master = ($this->width / $width) > ($this->height / $height) ? ImageK::WIDTH : ImageK::HEIGHT;
                break;
            case ImageK::INVERSE:
                // Choose direction with the minimum reduction ratio
                $master = ($this->width / $width) > ($this->height / $height) ? ImageK::HEIGHT : ImageK::WIDTH;
                break;
        }

        switch ($master)
        {
            case ImageK::WIDTH:
                // Recalculate the height based on the width proportions
                $height = $this->height * $width / $this->width;
                break;
            case ImageK::HEIGHT:
                // Recalculate the width based on the height proportions
                $width = $this->width * $height / $this->height;
                break;
        }

        // Convert the width and height to integers, minimum value is 1px
        $width  = max(round($width), 1);
        $height = max(round($height), 1);

        $this->_do_resize($width, $height);

        return $this;
    }


    public function resize_and_crop($size_width,$size_height){
// Открываем изображение
// Подсчитываем соотношение сторон картинки
        $ratio = $this->width / $this->height;
// Соотношение сторон нужных размеров
        $original_ratio = $size_width / $size_height;
// Размеры, до которых обрежем картинку до масштабирования
        $crop_width = $this->width;
        $crop_height = $this->height;
// Смотрим соотношения
        if($ratio > $original_ratio)
        {
            // Если ширина картинки слишком большая для пропорции,
            // то будем обрезать по ширине
            $crop_width = round($original_ratio * $crop_height);
        }
        else
        {
            // Либо наоборот, если высота картинки слишком большая для пропорции,
            // то обрезать будем по высоте
            $crop_height = round($crop_width / $original_ratio);
        }
// Обрезаем по высчитанным размерам до нужной пропорции
        $this->crop($crop_width, $crop_height);
// Масштабируем картинку то точных размеров
        $this->resize($size_width, $size_height, ImageK::NONE);

        return $this;
    }

    /**
     * Crop an image to the given size. Either the width or the height can be
     * omitted and the current width or height will be used.
     *
     * If no offset is specified, the center of the axis will be used.
     * If an offset of TRUE is specified, the bottom of the axis will be used.
     *
     *     // Crop the image to 200x200 pixels, from the center
     *     $image->crop(200, 200);
     *
     * @param   integer  new width
     * @param   integer  new height
     * @param   mixed    offset from the left
     * @param   mixed    offset from the top
     * @return  $this
     * @uses    Image::_do_crop
     */
    public function crop($width, $height, $offset_x = NULL, $offset_y = NULL)
    {
        if ($width > $this->width)
        {
            // Use the current width
            $width = $this->width;
        }

        if ($height > $this->height)
        {
            // Use the current height
            $height = $this->height;
        }

        if ($offset_x === NULL)
        {
            // Center the X offset
            $offset_x = round(($this->width - $width) / 2);
        }
        elseif ($offset_x === TRUE)
        {
            // Bottom the X offset
            $offset_x = $this->width - $width;
        }
        elseif ($offset_x < 0)
        {
            // Set the X offset from the right
            $offset_x = $this->width - $width + $offset_x;
        }

        if ($offset_y === NULL)
        {
            // Center the Y offset
            $offset_y = round(($this->height - $height) / 2);
        }
        elseif ($offset_y === TRUE)
        {
            // Bottom the Y offset
            $offset_y = $this->height - $height;
        }
        elseif ($offset_y < 0)
        {
            // Set the Y offset from the bottom
            $offset_y = $this->height - $height + $offset_y;
        }

        // Determine the maximum possible width and height
        $max_width  = $this->width  - $offset_x;
        $max_height = $this->height - $offset_y;

        if ($width > $max_width)
        {
            // Use the maximum available width
            $width = $max_width;
        }

        if ($height > $max_height)
        {
            // Use the maximum available height
            $height = $max_height;
        }

        $this->_do_crop($width, $height, $offset_x, $offset_y);

        return $this;
    }

    /**
     * Rotate the image by a given amount.
     *
     *     // Rotate 45 degrees clockwise
     *     $image->rotate(45);
     *
     *     // Rotate 90% counter-clockwise
     *     $image->rotate(-90);
     *
     * @param   integer   degrees to rotate: -360-360
     * @return  $this
     * @uses    Image::_do_rotate
     */
    public function rotate($degrees)
    {
        // Make the degrees an integer
        $degrees = (int) $degrees;

        if ($degrees > 180)
        {
            do
            {
                // Keep subtracting full circles until the degrees have normalized
                $degrees -= 360;
            }
            while($degrees > 180);
        }

        if ($degrees < -180)
        {
            do
            {
                // Keep adding full circles until the degrees have normalized
                $degrees += 360;
            }
            while($degrees < -180);
        }

        $this->_do_rotate($degrees);

        return $this;
    }

    /**
     * Flip the image along the horizontal or vertical axis.
     *
     *     // Flip the image from top to bottom
     *     $image->flip(Image::HORIZONTAL);
     *
     *     // Flip the image from left to right
     *     $image->flip(Image::VERTICAL);
     *
     * @param   integer  direction: Image::HORIZONTAL, Image::VERTICAL
     * @return  $this
     * @uses    Image::_do_flip
     */
    public function flip($direction)
    {
        if ($direction !== ImageK::HORIZONTAL)
        {
            // Flip vertically
            $direction = ImageK::VERTICAL;
        }

        $this->_do_flip($direction);

        return $this;
    }

    /**
     * Sharpen the image by a given amount.
     *
     *     // Sharpen the image by 20%
     *     $image->sharpen(20);
     *
     * @param   integer  amount to sharpen: 1-100
     * @return  $this
     * @uses    Image::_do_sharpen
     */
    public function sharpen($amount)
    {
        // The amount must be in the range of 1 to 100
        $amount = min(max($amount, 1), 100);

        $this->_do_sharpen($amount);

        return $this;
    }

    /**
     * Add a reflection to an image. The most opaque part of the reflection
     * will be equal to the opacity setting and fade out to full transparent.
     * Alpha transparency is preserved.
     *
     *     // Create a 50 pixel reflection that fades from 0-100% opacity
     *     $image->reflection(50);
     *
     *     // Create a 50 pixel reflection that fades from 100-0% opacity
     *     $image->reflection(50, 100, TRUE);
     *
     *     // Create a 50 pixel reflection that fades from 0-60% opacity
     *     $image->reflection(50, 60, TRUE);
     *
     * [!!] By default, the reflection will be go from transparent at the top
     * to opaque at the bottom.
     *
     * @param   integer   reflection height
     * @param   integer   reflection opacity: 0-100
     * @param   boolean   TRUE to fade in, FALSE to fade out
     * @return  $this
     * @uses    Image::_do_reflection
     */
    public function reflection($height = NULL, $opacity = 100, $fade_in = FALSE)
    {
        if ($height === NULL OR $height > $this->height)
        {
            // Use the current height
            $height = $this->height;
        }

        // The opacity must be in the range of 0 to 100
        $opacity = min(max($opacity, 0), 100);

        $this->_do_reflection($height, $opacity, $fade_in);

        return $this;
    }

    /**
     * Add a watermark to an image with a specified opacity. Alpha transparency
     * will be preserved.
     *
     * If no offset is specified, the center of the axis will be used.
     * If an offset of TRUE is specified, the bottom of the axis will be used.
     *
     *     // Add a watermark to the bottom right of the image
     *     $mark = Image::factory('upload/watermark.png');
     *     $image->watermark($mark, TRUE, TRUE);
     *
     * @param   object   watermark Image instance
     * @param   integer  offset from the left
     * @param   integer  offset from the top
     * @param   integer  opacity of watermark: 1-100
     * @return  $this
     * @uses    Image::_do_watermark
     */
    public function watermark(ImageK $watermark, $offset_x = NULL, $offset_y = NULL, $opacity = 100)
    {
        if ($offset_x === NULL)
        {
            // Center the X offset
            $offset_x = round(($this->width - $watermark->width) / 2);
        }
        elseif ($offset_x === TRUE)
        {
            // Bottom the X offset
            $offset_x = $this->width - $watermark->width;
        }
        elseif ($offset_x < 0)
        {
            // Set the X offset from the right
            $offset_x = $this->width - $watermark->width + $offset_x;
        }

        if ($offset_y === NULL)
        {
            // Center the Y offset
            $offset_y = round(($this->height - $watermark->height) / 2);
        }
        elseif ($offset_y === TRUE)
        {
            // Bottom the Y offset
            $offset_y = $this->height - $watermark->height;
        }
        elseif ($offset_y < 0)
        {
            // Set the Y offset from the bottom
            $offset_y = $this->height - $watermark->height + $offset_y;
        }

        // The opacity must be in the range of 1 to 100
        $opacity = min(max($opacity, 1), 100);

        $this->_do_watermark($watermark, $offset_x, $offset_y, $opacity);

        return $this;
    }

    /**
     * Set the background color of an image. This is only useful for images
     * with alpha transparency.
     *
     *     // Make the image background black
     *     $image->background('#000');
     *
     *     // Make the image background black with 50% opacity
     *     $image->background('#000', 50);
     *
     * @param   string   hexadecimal color value
     * @param   integer  background opacity: 0-100
     * @return  $this
     * @uses    Image::_do_background
     */
    public function background($color, $opacity = 100)
    {
        if ($color[0] === '#')
        {
            // Remove the pound
            $color = substr($color, 1);
        }

        if (strlen($color) === 3)
        {
            // Convert shorthand into longhand hex notation
            $color = preg_replace('/./', '$0$0', $color);
        }

        // Convert the hex into RGB values
        list ($r, $g, $b) = array_map('hexdec', str_split($color, 2));

        // The opacity must be in the range of 0 to 100
        $opacity = min(max($opacity, 0), 100);

        $this->_do_background($r, $g, $b, $opacity);

        return $this;
    }

    /**
     * Save the image. If the filename is omitted, the original image will
     * be overwritten.
     *
     *     // Save the image as a PNG
     *     $image->save('saved/cool.png');
     *
     *     // Overwrite the original image
     *     $image->save();
     *
     * [!!] If the file exists, but is not writable, an exception will be thrown.
     *
     * [!!] If the file does not exist, and the directory is not writable, an
     * exception will be thrown.
     *
     * @param   string   new image path
     * @param   integer  quality of image: 1-100
     * @return  boolean
     * @uses    Image::_save
     * @throws  Kohana_Exception
     */
    public function save($file = NULL, $quality = 100)
    {
        if ($file === NULL)
        {
            // Overwrite the file
            $file = $this->file;
        }

        if (is_file($file))
        {
            if ( ! is_writable($file))
            {
                throw new Exception('File must be writable: '.$file);
            }
        }
        else
        {
            // Get the directory of the file
            $directory = realpath(pathinfo($file, PATHINFO_DIRNAME));

            if ( ! is_dir($directory) OR ! is_writable($directory))
            {
                throw new Exception('Directory must be writable: '.$directory);
            }
        }

        // The quality must be in the range of 1 to 100
        $quality = min(max($quality, 1), 100);

        return $this->_do_save($file, $quality);
    }

    /**
     * Render the image and return the binary string.
     *
     *     // Render the image at 50% quality
     *     $data = $image->render(NULL, 50);
     *
     *     // Render the image as a PNG
     *     $data = $image->render('png');
     *
     * @param   string   ImageK type to return: png, jpg, gif, etc
     * @param   integer  quality of image: 1-100
     * @return  string
     * @uses    Image::_do_render
     */
    public function render($type = NULL, $quality = 100)
    {
        if ($type === NULL)
        {
            // Use the current image type
            $type = image_type_to_extension($this->type, FALSE);
        }

        return $this->_do_render($type, $quality);
    }

    /**
     * Execute a resize.
     *
     * @param   integer  new width
     * @param   integer  new height
     * @return  void
     */
    abstract protected function _do_resize($width, $height);

    /**
     * Execute a crop.
     *
     * @param   integer  new width
     * @param   integer  new height
     * @param   integer  offset from the left
     * @param   integer  offset from the top
     * @return  void
     */
    abstract protected function _do_crop($width, $height, $offset_x, $offset_y);

    /**
     * Execute a rotation.
     *
     * @param   integer  degrees to rotate
     * @return  void
     */
    abstract protected function _do_rotate($degrees);

    /**
     * Execute a flip.
     *
     * @param   integer  direction to flip
     * @return  void
     */
    abstract protected function _do_flip($direction);

    /**
     * Execute a sharpen.
     *
     * @param   integer  amount to sharpen
     * @return  void
     */
    abstract protected function _do_sharpen($amount);

    /**
     * Execute a reflection.
     *
     * @param   integer   reflection height
     * @param   integer   reflection opacity
     * @param   boolean   TRUE to fade out, FALSE to fade in
     * @return  void
     */
    abstract protected function _do_reflection($height, $opacity, $fade_in);

    /**
     * Execute a watermarking.
     *
     * @param   object   watermarking Image
     * @param   integer  offset from the left
     * @param   integer  offset from the top
     * @param   integer  opacity of watermark
     * @return  void
     */
    abstract protected function _do_watermark(ImageK $image, $offset_x, $offset_y, $opacity);

    /**
     * Execute a background.
     *
     * @param   integer  red
     * @param   integer  green
     * @param   integer  blue
     * @param   integer  opacity
     * @return void
     */
    abstract protected function _do_background($r, $g, $b, $opacity);

    /**
     * Execute a save.
     *
     * @param   string   new image filename
     * @param   integer  quality
     * @return  boolean
     */
    abstract protected function _do_save($file, $quality);

    /**
     * Execute a render.
     *
     * @param   string    ImageK type: png, jpg, gif, etc
     * @param   integer   quality
     * @return  string
     */
    abstract protected function _do_render($type, $quality);

} // End Image
