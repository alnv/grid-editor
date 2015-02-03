<?php

/**
 * @package   contao-bootstrap
 * @author    David Molineus <david.molineus@netzmacht.de>
 * @license   LGPL 3+
 * @copyright 2013-2015 netzmacht creative David Molineus
 */

// inject columnset selector for subcolumns
if(\Netzmacht\Bootstrap\Grid\Integration\Subcolumns::isActive()) {
    $GLOBALS['TL_DCA']['tl_content']['config']['onload_callback'][] =
        array('Netzmacht\Bootstrap\Grid\Integration\Subcolumns', 'appendColumnsetIdToPalette');
}

// inject columnset selector for semantic html5
if(\Netzmacht\Bootstrap\Grid\Integration\SemanticHtml5::isActive()) {
    $GLOBALS['TL_DCA']['tl_content']['config']['palettes_callback'][] =
        array('Netzmacht\Bootstrap\Grid\Integration\SemanticHtml5', 'callbackGeneratePalette');
}

$GLOBALS['TL_DCA']['tl_content']['metasubselectpalettes']['bootstrap_isGridElement'] = array(
    'row'    => array('bootstrap_grid'),
    'column' => array('bootstrap_gridRow'),
);


/**
 * fields
 */
$GLOBALS['TL_DCA']['tl_content']['fields']['sc_type']['options_callback'] = array('Netzmacht\Bootstrap\Grid\DataContainer\ColumnSet', 'getAllTypes');
$GLOBALS['TL_DCA']['tl_content']['fields']['sc_type']['eval']['submitOnChange'] = true;

$GLOBALS['TL_DCA']['tl_content']['fields']['bootstrap_grid'] = array
(
    'label'                   => &$GLOBALS['TL_LANG']['tl_content']['bootstrap_grid'],
    'exclude'                 => true,
    'inputType'               => 'select',
    'options_callback'        => array('Netzmacht\Bootstrap\Grid\DataContainer\ColumnSet', 'getGrids'),
    'reference'               => &$GLOBALS['TL_LANG']['tl_content'],
    'eval'                    => array(
        'mandatory' => true,
        'submitOnChange' => true,
        'tl_class' => 'w50'
    ),
    'sql'                     => "int(10) unsigned NOT NULL default '0'"
);


$GLOBALS['TL_DCA']['tl_content']['fields']['bootstrap_gridRow'] = array
(
    'label'                   => &$GLOBALS['TL_LANG']['tl_content']['bootstrap_gridRow'],
    'exclude'                 => true,
    'inputType'               => 'select',
    'options_callback'        => array('Netzmacht\Bootstrap\Grid\Integration\SemanticHtml5', 'getGridElements'),
    'eval'                    => array('mandatory' => true, 'submitOnChange' => true, 'tl_class' => 'w50'),
    'sql'                     => "int(10) unsigned NOT NULL default '0'"
);

$GLOBALS['TL_DCA']['tl_content']['fields']['bootstrap_isGridElement'] = array
(
    'label'                   => &$GLOBALS['TL_LANG']['tl_content']['bootstrap_isGridElement'],
    'exclude'                 => true,
    'inputType'               => 'select',
    'options'                 => array('row', 'column'),
    'reference'               => &$GLOBALS['TL_LANG']['tl_content']['bootstrap_gridElements'],
    'eval'                    => array(
        'submitOnChange' => true,
        'tl_class' => 'clr w50',
        'includeBlankOption' => true,
    ),
    'sql'                     => "varchar(8) NOT NULL default ''"
);


$GLOBALS['TL_DCA']['tl_content']['fields']['bootstrap_isGridColumn'] = array
(
    'label'                   => &$GLOBALS['TL_LANG']['tl_content']['bootstrap_isGridColumn'],
    'exclude'                 => true,
    'inputType'               => 'checkbox',
    'eval'                    => array('mandatory' => true, 'submitOnChange' => true, 'tl_class' => 'w50'),
    'sql'                     => "char(1) NOT NULL default ''"
);
