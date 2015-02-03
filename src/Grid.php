<?php

/**
 * @package   contao-bootstrap
 * @author    David Molineus <david.molineus@netzmacht.de>
 * @license   LGPL 3+
 * @copyright 2013-2015 netzmacht creative David Molineus
 */

namespace Netzmacht\Bootstrap\Grid;

use Netzmacht\Bootstrap\Grid\Builder\GridBuilder;

/**
 * Class Grid stores the grid columns definitions.
 *
 * @package Netzmacht\Bootstrap\Grid
 */
class Grid
{
    /**
     * The columns definition.
     *
     * @var array
     */
    protected $columns = array();

    /**
     * Database created grids.
     *
     * @var array
     */
    private static $gridsFromDb = array();

    /**
     * Construct.
     *
     * @param array $columns Column definitions.
     */
    public function __construct(array $columns = array())
    {
        foreach ($columns as $column) {
            $this->addColumn($column);
        }
    }

    /**
     * Lod grid from database.
     *
     * @param int $gridId The databse grid id.
     *
     * @return Grid
     *
     * @throws \InvalidArgumentException If grid is not defined.
     */
    public static function loadFromDatabase($gridId)
    {
        if (isset(static::$gridsFromDb[$gridId])) {
            return static::$gridsFromDb[$gridId];
        }

        $result = \Database::getInstance()
            ->prepare('SELECT * FROM tl_columnset WHERE id=? AND published=1')
            ->limit(1)
            ->execute($gridId);

        if ($result->numRows < 1) {
            throw new \InvalidArgumentException(sprintf('Could not find columnset with ID "%s"', $gridId));
        }

        $columns = $result->columns;
        $sizes   = deserialize($result->sizes, true);
        $builder = GridBuilder::create();

        for ($i = 0; $i < $columns; $i++) {
            $column = $builder->addColumn();

            foreach ($sizes as $size) {
                $key    = 'columnset_' . $size;
                $values = deserialize($result->$key, true);

                $column->forDevice(
                    $size,
                    $values[$i]['width'],
                    $values[$i]['offset'] ?: null,
                    $values[$i]['order'] ?: null
                );
            }
        }

        static::$gridsFromDb[$gridId] = $builder->build();

        return static::$gridsFromDb[$gridId];
    }

    /**
     * Add a column.
     *
     * @param array $column The column definition.
     *
     * @return $this
     */
    public function addColumn(array $column)
    {
        $this->columns[] = $column;

        return $this;
    }

    /**
     * Get a column by it's index. If not defined an empty array is returned.
     *
     * @param int $index The column index.
     *
     * @return array
     */
    public function getColumn($index)
    {
        if (isset($this->columns[$index])) {
            return $this->columns[$index];
        }

        return array();
    }

    /**
     * Check is a column exists.
     *
     * @param int $index The index.
     *
     * @return bool
     */
    public function hasColumn($index)
    {
        return isset($this->columns[$index]);
    }

    /**
     * Get column as class string.
     *
     * @param int $index Column index.
     *
     * @return string
     */
    public function getColumnAsString($index)
    {
        if (isset($this->columns[$index])) {
            return implode(' ', $this->columns[$index]);
        }

        return '';
    }

    /**
     * Get all columns.
     *
     * @return array
     */
    public function getColumns()
    {
        return $this->columns;
    }
}
