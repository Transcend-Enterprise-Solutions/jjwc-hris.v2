<div class="rounded-xl border border-slate-200 bg-slate-50 p-4 dark:border-slate-700 dark:bg-slate-900">
    <div class="flex flex-col gap-3 lg:flex-row lg:items-end lg:justify-between">
        <div>
            <p class="text-xs font-semibold uppercase text-slate-500 dark:text-slate-400">Productive / Non-productive URL Rules</p>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Browser-only classification from Module 3. These rules classify logged HRIS/browser URLs, not OS applications.</p>
        </div>
        <div class="grid gap-2 sm:grid-cols-[1fr_auto_auto]">
            <input type="text" wire:model.defer="newUrlPattern" class="rounded-md border border-slate-300 px-3 py-2 text-sm dark:border-slate-700 dark:bg-gray-800 dark:text-white" placeholder="URL contains, e.g. docs.google.com">
            <select wire:model.defer="newUrlClassification" class="rounded-md border border-slate-300 px-3 py-2 text-sm dark:border-slate-700 dark:bg-gray-800 dark:text-white">
                <option value="productive">Productive</option>
                <option value="non_productive">Non-productive</option>
                <option value="neutral">Neutral</option>
            </select>
            <button type="button" wire:click="addUrlRule" class="rounded-md bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700">Add Rule</button>
        </div>
    </div>
    <div class="mt-3 flex flex-wrap gap-2">
        @forelse ($urlRules as $rule)
            <button type="button" wire:click="toggleUrlRule({{ $rule->id }})"
                class="rounded-full border px-3 py-1 text-xs font-semibold {{ $rule->is_active ? 'border-emerald-200 bg-emerald-50 text-emerald-700 dark:border-emerald-500/20 dark:bg-emerald-500/10 dark:text-emerald-300' : 'border-slate-200 bg-white text-slate-400 dark:border-slate-700 dark:bg-gray-800' }}">
                {{ $rule->pattern }} - {{ str_replace('_', ' ', $rule->classification) }}
            </button>
        @empty
            <p class="text-xs text-slate-500 dark:text-slate-400">No URL rules yet.</p>
        @endforelse
    </div>
</div>
