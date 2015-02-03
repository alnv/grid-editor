<?php

/**
 * @package   contao-bootstrap
 * @author    David Molineus <david.molineus@netzmacht.de>
 * @license   LGPL 3+
 * @copyright 2013-2015 netzmacht creative David Molineus
 */

namespace Netzmacht\Bootstrap\Grid\Builder;

class Column
{
    const EXTRA_SMALL_DEVICES = 'xs';
    const SMALL_DEVICES       = 'sm';
    const MEDIUM_DEVICES      = 'md';
    const LARGE_DEVICES       = 'lg';

    const PULL = 'pull';
    const PUSH = 'push';

    /**
     * @var array
     */
    protected $sizes = array();

    /**
     * @var GridBuilder
     */
    protected $builder;

    /**
     * @param GridBuilder $builder
     */
    public function __construct(GridBuilder $builder)
    {
        $this->builder = $builder;
    }

    /**
     * @param $device
     * @param $width
     * @param null $offset
     * @param null $push
     * @return $this
     */
    public function forDevice($device, $width, $offset = null, $push = null)
    {
        $this->sizes[$device] = array('width' => $width, 'offset' => $offset, 'push' => $push);

        return $this;
    }

    /**
     * @param $device
     * @return null|array
     */
    public function getSize($device)
    {
        if (isset($this->sizes[$device])) {
            return $this->sizes[$device];
        }

        return null;
    }

    /**
     * @param $device
     * @return $this
     */
    public function removeSize($device)
    {
        unset($this->sizes[$device]);

        return $this;
    }

    /**
     * Build css classes
     *
     * @return array classes
     */
    public function build()
    {
        $classes = array();

        foreach ($this->sizes as $device => $size) {
            if ($size['width']) {
                $classes[] = sprintf('col-%s-%s', $device, $size['width']);
            } else {
                $classes[] = 'hidden-' . $device;
            }

            if ($size['offset']) {
                $classes[] = sprintf('col-%s-offset-%s', $device, $size['offset']);
            }

            if ($size['push'] !== null) {
                if (is_numeric($size['push'])) {
                    $push      = ($size['push'] < 0) ? 'pull' : 'push';
                    $classes[] = sprintf('col-%s-%s-%s', $device, $push, $size['push']);
                } else {
                    $classes[] = sprintf('col-%s-%s', $device, $size['push']);
                }
            }
        }

        return $classes;
    }

    /**
     * @return GridBuilder
     */
    public function finish()
    {
        return $this->builder;
    }
}
