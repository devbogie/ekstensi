<?php

namespace App\Http\Controllers\Admin;

use App\Models\Department;

class DepartmentController extends ReferenceController
{
    protected function modelClass(): string
    {
        return Department::class;
    }

    protected function singularLabel(): string
    {
        return 'Departemen';
    }

    protected function pluralLabel(): string
    {
        return 'Departemen';
    }
}
