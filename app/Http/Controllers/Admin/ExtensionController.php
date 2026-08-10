<?php

namespace App\Http\Controllers\Admin;

use App\Exports\ExtensionsExport;
use App\Exports\ExtensionsTemplate;
use App\Http\Controllers\Controller;
use App\Imports\ExtensionsImport;
use App\Models\Branch;
use App\Models\Department;
use App\Models\Extension;
use App\Models\Floor;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ExtensionController extends Controller
{
    public function index(Request $request)
    {
        return view('admin.extensions.index', [
            'departments' => Department::orderBy('name')->get(),
            'floors' => Floor::orderBy('name')->get(),
            'branches' => Branch::orderBy('name')->get(),
            'extensions' => $this->query($request)->paginate(10)->withQueryString(),
        ]);
    }

    public function data(Request $request)
    {
        return view('admin.extensions.partials.table', [
            'extensions' => $this->query($request)->paginate(10)->withQueryString(),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $this->validateData($request);

        Extension::create($data);

        return response()->json(['message' => 'Ekstensi berhasil ditambahkan.'], 201);
    }

    public function update(Request $request, Extension $extension): JsonResponse
    {
        $data = $this->validateData($request, $extension);

        $extension->update($data);

        return response()->json(['message' => 'Ekstensi berhasil diperbarui.']);
    }

    public function destroy(Extension $extension): JsonResponse
    {
        $extension->delete();

        return response()->json(['message' => 'Ekstensi berhasil dihapus.']);
    }

    public function export(Request $request): BinaryFileResponse
    {
        $extensions = $this->query($request)->get();

        return Excel::download(new ExtensionsExport($extensions), 'data-ekstensi.xlsx');
    }

    public function downloadTemplate(): BinaryFileResponse
    {
        return Excel::download(new ExtensionsTemplate, 'template-import-ekstensi.xlsx');
    }

    public function import(Request $request): JsonResponse
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:xlsx,xls', 'max:1024'],
        ]);

        try {
            $import = new ExtensionsImport;
            Excel::import($import, $request->file('file'));

            $message = "Import selesai. {$import->created()} ekstensi baru ditambahkan.";

            return response()->json(['message' => $message]);
        } catch (ValidationException $e) {
            $first = $e->errors()[array_key_first($e->errors())][0] ?? 'Format data tidak valid.';

            return response()->json(['message' => "Import gagal: {$first}"], 422);
        }
    }

    private function query(Request $request)
    {
        $query = Extension::query()->with(['department', 'floor', 'branch']);

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

    private function validateData(Request $request, ?Extension $extension = null): array
    {
        return $request->validate([
            'number' => [
                'required',
                'string',
                'max:32',
            ],
            'name' => ['required', 'string', 'max:255'],
            'department_id' => ['nullable', 'exists:departments,id'],
            'floor_id' => ['nullable', 'exists:floors,id'],
            'branch_id' => ['nullable', 'exists:branches,id'],
            'status' => ['required', 'boolean'],
        ]);
    }
}
