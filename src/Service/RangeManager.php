<?php
/**
 * Created by PhpStorm.
 * User: fle
 * Date: 06/05/2021
 * Time: 11:38
 */

namespace App\Service;


use App\Entity\Range;
use App\Entity\RangeLabel;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityNotFoundException;
use Doctrine\ORM\QueryBuilder;
use Exception;
use Symfony\Component\DependencyInjection\ContainerInterface;

class RangeManager
{

    private $em;
    private $container;

    public function __construct(EntityManagerInterface $em, ContainerInterface $container)
    {
        $this->em = $em;
        $this->container = $container;
    }

    public function get($range)
    {
        $id = $range;
        if ($range instanceof Range) {
            $id = $range->getId();
        }
        try {

            $range = $this->em->getRepository('App:Range')->find($id);

            /**
             * Vérification que la gamme existe ou ne soit pas supprimée
             */
            if ($range === null || $this->isDeleted($range)) {
                throw new EntityNotFoundException('rangeNotFound');
            }


            return $range;

        } catch (Exception $e) {
            throw new Exception($e->getMessage(), $e->getCode());
        }
    }

    /**
     * Vérifie qu'à ce jour, la gamme ce soit pas supprimée
     *
     * @param Range $range
     * @param bool $throwException
     * @return bool
     * @throws EntityNotFoundException
     * @throws Exception
     */
    public function isDeleted(Range $range, $throwException = false)
    {
        $now = new \DateTime();

        if ($range->getDeleted() !== null && $range->getDeleted() instanceof \DateTime && $range->getDeleted() < $now) {

            if ($throwException) {
                throw new EntityNotFoundException('rangeNotFound');
            }

            return true;

        }
        return false;
    }

    public function getRangeLabels($range)
    {
        $id = $range;
        if ($range instanceof Range) {
            $id = $range->getId();
        }
        try {

            $rangeLabels = $this->em->getRepository('App:RangeLabel')->findBy(array(
                    'range' => $range,
                    'deleted' => null
                )
            );

            /**
             * Vérification que la gamme traduite existe ou ne soit pas supprimée
             */
            if (empty($rangeLabels)) {
                throw new EntityNotFoundException('rangeLabelsNotFound');
            }


            return $rangeLabels;

        } catch (Exception $e) {
            throw new Exception($e->getMessage(), $e->getCode());
        }

    }

    public function getRangeLabelByRangeAndLocale(Range $range, $language)
    {

        $id = $range;
        if ($range instanceof Range) {
            $id = $range->getId();
        }
        try {

            $range = $this->em->getRepository('App:Range')->find($id);

            /**
             * Vérification que la gamme existe ou ne soit pas supprimée
             */
            if ($range === null || $this->isDeleted($range)) {
                throw new EntityNotFoundException('rangeNotFound');
            }

            $rangeLabel = $this->em->getRepository('App:RangeLabel')->findOneBy(array(
                'range' => $range,
                'language' => $language
            ));

            /**
             * Si il y'en a pas dans la langue de la locale, on en prend un au hasard
             */
            if ($rangeLabel === null || $this->IsDeletedRangeLabel($rangeLabel)) {
                $rangeLabel = $this->em->getRepository('App:RangeLabel')->findOneBy(array(
                    'range' => $range
                ));

                if ($rangeLabel === null || $this->IsDeletedRangeLabel($rangeLabel)) {
                    throw new EntityNotFoundException('rangeLabelNotFound');
                }
            }


            return $rangeLabel;

        } catch (Exception $e) {
            throw new Exception($e->getMessage(), $e->getCode());
        }
    }

    /**
     * Vérifie qu'à ce jour, le libellé gamme ne soit pas supprimé
     *
     * @param RangeLabel $rangeLabel
     * @param bool $throwException
     * @return bool
     * @throws EntityNotFoundException
     * @throws Exception
     */
    public function isDeletedRangeLabel(RangeLabel $rangeLabel, $throwException = false)
    {
        $now = new \DateTime();

        if ($rangeLabel->getDeleted() !== null && $rangeLabel->getDeleted() instanceof \DateTime && $rangeLabel->getDeleted() < $now) {

            if ($throwException) {
                throw new EntityNotFoundException('rangeLabelNotFound');
            }

            return true;

        }
        return false;
    }

    public function getAvailableRanges($executeQuery = false, string $catalog = null)
    {
        $queryBuilder = $this->em->getRepository(Range::class)->createQueryBuilder('r');

        $queryBuilder->select(array('r'))
            ->where('r.deleted IS NULL')
            ->andWhere('r.isEnabled = 1');

        if ($catalog) {
            $queryBuilder
                ->andWhere('r.catalog = :catalog')
                ->setParameter('catalog', $catalog);
        }

        if ($executeQuery) {
            return $queryBuilder->getQuery()->getResult();
        }

        return $queryBuilder;
    }


    /***********************
     * FONCTION D'AJOUT DE QUERYPARAM
     */

    public function addAvailableProducts(QueryBuilder $queryBuilder, $executeQuery = false)
    {
        $queryBuilder
            ->addSelect('p')
            ->leftJoin('r.products', 'p')
            ->andWhere('p.deleted IS NULL')
            ->andWhere('p.isEnabled = 1')
            ->addOrderBy('r.position', 'ASC')
            ->addOrderBy('p.position', 'ASC');

        if ($executeQuery) {
            return $queryBuilder->getQuery()->getResult();
        }

        return $queryBuilder;
    }


}
