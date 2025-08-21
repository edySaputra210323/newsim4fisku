<x-filament::page>
    <x-filament::section>
        <x-slot name="heading">
            Statistik Data Siswa
        </x-slot>

        <table class="table-auto w-full border">
            <thead>
                <tr class="bg-gray-100">
                    <th class="px-4 py-2">Keterangan</th>
                    <th class="px-4 py-2">Jumlah</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($statistik as $row)
                    <tr>
                        <td class="border px-4 py-2">{{ $row->keterangan }}</td>
                        <td class="border px-4 py-2 font-bold">
                            {{ $row->jumlah }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </x-filament::section>
</x-filament::page>
