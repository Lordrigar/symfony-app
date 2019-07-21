<?php
/**
 * Filtering utility service, capable of sorting by any entity field, filtering through any field and pagination
 * Required input:
 * {
 *	"page": 1,
 *	"sort": "name.asc",
 *	"filters": [
 *		{"surname": "Zed"}
 *	]
 * }
 * 
 * If default parameters are passed, they should also be passed in the same manner:
 * "name.asc"
 * etc, for default sorting parameter
 */

namespace App\Services;

use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Request;

class PageService
{
    /**var Request $request */
    private $request;

    /** var EntityManager $em */
    private $em;

    /** var string|null $entityName */
    private $entityName;

    /** var int $pageSize */
    private $pageSize;

    /** var int $currentPage */
    private $currentPage;

    /** var int $totalPages */
    private $totalPages;

    /** var int $totalRecords */
    private $totalRecords;

    /** var int $offset */
    private $offset;

    /** var string|null $sortField */
    private $sortField;

    /** var string|null $defaultSortField */
    private $defaultSortField;

    /** var string|null $sortDirection */
    private $sortDirection;

    /** var string|null $sortReverse */
    private $sortReverse;

    /** var array|null $filter */
    private $filter;

    /**
     * @param Request $request
     * @param EntityManager $em
     * @param string|null $entityName
     * @param int $pageSize
     * @param string|null $defaultSortField
     */
    public function __construct(
        Request $request,
        EntityManager $em,
        ?string $entityName = null,
        int $pageSize = 20,
        ?string $defaultSortField = null
    ) {
        $this->request = $request;
        $this->em = $em;
        $this->entityName = $entityName;
        $this->pageSize = $pageSize;
        $this->setFilter();
        $this->totalRecords = $this->getTotal();
        $this->setCurrentPage();
        $this->defaultSortField = $defaultSortField;
        $this->setSorting();
    }

    /**
     * Get the total number of records
     * 
     * @return int
     */
    private function getTotal(): int
    {
        // Returns total number of records without filtering present
        $total = $this->em->getRepository($this->entityName)
            ->createQueryBuilder('t')
            ->select('count(t.id)');

        // If filters are present apply them to the query builder to return total amount after filters are applied
        if (!empty($this->filter)) {
            foreach ($this->filter as $key => $value) {
                //Check if the field actually exists on the entity
                if ($this->em->getClassMetadata($this->entityName)->hasField($key)) {
                    $total = $total->andWhere("t.{$key} LIKE :{$key}");
                    if (
                        $this->em->getClassMetadata($this->entityName)->getTypeOfField($key) === 'string' ||
                        $this->em->getClassMetadata($this->entityName)->getTypeOfField($key) === 'text'
                    ) {
                        $total = $total->setParameter($key, '%' . $value . '%');
                    } else {
                        $total = $total->setParameter($key, $value);
                    }
                }
            }
        }
        $total = $total->getQuery()->getSingleScalarResult();
        return $total;
    }

    /**
     * Set the current page and total number of pages
     */
    private function setCurrentPage(): void
    {
        $request = json_decode($this->request->getContent());

        // If there is a page in request use it as a current one
        if (isset($request->page)) {
            $this->currentPage = $request->page;
        }

        // If current page has not been set, default to first page
        if (!isset($this->currentPage)) {
            $this->currentPage = 1;
        }

        $this->totalPages = ceil($this->totalRecords / $this->pageSize);

        // If records are less than capacity of current page, set the page as the last one
        // ie if page is 2 and 20 records per page, and total records are 31, that means last page is 11 records and should be set to last one
        if (($this->currentPage * $this->pageSize) > $this->totalRecords) {
            $this->currentPage = $this->totalPages;
        }

        // Offset for db table
        if ($this->currentPage > 1) {
            $this->offset = ($this->currentPage - 1) * $this->pageSize;
        } else {
            $this->offset = 0;
        }
    }

    /**
     * Get the records for the current page
     */
    public function getRecords(): array
    {
        // Start query builder 
        $records = $this->em->getRepository($this->entityName)
            ->createQueryBuilder('t');

        // Apply sorting if set
        if (!empty($this->sortField)) {
            $records = $records->orderBy('t.' . $this->sortField, $this->sortDirection);
        }

        // If filtering is requested, add filters the same way as for total records
        if (!empty($this->filter)) {
            foreach ($this->filter as $key => $value) {
                if ($this->em->getClassMetadata($this->entityName)->hasField($key)) {
                    $records = $records->andWhere("t.{$key} LIKE :{$key}");
                    if (
                        $this->em->getClassMetadata($this->entityName)->getTypeOfField($key) === 'string' ||
                        $this->em->getClassMetadata($this->entityName)->getTypeOfField($key) === 'text'
                    ) {
                        $records = $records->setParameter($key, '%' . $value . '%');
                    } else {
                        $records = $records->setParameter($key, $value);
                    }
                }
            }
        }
        // Apply offset and finish the query builder
        $records = $records->setFirstResult($this->offset)
            ->setMaxResults($this->pageSize)
            ->getQuery()
            ->getResult();
        return $records;
    }

    /**
     * Get the parameters for the page display. Provides valuable information for front-end.
     * 
     * @return array
     */
    public function getDisplayParameters(): array
    {
        // Set parameters of current pagination to be returned
        $return = [
            'current_page' => $this->currentPage,
            'total_pages' => $this->totalPages,
            'sort_field' => $this->sortField,
            'sort_order' => $this->sortDirection,
            'sort_reverse' => $this->sortReverse,
        ];

        // Add information about sorted field, if enabled
        if (empty($this->sortField)) {
            $return['sort'] = '';
        } else {
            $return['sort'] = $this->sortField . '.' . strtolower($this->sortDirection);
        }

        return $return;
    }

    /**
     * Set the sorting fields
     */
    private function setSorting(): void
    {
        $request = json_decode($this->request->getContent());

        // If sorting was requested, set it
        if (isset($request->sort)) {
            $sort = $request->sort;
        }

        if (empty($sort) && empty($this->defaultSortField)) {
            $this->sortField = '';
            $this->sortDirection = '';
        } else {
            if (empty($sort)) {
                $arr = explode('.', $this->defaultSortField);
            } else {
                $arr = explode('.', $sort);
            }
            if (empty($arr[0])) {
                $this->sortField = '';
                $this->sortDirection = '';
            } elseif (count($arr) == 1 || empty($arr[1])) {
                $this->sortField = $arr[0];
                $this->sortDirection = 'ASC';
                $this->sortReverse = $this->sortField . '.desc';
            } else {
                $this->sortField = $arr[0];
                if (strtolower($arr[1]) == 'desc') {
                    $this->sortDirection = 'DESC';
                    $this->sortReverse = $this->sortField . '.asc';
                } else {
                    $this->sortDirection = 'ASC';
                    $this->sortReverse = $this->sortField . '.desc';
                }
            }
            // Validate sort field, if exists on the entity
            if (!$this->em->getClassMetadata($this->entityName)->hasField($this->sortField)) {
                $this->sortField = '';
                $this->sortDirection = '';
            }
        }
    }

    /**
     * Set Filter (remove keys for empty values)
     */
    private function setFilter(): void
    {
        $filters = null;
        $request = json_decode($this->request->getContent(), true);

        // If filters are requested, set them
        if (isset($request['filters'])) {
            $filters = $request['filters'];
        }

        // If filters exist, iterate over them and set to be used in query
        if ($filters && is_array($filters)) {
            foreach ($filters as $filter) {
                foreach ($filter as $key => $value) {
                    if (!empty($value) || $value == '0') {
                        $this->filter[$key] = $value;
                    }
                }
            }
        }
    }
}
