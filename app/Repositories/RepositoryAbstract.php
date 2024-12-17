<?php

namespace App\Repositories;

use App\Enums\Constant;

use Illuminate\Container\Container as App;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth as AuthFace;
use Illuminate\Support\Facades\DB;

abstract class RepositoryAbstract implements RepositoryInterface
{
    /**
     * @var
     */
    protected $model;

    /**
     * @var Model
     */
    protected $originalModel;

    /**
     * @var App
     */
    private $app;

    /**
     * @var \stdClass
     */
    protected $REQUEST;

    /**
     * @var mixed
     */
    protected $ENV;

    /**
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function __construct(App $app, Request $request, Model $model)
    {
        $this->app = $app;
        $this->makeModel();
        $this->model = $model;
        $this->originalModel = $model;
        $this->initiate($request);
    }

    /**
     * Initial methods to run
     *
     * @param Request $request
     * @return void
     */
    private function initiate(Request $request): void
    {
        if ($this->USER = AuthFace::user()) {
            $this->isLogin = true;
        };
        // Set global Parameter
//        $this->current_language($request);
        $this->PERPAGE = $request->input('perpage', Constant::PER_PAGE);
        $this->PAGE = $request->input('page', 1);
        $this->REQUEST = $request->toArray();
    }

    /**
     * @return mixed
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function makeModel()
    {
        $model = $this->app->make($this->model());

        return $this->model = $model->newQuery();
    }

    /**
     * @return $this
     */
    public function resetModel()
    {
        $this->model = new $this->originalModel();
        return $this;
    }

    public function getModel()
    {
        return $this->model;
    }

    /**
     * @return mixed
     */
    abstract function model();

    /**
     * @param string[] $columns
     * @return mixed
     */
    public function all($columns = ['*'])
    {
        return $this->model->get($columns);
    }

    /**
     * @param $keyNeedUpdate
     * @param $data
     * @return mixed
     */
    public function updateOrCreate($keyNeedUpdate, $data)
    {
        return $this->updateOrCreate($keyNeedUpdate, $data);
    }

    /**
     * @param $perPage
     * @param $columns
     * @return mixed
     */
    public function paginate($perPage = 15, $columns = array('*'))
    {
        return $this->model->paginate($perPage, $columns);
    }

    /**
     * @param array $data
     * @return mixed
     */
    public function create(array $data)
    {
        return $this->model->create($data);
    }

    /**
     * @param array $data
     * @param $id
     * @param string $attribute
     * @return mixed
     */
    public function update(array $data, $id, string $attribute = "id")
    {
        return $this->model->where($attribute, '=', $id)->update($data);
    }

    /**
     * @param string $table
     * @param array $data
     * @param $id
     * @param string $attribute
     * @return int
     */
    public function updateV2(string $table, array $data, $id, string $attribute = "id"): int
    {
        return DB::table($table)->where($attribute, '=', $id)->update($data);
    }

    /**
     * @param $id
     * @return mixed
     */
    public function delete($id)
    {
        return $this->model->destroy($id);
    }

    /**
     * @param $id
     * @param array $columns
     * @return mixed
     */
    public function find($id, array $columns = ["*"])
    {
        return $this->model->find($id, $columns);
    }

    /**
     * @param $field
     * @param $value
     * @param string[] $columns
     * @return mixed
     */
    public function findBy($field, $value, $columns = array('*'))
    {
        return $this->model->where($field, '=', $value)->first($columns);
    }

    /**
     * @param $id
     * @param array $with
     * @return mixed
     */
    public function findById($id, array $with = [])
    {
        $data = $this->make($with)->where('id', $id);

        $this->resetModel();

        return $data->first();
    }

    /**
     * @param array $condition
     * @param array $select
     * @param array $with
     * @return mixed
     */
    public function getFirstBy(array $condition = [], array $select = ['*'], array $with = [])
    {
        $this->make($with);

        $this->applyConditions($condition);

        if (!empty($select)) {
            $data = $this->model->select($select);
        } else {
            $data = $this->model->select('*');
        }

        $this->resetModel();

        return $data->first();
    }

    public function getBy(array $condition = [], array $select = ['*'], array $with = [])
    {
        $this->make($with);

        $this->applyConditions($condition);

        if (!empty($select)) {
            $data = $this->model->select($select);
        } else {
            $data = $this->model->select('*');
        }

        $this->resetModel();

        return $data->get();
    }

    /**
     * @param array $where
     * @param $model
     * @return void
     */
    protected function applyConditions(array $where, &$model = null)
    {
        if (!$model) {
            $newModel = $this->model;
        } else {
            $newModel = $model;
        }

        foreach ($where as $field => $value) {
            if (is_array($value)) {
                [$field, $condition, $val] = $value;
                switch (strtoupper($condition)) {
                    case 'IN':
                        $newModel = $newModel->whereIn($field, $val);
                        break;
                    case 'NOT_IN':
                        $newModel = $newModel->whereNotIn($field, $val);
                        break;
                    default:
                        $newModel = $newModel->where($field, $condition, $val);
                        break;
                }
            } else {
                $newModel = $newModel->where($field, $value);
            }
        }

        if (!$model) {
            $this->model = $newModel;
        } else {
            $model = $newModel;
        }
    }

    /**
     * @param $data
     * @param array $condition
     * @return false|Model
     */
    public function createOrUpdate($data, array $condition = [])
    {
        /**
         * @var Model $item
         */
        if (is_array($data)) {
            if (empty($condition)) {
                $item = new $this->model();
            } else {
                $item = $this->getFirstBy($condition);
            }

            if (empty($item)) {
                $item = new $this->model();
            }

            $item = $item->fill($data);
        } elseif ($data instanceof Model) {
            $item = $data;
        } else {
            return false;
        }

        $this->resetModel();

        if ($item->save()) {
            return $item;
        }

        return false;
    }

    /**
     * @param array $with
     * @return mixed
     */
    public function make(array $with = [])
    {
        if (!empty($with)) {
            $this->model = $this->model->with($with);
        }

        return $this->model;
    }

    /**
     * @param array $condition
     * @return bool
     */
    public function deleteBy(array $condition = [])
    {
        $this->applyConditions($condition);
        $data = $this->model->get();

        if (empty($data)) {
            return false;
        }

        foreach ($data as $item) {
            $item->delete();
        }

        $this->resetModel();

        return true;
    }

    /**
     * @param array $condition
     * @param array $select
     * @return mixed
     */
    public function getFirstByWithTrash(array $condition = [], array $select = [])
    {
        $this->applyConditions($condition);

        $query = $this->model->withTrashed();


        if (!empty($select)) {
            return $query->select($select)->first();
        }

        $this->resetModel();

        return $query->first();
    }
}
