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

class RewritingUrlCreateEvent extends RewritingUrlEvent
{
    protected $url;
    protected $view_locale;
    protected $view;
    protected $view_id;
    protected $redirected;

    public function getUrl()
    {
        return $this->url;
    }

    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    public function getViewLocale()
    {
        return $this->view_locale;
    }

    public function setViewLocale($view_locale)
    {
        $this->view_locale = $view_locale;

        return $this;
    }

    public function getView()
    {
        return $this->view;
    }

    public function setView($view)
    {
        $this->view = $view;

        return $this;
    }

    public function getViewId()
    {
        return $this->view_id;
    }

    public function setViewId($view_id)
    {
        $this->view_id = $view_id;

        return $this;
    }

    public function getRedirected()
    {
        return $this->redirected;
    }

    public function setRedirected($redirected)
    {
        $this->redirected = $redirected;

        return $this;
    }
}
