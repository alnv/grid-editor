<?php

/**
 * @package   contao-bootstrap
 * @author    David Molineus <david.molineus@netzmacht.de>
 * @license   LGPL 3+
 * @copyright 2013-2015 netzmacht creative David Molineus
 */

// inject columnset selector for subcolumns
if(\Netzmacht\Bootstrap\Grid\Integration\Subcolumns::isActive()) {
    $GLOBALS['TL_DCA']['tl_form_field']['config']['onload_callback'][] = array(
        'Netzmacht\Bootstrap\Grid\Integration\Subcolumns',
        'appendColumnsetIdToPalette'
    );

    $GLOBALS['TL_DCA']['tl_form_field']['fields']['fsc_type']['eval']['includeBlankOption'] = true;
    $GLOBALS['TL_DCA']['tl_form_field']['fields']['fsc_type']['eval']['submitOnChange']     = true;
    $GLOBALS['TL_DCA']['tl_form_field']['fields']['fsc_type']['options_callback']           = array(
        'Netzmacht\Bootstrap\Grid\DataContainer\ColumnSet',
        'getAllTypes'
    );
}

/**
 * fields
 */
$GLOBALS['TL_DCA']['tl_form_field']['fields']['bootstrap_grid'] = array
(
    'label'                   => &$GLOBALS['TL_LANG']['tl_form_field']['bootstrap_grid'],
    'exclude'                 => true,
    'inputType'               => 'select',
    'options_callback'        => array('Netzmacht\Bootstrap\Grid\DataContainer\ColumnSet', 'getGrids'),
    'reference'               => &$GLOBALS['TL_LANG']['tl_content'],
    'eval'                    => array(
        'mandatory'      => true,
        'submitOnChange' => true,
        'tl_class'       => 'w50'
    ),
    'sql'                     => "int(10) unsigned NOT NULL default '0'"
);
