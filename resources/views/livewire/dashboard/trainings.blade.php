    
<div class="w-full flex justify-center {{ $availableTrainings > 0 ? 'mb-4' : '' }}">
    @if(count($availableTrainings) > 0)
        <style>
            @keyframes pulse-dot {
                0%, 100% { 
                    opacity: 1;
                    transform: scale(1);
                }
                50% { 
                    opacity: 0.7;
                    transform: scale(1.1);
                }
            }

            @keyframes float {
                0%, 100% { transform: translateY(0px); }
                50% { transform: translateY(-3px); }
            }

            @keyframes shimmer {
                0% { transform: translateX(-100%); }
                100% { transform: translateX(100%); }
            }

            @keyframes bounce-gentle {
                0%, 100% { transform: translateY(0); }
                50% { transform: translateY(-2px); }
            }

            .pulse-dot {
                animation: pulse-dot 2s ease-in-out infinite;
            }

            .float-animation {
                animation: float 3s ease-in-out infinite;
            }

            .shimmer-effect {
                position: relative;
                overflow: hidden;
            }

            .shimmer-effect::before {
                content: '';
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: linear-gradient(90deg, transparent, rgba(255,255,255,0.4), transparent);
                animation: shimmer 3s infinite;
            }

            .bounce-gentle {
                animation: bounce-gentle 2s ease-in-out infinite;
            }

            .gradient-text {
                background: linear-gradient(135deg, #3b82f6 0%, #8b5cf6 100%);
                -webkit-background-clip: text;
                -webkit-text-fill-color: transparent;
                background-clip: text;
            }

            .glow-effect {
                box-shadow: 0 0 10px rgba(59, 130, 246, 0.3);
            }
        </style>

        <div class="w-full flex flex-col col-span-full sm:col-span-12 bg-gray-50 dark:bg-slate-800 shadow-lg rounded-xl border border-slate-200 dark:border-slate-700 p-4">
        
            <div class="w-full flex justify-start items-center gap-4 mb-4">
                <div class="w-8 h-8 bg-gradient-to-br from-blue-500 to-purple-600 rounded-lg flex items-center justify-center float-animation">
                    <i class="fas fa-graduation-cap text-white text-sm"></i>
                </div>
                <h2 class="text-xl font-bold text-gray-800 dark:text-gray-300">Available Trainings/Seminars</h2>
            </div>

            <div class="flex-col gap-4 w-full justify-center items-start">
                @foreach($availableTrainings as $training)
                    <div class="bg-white text-gray-700 dark:text-gray-300 dark:bg-gray-700 rounded-lg border border-gray-100 dark:border-gray-800 overflow-hidden hover:shadow-lg transition duration-300">
                        <div class="p-6">
                            <div class="flex justify-between items-start mb-2">
                                <h3 class="text-md font-semibold text-gray-700 dark:text-gray-300 line-clamp-2">
                                    Title: {{ $training->program_title }}
                                </h3>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    {{ ucfirst($training->type) }}
                                </span>
                            </div>

                            <div class="mb-2">
                                <div class="flex items-center text-sm">
                                    <i class="fas fa-building mr-2"></i>
                                    <span>{{ $training->training_provider }}</span>
                                </div>
                                
                                <div class="flex items-center text-sm">
                                    <i class="fas fa-calendar mr-2"></i>
                                    <span>{{ \Carbon\Carbon::parse($training->date_start)->format('M d, Y') }} - {{ \Carbon\Carbon::parse($training->date_end)->format('M d, Y') }}</span>
                                </div>
                                
                                <div class="flex items-center text-sm">
                                    <i class="fas fa-map-marker-alt mr-2"></i>
                                    <span>{{ $training->venue }}</span>
                                </div>

                                @if($training->application_deadline)
                                    <div class="flex items-center text-sm">
                                        <i class="fas fa-clock mr-2"></i>
                                        <span>Deadline of Application: {{ \Carbon\Carbon::parse($training->application_deadline)->format('M d, Y') }}</span>
                                    </div>
                                @endif
                            </div>

                            @if($training->description)
                                <div class="mb-2">
                                    <p class="text-sm text-gray-700 dark:text-gray-300 line-clamp-3">
                                        {{ $training->description }}
                                    </p>
                                </div>
                            @endif

                            <div class="flex justify-between items-center">
                                <div class="text-sm text-gray-500 dark:text-gray-300">
                                    LTO: {{ $training->lto }}
                                </div>
                                
                                <div class="flex space-x-2">
                                    @if($training->attachment)
                                        <p wire:click="downloadDocument({{ $training->id }})" 
                                        class="cursor-pointer inline-flex items-center px-3 py-1 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm rounded-md transition duration-200">
                                            <i class="fas fa-download mr-1"></i>
                                            Attachment
                                        </p>
                                    @endif
                                    
                                    <button wire:click="applyForTraining({{ $training->id }})" 
                                            wire:loading.attr="disabled"
                                            wire:target="applyForTraining"
                                            class="inline-flex items-center px-4 py-2 bg-green-500 hover:bg-green-700 text-white text-sm font-medium rounded-md transition duration-200">
                                        <i class="fas fa-paper-plane mr-1" wire:target="applyForTraining" wire:loading.remove></i>
                                        <div wire:loading wire:target="applyForTraining">
                                            <div class="spinner-border small text-primary mr-1" role="status">
                                            </div>
                                        </div>
                                        Apply Now
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <style>
                .line-clamp-2 {
                    display: -webkit-box;
                    -webkit-line-clamp: 2;
                    -webkit-box-orient: vertical;
                    overflow: hidden;
                }
                
                .line-clamp-3 {
                    display: -webkit-box;
                    -webkit-line-clamp: 3;
                    -webkit-box-orient: vertical;
                    overflow: hidden;
                }
            </style>
        
        </div>
    @endif
</div>                                                                          