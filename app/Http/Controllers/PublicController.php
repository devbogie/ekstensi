<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Department;
use App\Models\Extension;
use App\Models\Floor;
use Illuminate\Http\Request;

class PublicController extends Controller
{
    public function index(Request $request)
    {
        return view('directory.index', [
            'departments' => Department::orderBy('name')->get(),
            'floors' => Floor::orderBy('name')->get(),
            'branches' => Branch::orderBy('name')->get(),
            'filterOptions' => $this->filterOptions(),
            'extensions' => $this->query($request)->paginate(10)->withQueryString(),
        ]);
    }

    public function data(Request $request)
    {
        return view('directory.partials.table', [
            'filterOptions' => $this->filterOptions(),
            'extensions' => $this->query($request)->paginate(10)->withQueryString(),
        ]);
    }

    private function filterOptions(): array
    {
        return [
            'department' => Department::orderBy('name')->get(),
            'floor' => Floor::orderBy('name')->get(),
            'branch' => Branch::orderBy('name')->get(),
        ];
    }

    private function query(Request $request)
    {
        $query = Extension::query()
            ->with(['department', 'floor', 'branch'])
            ->where('status', true);

        $search = trim((string) $request->string('search'));

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('number', 'like', "%{$search}%")
                    ->orWhere('name', 'like', "%{$search}%")
                    ->orWhereHas('department', fn ($q2) => $q2->where('name', 'like', "%{$search}%"))
                    ->orWhereHas('floor', fn ($q2) => $q2->where('name', 'like', "%{$search}%"))
                    ->orWhereHas('branch', fn ($q2) => $q2->where('name', 'like', "%{$search}%"));
            });
        }

        foreach (['department', 'floor', 'branch'] as $filter) {
            $value = $request->integer($filter);

            if ($value > 0) {
                $query->where("{$filter}_id", $value);
            }
        }

        return $query->orderBy('number');
    }
}
