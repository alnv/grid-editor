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
    private $index = -1;

    /**
     * If true only the classes are generated.
     *
     * @var bool
     */
    private $classesOnly;

    /**
     * Loop over the classes by faking an infinite column set.
     *
     * @var bool
     */
    private $infinite;

    /**
     * The infinite index.
     *
     * @var int
     */
    private $infiniteIndex = -1;

    /**
     * Construct.
     *
     * @param Grid $grid        The grid.
     * @param bool $classesOnly If true only the classes are generated.
     * @param bool $infinite    If true it fakes an infinite mode. The column resets are parsed differently then.
     */
    public function __construct(Grid $grid, $classesOnly = false, $infinite = false)
    {
        $this->grid        = $grid;
        $this->classesOnly = $classesOnly;
        $this->infinite    = $infinite;
    }

    /**
     * Get the grid.
     *
     * @return Grid
     */
    public function getGrid()
    {
        return $this->grid;
    }

    /**
     * Start a new row.
     *
     * If classesOnly is set, only the first column is returned.
     *
     * @return string
     */
    public function begin()
    {
        $this->index = 0;
        $this->infiniteIndex++;

        if ($this->classesOnly) {
            return $this->grid->getColumnAsString($this->index);
        }

        return sprintf(
            '%s%s%s<div class="%s">%s',
            $this->beginRow(),
            PHP_EOL,
            $this->grid->getColumnResetsAsString($this->index),
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
        $this->infiniteIndex++;

        if (!$this->grid->hasColumn($this->index)) {
            $this->index = 0;
        }

        if ($this->classesOnly) {
            return $this->grid->getColumnAsString($this->index);
        }

        $buffer = sprintf(
            '%s</div>%s%s<div class="%s">%s',
            PHP_EOL,
            PHP_EOL,
            $this->grid->getColumnResetsAsString($this->index),
            $this->grid->getColumnAsString($this->index),
            PHP_EOL
        );

        return $buffer;
    }

    /**
     * End a column and the current row.
     *
     * @return string
     */
    public function end()
    {
        $this->index = 0;

        if ($this->classesOnly) {
            return '';
        }

        return sprintf('%s</div>%s</div>%s', PHP_EOL, PHP_EOL, PHP_EOL);
    }

    /**
     * Automatically walk throw the grid.
     *
     * @return string
     */
    public function walk()
    {
        if ($this->index < 0) {
            return $this->begin();
        }

        if (!$this->grid->hasColumn($this->index + 1)) {
            return $this->end() . $this->begin();
        }

        return $this->column();
    }

    /**
     * Begin a new row.
     *
     * @return string
     */
    public function beginRow()
    {
        if ($this->classesOnly) {
            return trim('row ' . $this->grid->getRowClass());
        }

        return sprintf('<div class="%s">', trim('row ' . $this->grid->getRowClass()));
    }

    /**
     * Get the column resets for the current position, always as html.
     *
     * @param string|null $tag Custom html tag.
     *
     * @return string
     */
    public function getColumnResets($tag = null)
    {
        if (!$this->infinite || $this->infiniteIndex === 0 || $this->grid->hasColumn($this->infiniteIndex)) {
            return $this->grid->getColumnResetsAsString($this->index, $tag);
        }

        $number = count($this->grid->getColumns());

        return $this->grid->getColumnResetsAsString(($this->infiniteIndex % $number), $tag);
    }

    /**
     * Get index.
     *
     * @param bool $ignoreInfinite
     *
     * @return int
     */
    public function getIndex($ignoreInfinite = false)
    {
        if ($this->infinite && !$ignoreInfinite) {
            return $this->infiniteIndex;
        }

        return $this->index;
    }
}
