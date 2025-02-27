<?php

namespace Mautic\LeadBundle\Entity;

use Doctrine\DBAL\ArrayParameterType;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Mautic\CoreBundle\Entity\CommonRepository;

/**
 * @extends CommonRepository<LeadEventLog>
 */
class LeadEventLogRepository extends CommonRepository
{
    use TimelineTrait;

    /**
     * Returns paginator with failed rows.
     *
     * @param string $bundle
     * @param string $object
     *
     * @return Paginator
     */
    public function getFailedRows($importId, array $args = [], $bundle = 'lead', $object = 'import')
    {
        return $this->getSpecificRows($importId, 'failed', $args, $bundle, $object);
    }

    public function getEntities(array $args = [])
    {
        $entities = parent::getEntities($args);
        $entities = iterator_to_array($entities);

        foreach ($entities as $key => $row) {
            if (
                isset($row['properties']['error'])
                && preg_match('/SQLSTATE\[\w+\]: (.*)/', $row['properties']['error'], $matches)
            ) {
                $entities[$key]['properties']['error'] = $matches[1];
            }
        }

        return $entities;
    }

    /**
     * Returns paginator with specific type of rows.
     *
     * @param string $bundle
     * @param string $object
     *
     * @return Paginator
     */
    public function getSpecificRows($objectId, $action, array $args = [], $bundle = 'lead', $object = 'import')
    {
        return $this->getEntities(
            array_merge(
                [
                    'start'          => 0,
                    'limit'          => 100,
                    'orderBy'        => $this->getTableAlias().'.dateAdded',
                    'orderByDir'     => 'ASC',
                    'filter'         => [
                        'force' => [
                            [
                                'column' => $this->getTableAlias().'.bundle',
                                'expr'   => 'eq',
                                'value'  => $bundle,
                            ],
                            [
                                'column' => $this->getTableAlias().'.object',
                                'expr'   => 'eq',
                                'value'  => $object,
                            ],
                            [
                                'column' => $this->getTableAlias().'.action',
                                'expr'   => 'eq',
                                'value'  => $action,
                            ],
                            [
                                'column' => $this->getTableAlias().'.objectId',
                                'expr'   => 'eq',
                                'value'  => $objectId,
                            ],
                        ],
                    ],
                    'hydration_mode' => 'HYDRATE_ARRAY',
                ],
                $args
            )
        );
    }

    /**
     * @param ?string           $bundle
     * @param ?string           $object
     * @param array|string|null $actions
     *
     * @return array
     */
    public function getEvents(Lead $contact = null, $bundle = null, $object = null, $actions = null, array $options = [])
    {
        $alias = $this->getTableAlias();
        $qb    = $this->getEntityManager()->getConnection()->createQueryBuilder()
            ->select('*')
            ->from(MAUTIC_TABLE_PREFIX.'lead_event_log', $alias);

        if ($contact) {
            $qb->andWhere($alias.'.lead_id = :lead')
                ->setParameter('lead', $contact->getId());
        }

        if ($bundle) {
            $qb->andWhere($alias.'.bundle = :bundle')
                ->setParameter('bundle', $bundle);
        }

        if ($object) {
            $qb->andWhere($alias.'.object = :object')
                ->setParameter('object', $object);
        }

        if ($actions) {
            if (is_array($actions)) {
                $qb->andWhere(
                    $qb->expr()->in($alias.'.action', ':actions')
                )
                    ->setParameter('actions', $actions, ArrayParameterType::STRING);
            } else {
                $qb->andWhere($alias.'.action = :action')
                    ->setParameter('action', $actions);
            }
        }

        if (!empty($options['search'])) {
            $qb->andWhere($qb->expr()->like($alias.'.properties', $qb->expr()->literal('%'.$options['search'].'%')));
        }

        return $this->getTimelineResults($qb, $options, $alias.'.action', $alias.'.date_added', [], ['date_added']);
    }

    /**
     * Updates lead ID (e.g. after a lead merge).
     *
     * @param int $fromLeadId
     * @param int $toLeadId
     */
    public function updateLead($fromLeadId, $toLeadId): void
    {
        $q = $this->_em->getConnection()->createQueryBuilder();
        $q->update(MAUTIC_TABLE_PREFIX.'lead_event_log')
            ->set('lead_id', (int) $toLeadId)
            ->where('lead_id = '.(int) $fromLeadId)
            ->executeStatement();
    }

    /**
     * Defines default table alias for lead_event_log table.
     */
    public function getTableAlias(): string
    {
        return 'lel';
    }
}
