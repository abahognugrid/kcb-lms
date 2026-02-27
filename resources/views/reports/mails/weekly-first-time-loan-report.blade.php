@extends('layouts/blankLayout')

@section('title', 'Weekly First-Time Loan Report')

@section('content')
<div class="container">
    <h1 class="mb-4" style="color: #dc3545;">⚠️ Weekly First-Time Loan Alert</h1>

    <div class="card" style="border: 2px solid #dc3545;">
        <div class="card-body">
            <p>Dear {{ $partner->Institution_Name }} Team,</p>

            <div class="alert alert-danger" role="alert">
                <strong>Action Required:</strong> Your first-time loan percentage is below the acceptable threshold.
            </div>

            <p>We have analyzed your agent loan data for the past week and found concerning results:</p>

            <div class="card bg-light mb-4">
                <div class="card-header">
                    <strong>Analysis Period:</strong>
                    @if($analysis['analysis_period']['start'] && $analysis['analysis_period']['end'])
                        {{ \Carbon\Carbon::parse($analysis['analysis_period']['start'])->format('F j, Y') }} -
                        {{ \Carbon\Carbon::parse($analysis['analysis_period']['end'])->format('F j, Y') }}
                    @else
                        Past week
                    @endif
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-2">
                            <div class="text-center">
                                <h3 class="text-primary">{{ $analysis['total_agents'] }}</h3>
                                <p class="mb-0"><strong>Total Agents</strong></p>
                                <small class="text-muted">From CSV file</small>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="text-center">
                                <h3 class="text-warning">{{ $analysis['agents_not_in_database'] }}</h3>
                                <p class="mb-0"><strong>Not in Database</strong></p>
                                <small class="text-muted">Need onboarding</small>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="text-center">
                                <h3 class="text-info">{{ $analysis['agents_in_database_without_loans'] }}</h3>
                                <p class="mb-0"><strong>In DB, No Loans</strong></p>
                                <small class="text-muted">Onboarded agents</small>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="text-center">
                                <h3 class="text-secondary">{{ $analysis['agents_without_loans'] }}</h3>
                                <p class="mb-0"><strong>Total Without Loans</strong></p>
                                <small class="text-muted">Combined total</small>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="text-center">
                                <h3 class="text-success">{{ $analysis['agents_with_first_time_loans'] }}</h3>
                                <p class="mb-0"><strong>First-Time Loans</strong></p>
                                <small class="text-muted">This week</small>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="text-center">
                                <h3 class="{{ $analysis['first_time_loan_percentage'] < 30 ? 'text-danger' : 'text-warning' }}">
                                    {{ $analysis['first_time_loan_percentage'] }}%
                                </h3>
                                <p class="mb-0"><strong>First-Time Rate</strong></p>
                                <small class="text-muted">Target: ≥{{ $analysis['threshold'] ?? 30 }}%</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="alert alert-warning" role="alert">
                <strong>Issue:</strong> Your first-time loan rate of <strong>{{ $analysis['first_time_loan_percentage'] }}%</strong>
                is below the minimum threshold of <strong>{{ $analysis['threshold'] ?? 30 }}%</strong>.
            </div>

            <h4>What does this mean?</h4>
            <ul>
                <li><strong>Total Agents:</strong> All phone numbers in your agent CSV file</li>
                <li><strong>Not in Database:</strong> Agent phone numbers that don't exist as customers yet (need onboarding)</li>
                <li><strong>In DB, No Loans:</strong> Agents who are onboarded as customers but haven't received any approved loans</li>
                <li><strong>Total Without Loans:</strong> Combined count of agents not in database + agents in database without loans</li>
                <li><strong>First-Time Loans:</strong> Agents who got their first approved loan this week</li>
                <li><strong>Calculation:</strong> (First-Time Loans ÷ (First-Time Loans + Total Without Loans)) × 100</li>
            </ul>

            <h4>Recommended Actions:</h4>
            <ol>
                <li>Review your agent onboarding and loan application processes</li>
                <li>Identify barriers preventing agents from applying for loans</li>
                <li>Enhance agent education about loan products and benefits</li>
                <li>Consider targeted outreach to agents without loans</li>
                <li>Analyze successful agent profiles to replicate best practices</li>
                <li>Review loan approval criteria and processes for potential improvements</li>
            </ol>

            <div class="alert alert-info" role="alert">
                <strong>Note:</strong> This is an automated alert based on your agent CSV file. Low first-time loan rates may indicate
                missed opportunities for agent growth and revenue generation. Please take action to improve agent loan uptake.
            </div>

            <p>If you need assistance or have questions about this report, please contact our support team.</p>

            <p>Thank you for your attention to this important matter.</p>

            <hr>
            <h5>GnuGrid LMS Team</h5>
            <p><small>Partner ID: {{ $partner->Identification_Code }}</small></p>
        </div>
    </div>
</div>
@endsection
