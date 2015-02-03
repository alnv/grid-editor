<?php

/**
 * @package   contao-bootstrap
 * @author    David Molineus <david.molineus@netzmacht.de>
 * @license   LGPL 3+
 * @copyright 2013-2015 netzmacht creative David Molineus
 */

namespace Netzmacht\Bootstrap\Grid\DataContainer;

/**
 * Class Settings data container.
 *
 * @package Netzmacht\Bootstrap\Grid\DataContainer
 */
class Settings
{
    /**
     * Force an integer value.
     *
     * @param mixed $value Given value.
     *
     * @return int
     */
    public function forceInteger($value)
    {
        return (int) $value;
    }
}
