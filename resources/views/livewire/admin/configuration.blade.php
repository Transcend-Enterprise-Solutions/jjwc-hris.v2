<div class="w-full flex flex-col justify-center">

    <style>
        .spinner-border {
            display: inline-block;
            width: 1rem;
            height: 1rem;
            vertical-align: text-bottom;
            border: 2px solid currentColor;
            border-right-color: transparent;
            border-radius: 50%;
            -webkit-animation: spinner-border .75s linear infinite;
            animation: spinner-border .75s linear infinite;
            color: rgb(255, 255, 255);
        }

        .spinner-border-2 {
            display: inline-block;
            width: 1rem;
            height: 1rem;
            vertical-align: text-bottom;
            border: 2px solid currentColor;
            border-right-color: transparent;
            border-radius: 50%;
            -webkit-animation: spinner-border .75s linear infinite;
            animation: spinner-border .75s linear infinite;
            color: rgb(0, 0, 0);
        }

        @-webkit-keyframes spinner-border {
            to {
                transform: rotate(360deg);
            }
        }

        @keyframes spinner-border {
            to {
                transform: rotate(360deg);
            }
        }
    </style>

    <div class="flex flex-col items-center justify-center w-full">
        <div class="pb-4 mb-3 pt-4 sm:pt-0">
            <h1 class="text-lg font-bold text-center text-slate-800 dark:text-white">Configuration</h1>
        </div>

        <div class="w-full bg-white rounded-2xl p-3 sm:p-6 shadow dark:bg-gray-800 overflow-x-visible">

            <div class="mb-6 flex flex-col items-start gap-4 flex-wrap">
                <p class="text-gray-700 dark:text-gray-100">Biometrics Connection - <span class="text-xs {{ $conStatus ?  'text-green-500' : 'text-red-500' }}">{{ $conStatus ? 'Connected' : 'Not Connected' }}</span></p>
                <div class="grid grid-cols-3 gap-4 items-end w-full">
                    <div class="col-span-full sm:col-span-1">
                        <label for="authUrl" class="text-sm text-gray-500 dark:text-gray-300">Auth URL <span class="text-red-600">*</span></label>
                        <div class="relative w-full">
                        <input type="text"
                            wire:model.live="authUrl"
                            class="px-2 py-1.5 block w-full shadow-sm sm:text-sm border border-gray-400 hover:bg-gray-300 rounded-md
                                dark:hover:bg-slate-600 dark:border-slate-600
                                dark:text-gray-300 dark:bg-gray-800" {{ $editCon ? '':'readonly' }} style="pointer-events: {{ $editCon ? '':'none' }}">
                        <div class="absolute inset-y-0 right-0 flex items-center px-3">
                            <i :class="show ? 'bi bi-eye' : 'bi bi-eye-slash'" @click="show = !show" class="cursor-pointer text-gray-500"></i>
                        </div>
                        </div>
                        @error('authUrl')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="col-span-full sm:col-span-1">
                        <label for="username" class="text-sm text-gray-500 dark:text-gray-300">Username <span class="text-red-600">*</span></label>
                        <div class="relative w-full">
                        <input type="text"
                            wire:model.live="username"
                            class="px-2 py-1.5 block w-full shadow-sm sm:text-sm border border-gray-400 hover:bg-gray-300 rounded-md
                                dark:hover:bg-slate-600 dark:border-slate-600
                                dark:text-gray-300 dark:bg-gray-800" {{ $editCon ? '':'readonly' }} style="pointer-events: {{ $editCon ? '':'none' }}">
                        <div class="absolute inset-y-0 right-0 flex items-center px-3">
                            <i :class="show ? 'bi bi-eye' : 'bi bi-eye-slash'" @click="show = !show" class="cursor-pointer text-gray-500"></i>
                        </div>
                        </div>
                        @error('username')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>
                    <div x-data="{ show: false }" class="col-span-full sm:col-span-1">
                        <label for="password" class="text-sm text-gray-500 dark:text-gray-300">Password <span class="text-red-600">*</span></label>
                        <div class="relative w-full">
                        <input :type="show ? 'text' : 'password'" id="password" id="password"
                            wire:model.live="password"
                            class="px-2 py-1.5 block w-full shadow-sm sm:text-sm border border-gray-400 hover:bg-gray-300 rounded-md
                                dark:hover:bg-slate-600 dark:border-slate-600
                                dark:text-gray-300 dark:bg-gray-800" {{ $editCon ? '':'readonly' }} style="pointer-events: {{ $editCon ? '':'none' }}">
                        <div class="absolute inset-y-0 right-0 flex items-center px-3  {{ $editCon ? '':'hidden' }}">
                            <i :class="show ? 'bi bi-eye' : 'bi bi-eye-slash'" @click="show = !show" class="cursor-pointer text-gray-500"></i>
                        </div>
                        </div>
                        @error('password')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>


                    @if($editCon)
                        <div class="flex gap-4 items-end col-span-full sm:col-span-1">
                            <button
                                style="height: 36px"
                                class="px-2 block shadow-sm sm:text-sm hover:bg-green-700 rounded-md
                                        bg-green-600 text-white focus:ring-2 focus:ring-offset-2 focus:ring-black"
                                wire:click="testConnection" wire:loading.attr="disabled" wire:target="testConnection">
                                <span wire:loading.remove wire:target="testConnection">Test Connection</span>
                                <span wire:loading wire:target="testConnection">Connecting &nbsp;
                                    <div class="spinner-border small text-primary" role="status">
                                    </div>
                                </span>
                            </button>
                            <button
                                style="height: 36px"
                                class="px-2 block shadow-sm sm:text-sm hover:bg-blue-700 rounded-md
                                        bg-blue-600 text-white focus:ring-2 focus:ring-offset-2 focus:ring-black"
                                wire:click="saveConnection" wire:loading.attr="disabled" wire:target="saveConnection">
                                <span>Save</span>
                                <span wire:loading wire:target="saveConnection">
                                    <div class="spinner-border small text-primary" role="status">
                                    </div>
                                </span>
                            </button>
                            <button
                                style="height: 36px"
                                class="px-2 block shadow-sm sm:text-sm hover:bg-gray-700 rounded-md
                                        bg-gray-600 text-white focus:ring-2 focus:ring-offset-2 focus:ring-black"
                                wire:click="toggleEditConnection" wire:loading.attr="disabled" wire:target="toggleEditConnection">
                                <span>Cancel</span>
                            </button>
                        </div>
                    @else
                        <div class="flex gap-4 items-end col-span-full sm:col-span-1">
                            <button
                                style="height: 36px"
                                class="px-2 block shadow-sm sm:text-sm hover:bg-blue-700 rounded-md
                                        bg-blue-600 text-white focus:ring-2 focus:ring-offset-2 focus:ring-black"
                                wire:click="toggleEditConnection" wire:loading.attr="disabled" wire:target="toggleEditConnection">
                                <span>Edit</span>
                            </button>
                        </div>
                    @endif


                </div>


                @if($conMessage)
                    @if($successCon)
                        <div class="flex justify-center items-center w-full py-4 relative rounded-md text-white bg-green-500"
                            wire:transition.{{ $successCon ? 'enter' : 'leave' }}.duration.400ms>
                            <p class="text-sm text-gray-500 dark:text-gray-300 w-full px-10 break-words">{{ $conMessage }}</p>
                            <i class="bi bi-x text-lg text-white cursor-pointer" wire:click='closeMessage' style="position: absolute; top: 13px; right: 20px;"></i> 
                        </div>
                    @else
                        <div class="flex justify-center items-center w-full py-4 relative rounded-md text-white bg-red-500"
                            wire:transition.{{ $successCon ? 'enter' : 'leave' }}.duration.400ms>
                            <p class="text-sm text-gray-500 dark:text-gray-300 w-full px-10 break-words">{{ $conMessage }}</p>
                            <i class="bi bi-x text-lg text-white cursor-pointer" wire:click='closeMessage' style="position: absolute; top: 13px; right: 20px;"></i> 
                        </div>
                    @endif
                @endif


                
            </div>

        </div>
    </div>
</div>
