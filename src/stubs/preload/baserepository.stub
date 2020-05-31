<?php


namespace App\Repositories\Api;


use App\Repositories\BaseRepository;
use Spatie\QueryBuilder\QueryBuilder;

abstract class BaseCrudRepository extends BaseRepository
{
    protected $allowedFilters = [];
    protected $allowedSorts = [];

    abstract public function model();

    abstract public function forceDelete($model);

    abstract public function update($model, array $data);

    /**
     * Restore the soft deleted model
     * @param $model
     * @return mixed
     */
    abstract public function restore($model);


    public function search(string $search)
    {
        return app()->make($this->model)->search($search);
    }

    /**
     * search result from the query params passed in the api
     * @return QueryBuilder
     */
    public function buildQuery()
    {
        return QueryBuilder::for($this->model())
            ->allowedFilters($this->allowedFilters)
            ->allowedSorts($this->allowedSorts);
    }

    public function query(callable $callback)
    {
        return $this->model->query($callback);
    }

    public function getDeleted($orderBy = 'created_at', $sort = 'desc')
    {
        $this->model = $this->model
            ->onlyTrashed()
            ->orderBy($orderBy, $sort);
        return $this;
    }

    public function getModel()
    {
        return $this->model;
    }

    /**
     * @param $model
     * @return mixed
     */
    public function softDelete($model)
    {
        $result = $this->deleteById($model->id);

        return $result;
    }


}
