<?php

namespace Netzmacht\Bootstrap\Grid\Integration;

use Netzmacht\Bootstrap\Core\Bootstrap;
use Netzmacht\Bootstrap\Grid\Event\GetGridsEvent;
use Netzmacht\Bootstrap\Grid\Grid;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;


class Subcolumns
{
	/**
	 * @var string
	 */
	protected static $name = 'bootstrap_customizable';

	/**
	 *
	 */
	public static function setUp()
	{
		if(static::isActive()) {
			$GLOBALS['TL_HOOKS']['isVisibleElement'][]     = array('Netzmacht\Bootstrap\Grid\Integration\Subcolumns', 'hookIsVisibleElement');
			$GLOBALS['TL_EVENTS'][GetGridsEvent::NAME][]   = 'Netzmacht\Bootstrap\Grid\Integration\Subcolumns::getGrids';
            $GLOBALS['TL_HOOKS']['parseTemplate'][]        = array('Netzmacht\Bootstrap\Grid\Integration\Subcolumns', 'hookParseTemplate');
		}
	}

	/**
	 * @return bool
	 */
	public static function isActive()
	{
		return in_array('Subcolumns', \Config::getInstance()->getActiveModules())
			&& $GLOBALS['TL_CONFIG']['subcolumns'] == static::$name;
	}

    /**
     * @param \Template $template
     */
    public function hookParseTemplate(\Template $template)
    {
        if(TL_MODE == 'BE'
            && $template->getName() == 'be_subcolumns'
            && Bootstrap::getConfigVar('grid-editor.backend.replace-subcolumns-template')
        ) {
            $template->setName('be_subcolumns_bootstrap');
        }
    }

	/**
	 * @param \Model $model
	 * @param bool $isVisible
	 * @return bool
	 */
	public function hookIsVisibleElement(\Model $model, $isVisible)
	{
		if($GLOBALS['TL_CONFIG']['subcolumns'] == static::$name && (
			($model->getTable() == 'tl_module' && $model->type == 'subcolumns') ||
			$model->getTable() == 'tl_content' && ($model->type == 'colsetStart' ||
			$model->type == 'colsetPart'
		))) {
			if($model->type == 'colsetPart') {
				$modelClass = get_class($model);
				$parent     = $modelClass::findByPk($model->sc_parent);
				$type       = $parent->sc_type;
				$id         = $parent->bootstrap_grid;
			}
			else {
				$type       = $model->sc_type;
				$id         = $model->bootstrap_grid;
			}

			try {
				$GLOBALS['TL_SUBCL'][static::$name]['sets'][$type] = $this->prepareContainer($id);
			}
			catch(\Exception $e) {
			}
		}

		return $isVisible;
	}


	/**
	 * add column set field to the colsetStart content element. We need to do it dynamically because subcolumns
	 * creates its palette dynamically
	 *
	 * @param $dc
	 */
	public function appendColumnsetIdToPalette($dc)
	{
		if($dc->table == 'tl_content') {
			$model = \ContentModel::findByPK($dc->id);

			if($model->sc_type > 0) {
				\MetaPalettes::appendFields($dc->table, 'colsetStart', 'colset', array('bootstrap_grid'));
			}
		} else {
			$model = \ModuleModel::findByPk($dc->id);

			if($model->sc_type > 0) {
				$GLOBALS['TL_DCA']['tl_module']['palettes']['subcolumns'] = str_replace(
					'sc_type,',
					'sc_type,columnset_id,',
					$GLOBALS['TL_DCA']['tl_module']['palettes']['subcolumns']
				);
			}
		}
	}


	/**
	 * @param GetGridsEvent $event
	 */
	public static function getGrids(GetGridsEvent $event)
	{
		$model = $event->getModel();
		$grids = $event->getGrids();

		if($model->type == 'colsetStart' || $model->type == 'submcolumns') {
			$query   = 'SELECT * FROM tl_columnset WHERE published=1 AND columns=? ORDER BY title';
			$columns = $model->sc_type;
			$result  = \Database::getInstance()
				->prepare($query)
				->execute($columns);

			while($result->next()) {
				$grids[$result->id] = $result->title;
			}
		}
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
			$container[] = array(implode(' ', $column));
		}

		return $container;
	}


} 