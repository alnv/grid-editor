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
     * Column resets after for each column.
     *
     * @var array
     */
    private $columnResets = array();

    /**
     * Custom row class.
     *
     * @var string
     */
    private $rowClass;

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

        $builder = GridBuilder::create();
        $classes = array();

        foreach (deserialize($result->customClasses, true) as $class) {
            $classes[$class['column']] = $class['class'];
        }

        $grid = self::buildGridColumns($builder, $result, $classes);
        $grid->setRowClass($result->rowClass);

        foreach (deserialize($result->resets, true) as $row) {
            foreach (array('xs', 'sm', 'md', 'lg') as $size) {
                if (isset($row[$size]) && $row[$size]) {
                    $grid->addColumnReset($row['column'], $size);
                }
            }
        }

        static::$gridsFromDb[$gridId] = $grid;

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

    /**
     * Add column clear fix for a specific column and size.
     *
     * @param int    $column The column index.
     * @param string $size   The grid size.
     *
     * @return $this
     */
    public function addColumnReset($column, $size)
    {
        if (!isset($this->columnResets[$column])) {
            $this->columnResets[$column] = array();
        }

        if (!in_array($size, $this->columnResets[$size])) {
            $this->columnResets[$column][] = $size;
        }

        return $this;
    }

    /**
     * Add clear fix for a column.
     *
     * @param int   $column The column index.
     * @param array $sizes  The grid sizes.
     *
     * @return $this
     */
    public function addColumnResets($column, array $sizes)
    {
        if (!isset($this->columnResets[$column])) {
            $this->columnResets[$column] = $sizes;
        } else {
            $this->columnResets[$column] = array_unique(array_merge($this->columnResets[$column], $sizes));
        }

        return $this;
    }

    /**
     * Get resets for a column.
     *
     * @param int $index The column index.
     *
     * @return array
     */
    public function getColumnResets($index)
    {
        if (isset($this->columnResets[$index])) {
            return $this->columnResets[$index];
        }

        return array();
    }

    /**
     * Check if column as a clear fix for a specific size.
     *
     * @param int    $column The column index.
     * @param string $size   The grid column size.
     *
     * @return bool
     */
    public function hasColumnResetForSize($column, $size)
    {
        if (!isset($this->columnResets[$column])) {
            return false;
        }

        return in_array($size, $this->columnResets[$column]);
    }

    /**
     * Get column resets as string.
     *
     * @param int         $index Column index.
     * @param string|null $tag   Custom html tag. Default divs are created.
     *
     * @return string
     */
    public function getColumnResetsAsString($index, $tag = null)
    {
        $tag = $tag ?: 'div';

        return implode(
            PHP_EOL,
            array_map(
                function ($item) use ($tag) {
                    return sprintf(
                        '<%s class="clearfix visible-%s-block"></%s>' . PHP_EOL,
                        $tag,
                        $item,
                        $tag
                    );
                },
                $this->getColumnResets($index)
            )
        );
    }

    /**
     * Get rowClass.
     *
     * @return string
     */
    public function getRowClass()
    {
        return $this->rowClass;
    }

    /**
     * Set rowClass.
     *
     * @param string $rowClass RowClass.
     *
     * @return $this
     */
    public function setRowClass($rowClass)
    {
        $this->rowClass = $rowClass;

        return $this;
    }

    /**
     * Build columns.
     *
     * @param GridBuilder      $builder The grid builder.
     * @param \Database\Result $result  The database result.
     * @param array            $classes Custom css classes.
     *
     * @return Grid
     */
    private static function buildGridColumns($builder, $result, $classes)
    {
        $columns = $result->columns;
        $sizes   = deserialize($result->sizes, true);

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

                $index = ($i + 1);

                if (!empty($classes[$index])) {
                    $column->setClass($classes[$index]);
                }
            }
        }

        return $builder->build();
    }
}
