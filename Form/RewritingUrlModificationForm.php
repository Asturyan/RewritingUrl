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

use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\GreaterThan;
use Thelia\Core\Translation\Translator;

use Thelia\Form\BaseForm;

class RewritingUrlModificationForm extends BaseForm
{

    protected function buildForm($change_mode = false)
    {
        $url_constraints = array(new NotBlank());

        if (!$change_mode) {
            $url_constraints[] = new Callback(array(
                "methods" => array(array($this, "checkDuplicateUrl"))
            ));
        }
        
        $this->formBuilder
            ->add("id", "hidden", array(
                    "constraints" => array(
                        new GreaterThan(
                            array('value' => 0)
                        )
                    )
            ))
            ->add("view", "text", array(
                "constraints" => array(
                    new NotBlank()
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
                    new NotBlank()
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
        return "admin_rewriting_modification";
    }
}
