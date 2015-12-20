<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2015 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 */


namespace Eccube\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class ShoppingType extends AbstractType
{

    public $app;

    public function __construct(\Eccube\Application $app)
    {
        $this->app = $app;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $app = $this->app;

        $builder
            ->add('payment', 'entity', array(
                'class' => 'Eccube\Entity\Payment',
                'choices' => array(),
                'expanded' => true,
                'constraints' => array(
                    new Assert\NotBlank(),
                ),
            ))
            ->add('message', 'textarea', array(
                'required' => false,
                'constraints' => array(
                    new Assert\Length(array('min' => 0, 'max' => 3000))),
            ))
            ->add('shippings', 'collection', array(
                'type' => 'shipping_item',
            ))
            ->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) use ($app) {
                /* @var \Eccube\Entity\Order $Order */
                $Order = $event->getData();

                $form = $event->getForm();
                $form->add('payment', 'entity', array(
                    'class' => 'Eccube\Entity\Payment',
                    'choices' => $app['eccube.repository.payment']->findByOrder($Order),
                    'expanded' => true,
                    'constraints' => array(
                        new Assert\NotBlank(),
                    ),
                ));
            })
            ->addEventSubscriber(new \Eccube\Event\FormEventSubscriber());
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Eccube\Entity\Order',
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'shopping';
    }
}
