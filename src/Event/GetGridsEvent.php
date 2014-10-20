<?php

namespace Netzmacht\Bootstrap\Grid\Event;

use Symfony\Component\EventDispatcher\Event;

/**
 * Class GetGridsEvent
 * @package Netzmacht\Bootstrap\Grid\Event
 */
class GetGridsEvent extends Event
{
    const NAME = 'bootstrap.get-grid-options';

    /**
     * @var \Database\Result
     */
    protected $model;

    /**
     * @var \ArrayObject
     */
    protected $grids;

    /**
     * @param $model
     * @param array $grids
     */
    public function __construct($model, array $grids=array())
    {
        $this->model = $model;
        $this->grids = new \ArrayObject($grids);
    }

    /**
     * @param \ArrayObject $grids
     */
    public function setGrids($grids)
    {
        $this->grids = $grids;
    }

    /**
     * @return \ArrayObject
     */
    public function getGrids()
    {
        return $this->grids;
    }

    /**
     * @return \Database\Result
     */
    public function getModel()
    {
        return $this->model;
    }

}
