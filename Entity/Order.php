<?php

namespace Kitpages\ShopBundle\Entity;

use JMS\Payment\CoreBundle\Entity\PaymentInstruction;

class Order
{
    ////
    // custom methods
    ////
    public function setStateFromHistory()
    {
        $historyList  = $this->getOrderHistoryList();
        $currentId    = null;
        $currentState = null;
        $currentDate  = null;
        foreach ($historyList as $history) {
            if (is_null($currentId) || $currentId < $history->getId()) {
                $currentId    = $history->getId();
                $currentState = $history->getState();
                $currentDate  = $history->getStateDate();
            }
        }
        $this->setState($currentState);
        $this->setStateDate($currentDate);
    }
    
    /**
     * @var JMS\Payment\CoreBundle\Entity\PaymentInstruction
     */
    private $paymentInstruction;
    
    /**
     * @var string $randomKey
     */
    private $randomKey;
    
    /**
     * @var string $state
     */
    private $state;
    
    /**
     * @var datetime $stateDate
     */
    private $stateDate;
    
    /**
     * @var float $priceWithoutVat
     */
    private $priceWithoutVat;
    
    /**
     * @var float $priceIncludingVat
     */
    private $priceIncludingVat;
    
    /**
     * @var datetime $createdAt
     */
    private $createdAt;
    
    /**
     * @var datetime $updatedAt
     */
    private $updatedAt;
    
    /**
     * @var integer $id
     */
    private $id;
    
    /**
     * @var Kitpages\ShopBundle\Entity\Invoice
     */
    private $invoice;
    
    /**
     * @var Kitpages\ShopBundle\Entity\OrderUser
     */
    private $invoiceUser;
    
    /**
     * @var Kitpages\ShopBundle\Entity\OrderUser
     */
    private $shippingUser;
    
    /**
     * @var Kitpages\ShopBundle\Entity\OrderHistory
     */
    private $orderHistoryList;
    
    /**
     * @var Kitpages\ShopBundle\Entity\OrderLine
     */
    private $orderLineList;
    
    public function __construct()
    {
        $this->orderHistoryList = new \Doctrine\Common\Collections\ArrayCollection();
        $this->orderLineList    = new \Doctrine\Common\Collections\ArrayCollection();
        $this->createdAt        = new \DateTime();
        $this->updatedAt        = new \DateTime();
    }
    
    /**
     * Set randomKey
     *
     * @param string $randomKey
     */
    public function setRandomKey($randomKey)
    {
        $this->randomKey = $randomKey;
    }
    
    /**
     * Get randomKey
     *
     * @return string
     */
    public function getRandomKey()
    {
        return $this->randomKey;
    }
    
    /**
     * Set state
     *
     * @param string $state
     */
    public function setState($state)
    {
        $this->state = $state;
    }
    
    /**
     * Get state
     *
     * @return string
     */
    public function getState()
    {
        return $this->state;
    }
    
    /**
     * Set stateDate
     *
     * @param datetime $stateDate
     */
    public function setStateDate($stateDate)
    {
        $this->stateDate = $stateDate;
    }
    
    /**
     * Get stateDate
     *
     * @return datetime
     */
    public function getStateDate()
    {
        return $this->stateDate;
    }
    
    /**
     * Set priceWithoutVat
     *
     * @param float $priceWithoutVat
     */
    public function setPriceWithoutVat($priceWithoutVat)
    {
        $this->priceWithoutVat = $priceWithoutVat;
    }
    
    /**
     * Get priceWithoutVat
     *
     * @return float
     */
    public function getPriceWithoutVat()
    {
        return $this->priceWithoutVat;
    }
    
    /**
     * Set priceIncludingVat
     *
     * @param float $priceIncludingVat
     */
    public function setPriceIncludingVat($priceIncludingVat)
    {
        $this->priceIncludingVat = $priceIncludingVat;
    }
    
    /**
     * Get priceIncludingVat
     *
     * @return float
     */
    public function getPriceIncludingVat()
    {
        return $this->priceIncludingVat;
    }
    
    /**
     * Set createdAt
     *
     * @param datetime $createdAt
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
    }
    
    /**
     * Get createdAt
     *
     * @return datetime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }
    
    /**
     * Set updatedAt
     *
     * @param datetime $updatedAt
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;
    }
    
    /**
     * Get updatedAt
     *
     * @return datetime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }
    
    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }
    
    /**
     * Set invoice
     *
     * @param \Kitpages\ShopBundle\Entity\Invoice $invoice
     */
    public function setInvoice(\Kitpages\ShopBundle\Entity\Invoice $invoice)
    {
        $this->invoice = $invoice;
    }
    
    /**
     * Get invoice
     *
     * @return \Kitpages\ShopBundle\Entity\Invoice
     */
    public function getInvoice()
    {
        return $this->invoice;
    }
    
    /**
     * Set invoiceUser
     *
     * @param \Kitpages\ShopBundle\Entity\OrderUser $invoiceUser
     */
    public function setInvoiceUser(\Kitpages\ShopBundle\Entity\OrderUser $invoiceUser)
    {
        $this->invoiceUser = $invoiceUser;
    }
    
    /**
     * Get invoiceUser
     *
     * @return \Kitpages\ShopBundle\Entity\OrderUser
     */
    public function getInvoiceUser()
    {
        return $this->invoiceUser;
    }
    
    /**
     * Set shippingUser
     *
     * @param \Kitpages\ShopBundle\Entity\OrderUser $shippingUser
     */
    public function setShippingUser(\Kitpages\ShopBundle\Entity\OrderUser $shippingUser)
    {
        $this->shippingUser = $shippingUser;
    }
    
    /**
     * Get shippingUser
     *
     * @return \Kitpages\ShopBundle\Entity\OrderUser
     */
    public function getShippingUser()
    {
        return $this->shippingUser;
    }
    
    /**
     * Add orderHistoryList
     *
     * @param \Kitpages\ShopBundle\Entity\OrderHistory $orderHistoryList
     */
    public function addOrderHistory(\Kitpages\ShopBundle\Entity\OrderHistory $orderHistoryList)
    {
        $this->orderHistoryList[] = $orderHistoryList;
    }
    
    /**
     * Get orderHistoryList
     *
     * @return Doctrine\Common\Collections\Collection
     */
    public function getOrderHistoryList()
    {
        return $this->orderHistoryList;
    }
    
    /**
     * Add orderLineList
     *
     * @param \Kitpages\ShopBundle\Entity\OrderLine $orderLineList
     */
    public function addOrderLine(\Kitpages\ShopBundle\Entity\OrderLine $orderLineList)
    {
        $this->orderLineList[] = $orderLineList;
    }
    
    /**
     * Get orderLineList
     *
     * @return Doctrine\Common\Collections\Collection
     */
    public function getOrderLineList()
    {
        return $this->orderLineList;
    }
    
    /**
     * @var string $username
     */
    private $username;
    
    
    /**
     * Set username
     *
     * @param string $username
     */
    public function setUsername($username)
    {
        $this->username = $username;
    }
    
    /**
     * Get username
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }
    
    /**
     * @var string $locale
     */
    private $locale;
    
    
    /**
     * Set locale
     *
     * @param string $locale
     */
    public function setLocale($locale)
    {
        $this->locale = $locale;
    }
    
    /**
     * Get locale
     *
     * @return string
     */
    public function getLocale()
    {
        return $this->locale;
    }
    
    public function getPaymentInstruction()
    {
        return $this->paymentInstruction;
    }
    
    public function setPaymentInstruction(PaymentInstruction $instruction)
    {
        $this->paymentInstruction = $instruction;
    }
    
    /**
     * Add orderHistoryList
     *
     * @param \Kitpages\ShopBundle\Entity\OrderHistory $orderHistoryList
     *
     * @return Order
     */
    public function addOrderHistoryList(\Kitpages\ShopBundle\Entity\OrderHistory $orderHistoryList)
    {
        $this->orderHistoryList[] = $orderHistoryList;
        
        return $this;
    }
    
    /**
     * Remove orderHistoryList
     *
     * @param \Kitpages\ShopBundle\Entity\OrderHistory $orderHistoryList
     */
    public function removeOrderHistoryList(\Kitpages\ShopBundle\Entity\OrderHistory $orderHistoryList)
    {
        $this->orderHistoryList->removeElement($orderHistoryList);
    }
    
    /**
     * Add orderLineList
     *
     * @param \Kitpages\ShopBundle\Entity\OrderLine $orderLineList
     *
     * @return Order
     */
    public function addOrderLineList(\Kitpages\ShopBundle\Entity\OrderLine $orderLineList)
    {
        $this->orderLineList[] = $orderLineList;
        
        return $this;
    }
    
    /**
     * Remove orderLineList
     *
     * @param \Kitpages\ShopBundle\Entity\OrderLine $orderLineList
     */
    public function removeOrderLineList(\Kitpages\ShopBundle\Entity\OrderLine $orderLineList)
    {
        $this->orderLineList->removeElement($orderLineList);
    }
}