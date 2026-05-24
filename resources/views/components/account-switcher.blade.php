@php
    $switchableAccounts = Auth::user()->getSwitchableAccounts();
    $hasSwitchableAccounts = $switchableAccounts->count() > 0;
@endphp

@if($hasSwitchableAccounts)
    <div class="bg-white dark:bg-gray-800 shadow-sm border-t border-gray-200 dark:border-gray-700 p-4" style="width: 300px">
        <div class="mb-4">
            <h3 class="text-md font-semibold text-gray-900 dark:text-white mb-1">Switch Account</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400">Choose an account to switch to</p>
        </div>

        <!-- Current Account Display -->
        <div class="mb-4 p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-700">
            <p class="text-xs text-blue-600 dark:text-blue-400 mb-2 font-medium">Currently logged in as:</p>
            <div class="flex gap-3 items-center">
                @if (Auth::user()->profile_photo_path)
                    <img src="{{ route('profile-photo.file', ['filename' => basename(Auth::user()->profile_photo_path)]) }}"
                        alt="{{ Auth::user()->name }}"
                        style="width: 35px; height: 35px;"
                        class="rounded-full object-cover border-2 border-blue-500">
                @else
                    <div class="rounded-full bg-blue-500 border-2 border-blue-500 
                        flex items-center justify-center text-white text-xs font-medium"
                        style="width: 35px; height: 35px;">
                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}{{ strtoupper(substr(explode(' ', Auth::user()->name)[1] ?? '', 0, 1)) }}
                    </div>
                @endif
                <div class="flex-1">
                    <div class="flex items-center gap-2">
                        <span class="text-sm font-medium text-gray-900 dark:text-white">{{ Auth::user()->name }}</span>
                    </div>
                    <p class="inline-flex items-center px-2 py-1 rounded-lg text-xs font-medium 
                            {{ Auth::user()->isAdminAccount() ? 'bg-purple-100 text-purple-800 dark:bg-purple-800 dark:text-purple-100' : 'bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100' }}">
                        {{ Auth::user()->isAdminAccount() ? ucfirst(Auth::user()->admin_prefix) : 'Employee' }}
                    </p>
                </div>
            </div>
        </div>

        <!-- Available Accounts List -->
        <div class="space-y-2">
            @foreach($switchableAccounts as $account)
                <div class="group hover:bg-gray-50 dark:hover:bg-gray-700 rounded-lg p-3 transition-colors duration-200 cursor-pointer border border-transparent hover:border-gray-200 dark:hover:border-gray-600 account-item"
                    onclick="switchToAccount({{ $account->id }})" data-account-id="{{ $account->id }}">
                    <div class="flex gap-3 items-center">
                        <!-- Clickable Profile Photo with Loading Indicator -->
                        <div class="relative profile-photo-container" data-account-id="{{ $account->id }}">
                            @if ($account->profile_photo_path)
                                <img src="{{ route('profile-photo.file', ['filename' => basename($account->profile_photo_path)]) }}"
                                    alt="{{ $account->name }}"
                                    style="width: 35px; height: 35px; z-index: 9;"
                                    class="rounded-full object-cover border-2 border-gray-300 dark:border-gray-600 group-hover:border-blue-500 transition-colors duration-200 cursor-pointer profile-photo">
                            @else
                                <div class="rounded-full bg-gray-500 border-2 border-gray-300 
                                    dark:border-gray-600 group-hover:border-blue-500 dark:bg-gray-600 
                                    flex items-center justify-center text-white text-sm font-medium 
                                    cursor-pointer transition-colors duration-200 profile-photo"
                                    style="width: 35px; height: 35px; z-index: 9;">
                                    {{ strtoupper(substr($account->name, 0, 1)) }}{{ strtoupper(substr(explode(' ', $account->name)[1] ?? '', 0, 1)) }}
                                </div>
                            @endif
                            
                            <!-- Loading Spinner Overlay -->
                            <div class="loading-spinner absolute inset-0 rounded-full 
                                border-2 border-r-blue-500 animate-spin 
                                opacity-0 transition-opacity duration-200" 
                                 style="width: 35px; height: 35px; z-index: 10;"></div>
                        </div>

                        <!-- Account Info -->
                        <div class="flex-1">
                            <div class="flex items-center gap-2 mb-1">
                                <span class="text-sm font-medium text-gray-900 dark:text-white group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors duration-200">
                                    {{ $account->name }}
                                </span>
                            </div>
                            <p class="text-xs inline-flex items-center px-2 py-1 rounded-lg font-medium 
                                    {{ $account->isAdminAccount() ? 'bg-purple-100 text-purple-800 dark:bg-purple-800 dark:text-purple-100' : 'bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100' }}">
                                {{ $account->isAdminAccount() ? ucfirst($account->admin_prefix) : 'Employee' }}
                            </p>
                        </div>

                        <!-- Switch Arrow -->
                        <div class="opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                            <i class="bi bi-arrow-right-circle-fill text-blue-500 text-xl"></i>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

    </div>

    <style>
        .loading-spinner.active {
            opacity: 1 !important;
        }
        
        .profile-photo-container.loading .profile-photo {
            opacity: 0.4;
        }
        
        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
        
        .animate-spin {
            animation: spin 1s linear infinite;
        }
    </style>

    <script>
        async function switchToAccount(targetAccountId) {
            // Find the specific account item and profile photo container
            const accountItem = document.querySelector(`.account-item[data-account-id="${targetAccountId}"]`);
            const profileContainer = document.querySelector(`.profile-photo-container[data-account-id="${targetAccountId}"]`);
            const loadingSpinner = profileContainer.querySelector('.loading-spinner');
            
            // Show loading state
            accountItem.style.opacity = '0.6';
            accountItem.style.pointerEvents = 'none';
            
            // Activate loading spinner on profile photo
            profileContainer.classList.add('loading');
            loadingSpinner.classList.add('active');
            
            try {
                const response = await fetch('/account/switch', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        target_account_id: targetAccountId
                    })
                });

                // Check if response is ok
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                const data = await response.json();

                if (data.success) {
                    // Show success feedback before redirect
                    // const successMsg = document.createElement('div');
                    // successMsg.className = 'fixed top-4 right-4 bg-green-500 text-white px-4 py-2 rounded-lg shadow-lg z-50';
                    // successMsg.textContent = 'Switching accounts...';
                    // document.body.appendChild(successMsg);
                    
                    // Use replace instead of href to avoid back button issues
                    setTimeout(() => {
                        window.location.replace(data.redirect_url);
                    }, 800);
                } else {
                    console.error('Account switch failed:', data);
                    alert(data.message || 'Failed to switch accounts.');
                    // Restore button state
                    accountItem.style.opacity = '1';
                    accountItem.style.pointerEvents = 'auto';
                    profileContainer.classList.remove('loading');
                    loadingSpinner.classList.remove('active');
                }
            } catch (error) {
                console.error('Error switching accounts:', error);
                
                // Try to refresh the page as fallback since the switch might have worked
                const shouldRefresh = confirm('An error occurred during account switching. The switch may have been successful. Would you like to refresh the page to check?');
                if (shouldRefresh) {
                    window.location.reload();
                } else {
                    // Restore button state
                    accountItem.style.opacity = '1';
                    accountItem.style.pointerEvents = 'auto';
                    profileContainer.classList.remove('loading');
                    loadingSpinner.classList.remove('active');
                }
            }
        }
    </script>
@endif