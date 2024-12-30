@extends('layouts.app')

@section('content')

<div class="main-panel">
    <div class="content-wrapper">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-lg-6">
                        <h4 class="card-title">Package Information</h4>
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
                            <table id="order-listin" class="table">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Name</th>
                                        <th>Calls</th>
                                        <th>Message</th>
                                        <th>Details</th>
                                        <th>Price</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($packages as $key=>$package)
                                        <tr>
                                            <td>{{ ($key+1) + ($packages->currentPage() - 1)*$packages->perPage() }}</td>
                                            <td>{{ $package->name ?? '-' }}</td>
                                            <td>{{ $package->package_calls ?? '-' }}</td>
                                            <td>{{ $package->package_message ?? '-' }}</td>
                                            <td>{{ $package->package_details ?? '-' }}</td>
                                            <td>{{ $package->price ?? '-' }}</td>
                                            <td>
                                                <label class="switch">
                                                    <input onchange="update_status_packages(this)" type="checkbox"  value="{{ $package->id }}" <?php if($package->status == 1) echo "checked";?>>
                                                    <span class="slider"></span>
                                                </label>
                                            </td>
                                            <td>
                                                <a href="{{ route('packages.edit', encrypt($package->id)) }}"  class="btn btn-info btn-sm"" >
                                                    <i class="fas fa-edit"></i></a>
                                                <!-- <a href="javascript:void(0)"  class="btn btn-danger btn-sm"
                                                    onclick="ConformAlerts({{$package->id}},'{{ route('packages.destroy') }}' )" >
                                                        <i class="fas fa-trash-alt"></i></a> -->
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            <div class="d-flex justify-content-end">
                                {{ $packages->links( "pagination::bootstrap-4") }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection


@section('script')
<script>
function update_status_packages(el){
    if(el.checked){
        var status = 1;
    }
    else{
        var status = 0;
    }
    $.post('{{ route('packages.status') }}', {_token:'{{ csrf_token() }}', id:el.value, status:status}, function(data){
        if(data == 1){
            toastr.success('Status Changed successfully!');
        }
        else{
            toastr.error('Something Wrong!');
        }
    });
}
</script>
@endsection