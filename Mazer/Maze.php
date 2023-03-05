<?php
namespace Mazer;

use Exception;

class MazeException extends Exception{};

class Maze{
  const SHAPE = "invalide shape of maze data";
  const DATA_TYPE = 'Incorrect type of maze data elements';

  public array $container;
  public int $width;
  public int $height;
  function __construct(array $maze_data)
  {

    $this->container = array();
    $height = count($maze_data);
    $width = count($maze_data[0]);
    if($width < 2 or $height < 2){
      throw new MazeException(self::SHAPE);
    }
    
    foreach($maze_data as $row){
      $maze_row = array();
      if(count($row)!==$width){
        throw new MazeException(self::SHAPE);
      }
      foreach($row as $element){
        if(!is_numeric($element) or $element>9 or $element<0)
        {
          throw new MazeException(self::DATA_TYPE);
        }
        $maze_row[]=(int)$element;
      }
      $this->container[] = $maze_row;
    }
    $this->width = $width;
    $this->height = $height;
  }

  function getValue($x,$y){
    
    return $this->container[$y][$x];
  }

  function getPointValue($p){
    return $this->container[$p->y][$p->x];
  }
}
