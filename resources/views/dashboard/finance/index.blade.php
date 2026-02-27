@extends('layouts/contentNavbarLayout')

@section('title', 'Finance Dashboard')

@section('content')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">

    <style>
        :root {
            --brand-green: #22c55e;
            --brand-green-light: #dcfce7;
            --brand-green-dark: #16a34a;
            --text-primary: #374151;
            --text-secondary: #6b7280;
            --text-muted: #9ca3af;
            --border-light: #f3f4f6;
            --bg-white: #ffffff;
            --bg-gray-50: #f9fafb;
        }

        body {
            background-color: var(--bg-gray-50);
        }

        .dashboard-card {
            border: 1px solid var(--border-light);
            border-radius: 8px;
            background: var(--bg-white);
            transition: all 0.2s ease;
        }

        .dashboard-card:hover {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            border-color: #e5e7eb;
        }

        .metric-card {
            padding: 1.25rem;
            position: relative;
        }

        .metric-icon {
            width: 36px;
            height: 36px;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            margin-bottom: 12px;
        }

        .metric-value {
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 4px;
            color: var(--text-primary);
            line-height: 1.2;
        }

        .metric-label {
            font-size: 0.75rem;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.025em;
            color: var(--text-secondary);
            margin-bottom: 8px;
        }

        .metric-subtitle {
            font-size: 0.7rem;
            color: var(--text-muted);
        }

        .chart-container {
            background: var(--bg-white);
            border-radius: 8px;
            padding: 1.5rem;
            border: 1px solid var(--border-light);
        }

        .chart-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 16px;
            padding-bottom: 12px;
            border-bottom: 1px solid var(--border-light);
        }

        .chart-title {
            font-size: 0.875rem;
            font-weight: 600;
            color: var(--text-primary);
            margin: 0;
        }

        .period-badge {
            background: var(--brand-green-light);
            color: var(--brand-green-dark);
            padding: 3px 8px;
            border-radius: 12px;
            font-size: 0.65rem;
            font-weight: 500;
            border: 1px solid rgba(34, 197, 94, 0.2);
        }

        .dashboard-header {
            background: var(--bg-white);
            border-bottom: 1px solid var(--border-light);
            padding: 1.5rem 0;
            margin: -1.5rem -1.5rem 1.5rem -1.5rem;
        }

        .dashboard-title {
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 0.25rem;
            color: var(--text-primary);
        }

        .dashboard-subtitle {
            color: var(--text-secondary);
            font-weight: 400;
            font-size: 0.875rem;
        }

        /* Specific metric card styles */
        .revenue-icon {
            background: var(--brand-green-light);
            color: var(--brand-green-dark);
        }

        .cost-icon {
            background: #fef2f2;
            color: #dc2626;
        }

        .profit-icon {
            background: var(--brand-green-light);
            color: var(--brand-green-dark);
        }

        .commission-icon {
            background: #f8fafc;
            color: #64748b;
        }

        .monthly-stats-card {
            background: var(--bg-white);
            border: 1px solid var(--border-light);
            border-radius: 8px;
        }

        .monthly-stats-header {
            background: var(--brand-green);
            color: white;
            padding: 1rem 1.25rem;
            border-radius: 7px 7px 0 0;
            margin: -1px -1px 0 -1px;
        }

        .stats-row {
            padding: 0.75rem 1.25rem;
            border-bottom: 1px solid var(--border-light);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .stats-row:last-child {
            border-bottom: none;
        }

        .stats-label {
            font-weight: 500;
            color: var(--text-secondary);
            font-size: 0.8rem;
        }

        .stats-value {
            font-weight: 600;
            font-size: 0.9rem;
            color: var(--text-primary);
        }

        .commission-highlight {
            background: linear-gradient(135deg, var(--brand-green) 0%, var(--brand-green-dark) 100%);
            color: white;
        }

        .commission-highlight .metric-icon {
            background: rgba(255, 255, 255, 0.2);
            color: white;
        }

        .commission-highlight .metric-value,
        .commission-highlight .metric-label,
        .commission-highlight .metric-subtitle {
            color: white;
        }

        .commission-highlight .metric-label {
            opacity: 0.9;
        }

        .commission-highlight .metric-subtitle {
            opacity: 0.8;
        }

        @media (max-width: 768px) {
            .dashboard-title {
                font-size: 1.25rem;
            }

            .metric-value {
                font-size: 1.25rem;
            }

            .metric-card {
                padding: 1rem;
            }
        }
    </style>

    <div class="container-fluid py-2">
        <!-- Dashboard Header -->
        <div class="dashboard-header">
            <div class="container-fluid">
                <h1 class="dashboard-title">
                    <i class="fas fa-chart-line me-2 text-success"></i>
                    Finance Dashboard
                </h1>
                <p class="dashboard-subtitle">Financial overview and analytics</p>
            </div>
        </div>

        {{-- @php
            function ugx($value)
            {
                return 'UGX ' . number_format($value, 0, '.', ',');
            }
        @endphp --}}

        <form method="GET" class="row g-2 align-items-end mb-4">
            <div class="col-md-2">
                <label class="form-label fw-semibold">Partner Filter</label>
                <select name="partner_id" class="form-select">
                    <option value="">All</option>
                    <?php foreach($partners as $partner): ?>
                    <option value="{{ $partner->id }}" {{ $partner_id == $partner->id ? 'selected' : '' }}>
                        {{ $partner->Institution_Name }}</option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label fw-semibold">Filter By</label>
                <select name="filter" class="form-select" onchange="toggleDateInputs(this.value)">
                    <option value="today" {{ $filter == 'today' ? 'selected' : '' }}>Today</option>
                    <option value="this_week" {{ $filter == 'this_week' ? 'selected' : '' }}>This Week</option>
                    <option value="this_month" {{ $filter == 'this_month' ? 'selected' : '' }}>This Month</option>
                    <option value="custom" {{ $filter == 'custom' ? 'selected' : '' }}>Custom Range</option>
                </select>
            </div>
            <div class="col-md-2 d-none" id="customDates">
                <label class="form-label fw-semibold">From</label>
                <input type="date" name="from" value="{{ $from }}" class="form-control">
            </div>
            <div class="col-md-2 d-none" id="customTo">
                <label class="form-label fw-semibold">To</label>
                <input type="date" name="to" value="{{ $to }}" class="form-control">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-dark">
                    <i class="fas fa-filter"></i> Apply Filter
                </button>
            </div>
            <div class="col-md-1">
                <a class="btn btn-primary"
                    href="{{ route('finance.export.excel', request()->only(['filter', 'from', 'to', 'partner_id'])) }}">
                    <i class="fas fa-filter"></i> Excel
                </a>
            </div>
            <div class="col-md-1">
                <a class="btn btn-danger"
                    href="{{ route('finance.export.pdf', request()->only(['filter', 'from', 'to', 'partner_id'])) }}">
                    <i class="fas fa-filter"></i> PDF
                </a>
            </div>
        </form>
        <div class="row g-3">


            <div class="col-lg-3 col-md-6">
                <div class="dashboard-card h-100">
                    <div class="metric-card">
                        <div class="metric-icon cost-icon">
                            <i class="fas fa-arrow-trend-down"></i>
                        </div>
                        <div class="metric-label">Sales Commissions</div>
                        <div class="metric-value">{{ $totalCommission }}</div>
                        <div class="metric-subtitle">
                            <i class="fas fa-calendar-alt me-1"></i>
                            All time
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="dashboard-card h-100">
                    <div class="metric-card">
                        <div class="metric-icon profit-icon">
                            <i class="fas fa-coins"></i>
                        </div>
                        <div class="metric-label"> SMS Notifications</div>
                        <div class="metric-value">{{ $smsRevenue }}</div>
                        <div class="metric-subtitle">
                            <i class="fas fa-chart-line me-1"></i>
                            Total earnings
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="dashboard-card h-100">
                    <div class="metric-card">
                        <div class="metric-icon revenue-icon">
                            <i class="fas fa-arrow-trend-up"></i>
                        </div>
                        <div class="metric-label">Bulk SMS</div>
                        <div class="metric-value">{{ $bulkSmsRevenue }}</div>
                        <div class="metric-subtitle">
                            <i class="fas fa-calendar-alt me-1"></i>
                            All time
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card shadow-sm my-4">
            <div class="card-header text-white">
                <h5 class="mb-0">Partner Revenue Summary</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Partner</th>
                                <th>SMS Revenue</th>
                                <th>Bulk SMS Revenue</th>
                                <th>Commission</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($tableData as $row)
                                <tr>
                                    <td>{{ $row['partner_name'] }}</td>
                                    <td>{{ number_format($row['sms_revenue']) }}</td>
                                    <td>{{ number_format($row['bulk_sms_revenue']) }}</td>
                                    <td>{{ number_format($row['commission']) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>



        <!-- Analytics Charts -->
        <div class="row g-3 mt-5">
            <div class="col-lg-12">
                <div class="chart-container">
                    <div class="chart-header">
                        <h6 class="chart-title">
                            <i class="fas fa-chart-column me-2"></i>
                            Revenue Streams Comparison
                        </h6>
                        <span class="period-badge">Last 6 Months</span>
                    </div>
                    <div style="position: relative; height: 250px;">
                        <canvas id="revenueCostChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Analytics Charts -->
        <div class="row g-3 mt-5">
            <div class="col-lg-6">
                <div class="chart-container">
                    <div class="chart-header">
                        <h6 class="chart-title">
                            <i class="fas fa-chart-line me-2"></i>
                            SMS Revenue Trend
                        </h6>
                        <span class="period-badge">Last 6 Months</span>
                    </div>
                    <div style="position: relative; height: 250px;">
                        <canvas id="smsProfitChart"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="chart-container">
                    <div class="chart-header">
                        <h6 class="chart-title">
                            <i class="fas fa-chart-line me-2"></i>
                            Commissions Trend
                        </h6>
                        <span class="period-badge">Last 6 Months</span>
                    </div>
                    <div style="position: relative; height: 250px;">
                        <canvas id="commissionChart"></canvas>
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="chart-container">
                    <div class="chart-header">
                        <h6 class="chart-title">
                            <i class="fas fa-chart-column me-2"></i>
                            Bulk SMS Revenue Trend
                        </h6>
                        <span class="period-badge">Last 6 Months</span>
                    </div>
                    <div style="position: relative; height: 250px;">
                        <canvas id="feesRevenueChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Chart.js configuration
        Chart.defaults.font.family = "'system-ui', '-apple-system', sans-serif";
        Chart.defaults.font.size = 11;
        Chart.defaults.color = '#6b7280';

        const labels = @json(collect($monthlyData)->pluck('month'));
        const smsRevenue = @json(collect($monthlyData)->pluck('monthlySms'));
        const bulkSmsRevenue = @json(collect($monthlyData)->pluck('bulkMonthlySms'));
        const commission = @json(collect($monthlyData)->pluck('monthlyCommissions'));
        // Revenue vs Cost Chart
        new Chart(document.getElementById('revenueCostChart'), {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'SMS',
                    data: smsRevenue,
                    backgroundColor: 'blue',
                    borderColor: 'blue',
                    borderWidth: 1,
                    borderRadius: 4,
                    borderSkipped: false,
                }, {
                    label: 'Bulk SMS',
                    data: bulkSmsRevenue,
                    backgroundColor: 'red',
                    borderColor: 'red',
                    borderWidth: 1,
                    borderRadius: 4,
                    borderSkipped: false,
                }, {
                    label: 'Sales',
                    data: commission,
                    backgroundColor: 'yellow',
                    borderColor: 'yellow',
                    borderWidth: 1,
                    borderRadius: 4,
                    borderSkipped: false,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                        labels: {
                            usePointStyle: true,
                            padding: 15,
                            font: {
                                size: 11,
                                weight: 500
                            }
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        titleColor: '#ffffff',
                        bodyColor: '#ffffff',
                        borderColor: 'rgba(255, 255, 255, 0.1)',
                        borderWidth: 1,
                        cornerRadius: 6,
                        callbacks: {
                            label: function(context) {
                                return context.dataset.label + ': UGX ' +
                                    new Intl.NumberFormat().format(context.parsed.y);
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            font: {
                                size: 10
                            }
                        }
                    },
                    y: {
                        grid: {
                            color: '#f3f4f6'
                        },
                        ticks: {
                            font: {
                                size: 10
                            },
                            callback: function(value) {
                                return 'UGX ' + new Intl.NumberFormat().format(value);
                            }
                        }
                    }
                }
            }
        });

        // Revenue vs Cost Chart
        new Chart(document.getElementById('feesRevenueChart'), {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Bulk SMS',
                    data: bulkSmsRevenue,
                    backgroundColor: '#22c55e',
                    borderColor: '#16a34a',
                    borderWidth: 1,
                    borderRadius: 4,
                    borderSkipped: false,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                        labels: {
                            usePointStyle: true,
                            padding: 15,
                            font: {
                                size: 11,
                                weight: 500
                            }
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        titleColor: '#ffffff',
                        bodyColor: '#ffffff',
                        borderColor: 'rgba(255, 255, 255, 0.1)',
                        borderWidth: 1,
                        cornerRadius: 6,
                        callbacks: {
                            label: function(context) {
                                return context.dataset.label + ': UGX ' +
                                    new Intl.NumberFormat().format(context.parsed.y);
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            font: {
                                size: 10
                            }
                        }
                    },
                    y: {
                        grid: {
                            color: '#f3f4f6'
                        },
                        ticks: {
                            font: {
                                size: 10
                            },
                            callback: function(value) {
                                return 'UGX ' + new Intl.NumberFormat().format(value);
                            }
                        }
                    }
                }
            }
        });

        // Commission Trend Chart
        new Chart(document.getElementById('smsProfitChart'), {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'SMS Revenue',
                    data: smsRevenue,
                    borderColor: '#22c55e',
                    backgroundColor: 'rgba(34, 197, 94, 0.1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.3,
                    pointBackgroundColor: '#22c55e',
                    pointBorderColor: '#ffffff',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                        labels: {
                            usePointStyle: true,
                            padding: 15,
                            font: {
                                size: 11,
                                weight: 500
                            }
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        titleColor: '#ffffff',
                        bodyColor: '#ffffff',
                        borderColor: 'rgba(255, 255, 255, 0.1)',
                        borderWidth: 1,
                        cornerRadius: 6,
                        callbacks: {
                            label: function(context) {
                                return context.dataset.label + ': UGX ' +
                                    new Intl.NumberFormat().format(context.parsed.y);
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            font: {
                                size: 10
                            }
                        }
                    },
                    y: {
                        grid: {
                            color: '#f3f4f6'
                        },
                        ticks: {
                            font: {
                                size: 10
                            },
                            callback: function(value) {
                                return 'UGX ' + new Intl.NumberFormat().format(value);
                            }
                        }
                    }
                }
            }
        });

        // Commission Trend Chart
        new Chart(document.getElementById('commissionChart'), {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Sales Commission',
                    data: commission,
                    borderColor: '#22c55e',
                    backgroundColor: 'rgba(34, 197, 94, 0.1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.3,
                    pointBackgroundColor: '#22c55e',
                    pointBorderColor: '#ffffff',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                        labels: {
                            usePointStyle: true,
                            padding: 15,
                            font: {
                                size: 11,
                                weight: 500
                            }
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        titleColor: '#ffffff',
                        bodyColor: '#ffffff',
                        borderColor: 'rgba(255, 255, 255, 0.1)',
                        borderWidth: 1,
                        cornerRadius: 6,
                        callbacks: {
                            label: function(context) {
                                return context.dataset.label + ': UGX ' +
                                    new Intl.NumberFormat().format(context.parsed.y);
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            font: {
                                size: 10
                            }
                        }
                    },
                    y: {
                        grid: {
                            color: '#f3f4f6'
                        },
                        ticks: {
                            font: {
                                size: 10
                            },
                            callback: function(value) {
                                return 'UGX ' + new Intl.NumberFormat().format(value);
                            }
                        }
                    }
                }
            }
        });

        function toggleDateInputs(value) {
            const fromEl = document.getElementById('customDates');
            const toEl = document.getElementById('customTo');
            const show = value === 'custom';
            fromEl.classList.toggle('d-none', !show);
            toEl.classList.toggle('d-none', !show);
        }

        document.addEventListener('DOMContentLoaded', () => {
            toggleDateInputs('{{ $filter }}');
        });
    </script>
@endsection
