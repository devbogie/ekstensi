<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

abstract class ReferenceController extends Controller
{
    abstract protected function modelClass(): string;

    abstract protected function singularLabel(): string;

    abstract protected function pluralLabel(): string;

    public function index(Request $request)
    {
        return view('admin.reference.index', [
            'title' => $this->pluralLabel(),
            'singular' => $this->singularLabel(),
            'items' => $this->query($request)->paginate(10)->withQueryString(),
        ]);
    }

    public function data(Request $request)
    {
        return view('admin.reference.partials.table', [
            'singular' => $this->singularLabel(),
            'items' => $this->query($request)->paginate(10)->withQueryString(),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $model = $this->modelClass();

        $data = $this->validateData($request);

        $model::create($data);

        return response()->json(['message' => "{$this->singularLabel()} berhasil ditambahkan."], 201);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $model = $this->modelClass();
        $record = $model::findOrFail($id);

        $record->update($this->validateData($request, $record));

        return response()->json(['message' => "{$this->singularLabel()} berhasil diperbarui."]);
    }

    public function destroy(int $id): JsonResponse
    {
        $model = $this->modelClass();
        $record = $model::withCount('extensions')->findOrFail($id);

        if ($record->extensions_count > 0) {
            throw ValidationException::withMessages([
                'record' => "Tidak dapat dihapus: masih digunakan oleh {$record->extensions_count} ekstensi.",
            ]);
        }

        $record->delete();

        return response()->json(['message' => "{$this->singularLabel()} berhasil dihapus."]);
    }

    protected function query(Request $request)
    {
        $model = $this->modelClass();

        $query = $model::withCount('extensions');

        $search = trim((string) $request->string('search'));

        if ($search !== '') {
            $query->where('name', 'like', "%{$search}%");
        }

        return $query->orderBy('name');
    }

    protected function validateData(Request $request, $record = null): array
    {
        $model = $this->modelClass();

        return $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique(app($model)->getTable(), 'name')->ignore($record?->id),
            ],
        ]);
    }
}
