<?php
namespace Utils;

use TypeError;

class PointError extends TypeError{

};

class Point{
  public int $x;
  public int $y;
  function __construct($point){
    if(gettype($point) == 'string'){
      $point=explode(',',$point);
      if(count($point) !==2){
        throw new PointError('cant parse int point');
      }
    }
    list($x,$y) = $point;
    $this->x = (int)$x;
    $this->y = (int)$y;
    if(!is_int($this->x) or !is_int($this->y))
    {
      throw new PointError('cant parse int point.');
    }
  }

  function equals($point){
    if(!($point instanceof self)){
      return false;
    }
    $t = $this;
    $p = $point;
    return (($t->x == $p->x) and ($t->y == $p->y));
  }

  function hash(){
    return $this->x.','.$this->y;
  }
}
