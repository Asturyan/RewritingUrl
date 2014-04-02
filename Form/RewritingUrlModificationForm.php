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

namespace RewritingUrl\Form;

use Propel\Runtime\ActiveQuery\Criteria;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\GreaterThan;
use Symfony\Component\Validator\ExecutionContextInterface;
use Thelia\Core\Translation\Translator;
use Thelia\Model\RewritingUrlQuery;
use Thelia\Model\Base\LangQuery;

class RewritingUrlModificationForm extends RewritingUrlCreationForm
{

    protected function buildForm()
    {
        parent::buildForm();
        $this->formBuilder
            ->add("id", "hidden", array(
                    "constraints" => array(
                        new GreaterThan(
                            array('value' => 0)
                        )
                    )
            ))
            ->remove('view_locale')
            ->add("view_locale", "choice", array(
                'choices'   => LangQuery::create()->orderById()->find()->toKeyValue('locale', 'title'),
                "constraints" => array(
                    new NotBlank()
                ),
                "label" => Translator::getInstance()->trans('View Locale'),
                "label_attr" => array(
                    "for" => "view_locale"
                )
            ))
         ;

    }
    
    public function checkDuplicateUrl($value, ExecutionContextInterface $context)
    {
        $data = $context->getRoot()->getData();

        $rewriting = RewritingUrlQuery::create()
                ->filterById($data['id'], Criteria::NOT_IN)
                ->filterByViewLocale($data['view_locale'])
                ->filterByUrl($value)
                ->findOne();
       
        if ($rewriting) {
            $context->addViolation(Translator::getInstance()->trans('A rewriting rule with url "%url" already exists.', array('%url' => $value)));
        }
    }

    public function getName()
    {
        return "admin_rewriting_modification";
    }
}
