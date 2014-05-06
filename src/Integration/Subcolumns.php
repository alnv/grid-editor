<?php

namespace Netzmacht\Bootstrap\Grid\Integration;

use Netzmacht\Bootstrap\Grid\Grid;


class Subcolumns
{
	/**
	 * @var string
	 */
	protected $name = 'bootstrap_customizeable';

	/**
	 *
	 */
	public static function setUp()
	{
		if(in_array('subcolumns', \Config::getInstance()->getActiveModules())) {
			$GLOBALS['TL_HOOKS']['isVisibleElement'][] = array('Netzmacht\Bootstrap\Grid\Integration\Subcolumns', 'hookIsVisibleElement');
		}
	}


	/**
	 * @param \Model $model
	 * @param bool $isVisible
	 * @return bool
	 */
	public function hookIsVisibleElement(\Model $model, $isVisible)
	{
		if($GLOBALS['TL_CONFIG']['subcolumns'] == $this->name && (
			($model->getTable() == 'tl_module' && $model->type == 'subcolumns') ||
			$model->getTable() == 'tl_content' && ($model->type == 'colsetStart' ||
			$model->type == 'colsetPart'
		))) {
			if($model->type == 'colsetPart') {
				$modelClass = get_class($model);
				$parent     = $modelClass::findByPk($model->sc_parent);
				$type       = $parent->sc_type;
			}
			else {
				$type       = $model->sc_type;
			}

			$GLOBALS['TL_SUBCL'][$this->name]['sets'][$type] = $this->prepareContainer($model->columnset_id);
		}

		return $isVisible;
	}


	/**
	 * @param $id
	 * @return array
	 */
	protected function prepareContainer($id)
	{
		$container = array();
		$grid      = Grid::loadFromDatabase($id);

		foreach($grid->getColumns() as $column) {
			$container[][0] = array(implode(' ', $column));
		}

		return $container;
	}


} 