<?php
namespace Kitpages\ShopBundle\Entity;

class OrderUser
{

    /**
     * @var integer $userId
     */
    private $userId;

    /**
     * @var string $firstName
     */
    private $firstName;

    /**
     * @var string $lastName
     */
    private $lastName;

    /**
     * @var string $address
     */
    private $address;

    /**
     * @var string $zipCode
     */
    private $zipCode;

    /**
     * @var string $city
     */
    private $city;

    /**
     * @var string $state
     */
    private $state;

    /**
     * @var string $countryCode
     */
    private $countryCode;

    /**
     * @var string $email
     */
    private $email;

    /**
     * @var string $phone
     */
    private $phone;

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
     * @var \Kitpages\ShopBundle\Entity\Order
     */
    private $invoiceOrder;

    /**
     * @var \Kitpages\ShopBundle\Entity\Order
     */
    private $shippingOrder;
    
    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->updatedAt = new \DateTime();
    }
    
    /**
     * @return int
     */
    public function getUserId()
    {
        return $this->userId;
    }
    
    /**
     * @param int $userId
     *
     * @return OrderUser
     */
    public function setUserId( int $userId ): OrderUser
    {
        $this->userId = $userId;
        
        return $this;
    }
    
    /**
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }
    
    /**
     * @param string $firstName
     *
     * @return OrderUser
     */
    public function setFirstName( $firstName ): OrderUser
    {
        $this->firstName = $firstName;
        
        return $this;
    }
    
    /**
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }
    
    /**
     * @param string $lastName
     *
     * @return OrderUser
     */
    public function setLastName( $lastName ): OrderUser
    {
        $this->lastName = $lastName;
        
        return $this;
    }
    
    /**
     * @return string
     */
    public function getAddress()
    {
        return $this->address;
    }
    
    /**
     * @param string $address
     *
     * @return OrderUser
     */
    public function setAddress( $address ): OrderUser
    {
        $this->address = $address;
        
        return $this;
    }
    
    /**
     * @return string
     */
    public function getZipCode()
    {
        return $this->zipCode;
    }
    
    /**
     * @param string $zipCode
     *
     * @return OrderUser
     */
    public function setZipCode( $zipCode ): OrderUser
    {
        $this->zipCode = $zipCode;
        
        return $this;
    }
    
    /**
     * @return string
     */
    public function getCity()
    {
        return $this->city;
    }
    
    /**
     * @param string $city
     *
     * @return OrderUser
     */
    public function setCity( $city ): OrderUser
    {
        $this->city = $city;
        
        return $this;
    }
    
    /**
     * @return string
     */
    public function getState()
    {
        return $this->state;
    }
    
    /**
     * @param string $state
     *
     * @return OrderUser
     */
    public function setState( $state ): OrderUser
    {
        $this->state = $state;
        
        return $this;
    }
    
    /**
     * @return string
     */
    public function getCountryCode()
    {
        return $this->countryCode;
    }
    
    /**
     * @param string $countryCode
     *
     * @return OrderUser
     */
    public function setCountryCode( $countryCode ): OrderUser
    {
        $this->countryCode = $countryCode;
        
        return $this;
    }
    
    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }
    
    /**
     * @param string $email
     *
     * @return OrderUser
     */
    public function setEmail( $email ): OrderUser
    {
        $this->email = $email;
        
        return $this;
    }
    
    /**
     * @return string
     */
    public function getPhone()
    {
        return $this->phone;
    }
    
    /**
     * @param string $phone
     *
     * @return OrderUser
     */
    public function setPhone( $phone ): OrderUser
    {
        $this->phone = $phone;
        
        return $this;
    }
    
    /**
     * @return \Datetime
     */
    public function getCreatedAt(): \Datetime
    {
        return $this->createdAt;
    }
    
    /**
     * @param \Datetime $createdAt
     *
     * @return OrderUser
     */
    public function setCreatedAt( $createdAt ): OrderUser
    {
        $this->createdAt = $createdAt;
        
        return $this;
    }
    
    /**
     * @return \Datetime
     */
    public function getUpdatedAt(): \Datetime
    {
        return $this->updatedAt;
    }
    
    /**
     * @param \Datetime $updatedAt
     *
     * @return OrderUser
     */
    public function setUpdatedAt( $updatedAt ): OrderUser
    {
        $this->updatedAt = $updatedAt;
        
        return $this;
    }
    
    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }
    
    /**
     * @return \Kitpages\ShopBundle\Entity\Order
     */
    public function getInvoiceOrder(): \Kitpages\ShopBundle\Entity\Order
    {
        return $this->invoiceOrder;
    }
    
    /**
     * @param \Kitpages\ShopBundle\Entity\Order $invoiceOrder
     *
     * @return OrderUser
     */
    public function setInvoiceOrder( \Kitpages\ShopBundle\Entity\Order $invoiceOrder ): OrderUser
    {
        $this->invoiceOrder = $invoiceOrder;
        
        return $this;
    }
    
    /**
     * @return \Kitpages\ShopBundle\Entity\Order
     */
    public function getShippingOrder(): \Kitpages\ShopBundle\Entity\Order
    {
        return $this->shippingOrder;
    }
    
    /**
     * @param \Kitpages\ShopBundle\Entity\Order $shippingOrder
     *
     * @return OrderUser
     */
    public function setShippingOrder( \Kitpages\ShopBundle\Entity\Order $shippingOrder ): OrderUser
    {
        $this->shippingOrder = $shippingOrder;
        
        return $this;
    }
}