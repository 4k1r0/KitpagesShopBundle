<?php
namespace Kitpages\ShopBundle\Model\Payment;

use JMS\Payment\CoreBundle\Form\ChoosePaymentMethodType;
use Kitpages\ShopBundle\Entity\Order;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Validator\Constraints\IsTrue;

class PaymentManager
{

    public function __construct(
        FormFactory $formFactory,
        Router $router,
        $paymentList
    )
    {
        $this->formFactory = $formFactory;
        $this->router = $router;
        $this->paymentList = $paymentList;
    }

    public function getChoosePaymentForm(Order $order)
    {
        foreach($this->paymentList as $paymentKey => $paymentParameterList) {
            $paymentData[$paymentKey] = array(
                'return_url' => $this->router->generate(
					$paymentParameterList['return_url'], 
					array('orderId' => $order->getId(),), 
					UrlGeneratorInterface::ABSOLUTE_URL
				),
                'cancel_url' => $this->router->generate(
					$paymentParameterList['cancel_url'], 
					array('orderId' => $order->getId(),), 
					UrlGeneratorInterface::ABSOLUTE_URL
				),
                'useraction' => 'commit',
            );
        }

        $form = $this->formFactory->create(ChoosePaymentMethodType::class, null, array(
            'amount'   => $order->getPriceIncludingVat(),
            'currency' => 'EUR',
            'predefined_data' => $paymentData
        ,
        ));
        $form->add(
            'systemTerms',
            CheckboxType::class,
            array(
                'required' => true,
                'value' => 'yes',
                'label' => ' ',
                'mapped' => false,
                'constraints' => new IsTrue()
            )
        );
        return $form;
    }



}