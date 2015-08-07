<?php

/**
 * @package   contao-bootstrap
 * @author    David Molineus <david.molineus@netzmacht.de>
 * @license   LGPL 3+
 * @copyright 2013-2015 netzmacht creative David Molineus
 */

namespace Netzmacht\Bootstrap\Grid\Integration;

use Netzmacht\Bootstrap\Grid\Event\GetGridsEvent;
use Netzmacht\Bootstrap\Grid\Factory;
use Netzmacht\Bootstrap\Grid\Grid;

/**
 * SemanticHtml5 integration.
 *
 * @package Netzmacht\Bootstrap\Grid\Integration
 */
class SemanticHtml5
{
    /**
     * Column count indexes.
     *
     * @var array
     */
    public static $count = array();

    /**
     * The used grids.
     *
     * @var Grid[]
     */
    public static $grids = array();

    /**
     * Setup semantic html5 grid integration.
     *
     * @return void
     *
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    public static function setUp()
    {
        if (static::isActive()) {
            $GLOBALS['TL_HOOKS']['parseTemplate'][] = array(
                'Netzmacht\Bootstrap\Grid\Integration\SemanticHtml5',
                'hookParseTemplate'
            );

            $GLOBALS['TL_HOOKS']['getContentElement'][] = array(
                'Netzmacht\Bootstrap\Grid\Integration\SemanticHtml5',
                'hookGetContentElement'
            );

            $GLOBALS['TL_EVENTS'][GetGridsEvent::NAME][] = get_called_class() . '::getGrids';
        }
    }

    /**
     * Check if semantic html is active.
     *
     * @return bool
     */
    public static function isActive()
    {
        return in_array('semantic_html5', \Config::getInstance()->getActiveModules());
    }

    /**
     * Get grids from the database.
     *
     * @param GetGridsEvent $event The subscribed event.
     *
     * @return void
     *
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    public static function getGrids(GetGridsEvent $event)
    {
        $model = $event->getModel();
        $grids = $event->getGrids();

        if ($model->type == 'semantic_html5') {
            $query  = 'SELECT * FROM tl_columnset WHERE published=1 ORDER BY title';
            $result = \Database::getInstance()->query($query);

            while ($result->next()) {
                $key = sprintf($GLOBALS['TL_LANG']['tl_content']['bootstrap_columns'], $result->columns);

                $grids[$key][$result->id] = $result->title;
            }
        }
    }

    /**
     * Inject bootstrap isGridElement to semantic html5 palette.
     *
     * @return void
     *
     * @SuppressWarnings(PHPMD.Superglobals)
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
     * Use the parse template hook to inject column and row classes.
     *
     * @param \Template $template The template.
     *
     * @return void
     */
    public function hookParseTemplate(\Template $template)
    {
        if (substr($template->getName(), 0, 3) != 'ce_'
            || $template->type != 'semantic_html5'
            || $template->sh5_tag != 'start'
        ) {
            return;
        }

        $this->createRow($template);
        $this->createColumn($template);
    }

    /**
     * Add clear fixes for a column.
     *
     * @param \ContentModel $model  The content model.
     * @param string        $buffer The content element output.
     *
     * @return string
     */
    public function hookGetContentElement($model, $buffer)
    {
        if ($model->type === 'semantic_html5'
            && $model->sh5_tag === 'start'
            && $model->bootstrap_isGridElement === 'column'
            && static::$grids[$model->bootstrap_gridRow]
        ) {
            $row  = $model->bootstrap_gridRow;
            $grid = static::$grids[$row];

            // Index is already incremented, so go back
            $index = (static::$count[$row] - 1);

            $buffer = $grid->getColumnResetsAsString($index) . $buffer;
        }

        return $buffer;
    }

    /**
     * Get all grid elements.
     *
     * @param \DataContainer $dataContainer The data container driver.
     *
     * @return array
     *
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    public function getGridElements($dataContainer)
    {
        $elements = array();

        if ($dataContainer->activeRecord) {
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
    (c.ptable=? OR c.ptable=?)
    AND c.pid=?
    AND c.type=?
    AND c.sh5_tag=?
    AND c.bootstrap_isGridElement=?
    AND c.sorting < ?
ORDER BY
    sorting
SQL;

            $ptable = $GLOBALS['TL_DCA'][$dataContainer->table]['config']['ptable'];
            $result = \Database::getInstance()
                ->prepare($query)
                ->execute(
                    $ptable,
                    $ptable == 'tl_article' ? '' : $ptable,
                    $dataContainer->activeRecord->pid,
                    'semantic_html5',
                    'start',
                    'row',
                    $dataContainer->activeRecord->sorting
                );

            while ($result->next()) {
                $headline              = deserialize($result->headline, true);
                $elements[$result->id] = ($headline['value'] ?: ('ID ' . $result->id)) . ' [' .
                    $result->gridColumns . ' - ' . $result->gridTitle . ']';
            }
        }

        return $elements;
    }

    /**
     * Create the row class.
     *
     * @param \Template $template The template.
     *
     * @return void
     */
    private function createRow($template)
    {
        // semantic html5 element is marked as beginning of new grid row
        if ($template->bootstrap_isGridElement == 'row') {
            try {
                static::$grids[$template->id] = Factory::createById($template->bootstrap_grid);
                static::$count[$template->id] = 0;
            } catch (\InvalidArgumentException $e) {
                echo $e->getMessage();

                return;
            }

            $class = ($template->class ? ' ' : '') . 'row';

            if (static::$grids[$template->id]->getRowClass()) {
                $class .= ' ' . static::$grids[$template->id]->getRowClass();
            }

            $template->class .= $class;
        }
    }

    /**
     * Create the column classes.
     *
     * @param \Template $template The template.
     *
     * @return void
     */
    private function createColumn($template)
    {
        // semantic html5 element is marked as an grid column
        if ($template->bootstrap_isGridElement == 'column') {
            if (static::$grids[$template->bootstrap_gridRow]) {
                $row   = $template->bootstrap_gridRow;
                $grid  = static::$grids[$row];
                $index = static::$count[$row];

                $template->class .= ($template->class ? ' ' : '') . $grid->getColumnAsString($index);
                static::$count[$row]++;
            }
        }
    }
}
