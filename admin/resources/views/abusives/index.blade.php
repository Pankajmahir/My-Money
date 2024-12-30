@extends('layouts.app')

@section('content')

<div class="main-panel">
    <div class="content-wrapper">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-lg-12 mb-4">
                        <a href="{{ route('abusives.create') }}" class="btn btn-info"> Add Abusive </a>
                    </div>
                    <div class="col-lg-6">
                        <h4 class="card-title">Abusive Information</h4>
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
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($abusives as $key=>$abusive)
                                        <tr>
                                            <td>{{ ($key+1) + ($abusives->currentPage() - 1)*$abusives->perPage() }}</td>
                                            <td>{{ $abusive->name ?? '-' }}</td>
                                            <td>
                                                <a href="{{ route('abusives.edit', $abusive->id) }}" class="btn btn-primary btn-sm"><i class="fas fa-edit"></i> </a>
                                                <a href="javascript:void(0)"  class="btn btn-danger btn-sm"
                                                    onclick="ConformAlerts({{$abusive->id}},'{{ route('abusives.destroy') }}' )" ><i class="fas fa-trash-alt"></i></a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            <div class="d-flex justify-content-end">
                                {{ $abusives->links( "pagination::bootstrap-4") }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

