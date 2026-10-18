<x-filament-panels::page>
    @if ($revenuePeriod !== null)
        <x-filament::section heading="Importar gastos de {{ $revenuePeriod }}">
            <p class="text-sm text-gray-600 dark:text-gray-400">
                Esta importación está limitada a los gastos de {{ $revenuePeriod }}.
                Si alguna fila pertenece a otro mes, el archivo completo será rechazado.
            </p>
        </x-filament::section>
    @else
        <x-filament::section heading="Importación general">
            <p class="text-sm text-gray-600 dark:text-gray-400">
                Se importarán todos los meses presentes en el archivo CSV.
                Para trabajar con un solo mes, utiliza Importar CSV del mes desde Ingresos &amp; Extras.
            </p>
        </x-filament::section>
    @endif

    <x-filament::section>
        <p class="text-sm text-gray-600 dark:text-gray-400">
            Sube un CSV con estas columnas en este orden:
            <code>source,external_id,name,cost,date,type,category_id,payment_method_id</code>.
            Máximo 500 filas, UTF-8. La vista previa no guarda ningún dato.
        </p>
    </x-filament::section>

    @if (count($problems))
        <x-filament::section heading="Errores del archivo">
            <ul class="list-disc ps-5 text-sm text-danger-600">
                @foreach ($problems as $problem)
                    <li>{{ $problem }}</li>
                @endforeach
            </ul>
        </x-filament::section>
    @endif

    @if ($previewSummary !== null)
        <x-filament::section heading="Vista previa">
            <div class="grid grid-cols-2 gap-4 text-sm md:grid-cols-5">
                @foreach ($previewSummary as $status => $count)
                    <div>
                        <span class="block text-gray-500">{{ $status }}</span>
                        <strong>{{ $count }}</strong>
                    </div>
                @endforeach
            </div>

            <p class="mt-4 text-sm text-gray-600 dark:text-gray-400">
                Primeras 20 filas. Las filas con estado <strong>duplicate</strong> se omitirán.
                Los conflictos o meses sin ingreso bloquean toda la importación.
            </p>

            <div class="mt-4 overflow-x-auto">
                <table class="w-full text-start text-sm">
                    <thead>
                        <tr class="border-b border-gray-200 dark:border-gray-700">
                            <th class="p-2 text-start">Fila</th>
                            <th class="p-2 text-start">Fecha</th>
                            <th class="p-2 text-start">Concepto</th>
                            <th class="p-2 text-end">Importe</th>
                            <th class="p-2 text-start">Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($previewRows as $row)
                            <tr class="border-b border-gray-100 dark:border-gray-800">
                                <td class="p-2">{{ $row['line'] }}</td>
                                <td class="p-2">{{ $row['date'] }}</td>
                                <td class="p-2">{{ $row['name'] }}</td>
                                <td class="p-2 text-end">&#36;{{ $row['cost'] }}</td>
                                <td class="p-2">{{ $row['status'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </x-filament::section>
    @endif
</x-filament-panels::page>
