<?php

/**
 * @package   contao-bootstrap
 * @author    David Molineus <david.molineus@netzmacht.de>
 * @license   LGPL 3+
 * @copyright 2013-2015 netzmacht creative David Molineus
 */

namespace Netzmacht\Bootstrap\Grid\DataContainer;

use Netzmacht\Bootstrap\Core\Bootstrap;
use Netzmacht\Bootstrap\Grid\Event\GetGridsEvent;

/**
 * Class ColumnSet provides helper methods for handling the data container and generating the dynamic column set.
 *
 * @package Netzmacht\ColumnSet
 */
class ColumnSet extends \Backend
{
    /**
     * Add column set field to the colsetStart content element.
     *
     * We need to do it dynamically because subcolumns creates its palette dynamically.
     *
     * @param \DataContainer $dataContainer The data container driver.
     *
     * @return void
     *
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    public function appendColumnsetIdToPalette($dataContainer)
    {
        if ($GLOBALS['TL_CONFIG']['subcolumns'] != 'bootstrap_customizable') {
            return;
        }

        if ($dataContainer->table == 'tl_content') {
            $model = \ContentModel::findByPK($dataContainer->id);

            if ($model->sc_type > 0) {
                \MetaPalettes::appendFields($dataContainer->table, 'colsetStart', 'colset', array('columnset_id'));
            }
        } else {
            $model = \ModuleModel::findByPk($dataContainer->id);

            if ($model->sc_type > 0) {
                if ($model->sc_type > 0) {
                    $GLOBALS['TL_DCA']['tl_module']['palettes']['subcolumns'] = str_replace(
                        'sc_type,',
                        'sc_type,columnset_id,',
                        $GLOBALS['TL_DCA']['tl_module']['palettes']['subcolumns']
                    );
                }
            }
        }
    }

    /**
     * Append column sizes fields dynamically to the palettes.
     *
     * @param \DataContainer $dataContainer The data container driver.
     *
     * @return void
     */
    public function appendColumnSizesToPalette($dataContainer)
    {
        $model = \Database::getInstance()
            ->prepare('SELECT * FROM tl_columnset WHERE id=?')
            ->limit(1)
            ->execute($dataContainer->id);

        $sizes = array_merge(deserialize($model->sizes, true));

        foreach ($sizes as $size) {
            $field = 'columnset_' . $size;

            \MetaPalettes::appendFields('tl_columnset', 'columnset', array($field));
        }
    }

    /**
     * Create a MCW row for each column.
     *
     * @param string  $value Deseriazable value, for getting an array.
     * @param \Widget $mcw   The multi column wizard or DC_Table.
     *
     * @return mixed
     */
    public function createColumns($value, $mcw)
    {
        $columns = (int) $mcw->activeRecord->columns;
        $value   = deserialize($value, true);
        $count   = count($value);
        $total   = Bootstrap::getConfigVar('grid-editor.columns');

        if ($count == 0) {
            // initialize columns

            for ($i = 0; $i < $columns; $i++) {
                $value[$i]['width'] = floor($total / $columns);
            }
        } elseif ($count > $columns) {
            // reduce columns if necessary

            $count = (count($value) - $columns);

            for ($i = 0; $i < $count; $i++) {
                array_pop($value);
            }
        } else {
            // make sure that column numbers has not changed

            for ($i = 0; $i < ($columns - $count); $i++) {
                $value[($i + $count)]['width'] = floor($total / $columns);
            }
        }

        return $value;
    }

    /**
     * Replace subcolumns getAllTypes method to load all created columnsets.
     *
     * There is a fallback provided if not bootstrap_customizable is used.
     *
     * @return array
     *
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    public function getAllTypes()
    {
        if ($GLOBALS['TL_CONFIG']['subcolumns'] != 'bootstrap_customizable') {
            return array_keys($GLOBALS['TL_SUBCL'][$GLOBALS['TL_CONFIG']['subcolumns']]['sets']);
        }

        $this->import('Database');
        $collection = $this->Database->execute('SELECT columns FROM tl_columnset GROUP BY columns ORDER BY columns');

        $types = array();

        while ($collection->next()) {
            $types[] = $collection->columns;
        }

        return $types;
    }

    /**
     * Get grids.
     *
     * @param \DataContainer $dataContainer The data container driver.
     *
     * @return array
     *
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    public function getGrids($dataContainer)
    {
        if ($dataContainer->activeRecord) {
            $dispatcher = $GLOBALS['container']['event-dispatcher'];
            $event      = new GetGridsEvent($dataContainer->activeRecord);
            $dispatcher->dispatch(GetGridsEvent::NAME, $event);

            return $event->getGrids()->getArrayCopy();
        }

        return array();
    }

    /**
     * Get columns for a a specific module.
     *
     * @param \DataContainer $dataContainer The data container driver.
     *
     * @return array
     *
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    public function getColumnsForModule($dataContainer)
    {
        if ($GLOBALS['TL_CONFIG']['subcolumns'] != 'bootstrap_customizable') {
            $subcolumns = new \tl_module_sc();

            return $subcolumns->getColumns($dataContainer);
        }

        $model = \ModuleModel::findByPK($dataContainer->currentRecord);
        $cols  = array();

        $translate = array('first', 'second', 'third', 'fourth', 'fith');

        for ($i = 0; $i < $model->sc_type; $i++) {
            if (!array_key_exists($i, $translate)) {
                break;
            }

            $key        = $translate[$i];
            $cols[$key] = $GLOBALS['TL_LANG']['MSC']['sc_' . $key];
        }

        return $cols;
    }

    /**
     * Create the order values.
     *
     * @return array
     */
    public function getColumnOrders()
    {
        $columns = Bootstrap::getConfigVar('grid-editor.columns');
        $values  = array();

        for ($i = 0; $i <= $columns; $i++) {
            $values['push'][] = 'push-' . $i;
            $values['pull'][] = 'pull-' . $i;
        }

        return $values;
    }

    /**
     * Get column widths.
     *
     * @return array
     */
    public function getWidths()
    {
        $columns = Bootstrap::getConfigVar('grid-editor.columns');
        $values  = range(0, $columns);

        return $values;
    }

    /**
     * Get column numbers.
     *
     * @return array
     */
    public function getColumns()
    {
        $columns = Bootstrap::getConfigVar('grid-editor.columns');
        $values  = range(1, $columns);

        return $values;
    }

    /**
     * Get column numbers.
     *
     * @param \DataContainer $dataContainer Data container driver.
     *
     * @return array
     */
    public function getColumnNumbers($dataContainer)
    {
        if ($dataContainer->activeRecord) {
            $columns = $dataContainer->activeRecord->columns;
        } else {
            $columns = Bootstrap::getConfigVar('grid-editor.columns');
        }

        $values = range(1, $columns);

        return $values;
    }
}
