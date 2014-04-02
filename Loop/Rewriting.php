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

namespace RewritingUrl\Loop;

use Propel\Runtime\ActiveQuery\Criteria;

use Thelia\Core\Template\Element\BaseLoop;
use Thelia\Core\Template\Element\LoopResult;
use Thelia\Core\Template\Element\LoopResultRow;
use Thelia\Core\Template\Element\PropelSearchLoopInterface;
use Thelia\Core\Template\Loop\Argument\ArgumentCollection;
use Thelia\Core\Template\Loop\Argument\Argument;
use Thelia\Type\TypeCollection;
use Thelia\Type;
use Thelia\Type\BooleanOrBothType;
use Thelia\Model\Base\LangQuery;
use Thelia\Model\RewritingUrlQuery;

/**
 *
 * Rewriting loop, all params available :

 * Class RewritingUrl
 * @package RewritingUrl\Loop
 * @author Marc LEMARCHAND <mlemarchand@hubchannel.fr>
 */
class Rewriting extends BaseLoop implements PropelSearchLoopInterface
{
    protected $timestampable = true;
    protected $default_views = array('product', 'category', 'content', 'folder');

    /**
     * @return ArgumentCollection
     */
    protected function getArgDefinitions()
    {
        return new ArgumentCollection(
            Argument::createIntListTypeArgument('id'),
            Argument::createIntTypeArgument('lang'),
            new Argument(
                'order',
                new TypeCollection(
                    new Type\EnumListType(array('id', 'id_reverse', 'view', 'view_reverse', 'view_id', 'view_id_reverse', 'url', 'url_reverse', 'redirected', 'redirected_reverse'))
                ),
                'id'
            )
        );
    }

    public function buildModelCriteria()
    {
        $search = RewritingUrlQuery::create();

        $id = $this->getId();

        if (!is_null($id)) {
            $search->filterById($id, Criteria::IN);
        }
        
        $lang = $this->getLang();
        
        if (!is_null($lang)) {
            $localeSearch = LangQuery::create()->findPk($lang);
            $search->filterByViewLocale($localeSearch->getLocale());
        }
        
        $orders  = $this->getOrder();

        foreach ($orders as $order) {
            switch ($order) {
                case 'id':
                    $search->orderById(Criteria::ASC);
                    break;
                case 'id_reverse':
                    $search->orderById(Criteria::DESC);
                    break;

                case 'view':
                    $search->orderByView(Criteria::ASC);
                    break;
                case 'view_reverse':
                    $search->orderByView(Criteria::DESC);
                    break;
                
                case 'view_id':
                    $search->orderByViewId(Criteria::ASC);
                    break;
                case 'view_id_reverse':
                    $search->orderByViewId(Criteria::DESC);
                    break;

                case 'url':
                    $search->orderByUrl(Criteria::ASC);
                    break;
                case 'url_reverse':
                    $search->orderByUrl(Criteria::DESC);
                    break;

                case 'redirected':
                    $search->orderByRedirected(Criteria::ASC);
                    break;
                case 'redirected_reverse':
                    $search->orderByRedirected(Criteria::DESC);
                    break;
            }
        }

        return $search;

    }

    public function parseResults(LoopResult $loopResult)
    {
        foreach ($loopResult->getResultDataCollection() as $rewrite) {

            $loopResultRow = new LoopResultRow($rewrite);
            $lang = LangQuery::create()->findOneByLocale($rewrite->getViewLocale());
            $loopResultRow
                ->set("ID"                      , $rewrite->getId())
                ->set("URL"                     , $rewrite->getUrl())
                ->set("VIEW_LOCALE"             , $rewrite->getViewLocale())
                ->set("VIEW_LOCALE_ID"          , $lang->getId())
                ->set("VIEW"                    , $rewrite->getView())
                ->set("VIEW_ID"                 , $rewrite->getViewId())
                ->set("CUSTOM"                  , !in_array($rewrite->getView(), $this->default_views)?TRUE:FALSE)
                ->set("REDIRECTED"              , $rewrite->getRedirected())
            ;

            $loopResult->addRow($loopResultRow);
        }

        return $loopResult;

    }
}
