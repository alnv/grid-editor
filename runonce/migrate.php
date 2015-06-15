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
    /**
     * The database connection.
     *
     * @var \Database
     */
    private $database;

    /**
     * Column sizes used for migration.
     *
     * @var array
     */
    private $sizes = array('xs', 'sm', 'md', 'lg');

    /**
     * Construct.
     */
    public function __construct()
    {
        $this->database = \Database::getInstance();
    }

    /**
     * Run the migration.
     *
     * @return void
     */
    public function run()
    {
        $this->migrateFromLegacy();
        $this->migrateColumnResets();
    }

    /**
     * Migrate form legacy column set editor.
     *
     * @return void
     */
    private function migrateFromLegacy()
    {
        if (!$this->database->fieldExists('bootstrap_grid', 'tl_content')
            && $this->database->fieldExists('columnset_id', 'tl_content')
        ) {
            $this->database->query(
                'ALTER TABLE tl_content ADD bootstrap_grid int(10) unsigned NOT NULL default \'0\';'
            );
            $this->database->query('UPDATE tl_content SET bootstrap_grid=columnset_id WHERE bootstrap_grid = 0');
        }
    }

    /**
     * Migrate column resets to new storage.
     *
     * @return void
     */
    private function migrateColumnResets()
    {
        if (!$this->database->fieldExists('clearfix', 'tl_columnset')) {
            return;
        }

        $result = $this->database->query('SELECT * FROM tl_columnset WHERE clearfix != \'\'');

        if ($result->numRows < 1) {
            return;
        }

        while ($result->next()) {
            $old    = deserialize($result->clearfix, true);
            $resets = array();
            $values = array();

            // Transform resets having size as key.
            foreach ($old as $reset) {
                foreach ($this->sizes as $size) {
                    if (!empty($reset[$size])) {
                        $resets[$size][] = (int) $resets['column'];
                    }
                }
            }

            // Check definitions
            foreach ($resets as $size => $columns) {
                $dbColumn   = 'columnset_' . $size;

                if (!isset($values[$dbColumn])) {
                    $values[$dbColumn] = deserialize($result->$dbColumn, true);
                }

                foreach ($values[$dbColumn] as $index => $config) {
                    // Column already exists, skip migration
                    if (array_key_exists('reset', $values[$dbColumn][$index])) {
                        continue;
                    }

                    $values[$dbColumn][$index]['reset'] = in_array($index, $columns);
                }
            }

            if ($values) {
                $this->database->prepare('UPDATE tl_columnset %s WHERE id=?')
                    ->set($values)
                    ->execute($result->id);
            }
        }
    }
}

$migrate = new migrate();
$migrate->run();
