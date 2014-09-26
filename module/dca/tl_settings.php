<?php

\MetaPalettes::appendFields('tl_settings', 'bootstrap', array('bootstrap_gridColumns'));


$GLOBALS['TL_DCA']['tl_settings']['fields']['bootstrap_gridColumns'] = array
(
    'inputType' => 'text',
    'label' => &$GLOBALS['TL_LANG']['tl_settings']['bootstrap_gridColumns'],
    'default' => 12,
    'eval' => array
    (
        'tl_class' => 'w50',
        'rgxp' => 'digit',
    ),
);