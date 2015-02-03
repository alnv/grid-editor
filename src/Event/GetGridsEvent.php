<?php

/**
 * @package   contao-bootstrap
 * @author    David Molineus <david.molineus@netzmacht.de>
 * @license   LGPL 3+
 * @copyright 2013-2015 netzmacht creative David Molineus
 */

namespace Netzmacht\Bootstrap\Grid\Event;

use Symfony\Component\EventDispatcher\Event;

/**
 * Class GetGridsEvent is emitted when the grid informations in the backend are loaded.
 *
 * @package Netzmacht\Bootstrap\Grid\Event
 */
class GetGridsEvent extends Event
{
    const NAME = 'bootstrap.get-grid-options';

    /**
     * The current model.
     *
     * @var \Database\Result
     */
    protected $model;

    /**
     * The grids as array object.
     *
     * @var \ArrayObject
     */
    protected $grids;

    /**
     * Construct.
     *
     * @param \Database\Result|\Model $model The context model.
     * @param array                   $grids The defined grids.
     */
    public function __construct($model, array $grids = array())
    {
        $this->model = $model;
        $this->grids = new \ArrayObject($grids);
    }

    /**
     * Set the grids.
     *
     * @param \ArrayObject $grids The grids.
     *
     * @return $this
     */
    public function setGrids($grids)
    {
        $this->grids = $grids;

        return $this;
    }

    /**
     * Get the grids.
     *
     * @return \ArrayObject
     */
    public function getGrids()
    {
        return $this->grids;
    }

    /**
     * Get the context model.
     *
     * @return \Database\Result|\Model
     */
    public function getModel()
    {
        return $this->model;
    }
}
