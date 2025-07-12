<x-filament::widget>
<x-filament::card>
    <h2 class="text-xl font-bold my-2">{{ $this->formattedDate }}</h2>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        @foreach ($this->groupedCategories as $category => $bills)
            <div class="border p-4 rounded-xl shadow">
                <h3 class="font-semibold">{{ $category }}</h3>
                <p class="text-sm">
                    Presupuesto: ${{ number_format($bills->first()->category->getBudgetAmount($record->date), 2) }}
                </p>

{{--
                <ul class="mt-2">
                    @foreach ($bills as $bill)
                        <li class="text-sm flex justify-between">
                            <span>{{ $bill->name }}</span>
                            <span>${{ number_format($bill->cost, 2) }}</span>
                        </li>
                    @endforeach
                </ul>

 --}}
                <hr class="my-2">
                @php
                    $total = $bills->sum('cost');
                    $presupuesto = $bills->first()?->category->getBudgetAmount($record->date) ?? 0;
                    $diff = $presupuesto - $total;
                @endphp
                <p><strong>Total:</strong> ${{ number_format($total, 2) }}</p>
                @if ($diff >= 0)
                    <p class="fi-wi-stats-overview-stat-description text-sm fi-color-custom text-custom-600 dark:text-custom-400 fi-color-success" style="--c-400:var(--success-400);--c-600:var(--success-600);"><strong>Disponible:</strong> ${{ number_format($diff, 2) }}</p>
                @else
                    <p class="fi-wi-stats-overview-stat-description text-sm fi-color-custom text-custom-600 dark:text-custom-400 fi-color-danger" style="--c-400:var(--danger-400);--c-600:var(--danger-600);"><strong>Sobrepaso:</strong> ${{ number_format(abs($diff), 2) }}</p>
                @endif
            </div>
        @endforeach
    </div>

    <hr class="my-4 border-dashed">

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

        <x-filament::card>
            <h3 class="font-bold">Gastos</h3>
            <p class="mt-2"><strong>Total:</strong> ${{ number_format($this->gastos, 2) }}</p>
            <p><strong>Ingreso:</strong> ${{ number_format($record->amount, 2) }}</p>
            <p><strong>Balance:</strong> ${{ number_format($record->amount - $this->gastos, 2) }}</p>
        </x-filament::card>

        <x-filament::card>
            <h3 class="font-bold">Gastos Extras</h3>

{{--
            <ul>
                @foreach ($this->extras as $extra)
                    <li class="flex justify-between text-sm">
                        <span>{{ $extra->name }}</span>
                        <span>${{ number_format($extra->cost, 2) }}</span>
                    </li>
                @endforeach
            </ul>

 --}}
            <p class="mt-2"><strong>Total:</strong> ${{ number_format($this->gastoExtras, 2) }}</p>
            <p><strong>Ingreso Extra:</strong> ${{ number_format($record->extra, 2) }}</p>
            <p><strong>Balance:</strong> ${{ number_format($record->extra - $this->gastoExtras, 2) }}</p>
        </x-filament::card>

        <x-filament::card>
            <h3 class="font-bold">Ahorros</h3>

{{--
            <ul>
                @foreach ($this->ahorros as $ahorro)
                    <li class="flex justify-between text-sm">
                        <span>{{ $ahorro->name }}</span>
                        <span>${{ number_format($ahorro->cost, 2) }}</span>
                    </li>
                @endforeach
            </ul>

 --}}
            <p class="mt-2"><strong>Total:</strong> ${{ number_format($this->gastoAhorros, 2) }}</p>
            <p><strong>Ingreso Ahorro:</strong> ${{ number_format($record->saving, 2) }}</p>
            <p><strong>Balance:</strong> ${{ number_format($record->saving - $this->gastoAhorros, 2) }}</p>
        </x-filament::card>
    </div>
</x-filament::card>
</x-filament::widget>
