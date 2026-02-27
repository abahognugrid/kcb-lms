@extends('layouts/blankLayout')

@section('title', 'Weekly Repayment Timeliness Report')

@section('content')
<div class="container">
    <h1 class="mb-4" style="color: #dc3545;">⚠️ Weekly Repayment Timeliness Alert</h1>

    <div class="card" style="border: 2px solid #dc3545;">
        <div class="card-body">
            <p>Dear {{ $partner->Institution_Name }} Team,</p>

            <div class="alert alert-danger" role="alert">
                <strong>Action Required:</strong> Your repayment timeliness rate is below the acceptable threshold.
            </div>

            <p>We have analyzed your loan repayment data for the recent period and found concerning results:</p>

            <div class="card bg-light mb-4">
                <div class="card-header">
                    <strong>Analysis Period:</strong>
                    @if($analysis['analysis_period']['start'] && $analysis['analysis_period']['end'])
                        {{ \Carbon\Carbon::parse($analysis['analysis_period']['start'])->format('F j, Y') }} -
                        {{ \Carbon\Carbon::parse($analysis['analysis_period']['end'])->format('F j, Y') }}
                    @else
                        Recent repayment period
                    @endif
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="text-center">
                                <h3 class="text-primary">{{ $analysis['total_payments'] }}</h3>
                                <p class="mb-0"><strong>Total Payments</strong></p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-center">
                                <h3 class="text-success">{{ $analysis['on_time_payments'] }}</h3>
                                <p class="mb-0"><strong>On-Time Payments</strong></p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-center">
                                <h3 class="{{ $analysis['on_time_percentage'] < 20 ? 'text-danger' : 'text-warning' }}">
                                    {{ $analysis['on_time_percentage'] }}%
                                </h3>
                                <p class="mb-0"><strong>On-Time Rate</strong></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="alert alert-warning" role="alert">
                <strong>Issue:</strong> Your on-time payment rate of <strong>{{ $analysis['on_time_percentage'] }}%</strong>
                is below the minimum threshold of <strong>20%</strong>.
            </div>

            <h4>What does this mean?</h4>
            <ul>
                <li><strong>On-time payments</strong> are those made exactly on the maturity date</li>
                <li><strong>Early payments</strong> are made before the maturity date</li>
                <li><strong>Late payments</strong> are made after the maturity date</li>
                <li>This analysis only considers payments made exactly on maturity dates</li>
            </ul>

            <h4>Recommended Actions:</h4>
            <ol>
                <li>Review your collection processes and identify bottlenecks</li>
                <li>Enhance customer communication about payment due dates</li>
                <li>Consider implementing automated payment reminders</li>
                <li>Analyze customer payment patterns to identify at-risk accounts</li>
                <li>Review and optimize your repayment collection strategies</li>
            </ol>

            <div class="alert alert-info" role="alert">
                <strong>Note:</strong> This is an automated alert. Consistent low on-time payment rates may affect your risk assessment and partnership terms.
                Please take immediate action to improve your repayment timeliness.
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
