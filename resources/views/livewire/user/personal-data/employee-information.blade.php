<div class="w-full overflow-hidden relative">

    <div class="flex justify-between flex-wrap bg-gray-100 dark:bg-gray-700 px-6 py-4">
        <h2 class="text-xl font-bold dark:text-white">Employee Information</h2>
    </div>

    <div class="grid grid-cols-1 gap-3 p-6">
        <div class="col-span-full flex items-start justify-start  gap-2 text-sm">
            <span class="font-medium text-gray-600 dark:text-gray-400 w-32 flex-shrink-0">Employee Number:</span>
            <span class="text-gray-900 dark:text-gray-100">{{ $user->emp_code }}</span>
        </div>
        <div class="col-span-full flex items-start justify-start   gap-2 text-sm">
            <span class="font-medium text-gray-600 dark:text-gray-400 w-32 flex-shrink-0">Position:</span>
            <span class="text-gray-900 dark:text-gray-100">{{ $user->position ? $user->position->position : '--' }}</span>
        </div>
        <div class="col-span-full flex items-start justify-start   gap-2 text-sm">
            <span class="font-medium text-gray-600 dark:text-gray-400 w-32 flex-shrink-0">Office/Division:</span>
            <span class="text-gray-900 dark:text-gray-100">{{ $user->officeDivision ? $user->officeDivision->office_division : '--' }}</span>
        </div>
        <div class="col-span-full flex items-start justify-start   gap-2 text-sm">
            <span class="font-medium text-gray-600 dark:text-gray-400 w-32 flex-shrink-0">Unit:</span>
            <span class="text-gray-900 dark:text-gray-100">{{ $user->officeDivisionUnit ? $user->officeDivisionUnit->unit : '--' }}</span>
        </div>
        <div class="col-span-full flex items-start justify-start gap-2 text-sm">
            <span class="font-medium text-gray-600 dark:text-gray-400 w-32 flex-shrink-0">Appointment:</span>
            <span class="text-gray-900 dark:text-gray-100 uppercase">{{ $user->userData->appointment ?: 'Not provided' }}</span>
        </div>
        <div class="col-span-full flex items-start justify-start gap-2 text-sm">
            <span class="font-medium text-gray-600 dark:text-gray-400 w-32 flex-shrink-0">Date Hired:</span>
            <span class="text-gray-900 dark:text-gray-100">{{ $user->userData->date_hired ? \Carbon\Carbon::parse($user->userData->date_hired)->format('F d, Y') : 'Not provided' }}</span>
        </div>      
        <div class="col-span-full flex items-start justify-start gap-2 text-sm">
            <span class="font-medium text-gray-600 dark:text-gray-400 flex-shrink-0">Employment Status:</span>
            <span class="inline-block px-3 py-1 text-sm font-semibold
                {{ $user->active_status == 0 ? 'text-red-800 bg-red-200' : '--' }}
                {{ $user->active_status == 1 ? 'text-green-800 bg-green-200' : '--' }}
                {{ $user->active_status == 2 ? 'text-yellow-800 bg-yellow-200' : '--' }}
                {{ $user->active_status == 3 ? 'text-purple-800 bg-purple-200' : '--' }}
                    rounded-full">
                @if($user->active_status == 0)
                    Inactive
                @elseif($user->active_status == 1)
                    Active
                @elseif($user->active_status == 2)
                    Resigned
                @elseif($user->active_status == 3)
                    Retired
                @endif
            </span>
        </div>
    </div>
</div>