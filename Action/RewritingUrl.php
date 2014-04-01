<?php
/*************************************************************************************/
/*                                                                                   */
/*      HubChannel	                                                             */
/*                                                                                   */
/*      Copyright (c) HubChannel                                                     */
/*      email : mlemarchand@hubchannel.fr                                            */
/*      web : http://www.hubchannel.fr                                               */
/*                                                                                   */
/*      This program is free software; you can redistribute it and/or modify         */
/*      it under the terms of the GNU General Public License as published by         */
/*      the Free Software Foundation; either version 3 of the License                */
/*                                                                                   */
/*      This program is distributed in the hope that it will be useful,              */
/*      but WITHOUT ANY WARRANTY; without even the implied warranty of               */
/*      MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the                */
/*      GNU General Public License for more details.                                 */
/*                                                                                   */
/*      You should have received a copy of the GNU General Public License            */
/*	    along with this program. If not, see <http://www.gnu.org/licenses/>.     */
/*                                                                                   */
/*************************************************************************************/

namespace RewritingUrl\Action;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use RewritingUrl\Event\RewritingUrlCreateEvent;
use RewritingUrl\Event\RewritingUrlDeleteEvent;
use RewritingUrl\Event\RewritingUrlUpdateEvent;
use RewritingUrl\Event\RewritingUrlEvent;

use Thelia\Action\BaseAction;
use Thelia\Model\RewritingUrl as RewritingUrlModel;
use Thelia\Model\RewritingUrlQuery;

class RewritingUrl extends BaseAction implements EventSubscriberInterface
{
    protected $default_views = array('product', 'category', 'content', 'folder');
    
    /**
     * Create a new rewriting entry
     *
     * @param \RewritingUrl\Event\RewritingUrlCreateEvent $event
     */
    public function create(RewritingUrlCreateEvent $event)
    {
        $rewriting = new RewritingUrlModel();

        $rewriting
            ->setView($event->getView())
            ->setViewId($event->getViewId())
            ->setViewLocale($event->getViewLocale())
            ->setUrl($event->getUrl())
            ->setRedirected($event->getRedirected())
        ->save();
        
        $event->setRewritingUrl($rewriting);
    }

    /**
     * Change a rewriting entry
     *
     * @param \RewritingUrl\Event\RewritingUrlUpdateEvent $event
     */
    public function modify(RewritingUrlUpdateEvent $event)
    {
        if (null !== $rewriting = RewritingUrlQuery::create()->findPk($event->getRewritingUrlId())) {
            
            $rewriting
                ->setRedirected($event->getRedirected()?$event->getRedirected():NULL)
                ->setView($event->getView() !== NULL?$event->getView():$rewriting->getView())
                ->setViewId($event->getViewId() !== NULL?$event->getViewId():$rewriting->getViewId())
                ->setViewLocale($event->getViewLocale())
                ->setUrl($event->getUrl())
            ->save();

            $event->setRewritingUrl($rewriting);
        }
    }

    /**
     * Delete a rewriting entry
     *
     * @param \RewritingUrl\Event\RewritingUrlDeleteEvent $event
     */
    public function delete(RewritingUrlDeleteEvent $event)
    {

        if (null !== ($rewriting = RewritingUrlQuery::create()->findPk($event->getRewritingUrlId()))) {

            if (!in_array($rewriting->getView(), $this->default_views)) {

                $rewriting->delete();

                $event->setRewritingUrl($rewriting);
            }
        }
    }

    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
                RewritingUrlEvent::REWRITING_CREATE => array(
                    "create", 128
                ), RewritingUrlEvent::REWRITING_UPDATE => array(
                    "modify", 128
                ), RewritingUrlEvent::REWRITING_DELETE => array(
                    "delete", 128
                ),
        );
    }
}
