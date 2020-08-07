<?php

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;


/**
 * @ORM\Entity
 * @ORM\Table(name="products")
 */
class Product
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    protected $id;
    /**
     * @ORM\Column(type="string")
     */
    protected $name;
    /**
     * @ORM\Column(type="string",name="external_id")
     */
    protected $externalId;

    /**
     * @ORM\Column(type="string",name="title")
     */
    protected $title;
    /**
     * @ORM\Column(type="string",name="manufacturer_id")
     */
    protected $manufacturerId;
    /**
     * @ORM\Column(type="string",name="manufacturer_name")
     */
    protected $manufacturerName;
    /**
     * @ORM\Column(type="integer",name="warranty")
     */
    protected $warranty;
    /**
     * @ORM\Column(type="string",name="warranty_type")
     */
    protected $warranty_type;

    /**
     * @ORM\Column(type="string",name="currency")
     */
    protected $currency;
    /**
     * @ORM\Column(type="integer",name="vat_percent")
     */
    protected $vat_percent;
    /**
     * @ORM\Column(type="integer",name="product_category_id")
     */
    protected $product_category_id;


    public function getId()
    {
        return $this->id;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getExternalId()
    {
        return $this->externalId;
    }

    /**
     * @param mixed $externalId
     */
    public function setExternalId($externalId)
    {
        $this->externalId = $externalId;
    }

    /**
     * @return mixed
     */
    public function getManufacturerId()
    {
        return $this->manufacturerId;
    }

    /**
     * @param mixed $manufacturerId
     */
    public function setManufacturerId($manufacturerId)
    {
        $this->manufacturerId = $manufacturerId;
    }

    /**
     * @return mixed
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param mixed $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @return mixed
     */
    public function getManufacturerName()
    {
        return $this->manufacturerName;
    }

    /**
     * @param mixed $manufacturerName
     */
    public function setManufacturerName($manufacturerName)
    {
        $this->manufacturerName = $manufacturerName;
    }

    /**
     * @return mixed
     */
    public function getWarranty()
    {
        return $this->warranty;
    }

    /**
     * @param mixed $warranty
     */
    public function setWarranty($warranty)
    {
        $this->warranty = $warranty;
    }

    /**
     * @return mixed
     */
    public function getWarrantyType()
    {
        return $this->warranty_type;
    }

    /**
     * @param mixed $warranty_type
     */
    public function setWarrantyType($warranty_type)
    {
        $this->warranty_type = $warranty_type;
    }

    /**
     * @return mixed
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * @param mixed $currency
     */
    public function setCurrency($currency)
    {
        $this->currency = $currency;
    }

    /**
     * @return mixed
     */
    public function getVatPercent()
    {
        return $this->vat_percent;
    }

    /**
     * @param mixed $vat_percent
     */
    public function setVatPercent($vat_percent)
    {
        $this->vat_percent = $vat_percent;
    }

    /**
     * @return mixed
     */
    public function getProductCategoryId()
    {
        return $this->product_category_id;
    }

    /**
     * @param mixed $product_category_id
     */
    public function setProductCategoryId($product_category_id)
    {
        $this->product_category_id = $product_category_id;
    }
}