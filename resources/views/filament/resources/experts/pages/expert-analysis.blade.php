<x-filament-panels::page>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        @keyframes slideInRight {
            from {
                opacity: 0;
                transform: translateX(20px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }
        
        @keyframes slideInLeft {
            from {
                opacity: 0;
                transform: translateX(-20px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }
        
        .message-user {
            animation: slideInRight 0.3s ease-out;
        }
        
        .message-ai {
            animation: slideInLeft 0.3s ease-out;
        }
        
        .expert-card {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .expert-card:hover {
            transform: translateX(4px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
        
        .skill-badge {
            transition: all 0.2s ease;
        }
        
        .skill-badge:hover {
            transform: scale(1.1);
        }
        
        .chat-gradient {
            background: linear-gradient(to bottom, #f9fafb 0%, #ffffff 100%);
        }
        
        .dark .chat-gradient {
            background: linear-gradient(to bottom, #111827 0%, #1f2937 100%);
        }
        
        .send-button {
            transition: all 0.2s ease;
        }
        
        .send-button:hover:not(:disabled) {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(79, 70, 229, 0.4);
        }
        
        .send-button:active:not(:disabled) {
            transform: translateY(0);
        }
        
        .typing-indicator span {
            animation: bounce 1.4s infinite ease-in-out both;
        }
        
        .typing-indicator span:nth-child(1) {
            animation-delay: -0.32s;
        }
        
        .typing-indicator span:nth-child(2) {
            animation-delay: -0.16s;
        }
        
        @keyframes bounce {
            0%, 80%, 100% { 
                transform: scale(0);
                opacity: 0.5;
            }
            40% { 
                transform: scale(1);
                opacity: 1;
            }
        }
        
        .scrollbar-thin::-webkit-scrollbar {
            width: 6px;
        }
        
        .scrollbar-thin::-webkit-scrollbar-track {
            background: transparent;
        }
        
        .scrollbar-thin::-webkit-scrollbar-thumb {
            background: #d1d5db;
            border-radius: 3px;
        }
        
        .scrollbar-thin::-webkit-scrollbar-thumb:hover {
            background: #9ca3af;
        }
        
        .dark .scrollbar-thin::-webkit-scrollbar-thumb {
            background: #4b5563;
        }
        
        .dark .scrollbar-thin::-webkit-scrollbar-thumb:hover {
            background: #6b7280;
        }
    </style>

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6 h-full">
        
        <!-- Sidebar: Selected Experts -->
        <div class="lg:col-span-1 bg-white dark:bg-gray-900 rounded-2xl shadow-lg border border-gray-100 dark:border-gray-800 flex flex-col overflow-hidden">
            <!-- Sidebar Header -->
            <div class="p-5 bg-gradient-to-r from-indigo-500 to-purple-600 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="font-bold text-lg">Experts Sélectionnés</h3>
                        <p class="text-xs text-indigo-100 mt-1">Pool de candidatures</p>
                    </div>
                    <div class="bg-white/20 backdrop-blur-sm px-3 py-1.5 rounded-full">
                        <span class="font-bold text-lg">{{ count($this->experts) }}</span>
                    </div>
                </div>
            </div>
            
            <!-- Experts List -->
            <div class="flex-1 overflow-y-auto p-4 space-y-3 scrollbar-thin">
                @forelse($this->experts as $index => $expert)
                    <div class="expert-card p-4 bg-gradient-to-br from-gray-50 to-white dark:from-gray-800 dark:to-gray-850 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm hover:border-indigo-300 dark:hover:border-indigo-600">
                        <!-- Expert Header -->
                        <div class="flex items-start justify-between mb-3">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full bg-gradient-to-br from-indigo-400 to-purple-500 flex items-center justify-center text-white font-bold shadow-md">
                                    {{ strtoupper(substr($expert->first_name, 0, 1) . substr($expert->last_name, 0, 1)) }}
                                </div>
                                <div class="flex-1">
                                    <div class="font-semibold text-gray-900 dark:text-gray-100 text-sm">
                                        {{ $expert->first_name }} {{ $expert->last_name }}
                                    </div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                                        {{ $expert->title ?? ($expert->years_of_experience . ' ans d\'exp.') }}
                                    </div>
                                </div>
                            </div>
                            <div class="text-xs text-gray-400 font-mono">#{{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}</div>
                        </div>
                        
                        <!-- Skills -->
                        @if($expert->skills && count($expert->skills) > 0)
                            <div class="flex flex-wrap gap-1.5">
                                @foreach(array_slice($expert->skills, 0, 4) as $skill)
                                    <span class="skill-badge px-2 py-1 bg-indigo-50 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-300 rounded-md text-[10px] font-medium border border-indigo-100 dark:border-indigo-800">
                                        {{ $skill }}
                                    </span>
                                @endforeach
                                @if(count($expert->skills) > 4)
                                    <span class="px-2 py-1 bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400 rounded-md text-[10px] font-medium">
                                        +{{ count($expert->skills) - 4 }}
                                    </span>
                                @endif
                            </div>
                        @endif
                    </div>
                @empty
                    <div class="flex flex-col items-center justify-center py-12 text-center">
                        <div class="w-16 h-16 rounded-full bg-gray-100 dark:bg-gray-800 flex items-center justify-center mb-3">
                            <x-heroicon-o-users class="w-8 h-8 text-gray-400" />
                        </div>
                        <p class="text-gray-500 dark:text-gray-400 text-sm font-medium">Aucun expert sélectionné</p>
                        <p class="text-gray-400 dark:text-gray-500 text-xs mt-1">Ajoutez des experts pour commencer</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Main Chat Area -->
        <div class="lg:col-span-3 bg-white dark:bg-gray-900 rounded-2xl shadow-lg border border-gray-100 dark:border-gray-800 flex flex-col overflow-hidden" style="height: 750px;">
            
            <!-- Chat Header -->
            <div class="p-5 border-b border-gray-200 dark:border-gray-800 bg-gradient-to-r from-indigo-50 to-purple-50 dark:from-gray-800 dark:to-gray-850">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <div class="relative">
                            <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center shadow-lg">
                                <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                                </svg>
                            </div>
                            <div class="absolute -bottom-1 -right-1 w-4 h-4 bg-green-500 border-2 border-white dark:border-gray-900 rounded-full"></div>
                        </div>
                        <div>
                            <h2 class="font-bold text-xl text-gray-900 dark:text-gray-100">Lara</h2>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Assistant IA • Analyse de candidatures</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        {{ $this->exportPdfAction }}
                        
                        <div class="flex items-center gap-2 px-3 py-1.5 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 text-green-700 dark:text-green-400 rounded-full">
                            <div class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></div>
                            <span class="text-xs font-medium">Mistral AI</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Chat Messages -->
            <div class="flex-1 overflow-y-auto p-6 space-y-6 chat-gradient scrollbar-thin" id="chat-container">
                @foreach($messages as $msg)
                    <div class="flex gap-3 {{ $msg['role'] === 'user' ? 'flex-row-reverse message-user' : 'message-ai' }}">
                        <div class="flex-shrink-0">
                            @if($msg['role'] === 'user')
                                <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-gray-600 to-gray-800 flex items-center justify-center shadow-md">
                                    <x-heroicon-s-user class="w-5 h-5 text-white" />
                                </div>
                            @else
                                <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center shadow-md">
                                    <span class="text-white font-bold text-sm">LA</span>
                                </div>
                            @endif
                        </div>
                        <div class="flex-1 max-w-[85%]">
                            <div class="{{ $msg['role'] === 'user' ? 'bg-gradient-to-br from-indigo-600 to-purple-600 text-white' : 'bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 border border-gray-200 dark:border-gray-700' }} p-4 rounded-2xl {{ $msg['role'] === 'user' ? 'rounded-tr-md' : 'rounded-tl-md' }} shadow-sm">
                                <div class="text-sm prose prose-sm {{ $msg['role'] === 'user' ? 'prose-invert' : 'dark:prose-invert' }} max-w-none leading-relaxed">
                                    {!! Str::markdown($msg['content']) !!}
                                </div>
                            </div>
                            <div class="text-xs text-gray-400 mt-2 {{ $msg['role'] === 'user' ? 'text-right mr-1' : 'ml-1' }}">
                                {{ $msg['time'] ?? 'Now' }}
                            </div>
                        </div>
                    </div>
                @endforeach

                @if($isAnalyzing)
                    <div class="flex gap-3 message-ai">
                        <div class="flex-shrink-0">
                            <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center shadow-md">
                                <span class="text-white font-bold text-sm">LA</span>
                            </div>
                        </div>
                        <div class="flex-1 max-w-[85%]">
                            <div class="bg-white dark:bg-gray-800 p-4 rounded-2xl rounded-tl-md shadow-sm border border-gray-200 dark:border-gray-700">
                                <div class="flex items-center gap-3">
                                    <div class="typing-indicator flex gap-1">
                                        <span class="w-2 h-2 bg-indigo-500 rounded-full"></span>
                                        <span class="w-2 h-2 bg-indigo-500 rounded-full"></span>
                                        <span class="w-2 h-2 bg-indigo-500 rounded-full"></span>
                                    </div>
                                    <span class="text-sm text-gray-500 dark:text-gray-400">Analyse en cours des profils...</span>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Input Area -->
            <div class="p-5 border-t border-gray-200 dark:border-gray-800 bg-gray-50 dark:bg-gray-850">
                <form wire:submit.prevent="sendMessage" class="space-y-3">
                    <div class="relative">
                        <textarea 
                            wire:model="userInput" 
                            class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-800 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition-all resize-none pr-12" 
                            rows="3" 
                            placeholder="💬 Décrivez le profil recherché (compétences, langues, expérience, soft skills...)"></textarea>
                        
                        <!-- Attachment Button -->
                        <label class="absolute right-3 bottom-3 cursor-pointer group">
                            <div class="p-2 text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400 hover:bg-indigo-50 dark:hover:bg-indigo-900/20 rounded-lg transition-all">
                                <x-heroicon-m-paper-clip class="w-5 h-5" />
                            </div>
                            <input type="file" wire:model="attachment" class="hidden" accept=".pdf">
                        </label>
                    </div>
                    
                    <!-- File Upload Preview -->
                    @if($attachment)
                        <div class="flex items-center gap-2 p-2 bg-indigo-50 dark:bg-indigo-900/20 rounded-lg border border-indigo-200 dark:border-indigo-800">
                            <x-heroicon-s-document class="w-4 h-4 text-indigo-600 dark:text-indigo-400" />
                            <span class="text-xs text-indigo-700 dark:text-indigo-300 flex-1">{{ $attachment->getClientOriginalName() }}</span>
                            <button type="button" wire:click="$set('attachment', null)" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-200">
                                <x-heroicon-s-x-circle class="w-4 h-4" />
                            </button>
                        </div>
                    @endif
                    
                    <div class="flex items-center gap-3">
                        <div class="flex-1 text-xs text-gray-500 dark:text-gray-400">
                            <span class="font-medium">{{ count($this->experts) }}</span> expert(s) • Powered by Mistral AI
                        </div>
                        <button type="submit" 
                            class="send-button bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white px-6 py-2.5 rounded-xl font-medium flex items-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none shadow-md"
                            wire:loading.class="opacity-75"
                            wire:loading.attr="disabled"
                            wire:target="sendMessage, attachment">
                            
                            <div wire:loading.remove wire:target="sendMessage">
                                <x-heroicon-m-paper-airplane class="w-5 h-5 transform rotate-45" />
                            </div>
                            <div wire:loading wire:target="sendMessage">
                                <x-filament::loading-indicator class="w-5 h-5 text-white" />
                            </div>
                            
                            <span wire:loading.remove wire:target="sendMessage" class="font-semibold">Envoyer</span>
                            <span wire:loading wire:target="sendMessage" class="font-semibold">Envoi...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Auto-scroll to bottom when new messages arrive
        const chatContainer = document.getElementById('chat-container');
        if (chatContainer) {
            const observer = new MutationObserver(() => {
                chatContainer.scrollTop = chatContainer.scrollHeight;
            });
            observer.observe(chatContainer, { childList: true, subtree: true });
            
            // Initial scroll
            chatContainer.scrollTop = chatContainer.scrollHeight;
        }
    </script>
</x-filament-panels::page>