<?php
/**
 * Created by PhpStorm.
 * User: david
 * Date: 06.05.14
 * Time: 11:52
 */

namespace Netzmacht\Bootstrap\Grid\GridBuilder;


use Netzmacht\Bootstrap\Grid\Grid;

class GridBuilder
{
	/**
	 * @var Column[]
	 */
	protected $columns = array();


	/**
	 * @return GridBuilder
	 */
	public static function create()
	{
		return new static();
	}


	/**
	 * @return Column
	 */
	public function addColumn()
	{
		$column          = new Column($this);
		$this->columns[] = $column;

		return $column;
	}


	/**
	 * @return Grid
	 */
	public function build()
	{
		$grid = new Grid();

		foreach($this->columns as $column) {
			$grid->addColumn($column->build());
		}

		return $grid;
	}

} 