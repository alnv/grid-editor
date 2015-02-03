<?php

/**
 * @package   contao-bootstrap
 * @author    David Molineus <david.molineus@netzmacht.de>
 * @license   LGPL 3+
 * @copyright 2013-2015 netzmacht creative David Molineus
 */

$GLOBALS['TL_DCA']['tl_module']['config']['onload_callback'][] = array('Netzmacht\Bootstrap\Grid\DataContainer\ColumnSet', 'appendColumnsetIdToPalette');

$GLOBALS['TL_DCA']['tl_module']['fields']['sc_type']['options_callback'] = array('Netzmacht\Bootstrap\Grid\DataContainer\ColumnSet', 'getAllTypes');
$GLOBALS['TL_DCA']['tl_module']['fields']['sc_type']['eval']['submitOnChange'] = true;

$GLOBALS['TL_DCA']['tl_module']['fields']['sc_modules']['eval']['columnFields']['column']['options_callback'] = array('Netzmacht\Bootstrap\Grid\DataContainer\ColumnSet', 'getColumnsForModule');

$GLOBALS['TL_DCA']['tl_module']['fields']['columnset_id'] = array
(
    'label'                   => &$GLOBALS['TL_LANG']['tl_module']['columnset_id'],
    'exclude'                 => true,
    'inputType'               => 'select',
    'options_callback'        => array('Netzmacht\Bootstrap\Grid\DataContainer\ColumnSet', 'getGrids'),
    'reference'               => &$GLOBALS['TL_LANG']['tl_content'],
    'eval'                    => array('mandatory' => true, 'submitOnChange' => true, 'tl_class' => 'clr'),
    'sql'                     => "int(10) unsigned NOT NULL default '0'"
);
