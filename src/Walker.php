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
    function __construct(Grid $grid)
    {
        $this->grid = $grid;
    }

    /**
     * Start a new row
     * @return string
     */
    public function start()
    {
        $this->index = 0;

        return sprintf('<div class="row"><div class="%s">', $this->grid->getColumnAsString($this->index));
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

        return sprintf('</div><div class="%s">', $this->grid->getColumnAsString($this->index));
    }

    /**
     * End a column and the row
     *
     * @return string
     */
    public function end()
    {
        $this->index = 0;

        return '</div></div>';
    }

    /**
     * Automatically walk throw the grid
     *
     * @return string
     */
    public function walk()
    {
        if ($this->index == 0) {
            return $this->start();
        }

        if (!$this->grid->hasColumn($this->index+1)) {
            return $this->end() . $this->start();
        }

        return $this->column();
    }
} 