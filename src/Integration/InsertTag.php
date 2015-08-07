<?php

/**
 * @package   contao-bootstrap
 * @author    David Molineus <david.molineus@netzmacht.de>
 * @license   LGPL 3+
 * @copyright 2013-2015 netzmacht creative David Molineus
 */

namespace Netzmacht\Bootstrap\Grid\Integration;

use Netzmacht\Bootstrap\Core\Event\ReplaceInsertTagsEvent;
use Netzmacht\Bootstrap\Grid\Factory;
use Netzmacht\Bootstrap\Grid\Walker;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class InsertTag provides an insert tag integration for the grids.
 *
 * Begin a grid by defining an identifier and the to using column set id.
 * {{grid::IDENTIFIER::begin::COLUMNSET_ID}}
 *
 * You can also pass the infinite flag. When enabling it you can build endless columns.
 * {{grid::IDENTIFIER::begin::COLUMNSET_ID::infinite}}
 *
 * Create a new column without the columnset id.
 * {{grid::IDENTIFIER}}
 *
 * Close the grid.
 * {{grid::IDENTIFIER::end}}
 *
 * @package Netzmacht\Bootstrap\Grid\Integration
 */
class InsertTag implements EventSubscriberInterface
{
    /**
     * The insert tag walkers.
     *
     * @var Walker[]
     */
    private static $walkers = array();

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            ReplaceInsertTagsEvent::NAME => 'parseInsertTag'
        );
    }

    /**
     * Rewrite grid insert tags so that the refresh flag is added.
     *
     * @param string $buffer The template buffer.
     *
     * @return string
     */
    public function hookOutputFrontendTemplate($buffer)
    {
        return preg_replace(
            '/(\{\{grid::.*)\}\}/Ui',
            '$1|refresh}}',
            $buffer
        );
    }

    /**
     * Parse the insert tag.
     *
     * @param ReplaceInsertTagsEvent $event The subscribed event.
     *
     * @return void
     */
    public function parseInsertTag(ReplaceInsertTagsEvent $event)
    {
        if ($event->getTag() != 'grid') {
            return;
        }

        if (TL_MODE !== 'FE') {
            $event->setHtml(sprintf('[[%s]]', $event->getRaw()));
            return;
        }

        $walker = $this->getWalker($event);

        if (!$walker) {
            return;
        }

        if ($event->getParam(3) === 'infinite' && !in_array($event->getParam(1), array('begin', 'end'))) {
            $event->setHtml($walker->column());
        } elseif (in_array($event->getParam(1), array('begin', 'walk', 'end'))) {
            $method = $event->getParam(1);
            $event->setHtml($walker->$method());
        } else {
            $event->setHtml($walker->column());
        }
    }

    /**
     * Get a walker for the subscribed event.
     *
     * @param ReplaceInsertTagsEvent $event The subscribed event.
     *
     * @return Walker|null
     */
    private function getWalker(ReplaceInsertTagsEvent $event)
    {
        $identifier = $event->getParam(0);

        if (!isset(static::$walkers[$identifier])) {
            list($columnSetId, $infinite) = $this->translateParams($event);

            try {
                static::$walkers[$identifier] = new Walker(Factory::createById($columnSetId), false, $infinite);
            } catch (\Exception $e) {
                return null;
            }
        }

        return static::$walkers[$identifier];
    }

    /**
     * Translate event params.
     *
     * @param ReplaceInsertTagsEvent $event The subscribed event.
     *
     * @return array
     */
    private function translateParams(ReplaceInsertTagsEvent $event)
    {
        $infinite    = false;
        $columnSetId = $event->getParam(0);

        if ($event->getParam(1) === 'begin' && $event->getParam(2)) {
            $columnSetId = $event->getParam(2);

            if ($event->getParam(3)) {
                $infinite = true;
            }
        }

        return array($columnSetId, $infinite);
    }
}
