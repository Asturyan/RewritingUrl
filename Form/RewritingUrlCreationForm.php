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

use Symfony\Component\Validator\Constraints;
use Symfony\Component\Validator\ExecutionContextInterface;
use Thelia\Core\Translation\Translator;
use Thelia\Model\RewritingUrlQuery;

use Thelia\Form\BaseForm;

class RewritingUrlCreationForm extends BaseForm
{
    protected function buildForm($change_mode = false)
    {
        $url_constraints = array(new Constraints\NotBlank());

        if (!$change_mode) {
            $url_constraints[] = new Constraints\Callback(array(
                "methods" => array(array($this, "checkDuplicateUrl"))
            ));
        }

        $this->formBuilder
            ->add("view", "text", array(
                "constraints" => array(
                    new Constraints\NotBlank()
                ),
                'label' => Translator::getInstance()->trans('View *'),
                'label_attr' => array(
                    'for' => 'view'
                )
            ))
            ->add("view_id", "text", array(
                "label" => Translator::getInstance()->trans('View ID'),
                "label_attr" => array(
                    "for" => "view_id"
                )
            ))
            ->add("url", "text", array(
                "constraints" => $url_constraints,
                "label" => Translator::getInstance()->trans('Url *'),
                "label_attr" => array(
                    "for" => "url"
                )
            ))
            ->add("view_locale", "hidden", array(
                "constraints" => array(
                    new Constraints\NotBlank()
                )
            ))
            ->add("redirected", "text", array(
                "label" => Translator::getInstance()->trans('Redirected'),
                "label_attr" => array(
                    "for" => "value"
                )
            ))
        ;
    }

    public function getName()
    {
        return "admin_rewriting_creation";
    }

    public function checkDuplicateUrl($value, ExecutionContextInterface $context)
    {
        $rewriting = RewritingUrlQuery::create()->findOneByUrl($value);

        if ($rewriting) {
            $context->addViolation(Translator::getInstance()->trans('A rewriting rule with url "%url" already exists.', array('%url' => $value)));
        }
    }

}
