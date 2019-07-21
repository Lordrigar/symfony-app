<?php

namespace App\Services;

use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Request;

class PageService
{
    private $request;
    private $em;
    private $entityName;
    private $pageSize;
    private $currentPage;
    private $totalPages;
    private $totalRecords;
    private $offset;
    private $sortField;
    private $defaultSortField;
    private $sortDirection;
    private $sortReverse;
    private $filter;
    
    /**
     * Constructor
     */
    public function __construct(
        Request $request,
        EntityManager $em, 
        ?string $entity_name = null, 
        int $page_size = 20, 
        ?string $default_sort_field = null
    ) {
        $this->request = $request;
        $this->em = $em;
        $this->entityName = $entity_name;
        $this->pageSize = $page_size;
        $this->setFilter();
        $this->totalRecords = $this->getTotal();
        $this->setCurrentPage();
        $this->defaultSortField = $default_sort_field;
        $this->setSorting();
    }

    /**
     * Get the total number of records
     */
    private function getTotal() 
    {
        $total = $this->em->getRepository($this->entityName)
            ->createQueryBuilder('t')
            ->select('count(t.id)');
        if(!empty($this->filter)) {
            foreach($this->filter as $key => $value) {
                if( $this->em->getClassMetadata($this->entityName)->hasField($key) ) {
                    $total = $total->andWhere("t.{$key} LIKE :{$key}");
                    if( $this->em->getClassMetadata($this->entityName)->getTypeOfField($key) === 'string' || 
                        $this->em->getClassMetadata($this->entityName)->getTypeOfField($key) === 'text' ) {
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
    private function setCurrentPage() 
    {
        $request = json_decode($this->request->getContent());
        if (isset($request->page)) {
            $this->currentPage = $request->page;
        }

        if(!isset($this->currentPage)) {
            $this->currentPage = 1;
        }

        $this->totalPages = ceil($this->totalRecords / $this->pageSize);
        
        if(($this->currentPage * $this->pageSize) > $this->totalRecords) {
            $this->currentPage = $this->totalPages;
        }
        
        // Offset for db table
        if($this->currentPage > 1) {
            $this->offset = ($this->currentPage - 1) * $this->pageSize;
        } else {
           $this->offset = 0;
        }
    }

    /**
     * Get the records for the current page
     */
    public function getRecords() 
    {
        $records = $this->em->getRepository($this->entityName)
            ->createQueryBuilder('t');
        if(!empty($this->sortField)) {
            $records = $records->orderBy('t.' . $this->sortField, $this->sortDirection);
        }
        if(!empty($this->filter)) {
            foreach($this->filter as $key => $value) {
                if( $this->em->getClassMetadata($this->entityName)->hasField($key) ) {
                    $records = $records->andWhere("t.{$key} LIKE :{$key}");
                    if( $this->em->getClassMetadata($this->entityName)->getTypeOfField($key) === 'string' || 
                        $this->em->getClassMetadata($this->entityName)->getTypeOfField($key) === 'text' ) {
                        $records = $records->setParameter($key, '%' . $value . '%');
                    } else {
                        $records = $records->setParameter($key, $value);
                    }
                }
            }
        }
        $records = $records->setFirstResult($this->offset)
            ->setMaxResults($this->pageSize)
            ->getQuery()
            ->getResult();
        return $records;
    }
    
    /**
     * Get the parameters for the page display
     */
    public function getDisplayParameters() 
    {
        $return = array(
            'current_page' => $this->currentPage,
            'total_pages' => $this->totalPages,
            'sort_field' => $this->sortField,
            'sort_order' => $this->sortDirection,
            'sort_reverse' => $this->sortReverse,
        );

        if(empty($this->sortField)) {
            $return['sort'] = '';
        } else {
            $return['sort'] = $this->sortField . '.' . strtolower($this->sortDirection);
        }

        return $return;
    }

    /**
     * Set the sorting fields
     */
    private function setSorting() 
    {
        $request = json_decode($this->request->getContent());

        if (isset($request->sort)) {
            $sort = $request->sort;
        }

        if(empty($sort) && empty($this->defaultSortField)) {
            $this->sortField = '';
            $this->sortDirection = '';
        } else {
            if(empty($sort)) {
                $arr = explode('.', $this->defaultSortField);
            } else {
                $arr = explode('.', $sort);
            }
            if(empty($arr[0])) {
                $this->sortField = '';
                $this->sortDirection = '';
            } elseif(count($arr) == 1 || empty($arr[1])) {
                $this->sortField = $arr[0];
                $this->sortDirection = 'ASC';
                $this->sortReverse = $this->sortField . '.desc';
            } else {
                $this->sortField = $arr[0];
                if(strtolower($arr[1]) == 'desc') {
                    $this->sortDirection = 'DESC';
                    $this->sortReverse = $this->sortField . '.asc';
                } else {
                    $this->sortDirection = 'ASC';
                    $this->sortReverse = $this->sortField . '.desc';
                }
            }
            // Validate sort field
            if(!$this->em->getClassMetadata($this->entityName)->hasField($this->sortField) ) {
                $this->sortField = '';
                $this->sortDirection = '';
            }
        }
    }

    /**
     * Set Filter (remove keys for empty values)
     */
    private function setFilter() 
    {
        $this->filter = [];
        $filters = null;
        $request = json_decode($this->request->getContent(), true);

        if (isset($request['filters'])) {
            $filters = $request['filters'];
        }

        if($filters && is_array($filters)) {
            foreach($filters as $filter) {
                foreach ($filter as $key => $value) {
                    if(!empty($value) || $value == '0') {
                        $this->filter[$key] = $value;
                    }
                }
            }
        }
    }
}
