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

namespace RewritingUrl\Event;
use Thelia\Core\Event\ActionEvent;
use Thelia\Model\RewritingUrl;

class RewritingUrlEvent extends ActionEvent
{
    protected $rewriting = null;
    
    const REWRITING_CREATE            = 'rewriting.action.create';
    const REWRITING_UPDATE            = 'rewriting.action.update';
    const REWRITING_DELETE            = 'rewriting.action.delete';

    public function __construct(RewritingUrl $rewriting = null)
    {
        $this->rewriting = $rewriting;
    }

    public function hasRewritingUrl()
    {
        return ! is_null($this->rewriting);
    }

    public function getRewritingUrl()
    {
        return $this->rewriting;
    }

    public function setRewritingUrl($rewriting)
    {
        $this->rewriting = $rewriting;

        return $this;
    }
}
