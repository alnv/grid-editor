<?php

/**
 * @package   contao-bootstrap
 * @author    David Molineus <david.molineus@netzmacht.de>
 * @license   LGPL 3+
 * @copyright 2013-2015 netzmacht creative David Molineus
 */

return array(
    'grid-editor' => array(
        'columns' => $GLOBALS['TL_CONFIG']['bootstrap_gridColumns'],
        'backend' => array(
            'replace-subcolumns-template' => true
        )
    ),
);
