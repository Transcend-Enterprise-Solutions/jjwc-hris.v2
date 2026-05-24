<div
    id="clock"
    wire:ignore
    x-data="{
        now: new Date(),
        timer: null,
        init() {
            this.timer = setInterval(() => {
                this.now = new Date();
            }, 1000);
        },
        format() {
            return this.now.toLocaleString('en-US', {
                weekday: 'long',
                year: 'numeric',
                month: 'long',
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit',
                hour12: true,
            });
        },
    }"
    x-init="init()"
    x-text="format()"
    class="text-sm font-semibold mb-2 text-gray-900 dark:text-white h-10 text-left"
>
</div>
