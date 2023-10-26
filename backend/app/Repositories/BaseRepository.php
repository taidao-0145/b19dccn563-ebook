<?php

namespace App\Repositories;

use App\Repositories\Interfaces\RepositoryInterface;

abstract class BaseRepository implements RepositoryInterface
{
    /**
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $_model;

    /**
     * BaseRepository constructor.
     */
    public function __construct()
    {
        $this->setModel();
    }

    /**
     * get model
     * @return string
     */
    abstract public function getModel();

    /**
     * Set model
     */
    public function setModel()
    {
        $this->_model = app()->make(
            $this->getModel()
        );
    }

    /**
     * Return the query builder order by the specified attribute
     *
     * @param string $attribute
     * @param string $dir
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function orderBy($attribute, $dir = 'asc')
    {
        return $this->_model->orderBy($attribute, $dir);
    }

    /**
     * Get All
     *
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public function getAll()
    {
        return $this->_model->all();
    }

    /**
     * Count the number of specified model records in the database
     *
     * @return int
     */
    public function count()
    {
        return $this->_model->count();
    }

    /**
     * Find a model instance by its attribute.
     *
     * @param string $attribute
     * @param mixed  $value
     * @param bool   $shouldThrowException
     * @return mixed
     */
    public function findBy($attribute, $value, $shouldThrowException = true)
    {
        $result = $this->_model->where($attribute, $value);

        return $shouldThrowException ? $result->firstOrFail() : $result->first();
    }

    /**
     * Find a model instance by its ID.
     *
     * @param $id
     * @return mixed
     */
    public function find($id)
    {
        $result = $this->_model->findOrFail($id);

        return $result;
    }

    /**
     * Get the first specified model record from the database
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function first()
    {
        $result = $this->_model->firstOrFail();

        return $result;
    }

    /**
     * Find entities by their attribute values.
     *
     * @param string $attribute
     * @param array  $values
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function whereIn($attribute, array $values)
    {
        return $this->_model->whereIn($attribute, $values);
    }

    /**
     * Find data by multiple fields
     *
     * @param string $attribute
     * @param string $condition
     * @param string $value
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function where($attribute, $condition, $value)
    {
        return $this->_model->where($attribute, $condition, $value);
    }

    /**
     * Create
     *
     * @param array $attributes
     * @return mixed
     */
    public function create(array $attributes)
    {
        return $this->_model->create($attributes);
    }

    /**
     * Update
     *
     * @param $id
     * @param array $attributes
     * @return bool|mixed
     */
    public function update($id, array $attributes)
    {
        $result = $this->find($id);
        if ($result) {
            $result->update($attributes);

            return $result;
        }

        return false;
    }

    /**
     * Update or Create an model in repository
     *
     * @param array $attributes
     * @param array $values
     * @return mixed
     */
    public function updateOrCreate(array $attributes, array $values = [])
    {
        $result = $this->_model->updateOrCreate($attributes, $values);

        return $result;
    }

    /**
     * Delete
     *
     * @param $id
     * @return bool
     */
    public function delete($id)
    {
        $result = $this->find($id);
        if ($result) {
            $result->delete();

            return true;
        }

        return false;
    }

    /**
     * Insert
     *
     * @param $data
     * @return mixed
     */
    public function insert($data)
    {
        return $this->_model->insert($data);
    }

    /**
     * Check duplicate field value
     *
     * @param string $fieldName
     * @param string $value
     * @return bool
     */
    public function isExist($fieldName, $value)
    {
        return $this->_model->where($fieldName, $value)->exists();
    }
}
