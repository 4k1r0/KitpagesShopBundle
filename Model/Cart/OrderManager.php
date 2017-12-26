<?php
namespace Kitpages\ShopBundle\Model\Cart;

use JMS\Payment\CoreBundle\Model\PaymentInstructionInterface;
use JMS\Payment\CoreBundle\Model\PaymentInterface;
use JMS\Payment\CoreBundle\PluginController\Event\PaymentStateChangeEvent;
use Kitpages\ShopBundle\Entity\Order;
use Kitpages\ShopBundle\Entity\OrderHistory;
use Kitpages\ShopBundle\Entity\OrderLine;
use Kitpages\ShopBundle\Entity\OrderUser;
use Kitpages\ShopBundle\Entity\Invoice;
use Kitpages\ShopBundle\Model\Cart\CartInterface;
use Kitpages\ShopBundle\Event\ShopEvent;
use Kitpages\ShopBundle\KitpagesShopEvents;
use Kitpages\ShopBundle\Model\Cart\ProductInterface;
use Kitpages\ShopBundle\Model\Discount\DiscountInterface;

use Kitano\PaymentBundle\Event\PaymentEvent;
use Kitano\PaymentBundle\Entity\Transaction;

use Symfony\Bridge\Monolog\Logger;
use Symfony\Component\HttpFoundation\Session;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Templating\EngineInterface;


class OrderManager
{
    protected $isCartIncludingVat = null;
    protected $cartManager = null;
    protected $doctrine = null;
    /** @var null|LoggerInterface */
    protected $logger = null;

    /** @var null|\Symfony\Component\Templating\EngineInterface */
    protected $templating = null;

    /** @var null|\Symfony\Component\EventDispatcher\EventDispatcherInterface */
    protected $dispatcher = null;

    public function __construct(
        Registry $doctrine,
        Logger $logger,
        CartManagerInterface $cartManager,
        $isCartIncludingVat,
        EngineInterface $templating,
        EventDispatcherInterface $dispatcher
    )
    {
        $this->doctrine = $doctrine;
        $this->logger = $logger;
        $this->cartManager = $cartManager;
        $this->isCartIncludingVat = $isCartIncludingVat;
        $this->templating = $templating;
        $this->dispatcher = $dispatcher;
    }


    /**
     * @param string username ($this->get('security.token_storage')->getUsername();)
     * @param OrderUser|null $invoiceUser
     * @param OrderUser|null $shippingUser
     * @return Order $order
     */
    public function createOrder(
        $username = null,
        OrderUser $invoiceUser = null,
        OrderUser $shippingUser = null

    )
    {
        $this->logger->debug("in create order");
        $cart = $this->cartManager->getCart();
        $price = $this->cartManager->getTotalPrice();

        // create order
        $order = new Order();
        $this->setOrderPrice($order, $price);
        $order->setRandomKey($this->getNewRandomKey());

        // create first orderHistory
        $orderHistory = new OrderHistory();
        $orderHistory->setUsername($username);
        $orderHistory->setOrder($order);
        $orderHistory->setState(OrderHistory::STATE_CREATED);
        $orderHistory->setStateDate(new \DateTime());
        $orderHistory->setPriceIncludingVat($order->getPriceIncludingVat());
        $orderHistory->setPriceWithoutVat($order->getPriceWithoutVat());
        $order->addOrderHistory($orderHistory);
        $order->setStateFromHistory();

        // create lines
        $lineList = $cart->getLineList();
        foreach ($lineList as $line)
        {
            $orderLine = new OrderLine();
            $orderLine->setOrder($order);
            $orderLine->setCartLineId($line->getId());
            $orderLine->setCartParentLineId($line->getParentLineId());
            $orderLine->setQuantity($line->getQuantity());
            $orderLine->setShopCategory($line->getCartable()->getShopCategory());
            $orderLine->setShopName($line->getCartable()->getShopName());
            $orderLine->setShopDescription($line->getCartable()->getShopDescription());
            $orderLine->setShopData($line->getCartable()->getShopData());
            $orderLine->setShopReference($line->getCartable()->getShopReference());

            if ($line->getCartable() instanceof ProductInterface) {
                $orderLine->setCartableClass('product');
            } elseif ($line->getCartable() instanceof DiscountInterface) {
                $orderLine->setCartableClass('discount');
            } else {
                $orderLine->setCartableClass('other');
            }

            if ($this->isCartIncludingVat) {
                $orderLine->setPriceIncludingVat($this->cartManager->getLinePrice($line->getId()));
            } else {
                $orderLine->setPriceWithoutVat($this->cartManager->getLinePrice($line->getId()));
            }
            $order->addOrderLine($orderLine);
        }

        // add users
        if (!is_null($invoiceUser)) {
            $order->setInvoiceUser($invoiceUser);
        }
        if (!is_null($shippingUser)) {
            $order->setShippingUser($shippingUser);
        }

        return $order;
    }

    public function addVat(Order $order)
    {
        $invoiceUser = $order->getInvoiceUser();
        if (! ($invoiceUser instanceof OrderUser) ) {
            $hasVat = false;
        } elseif ($invoiceUser->getCountryCode() == null) {
            $hasVat = false;
        } else {
            $countryCode = $invoiceUser->getCountryCode();
            $hasVat = true;
        }

        if ($hasVat) {
            $vat = $this->cartManager->getTotalVat($countryCode);
        } else {
            $vat = 0;
        }
        if ($this->isCartIncludingVat) {
            $order->setPriceWithoutVat($order->getPriceIncludingVat() - $vat);
        }
        else {
            $order->setPriceIncludingVat($order->getPriceWithoutVat() + $vat);
        }
        foreach ($order->getOrderLineList() as $orderLine)
        {
            if ($hasVat) {
                $vat = $this->cartManager->getLineVat($orderLine->getCartLineId(), $countryCode);
            } else {
                $vat = 0;
            }

            if ($this->isCartIncludingVat) {
                $orderLine->setPriceWithoutVat($orderLine->getPriceIncludingVat() - $vat );
            } else {
                $orderLine->setPriceIncludingVat($orderLine->getPriceWithoutVat() + $vat );
            }
        }

    }

    protected function getNewRandomKey() {
        $em = $this->doctrine->getManager();
        $repo = $em->getRepository("KitpagesShopBundle:Order");
        $keyExists = true;
        while ($keyExists == true ) {
            $key = uniqid("order-", true);
            $order = $repo->findOneBy(array('randomKey' => $key));
            if ($order == null) {
                $keyExists = false;
            }
        }
        return $key;
    }

    protected function setOrderPrice(Order $order, $price)
    {
        if ($this->isCartIncludingVat) {
            $order->setPriceIncludingVat($price);
        }
        else {
            $order->setPriceWithoutVat($price);
        }
    }

    public function paymentListener(PaymentStateChangeEvent $event)
    {
        $payment = $event->getPayment();
        if (!($payment instanceof PaymentInterface)) {
            return;
        }
        $paymentInstruction = $payment->getPaymentInstruction();
        if (!($paymentInstruction instanceof PaymentInstructionInterface)) {
            return;
        }
        // get order
        $em = $this->doctrine->getManager();
        $repo = $em->getRepository("KitpagesShopBundle:Order");
        $order = $repo->findOneBy(array('paymentInstruction' => $paymentInstruction));
        if (!$order instanceof Order) {
            throw new \Exception("paymentListener : unknown order for paymentInstructionId=" . $paymentInstruction->getId());
        }

        if ($order->getState() == OrderHistory::STATE_PAYED) {
            $this->cartManager->getCart()->emptyCart();
        }

        if ($order->getState() != OrderHistory::STATE_READY_TO_PAY) {
            $this->logger->info("paymentListener : orderId=" . $order->getId() . " not updated by payment process because state is not ready_to_pay");
            return;
        }
        // check transaction success
        if ($payment->getState() === PaymentInterface::STATE_DEPOSITED) {

            // update order
            $orderHistory = new OrderHistory();
            $orderHistory->setUsername("payment-notification");
            $orderHistory->setOrder($order);
            $orderHistory->setState(OrderHistory::STATE_PAYED);
            $orderHistory->setStateDate(new \DateTime());
            $orderHistory->setPriceIncludingVat($order->getPriceIncludingVat());
            $orderHistory->setPriceWithoutVat($order->getPriceWithoutVat());
            $orderHistory->setNote("Transaction accepted by the bank, paymentId=" . $payment->getId());
            $order->addOrderHistory($orderHistory);
            $em->flush(); // hack to have the historyId
            $order->setStateFromHistory();
            $em->flush();
            // empty cart
            $this->cartManager->getCart()->emptyCart();
            // generate invoice, id generation
            $invoice = new Invoice();
            $invoice->setOrder($order);
            $invoice->setReference(null);
            $order->setInvoice($invoice);
            $em->persist($invoice);
            $em->flush();
            // invoice generation step 2 (html and reference generation)
            $invoice->setReference('I-' . $invoice->getId());
            $invoiceHtmlContent = $this->templating->render(
                "KitpagesShopBundle:Invoice:table.html.twig",
                array(
                    "order" => $order,
                    "payment" => $payment,
                    "invoice" => $invoice
                )
            );
            $invoice->setContentHtml($invoiceHtmlContent);
            $em->flush();

            $event = new ShopEvent();
            $event->set("payment", $payment);
            $event->set("order", $order);
            $event->set("invoice", $invoice);
            $event->set("orderHistory", $orderHistory);
            $this->dispatcher->dispatch(KitpagesShopEvents::AFTER_ORDER_PAYED, $event);
        }

        // transaction status ok
        if ($payment->getState() === PaymentInterface::STATE_CANCELED) {
            // update order
            $orderHistory = new OrderHistory();
            $orderHistory->setUsername("payment-notification");
            $orderHistory->setOrder($order);
            $orderHistory->setState(OrderHistory::STATE_CANCELED);
            $orderHistory->setStateDate(new \DateTime());
            $orderHistory->setPriceIncludingVat($order->getPriceIncludingVat());
            $orderHistory->setPriceWithoutVat($order->getPriceWithoutVat());
            $orderHistory->setNote("Transaction canceled by the user, paymentId=" . $payment->getId());
            $order->addOrderHistory($orderHistory);
            $em->flush(); // hack to have the historyId
            $order->setStateFromHistory();
            $em->flush();

            $event = new ShopEvent();
            $event->set("payment", $payment);
            $event->set("order", $order);
            $event->set("orderHistory", $orderHistory);
            $this->dispatcher->dispatch(KitpagesShopEvents::AFTER_ORDER_CANCELED, $event);
        }

        // transaction status ok
        if ($payment->getState() === PaymentInterface::STATE_FAILED) {
            $orderHistory = new OrderHistory();
            $orderHistory->setUsername("payment-notification");
            $orderHistory->setOrder($order);
            $orderHistory->setState(OrderHistory::STATE_READY_TO_PAY);
            $orderHistory->setStateDate(new \DateTime());
            $orderHistory->setPriceIncludingVat($order->getPriceIncludingVat());
            $orderHistory->setPriceWithoutVat($order->getPriceWithoutVat());
            $orderHistory->setNote("Payment refused by the bank, paymentId=" . $payment->getId() . ", paymentState=" . $payment->getState());
            $order->addOrderHistory($orderHistory);
            $order->setStateFromHistory();
            $em->flush();

            $event = new ShopEvent();
            $event->set("payment", $payment);
            $event->set("order", $order);
            $event->set("orderHistory", $orderHistory);
            $this->dispatcher->dispatch(KitpagesShopEvents::AFTER_TRANSACTION_REFUSED, $event);
        }

        // transaction status ok
        if ($payment->getState() === PaymentInterface::STATE_EXPIRED) {
            $orderHistory = new OrderHistory();
            $orderHistory->setUsername("payment-notification");
            $orderHistory->setOrder($order);
            $orderHistory->setState(OrderHistory::STATE_READY_TO_PAY);
            $orderHistory->setStateDate(new \DateTime());
            $orderHistory->setPriceIncludingVat($order->getPriceIncludingVat());
            $orderHistory->setPriceWithoutVat($order->getPriceWithoutVat());
            $orderHistory->setNote("Payment expired by the bank, paymentId=" . $payment->getId() . ", paymentState=" . $payment->getState());
            $order->addOrderHistory($orderHistory);
            $order->setStateFromHistory();
            $em->flush();

            $event = new ShopEvent();
            $event->set("payment", $payment);
            $event->set("order", $order);
            $event->set("orderHistory", $orderHistory);
            $this->dispatcher->dispatch(KitpagesShopEvents::AFTER_TRANSACTION_EXPIRED, $event);
        }

    }
}
