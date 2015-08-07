<?php

/**
 * @package    contao-bootstrap
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2015 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */
namespace Netzmacht\Bootstrap\Grid;

use Netzmacht\Bootstrap\Grid\Builder\GridBuilder;

/**
 * Grid factory.
 *
 * @package Netzmacht\Bootstrap\Grid
 */
class Factory
{
    /**
     * Database created grids.
     *
     * @var array
     */
    private static $cache = array();

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


    /**
     * Fetch the database result.
     *
     * @param int $gridId The grid id.
     *
     * @return \Database\Result
     * @throws \InvalidArgumentException If grid is not found.
     */
    private static function fetchResult($gridId)
    {
        $result = \Database::getInstance()
            ->prepare('SELECT * FROM tl_columnset WHERE id=? AND published=1')
            ->limit(1)
            ->execute($gridId);

        if ($result->numRows < 1) {
            throw new \InvalidArgumentException(sprintf('Could not find columnset with ID "%s"', $gridId));
        }

        return $result;
    }

    /**
     * Prepare the css classes.
     *
     * @param \Database\Result $result The database result.
     *
     * @return array
     */
    private static function prepareClasses($result)
    {
        $classes = array();

        foreach (deserialize($result->customClasses, true) as $class) {
            $classes[$class['column']] = $class['class'];
        }

        return $classes;
    }

    /**
     * Build the column resets.
     *
     * @param \Database\Result $result The database result.
     * @param Grid             $grid   The grid being built.
     *
     * @return void
     */
    private static function buildColumnResets($result, $grid)
    {
        foreach (deserialize($result->resets, true) as $row) {
            foreach (array('xs', 'sm', 'md', 'lg') as $size) {
                if (isset($row[$size]) && $row[$size]) {
                    $grid->addColumnReset(($row['column'] - 1), $size);
                }
            }
        }
    }

    /**
     * Create the grid by id.
     *
     * @param int $gridId The grid id.
     *
     * @return Grid
     * @throws \InvalidArgumentException If grid is not found.
     */
    public static function createById($gridId)
    {
        if (isset(static::$cache[$gridId])) {
            return static::$cache[$gridId];
        }

        $result  = self::fetchResult($gridId);
        $builder = GridBuilder::create();
        $classes = self::prepareClasses($result);

        $grid = self::buildGridColumns($builder, $result, $classes);
        $grid->setRowClass($result->rowClass);

        self::buildColumnResets($result, $grid);

        static::$cache[$gridId] = $grid;

        return static::$cache[$gridId];
    }
}
