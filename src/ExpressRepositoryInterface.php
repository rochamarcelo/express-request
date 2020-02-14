<?php
namespace ExpressRequest;

use Cake\Datasource\RepositoryInterface;
use Cake\ORM\Query;
use ExpressRequest\Types\FiltersCollection;

interface ExpressRepositoryInterface extends RepositoryInterface
{
    public function getQuery($arg = null): Query;

    public function getFilterable(): FiltersCollection;

    public function getSelectables(): array;
}
