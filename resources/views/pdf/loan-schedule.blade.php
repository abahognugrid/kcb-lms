@extends('pdf.layouts')

@section('content')
    <div class="text-center">
        <h2 style="margin-bottom: 5px; margin-top: 0; font-size: 16px">{{ $partnerName }}</h2>
        <h4 style="margin-top: 0; margin-bottom: 4px">Loan Schedule</h4>
        <p style="margin-top: 0; font-size: 10px">Disbursed: {{ $filters['startDate']  }}</p>
    </div>

    @if (!$summaries->isEmpty())
        <table class="table table-bordered table-striped">
            <thead>
                <tr class="table-header">
                    <th class="text-start">Payment Due Date</th>
                    <th>Type</th>
                    <th class="text-end">Principal</th>
                    <th class="text-end">Interest</th>
                    <th class="text-end">Total Outstanding</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $totalPrincipal = 0;
                    $totalInterest = 0;
                    $totalOutstanding = 0;
                @endphp
                @foreach ($summaries as $group)
                    @foreach ($group as $summary)
                        <tr>
                            <td>
                                @if ($loop->first)
                                    {{ $summary->payment_month_name . ' ' . $summary->payment_year }}
                                @endif
                            </td>
                            <td>{{ $summary->type }}</td>
                            <td class="text-end">{{ number_format($summary->principal, 2) }}</td>
                            <td class="text-end">{{ number_format($summary->interest, 2) }}</td>
                            <td class="text-end">{{ number_format($summary->total_outstanding, 2) }}</td>
                        </tr>
                        @php
                            $totalPrincipal += $summary->principal;
                            $totalInterest += $summary->interest;
                            $totalOutstanding += $summary->total_outstanding;
                        @endphp
                    @endforeach
                    <tr>
                        <td></td>
                        <td class="fw-bold">Sub Total</td>
                        <td class="text-end fw-bold">{{ number_format($group->sum('principal'), 2) }}</td>
                        <td class="text-end fw-bold">{{ number_format($group->sum('interest'), 2) }}</td>
                        <td class="text-end fw-bold">{{ number_format($group->sum('total_outstanding'), 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td></td>
                    <td class="fw-bold">Total</td>
                    <td class="text-end fw-bold">{{ number_format($totalPrincipal, 2) }}</td>
                    <td class="text-end fw-bold">{{ number_format($totalInterest, 2) }}</td>
                    <td class="text-end fw-bold">{{ number_format($totalOutstanding, 2) }}</td>
                </tr>
            </tfoot>
        </table>
    @else
        <table class="table table-bordered table-striped">
            <thead>
                <tr class="table-header">
                    <th class="text-start">Payment Due Date</th>
                    <th>Type</th>
                    <th>#</th>
                    <th class="text-end">Principal</th>
                    <th class="text-end">Interest</th>
                    <th class="text-end">Total Payment</th>
                </tr>
            </thead>
            <tbody>
                @if ($schedules->isEmpty())
                    <tr>
                        <td colspan="6" class="text-center">No repayment schedule available.</td>
                    </tr>
                @else
                    @foreach ($schedules as $scheduleGroup)
                        @foreach ($scheduleGroup as $scheduleKey => $schedule)
                            <tr class="{{ $scheduleKey === 0 ? 'table-secondary' : '' }}">
                                <td>
                                    @if ($loop->first)
                                        {{ $schedule->payment_due_date->format('M d, Y') }}
                                    @endif
                                </td> <!-- Empty cell for grouping -->
                                <td>{{ $schedule->type }}</td>
                                <td class="text-end">{{ $schedule->installment_number }}</td>
                                <td class="text-end">{{ number_format($schedule->principal, 2) }}</td>
                                <td class="text-end">{{ number_format($schedule->interest, 2) }}</td>
                                <td class="text-end">{{ number_format($schedule->total_payment, 2) }}
                                </td>
                            </tr>
                        @endforeach
                    @endforeach
                @endif
                <tr>
                    <td colspan="3">
                        <strong>Total</strong>
                    </td>
                    <td class="text-end">
                        <strong>
                            {{ number_format($loan->schedule->sum('principal'), 2) }}
                        </strong>
                    </td>
                    <td class="text-end">
                        <strong>
                            {{ number_format($loan->schedule->sum('interest'), 2) }}
                        </strong>
                    </td>
                    <td class="text-end">
                        <strong>
                            {{ number_format($loan->schedule->sum('total_payment'), 2) }}
                        </strong>
                    </td>
                </tr>
            </tbody>
        </table>
    @endif
    <x-print-footer/>
@endsection
