<?php
namespace Kitpages\ShopBundle\Entity;

class Invoice
{

    /**
     * @var string $reference
     */
    private $reference;

    /**
     * @var string $contentHtml
     */
    private $contentHtml;

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
    private $order;
    
    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->updatedAt = new \DateTime();
    }
    
    
    /**
     * Set reference
     *
     * @param string $reference
     */
    public function setReference($reference)
    {
        $this->reference = $reference;
    }

    /**
     * Get reference
     *
     * @return string
     */
    public function getReference()
    {
        return $this->reference;
    }

    /**
     * @param string $contentHtml
     */
    public function setContentHtml($contentHtml)
    {
        $this->contentHtml = $contentHtml;
    }

    /**
     * @return string
     */
    public function getContentHtml()
    {
        return $this->contentHtml;
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
     * Set order
     *
     * @param \Kitpages\ShopBundle\Entity\Order $order
     */
    public function setOrder(\Kitpages\ShopBundle\Entity\Order $order)
    {
        $this->order = $order;
    }

    /**
     * Get order
     *
     * @return \Kitpages\ShopBundle\Entity\Order
     */
    public function getOrder()
    {
        return $this->order;
    }

}