<div class="form-group">
    <label class="col-sm-3 control-label" for="{{ $fieldName }}">{{ $label }}</label>
    <div class="col-sm-9">
        <div id="photo">
        </div>
    </div>
</div>

@section('script')
<script type="text/javascript">

    $(document).ready(function(){
        $("#photo").spartanMultiImagePicker({
            fieldName:        '{{ $fieldName }}',
            maxCount:         1,
            rowHeight:        '200px',
            groupClassName:   'col-md-4 col-sm-9 col-xs-6',
            maxFileSize:      '',
            allowedExt:       'jpeg|png|jpg|gif|svg',
            dropFileLabel : "Drop Here",
            @if ($image != null)
            placeholderImage: {
                image: '{{$image}}',
                width: '100%'
            },
            @endif
            onExtensionErr : function(index, file){
                console.log(index, file,  'extension err');
                alert('Please only input png or jpg type file')
            },
            onSizeErr : function(index, file){
                console.log(index, file,  'file size too big');
                alert('File size too big');
            }
        });
    });
</script>
@endsection
