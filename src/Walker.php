<?php

/**
 * @package   contao-bootstrap
 * @author    David Molineus <david.molineus@netzmacht.de>
 * @license   LGPL 3+
 * @copyright 2013-2015 netzmacht creative David Molineus
 */

namespace Netzmacht\Bootstrap\Grid;

/**
 * Class Walker allows to walk through a grid and automatically create the grid html.
 *
 * @package Netzmacht\Bootstrap\Grid
 */
class Walker
{
    /**
     * The grid being used.
     *
     * @var Grid
     */
    private $grid;

    /**
     * The current index.
     *
     * @var int
     */
    private $index;

    /**
     * Construct.
     *
     * @param Grid $grid The grid.
     */
    public function __construct(Grid $grid)
    {
        $this->grid = $grid;
    }

    /**
     * Start a new row.
     *
     * @return string
     */
    public function begin()
    {
        $this->index = 0;

        return sprintf(
            '<div class="row%s">%s%s<div class="%s">%s',
            $this->grid->getRowClass() ? (' ' . $this->grid->getRowClass()) : '',
            PHP_EOL,
            $this->grid->getClearFixesAsString($this->index),
            $this->grid->getColumnAsString($this->index),
            PHP_EOL
        );
    }

    /**
     * Get the current column.
     *
     * @return string
     */
    public function column()
    {
        $this->index++;

        if (!$this->grid->hasColumn($this->index)) {
            $this->index = 0;
        }

        return sprintf(
            '%s</div>%s%s<div class="%s">%s',
            PHP_EOL,
            PHP_EOL,
            $this->grid->getClearFixesAsString($this->index),
            $this->grid->getColumnAsString($this->index),
            PHP_EOL
        );
    }

    /**
     * End a column and the current row.
     *
     * @return string
     */
    public function end()
    {
        $this->index = 0;

        return sprintf('%s</div>%s</div>%s', PHP_EOL, PHP_EOL, PHP_EOL);
    }

    /**
     * Automatically walk throw the grid.
     *
     * @return string
     */
    public function walk()
    {
        if ($this->index == 0) {
            return $this->begin();
        }

        if (!$this->grid->hasColumn($this->index + 1)) {
            return $this->end() . $this->begin();
        }

        return $this->column();
    }
}
