<?php

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\Mapping\MappingException;

/**
 * Class Task
 */
class Task
{
    const STATUS_OK = 0;
    const STATUS_NOT_OK = 1;

    /**
     * @var EntityManager
     */
    private $em;

    /**
     * Task constructor.
     *
     * @param EntityManager $entityManager
     */
    public function __construct(
        EntityManager $entityManager
    )
    {
        $this->em = $entityManager;

    }

    /**
     * @param string $url
     * @param int    $page
     * @param int    $count
     *
     * @return mixed
     */
    public function getProducts($url, $page, $count)
    {
        $ch = curl_init("{$url}?count={$count}&page={$page}");
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/xml']);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_USERPWD, $_ENV['APP_API_USERNAME'] . ":" . $_ENV['APP_API_PASSWORD']);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        $return = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        //@todo use constant
        if ($httpCode == 200) {
            return [
                'code' => self::STATUS_OK,
                'data' => json_decode($return, true),
            ];

        } else {
            return [
                'code' => self::STATUS_NOT_OK,
            ];
        }
    }


    /**
     * @param $products
     *
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws MappingException
     */
    function saveProducts($products)
    {
        $ids = array_map(
            function ($n) {
                return $n['id'];
            }, $products
        );
        $r = $this->em->getRepository(Product::class);
        $existingProducts = $r->findBy(['externalId' => $ids]);

        foreach ($products as $product) {
            $existingProduct = array_filter(
                $existingProducts,
                function ($obj) use ($product) {
                    return $obj->getExternalId() == $product['id'];
                }
            );
            $e = $existingProduct ? array_values($existingProduct)[0] : new Product();
            $e->setExternalId($product['id']);
            $e->setName($product['name']);
            $e->setTitle($product['title']);
            $e->setManufacturerId($product['manufacturer_id']);
            $e->setManufacturerName($product['manufacturer_name']);
            $e->setWarranty($product['warranty']);
            $e->setWarrantyType($product['warranty_type']);
            $e->setCurrency($product['currency']);
            $e->setVatPercent($product['vat_percent']);
            $e->setProductCategoryId($product['product_category_id']);
            $this->em->persist($e);
        }
        $this->em->flush();
        $this->em->clear();
    }
}