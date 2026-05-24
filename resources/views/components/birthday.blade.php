<div class="relative bg-gradient-to-br from-pink-400 via-purple-500 to-indigo-600 dark:from-pink-600 dark:via-purple-700 dark:to-indigo-800 p-6 sm:p-8 rounded-2xl overflow-hidden mb-8 shadow-2xl border-2 border-white/20">
    <!-- Animated Background Pattern -->
    <div class="absolute inset-0 opacity-20">
        <div class="absolute top-4 left-4 w-8 h-8 bg-yellow-300 rounded-full animate-pulse"></div>
        <div class="absolute top-12 right-8 w-6 h-6 bg-pink-300 rounded-full animate-bounce" style="animation-delay: 0.2s;"></div>
        <div class="absolute bottom-8 left-12 w-10 h-10 bg-blue-300 rounded-full animate-pulse" style="animation-delay: 0.4s;"></div>
        <div class="absolute bottom-4 right-16 w-4 h-4 bg-green-300 rounded-full animate-bounce" style="animation-delay: 0.6s;"></div>
        <div class="absolute top-1/2 left-1/4 w-5 h-5 bg-orange-300 rounded-full animate-pulse" style="animation-delay: 0.8s;"></div>
        <div class="absolute top-1/3 right-1/3 w-7 h-7 bg-red-300 rounded-full animate-bounce" style="animation-delay: 1s;"></div>
    </div>

    <!-- Floating Birthday Elements -->
    <div class="absolute right-6 top-6 animate-float">
        <div class="text-4xl animate-bounce-slow">🎈</div>
    </div>
    <div class="absolute right-16 top-8 animate-float" style="animation-delay: 0.5s;">
        <div class="text-3xl animate-bounce-slow" style="animation-delay: 0.3s;">🎉</div>
    </div>
    <div class="absolute right-8 top-16 animate-float" style="animation-delay: 1s;">
        <div class="text-2xl animate-bounce-slow" style="animation-delay: 0.6s;">🎁</div>
    </div>
    
    <!-- Left side decorative elements -->
    <div class="absolute left-4 bottom-6 animate-float" style="animation-delay: 1.5s;">
        <div class="text-3xl animate-spin-slow">🌟</div>
    </div>
    <div class="absolute left-12 top-4 animate-float" style="animation-delay: 0.8s;">
        <div class="text-2xl animate-pulse">✨</div>
    </div>

    <!-- Main Content -->
    <div class="relative z-10">
        <h1 class="text-3xl md:text-4xl lg:text-5xl font-extrabold mb-3 text-white drop-shadow-lg">
            🎊 Happy Birthday, 
            <span class="bg-gradient-to-r from-yellow-300 via-pink-300 to-blue-300 bg-clip-text text-transparent animate-pulse">
                {{ Auth::user()->name }}
            </span>
            <span class="inline-block animate-wiggle text-yellow-300 ml-2">🎂</span>
        </h1>
        <div class="flex items-center space-x-2 mb-4">
            <div class="flex space-x-1">
                <span class="text-2xl animate-bounce" style="animation-delay: 0.1s;">🎵</span>
                <span class="text-2xl animate-bounce" style="animation-delay: 0.2s;">🎶</span>
                <span class="text-2xl animate-bounce" style="animation-delay: 0.3s;">🎵</span>
            </div>
            <p class="text-white/90 text-lg font-medium">
                Wishing you joy, laughter, and endless happiness!
            </p>
        </div>
        <div class="bg-white/20 backdrop-blur-sm rounded-xl p-4 border border-white/30">
            <p class="text-white font-semibold text-lg mb-1">
                May your special day be filled with wonderful moments!
            </p>
            <p class="text-white/80 text-right font-medium">
                - With love, JJWC ❤️
            </p>
        </div>
    </div>

    <!-- Decorative corner elements -->
    <div class="absolute top-0 left-0 w-20 h-20 bg-gradient-to-br from-yellow-300/30 to-transparent rounded-br-full"></div>
    <div class="absolute bottom-0 right-0 w-24 h-24 bg-gradient-to-tl from-pink-300/30 to-transparent rounded-tl-full"></div>
</div>

<style>
    @keyframes wiggle {
        0%, 100% { transform: rotate(-5deg) scale(1); }
        50% { transform: rotate(5deg) scale(1.1); }
    }
    
    @keyframes float {
        0%, 100% { transform: translateY(0px) rotate(0deg); }
        50% { transform: translateY(-10px) rotate(5deg); }
    }
    
    @keyframes bounce-slow {
        0%, 20%, 53%, 80%, 100% { transform: translateY(0) scale(1); }
        40%, 43% { transform: translateY(-8px) scale(1.05); }
        70% { transform: translateY(-4px) scale(1.02); }
    }
    
    @keyframes spin-slow {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }
    
    .animate-wiggle {
        animation: wiggle 1.5s ease-in-out infinite;
    }
    
    .animate-float {
        animation: float 3s ease-in-out infinite;
    }
    
    .animate-bounce-slow {
        animation: bounce-slow 2s infinite;
    }
    
    .animate-spin-slow {
        animation: spin-slow 4s linear infinite;
    }
</style>

<script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.5.1/dist/confetti.browser.min.js"></script>
<script>
    function playConfetti() {
        const duration = 3 * 1000;
        const defaults = { startVelocity: 30, spread: 360, ticks: 60, zIndex: 0 };
        const animationEnd = Date.now() + duration;

        function randomInRange(min, max) {
            return Math.random() * (max - min) + min;
        }

        const interval = setInterval(function() {
            const timeLeft = animationEnd - Date.now();

            if (timeLeft <= 0) {
                return clearInterval(interval);
            }

            const particleCount = 50 * (timeLeft / duration);

            confetti({
                ...defaults,
                particleCount,
                origin: { x: randomInRange(0.1, 0.3), y: Math.random() - 0.2 }
            });
            confetti({
                ...defaults,
                particleCount,
                origin: { x: randomInRange(0.7, 0.9), y: Math.random() - 0.2 }
            });
        }, 250);
    }

    window.onload = function() {
        playConfetti();
    }
    
    setInterval(playConfetti, 10000);
</script>