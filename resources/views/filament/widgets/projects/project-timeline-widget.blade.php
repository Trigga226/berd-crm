<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">
            <div class="flex items-center space-x-2">
                <x-filament::icon
                    icon="heroicon-o-clock"
                    class="h-5 w-5 text-gray-500"
                />
                <span>Timeline {{ $project ? "du Projet" : "Globale des Projets" }}</span>
            </div>
        </x-slot>

        @if(count($deliverables) > 0)
            <div class="relative py-4">
                {{-- Ligne verticale --}}
                <div class="absolute left-4 top-0 bottom-0 w-0.5 bg-gray-200 dark:bg-gray-700 ml-[-1px]"></div>

                <div class="space-y-8 relative">
                    @foreach($deliverables as $deliverable)
                        <div class="flex items-start">
                            {{-- Point sur la ligne --}}
                            <div class="relative z-10 flex items-center justify-center">
                                @php
                                    $dotColor = match($deliverable->status) {
                                        'validated' => 'bg-success-500',
                                        'pending' => 'bg-warning-500',
                                        'rejected' => 'bg-danger-500',
                                        default => 'bg-gray-400',
                                    };
                                    $isPast = $deliverable->planned_date?->isPast() && $deliverable->status !== 'validated';
                                @endphp
                                <div class="h-8 w-8 rounded-full {{ $dotColor }} border-4 border-white dark:border-gray-900 flex items-center justify-center text-white">
                                    @if($deliverable->status === 'validated')
                                        <x-filament::icon icon="heroicon-m-check" class="h-4 w-4" />
                                    @elseif($isPast)
                                        <x-filament::icon icon="heroicon-m-exclamation-triangle" class="h-4 w-4" />
                                    @else
                                        <span class="text-[10px] font-bold">{{ $loop->iteration }}</span>
                                    @endif
                                </div>
                            </div>

                            <div class="ml-6 flex-1 bg-white dark:bg-gray-800 p-4 rounded-xl border border-gray-100 dark:border-gray-700 shadow-sm hover:shadow-md transition-shadow">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <h4 class="text-sm font-bold text-gray-900 dark:text-white flex items-center">
                                            @unless($project)
                                                <span class="text-primary-600 mr-2">[{{ $deliverable->project->code ?? 'PRJ' }}]</span>
                                            @endunless
                                            {{ $deliverable->title }}
                                            @if($deliverable->is_milestone)
                                                <x-filament::badge color="primary" class="ml-2 text-[10px]">Jalon</x-filament::badge>
                                            @endif
                                        </h4>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 line-clamp-1">
                                            {{ $deliverable->description ?: 'Pas de description' }}
                                        </p>
                                    </div>
                                    <div class="text-right">
                                        <div class="text-xs font-bold {{ $isPast ? 'text-danger-600' : 'text-gray-900 dark:text-white' }}">
                                            {{ $deliverable->planned_date?->format('d M Y') ?: '—' }}
                                        </div>
                                        <div class="text-[10px] text-gray-500 uppercase tracking-wider mt-1">
                                            Date prévue
                                        </div>
                                    </div>
                                </div>

                                @if($deliverable->status === 'validated' && $deliverable->actual_date)
                                    <div class="mt-3 pt-3 border-t border-gray-50 dark:border-gray-700 flex items-center text-[10px] text-success-600 font-medium">
                                        <x-filament::icon icon="heroicon-m-check-circle" class="h-3 w-3 mr-1" />
                                        Validé le {{ $deliverable->actual_date->format('d/m/Y') }}
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @else
            <div class="py-12 text-center">
                <x-filament::icon
                    icon="heroicon-o-calendar"
                    class="mx-auto h-12 w-12 text-gray-300 dark:text-gray-600"
                />
                <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">Aucun livrable</h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Ajoutez des livrables pour voir la timeline.</p>
            </div>
        @endif
    </x-filament::section>
</x-filament-widgets::widget>
