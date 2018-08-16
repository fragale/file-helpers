<?php

namespace Fragale\FileHelpers;


class FileTouch
{

  public $name;

  /**
   * Create a new instance.
   *
   * @return mixed
   */
  public function __construct($filename='')
  {
    if($filename){
      $this->name=$filename;
    }
  }


  /**
   * Run actions over filesystem, based in an array containing instructions.
   *
   * @return array
   */
  public function getContent($filename)
  {
    return file_get_contents($filename);
  }

  public function putContent($content,$filename)
  {
    return file_put_contents($filename,$content);
  }


  /**
   * add a value to a config.file based in array format (like Laravel config/app.php)
   *
   * @return boolean
   */
  public function addKeyValueToConfigFile($key,$value,$filename)
  {
    $content=$this->getContent($filename);
    $offset=strpos($content,"'$key'");
    if ($offset===false){
      $offset=strpos($content,"\"$key\"");
    }
    /*the key don't exists?*/
    if ($offset===false){
      return false;
    }

    /*the value already exists in the element?*/
    $exists=strpos($content,$value,$offset+1);
    if ($exists!==false){
      return false;
    }

    $position=strpos($content,"]",$offset+1);

    /*can't determinate the end of element*/
    if ($position===false){
      return false;
    }


    $partA=substr($content,0,$position-1);

    $partB=substr($content,$position);

    return $this->putContent(substr($content,0,$position-1).$value.substr($content,$position),$filename);

  }


  /**
   * add a value to a .env (like Laravel .env)
   *
   * @return boolean
   */
  public function addKeyValueToEnvFile($key,$value,$filename)
  {
    $content=$this->getContent($filename);

    /*the key already exists in the .env file?*/
    $exists=strpos($content,$key);
    if ($exists!==false){
      return false;
    }

    return $this->putContent($content.PHP_EOL.$key.'='.$value.PHP_EOL,$filename);



  }

  /**
   * replace in file a pattern that'll be sustituted whit values informed
   * in a array ['pattern' = 'value']
   *
   * @return boolean
   */
  public function replaceInFile($patterns,$filename)
  {
    $content=$this->getContent($filename);

    foreach ($patterns as $pattern => $sustitute) {
      $content = str_replace($pattern, $sustitute, $content);
    }

    return $this->putContent($content,$filename);

  }

  /**
   * rename files using a pattern for change the filename
   *
   * @return boolean
   */
  public function renameFiles($targetPath,$pattern, $sustitute)
  {
    $t=new ExplorePath();
    $target=$t->explore($targetPath);
    $hasChanges=false;

    foreach ($target as $key => $fileInfo) {
      if($fileInfo['type']=='f'){
        $path = pathinfo($fileInfo['full_path']);
        $filename = $path['dirname'].'/'.str_replace($pattern, $sustitute, $path['basename']);
        if ($fileInfo['full_path']<>$filename){
          //echo "-->".$fileInfo['full_path']."--> $filename \n";
          $hasChanges=rename($fileInfo['full_path'],$filename);
        }
      }
    }

    return $hasChanges;
  }


}
