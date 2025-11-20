<script>
    @if($errors->any())
    @foreach ($errors->all() as $error)

    error_alert("{{$error}}",'خطا');
    @endforeach
    @endif

    @if(session()->has('success'))
    success_alert("{{session('success')}}");
    @endif


    $('.ui.modal').on('touchmove', function(event) {
        event.stopImmediatePropagation();
    })

    $('.dropdown:not(.not_dropdown)').dropdown();
    $('.checkbox:not(.not_checkbox)').checkbox();
</script>

