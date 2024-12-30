@extends('layouts.app')

@section('content')

<div class="main-panel">
    <div class="content-wrapper">
        <div class="row">
            <div class="col-md-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Notification Settings</h4>
                        <p class="card-description">
                            Basic Notification Setting
                        </p>
                        <form class="forms-sample" action="{{ route('send-notifications.store')  }}" method="post">
                            @csrf
                            <div class="form-group">
                                <label for="exampleInputUsername1">Select User</label>
                                <select class="form-control" name="user">
                                    <option value="all_user"> All User </option>
                                    @foreach(App\Models\User::where('user_type', 'customer')->get() as $item)
                                        <option value="{{ $item->id }}"> {{  $item->phone }} - ( {{ $item->name ?? '-' }} )</option>
                                    @endforeach
                                </select>
                                @error('user')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="exampleInputEmail1">title </label>
                                <input type="text"  class="form-control"  rows="3" name="title"  placeholder="title">
                                @error('title')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="exampleInputEmail1">Message </label>
                                <textarea  class="form-control" style="height: 200px;" name="message" ></textarea>
                                @error('message')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <button type="submit" class="btn btn-primary me-2">Submit</button>
                        </form>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

@endsection

@section('script')
<script src="https://cdn.ckeditor.com/ckeditor5/26.0.0/classic/ckeditor.js" ></script>
<script src="https://unpkg.com/@ckeditor/ckeditor5-inspector@2.2.1/build/inspector.js" ></script>

<script>
function CustomizationPlugin( editor ) {

}   
    ClassicEditor
        .create( document.querySelector( '#editor' ), {
        extraPlugins: [ CustomizationPlugin ]
    } )
        .then( newEditor => {
        window.editor = newEditor;
        CKEditorInspector.attach( newEditor, {
            isCollapsed: true
        } );
    } )
        .catch( error => {
        console.error( error );
    });

    ClassicEditor
        .create( document.querySelector( '#editors' ), {
        extraPlugins: [ CustomizationPlugin ]
    } )
        .then( newEditor => {
        window.editor = newEditor;
        CKEditorInspector.attach( newEditor, {
            isCollapsed: true
        } );
    } )
        .catch( error => {
        console.error( error );
    });

</script>
@endsection