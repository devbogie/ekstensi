@php
    // Pencegahan formula injection saat file dibuka di Excel:
    // sel yang diawali =, +, -, @ diberi prefix apostrof agar diperlakukan sebagai teks.
    $safe = fn (mixed $value): string => (string) (preg_match('/^[=+\-@]/', (string) $value) ? "'" . $value : $value);
@endphp
<table>
    <thead>
        <tr>
            <th>Nomor Ekstensi</th>
            <th>Nama</th>
            <th>Departemen</th>
            <th>Lantai</th>
            <th>Cabang</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($extensions as $extension)
            <tr>
                <td>{{ $safe($extension->number) }}</td>
                <td>{{ $safe($extension->name) }}</td>
                <td>{{ $safe($extension->department?->name ?? '') }}</td>
                <td>{{ $safe($extension->floor?->name ?? '') }}</td>
                <td>{{ $safe($extension->branch?->name ?? '') }}</td>
            </tr>
        @endforeach
    </tbody>
</table>