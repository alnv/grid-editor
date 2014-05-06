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


	/**
	 * Setup semantic html5 grid integration
	 */
	public static function setUp()
	{
		if(static::isActive()) {
			$GLOBALS['TL_HOOKS']['parseTemplate'][] = array('Netzmacht\Bootstrap\Grid\Integration\SemanticHtml5', 'hookParseTemplate');
		}
	}


	/**
	 * @return bool
	 */
	public static function isActive()
	{
		return in_array('semantic_html5', \Config::getInstance()->getActiveModules());
	}


	/**
	 * Inject bootstrap isGridElement to semantic html5 palette
	 */
	public function callbackGeneratePalette()
	{
		$GLOBALS['TL_DCA']['tl_content']['palettes']['semantic_html5'] = str_replace(
			'sh5_additional',
			'sh5_additional,bootstrap_isGridElement',
			$GLOBALS['TL_DCA']['tl_content']['palettes']['semantic_html5']
		);
	}


	/**
	 * @param \Template $template
	 */
	public function hookParseTemplate(\Template $template)
	{
		if(substr($template->getName(), 0, 3) != 'ce_' || $template->type != 'semantic_html5' || $template->sh5_tag != 'start') {
			return;
		}


		// semantic html5 element is marked as beginning of new grid row
		if($template->bootstrap_isGridElement == 'row') {
			try {
				static::$grids[$template->id] = Grid::loadFromDatabase($template->bootstrap_grid);
				static::$count[$template->id] = 0;
			}
			catch(\InvalidArgumentException $e) {
				echo $e->getMessage();
				return;
			}

			$template->class .= ($template->class ? ' ' : '') . 'row';
		}

		// semantic html5 element is marked as an grid column
		if($template->bootstrap_isGridElement == 'column') {
			if(static::$grids[$template->bootstrap_gridRow]) {
				$row   = $template->bootstrap_gridRow;
				$grid  = static::$grids[$row];
				$index = static::$count[$row];

				$template->class .= ($template->class ? ' ' : '') . $grid->getColumnAsString($index);
				static::$count[$row]++;
			}
		}

	}

	/**
	 * @return array
	 */
	public function getGrids()
	{
		$grids  = array();
		$result = \Database::getInstance()
			->prepare('SELECT * FROM tl_columnset WHERE published=1 ORDER BY columns, title')
			->execute();

		while($result->next()) {
			$key = sprintf($GLOBALS['TL_LANG']['tl_content']['bootstrap_columns'], $result->columns);
			$grids[$key][$result->id] = $result->title;
		}

		return $grids;
	}


	/**
	 * @param $dc
	 * @return array
	 */
	public function getGridElements($dc)
	{
		$elements = array();

		if($dc->activeRecord) {
			$query = <<<SQL
SELECT
	c.*, g.title as gridTitle, g.columns as gridColumns
FROM
	tl_content c
LEFT JOIN
	tl_columnset g
ON
	g.id = c.bootstrap_grid
WHERE
	(c.ptable=? OR c.ptable=?) AND c.pid=? AND c.type=? AND c.sh5_tag=? AND c.bootstrap_isGridElement=? AND c.sorting < ?
ORDER BY
	sorting
SQL;

			$ptable = $GLOBALS['TL_DCA'][$dc->table]['config']['ptable'];
			$result = \Database::getInstance()
				->prepare($query)
				->execute(
					$ptable,
					$ptable == 'tl_article' ? '' : $ptable,
					$dc->activeRecord->pid,
					'semantic_html5',
					'start',
					'row',
					$dc->activeRecord->sorting
				);

			while($result->next()) {
				$headline = deserialize($result->headline, true);
				$elements[$result->id] = ($headline['value'] ?: ('ID ' . $result->id)) . ' [' .
					$result->gridColumns . ' - ' . $result->gridTitle . ']';
			}
		}

		return $elements;

	}

} 