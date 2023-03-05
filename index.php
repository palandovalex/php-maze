<?php
require __DIR__ . '/Mazer/Maze.php';
require __DIR__ . '/Mazer/PathFinder.php';
require __DIR__ . '/Utils/Point.php';
require __DIR__ . '/Mazer/MazeRouter.php';
require __DIR__ . '/Mazer/MazePrinter.php';


use \Utils\Point;
use \Mazer\Maze;
use \Mazer\MazeRouter;
use \Mazer\MazeException;
use \Mazer\MazePrinter;
use \Mazer\MazeRouteException;

echo "Please enter path to csv file of your maze
(it must be 2d array or single digits, coma separated): ";

$pathToMaze = trim(fgets(STDIN));

if(!$pathToMaze){
  $pathToMaze = __DIR__ . '/default_maze.csv';
}

$handle = fopen($pathToMaze, "r");
if ($handle === FALSE) {
  echo "cant open file with maze";
  exit();
}

$maze_data = array();
while (($mazeRow = fgetcsv($handle))!==False) {
  $maze_data[] = $mazeRow;
}

try{
  $maze = new Maze($maze_data);
}
catch(MazeException $e){
  echo $e;
  exit();
}

echo "Your maze is: \n";

$printer = new MazePrinter($maze);
$printer->print();

$maze_route = new MazeRouter($maze);

$default_route=[new Point('0,0'),new Point('10,20')];
$previos_input = '';
while(true){
  try{
    echo "Enter the route node(default \033[94m0,0 and 10,20\033[0m), or empty string to exit:\n";
    $user_input = trim(fgets(STDIN));
    if($user_input == '' ){
      if($previos_input==''){
        $maze_route->addRouteNode($default_route[0]);
        $maze_route->addRouteNode($default_route[1]);
      }
      break;
    }
    $previos_input = $user_input;
    $point = new Point($user_input);
    try{
      $maze_route->addRouteNode($point);
    }
    catch(MazeRouteException $e){
      print($e->getMessage());
      echo "\nincorrect route node, try again: \n";
      continue;
    }
  }
  catch(TypeError $e){
    print($e);
    echo "Please try again:\n";
  }
}

echo "computing optimal path...\n";

$finder = new \Mazer\DijkstraPathFinder();
$path = $maze_route->buildRoute($finder);

echo "Optimal path is \033[94m1\033[0m: \n";
$printer->print($maze_route);

//echo $maze->__toString($path);

