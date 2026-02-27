@php
    $url_base = '';
    $url_segment_to_check = request()->segment(2);
@endphp
<div class="row">
    <div class="col-md-12">
        <div class="mb-4">
            <div class="d-flex flex-wrap gap-3 align-items-center">
                <a class="btn btn-sm btn-{{ $url_segment_to_check == '' ? '' : 'outline-' }}success waves-effect waves-light"
                    href="{{ url('/chart-of-accounts') }}" role="button">All Accounts</a>
                <a class="btn btn-sm btn-{{ $url_segment_to_check == 'assets' ? '' : 'outline-' }}success waves-effect waves-light"
                    href="{{ url('/chart-of-accounts/assets') }}" role="button">Assets</a>
                <a class="btn btn-sm btn-{{ $url_segment_to_check == 'liabilities' ? '' : 'outline-' }}success waves-effect waves-light"
                    href="{{ url('/chart-of-accounts/liabilities') }}"
                    role="button">Liabilities</a>
                <a class="btn btn-sm btn-{{ $url_segment_to_check == 'capital' ? '' : 'outline-' }}success waves-effect waves-light"
                    href="{{ url('/chart-of-accounts/capital') }}" role="button">Capital</a>
                <a class="btn btn-sm btn-{{ $url_segment_to_check == 'income' ? '' : 'outline-' }}success waves-effect waves-light"
                    href="{{ url('/chart-of-accounts/income') }}" role="button">Income</a>
                <a class="btn btn-sm btn-{{ $url_segment_to_check == 'expenses' ? '' : 'outline-' }}success waves-effect waves-light"
                    href="{{ url('/chart-of-accounts/expenses') }}" role="button">Expenses</a>
            </div>
        </div>
    </div>
</div>
