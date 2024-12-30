@extends('layouts.app')

@section('content')
    <div class="main-panel">
        <div class="content-wrapper">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Monthly Active Users Report for {{ $year }}</h4>

                    <form method="GET" action="{{ route('monthly-user-activity-report') }}">
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="year">Select Year:</label>
                                <select name="year" id="year" class="form-control">
                                    @for ($y = Carbon\Carbon::now()->year; $y >= Carbon\Carbon::now()->year - 10; $y--)
                                        <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                                    @endfor
                                </select>
                            </div>
                            <div class="col-md-4">
                                <button type="submit" class="btn btn-primary mt-4">Filter</button>
                                <a href="{{ route('user-activity-export-csv', ['year' => $year]) }}" class="btn btn-success mt-4">
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
                                    <th>Month</th>
                                    <th>Activity Count</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($activities as $key => $activity)
                                    <tr>
                                        <td>{{ $key + 1 }}</td>
                                        <td>{{ \Carbon\Carbon::createFromFormat('m', $activity->month)->format('F') }}</td>
                                        <td>{{ $activity->activity_count }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
