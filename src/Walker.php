<?php

/**
 * @package    contao-bootstrap
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Bootstrap\Grid;

/**
 * Class Walker allows to walk through a grid and automatically create the grid html
 *
 * @package Netzmacht\Bootstrap\Grid
 */
class Walker
{
    /**
     * @var Grid
     */
    private $grid;

    /**
     * @var int
     */
    private $index;

    /**
     * @param Grid $grid
     */
    public function __construct(Grid $grid)
    {
        $this->grid = $grid;
    }

    /**
     * Start a new row
     * @return string
     */
    public function begin()
    {
        $this->index = 0;

        return sprintf('<div class="row">%s<div class="%s">%s', PHP_EOL, $this->grid->getColumnAsString($this->index), PHP_EOL);
    }

    /**
     * @return string
     */
    public function column()
    {
        $this->index++;

        if (!$this->grid->hasColumn($this->index)) {
            $this->index = 0;
        }

        return sprintf(
            '%s</div>%s<div class="%s">%s',
            PHP_EOL,
            PHP_EOL,
            $this->grid->getColumnAsString($this->index),
            PHP_EOL
        );
    }

    /**
     * End a column and the row
     *
     * @return string
     */
    public function end()
    {
        $this->index = 0;

        return sprintf('%s</div>%s</div>%s', PHP_EOL, PHP_EOL, PHP_EOL);
    }

    /**
     * Automatically walk throw the grid
     *
     * @return string
     */
    public function walk()
    {
        if ($this->index == 0) {
            return $this->begin();
        }

        if (!$this->grid->hasColumn($this->index+1)) {
            return $this->end() . $this->begin();
        }

        return $this->column();
    }
}
