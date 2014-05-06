<?php
/**
 * Created by PhpStorm.
 * User: david
 * Date: 06.05.14
 * Time: 11:38
 */

namespace Netzmacht\Bootstrap\Grid\Integration;


use Netzmacht\Bootstrap\Grid\Grid;

class SemanticHtml5
{
	/**
	 * @var array
	 */
	static $count = array();

	/**
	 * @var Grid[]
	 */
	static $grids = array();


	public static function setUp()
	{
		if(in_array('semantic_html5', \Config::getInstance()->getActiveModules())) {
			$GLOBALS['TL_HOOKS']['parseTemplate'][] = array('Netzmacht\Bootstrap\Grid\Integration\SemanticHtml5', 'hookParseTemplate');
		}
	}


	/**
	 * @param \Template $template
	 */
	public function hookParseTemplate(\Template $template)
	{
		if(substr($template->getName(), 0, 3) != 'ce_' || $template->type != 'semantic_html5' || $template->sh5_type != 'start') {
			return;
		}

		// semantic html5 element is marked as beginning of new grid row
		if($template->bootstrap_isGridRow) {
			try {
				static::$grids[$template->id] = Grid::loadFromDatabase($template->bootstrap_columnset);
				static::$count[$template->id] = 0;
			}
			catch(\InvalidArgumentException $e) {
				return;
			}

			$template->class .= ($template->class ? ' ' : '') . 'row';
		}

		// semantic html5 element is marked as an grid column
		if($template->bootstrap_isGridColumn) {
			if(static::$grids[$template->bootstrap_gridRow]) {
				$row   = $template->bootstrap_gridRow;
				$grid  = static::$grids[$row];
				$index = static::$count[$row];

				$template->class .= ($template->class ? ' ' : '') . $grid->getColumnAsString($index);
				static::$count[$row]++;
			}
		}

	}

} 