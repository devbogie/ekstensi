<?php

namespace App\Http\Controllers\Admin;

use App\Models\Branch;

class BranchController extends ReferenceController
{
    protected function modelClass(): string
    {
        return Branch::class;
    }

    protected function singularLabel(): string
    {
        return 'Cabang';
    }

    protected function pluralLabel(): string
    {
        return 'Cabang';
    }
}
