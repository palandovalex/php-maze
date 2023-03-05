<?php
namespace Mazer;

use Exception;

interface PathFinder{
  function findPath($start, $goal, $graf);
} 

class PathNotFound extends Exception{}

class DijkstraPathFinder implements PathFinder {
  function findPath($start, $goal, $graf){
    return self::_dijkstra($start,$goal,$graf);  
  }

  public $visited = null;
  function _dijkstra($start, $goal, $graf){
    $graf = $graf;

    $queue = [[0, $start]];
    $cost_visited = [$start => 0];
    $visited = [$start => null];

    while(count($queue)){

      list( ,$curr_node) = array_pop($queue);
      if($curr_node==$goal)
      {
        break;
      }

      $next_nodes = $graf[$curr_node];
      foreach($next_nodes as $next){
        list($neigh_cost, $neigh_node) = $next;

        if($curr_node == $neigh_node){
          var_dump($next_nodes);
          var_dump($curr_node);
          var_dump($neigh_node);
          throw new Exception();
        }

        $new_cost = $cost_visited[$curr_node] + $neigh_cost;

        if((!array_key_exists($neigh_node, $cost_visited)) or 
          ($new_cost < $cost_visited[$neigh_node]))
        {
          array_unshift($queue,[$new_cost,$neigh_node]);
          $cost_visited[$neigh_node] = $new_cost;
          $visited[$neigh_node] = $curr_node;
        }
      }
    }
    if(!($visited[$goal])){
      throw new PathNotFound();
    }

    $optimal_route = [$goal];
    while(end($optimal_route)!==$start){
      $optimal_route[] = $visited[end($optimal_route)];
    }

    $this->visited = $visited;


    return $optimal_route;
  }

  static function visitedVizualise($visited){
    sort($visited);
    
  }
}
