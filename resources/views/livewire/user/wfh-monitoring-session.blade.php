<div
    class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 p-6 space-y-6"
    wire:poll.15s="refreshMonitoringState"
    x-data="{
        init() {
            const syncContext = () => {
                $wire.syncBrowserContext(document.title, window.location.href)
            }

            const heartbeat = () => {
                $wire.recordHeartbeat()
            }

            syncContext()
            heartbeat()

            setInterval(syncContext, 60000)
            setInterval(() => {
                if (document.visibilityState === 'visible') {
                    heartbeat()
                }
            }, 30000)

            window.addEventListener('mousemove', heartbeat, { passive: true })
            window.addEventListener('keydown', heartbeat)
            window.addEventListener('touchstart', heartbeat, { passive: true })
            document.addEventListener('visibilitychange', heartbeat)
        }
    }"
    x-init="init()"
>
    <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
        <div>
            <p class="text-xs uppercase tracking-[0.35em] text-sky-500 dark:text-sky-300">WFH Monitoring</p>
            <h2 class="mt-2 text-2xl font-semibold text-slate-900 dark:text-white">Session management</h2>
            <p class="mt-2 text-sm text-slate-500 dark:text-slate-400 max-w-2xl">
                Browser-based monitoring that tracks session start/end, activity heartbeat, AFK status, and the employee's current work declaration.
            </p>
        </div>

        <div class="grid grid-cols-2 gap-3 text-sm">
            <div class="rounded-xl bg-slate-50 dark:bg-slate-900/60 px-4 py-3">
                <div class="text-slate-500 dark:text-slate-400">Current state</div>
                <div class="mt-1 text-lg font-semibold text-slate-900 dark:text-white">{{ $this->sessionState }}</div>
            </div>
            <div class="rounded-xl bg-slate-50 dark:bg-slate-900/60 px-4 py-3">
                <div class="text-slate-500 dark:text-slate-400">Elapsed time</div>
                <div class="mt-1 text-lg font-semibold text-slate-900 dark:text-white">{{ $this->activeSessionDuration }}</div>
            </div>
        </div>
    </div>

    <div class="grid gap-4 lg:grid-cols-4">
        <div class="rounded-2xl bg-slate-50 dark:bg-slate-900/60 p-4">
            <p class="text-sm text-slate-500 dark:text-slate-400">Work status</p>
            <div class="mt-2 flex flex-wrap gap-2">
                @foreach (['WFH', 'Field Work', 'On Break', 'Meeting'] as $status)
                    <button
                        type="button"
                        wire:click="updateWorkStatus('{{ $status }}')"
                        class="rounded-full px-3 py-1.5 text-xs font-medium transition
                        {{ $workStatus === $status ? 'bg-sky-600 text-white' : 'bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 border border-slate-200 dark:border-slate-700' }}"
                    >
                        {{ $status }}
                    </button>
                @endforeach
            </div>
        </div>

        <div class="rounded-2xl bg-slate-50 dark:bg-slate-900/60 p-4">
            <p class="text-sm text-slate-500 dark:text-slate-400">AFK threshold</p>
            <p class="mt-2 text-2xl font-semibold text-slate-900 dark:text-white">{{ $afkThresholdMinutes }} min</p>
            <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">The session shifts to AFK when heartbeats stop beyond this window.</p>
        </div>

        <div class="rounded-2xl bg-slate-50 dark:bg-slate-900/60 p-4">
            <p class="text-sm text-slate-500 dark:text-slate-400">Browser tab</p>
            <p class="mt-2 text-sm font-medium text-slate-900 dark:text-white truncate" title="{{ $browserTabTitle }}">{{ $browserTabTitle ?: 'Waiting for browser sync...' }}</p>
            <p class="mt-1 text-xs text-slate-500 dark:text-slate-400 truncate" title="{{ $browserUrl }}">{{ $browserUrl ?: 'No URL captured yet' }}</p>
        </div>

        <div class="rounded-2xl bg-slate-50 dark:bg-slate-900/60 p-4">
            <p class="text-sm text-slate-500 dark:text-slate-400">Activity count</p>
            <p class="mt-2 text-2xl font-semibold text-slate-900 dark:text-white">{{ $activeSession?->activity_count ?? 0 }}</p>
            <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Recorded heartbeats and context updates.</p>
        </div>
    </div>

    <div class="grid gap-6 xl:grid-cols-[1.4fr_1fr]">
        <div class="space-y-4">
            <div class="rounded-2xl border border-slate-200 dark:border-slate-700 p-5">
                <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                    <div class="space-y-4">
                        <div>
                            <label class="text-sm font-medium text-slate-700 dark:text-slate-200">Session notes</label>
                            <textarea
                                wire:model.defer="sessionNotes"
                                rows="3"
                                class="mt-2 w-full rounded-xl border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:border-sky-500 focus:ring-sky-500"
                                placeholder="Optional notes before starting the session..."
                            ></textarea>
                        </div>
                        <div class="grid gap-3 sm:grid-cols-2">
                            <input
                                type="text"
                                wire:model.defer="browserTabTitle"
                                class="w-full rounded-xl border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:border-sky-500 focus:ring-sky-500"
                                placeholder="Browser tab title"
                            />
                            <input
                                type="url"
                                wire:model.defer="browserUrl"
                                class="w-full rounded-xl border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:border-sky-500 focus:ring-sky-500"
                                placeholder="Browser URL"
                            />
                        </div>
                    </div>

                    <div class="flex flex-wrap gap-3">
                        <button
                            type="button"
                            wire:click="startSession"
                            class="rounded-xl bg-sky-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-sky-700"
                        >
                            Start Session
                        </button>
                        <button
                            type="button"
                            wire:click="endSession"
                            class="rounded-xl bg-rose-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-rose-700"
                        >
                            End Session
                        </button>
                    </div>
                </div>
            </div>

            <div class="rounded-2xl border border-slate-200 dark:border-slate-700 p-5">
                <div class="flex items-center justify-between">
                    <h3 class="text-base font-semibold text-slate-900 dark:text-white">Recent sessions</h3>
                    <span class="text-xs text-slate-500 dark:text-slate-400">Last 5</span>
                </div>

                <div class="mt-4 overflow-hidden rounded-xl border border-slate-200 dark:border-slate-700">
                    <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-700 text-sm">
                        <thead class="bg-slate-50 dark:bg-slate-900/60 text-slate-500 dark:text-slate-400">
                            <tr>
                                <th class="px-4 py-3 text-left font-medium">Started</th>
                                <th class="px-4 py-3 text-left font-medium">Status</th>
                                <th class="px-4 py-3 text-left font-medium">Work</th>
                                <th class="px-4 py-3 text-left font-medium">Elapsed</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 dark:divide-slate-700 bg-white dark:bg-slate-800">
                            @forelse ($recentSessions as $session)
                                <tr>
                                    <td class="px-4 py-3 text-slate-700 dark:text-slate-200">
                                        {{ optional($session->started_at)->format('M d, Y h:i A') }}
                                    </td>
                                    <td class="px-4 py-3">
                                        <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold
                                            {{ $session->status === 'afk' ? 'bg-amber-100 text-amber-800' : ($session->status === 'ended' ? 'bg-slate-200 text-slate-700' : 'bg-emerald-100 text-emerald-800') }}">
                                            {{ strtoupper($session->status) }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-slate-700 dark:text-slate-200">{{ $session->work_status }}</td>
                                    <td class="px-4 py-3 text-slate-700 dark:text-slate-200">
                                        {{ optional($session->started_at)->diffForHumans($session->ended_at ?? now(), true) }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-4 py-6 text-center text-slate-500 dark:text-slate-400">
                                        No monitoring sessions yet.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="rounded-2xl border border-slate-200 dark:border-slate-700 p-5">
            <div class="flex items-center justify-between">
                <h3 class="text-base font-semibold text-slate-900 dark:text-white">Session events</h3>
                <span class="text-xs text-slate-500 dark:text-slate-400">Latest 12</span>
            </div>

            <div class="mt-4 space-y-3">
                @forelse ($sessionEvents as $event)
                    <div class="rounded-xl bg-slate-50 dark:bg-slate-900/60 p-4">
                        <div class="flex items-center justify-between gap-3">
                            <p class="text-sm font-semibold text-slate-900 dark:text-white">{{ $event->label }}</p>
                            <span class="text-[11px] uppercase tracking-[0.2em] text-sky-500 dark:text-sky-300">{{ $event->event_type }}</span>
                        </div>
                        <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">
                            {{ optional($event->occurred_at)->format('M d, Y h:i A') }}
                        </p>
                        @if ($event->details)
                            <p class="mt-2 text-sm text-slate-700 dark:text-slate-300">{{ $event->details }}</p>
                        @endif
                    </div>
                @empty
                    <div class="rounded-xl border border-dashed border-slate-300 dark:border-slate-600 p-6 text-center text-sm text-slate-500 dark:text-slate-400">
                        Start a session to begin capturing heartbeat and AFK events.
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
