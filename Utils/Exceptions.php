<?php
namespace Mazer;

use Exception;
class MazeException extends Exception{
  function __construct(string $message=null){
    if($message){
      $this->message = $message;
    }
  }
};

class MazeRouteException extends MazeException{};

