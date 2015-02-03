<?php

/**
 * @package   contao-bootstrap
 * @author    David Molineus <david.molineus@netzmacht.de>
 * @license   LGPL 3+
 * @copyright 2013-2015 netzmacht creative David Molineus
 */

\MetaPalettes::appendFields('tl_settings', 'bootstrap', array('bootstrap_gridColumns'));


$GLOBALS['TL_DCA']['tl_settings']['fields']['bootstrap_gridColumns'] = array
(
    'inputType'     => 'text',
    'label'         => &$GLOBALS['TL_LANG']['tl_settings']['bootstrap_gridColumns'],
    'default'       => 12,
    'save_callback' => array
    (
        array('Netzmacht\Bootstrap\Grid\DataContainer\Settings', 'forceInteger'),
    ),
    'eval'          => array
    (
        'tl_class' => 'w50',
        'rgxp'     => 'digit',
    ),
);
