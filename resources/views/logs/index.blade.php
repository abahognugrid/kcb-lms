@extends('layouts/contentNavbarLayout')
@section('title', 'System Logs')
@section('content')
    <div class="card p-5">

        <h3 class="mb-4">System Logs</h3>

        <form method="GET" class="row mb-3">

            <div class="col-md-3">
                <select name="level" class="form-control">
                    <option value="">All Levels</option>
                    <option value="INFO">INFO</option>
                    <option value="ERROR">ERROR</option>
                    <option value="WARNING">WARNING</option>
                    <option value="DEBUG">DEBUG</option>
                </select>
            </div>

            <div class="col-md-6">
                <input type="text" name="search" class="form-control" placeholder="Search logs">
            </div>

            <div class="col-md-3">
                <button class="btn btn-primary">Filter</button>
            </div>

        </form>


        <table class="table table-bordered table-striped">

            <thead>
                <tr>
                    <th>ID</th>
                    <th>Level</th>
                    <th>Message</th>
                    <th>Date</th>
                </tr>
            </thead>

            <tbody>

                @foreach ($logs as $log)
                    <tr>
                        <td>{{ $log->id }}</td>

                        <td>

                            @if ($log->level == 'ERROR')
                                <span class="badge bg-danger">{{ $log->level }}</span>
                            @elseif($log->level == 'WARNING')
                                <span class="badge bg-warning">{{ $log->level }}</span>
                            @elseif($log->level == 'INFO')
                                <span class="badge bg-info">{{ $log->level }}</span>
                            @else
                                <span class="badge bg-secondary">{{ $log->level }}</span>
                            @endif

                        </td>

                        <td style="max-width:500px; word-wrap:break-word;">
                            {{ $log->message }}
                        </td>

                        <td>{{ $log->created_at }}</td>

                    </tr>
                @endforeach

            </tbody>

        </table>

        {{ $logs->links() }}

    </div>
@endsection
