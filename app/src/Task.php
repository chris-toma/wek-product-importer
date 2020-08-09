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
    const CURL_STATUS_OK = 0;
    const CURL_STATUS_NOT_OK = 10;

    const IMPORT_STATUS_OK = 0;
    const IMPORT_STATUS_DOCTRINE_EXCEPTION = 20;
    const IMPORT_STATUS_NO_MORE_PRODUCTS = 30;

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
     * @param int $page
     * @param int $productCount
     */
    public function executeImport($page, $productCount)
    {
        $ret = $this->getProducts(
            $_ENV['API_ENDPOINT_URL'],
            $page,
            $productCount
        );

        if ($ret['code'] == self::CURL_STATUS_OK) {
            if (($c = count($ret['data']['products'])) > 0) {
                shell_exec("echo {$c} products found on page {$page} >> log.log");
                try {
                    $this->saveProducts($ret['data']['products']);
                    exit(self::IMPORT_STATUS_OK);

                } catch (Exception $e) {
                    exit(self::IMPORT_STATUS_DOCTRINE_EXCEPTION);
                }
            } else {
                shell_exec("echo {$c} products found on page {$page} >> log.log");
                exit(self::IMPORT_STATUS_NO_MORE_PRODUCTS);
            }
        } else {
            exit(self::CURL_STATUS_NOT_OK);
        }
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
        curl_setopt($ch, CURLOPT_TIMEOUT, 20);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        $return = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $data = json_decode($return, true);
        curl_close($ch);

        //@todo use constant
        if ($httpCode == 200 && $data) {
            return [
                'code' => self::CURL_STATUS_OK,
                'data' => $data,
            ];

        } else {
            return [
                'code' => self::CURL_STATUS_NOT_OK,
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

        $existingProducts = $this->em
            ->getRepository(Product::class)
            ->findBy(['externalId' => $ids])
        ;

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