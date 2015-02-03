<?php

/**
 * @package   contao-bootstrap
 * @author    David Molineus <david.molineus@netzmacht.de>
 * @license   LGPL 3+
 * @copyright 2013-2015 netzmacht creative David Molineus
 */


// backend modukle
$GLOBALS['BE_MOD']['design']['columnset'] = array(
    'icon' => 'system/modules/bootstrap-grid/assets/icon.png',
    'tables' => array('tl_columnset'),
);


// integrations
$GLOBALS['TL_HOOKS']['outputFrontendTemplate'][] = array(
    'Netzmacht\Bootstrap\Grid\Integration\InsertTag',
    'hookOutputFrontendTemplate'
);

$GLOBALS['TL_EVENT_SUBSCRIBERS'][] = 'Netzmacht\Bootstrap\Grid\Integration\InsertTag';

\Netzmacht\Bootstrap\Grid\Integration\Subcolumns::setUp();
\Netzmacht\Bootstrap\Grid\Integration\SemanticHtml5::setUp();


// add separator for colsetPart. It's missing by subcolumns
$GLOBALS['TL_WRAPPERS']['separator'][] = 'colsetPart';

if(!isset($GLOBALS['TL_CONFIG']['bootstrap_gridColumns'])) {
    $GLOBALS['TL_CONFIG']['bootstrap_gridColumns'] = 12;
}

// subcolumns columnset definition
$GLOBALS['TL_SUBCL']['bootstrap_customizable'] = array
(
    'label'        => 'Bootstrap 3 (customizable)', // Label for the selectmenu
    'scclass'     => 'row', // Class for the wrapping container
    'inside'     => false, // Are inside containers used?
    'gap'         => false, // A gap between the columns can be entered in backend
    'sets'        => array( // provide default column sets as fallback if an database entry is deleted
        '1' => array(
            array('col-lg-12'),
        ),
        '2' => array(
            array('col-lg-6'),
            array('col-lg-6'),
        ),
        '3' => array(
            array('col-lg-4'),
            array('col-lg-4'),
            array('col-lg-4'),
        ),
        '4' => array(
            array('col-lg-3'),
            array('col-lg-3'),
            array('col-lg-3'),
            array('col-lg-3'),
        ),
        '5' => array(
            array('col-lg-3'),
            array('col-lg-3'),
            array('col-lg-2'),
            array('col-lg-2'),
            array('col-lg-2'),
        ),
        '6' => array(
            array('col-lg-2'),
            array('col-lg-2'),
            array('col-lg-2'),
            array('col-lg-2'),
            array('col-lg-2'),
            array('col-lg-2'),
        ),
        '7' => array(
            array('col-lg-2'),
            array('col-lg-2'),
            array('col-lg-2'),
            array('col-lg-2'),
            array('col-lg-2'),
            array('col-lg-1'),
            array('col-lg-1'),
        ),
        '8' => array(
            array('col-lg-2'),
            array('col-lg-2'),
            array('col-lg-2'),
            array('col-lg-2'),
            array('col-lg-1'),
            array('col-lg-1'),
            array('col-lg-1'),
            array('col-lg-1'),
        ),
        '9' => array(
            array('col-lg-2'),
            array('col-lg-2'),
            array('col-lg-2'),
            array('col-lg-1'),
            array('col-lg-1'),
            array('col-lg-1'),
            array('col-lg-1'),
            array('col-lg-1'),
            array('col-lg-1'),
        ),
        '10' => array(
            array('col-lg-2'),
            array('col-lg-2'),
            array('col-lg-1'),
            array('col-lg-1'),
            array('col-lg-1'),
            array('col-lg-1'),
            array('col-lg-1'),
            array('col-lg-1'),
            array('col-lg-1'),
            array('col-lg-1'),
        ),
        '11' => array(
            array('col-lg-2'),
            array('col-lg-1'),
            array('col-lg-1'),
            array('col-lg-1'),
            array('col-lg-1'),
            array('col-lg-1'),
            array('col-lg-1'),
            array('col-lg-1'),
            array('col-lg-1'),
            array('col-lg-1'),
            array('col-lg-1'),
        ),
        '12' => array(
            array('col-lg-1'),
            array('col-lg-1'),
            array('col-lg-1'),
            array('col-lg-1'),
            array('col-lg-1'),
            array('col-lg-1'),
            array('col-lg-1'),
            array('col-lg-1'),
            array('col-lg-1'),
            array('col-lg-1'),
            array('col-lg-1'),
            array('col-lg-1'),
        ),
    ),
);
