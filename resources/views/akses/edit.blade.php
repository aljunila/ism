@extends('main')
@section('scriptheader')
    <link rel="stylesheet" type="text/css" href="{{ url('/app-assets/vendors/css/pickers/pickadate/pickadate.css')}}">
    <link rel="stylesheet" type="text/css" href="{{ url('/app-assets/vendors/css/pickers/flatpickr/flatpickr.min.css')}}">
     <!-- BEGIN: Page CSS-->
    <link rel="stylesheet" type="text/css" href="{{ url('/app-assets/css/core/menu/menu-types/vertical-menu.css')}}">
    <link rel="stylesheet" type="text/css" href="{{ url('/app-assets/css/plugins/forms/pickers/form-flat-pickr.css')}}">
    <link rel="stylesheet" type="text/css" href="{{ url('/app-assets/css/plugins/forms/pickers/form-pickadate.css')}}">
    <!-- END: Page CSS-->
@endsection

@section('scriptfooter')
    <!-- BEGIN: Page Vendor JS-->
    <script src="{{ url('/vuexy/app-assets/vendors/js/pickers/pickadate/picker.js')}}"></script>
    <script src="{{ url('/vuexy/app-assets/vendors/js/pickers/pickadate/picker.date.js')}}"></script>
    <script src="{{ url('/vuexy/app-assets/vendors/js/pickers/pickadate/picker.time.js')}}"></script>
    <script src="{{ url('/vuexy/app-assets/vendors/js/pickers/pickadate/legacy.js')}}"></script>
    <script src="{{ url('/vuexy/app-assets/vendors/js/pickers/flatpickr/flatpickr.min.js')}}"></script>
    <!-- END: Page Vendor JS-->
    <!-- BEGIN: Page JS-->
    <script src="{{ url('/vuexy/app-assets/js/scripts/forms/pickers/form-pickers.js')}}"></script>
    <!-- END: Page JS-->
<script>
   $(document).ready(function() {
    // === Parent → semua children ===
    $(document).on("change", ".check-parent", function() {
        let isChecked = this.checked;
        $(this).closest("li")
               .find("input[type=checkbox]")
               .prop("checked", isChecked)
               .prop("indeterminate", false);
    });

    // === Child → Parent rekursif ===
    $(document).on("change", "ul input[type=checkbox]", function() {
        let currentLi = $(this).closest("li").parent().closest("li");
        while (currentLi.length) {
            let parentCb = currentLi.children("input[type=checkbox]");
            let childCbs = currentLi.find("> ul > li > input[type=checkbox]");

            let allChecked = childCbs.length === childCbs.filter(":checked").length;
            let someChecked = childCbs.filter(":checked").length > 0;

            parentCb.prop("checked", allChecked);
            parentCb.prop("indeterminate", !allChecked && someChecked);

            currentLi = currentLi.parent().closest("li"); // naik ke atas
        }
    }); 

   $("#btnSave").click(function() {
        let checked = [];
        $("input[type=checkbox]:checked").each(function() {
            checked.push($(this).data("id"));
        });

        $.ajax({
            url: "{{ route('akses.save') }}",
            method: "POST",
            data: {
                id: $('#id').val(),
                _token: "{{ csrf_token() }}",
                checked: checked
            },
            success: function(res) {
                Swal.fire({
                    icon: "success",
                    title: "Berhasil",
                    text: "Akses user telah tersimpan",
                    timer: 2000,
                    showConfirmButton: false
                }).then(() => {
                    window.location.href = "{{ url('/akses') }}";
                });
            },
            error: function(err) {
                alert("Terjadi error saat simpan");
                console.error(err);
            }
        });
    });
});

$('#id_previllage').change(function(){
    let idp = $('#id_previllage').val()

    if(idp==1) {
        $('#akses').hide()
    } else {
        $('#akses').show()
    }
})
</script>

@endsection

@section('content')
<section id="basic-horizontal-layouts">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Akses User : {{$show->nama}}</h4>
                </div>
                <div class="card-body">
                    @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                        </ul>
                    </div>
                    @endif
                    <form id="menuForm" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="col-12">
                            <div class="mb-1 row">
                                <div class="col-sm-3">
                                    <label class="col-form-label" for="first-name">Level Akses</label>
                                </div>
                                <div class="col-sm-9">
                                    <select name="id_previllage" id="id_previllage"  class="form-control" required>
                                        @foreach($previllage as $p)
                                            @if($show->id_previllage==$p->id)
                                                    <option value="{{$p->id}}" selected>{{$p->nama}}</option>
                                                @else
                                                    <option value="{{$p->id}}">{{$p->nama}}</option>
                                                @endif
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <hr>
                            <div class="mb-1 row" id="akses">
                                <div class="col-sm-3">
                                    <label class="col-form-label" for="first-name">Akses Menu</label>
                                </div>
                                <div class="col-sm-9">
                                    <input type="hidden" name="id" id="id" value="{{$show->id}}">
                                    <ul class="tree list-unstyled">
                                        @foreach($tree as $node)
                                            @include('akses.tree-node', ['node' => $node])
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-9 offset-sm-3">
                            <button type="button" id="btnSave" class="btn btn-primary me-1">Simpan</button>
                            <button type="reset" class="btn btn-outline-secondary">Reset</button>
                        </div>
                    </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection