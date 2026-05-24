<div x-data="{ isRefreshing: false }" class="flex items-center">
    <button
        class="w-8 h-8 flex items-center justify-center rounded-full bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 dark:hover:bg-slate-600/80"
        @click="isRefreshing = true; setTimeout(() => location.reload(), 500)"
        aria-label="Refresh"
    >
        <i
            class="bi bi-arrow-clockwise text-xl text-slate-500 dark:text-slate-400"
            :class="{ 'animate-spin-fast': isRefreshing }"
        ></i>
    </button>
</div>

<style>
    @keyframes spin-fast {
        to {
            transform: rotate(360deg);
        }
    }

    .animate-spin-fast {
        animation: spin-fast 0.5s linear infinite;
    }
</style>
