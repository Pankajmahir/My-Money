@extends('layouts.app')

@section('content')
    <div class="main-panel">
        <div class="content-wrapper">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Daily Active Users Report</h4>

                    <form method="GET" action="{{ route('user-activity-report') }}">
                        <div class="row mb-3">
                            <div class="col">
                                <input type="date" name="start_date" class="form-control"
                                    value="{{ request()->start_date ?? \Carbon\Carbon::now()->subDays(7)->format('Y-m-d') }}">
                            </div>
                            <div class="col">
                                <input type="date" name="end_date" class="form-control"
                                    value="{{ request()->end_date ?? \Carbon\Carbon::today()->format('Y-m-d') }}">
                            </div>
                            <div class="col">
                                <button type="submit" class="btn btn-primary">Filter</button>
                                <a href="{{ route('user-activity-export', request()->query()) }}" class="btn btn-success">
                                    Export
                                </a>
                            </div>
                        </div>
                    </form>

                    <div class="table-responsive mt-4">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Date</th>
                                    <th>Active Count</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($activities as $key => $activity)
                                    <tr>
                                        <td>{{ $key + 1 + ($activities->currentPage() - 1) * $activities->perPage() }}</td>
                                        <td>{{ \Carbon\Carbon::parse($activity->activity_date)->format('d-m-Y') }}</td>
                                        <td>{{ $activity->activity_count  }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination Links -->
                    <div class="d-flex justify-content-end">
                        {{ $activities->links('pagination::bootstrap-4') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
