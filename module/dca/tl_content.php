<?php

/**
 * inject bootstrap column set definitions
 */
//$GLOBALS['TL_DCA']['tl_content']['config']['onload_callback'][] = array('Netzmacht\\ColumnSet\\ColumnSet', 'appendColumnsetIdToPalette');

$GLOBALS['TL_DCA']['tl_content']['palettes']['semantic_html5'] = str_replace(
	'sh5_additional',
	'sh5_additional,bootstrap_isGridRow,bootstrap_isGridColumn',
	$GLOBALS['TL_DCA']['tl_content']['palettes']['semantic_html5']
);


/**
 * fields
 */
$GLOBALS['TL_DCA']['tl_content']['fields']['sc_type']['options_callback'] = array('Netzmacht\\ColumnSet\\ColumnSet', 'getAllTypes');
$GLOBALS['TL_DCA']['tl_content']['fields']['sc_type']['eval']['submitOnChange'] = true;

$GLOBALS['TL_DCA']['tl_content']['fields']['columnset_id'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_content']['columnset_id'],
	'exclude'                 => true,
	'inputType'               => 'select',
	//'options_callback'        => array('Netzmacht\\ColumnSet\\ColumnSet', 'getAllColumnsets'),
	'reference'               => &$GLOBALS['TL_LANG']['tl_content'],
	'eval'                    => array('mandatory' => true, 'submitOnChange' => true, 'tl_class' => 'clr'),
	'sql'                     => "varchar(10) NOT NULL default ''"
);


$GLOBALS['TL_DCA']['tl_content']['fields']['bootstrap_isGridRow'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_content']['bootstrap_isGridRow'],
	'exclude'                 => true,
	'inputType'               => 'checkbox',
	'eval'                    => array('mandatory' => true, 'submitOnChange' => true, 'tl_class' => 'clr'),
	'sql'                     => "char(1) NOT NULL default ''"
);


$GLOBALS['TL_DCA']['tl_content']['fields']['bootstrap_isGridColumn'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_content']['bootstrap_isGridColumn'],
	'exclude'                 => true,
	'inputType'               => 'checkbox',
	'eval'                    => array('mandatory' => true, 'submitOnChange' => true, 'tl_class' => 'clr'),
	'sql'                     => "char(1) NOT NULL default ''"
);