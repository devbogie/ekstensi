<?php

namespace App\Http\Controllers\Admin;

use App\Models\Floor;

class FloorController extends ReferenceController
{
    protected function modelClass(): string
    {
        return Floor::class;
    }

    protected function singularLabel(): string
    {
        return 'Lantai';
    }

    protected function pluralLabel(): string
    {
        return 'Lantai';
    }
}
