<?php

namespace App\Repositories;

interface RepositoryInterface
{
    public function all(array $columns = array('*'));

    public function updateOrCreate($keyNeedUpdate, $data);

    public function paginate(int $perPage = 15, array $columns = array('*'));

    public function create(array $data);

    public function update(array $data, int $id);

    public function updateV2(string $table, array $data, int $id);

    public function delete(int $id);

    public function find(int $id, array $columns = array('*'));

    public function findBy(string $field, string $value, array $columns = array('*'));

    public function createOrUpdate($data, array $condition = []);

    public function deleteBy(array $condition = []);

    public function getFirstBy(array $condition = [], array $select = ['*'], array $with = []);

    public function getBy(array $condition = [], array $select = ['*'], array $with = []);

    public function findById($id, array $with = []);

    public function getModel();

    public function getFirstByWithTrash(array $condition = [], array $select = []);
}
