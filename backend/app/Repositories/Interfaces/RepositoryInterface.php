<?php

namespace App\Repositories\Interfaces;

interface RepositoryInterface
{
    /**
     * Order by
     * @param string $attribute
     * @param string $dir
     * @return mixed
     */
    public function orderBy($attribute, $dir);

    /**
     * Get all
     * @return mixed
     */
    public function getAll();

    /**
     * Count
     * @return int
     */
    public function count();

    /**
     * Find a model instance by its attribute.
     * @param string $attribute
     * @param mixed  $value
     * @param bool   $shouldThrowException
     * @return mixed
     */
    public function findBy($attribute, $value, $shouldThrowException);

    /**
     * Find a model instance by its ID.
     * @param $id
     * @return mixed
     */
    public function find($id);

    /**
     * Get the first specified model record from the database
     * @return mixed
     */
    public function first();

    /**
     * Find entities by their attribute values.
     * @param string $attribute
     * @param array  $values
     * @return mixed
     */
    public function whereIn($attribute, array $values);

    /**
     * Find data by multiple fields
     * @param string $attribute
     * @param string $condition
     * @param string $value
     * @return mixed
     */
    public function where($attribute, $condition, $value);

    /**
     * Create
     * @param array $attributes
     * @return mixed
     */
    public function create(array $attributes);

    /**
     * Update
     * @param $id
     * @param array $attributes
     * @return mixed
     */
    public function update($id, array $attributes);

    /**
     * Update or Create an model in repository
     * @param array $attributes
     * @param array $values
     * @return mixed
     */
    public function updateOrCreate(array $attributes, array $values);

    /**
     * Delete
     * @param $id
     * @return mixed
     */
    public function delete($id);

    /**
     * Add multi row to table
     * @param $data
     * @return true/false
     */
    public function insert($data);
}
