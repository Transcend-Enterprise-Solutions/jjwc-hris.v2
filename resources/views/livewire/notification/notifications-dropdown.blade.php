@props([
    'align' => 'right'
])

<div x-data="{ open: false }" class="relative" wire:poll.10s='refreshNotifications'>
    <style>
        .notif-scrollbar::-webkit-scrollbar {
            width: 5px;
        }
        .notif-scrollbar::-webkit-scrollbar-thumb {
            background-color: #1a1a1a4b;
            border-radius: 0 !important;
        }
        .notif-scrollbar::-webkit-scrollbar-track {
            background-color: #ffffff23;
            border-radius: 0 !important;
        }
    </style>

    <!-- Button for triggering the dropdown -->
    <button
        class="relative w-8 h-8 flex items-center justify-center bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 dark:hover:bg-slate-600/80 rounded-full"
        :class="{ 'bg-slate-200': open }"
        aria-haspopup="true"
        @click.prevent="open = !open"
        :aria-expanded="open"
    >
        <span class="sr-only">Notifications</span>
        <i class="bi bi-bell-fill"></i>
        <!-- Notification counter -->
        @if($unreadCount > 0)
            <div class="absolute top-0 right-0 w-2 h-2 bg-red-500 flex items-center justify-center rounded-full">
            </div>
        @endif
    </button>

    <!-- Dropdown menu -->
    <div
        class="fixed top-16 right-4 z-50 w-80 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 py-1.5 rounded-2xl shadow-lg overflow-hidden max-h-[calc(100vh-5rem)] overflow-y-auto"
        @click.outside="open = false"
        @keydown.escape.window="open = false"
        x-show="open"
        x-transition:enter="transition ease-out duration-200 transform"
        x-transition:enter-start="opacity-0 translate-y-2"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-out duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        x-cloak
    >
        <!-- Dropdown header -->
        <div class="flex items-center justify-between text-xs font-semibold text-slate-400 dark:text-slate-500 uppercase pt-1.5 pb-2 px-4">
            <span>Notifications</span>
            @if($unreadCount > 0)
                <button
                    class="text-sm text-blue-500 hover:text-blue-600 dark:text-blue-400 dark:hover:text-blue-300"
                    wire:click="markAllAsRead"
                >
                    Mark all as read
                </button>
            @endif
        </div>
        
        <!-- Dropdown items -->
        <ul class="max-h-64 overflow-y-auto notif-scrollbar" x-auto-animate>
            @if (Auth::user()->user_role === 'sa' || Auth::user()->user_role === 'hr')
                <!-- Admin/HR Notifications -->
                @php
                    $displayedNotifications = [];
                @endphp

                <!-- Document Requests -->
                @if($docRequestCount > 0)
                    <li>
                        <div class="block py-2 px-4 hover:bg-slate-50 dark:hover:bg-slate-700/20">
                            <div class="flex justify-between items-start">
                                <a wire:navigate href="{{ route('/employee-management/admin-doc-request') }}" class="flex-grow">
                                    <span class="block text-sm mb-1">
                                        <i class="fas fa-file-alt text-blue-500"></i>
                                        <span class="font-medium text-slate-800 dark:text-slate-100">
                                            Document Requests
                                        </span>
                                    </span>
                                    <span class="block text-xs text-slate-600 dark:text-slate-300">
                                        {{ $this->getDocRequestMessage() }}
                                    </span>
                                    @if($latestDocNotifDate)
                                        <span class="block text-xs font-medium text-slate-400 dark:text-slate-500">
                                            {{ \Carbon\Carbon::parse($latestDocNotifDate->created_at)->diffForHumans() }}
                                        </span>
                                    @endif
                                </a>
                                <div x-data="{ open: false }" class="relative inline-block">
                                    <button @click="open = !open" @click.away="open = false"
                                            class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="2" stroke="currentColor" class="w-5 h-5">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M12 6.75c.69 0 1.25-.56 1.25-1.25S12.69 4.25 12 4.25 10.75 4.81 10.75 5.5 11.31 6.75 12 6.75zM12 13.25c.69 0 1.25-.56 1.25-1.25s-.56-1.25-1.25-1.25-1.25.56-1.25 1.25.56 1.25 1.25 1.25zM12 19.75c.69 0 1.25-.56 1.25-1.25s-.56-1.25-1.25-1.25-1.25.56-1.25 1.25.56 1.25 1.25 1.25z"/>
                                        </svg>
                                    </button>
                                    <div x-show="open" x-transition
                                        class="absolute right-0 z-10 mt-2 w-36 bg-white dark:bg-gray-800 shadow-lg rounded-md">
                                        <button class="block w-full px-4 py-2 text-left text-sm text-blue-500 hover:text-blue-600 dark:text-blue-400 dark:hover:text-blue-300"
                                                wire:click="markGroupAsRead('doc_request')">
                                            Mark as read
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </li>
                @endif

                <!-- Location Requests -->
                @if($locRequestCount > 0)
                    <li>
                        <div class="block py-2 px-4 hover:bg-slate-50 dark:hover:bg-slate-700/20">
                            <div class="flex justify-between items-start">
                                <a href="{{ route('/employee-management/wfh-management', ['tab' => 'requests']) }}" class="flex-grow">
                                    <span class="block text-sm mb-1">
                                        <i class="fas fa-map-marker-alt text-red-500"></i>
                                        <span class="font-medium text-slate-800 dark:text-slate-100">
                                            WFH Location Requests
                                        </span>
                                    </span>
                                    <span class="block text-xs text-slate-600 dark:text-slate-300">
                                        {{ $this->getLocRequestMessage() }}
                                    </span>
                                    @if($latestLocNotifDate)
                                        <span class="block text-xs font-medium text-slate-400 dark:text-slate-500">
                                            {{ \Carbon\Carbon::parse($latestLocNotifDate->created_at)->diffForHumans() }}
                                        </span>
                                    @endif
                                </a>
                                <div x-data="{ open: false }" class="relative inline-block">
                                    <button @click="open = !open" @click.away="open = false"
                                            class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="2" stroke="currentColor" class="w-5 h-5">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M12 6.75c.69 0 1.25-.56 1.25-1.25S12.69 4.25 12 4.25 10.75 4.81 10.75 5.5 11.31 6.75 12 6.75zM12 13.25c.69 0 1.25-.56 1.25-1.25s-.56-1.25-1.25-1.25-1.25.56-1.25 1.25.56 1.25 1.25 1.25zM12 19.75c.69 0 1.25-.56 1.25-1.25s-.56-1.25-1.25-1.25-1.25.56-1.25 1.25.56 1.25 1.25 1.25z"/>
                                        </svg>
                                    </button>
                                    <div x-show="open" x-transition
                                        class="absolute right-0 z-10 mt-2 w-36 bg-white dark:bg-gray-800 shadow-lg rounded-md">
                                        <button class="block w-full px-4 py-2 text-left text-sm text-blue-500 hover:text-blue-600 dark:text-blue-400 dark:hover:text-blue-300"
                                                wire:click="markGroupAsRead('locrequest')">
                                            Mark as read
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </li>
                @endif

                <!-- Official Business Requests -->
                @if($obRequestCount > 0)
                    <li>
                        <div class="block py-2 px-4 hover:bg-slate-50 dark:hover:bg-slate-700/20">
                            <div class="flex justify-between items-start">
                                <a href="{{ route('/employee-management/admin-official-business', ['tab' => 'requests']) }}" class="flex-grow">
                                    <span class="block text-sm mb-1">
                                        <i class="fas fa-map-marker-alt text-red-500"></i>
                                        <span class="font-medium text-slate-800 dark:text-slate-100">
                                            Official Business Requests
                                        </span>
                                    </span>
                                    <span class="block text-xs text-slate-600 dark:text-slate-300">
                                        {{ $this->getOBRequestMessage() }}
                                    </span>
                                    @if($latestObNotifDate)
                                        <span class="block text-xs font-medium text-slate-400 dark:text-slate-500">
                                            {{ \Carbon\Carbon::parse($latestObNotifDate->created_at)->diffForHumans() }}
                                        </span>
                                    @endif
                                </a>
                                <div x-data="{ open: false }" class="relative inline-block">
                                    <button @click="open = !open" @click.away="open = false"
                                            class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="2" stroke="currentColor" class="w-5 h-5">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M12 6.75c.69 0 1.25-.56 1.25-1.25S12.69 4.25 12 4.25 10.75 4.81 10.75 5.5 11.31 6.75 12 6.75zM12 13.25c.69 0 1.25-.56 1.25-1.25s-.56-1.25-1.25-1.25-1.25.56-1.25 1.25.56 1.25 1.25 1.25zM12 19.75c.69 0 1.25-.56 1.25-1.25s-.56-1.25-1.25-1.25-1.25.56-1.25 1.25.56 1.25 1.25 1.25z"/>
                                        </svg>
                                    </button>
                                    <div x-show="open" x-transition
                                        class="absolute right-0 z-10 mt-2 w-36 bg-white dark:bg-gray-800 shadow-lg rounded-md">
                                        <button class="block w-full px-4 py-2 text-left text-sm text-blue-500 hover:text-blue-600 dark:text-blue-400 dark:hover:text-blue-300"
                                                wire:click="markGroupAsRead('obrequest')">
                                            Mark as read
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </li>
                @endif

                <!-- Pending Changes -->
                @if($pendingChangeCount > 0)
                    <li>
                        <div class="block py-2 px-4 hover:bg-slate-50 dark:hover:bg-slate-700/20">
                            <div class="flex justify-between items-start">
                                <a href="{{ route('/employee-management/employees') }}" class="flex-grow">
                                    <span class="block text-sm mb-1">
                                        <i class="fas fa-user-edit text-purple-500"></i>
                                        <span class="font-medium text-slate-800 dark:text-slate-100">
                                            Employee Changes
                                        </span>
                                    </span>
                                    <span class="block text-xs text-slate-600 dark:text-slate-300">
                                        {{ $this->getPendingChangeMessage() }}
                                    </span>
                                    @if($latestPendingChangeNotifDate)
                                        <span class="block text-xs font-medium text-slate-400 dark:text-slate-500">
                                            {{ \Carbon\Carbon::parse($latestPendingChangeNotifDate->created_at)->diffForHumans() }}
                                        </span>
                                    @endif
                                </a>
                                <div x-data="{ open: false }" class="relative inline-block">
                                    <button @click="open = !open" @click.away="open = false"
                                            class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="2" stroke="currentColor" class="w-5 h-5">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M12 6.75c.69 0 1.25-.56 1.25-1.25S12.69 4.25 12 4.25 10.75 4.81 10.75 5.5 11.31 6.75 12 6.75zM12 13.25c.69 0 1.25-.56 1.25-1.25s-.56-1.25-1.25-1.25-1.25.56-1.25 1.25.56 1.25 1.25 1.25zM12 19.75c.69 0 1.25-.56 1.25-1.25s-.56-1.25-1.25-1.25-1.25.56-1.25 1.25.56 1.25 1.25 1.25z"/>
                                        </svg>
                                    </button>
                                    <div x-show="open" x-transition
                                        class="absolute right-0 z-10 mt-2 w-36 bg-white dark:bg-gray-800 shadow-lg rounded-md">
                                        <button class="block w-full px-4 py-2 text-left text-sm text-blue-500 hover:text-blue-600 dark:text-blue-400 dark:hover:text-blue-300"
                                                wire:click="markGroupAsRead('pending_change')">
                                            Mark as read
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </li>
                @endif

                @if($unreadCount == 0)
                    <li class="py-4 px-4 text-sm text-slate-500 dark:text-slate-400 text-center">
                        <i class="bi bi-bell text-2xl mb-2 block"></i>
                        No new notifications
                    </li>
                @endif

            @elseif(Auth::user()->user_role === 'sv')
                <!-- Supervisor Notifications -->
                @if($obRequestCount > 0)
                    <li>
                        <div class="block py-2 px-4 hover:bg-slate-50 dark:hover:bg-slate-700/20">
                            <div class="flex justify-between items-start">
                                <a href="{{ route('/employee-management/admin-official-business', ['tab' => 'requests']) }}" class="flex-grow">
                                    <span class="block text-sm mb-1">
                                        <i class="fas fa-map-marker-alt text-red-500"></i>
                                        <span class="font-medium text-slate-800 dark:text-slate-100">
                                            Official Business Requests
                                        </span>
                                    </span>
                                    <span class="block text-xs text-slate-600 dark:text-slate-300">
                                        {{ $this->getOBRequestMessage() }}
                                    </span>
                                    @if($latestObNotifDate)
                                        <span class="block text-xs font-medium text-slate-400 dark:text-slate-500">
                                            {{ \Carbon\Carbon::parse($latestObNotifDate->created_at)->diffForHumans() }}
                                        </span>
                                    @endif
                                </a>
                                <div x-data="{ open: false }" class="relative inline-block">
                                    <button @click="open = !open" @click.away="open = false"
                                            class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="2" stroke="currentColor" class="w-5 h-5">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M12 6.75c.69 0 1.25-.56 1.25-1.25S12.69 4.25 12 4.25 10.75 4.81 10.75 5.5 11.31 6.75 12 6.75zM12 13.25c.69 0 1.25-.56 1.25-1.25s-.56-1.25-1.25-1.25-1.25.56-1.25 1.25.56 1.25 1.25 1.25zM12 19.75c.69 0 1.25-.56 1.25-1.25s-.56-1.25-1.25-1.25-1.25.56-1.25 1.25.56 1.25 1.25 1.25z"/>
                                        </svg>
                                    </button>
                                    <div x-show="open" x-transition
                                        class="absolute right-0 z-10 mt-2 w-36 bg-white dark:bg-gray-800 shadow-lg rounded-md">
                                        <button class="block w-full px-4 py-2 text-left text-sm text-blue-500 hover:text-blue-600 dark:text-blue-400 dark:hover:text-blue-300"
                                                wire:click="markGroupAsRead('obrequest')">
                                            Mark as read
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </li>
                @endif

                @if($unreadCount == 0)
                    <li class="py-4 px-4 text-sm text-slate-500 dark:text-slate-400 text-center">
                        <i class="bi bi-bell text-2xl mb-2 block"></i>
                        No new notifications
                    </li>
                @endif

            @elseif(Auth::user()->user_role === 'emp')
                <!-- Employee Notifications -->
                @forelse ($notifications as $type => $group)
                    @if(!$group['read'])
                        @php
                            $formatedType = str_replace('_', ' ', $type)
                        @endphp
                        <li>
                            <div class="block py-2 px-4 hover:bg-slate-50 dark:hover:bg-slate-700/20">
                                <div class="flex justify-between items-start">
                                    @if($type === 'doc_request')
                                        <a wire:navigate href="{{ route('/my-records/doc-request') }}" class="flex-grow">
                                            <span class="block text-sm mb-1">
                                                <i class="fas fa-file-alt text-blue-500"></i>
                                                <span class="font-medium text-slate-800 dark:text-slate-100">
                                                    Document Request
                                                </span>
                                            </span>
                                    @elseif($type === 'loc_request')
                                        <a href="{{ route('home', ['showWFHLocHistory' => true]) }}" class="flex-grow">
                                            <span class="block text-sm mb-1">
                                                <i class="fas fa-map-marker-alt text-red-500"></i>
                                                <span class="font-medium text-slate-800 dark:text-slate-100">
                                                    WFH Location Request
                                                </span>
                                            </span>
                                    @elseif($type === 'ob_request')
                                        <a href="{{ route('/daily-time-record/official-business') }}" class="flex-grow">
                                            <span class="block text-sm mb-1">
                                                <i class="fas fa-map-marker-alt text-red-500"></i>
                                                <span class="font-medium text-slate-800 dark:text-slate-100">
                                                   Official Business Request
                                                </span>
                                            </span>
                                    @elseif($type === 'pending_changes')
                                        <a href="{{ route('/my-records/personal-data-sheet') }}" class="flex-grow">
                                            <span class="block text-sm mb-1">
                                                <i class="fas fa-edit text-red-500"></i>
                                                <span class="font-medium text-slate-800 dark:text-slate-100">
                                                    Change Information Request
                                                </span>
                                            </span>
                                    @else
                                        <div class="flex-grow">
                                            <span class="block text-sm mb-1">
                                                <i class="fas fa-bell text-yellow-500"></i>
                                                <span class="font-medium text-slate-800 dark:text-slate-100">
                                                    {{ ucfirst($formatedType) }}
                                                </span>
                                            </span>
                                        </div>
                                    @endif

                                    <span class="block text-xs text-slate-600 dark:text-slate-300">
                                        You have {{ $group['count'] }} new {{ $formatedType }} notification(s)
                                    </span>
                                    
                                    @if(in_array($type, ['doc_request', 'loc_request', 'pending_changes', 'ob_request', 'leave_request']))
                                        </a>
                                    @endif
                                    
                                    <span class="block text-xs font-medium text-slate-400 dark:text-slate-500 mb-1">
                                        {{ $group['latest']->created_at->diffForHumans() }}
                                    </span>
                                    
                                    <div x-data="{ open: false }" class="relative inline-block">
                                        <button @click="open = !open" @click.away="open = false"
                                                class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                stroke-width="2" stroke="currentColor" class="w-5 h-5">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M12 6.75c.69 0 1.25-.56 1.25-1.25S12.69 4.25 12 4.25 10.75 4.81 10.75 5.5 11.31 6.75 12 6.75zM12 13.25c.69 0 1.25-.56 1.25-1.25s-.56-1.25-1.25-1.25-1.25.56-1.25 1.25.56 1.25 1.25 1.25zM12 19.75c.69 0 1.25-.56 1.25-1.25s-.56-1.25-1.25-1.25-1.25.56-1.25 1.25.56 1.25 1.25 1.25z"/>
                                            </svg>
                                        </button>
                                        <div x-show="open" x-transition
                                            class="absolute right-0 z-10 mt-2 w-36 bg-white dark:bg-gray-800 shadow-lg rounded-md">
                                            <button class="block w-full px-4 py-2 text-left text-sm text-blue-500 hover:text-blue-600 dark:text-blue-400 dark:hover:text-blue-300"
                                                    wire:click="markGroupAsRead('{{ $type }}')">
                                                Mark as read
                                            </button>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </li>
                    @endif
                @empty
                    <li class="py-4 px-4 text-sm text-slate-500 dark:text-slate-400 text-center">
                        <i class="bi bi-bell text-2xl mb-2 block"></i>
                        No new notifications
                    </li>
                @endforelse
            @endif
        </ul>
    </div>
</div>