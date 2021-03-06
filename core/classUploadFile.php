<?php if(!defined('QCOM1'))exit();

class UploadFile
{
    public  $name;
    public  $filetype;
    public  $filesize;
    public  $extension;
    public  $source;
    public  $destin;
    public  $fileCategory;
    public  $srcWidth;
    public  $srcHeight;
    // Settings
    public  $directory = 'upload'; // upload directory
    public  $maxFileSize = 120; // MB, max size 120MB
    public  $maxWidth  = 175;   // max resize image width
    public  $maxHeight = 150;   // max resize image height
    public  $imageAllow = ['jpg','png','gif','bmp']; // images for thumbnails
    public  $fileAllow = ['tif','svg','ico','css','txt','pdf','zip','7z','tar.gz','tgz',
                            'mp4','swf','avi','mov','wmv','flv','mpg','mp3','flac','wav'];
    // Settings end
    public  $imageMime = array(
            'jpg' => ['image/jpeg','image/jpg','image/jp_','image/pjpeg'],
            'png' => ['image/png','image/x-png','application/png','application/x-png'],
            'gif' => ['image/gif','image/x-xbitmap','image/gi_'],
            'bmp' => ['image/bmp','image/x-bmp','image/x-bitmap'] );
    public  $fileMime = array(
            'tif' => ['image/tif','image/x-tif','image/tiff','image/x-tiff'],
            'svg' => ['image/svg+xml','application/svg+xml','image/svg-xml'],
            'ico' => ['image/ico','image/x-icon'],
            'css' => ['text/css','application/css-stylesheet'],
            'txt' => ['text/plain','application/txt'],
            'pdf' => ['application/pdf','application/x-pdf'],
            'zip' => ['application/zip','application/x-zip','application/x-zip-compressed','application/x-compressed'],
            '7z'  => ['application/x-7z-compressed'],
            'tar.gz' => ['application/gzip','application/x-gzip','application/x-tar'],
            'tgz' =>    ['application/gzip','application/x-gzip','application/x-tar'],
            'mp4' => ['video/mp4','video/mp4v-es'],
            'swf' => ['application/x-shockwave-flash'],
            'avi' => ['video/avi','video/msvideo','video/x-msvideo'],
            'mov' => ['video/quicktime','video/x-quicktime'],
            'wmv' => ['video/x-ms-wmv'],
            'flv' => ['video/x-flv'],
            'mpg' => ['video/mpeg','video/mpg','video/x-mpg'],
            'mp3' => ['audio/mp3','audio/x-mp3'],
            'flac' => ['audio/flac'],
            'wav' => ['audio/wav','audio/x-wav','audio/wave'] );

    public function __construct()
    {
    }

    public function registerFile($file)
    {
        if ($file['error']) {
            exit('File upload error: '.$file['error']);
        }
        $this->name      = $file['name'];
        $this->extension = $this->getExtension();
        $this->name = pathinfo($this->name, PATHINFO_FILENAME).$this->extension;
        $this->filetype  = $file['type'];
        $this->filesize  = $file['size'];
        $this->source    = $file['tmp_name'];
        $this->destin    = $this->directory.'/'.$this->name;
        $this->checkDir();
        $this->checkSize();
        $this->validateFile();
    }

    public function upload()
    {
        if (!move_uploaded_file($this->source, $this->destin)){
            echo "Move uploaded files error:<br>".$this->source.'<br>'.$this->destin;
            exit();
        }
        $this->setSource($this->destin);
    }

    public function validateFile()
    {
        $allImageMimes = array();
        foreach($this->imageAllow as $img) {
            $allImageMimes = array_merge($allImageMimes, $this->imageMime[$img]);
        }
        $allFileMimes = array();
        foreach($this->fileAllow as $file) {
            $allFileMimes = array_merge($allFileMimes, $this->fileMime[$file]);
        }
        if (in_array($this->filetype, $allImageMimes)) {
            $this->fileCategory = 'image';
            $this->getDimensions();
        } elseif (in_array($this->filetype, $allFileMimes)) {
            $this->fileCategory = 'file';
        } else {
            $this->fileCategory = 'nosupport';
            exit('<b>'.$this->name.'</b><br>
                File with extension <b>'.$this->extension.'</b> not supported'.'<br>
                Mime filetype: '.$this->filetype.'<br>
                File category: '.$this->fileCategory );
        }
        if (in_array($this->filetype, $this->imageMime['bmp'])) {
            $this->bmp2jpg();
        }      
    }

    public function getFileData()
    {
        $fdata = array(
            'filename' => $this->name,
            'filetype' => $this->filetype,
            'extension' => $this->extension,
            'filesize' => $this->filesize,
            'fsource' => $this->source,
            'fdestin' => $this->destin );
        if ($this->fileCategory == 'image') {
            $fdata['width']  = $this->srcWidth;
            $fdata['height'] = $this->srcHeight;
        }
        return $fdata;
    }

    public function checkSize()
    {
        if ($this->filesize > $this->maxFileSize * 1048576) {
            exit('File size too big!<br>
            Max size is: <b>'.$this->maxFileSize.' MB</b>');
        }
    }

    public function getExtension()
    {
            return '.'.strtolower(pathinfo($this->name, PATHINFO_EXTENSION));
    }

    public function setDirectory($dir)
    {
        $this->directory = $dir;
        $this->checkDir();
        $this->destin = $this->directory.'/'.$this->name;
    }

    public function checkDir()
    {
        if (!file_exists($this->directory))
            if (!mkdir($this->directory))
                exit('Upload Directory does not exist and could not be created');
    }

    public function getSupported()
    {
        $all = array_merge($this->imageAllow, $this->fileAllow);
        $supported = implode(', ', $all);
        return $supported;
    }

    public function setFileSize($size) //MB, megabytes
    {
        $this->maxFileSize = $size;
    }

    public function setName($name)
    {
        $this->name = $name;
        $this->destin = $this->directory.'/'.$this->name;
    }

    public function setSource($file)
    {
        $this->source = $file;
    }

    public function setDestin($file)
    {
        $this->destin = $file;
    }

    public function getDimensions()
    {
        list($this->srcWidth, $this->srcHeight) = getimagesize($this->source);
    }        
        
    public function setMaxSize($width, $height)
    {
        $this->maxWidth  = $width;
        $this->maxHeight = $height;
    }

    public function getDisplayWidth($image)
    {
        list($width, $height) = getimagesize($image);
        $k = min($this->maxWidth/$width, $this->maxHeight/$height);
        if ($k >= 1)
            return $width;
        else
            return round($k * $width);
    }

    public function bmp2jpg()
    {
        require 'core/classConvertBMP.php';
        ConvertBMP::bmp2jpg($this->source, $this->source);
        $this->name = str_replace($this->extension, '.jpg', $this->name);
        $this->filetype = 'image/jpeg';
        $this->extension = '.jpg';
        $this->filesize = filesize($this->source);
        $this->getDimensions();
        $this->destin = $this->directory.'/'.$this->name;
    }

    public function resize()
    {
        if ($this->srcWidth <= $this->maxWidth && $this->srcHeight <= $this->maxHeight) {
            copy($this->source, $this->destin);
            return;
        }
        $imgSource = $this->source;
        $imgResize = $this->destin;

        $k = min($this->maxWidth/$this->srcWidth, $this->maxHeight/$this->srcHeight);               
        $newWidth  = round($k * $this->srcWidth);
        $newHeight = round($k * $this->srcHeight);

        $thumbImage = imagecreatetruecolor( $newWidth, $newHeight );

        if (in_array($this->filetype, $this->imageMime['jpg'])) {
            $srcImage = imagecreatefromjpeg($imgSource);
        } elseif (in_array($this->filetype, $this->imageMime['png'])) {
            $srcImage = imagecreatefrompng($imgSource);
            imagealphablending($thumbImage, FALSE);
            imagesavealpha($thumbImage, TRUE);
         	$transparent = imagecolorallocatealpha($thumbImage, 255, 255, 255, 127);
            imagefilledrectangle($thumbImage, 0, 0, $newWidth, $newHeight, $transparent);
        } elseif (in_array($this->filetype, $this->imageMime['gif'])) {
            $srcImage = imagecreatefromgif($imgSource);
            $transIndex = imagecolortransparent($srcImage); 
            $transColor = array('red'=>255, 'green'=>255, 'blue'=>255); 
            if ($transIndex >= 0)
                $transColor = imagecolorsforindex($srcImage, $transIndex);    
            $transIndex = imagecolorallocate($thumbImage,$transColor['red'],$transColor['green'],$transColor['blue']); 
            imagefill($thumbImage, 0, 0, $transIndex); 
            imagecolortransparent($thumbImage, $transIndex);
        } elseif (in_array($this->filetype, $this->imageMime['bmp'])) {
            $srcImage = imagecreatefrombmp($imgSource);
        } else {
            exit('Error. Not a valid image. Can not resize.');
        }

        imagecopyresampled($thumbImage, $srcImage, 0, 0, 0, 0, 
            $newWidth, $newHeight, $this->srcWidth, $this->srcHeight);

        if (in_array($this->filetype, $this->imageMime['jpg'])) {
            imagejpeg($thumbImage, $imgResize, 90);
        } elseif (in_array($this->filetype, $this->imageMime['png'])) {
            imagepng($thumbImage, $imgResize, 5);
        } elseif (in_array($this->filetype, $this->imageMime['gif'])) {
            imagegif($thumbImage, $imgResize);
        } elseif (in_array($this->filetype, $this->imageMime['bmp'])) {
            imagebmp($thumbImage, $imgResize);
        }

        imagedestroy($srcImage);
        imagedestroy($thumbImage);
        
        return $this->destin;
    }

}
