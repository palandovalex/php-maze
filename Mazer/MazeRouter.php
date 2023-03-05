<?php
namespace Mazer;
use \Utils\Point;

final class PointType{
  const None  = 0;
  const Start = 1;
  const End   = 2;
  const Node  = 3;
  const Route = 4;
}

class MazeRouteException extends MazeException{};

class MazeRouter{
  private Maze $maze;
  private array $route_nodes;
  private array $optimal_route;
  public array $failed_route_nodes;
  public bool $failed;

  

  function __construct(Maze $maze)
  {
    $this->maze = $maze;
    $this->route_nodes = array();
    $this->failed_route_nodes = array();
    $this->failed = false;
  }

  function addRouteNode(Point $point){

    if(!$this->_checkPoint($point)){
      throw new MazeRouteException('this route node is out of bounds, or in the wall'); 
    }

    $previos_point=end($this->route_nodes);
    if($point->equals($previos_point))
    {
      throw new MazeRouteException("two consecutive route nodes must not match");
    }

    $this->route_nodes[]=$point;
  }

  function getPointType($x,$y){
    $pointHash = $x.','.$y;
    $routeSet = self::_getSet($this->optimal_route);
    $nodesSet = self::_getHashSet($this->route_nodes);


    if(array_key_exists($pointHash,$nodesSet)){
      if(end($this->route_nodes)->hash() == $pointHash){
        return PointType::End;
      }
      elseif($this->route_nodes[0]->hash() == $pointHash){
        return PointType::Start;
      }
      return PointType::Node;
    }
    elseif(array_key_exists($pointHash,$routeSet)){
      return PointType::Route;
    }
    return PointType::None;
  }

  function buildRoute(PathFinder $pathFinder){
    $nodes_count = count($this->route_nodes);
    $opt_route = array();
    if ($nodes_count<2){
      throw new MazeRouteException("It is impossible to build a route from {$nodes_count} nodes.");
    }

    $graf = $this->_buildGraf();

    $prev_node=$this->route_nodes[0];
    for($i=1;$i<count($this->route_nodes);$i++)
    {
      $node = $this->route_nodes[$i];

      try{
        $route_part = $pathFinder->findPath(
          $prev_node->hash(), $node->hash(), $graf
        );

        $opt_route = array_merge($opt_route, $route_part); 
        $prev_node = $node;
      }
      catch(PathNotFound $e){//dont delete $e
        $failed_route_nodes[]=$node;
      }
    }
    if($prev_node !== end($this->route_nodes)){
      $this->failed = true;
    }

    $this->optimal_route = $opt_route;
  }

  function _buildGraf(){
    $width = $this->maze->width;
    $height = $this->maze->height;
    $graf = [];
    for($y=0;$y<$height;$y++){
      for($x=0;$x<$width;$x++){
        $p = new Point([$x,$y]);
        if($this->_checkPoint($p)){
          $graf[($p->hash())] = $this->_getNextPoints($x,$y);
        }
      }
    }
    return $graf;
  }

  function _getNextPoints($x,$y){
    $ways = [[0,1],[1,0],[0,-1],[-1,0]];

    $next_points = [];
    foreach($ways as $way){
      list($dx,$dy) = $way;

      $next_point = new Point([$x+$dx,$y+$dy]);
      if($this->_checkPoint($next_point)){
        $cost = $this->maze->getPointValue($next_point);
        $next_points[]=[$cost, $next_point->hash()];
      }
    }
    return $next_points;
  }

  static function _getSet($points){
    $set = array();
    foreach($points as $point){
      $set[$point]=null;
    }
    return $set;
  }
  static function _getHashSet($points){
    $set = array();
    foreach($points as $point){
      $set[$point->hash()]=null;
    }
    return $set;
  }


  private function _checkPoint($point){
    $x = $point->x;
    $y = $point->y;
    $width = $this->maze->width;
    $height = $this->maze->height;
    $maze = $this->maze;
    if($x<0 or $x>= $width or $y<0 or $y>= $height or $maze->getValue($x,$y) == 0)
    { 
      return false;
    }
    return true;
  }
}
