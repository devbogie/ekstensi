<?php

namespace App\Imports;

use App\Models\Branch;
use App\Models\Department;
use App\Models\Extension;
use App\Models\Floor;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class ExtensionsImport implements ToModel, WithChunkReading, WithHeadingRow, WithValidation
{
    private int $created = 0;

    /** @var array<string, array<string, int>> Cache nama -> ID per model referensi */
    private array $cache = [];

    public function model(array $row): ?Extension
    {
        $number = $this->normalizeNumber($row['nomor_ekstensi'] ?? null);

        if ($number === null) {
            return null;
        }

        $name = $this->clean($row['nama'] ?? null) ?? 'Tanpa Nama';

        $extension = Extension::create([
            'number' => $number,
            'name' => $name,
            'department_id' => $this->resolve(Department::class, $row['departemen'] ?? null),
            'floor_id' => $this->resolve(Floor::class, $row['lantai'] ?? null),
            'branch_id' => $this->resolve(Branch::class, $row['cabang'] ?? null),
        ]);

        $this->created++;

        return $extension;
    }

    public function rules(): array
    {
        return [
            'nomor_ekstensi' => ['required', function (string $attribute, mixed $value, \Closure $fail) {
                $number = $this->normalizeNumber($value);

                if ($number === null || $number === '') {
                    $fail('The nomor_ekstensi field is required.');
                }

                if ($number !== null && mb_strlen($number) > 32) {
                    $fail('The nomor_ekstensi field must not be greater than 32 characters.');
                }
            }],
        ];
    }

    public function chunkSize(): int
    {
        return 500;
    }

    public function created(): int
    {
        return $this->created;
    }

    private function resolve(string $model, mixed $value): ?int
    {
        $name = $this->clean($value);

        if ($name === null) {
            return null;
        }

        return $this->cache[$model][$name] ??= $model::firstOrCreate(['name' => $name])->id;
    }

    private function normalizeNumber(mixed $value): ?string
    {
        $number = $this->clean($value);

        return $number === null ? null : preg_replace('/\s+/u', '', $number);
    }

    private function clean(mixed $value): ?string
    {
        $value = Str::of((string) ($value ?? ''))->trim();

        return $value->isNotEmpty() ? (string) $value : null;
    }
}
