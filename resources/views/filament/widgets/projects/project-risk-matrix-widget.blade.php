<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">
            <div class="flex items-center space-x-2">
                <x-filament::icon
                    icon="heroicon-o-shield-exclamation"
                    class="h-5 w-5 text-gray-500"
                />
                <span>Matrice des Risques {{ $project ? "- {$project->title}" : "Globale" }}</span>
            </div>
        </x-slot>

        <div class="mt-4 grid grid-cols-4 gap-2">
            {{-- Libellés Impact (Y-axis) --}}
            <div class="col-span-1 flex flex-col justify-between py-8 text-right pr-4 italic text-xs text-gray-500">
                <div class="h-1/3 flex items-center justify-end">Élevé</div>
                <div class="h-1/3 flex items-center justify-end">Moyen</div>
                <div class="h-1/3 flex items-center justify-end">Faible</div>
            </div>

            {{-- Grille de la matrice --}}
            <div class="col-span-3 grid grid-cols-3 grid-rows-3 gap-2 aspect-square max-w-md">
                @foreach(['high', 'medium', 'low'] as $impact)
                    @foreach(['low', 'medium', 'high'] as $prob)
                        @php
                            $risks = $matrix[$impact][$prob];
                            $count = count($risks);
                            $bgColor = match(true) {
                                ($impact === 'high' && $prob === 'high') || ($impact === 'high' && $prob === 'medium') || ($impact === 'medium' && $prob === 'high') => 'bg-danger-50 dark:bg-danger-900/20 border-danger-200 dark:border-danger-800',
                                ($impact === 'low' && $prob === 'low') => 'bg-success-50 dark:bg-success-900/20 border-success-200 dark:border-success-800',
                                default => 'bg-warning-50 dark:bg-warning-900/20 border-warning-200 dark:border-warning-800',
                            };
                            $badgeColor = match(true) {
                                ($impact === 'high' && $prob === 'high') || ($impact === 'high' && $prob === 'medium') || ($impact === 'medium' && $prob === 'high') => 'danger',
                                ($impact === 'low' && $prob === 'low') => 'success',
                                default => 'warning',
                            };
                        @endphp

                        <div class="border rounded-lg {{ $bgColor }} flex flex-col items-center justify-center p-2 group relative cursor-help">
                            @if($count > 0)
                                <x-filament::badge :color="$badgeColor" size="lg" class="scale-125">
                                    {{ $count }}
                                </x-filament::badge>
                                
                                {{-- Tooltip --}}
                                <div class="absolute z-20 hidden group-hover:block bottom-full mb-2 w-64 bg-gray-900 text-white text-[10px] p-2 rounded shadow-xl pointer-events-none">
                                    <ul class="list-disc list-inside">
                                        @foreach($risks as $risk)
                                            <li class="truncate">
                                                @unless($project)
                                                    <span class="font-bold">[{{ $risk->project->code ?? 'PRJ' }}]</span>
                                                @endunless
                                                {{ $risk->title }}
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                        </div>
                    @endforeach
                @endforeach
            </div>

            {{-- Libellés Probabilité (X-axis) --}}
            <div class="col-span-1"></div>
            <div class="col-span-3 grid grid-cols-3 gap-2 text-center pt-2 italic text-xs text-gray-500">
                <div>Faible</div>
                <div>Moyen</div>
                <div>Élevé</div>
            </div>
        </div>

        <div class="mt-6 flex justify-center items-center space-x-6 text-[10px] uppercase tracking-wider text-gray-400">
            <span class="flex items-center"><span class="h-2 w-2 rounded-full bg-success-500 mr-2"></span>Risque Faible</span>
            <span class="flex items-center"><span class="h-2 w-2 rounded-full bg-warning-500 mr-2"></span>Risque Modéré</span>
            <span class="flex items-center"><span class="h-2 w-2 rounded-full bg-danger-500 mr-2"></span>Risque Critique</span>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
