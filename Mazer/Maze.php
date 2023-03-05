<?php
namespace Mazer;

use Exception;

class MazeException extends Exception{};

class Maze{
  const SHAPE = "invalide shape of maze data";
  const DATA_TYPE = 'Incorrect type of maze data elements';

  const COLOR_OFF = "\033[0m";
  const COLOR_CODES =array(
    "\033[47,30m",
    "\033[96m",
    "\033[96m",
    "\033[96m",
    "\033[33m",
    "\033[33m",
    "\033[33m",
    "\033[91m",
    "\033[91m",
    "\033[91m"
  );
  const COLOR_POINT = "\033[43m";
  const COLOR_NODE = "\033[42m";
  const COLOR_START_NODE = "\033[44m";
  const COLOR_END_NODE = "\033[44m";


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


  function __toString(){
    $futter = '|'.str_repeat("-", $this->width).'|'."\n";
    $result = $futter;

    for($y=0;$y<count($this->container);$y++)
    {
      $result = $result . "|";

      $line = $this->container[$y];
      for($x=0;$x<count($line); $x++){
        $e = $line[$x];
        $code = self::COLOR_CODES[$e];
        $result = $result . $code . 
          $e . self::COLOR_OFF;
      }
      $result = $result . "|\n";
    }
    $result = $result . $futter;
    return $result;
  }



  function printWithRouter(MazeRouter $router){
    $futter = '|' . str_repeat("-", $this->width) . '|';
    $result = $futter . "\n";

    $pointsSet = $router->getPointsSet();
    $nodesSet = $router->getNodesSet();

    for($y=0;$y<count($this->container);$y++)
    {
      $result = $result . "|";

      $line = $this->container[$y];
      for($x=0;$x<count($line); $x++){
        $e = $line[$x];
        $pointHash = $x.','.$y;
        $code = self::getColorCode($e,$pointsSet,$nodesSet,$pointHash);
        $result = $result . $code . 
          $e . self::COLOR_OFF;
      }
      $result = $result . "|\n";
    }
    $result = $result . $futter;
    return $result;
  }

  static function getColorCode($element, $pointsSet, $nodesSet, $hash){
    if($pointsSet[$hash]){
      return self::COLOR_POINT; 
    }
    elseif($nodesSet[$hash]){
      return self::COLOR_NODE;
    }
    return self::COLOR_CODES[$element];
  }
}
