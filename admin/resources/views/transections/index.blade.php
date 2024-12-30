@extends('layouts.app')

@section('content')

<div class="main-panel">
    <div class="content-wrapper">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-lg-6">
                        <h4 class="card-title">Transection Information</h4>
                    </div>
                    <div class="col-lg-6">
                        <div class="d-flex justify-content-end" style="min-width: 200px;">
                            <form class="" id="sort_categories" action="" method="GET">
                                <div class="form-outline">
                                    <input type="text" class="form-control" id="search" name="search"@isset($sort_search) value="{{ $sort_search }}" @endisset placeholder=" Type name & Enter">
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <div class="table-responsive">
                            <table id="order-listng" class="table">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Name</th>
                                        <th>Package Name</th>
                                        <th>Total Calls</th>
                                        <th>Total Message</th>
                                        <th>Transection Amount</th>
                                        <th>Payment Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($transections as $key=>$transection)
                                        <tr>
                                            <td>{{ ($key+1) + ($transections->currentPage() - 1)*$transections->perPage() }}</td>
                                            <td>{{ $transection->user->name ?? '-' }}</td>
                                            <td>{{ $transection->package_name ?? '-' }}</td>
                                            <td>{{ $transection->package_calls ?? '-' }}</td>
                                            <td>{{ $transection->package_message ?? '-' }}</td>
                                            <td>{{ FinalPrice($transection->transection_amount) }}</td>
                                            @if($transection->payment_status == 'paid')
                                                <td> <button class="btn btn-success">{{ $transection->payment_status ?? '-' }}</button></td>
                                            @else 
                                                <td> <button class="btn btn-danger">{{ $transection->payment_status ?? '-' }}</button></td>
                                            @endif
                                            <td>
                                                <a href="javascript:void(0)"  class="btn btn-danger btn-sm"
                                                    onclick="ConformAlerts({{$transection->id}},'{{ route('transections.destroy') }}' )" >
                                                        <i class="fas fa-trash-alt"></i></a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            <div class="d-flex justify-content-end">
                                {{ $transections->links( "pagination::bootstrap-4") }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

