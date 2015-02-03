<?php

/**
 * @package   contao-bootstrap
 * @author    David Molineus <david.molineus@netzmacht.de>
 * @license   LGPL 3+
 * @copyright 2013-2015 netzmacht creative David Molineus
 */

namespace Netzmacht\Bootstrap\Grid\Builder;

use Netzmacht\Bootstrap\Grid\Grid;

class GridBuilder
{
    /**
     * @var Column[]
     */
    protected $columns = array();

    /**
     * @return GridBuilder
     */
    public static function create()
    {
        return new static();
    }

    /**
     * @return Column
     */
    public function addColumn()
    {
        $column          = new Column($this);
        $this->columns[] = $column;

        return $column;
    }

    /**
     * @param $index
     * @return Column
     */
    public function getColumn($index)
    {
        return $this->columns[$index];
    }

    /**
     * @return Grid
     */
    public function build()
    {
        $grid = new Grid();

        foreach ($this->columns as $column) {
            $grid->addColumn($column->build());
        }

        return $grid;
    }
}
