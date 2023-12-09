<?php

namespace App\Traits;

trait CheckPermissionsCrud
{
    public function checkPermissions($entityName = null)
    {
        $entity = $entityName ?? (new \ReflectionClass($this->crud->getModel()))->getShortName();

        if (! backpack_user()->can($entity.'.create')) {
            $this->crud->denyAccess('create');
        }

        if (! backpack_user()->can($entity.'.update')) {
            $this->crud->denyAccess('update');
        }

        if (! backpack_user()->can($entity.'.read')) {
            $this->crud->denyAccess('list');
        }

        if (! backpack_user()->can($entity.'.delete')) {
            $this->crud->denyAccess('delete');
        }
    }

    public function onlyAdmin()
    {
        if (! backpack_user()->isSuper()) {
            abort(401, __('crud.permission.deny'));
        }
    }
}
