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



  static function _dijkstra($start, $goal, $graf){
    $graf = $graf;

    $queue = [[0, $start]];
    $cost_visited = [$start => 0];
    $visited = [$start => null];

    while(count($queue)){

      self::_sortByCost($queue);
      list( ,$curr_node) = array_pop($queue);
      if($curr_node==$goal)
      {
        break;
      }

      $next_nodes = $graf[$curr_node];
      foreach($next_nodes as $next_node){
        list($neigh_cost, $neigh_node) = $next_node;
        $new_cost = $cost_visited[$curr_node] + $neigh_cost;

        $neigh_node;
        if(!array_key_exists($neigh_node, $cost_visited) or 
          $new_cost < $cost_visited[$neigh_node])
        {
          $queue[]=[$new_cost,$neigh_node];
          $cost_visited[$neigh_node] = $new_cost;
          $visited[$neigh_node] = $curr_node;
        }
      }
    }
    if(!($visited[$goal])){
      throw new PathNotFound();
    }



    print('count visited - '.count($visited)."\n");
    $optimal_route = [$goal];
    while(end($optimal_route)!==$start){
      $curr_node = end($optimal_route);
      $next_nodes = $graf[$curr_node];
      $cost_next_nodes = [];
      foreach($next_nodes as $node){
        list(,$node) = $node;
        if(array_key_exists($node, $visited)){
          $node_cost = $cost_visited[$node];
          $cost_next_nodes[] = [$node_cost,$node];
        }
      }
      $optimal_route[]=self::_getCheapestNode($cost_next_nodes);
    }
    return $optimal_route;
  }

  static private function _getCheapestNode($nodes){
    return min($nodes)[1];
  }
  
  static private function _sortByCost($queue){
    usort($queue, function($a,$b){
      return -($a[0]-$b[0]);
    });
  }
}
