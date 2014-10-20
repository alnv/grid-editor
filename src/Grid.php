<?php
/**
 * Created by PhpStorm.
 * User: david
 * Date: 06.05.14
 * Time: 11:45
 */

namespace Netzmacht\Bootstrap\Grid;

use Netzmacht\Bootstrap\Grid\Builder\GridBuilder;

class Grid
{
    /**
     * @var array
     */
    protected $columns = array();

    /**
     * @var array
     */
    private static $gridsFromDb = array();

    /**
     * @param $columns
     */
    public function __construct(array $columns = array())
    {
        foreach ($columns as $column) {
            $this->addColumn($column);
        }
    }

    /**
     * @param  int $gridId
     * @return \Netzmacht\Bootstrap\Grid\Grid
     * @throws \InvalidArgumentException
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

        for ($i=0; $i<$columns; $i++) {
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
     * @param array $column
     */
    public function addColumn(array $column)
    {
        $this->columns[] = $column;
    }

    /**
     * @param $index
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
     * @param $index
     * @return bool
     */
    public function hasColumn($index)
    {
        return isset($this->columns[$index]);
    }

    /**
     * @param $index
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
     * @return array
     */
    public function getColumns()
    {
        return $this->columns;
    }
}
