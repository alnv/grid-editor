<?php

/**
 * @package   contao-bootstrap
 * @author    David Molineus <david.molineus@netzmacht.de>
 * @license   LGPL 3+
 * @copyright 2013-2015 netzmacht creative David Molineus
 */

namespace Netzmacht\Bootstrap\Grid\Builder;

/**
 * A grid column being built.
 *
 * @package Netzmacht\Bootstrap\Grid\Builder
 */
class Column
{
    const EXTRA_SMALL_DEVICES = 'xs';
    const SMALL_DEVICES       = 'sm';
    const MEDIUM_DEVICES      = 'md';
    const LARGE_DEVICES       = 'lg';

    const PULL = 'pull';
    const PUSH = 'push';

    /**
     * The column sizes.
     *
     * @var array
     */
    protected $sizes = array();

    /**
     * The grid builder.
     *
     * @var GridBuilder
     */
    protected $builder;

    /**
     * Construct.
     *
     * @param GridBuilder $builder The grid builder.
     */
    public function __construct(GridBuilder $builder)
    {
        $this->builder = $builder;
    }

    /**
     * Create a column for a device.
     *
     * @param string   $device The device name.
     * @param int      $width  The column width.
     * @param int|null $offset Optional offset.
     * @param int|null $push   Optional push.
     *
     * @return $this
     */
    public function forDevice($device, $width, $offset = null, $push = null)
    {
        $this->sizes[$device] = array('width' => $width, 'offset' => $offset, 'push' => $push);

        return $this;
    }

    /**
     * Get a size for a device.
     *
     * @param string $device The device.
     *
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
     * Remove size.
     *
     * @param string $device The device name.
     *
     * @return $this
     */
    public function removeSize($device)
    {
        unset($this->sizes[$device]);

        return $this;
    }

    /**
     * Build css classes.
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
     * Finish the column being build.
     *
     * @return GridBuilder
     */
    public function finish()
    {
        return $this->builder;
    }
}
