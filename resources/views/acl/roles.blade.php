@extends('main')

@section('content')
@section('scriptheader')
  <link rel="stylesheet" type="text/css" href="{{ url('/vuexy/app-assets/vendors/css/tables/datatable/dataTables.bootstrap5.min.css')}}">
  <link rel="stylesheet" type="text/css" href="{{ url('/vuexy/app-assets/vendors/css/tables/datatable/responsive.bootstrap5.min.css')}}">
  <link rel="stylesheet" type="text/css" href="{{ url('/vuexy/app-assets/vendors/css/tables/datatable/buttons.bootstrap5.min.css')}}">
@endsection

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h4 class="card-title">ACL - Role</h4>
        <button class="btn btn-primary btn-sm" id="btn-add-role">Tambah Role</button>
    </div>
    <div class="card-body">
        <table id="table-role" class="table table-striped w-100">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Kode</th>
                    <th>Nama</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>

<div class="modal fade" id="modal-role" tabindex="-1" aria-labelledby="modal-role-label" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal-role-label">Tambah Role</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-1">
                    <label class="form-label">Kode</label>
                    <input type="text" id="role_kode" class="form-control">
                </div>
                <div class="mb-1">
                    <label class="form-label">Nama</label>
                    <input type="text" id="role_nama" class="form-control">
                </div>
                <div class="mb-1">
                    <label class="form-label">Status</label>
                    <select id="role_status" class="form-select">
                        <option value="A">Aktif</option>
                        <option value="D">Nonaktif</option>
                    </select>
                </div>
                <div class="mb-1">
                    <label class="form-label">Jenis</label>
                    <select name="jenis_role" class="form-select" id="jenis_role">
                        <option value="">Pilih Jenis</option>
                        <option value="1">Admin Perusahaan</option>
                        <option value="2">Admin Kapal</option>
                        <option value="3">Karyawan</option>
                        <option value="4">Admin Pusat</option>
                    </select>
                </div>
                <div class="mb-1">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="role_superadmin">
                        <label class="form-check-label" for="role_superadmin">Superadmin (akses semua perusahaan, tanpa mapping menu)</label>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-primary" id="btn-save-role">Simpan</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scriptfooter')
<script src="{{ url('/assets/plugins/jquery/jquery.min.js') }}"></script>
<script src="{{ url('/assets/plugins/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ url('/assets/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
<script src="{{ url('/assets/plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
<script src="{{ url('/assets/plugins/datatables-responsive/js/responsive.bootstrap4.min.js') }}"></script>
<script src="{{ url('/assets/plugins/datatables-buttons/js/dataTables.buttons.min.js') }}"></script>
<script src="{{ url('/assets/plugins/datatables-buttons/js/buttons.bootstrap4.min.js') }}"></script>
<script>
    $(function () {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        const table = $('#table-role').DataTable({
            processing: true,
            serverSide: true,
            ajax: '{{ route('acl.roles.data') }}',
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'kode', name: 'kode' },
                { data: 'nama', name: 'nama' },
                { data: 'status', name: 'status' },
                { data: 'aksi', name: 'aksi', orderable: false, searchable: false }
            ]
        });

        const resetForm = () => {
            $('#modal-role-label').text('Tambah Role');
            $('#role_kode').val('');
            $('#role_nama').val('');
            $('#role_status').val('A');
            $('#jenis_role').val('');
            $('#role_superadmin').prop('checked', false);
            $('#btn-save-role').data('mode', 'create').data('id', '');
        };

        $('#btn-add-role').on('click', function () {
            resetForm();
            $('#modal-role').modal('show');
        });

        $('#btn-save-role').on('click', function () {
            const mode = $(this).data('mode') || 'create';
            const id = $(this).data('id');
            const payload = {
                kode: $('#role_kode').val(),
                nama: $('#role_nama').val(),
                status: $('#role_status').val(),
                jenis: $('#jenis_role').val(),
                is_superadmin: $('#role_superadmin').is(':checked') ? 1 : 0,
            };
            const ajaxOpts = {
                url: mode === 'edit' ? '{{ url('acl/roles') }}/' + id : '{{ route('acl.roles.store') }}',
                type: mode === 'edit' ? 'PUT' : 'POST',
                data: payload
            };
            $.ajax(ajaxOpts)
            .done(() => {
                Swal.fire('Sukses', mode === 'edit' ? 'Role diperbarui' : 'Role ditambahkan', 'success');
                $('#modal-role').modal('hide');
                table.ajax.reload(null, false);
                loadRoles();
            })
            .fail(xhr => Swal.fire('Gagal', xhr.responseJSON?.message || 'Terjadi kesalahan', 'error'));
        });

        $(document).on('click', '.btn-edit-role', function () {
            const btn = $(this);
            $('#modal-role-label').text('Edit Role');
            $('#role_kode').val(btn.data('kode'));
            $('#role_nama').val(btn.data('nama'));
            $('#role_status').val(btn.data('status'));
            $('#jenis_role').val(btn.data('jenis'));
            $('#role_superadmin').prop('checked', btn.data('superadmin') == 1);
            $('#btn-save-role').data('mode', 'edit').data('id', btn.data('id'));
            $('#modal-role').modal('show');
        });

        $(document).on('click', '.btn-delete-role', function () {
            const id = $(this).data('id');
            Swal.fire({
                title: 'Hapus role ini?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (!result.isConfirmed) return;
                $.ajax({
                    url: '{{ url('acl/roles') }}/' + id,
                    type: 'DELETE',
                    success: function () {
                        Swal.fire('Terhapus', 'Role berhasil dihapus', 'success');
                        table.ajax.reload(null, false);
                        loadRoles();
                    },
                    error: function (xhr) {
                        Swal.fire('Gagal', xhr.responseJSON?.message || 'Terjadi kesalahan', 'error');
                    }
                });
            });
        });

        const buildTree = (items, parent = 0) => {
            const children = items.filter(i => parseInt(i.id_parent || 0) === parseInt(parent));
            if (!children.length) return '';
            let html = '<ul class="list-unstyled ms-1">';
            children.forEach(c => {
                html += `<li class="mb-25">
                    <div class="form-check">
                        <input class="form-check-input menu-check" type="checkbox" value="${c.id}" data-parent="${c.id_parent}" id="menu_${c.id}">
                        <label class="form-check-label" for="menu_${c.id}">${c.nama} (${c.kode})</label>
                    </div>
                    ${buildTree(items, c.id)}
                </li>`;
            });
            html += '</ul>';
            return html;
        };

        const loadMenus = () => {
            $.get('{{ route('acl.menu.data') }}', function (res) {
                const data = res.data || [];
                const treeHtml = buildTree(data, 0);
                $('#menu-tree').html(treeHtml || '<p class="text-muted">Tidak ada menu</p>');
                attachSearch();
            });
        };

        const attachSearch = () => {
            const term = ($('#menu-search').val() || '').toLowerCase();
            $('.menu-check').each(function () {
                const label = $(this).next('label').text().toLowerCase();
                const match = label.includes(term);
                $(this).closest('li').toggle(match || term === '');
            });
        };

        $(document).on('input', '#menu-search', attachSearch);

        let currentMapRoleId = null;

        $(document).on('click', '.btn-map-menu', function () {
            const btn = $(this);
            currentMapRoleId = btn.data('id');
            $('#map-role-name').text(btn.data('nama'));
            $('.menu-check').prop('checked', false);
            loadMenus();
            $.get('{{ url('acl/roles/menu') }}/' + currentMapRoleId, function (res) {
                res.forEach(id => {
                    $(`#menu_${id}`).prop('checked', true);
                });
            });
            $('#modal-map').modal('show');
        });

        $('#btn-save-map').on('click', function () {
            if (!currentMapRoleId) {
                Swal.fire('Info', 'Pilih role terlebih dahulu', 'info');
                return;
            }
            const menuIds = $('.menu-check:checked').map(function(){ return $(this).val(); }).get();
            $.ajax({
                url: '{{ url('acl/roles/map-menu') }}',
                type: 'POST',
                data: { role_id: currentMapRoleId, menu_ids: menuIds },
                success: function () {
                    Swal.fire('Sukses', 'Mapping disimpan', 'success');
                    $('#modal-map').modal('hide');
                },
                error: function (xhr) {
                    Swal.fire('Gagal', xhr.responseJSON?.message || 'Terjadi kesalahan', 'error');
                }
            });
        });

        // check/uncheck cascades to children
        $(document).on('change', '.menu-check', function () {
            const checked = $(this).is(':checked');
            const parentId = $(this).val();
            // children
            $(this).closest('li').find('ul .menu-check').prop('checked', checked);
            // optional: bubble up when child checked
            if (checked) {
                let current = $(this).closest('ul').closest('li').find('> .form-check > .menu-check');
                while (current && current.length) {
                    current.prop('checked', true);
                    current = current.closest('ul').closest('li').find('> .form-check > .menu-check');
                }
            }
        });

        loadMenus();
    });
</script>
@endsection
<div class="modal fade" id="modal-map" tabindex="-1" aria-labelledby="modal-map-label" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal-map-label">Mapping Menu</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <div>
                        <span class="fw-bold" id="map-role-name"></span>
                    </div>
                    <input type="text" id="menu-search" class="form-control form-control-sm w-50" placeholder="Cari menu...">
                </div>
                <div id="menu-tree" class="border rounded p-1" style="max-height:360px; overflow:auto;"></div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-primary btn-sm" id="btn-save-map">Simpan Mapping</button>
            </div>
        </div>
    </div>
</div>
