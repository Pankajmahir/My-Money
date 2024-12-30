@extends('layouts.app')

@section('content')

<div class="main-panel">
    <div class="content-wrapper">
        <div class="row">
            <div class="col-md-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">General Settings</h4>
                        <p class="card-description">
                            Basic General Setting
                        </p>
                        <form class="forms-sample" action="{{ route('general_settings.store')  }}" method="post">
                            @csrf
                            <input type="hidden" name="id" value="{{ $settings->id }}" />
                            <div class="form-group">
                                <label for="exampleInputUsername1">Phone</label>
                                <input type="text" class="form-control" id="phone" name="phone"
                                    placeholder="phone" spellcheck="false" data-ms-editor="true" value="{{ $settings->phone }}">
                            </div>
                            <div class="form-group">
                                <label for="exampleInputEmail1">Whatsapp Number</label>
                                <input type="text" class="form-control" id="whatsapp_number" placeholder="whatsapp number" name="whatsapp_number" value="{{ $settings->whatsapp_number }}">
                            </div>

                            <div class="form-group">
                                <label for="exampleInputEmail1">Ref From Call</label>
                                <input type="number" class="form-control" id="ref_from_call" placeholder="whatsapp number" name="ref_from_call" value="{{ $settings->ref_from_call }}">
                            </div>

                            <div class="form-group">
                                <label for="exampleInputEmail1">Ref From SMS</label>
                                <input type="number" class="form-control" id="ref_from_sms" placeholder="whatsapp number" name="ref_from_sms" value="{{ $settings->ref_from_sms }}">
                            </div>

                            <div class="form-group">
                                <label for="exampleInputEmail1">Ref To Call</label>
                                <input type="number" class="form-control" id="ref_to_call" placeholder="whatsapp number" name="ref_to_call" value="{{ $settings->ref_to_call }}">
                            </div>

                            <div class="form-group">
                                <label for="exampleInputEmail1">Ref To SMS</label>
                                <input type="number" class="form-control" id="ref_to_sms" placeholder="whatsapp number" name="ref_to_sms" value="{{ $settings->ref_to_sms }}">
                            </div>
                            <div class="form-group">
                                <label for="exampleInputEmail1">Daily Call Limit per Customer</label>
                                <input type="number" class="form-control" id="daily_call_limit" placeholder="daily call limit" name="daily_call_limit" value="{{ $settings->daily_call_limit }}">
                            </div>
                            <div class="form-group">
                                <label for="exampleInputEmail1">Daily SMS Limit per Customer</label>
                                <input type="number" class="form-control" id="daily_sms_limit" placeholder="daily sms limit" name="daily_sms_limit" value="{{ $settings->daily_sms_limit }}">
                            </div>
                            <div class="form-group">
                                <label for="exampleInputEmail1">Daily Email Limit per Customer</label>
                                <input type="number" class="form-control" id="daily_email_limit" placeholder="daily email limit" name="daily_email_limit" value="{{ $settings->daily_email_limit }}">
                            </div>
                            <div class="form-group">
                                <label for="exampleInputEmail1">About</label>
                                <textarea id="editor" name="about" > {{ $settings->about }} </textarea>
                            </div>
                            <!-- <div class="form-group">
                                <label for="exampleInputEmail1">Faq</label>
                                <textarea id="editors" name="faq" > {{ $settings->faq }} </textarea>
                            </div> -->
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
