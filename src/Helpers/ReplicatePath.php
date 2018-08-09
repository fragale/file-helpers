<?php

namespace Fragale\FileHelpers;

use Fragale\FileHelpers\ExplorePath;

class ReplicatePath
{



  /**
   * Create a new instance.
   *
   * @return void
   */
  public function __construct()
  {
    $this->hasConflicts=false;
    $this->notExistsInSource=[];
    $this->alreadyExistsInTarget=[];
    $this->conflictBuffer=[];
    $this->outputBuffer=[];
  }


  /**
   * Analize if paths are valid
   *
   * @return boolean
   */
  public function analize($sourcePath, $targetPath)
  {

    $this->checkIfExists($sourcePath);
    $this->checkIfExists($targetPath);

    if (!$this->hasConflicts) {
      $s=new ExplorePath();
      $t=new ExplorePath();

      $this->source=$s->explore($sourcePath);
      $this->target=$t->explore($targetPath);

      $this->checkConflicts();
    }

    $this->makeOutputBuffer();

    return ($this->hasConflicts) ? false : true;

  }

  /**
   * Check conflicts between paths
   *
   * @return void
   */
  public function checkConflicts()
  {

    /*busca cuales son los archivos en source que se encuentran en target*/
    foreach ($this->source as $key => $fileInfo) {
      if(array_search($fileInfo['path'], array_column($this->target, 'path'))){
        $this->alreadyExistsInTarget[]=$fileInfo['path'];
        $this->hasConflicts=true;
      }
    }

    /*busca cuales son los archivos en target que se no se encuentran en source*/
    foreach ($this->target as $key => $fileInfo) {
      if(array_search($fileInfo['path'], array_column($this->source, 'path'))){
        $this->notExistsInSource[]=$fileInfo['path'];
      }
    }

  }

  /**
   * Chech if path exists
   *
   * @return boolean
   */
  public function checkIfExists($path)
  {

    if (!file_exists($path)){
      $this->hasConflicts=true;
      $this->conflictBuffer[]="Path $path doesn't exists!";
    }

    return ($this->hasConflicts) ? false : true;

  }


  /**
   * Make output buffer
   *
   * @return array
   */
  public function makeOutputBuffer()
  {

    if (count($this->conflictBuffer)){
      $this->outputBuffer[]="You need to resolve the following conflicts before continue:";
      foreach ($this->conflictBuffer as $text) {
        $this->outputBuffer[]=$text;
      }
    }

    if (count($this->alreadyExistsInTarget)){
      $this->outputBuffer[]="Conflict!. This following files already exists in target path:";
      foreach ($this->alreadyExistsInTarget as $text) {
        $this->outputBuffer[]=$text;
      }
    }

    if (count($this->notExistsInSource)){
      $this->outputBuffer[]="Warning!. This following are in target but doesn't exists in source path:";
      foreach ($this->notExistsInSource as $text) {
        $this->outputBuffer[]=$text;
      }
    }

    return $this->outputBuffer;

  }



}
