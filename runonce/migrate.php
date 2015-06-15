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
     * @var
     */
    private $previousErrorHandler;

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
        /*
         * Error handler of composer plugin is broken. use our own instead
         * @see #contao-community-alliance/composer-plugin/issues/14
         */
        $this->previousErrorHandler = set_error_handler(array($this, 'handleError'), E_ALL);

        $this->migrateFromLegacy();
        $this->migrateColumnResets();

        set_error_handler($this->previousErrorHandler);
    }

    /**
     * Handle errors properly as a workaround of the broken cca/composer-plugin error handler.
     *
     * @param int    $errno   Contains the level of the error raised, as an integer.
     * @param string $errstr  The error message.
     * @param string $errfile The file in which the error occured.
     * @param int    $errline The line in the file on which the error occured.
     *
     * @return void
     *
     * @throws \ErrorException Always, the error converted to an ErrorException.
     */
    public function handleError($errno, $errstr, $errfile, $errline)
    {
        if ($errno === E_NOTICE) {
            return;
        }

        throw new \ErrorException($errstr, 0, $errno, $errfile, $errline);
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
                $dbColumn = 'columnset_' . $size;

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
