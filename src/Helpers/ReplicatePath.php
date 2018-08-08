<?php

namespace Fragale\FileHelpers;

use Fragale\FileHelpers\ExplorePath;

class ReplicatePath
{

  protected $fileConflictBuffer;

  /**
   * Create a new instance.
   *
   * @return void
   */
  public function __construct()
  {

  }


  /**
   * Duplicate a path structure
   *
   * @return boolean
   */
  public function replicate($sourcePath, $targetPath)
  {
    $source=new ExplorePath($sourcePath);
    $target=new ExplorePath($targetPath);

  }


}
