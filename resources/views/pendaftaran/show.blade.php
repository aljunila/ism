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
            <div class="col-12">
                <div class="card">
                    <div class="card-header border-bottom">
                        <h4 class="card-title">Pendaftaran</h4>
                        <a href="/pendaftaran/add" class="btn btn-primary btn-sm">Tambah Data</a>
                    </div>
                    <div class="card-body"><br>
                    <table id="institution" class="table table-bordered table-striped" width="100%">
							<thead>
							<tr>
								<th width="5%">No.</th>
                <th width="30%">Nama</th>
                <th width="15%">Tgl Mulai</th>
                <th width="15%">Tgl Berakhir</th>
                <th width="15%">Sertai Biaya</th>
								<th width="20%">Aksi</th>
							</tr>
							</thead>
							<tbody>
							@foreach( $daftar as $each )
							<tr>
								<td>{{$loop->iteration}}</td>
                <td>{{ $each->nama}}<br>
                  <a href="http://127.0.0.1:8000/ppdb/{{$each->id}}" target="_blank">http://127.0.0.1:8000/ppdb/{{$each->id}}</a></td>
								<td>{{ $each->tgl_mulai }}</td>
                <td>{{ $each->tgl_akhir }}</td>
                <td>@if ($each->fee=='Y') 
                      Ya&nbsp;<a href="/pendaftaran/bill/{{$each->id}}" class="btn btn-outline-success round btn-sm" title="Buat Biaya Pendaftaran" target="_blank"><i data-feather="plus" class="me-25"></i>Buat Biaya</a>
                    @else
                      Tidak
                    @endif
                </td>
								<td class="demo-inline-spacing">
									<a href="/pendaftaran/edit/{{$each->id}}" class="btn btn-icon rounded-circle btn-xs btn-flat-warning" title="Edit"><i data-feather="edit"></i></a> 
                                    <form action ="{{url('pendaftaran/delete', $each->id)}}" method="POST" onsubmit="return confirm('Apakah Anda yakin akan hapus data?')">
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
@endsection