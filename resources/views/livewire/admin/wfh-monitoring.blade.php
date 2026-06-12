<div class="w-full" wire:ignore>
    <div
        id="wfh-monitoring-wall"
        data-api-base="{{ url('/wfh-monitoring/api') }}"
        data-initial-date="{{ now()->toDateString() }}"
        data-wall-url="{{ route('wfh-monitoring.wall') }}"
        data-ice-servers='@json(config('wfh_monitoring.ice_servers'))'
    ></div>
</div>
