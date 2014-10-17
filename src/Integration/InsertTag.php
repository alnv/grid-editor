<?php

/**
 * @package    contao-bootstrap
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Bootstrap\Grid\Integration;


use Netzmacht\Bootstrap\Core\Event\ReplaceInsertTagsEvent;
use Netzmacht\Bootstrap\Grid\Grid;
use Netzmacht\Bootstrap\Grid\Walker;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class InsertTag provides an insert tag integration for the grids
 *
 * {{grid::IDENTIFIER::start::COLUMNSET_ID}}
 * {{grid::IDENTIFIER}}
 * {{grid::IDENTIFIER::end}}
 * @package Netzmacht\Bootstrap\Grid\Integration
 */
class InsertTag implements EventSubscriberInterface
{
    /**
     * @var Walker[]
     */
    private static $walkers=array();

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return array(
            ReplaceInsertTagsEvent::NAME => 'parseInsertTag'
        );
    }

    /**
     * @param ReplaceInsertTagsEvent $event
     */
    public function parseInsertTag(ReplaceInsertTagsEvent $event)
    {
        if ($event->getTag() != 'grid') {
            return;
        }

        $columnSetId = $this->getColumnSetId($event);
        $walker      = $this->getWalker($event->getParam(0), $columnSetId);

        if (!$walker) {
            // TODO: DEBUG Message
            return;
        }

        if (in_array($event->getParam(1), array('start', 'end'))) {
            $method = $event->getParam(1);
            $event->setHtml($walker->$method());
        } else {
          $event->setHtml($walker->column());
        }
    }

    /**
     * @param string $identifier
     * @param int $columnSetId
     * @return Walker|null
     */
    private function getWalker($identifier, $columnSetId)
    {
        if (!isset(static::$walkers[$identifier])) {
            try {
                $grid = Grid::loadFromDatabase($columnSetId);
                static::$walkers[$identifier] = new Walker($grid);
            }
            catch (\Exception $e) {
                return null;
            }
        }

        return static::$walkers[$identifier];
    }

    /**
     * @param ReplaceInsertTagsEvent $event
     * @return null|string
     */
    private function getColumnSetId(ReplaceInsertTagsEvent $event)
    {
        $columnSetId = $event->getParam(0);

        if ($event->getParam(1) == 'start' && $event->getParam(2)) {
            $columnSetId = $event->getParam(2);

            return $columnSetId;
        }

        return $columnSetId;
    }

} 