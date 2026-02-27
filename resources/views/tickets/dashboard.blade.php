@extends('layouts/contentNavbarLayout')

@section('title', 'Ticket Dashboard')

@section('content')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <div class="container mt-4">
        <h5 class="my-4">Support Tickets Overview</h5>

        <div class="row g-4">
            <div class="col-md-2">
                <div class="card shadow border-0 text-white bg-primary">
                    <div class="card-body">
                        <h6>Total</h6>
                        <h2>{{ $totalTickets }}</h2>
                    </div>
                </div>
            </div>

            <div class="col-md-2">
                <div class="card shadow border-0 text-white bg-warning">
                    <div class="card-body">
                        <h6>Open</h6>
                        <h2>{{ $openTickets }}</h2>
                    </div>
                </div>
            </div>

            <div class="col-md-2">
                <div class="card shadow border-0 text-white bg-success">
                    <div class="card-body">
                        <h6>Resolved</h6>
                        <h2>{{ $resolvedTickets }}</h2>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="card shadow border-0 text-white bg-secondary">
                    <div class="card-body">
                        <h6>Closed</h6>
                        <h2>{{ $closedTickets }}</h2>
                    </div>
                </div>
            </div>

            <div class="col-md-2">
                <div class="card shadow border-0 text-white bg-danger">
                    <div class="card-body">
                        <h6>High Priority</h6>
                        <h2>{{ $highPriorityTickets }}</h2>
                    </div>
                </div>
            </div>

            <div class="col-md-2">
                <div class="card shadow border-0 text-white bg-dark">
                    <div class="card-body">
                        <h6>Locked</h6>
                        <h2>{{ $lockedTickets }}</h2>
                    </div>
                </div>
            </div>
        </div>
        <div class="row mt-5">
            <div class="col-md-12">
                <div class="card shadow">
                    <div class="card-header">
                        <strong>Tickets Created Over Time</strong>
                    </div>
                    <div class="card-body">
                        <canvas id="ticketsChart" height="300" style="width:100%;"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <h5 class="mt-5 mb-3">Ticket Stats by Time Period</h5>
        <div class="row g-4">
            @php
                $timeStats = [
                    ['label' => 'Today', 'color' => 'danger', 'icon' => 'bx-calendar', 'data' => $dailyStats],
                    [
                        'label' => 'This Week',
                        'color' => 'success',
                        'icon' => 'bx-calendar-week',
                        'data' => $weeklyStats,
                    ],
                    [
                        'label' => 'This Month',
                        'color' => 'warning',
                        'icon' => 'bx-calendar-event',
                        'data' => $monthlyStats,
                    ],
                    ['label' => 'This Year', 'color' => 'info', 'icon' => 'bx-calendar', 'data' => $yearlyStats],
                ];
            @endphp

            @foreach ($timeStats as $stat)
                <div class="col-md-3 col-sm-6">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-3">
                                <div class="avatar flex-shrink-0 bg-{{ $stat['color'] }} text-white rounded-circle me-3"
                                    style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
                                    <i class="bx {{ $stat['icon'] }} fs-5"></i>
                                </div>
                                <h6 class="mb-0">{{ $stat['label'] }}</h6>
                            </div>
                            <table class="table table-sm table-striped table-bordered mb-0">
                                <tbody>
                                    <tr>
                                        <th scope="row" class="ps-0">Total</th>
                                        <td class="text-end">{{ $stat['data']['total'] }}</td>
                                    </tr>
                                    <tr>
                                        <th scope="row" class="ps-0">Open</th>
                                        <td class="text-end">{{ $stat['data']['open'] }}</td>
                                    </tr>
                                    <tr>
                                        <th scope="row" class="ps-0">Closed</th>
                                        <td class="text-end">{{ $stat['data']['closed'] }}</td>
                                    </tr>
                                    <tr>
                                        <th scope="row" class="ps-0">Resolved</th>
                                        <td class="text-end">{{ $stat['data']['resolved'] }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endsection
@section('scripts')
    <script>
        const ctx = document.getElementById('ticketsChart').getContext('2d');
        const ticketsChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: {!! json_encode($ticketDataOverTime->pluck('date')) !!},
                datasets: [{
                    label: 'Tickets',
                    data: {!! json_encode($ticketDataOverTime->pluck('total')) !!},
                    backgroundColor: 'rgba(13, 110, 253, 0.2)',
                    borderColor: 'rgba(13, 110, 253, 1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.3
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true,
                        stepSize: 1
                    }
                }
            }
        });
    </script>
@endsection
