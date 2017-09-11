<?php
/**
 * Created by PhpStorm.
 * User: pengcheng8
 * Date: 17/9/11
 * Time: 下午5:50
 * DESC:
 */

interface IteratorAggregate extends Traversable
{
    public function getIterator();
}

interface Iterator extends Traversable
{
    public function current();

    public function next();

    public function key();

    public function valid();

    public function rewind();

}