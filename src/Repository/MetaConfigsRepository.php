<?php

namespace App\Repository;

use App\Entity\MetaConfigs;
use App\Helper\GetAll;
use App\Service\Share\Config;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Exception;
use Doctrine\DBAL\Exception\InvalidArgumentException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<MetaConfigs>
 *
 * @method MetaConfigs|null find($id, $lockMode = null, $lockVersion = null)
 * @method MetaConfigs|null findOneBy(array $criteria, array $orderBy = null)
 * @method MetaConfigs[]    findAll()
 * @method MetaConfigs[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MetaConfigsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry , private readonly Config $config , private readonly GetAll $getAll)
    {
        parent::__construct($registry, MetaConfigs::class);
    }

    public function findById($metaConfig_id): ?MetaConfigs
    {
        $app_code = $this->config->getConfig('appCode');
        return $this->findOneBy(['id' => $metaConfig_id , 'deleted_at' => null , 'app_code' => $app_code]);
    }

    public function findByName($metaConfig_name): ?MetaConfigs
    {
        $app_code = $this->config->getConfig('appCode');
        return $this->findOneBy(['name' => $metaConfig_name , 'deleted_at' => null , 'app_code' => $app_code]);
    }

    public function findAllByReference($metaConfig_reference): array
    {
        $app_code = $this->config->getConfig('appCode');
        return $this->findBy(['reference' => $metaConfig_reference , 'deleted_at' => null , 'app_code' => $app_code]);
    }

    public function findByNameAndReference($metaConfig_name , $metaConfig_reference): ?MetaConfigs
    {
        $app_code = $this->config->getConfig('appCode');
        return $this->findOneBy(['name' => $metaConfig_name , 'reference' => $metaConfig_reference , 'deleted_at' => null , 'app_code' => $app_code]);
    }

    public function toArray($reference , $show_all_fields = true): array
    {
        return $this->getAll->toArray($reference , $show_all_fields);
    }

    /**
     * @throws InvalidArgumentException
     * @throws Exception
     */
    public function OrderAndSearch(): array
    {
        $qb = $this->createQueryBuilder('p');
        $metaConfigs = $this->getAll->orderAndSearch($qb);
        $data = [];
        foreach ($metaConfigs as $metaConfig){
            $result = $this->toArray($metaConfig , false);
            /*if($meta_mode) {
                $courses_meta = $this->coursesMetaRepository->findBy(['course_ref' => $course, 'deleted_at' => null]);
                $result['meta'] = $this->meta->addMetaToEntity($courses_meta, $meta_mode);
            }*/
            $data[] = $result;
        }
        return $data;
    }

//    /**
//     * @return MetaConfigs[] Returns an array of MetaConfigs objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('m.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?MetaConfigs
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
