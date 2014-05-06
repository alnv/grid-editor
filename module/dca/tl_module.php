<?php

$GLOBALS['TL_DCA']['tl_module']['config']['onload_callback'][] = array('Netzmacht\\ColumnSet\\ColumnSet', 'appendColumnsetIdToPalette');

$GLOBALS['TL_DCA']['tl_module']['fields']['sc_type']['options_callback'] = array('Netzmacht\\ColumnSet\\ColumnSet', 'getAllTypes');
$GLOBALS['TL_DCA']['tl_module']['fields']['sc_type']['eval']['submitOnChange'] = true;

$GLOBALS['TL_DCA']['tl_module']['fields']['sc_modules']['eval']['columnFields']['column']['options_callback'] = array('Netzmacht\\ColumnSet\\ColumnSet', 'getColumnsForModule');

$GLOBALS['TL_DCA']['tl_module']['fields']['columnset_id'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_module']['columnset_id'],
	'exclude'                 => true,
	'inputType'               => 'select',
	'options_callback'        => array('Netzmacht\\ColumnSet\\ColumnSet', 'getAllColumnsets'),
	'reference'               => &$GLOBALS['TL_LANG']['tl_content'],
	'eval'                    => array('mandatory' => true, 'submitOnChange' => true, 'tl_class' => 'clr'),
	'sql'                     => "varchar(10) NOT NULL default ''"
);