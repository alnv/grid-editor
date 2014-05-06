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
	function __construct(array $columns=array())
	{
		foreach($columns as $column) {
			$this->addColumn($column);
		}
	}


	/**
	 * @param $id
	 * @return \Netzmacht\Bootstrap\Grid\Grid
	 * @throws \InvalidArgumentException
	 */
	public static function loadFromDatabase($id)
	{
		if(isset(static::$gridsFromDb[$id])) {
			return static::$gridsFromDb[$id];
		}

		$result = \Database::getInstance()
			->prepare('SELECT * FROM tl_columnset WHERE id=? AND published=1')
			->limit(1)
			->execute($id);

		if($result->numRows < 1) {
			throw new \InvalidArgumentException(sprintf('Could not find columnset with ID "%s"', $id));
		}

		$columns = $result->columns;
		$sizes   = deserialize($result->sizes, true);
		$builder = GridBuilder::create();

		for($i=0; $i<$columns; $i++) {
			$column = $builder->addColumn();

			foreach($sizes as $size) {
				$key    = 'columnset_' . $size;
				$values = deserialize($result->$key, true);

				$column->forDevice($size, $values[$i]['width'], $values[$i]['offset'] ?: null, $values[$i]['order'] ?: null);
			}
		}

		static::$gridsFromDb[$id] = $builder->build();

		return static::$gridsFromDb[$id];
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
		if(isset($this->columns[$index])) {
			return $this->columns[$index];
		}

		return array();
	}


	/**
	 * @param $index
	 * @return string
	 */
	public function getColumnAsString($index)
	{
		if(isset($this->columns[$index])) {
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