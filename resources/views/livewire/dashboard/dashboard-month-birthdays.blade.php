<div class="w-full">
    <style>
        @keyframes wiggle {
            0%, 100% { transform: rotate(-5deg); }
            50% { transform: rotate(5deg); }
        }
        .animate-wiggle {
            animation: wiggle 1s ease-in-out infinite;
        }
    </style>

    <div id="confetti-container" class="w-full rounded-lg overflow-hidden relative">
        <div class="p-6 w-full flex-col items-center justify-center">
            <p class="text-center text-2xl font-semibold text-gray-800 dark:text-gray-50">Birthdays this Month</p>
            @if($birthdayEmployees)
                @foreach ($birthdayEmployees as $emp)
                    <div class="flex flex-col sm:flex-row justify-between w-full items-left py-2 {{ $loop->last ? '' : 'border-b border-slate-500/30' }}">
                        <div>
                            <div class="flex gap-3 items-center">
                                @if ($emp && $emp->profile_photo_path)
                                    <img src="{{ route('profile-photo.file', ['filename' => basename($emp->profile_photo_path)]) }}"
                                        alt="{{ $emp ? $emp->name : '' }}"
                                        style="width: 35px; height: 35px;"
                                        class="rounded-full object-cover border border-gray-500">
                                @elseif($emp && $emp->name)
                                    <div class="rounded-full bg-gray-500 border border-gray-500 dark:bg-gray-600 flex items-center justify-center text-white text-xs font-medium" style="width: 35px; height: 35px;">
                                        {{ strtoupper(substr(($emp ? $emp->name : ''), 0, 1)) }}{{ strtoupper(substr(explode(' ', ($emp ? $emp->name : ''))[1] ?? '', 0, 1)) }}
                                    </div>
                                @endif
                                <span class="text-gray-700 dark:text-gray-200 text-sm">{{ $emp->surname . ', ' . $emp->first_name }} <span class="opacity-70">- {{ $emp->age }} yrs. old </span></span>
                            </div> 
                        </div>
                        <div class="flex gap-4">
                            <p class="text-sm">{{ Carbon\Carbon::parse($emp->date_of_birth)->format('F d, Y') }}</p>
                            <span class="inline-block animate-wiggle">🎂</span>
                        </div>
                    </div>
                @endforeach
            @endif
        </div>
        <canvas id="confetti-canvas" class="absolute top-0 left-0 w-full h-full"></canvas>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.5.1/dist/confetti.browser.min.js"></script>
<script>
    const canvas = document.getElementById('confetti-canvas');
    const confettiInstance = confetti.create(canvas, { resize: true, useWorker: true });

    function playConfetti() {
        const duration = 3 * 1000;
        const defaults = { startVelocity: 30, spread: 360, ticks: 60, zIndex: 0 };
        const animationEnd = Date.now() + duration;

        function randomInRange(min, max) {
            return Math.random() * (max - min) + min;
        }

        const interval = setInterval(function () {
            const timeLeft = animationEnd - Date.now();
            if (timeLeft <= 0) return clearInterval(interval);

            const particleCount = 50 * (timeLeft / duration);
            
            confettiInstance({
                ...defaults,
                particleCount,
                origin: { x: randomInRange(0.1, 0.3), y: Math.random() - 0.2 }
            });
            confettiInstance({
                ...defaults,
                particleCount,
                origin: { x: randomInRange(0.7, 0.9), y: Math.random() - 0.2 }
            });
        }, 250);
    }

    window.onload = playConfetti;
    setInterval(playConfetti, 10000);
</script>


