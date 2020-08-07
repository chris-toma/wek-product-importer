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

    public function execute()
    {
        $page = 1;
        $productCount = $_ENV['DEFAULT_PRODUCT_COUNT'];
        $retryCount = 0;

        do {
            if ($retryCount > $_ENV['RETRY_MAX_LIMIT']) {
                print "end by max retry \n";
                exit(1);
                break;
            }

            $ret = $this->getProducts(
                $_ENV['API_ENDPOINT_URL'],
                $page,
                $productCount
            );

            if ($ret['code'] == self::STATUS_NOT_OK) {
                $retryCount++;
                // decreasing the product per page value in case this is the problem
                $productCount = $productCount > $_ENV['MIN_PRODUCT_COUNT']
                    ? round($productCount / 2)
                    : $_ENV['MIN_PRODUCT_COUNT'];

                print "retry count {$retryCount} \n";
                sleep(
                    (
                    $retryCount < $_ENV['MAX_DELAY_MULTIPLIER']
                        ? $retryCount
                        : $_ENV['MAX_DELAY_MULTIPLIER']
                    ) * $_ENV['RETRY_DELAY_SECONDS']
                );
                // log data
                continue;
            } else {
                // reset the retry count in case we had a retry before this
                $retryCount = 0;
                $page++;
            }
            if (count($ret['data']['products']) > 0) {
                print "page {$ret['data']['current_page']} from {$ret['data']['total_pages']} has products \n";
                try {
                    $this->saveProducts($ret['data']['products']);
                    //log success
                } catch (Exception $e) {
                    //log errors
                }
            } else {
                print "no more products \n";
                break;
            }

        } while ($retryCount > 0 || $ret['data']['current_page'] <= $ret['data']['total_pages']);
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
                'code' => self::STATUS_OK,
                'data' => $data,
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