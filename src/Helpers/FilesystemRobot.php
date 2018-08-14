<?php

namespace Fragale\FileHelpers;

use Fragale\FileHelpers\FileTouch;
use Fragale\FileHelpers\ReplicatePath;

class FilesystemRobot
{

  public $fileBuffer;

  /**
   * Create a new instance.
   *
   * @return mixed
   */
  public function __construct()
  {

  }


  /**
   * Run actions over filesystem, based in an array containing instructions.
   *
   * @return array
   */
  public function run($instructionset)
  {
    foreach ($instructionset as $instructions) {
      foreach ($instructions as $instruction => $sentence) {
        //dump($instruction);
        //dump($sentence);
        switch ($instruction) {
          case 'mkdir':
          $this->runMkdir($sentence);
          break;

          case 'copy':
          $this->runCopy($sentence);
          break;

          case 'addconfig':
          $this->runAddConfig($sentence);
          break;

          case 'replicate':
          $this->runReplicate($sentence);
          break;

          default:
          try {
            throw new \Exception ("No se reconoce la instruccion $instruction");
          } catch (\Exception $e) {
            echo 'Error: '.$e->getMessage()."\n";
          }
          break;
        }

      }
    }
  }

  /**
   * Create a directory, based in an array containing instructions
   *
   * @return boolean
   */
  public function runMkdir($sentence)
  {

    if (array_key_exists('path',$sentence) and array_key_exists('target',$sentence)){

      $destination=$sentence['target'].'/'.$sentence['path'];
      echo "->creating directory: $destination \n";

      try {
        if (!file_exists($destination)){
          mkdir($destination, 0777, true);
          return true;
        } else {
          echo "Warning: $destination ya existe, no se ejecuta accion \n";
        }
      } catch (\Exception $e) {
         echo 'Error: '.$e->getMessage()."\n";
      }

    }else{
      try {
        throw new \Exception ("la instruccion mkdir está incompleta debe especificar path =>'path/to/create', target => 'target/where/create'\n");
      } catch (\Exception $e) {
         echo 'Error: '.$e->getMessage()."\n";
      }

    }
    return false;
  }

  /**
   * Copy a file, based in an array containing instructions
   *
   * @return boolean
   */
  public function runCopy($sentence,$replace=false)
  {

    if (array_key_exists('file',$sentence) and array_key_exists('from',$sentence) and array_key_exists('to',$sentence)){

      $source=$sentence['from'].'/'.$sentence['file'];
      $target_file= (array_key_exists('rename',$sentence)) ? $sentence['rename'] : $sentence['file'];
      $target=$sentence['to'].'/'.$target_file;

      $replace=(array_key_exists('replace',$sentence) and $sentence['replace']) ? true : $replace;

      //dump("copy source: $source");
      //dump("copy target: $target");

      echo "->copying $source to $target \n";


      if (file_exists($source)){
        /*si el file existe y hay que reemplazarlo*/
        if (file_exists($target) and $replace){
          try {
            unlink($target);
          } catch (\Exception $e) {
            echo 'Error: '.$e->getMessage()."\n";
          }
        }

        /*intenta hacer la copia*/
        try {
          if (!file_exists($target)){
            copy($source, $target);
            return true;
          } else {
            echo "Warning: $target ya existe, no se reemplazara \n";
          }
        } catch (\Exception $e) {
          echo 'Error: '.$e->getMessage()."\n";
        }

      } else {
        echo "Warning: $source no existe, no se copiara \n";
      }

    }else{
      try {
        throw new \Exception ("la instruccion copy está incompleta debe especificar file=>'filename.ext', from =>'source/path', to => 'target/path'\n");
      } catch (\Exception $e) {
         echo 'Error: '.$e->getMessage()."\n";
      }

    }
    return false;
  }


  /**
   * Alter a config file basen in array format, like config/app.php in Laravel
   *
   * @return boolean
   */
  public function runAddConfig($sentence)
  {

    if (array_key_exists('file',$sentence) and array_key_exists('key',$sentence) and array_key_exists('add',$sentence)){

      echo "->adding value in key ".$sentence['key']." to ".$sentence['file']." \n";

      $file=new FileTouch();
      return $file->addKeyValueToConfigFile($sentence['key'], $sentence['add'], $sentence['file']);

    } else {
          try {
            throw new \Exception ("la instruccion addconfig está incompleta debe especificar file=>'filename.ext', key =>'arrayelemnt', add => 'value'\n");
          } catch (\Exception $e) {
             echo 'Error: '.$e->getMessage()."\n";
          }

    }

    return false;
  }


  /**
   * Alter a config file basen in array format, like config/app.php in Laravel
   *
   * @return boolean
   */
  public function runReplicate($sentence)
  {
    if (array_key_exists('source',$sentence) and array_key_exists('target',$sentence)){

      echo "->replicating ".$sentence['source']." into ".$sentence['target']."\n";

      $replicator=new ReplicatePath();
      $replicator->analize($sentence['source'], $sentence['target']);
      $replicator->copy();
      return true;

    } else {
          try {
            throw new \Exception ("la instruccion replicate está incompleta debe especificar source=>'source/path', target=>'target/path'\n");
          } catch (\Exception $e) {
             echo 'Error: '.$e->getMessage()."\n";
          }

    }

    return false;

  }







}
