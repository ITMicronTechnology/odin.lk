@extends('admin.layout')

@section('title', 'Visitor Analytics')

@section('actions')
    <div class="btn-group btn-group-sm" role="group" aria-label="Period">
        @foreach ($ranges as $key => [$label])
            <a href="{{ route('admin.analytics.index', ['range' => $key]) }}" class="btn {{ $range === $key ? 'btn-odin' : 'btn-light border' }}">{{ $label }}</a>
        @endforeach
    </div>
@endsection

@section('content')
    @php
        $deviceIcons = ['mobile' => 'bi-phone', 'tablet' => 'bi-tablet', 'desktop' => 'bi-laptop'];
        $deviceTotal = max(1, $devices->sum('visits'));
        $countryTotal = max(1, $countries->sum('visits'));
        $periodLabel = strtolower($ranges[$range][0]);
    @endphp

    {{-- Headline numbers --}}
    <div class="row g-3 mb-3">
        <div class="col-sm-6 col-xl-3">
            <div class="card kpi h-100">
                <div class="icon" style="background: var(--odin-blue)">👥</div>
                <div>
                    <div class="label">Total visitors (all time)</div>
                    <div class="value">{{ number_format($summary['total_visitors']) }}</div>
                    <div class="small text-muted">{{ number_format($summary['range_visitors']) }} in {{ $periodLabel }} · {{ number_format($summary['new_visitors']) }} new</div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card kpi h-100">
                <div class="icon" style="background: var(--odin-red)">👀</div>
                <div>
                    <div class="label">Visits / sessions ({{ $periodLabel }})</div>
                    <div class="value">{{ number_format($summary['total_visits']) }}</div>
                    <div class="small text-muted">{{ number_format($summary['page_views']) }} page views · {{ $summary['pages_per_visit'] }} pages/visit</div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card kpi h-100">
                <div class="icon" style="background: #16a34a">🟢</div>
                <div>
                    <div class="label">Users online now <span class="live-dot" title="Updates every 20 seconds"></span></div>
                    <div class="value" id="liveOnline">{{ number_format($summary['online']) }}</div>
                    <div class="small text-muted">active in the last {{ config('analytics.online_minutes') }} minutes</div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card kpi h-100">
                <div class="icon" style="background: var(--odin-black)">🧑‍💼</div>
                <div>
                    <div class="label">Registered customers</div>
                    <div class="value">{{ number_format($summary['registered_customers']) }}</div>
                    <div class="small text-muted">{{ number_format($summary['logged_in_visitors']) }} visited while logged in · {{ $summary['bounce_rate'] }}% bounce</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Daily / weekly / monthly + chart, devices --}}
    <div class="row g-3 mb-3">
        <div class="col-xl-8">
            <div class="card h-100">
                <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
                    <span>📅 Daily / weekly / monthly visitors</span>
                    <div class="d-flex gap-4 small">
                        <div><span class="text-muted">Today</span> <strong class="fs-5 ms-1" id="liveToday">{{ number_format($summary['daily']) }}</strong></div>
                        <div><span class="text-muted">7 days</span> <strong class="fs-5 ms-1">{{ number_format($summary['weekly']) }}</strong></div>
                        <div><span class="text-muted">30 days</span> <strong class="fs-5 ms-1">{{ number_format($summary['monthly']) }}</strong></div>
                    </div>
                </div>
                <div class="card-body"><canvas id="visitorsChart" height="110"></canvas></div>
            </div>
        </div>
        <div class="col-xl-4">
            <div class="card h-100">
                <div class="card-header">📱 Mobile vs desktop</div>
                <div class="card-body">
                    @if ($devices->isEmpty())
                        <p class="text-muted small mb-0">No visits in this period yet.</p>
                    @else
                        <div class="mx-auto mb-3" style="max-width: 210px"><canvas id="deviceChart"></canvas></div>
                        @foreach ($devices as $device)
                            <div class="d-flex justify-content-between align-items-center py-1 border-bottom small">
                                <span><i class="bi {{ $deviceIcons[$device->name] ?? 'bi-question-circle' }} me-2"></i>{{ ucfirst($device->name) }}</span>
                                <span><strong>{{ round($device->visits / $deviceTotal * 100) }}%</strong> <span class="text-muted">· {{ number_format($device->visits) }} visits</span></span>
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Countries + browsers/OS --}}
    <div class="row g-3 mb-3">
        <div class="col-xl-6">
            <div class="card h-100">
                <div class="card-header">🌍 Countries</div>
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead><tr><th>Country</th><th class="text-end">Visitors</th><th class="text-end">Visits</th><th style="width: 30%"></th></tr></thead>
                        <tbody>
                            @forelse ($countries as $country)
                                <tr>
                                    <td><span class="fs-5 me-2">{{ $country->flag }}</span>{{ $country->label }}</td>
                                    <td class="text-end">{{ number_format($country->visitors) }}</td>
                                    <td class="text-end">{{ number_format($country->visits) }}</td>
                                    <td><div class="progress" style="height: 6px"><div class="progress-bar bg-danger" style="width: {{ round($country->visits / $countryTotal * 100) }}%"></div></div></td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="text-muted text-center py-4">No visits in this period yet.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-xl-6">
            <div class="card h-100">
                <div class="card-header">🌐 Browsers &amp; devices</div>
                <div class="row g-0">
                    @foreach (['Browser' => $browsers, 'Operating system' => $systems] as $heading => $rows)
                        <div class="col-md-6 {{ $loop->first ? 'border-end' : '' }}">
                            <table class="table align-middle mb-0">
                                <thead><tr><th>{{ $heading }}</th><th class="text-end">Visits</th></tr></thead>
                                <tbody>
                                    @forelse ($rows as $row)
                                        <tr><td>{{ $row->name }}</td><td class="text-end">{{ number_format($row->visits) }}</td></tr>
                                    @empty
                                        <tr><td colspan="2" class="text-muted small">No data yet.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    {{-- Pages + referrers --}}
    <div class="row g-3 mb-3">
        <div class="col-xl-8">
            <div class="card h-100">
                <div class="card-header">📄 Pages visitors view</div>
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead><tr><th>Page</th><th class="text-end">Views</th><th class="text-end">Unique visitors</th></tr></thead>
                        <tbody>
                            @forelse ($pages as $page)
                                <tr>
                                    <td>
                                        <span class="badge text-bg-light border me-2">{{ $page->label }}</span>
                                        <a href="{{ url($page->path) }}" target="_blank" class="small text-break">{{ $page->path }}</a>
                                    </td>
                                    <td class="text-end fw-semibold">{{ number_format($page->views) }}</td>
                                    <td class="text-end">{{ number_format($page->visitors) }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="3" class="text-muted text-center py-4">No page views in this period yet.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-xl-4">
            <div class="card h-100">
                <div class="card-header">🔗 Where visitors come from</div>
                <ul class="list-group list-group-flush">
                    @forelse ($referrers as $referrer)
                        <li class="list-group-item d-flex justify-content-between small">
                            <span class="text-truncate me-2">{{ $referrer->name ?? 'Direct / typed address' }}</span>
                            <strong>{{ number_format($referrer->visits) }}</strong>
                        </li>
                    @empty
                        <li class="list-group-item text-muted small">No visits in this period yet.</li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>

    {{-- Live + recent --}}
    <div class="row g-3">
        <div class="col-xl-5">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between">🟢 Online right now <span class="small text-muted">auto-updates</span></div>
                <ul class="list-group list-group-flush" id="liveList">
                    @forelse ($online as $visit)
                        <li class="list-group-item small d-flex justify-content-between gap-2">
                            <span><span class="me-1">{{ $stats->flag($visit->country_code) }}</span> <i class="bi {{ $deviceIcons[$visit->device_type] ?? 'bi-display' }}"></i> {{ $visit->browser }} · <span class="text-break">{{ $visit->current_path ?? $visit->landing_path }}</span></span>
                            <span class="text-muted text-nowrap">{{ $visit->last_activity_at->diffForHumans() }}</span>
                        </li>
                    @empty
                        <li class="list-group-item small text-muted">Nobody is on the site right now.</li>
                    @endforelse
                </ul>
            </div>
        </div>
        <div class="col-xl-7">
            <div class="card h-100">
                <div class="card-header">🕒 Latest visits</div>
                <div class="table-responsive">
                    <table class="table align-middle mb-0 small">
                        <thead><tr><th>When</th><th>Visitor</th><th>Device</th><th>Landing page</th><th class="text-end">Pages</th></tr></thead>
                        <tbody>
                            @forelse ($recent as $visit)
                                <tr>
                                    <td class="text-nowrap">{{ $visit->started_at->diffForHumans() }}</td>
                                    <td class="text-nowrap">
                                        {{ $stats->flag($visit->country_code) }}
                                        {{ $visit->visitor->user?->name ?? 'Guest' }}
                                    </td>
                                    <td class="text-nowrap"><i class="bi {{ $deviceIcons[$visit->device_type] ?? 'bi-display' }}"></i> {{ $visit->browser }} / {{ $visit->os }}</td>
                                    <td class="text-break">{{ $visit->landing_path }}</td>
                                    <td class="text-end">{{ $visit->page_views_count }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="text-muted text-center py-4">No visits yet.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <p class="small text-muted mt-3 mb-0">
        <i class="bi bi-info-circle me-1"></i>
        Counts storefront pages only. Bots, admin pages{{ config('analytics.exclude_admins') ? ' and your own browsing while logged in as admin' : '' }} are not counted.
        A visit ends after {{ config('analytics.visit_timeout_minutes') }} minutes without activity. Visitors are identified by an anonymous cookie, so the same person on two devices counts twice.
    </p>
@endsection

@push('scripts')
    <script src="{{ asset('vendor/chartjs/chart.umd.min.js') }}"></script>
    <script>
        (function () {
            const daily = @json($daily);
            new Chart(document.getElementById('visitorsChart'), {
                type: 'line',
                data: {
                    labels: daily.map(d => d.label),
                    datasets: [
                        { label: 'Unique visitors', data: daily.map(d => d.visitors), borderColor: '#e10600', backgroundColor: 'rgba(225, 6, 0, .1)', fill: true, tension: .35 },
                        { label: 'Visits', data: daily.map(d => d.visits), borderColor: '#0a3d91', backgroundColor: '#0a3d91', tension: .35 },
                        { label: 'Page views', data: daily.map(d => d.views), borderColor: '#9ca3af', backgroundColor: '#9ca3af', borderDash: [5, 4], tension: .35 }
                    ]
                },
                options: {
                    interaction: { mode: 'index', intersect: false },
                    plugins: { legend: { position: 'bottom' } },
                    scales: { y: { beginAtZero: true, ticks: { precision: 0 } } }
                }
            });

            const devices = @json($devices);
            const deviceCanvas = document.getElementById('deviceChart');
            if (deviceCanvas && devices.length) {
                const colors = { mobile: '#e10600', desktop: '#0a3d91', tablet: '#0b0b0f' };
                new Chart(deviceCanvas, {
                    type: 'doughnut',
                    data: {
                        labels: devices.map(d => d.name.charAt(0).toUpperCase() + d.name.slice(1)),
                        datasets: [{ data: devices.map(d => d.visits), backgroundColor: devices.map(d => colors[d.name] || '#9ca3af'), borderWidth: 0 }]
                    },
                    options: { cutout: '65%', plugins: { legend: { display: false } } }
                });
            }

            // Refresh the "online now" widgets every 20 seconds.
            const icons = { mobile: 'bi-phone', tablet: 'bi-tablet', desktop: 'bi-laptop' };
            setInterval(function () {
                $.getJSON(@json(route('admin.analytics.live'))).done(function (data) {
                    $('#liveOnline').text(data.online.toLocaleString());
                    $('#liveToday').text(data.today.toLocaleString());
                    const $list = $('#liveList').empty();
                    if (!data.visits.length) {
                        $list.append($('<li class="list-group-item small text-muted"></li>').text('Nobody is on the site right now.'));
                    }
                    data.visits.forEach(function (visit) {
                        $('<li class="list-group-item small d-flex justify-content-between gap-2"></li>')
                            .append($('<span></span>')
                                .append($('<span class="me-1"></span>').text(visit.flag))
                                .append($('<i class="bi"></i>').addClass(icons[visit.device] || 'bi-display'))
                                .append(document.createTextNode(' ' + visit.browser + ' · '))
                                .append($('<span class="text-break"></span>').text(visit.path)))
                            .append($('<span class="text-muted text-nowrap"></span>').text(visit.seen))
                            .appendTo($list);
                    });
                });
            }, 20000);
        })();
    </script>
@endpush
