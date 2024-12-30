@extends('layouts.app')

@section('content')

<div class="main-panel">
    <div class="content-wrapper">
        <div class="row">
            <div class="col-md-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Add Faq</h4>
                        <p class="card-description">
                            Basic Faq
                        </p>
                        <form class="forms-sample" action="{{ route('faqs.store')  }}" method="post">
                            @csrf
                            <div class="form-group">
                                <label for="exampleInputUsername1">Title</label>
                                <input type="text" class="form-control" name="title" placeholder="Enter Title">
                            </div>
                            <div class="form-group">
                                <label for="exampleInputEmail1">Faq</label>
                                <textarea id="editors" name="description" > </textarea>
                            </div>
                            <button type="submit" class="btn btn-primary me-2">Submit</button>
                            <button class="btn btn-light">Cancel</button>
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