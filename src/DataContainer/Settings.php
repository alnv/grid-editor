<?php
/**
 * Created by PhpStorm.
 * User: david
 * Date: 26.09.14
 * Time: 09:19
 */

namespace Netzmacht\Bootstrap\Grid\DataContainer;

class Settings
{
    /**
     * @param $value
     * @return int
     */
    public function forceInteger($value)
    {
        return (int) $value;
    }
}
