@extends('main')
@section('scriptheader')
  <!-- BEGIN: Vendor CSS-->
    <link rel="stylesheet" type="text/css" href="{{ url('/vuexy/app-assets/vendors/css/vendors.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{ url('/vuexy/app-assets/vendors/css/tables/datatable/dataTables.bootstrap5.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{ url('/vuexy/app-assets/vendors/css/tables/datatable/responsive.bootstrap5.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{ url('/vuexy/app-assets/vendors/css/tables/datatable/buttons.bootstrap5.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{ url('/vuexy/app-assets/vendors/css/tables/datatable/rowGroup.bootstrap5.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{ url('/vuexy/app-assets/vendors/css/pickers/flatpickr/flatpickr.min.css')}}">
    <!-- END: Vendor CSS-->
@endsection

@section('scriptfooter')
<!-- jQuery -->
<script src="{{ url('/assets/plugins/jquery/jquery.min.js') }}"></script>
<!-- Bootstrap 4 -->
<script src="{{ url('/assets/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<!-- DataTables  & Plugins -->
<script src="{{ url('/assets/plugins/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ url('/assets/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
<script src="{{ url('/assets/plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
<script src="{{ url('/assets/plugins/datatables-responsive/js/responsive.bootstrap4.min.js') }}"></script>
<script src="{{ url('/assets/plugins/datatables-buttons/js/dataTables.buttons.min.js') }}"></script>
<script src="{{ url('/assets/plugins/datatables-buttons/js/buttons.bootstrap4.min.js') }}"></script>
<script src="{{ url('/assets/plugins/jszip/jszip.min.js') }}"></script>
<script src="{{ url('/assets/plugins/pdfmake/pdfmake.min.js') }}"></script>
<script src="{{ url('/assets/plugins/pdfmake/vfs_fonts.js') }}"></script>
<script src="{{ url('/assets/plugins/datatables-buttons/js/buttons.html5.min.js') }}"></script>
<script src="{{ url('/assets/plugins/datatables-buttons/js/buttons.print.min.js') }}"></script>
<script src="{{ url('/assets/plugins/datatables-buttons/js/buttons.colVis.min.js') }}"></script>
<!-- AdminLTE App -->
<script>
  $(function () {
		$("#institution").DataTable();
    $("#example1").DataTable({
      "responsive": true, "lengthChange": false, "autoWidth": false,
      // "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
    }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
    $('#example2').DataTable({
      "paging": true,
      "lengthChange": false,
      "searching": false,
      "ordering": true,
      "info": true,
      "autoWidth": false,
      "responsive": true,
    });
  });
</script>
@endsection
@section('content')
<section id="complex-header-datatable">
        <div class="row">
            <div class="col-10">
                <div class="card">
                    <div class="card-header border-bottom">
                        <h4 class="card-title">{{$show->nama}}</h4>
                        <a href="/pendaftaran/add" class="btn btn-primary btn-sm"  data-bs-toggle="modal" data-bs-target="#tambah">Tambah Data</a>
                    </div>
                    <div class="card-body"><br>
                    <table id="institution" class="table table-bordered table-striped" width="100%">
							<thead>
							<tr>
								<th width="5%">No.</th>
                                <th width="30%">Nama Biaya</th>
                                <th width="15%">Nominal</th>
								<th width="20%">Aksi</th>
							</tr>
							</thead>
							<tbody>
							@foreach( $daftar as $each )
							<tr>
								<td>{{$loop->iteration}}</td>
                                <td>{{ $each->nama }}</td>
                                <td>Rp. {{ number_format($each->nominal, 0, ',', '.')}}</td>
                                <td class="demo-inline-spacing">
									<form action ="{{url('pendaftaran/deletebill', $each->id)}}" method="POST" onsubmit="return confirm('Apakah Anda yakin akan hapus data?')">
                                    @csrf
                                        <button class="btn btn-icon rounded-circle btn-xs btn-flat-danger" title="Hapus"><i data-feather='trash-2'></i></button>
                                    </form>	
								</td> 
							</tr>
							@endforeach
						
							</tbody>
						</table>
                    </div>
                </div>
            </div>
        </div>
</section>

<div class="modal fade text-start" id="tambah" tabindex="-1" aria-labelledby="myModalLabel33" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel33">Tambah Biaya Pendaftaran</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                    </ul>
                </div>
            @endif
			<form action="{{ url('pendaftaran/addbill') }}" method="POST" enctype="multipart/form-data">
   			@csrf
            <input type="hidden" name="id_daftar" value="{{$show->id}}" class="form-control" />
            <input type="hidden" name="id_periode" value="{{$show->id_periode}}" class="form-control" />
            <div class="modal-body">
                <label>Nama Biaya </label>
                <div class="mb-1">
                    <input type="text" name="nama" class="form-control" />
                </div>

                <label>Nominal</label>
                <div class="mb-1">
                    <input type="number" name="nominal" class="form-control" />
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary" data-bs-dismiss="modal">Simpan</button>
            </div>
            </form>
        </div>
    </div>
</div>
@endsection