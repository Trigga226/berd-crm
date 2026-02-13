<div class="space-y-6">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Titre -->
        <div class="col-span-full">
            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Titre</h3>
            <p class="mt-1 text-base font-semibold text-gray-900 dark:text-gray-100">{{ $record->titre }}</p>
        </div>

        <!-- Type -->
        <div>
            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Type</h3>
            <div class="mt-1">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                    @if($record->type === 'info') bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200
                    @elseif($record->type === 'success') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                    @elseif($record->type === 'warning') bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200
                    @elseif($record->type === 'danger') bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200
                    @else bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200
                    @endif">
                    {{ ucfirst($record->type) }}
                </span>
            </div>
        </div>

        <!-- Auteur -->
        <div>
            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Auteur</h3>
            <p class="mt-1 text-base text-gray-900 dark:text-gray-100 flex items-center gap-2">
                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                </svg>
                {{ $record->auteur }}
            </p>
        </div>

        <!-- Date -->
        <div>
            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Date</h3>
            <p class="mt-1 text-base text-gray-900 dark:text-gray-100 flex items-center gap-2">
                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
                {{ \Carbon\Carbon::parse($record->date)->format('d/m/Y') }}
            </p>
        </div>

        <!-- Dates de création et modification -->
        <div>
            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Créé le</h3>
            <p class="mt-1 text-sm text-gray-700 dark:text-gray-300">
                {{ $record->created_at?->format('d/m/Y à H:i') ?? 'N/A' }}
            </p>
        </div>

        <div>
            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Modifié le</h3>
            <p class="mt-1 text-sm text-gray-700 dark:text-gray-300">
                {{ $record->updated_at?->format('d/m/Y à H:i') ?? 'N/A' }}
            </p>
        </div>

        <!-- Description -->
        <div class="col-span-full">
            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Description</h3>
            <div class="mt-2 p-4 bg-gray-50 dark:bg-gray-800 rounded-lg">
                <p class="text-base text-gray-900 dark:text-gray-100 whitespace-pre-wrap">{{ $record->description }}</p>
            </div>
        </div>
    </div>
</div>
