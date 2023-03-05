<?php
namespace Mazer;

use Utils\Point;

class MazePrinter{

  const COLOR_OFF = "\033[0m";

  const COLOR_WALL  = "\033[47;30m";
  const COLOR_START = "\033[44m";
  const COLOR_END   = "\033[41m";
  const COLOR_ROUTE = "\033[43m";
  const COLOR_NODE  = "\033[42m";

  const COLOR_POINTS =array(
    0=>self::COLOR_WALL, 
    1=>"\033[96m",
    2=>"\033[96m", 
    3=>"\033[96m",
    4=>"\033[33m",
    5=>"\033[33m",
    6=>"\033[33m",
    7=>"\033[91m",
    8=>"\033[91m",
    9=>"\033[91m"
  );

  private Maze $maze;
  function __construct($maze){
    $this->maze = $maze;
  }

  function print(MazeRouter $router=null){

    echo "maze legend: ".
      self::COLOR_START.'start,'.self::COLOR_OFF.' '.
      self::COLOR_ROUTE.'route,'.self::COLOR_OFF.' '.
      self::COLOR_NODE.'middle route nodes,'.self::COLOR_OFF.' '.
      self::COLOR_END.'route end,'.self::COLOR_OFF.' '.
      self::COLOR_WALL.'wall,'.self::COLOR_OFF.' '."\n";
    
    if($router){
      if($router->failed){
        echo "failed to build your route\n";
      }
      elseif(count($router->failed_route_nodes)){
        echo "cant build route for some of middle points of points\n";
      }
      echo $this->_printMazeRoute($router);
      return;
    }
    echo $this->_printMaze();
  }

  function _printMaze(){
    $maze = $this->maze;
    $futter = '|'.str_repeat("-", $maze->width).'|'."\n";
    $result = $futter;

    for($y=0;$y<count($maze->container);$y++)
    {
      $result .= "|";
      $line = $maze->container[$y];
      for($x=0;$x<count($line); $x++){
        $e = $line[$x];
        $code = self::COLOR_POINTS[$e];
        $result .= $code . $e . self::COLOR_OFF;
      }
      $result .= "|\n";
    }
    $result .= $futter;
    return $result;
  }

  function printVisited($visited, MazeRouter $router){
    $arrows = [
      '-1,0' => '←',
      '1,0' => '→',
      '0,-1' => '↑',
      '0,1' => '↓'
    ];
    $maze = $this->maze;
    $futter = '|' . str_repeat("-", $maze->width) . "|\n";
    $result = $futter;


    for($y=0;$y<count($maze->container);$y++)
    {
      $result .= "|";

      $line = $maze->container[$y];
      for($x=0;$x<count($line); $x++){
        $e = $line[$x];
        $pointType = $router->getPointType($x,$y);
        $code = self::getColorCode($e,$pointType);

        $hash = $x.','.$y;
        if(array_key_exists($hash, $visited) and !is_null($visited[$hash])){
          $fromHash = $visited[$hash];
          $from = new Point($fromHash);
          $dx = $x - $from->x;
          $dy = $y - $from->y;
          $e = $arrows[$dx.','.$dy];
        }


        $result .= $code . $e . self::COLOR_OFF;
      }
      $result .= "|\n";
    }
    $result .= $futter;
    echo $result;
  }

  function _printMazeRoute(MazeRouter $router){
    $maze = $this->maze;
    $futter = '|' . str_repeat("-", $maze->width) . "|\n";
    $result = $futter;


    for($y=0;$y<count($maze->container);$y++)
    {
      $result .= "|";

      $line = $maze->container[$y];
      for($x=0;$x<count($line); $x++){
        $e = $line[$x];
        $pointType = $router->getPointType($x,$y);
        $code = self::getColorCode($e,$pointType);
        $result .= $code . $e . self::COLOR_OFF;
      }
      $result .= "|\n";
    }
    $result .= $futter;
    return $result;
  }

  static function getColorCode(int $element, $pointType){
    switch($pointType){
      case PointType::Start:
        return self::COLOR_START;
      case PointType::End:
        return self::COLOR_END;
      case PointType::Node:   
        return self::COLOR_NODE;
      case PointType::Route: 
        return self::COLOR_ROUTE;
      case PointType::None: 
        return self::COLOR_POINTS[$element]; 
    };
  }


}

