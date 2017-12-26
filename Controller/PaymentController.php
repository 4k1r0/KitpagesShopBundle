<?php

namespace Kitpages\ShopBundle\Controller;

use JMS\Payment\CoreBundle\Plugin\Exception\Action\VisitUrl;
use JMS\Payment\CoreBundle\Plugin\Exception\ActionRequiredException;
use JMS\Payment\CoreBundle\PluginController\Result;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Kitpages\ShopBundle\Entity\Order;
use Kitpages\ShopBundle\Entity\OrderHistory;
use Kitpages\ShopBundle\Entity\Invoice;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;


class PaymentController extends Controller
{
    
    public function completeAction($orderId)
    {
        if (!$this->get('security.authorization_checker')->isGranted('ROLE_SHOP_USER')) {
            throw $this->createAccessDeniedException();
        }
        
        $em = $this->getDoctrine()->getManager();
        
        $order = $em->getRepository("KitpagesShopBundle:Order")->find($orderId);
        
        $instruction = $order->getPaymentInstruction();
        if (null === $pendingTransaction = $instruction->getPendingTransaction()) {
            $payment = $this->get("payment.plugin_controller")
                ->createPayment($instruction->getId(), $instruction->getAmount() - $instruction->getDepositedAmount());
        } else {
            $payment = $pendingTransaction->getPayment();
        }
        
        $result =
            $this->get("payment.plugin_controller")->approveAndDeposit($payment->getId(), $payment->getTargetAmount());
        $logger = $this->get('logger');
        $logger->info("payment complete action");
        $logger->info("payment:" . $result->getStatus());
        if (Result::STATUS_PENDING === $result->getStatus()) {
            $ex = $result->getPluginException();
            
            if ($ex instanceof ActionRequiredException) {
                $action = $ex->getAction();
                
                if ($action instanceof VisitUrl) {
                    $logger->info("payment redirect");
                    
                    return new RedirectResponse($action->getUrl());
                }
                $logger->info("payment exception " . $ex->getMessage());
                throw $ex;
            }
        } else {
            if (Result::STATUS_SUCCESS !== $result->getStatus()) {
                $logger->info('Transaction was not successful: ' . $result->getReasonCode());
                throw new \RuntimeException('Transaction was not successful: ' . $result->getReasonCode());
            }
        }
        $logger->info("payment complete success");
        $displayOrderRoute = $this->container->getParameter('kitpages_shop.order_display_route_name');
        
        return new RedirectResponse($this->get('router')->generate($displayOrderRoute, [
            'orderId' => $order->getId(),
        ]));
        
    }
    
    public function cancelAction($orderId)
    {
        $displayOrderRoute = $this->container->getParameter('kitpages_shop.order_display_route_name');
        
        return $this->redirect(
            $this->generateUrl(
                $displayOrderRoute,
                ['orderId' => $orderId]
            )
        );
    }
    
}
