<?php

/**
 * @package   contao-bootstrap
 * @author    David Molineus <david.molineus@netzmacht.de>
 * @license   LGPL 3+
 * @copyright 2013-2015 netzmacht creative David Molineus
 */

namespace Netzmacht\Bootstrap\Grid\Builder;

use Netzmacht\Bootstrap\Grid\Grid;

/**
 * Class GridBuilder is used to create the grid definition.
 *
 * @package Netzmacht\Bootstrap\Grid\Builder
 */
class GridBuilder
{
    /**
     * The grid columns.
     *
     * @var Column[]
     */
    protected $columns = array();

    /**
     * Create the grid builder.
     *
     * @return GridBuilder
     */
    public static function create()
    {
        return new static();
    }

    /**
     * Add a new column.
     *
     * @return Column
     */
    public function addColumn()
    {
        $column          = new Column($this);
        $this->columns[] = $column;

        return $column;
    }

    /**
     * Get a column.
     *
     * @param int $index The column index.
     *
     * @return Column
     */
    public function getColumn($index)
    {
        return $this->columns[$index];
    }

    /**
     * Build the grid.
     *
     * @return Grid
     */
    public function build()
    {
        $grid = new Grid();

        foreach ($this->columns as $index => $column) {
            $grid->addColumn($column->build());
            $grid->addColumnResets($index, $column->getResets());
        }

        return $grid;
    }
}
