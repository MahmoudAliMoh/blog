<?php

namespace App\Services\Categories;

use App\Contracts\Categories\CategoryRepositoryContract;
use App\Contracts\Categories\CategoryServiceContract;
use App\Contracts\Categories\CategoryValidatorContract;
use App\Transformers\CategoriesTransformer;
use Illuminate\Support\Facades\DB;
use Spatie\Fractal\FractalFacade;

class CategoriesService implements CategoryServiceContract
{
    /*
     * repository instance from CategoriesRepositoryContract.
     */
    protected $repository;

    /*
     * validator instance from CategoriesValidatorContract.
     */
    protected $validator;

    /**
     * CategoriesService constructor.
     * @param CategoryRepositoryContract $repository
     * @param CategoryValidatorContract $validator
     */
    public function __construct(CategoryRepositoryContract $repository, CategoryValidatorContract $validator)
    {
        $this->repository = $repository;
        $this->validator = $validator;
    }

    /**
     * Validate request of categories;
     *
     * @param $data
     * @return void
     */
    private function validator($data): void
    {
        $this->validator->validations($data);
    }

    /**
     * Validate and store categories data.
     *
     * @param $data
     * @return bool
     * @throws /Exception
     */
    public function store($data): bool
    {
        $this->validator($data);
        DB::beginTransaction();

        try {

            $categoryData = [
                'name' => $data['name']
            ];

            $this->repository->store($categoryData);

            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();
            throw $exception;
        }

        return true;
    }

    /**
     * List all categories data;
     *
     * @return array
     */
    public function list(): array
    {
        $categories = $this->repository->list();
        $categoriesData = FractalFacade::collection($categories)
            ->transformWith(new CategoriesTransformer())
            ->toArray();
        return $categoriesData;
    }

    /**
     * Destroy category by id.
     *
     * @param int $id
     * @return bool
     * @throws /Exception
     */
    public function destroy(int $id): bool
    {
        DB::beginTransaction();

        try {
            $this->repository->destroy($id);
            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();
            throw $exception;
        }

        return true;
    }

    /**
     * Show category data by id.
     *
     * @param int $id
     * @return array
     */
    public function show(int $id): array
    {
        $category = $this->repository->show($id);
        $categoryData = FractalFacade::item($category)
            ->transformWith(new CategoriesTransformer())
            ->toArray();
        return $categoryData;
    }

    /**
     * Update category data by id.
     *
     * @param array $data
     * @param int $id
     * @return bool
     * @throws /Exception
     */
    public function update(int $id, array $data): bool
    {
        $this->validator($data);
        DB::beginTransaction();

        try {

            $categoryData = [
                'name' => $data['name']
            ];

            $this->repository->update($id, $categoryData);

            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();
            throw $exception;
        }

        return true;
    }
}
