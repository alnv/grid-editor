<?php

/**
 * @package   contao-bootstrap
 * @author    David Molineus <david.molineus@netzmacht.de>
 * @license   LGPL 3+
 * @copyright 2013-2015 netzmacht creative David Molineus
 */

namespace Netzmacht\Bootstrap\Grid;

class migrate
{
    public function run()
    {
        $database = \Database::getInstance();

        if (!$database->fieldExists('bootstrap_grid', 'tl_content')
            && $database->fieldExists('columnset_id', 'tl_content')) {
            $database->query('ALTER TABLE tl_content ADD bootstrap_grid int(10) unsigned NOT NULL default \'0\';');
            $database->query('UPDATE tl_content SET bootstrap_grid=columnset_id WHERE bootstrap_grid = 0');
        }
    }
}

$migrate = new migrate();
$migrate->run();
