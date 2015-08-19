<?php

/**
 * @package   contao-bootstrap
 * @author    David Molineus <david.molineus@netzmacht.de>
 * @license   LGPL 3+
 * @copyright 2013-2015 netzmacht creative David Molineus
 */

namespace Netzmacht\Bootstrap\Grid\Integration;

use Netzmacht\Bootstrap\Core\Bootstrap;
use Netzmacht\Bootstrap\Grid\Event\GetGridsEvent;
use Netzmacht\Bootstrap\Grid\Factory;
use Netzmacht\Bootstrap\Grid\Grid;

/**
 * Subcolumns integration.
 *
 * @package Netzmacht\Bootstrap\Grid\Integration
 */
class Subcolumns
{
    /**
     * The subcolumns config name.
     *
     * @var string
     */
    protected static $name = 'bootstrap_customizable';

    /**
     * Setup the subcolumns integration.
     *
     * @return void
     *
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    public static function setUp()
    {
        if (static::isActive()) {
            $GLOBALS['TL_HOOKS']['isVisibleElement'][] = array(
                'Netzmacht\Bootstrap\Grid\Integration\Subcolumns',
                'hookIsVisibleElement'
            );

            $GLOBALS['TL_EVENTS'][GetGridsEvent::NAME][] = 'Netzmacht\Bootstrap\Grid\Integration\Subcolumns::getGrids';

            $GLOBALS['TL_HOOKS']['parseTemplate'][] = array(
                'Netzmacht\Bootstrap\Grid\Integration\Subcolumns',
                'hookParseTemplate'
            );

            $GLOBALS['TL_HOOKS']['loadFormField'][] = array(
                'Netzmacht\Bootstrap\Grid\Integration\Subcolumns',
                'hookLoadFormField'
            );
        }
    }

    /**
     * Check if subcolumns integration is activated.
     *
     * @return bool
     */
    public static function isActive()
    {
        return in_array('Subcolumns', \Config::getInstance()->getActiveModules())
            && \Config::get('subcolumns') === static::$name;
    }

    /**
     * ParseTemplate hook being used to beatify the backend view.
     *
     * @param \Template $template The template.
     *
     * @return void
     */
    public function hookParseTemplate(\Template $template)
    {
        if (TL_MODE == 'BE'
            && $template->getName() == 'be_subcolumns'
            && Bootstrap::getConfigVar('grid-editor.backend.replace-subcolumns-template')
        ) {
            $template->setName('be_subcolumns_bootstrap');
        }
    }

    /**
     * The isVisibleElement hook is used to dynamically load the grid definitions from the database.
     *
     * Known limitation: If being logged in in the backend and frontend preview is used with show all elements
     * it does not work!
     *
     * @param \Model $model     The row model.
     * @param bool   $isVisible The current visible state.
     *
     * @return bool
     *
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    public function hookIsVisibleElement(\Model $model, $isVisible)
    {
        if (static::isActive() && (
            ($model->getTable() == 'tl_module' && $model->type == 'subcolumns') ||
            $model->getTable() == 'tl_content' && ($model->type == 'colsetStart' ||
            $model->type == 'colsetPart'
        ))) {
            if ($model->type == 'colsetPart') {
                $modelClass = get_class($model);
                $parent     = $modelClass::findByPk($model->sc_parent);
                $type       = $parent->sc_type;
                $gridId     = $parent->bootstrap_grid;
            } else {
                $type   = $model->sc_type;
                $gridId = $model->bootstrap_grid;
            }

            try {
                $this->updateSubcolumnsDefinition($gridId, $type);
            } catch (\Exception $e) {
                // Do not throw the exception in the frontend. If nothing could fetched the fallback is used.
            }
        }

        return $isVisible;
    }

    /**
     * Load columnset definition for the form field.
     *
     * @param \Widget $widget The form widget.
     *
     * @return \Widget
     *
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    public function hookLoadFormField($widget)
    {
        if ($widget->type === 'formcolstart') {
            $type   = $widget->fsc_type;
            $gridId = $widget->bootstrap_grid;
            $grid   = Factory::createById($gridId);

            $GLOBALS['TL_SUBCL'][static::$name]['sets'][$type] = $this->prepareContainer($grid);

        } elseif ($widget->type === 'formcolpart' || $widget->type === 'formcolend') {
            $parent = \FormFieldModel::findByPk($widget->fsc_parent);

            $this->updateSubcolumnsDefinition($parent->bootstrap_grid, $parent->fsc_type);
        }

        return $widget;
    }

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
        if ($dataContainer->table == 'tl_content') {
            $model = \ContentModel::findByPK($dataContainer->id);

            if ($model->sc_type > 0) {
                \MetaPalettes::appendFields($dataContainer->table, 'colsetStart', 'colset', array('bootstrap_grid'));
            }
        } elseif ($dataContainer->table == 'tl_form_field') {
            $model = \FormFieldModel::findByPk($dataContainer->id);

            if ($model->fsc_type > 0) {
                $GLOBALS['TL_DCA']['tl_form_field']['palettes']['formcolstart'] = str_replace(
                    'fsc_color,',
                    'fsc_color,bootstrap_grid,',
                    $GLOBALS['TL_DCA']['tl_form_field']['palettes']['formcolstart']
                );
            }
        } else {
            $model = \ModuleModel::findByPk($dataContainer->id);

            if ($model->sc_type > 0) {
                $GLOBALS['TL_DCA']['tl_module']['palettes']['subcolumns'] = str_replace(
                    'sc_type,',
                    'sc_type,columnset_id,',
                    $GLOBALS['TL_DCA']['tl_module']['palettes']['subcolumns']
                );
            }
        }
    }

    /**
     * Load all grids from the database.
     *
     * @param GetGridsEvent $event The subscribed event.
     *
     * @return void
     */
    public static function getGrids(GetGridsEvent $event)
    {
        $model = $event->getModel();
        $grids = $event->getGrids();

        if ($model->type == 'colsetStart' || $model->type == 'subcolumns') {
            $query   = 'SELECT * FROM tl_columnset WHERE published=1 AND columns=? ORDER BY title';
            $columns = $model->sc_type;
            $result  = \Database::getInstance()
                ->prepare($query)
                ->execute($columns);

            while ($result->next()) {
                $grids[$result->id] = $result->title;
            }
        } elseif ($model->type == 'formcolstart') {
            $query   = 'SELECT * FROM tl_columnset WHERE published=1 AND columns=? ORDER BY title';
            $columns = $model->fsc_type;
            $result  = \Database::getInstance()
                ->prepare($query)
                ->execute($columns);

            while ($result->next()) {
                $grids[$result->id] = $result->title;
            }
        }
    }

    /**
     * Prepare the container by loading the grid and parse it as subcolumns definition.
     *
     * @param Grid $grid The grid object.
     *
     * @return array
     */
    protected function prepareContainer(Grid $grid)
    {
        $container = array();

        foreach ($grid->getColumns() as $column) {
            $container[] = array(implode(' ', $column));
        }

        return $container;
    }

    /**
     * Update the subcolumns definition.
     *
     * @param int    $gridId The grid id.
     * @param string $type   The subcolumns type.
     *
     * @return void
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    private function updateSubcolumnsDefinition($gridId, $type)
    {
        $grid = Factory::createById($gridId);

        $GLOBALS['TL_SUBCL'][static::$name]['sets'][$type] = $this->prepareContainer($grid);

        if ($grid->getRowClass()) {
            $GLOBALS['TL_SUBCL'][static::$name]['scclass'] = 'row ' . $grid->getRowClass();
        } else {
            $GLOBALS['TL_SUBCL'][static::$name]['scclass'] = 'row';
        }
    }
}
