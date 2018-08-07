<?php

namespace Fragale\FileHelpers\Helpers;

//use Illuminate\Filesystem\Filesystem as File;

class ExplorePath
{

  protected $fileBuffer;

  /**
   * Create a new instance.
   *
   * @return void
   */
  public function __construct()
  {
    $this->fileBuffer=[];
  }


  /**
   * explore a path and return an array with complete file paths and each type.
   *
   * @return array
   */
  public function explore($path)
  {

    if($this->isDir($path)){
      $this->read($path);
    } else {
      $this->addToBuffer($path);
    }

    return $this->fileBuffer;

  }



  /**
   * Read a directory and append his content to a buffer.
   *
   * @return void
   */
  public function read($path)
  {
    $this->addToBuffer($path);

    $resource = opendir($path);
    while (false !== ($fileName = readdir($resource))) {
      if($this->isValid($fileName)){
        if($this->isDir($path.'/'.$fileName)){
          $this->read($path.'/'.$fileName);
        } else {
          $this->addToBuffer($path.'/'.$fileName);
        }
      }
    }
  }

  /**
   * Add the complete path to buffer.
   *
   * @return void
   */
  public function addToBuffer($path)
  {
    $this->fileBuffer[]=['path' => $path,'type' => $this->fileType($path)];
  }


  /**
   * Check if $path is a dir.
   *
   * @return boolean
   */
  public function isDir($path)
  {
    return is_dir($path);
  }

  /**
   * Check if $fileName is a valid file name.
   *
   * @return boolean
   */
  public function isValid($fileName)
  {
    $invalidNames=['.', '..'];
    return !in_array($fileName, $invalidNames);
  }


  /**
   * Determinate the file type.
   * may be f=files, d=directory
   *
   * @return string
   */
  public function fileType($file)
  {
    return ($this->isDir($file)) ? 'd' : 'f';
  }


}
