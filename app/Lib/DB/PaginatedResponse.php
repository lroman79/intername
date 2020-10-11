<?php

namespace App\Lib\DB;

use Illuminate\Database\Eloquent\Collection as EloquentCollection;

class PaginatedResponse
{
    /**
     * Current data collection.
     *
     * @var EloquentCollection
     */
    public $dataCollection;

    /**
     * The number of records in current data collection.
     *
     * @var int
     */
    public $dataCount;

    /**
     * PaginatedResponse constructor.
     *
     * @param EloquentCollection $dataCollection
     * @param int $dataCount
     */
    public function __construct(EloquentCollection $dataCollection, int $dataCount)
    {
        $this->dataCollection = $dataCollection;
        $this->dataCount = $dataCount;
    }
}
