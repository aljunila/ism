@extends('main')

@section('scriptheader')
  <link rel="stylesheet" type="text/css" href="{{ url('/vuexy/app-assets/vendors/css/tables/datatable/dataTables.bootstrap5.min.css')}}">
  <link rel="stylesheet" type="text/css" href="{{ url('/vuexy/app-assets/vendors/css/tables/datatable/responsive.bootstrap5.min.css')}}">
  <link rel="stylesheet" type="text/css" href="{{ url('/vuexy/app-assets/vendors/css/tables/datatable/buttons.bootstrap5.min.css')}}">
  <link rel="stylesheet" type="text/css" href="{{ url('/app-assets/vendors/css/forms/select/tom-select.css')}}">
@endsection

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h4 class="card-title">ACL - User Management</h4>
        <button class="btn btn-primary btn-sm" id="btn-add-user">Tambah User</button>
    </div>
    <div class="card-body">
        <table id="table-user" class="table table-striped w-100">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Username</th>
                    <th>Nama</th>
                    <th>Role</th>
                    <th>Perusahaan</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>

<div class="modal fade" id="modal-user" tabindex="-1" aria-labelledby="modal-user-label" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal-user-label">Tambah User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row g-1">
                    <div class="col-md-4">
                        <label class="form-label">Username</label>
                        <input type="text" id="user_username" class="form-control">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Password</label>
                        <input type="password" id="user_password" class="form-control">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Nama</label>
                        <input type="text" id="user_nama" class="form-control">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Role Utama</label>
                        <select id="user_role" multiple></select>
                    </div>
                    <div class="col-md-6" id="wrap-perusahaan">
                        <label class="form-label">Perusahaan Utama</label>
                        <select id="user_perusahaan"></select>
                    </div>
                    <div class="col-md-12" id="wrap-karyawan">
                        <label class="form-label">Karyawan</label>
                        <select id="user_karyawan"></select>
                    </div>
                </div>

                <hr>
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <h6 class="mb-0">Additional Role per Perusahaan</h6>
                    <button class="btn btn-sm btn-outline-primary" id="btn-add-additional">Tambah Perusahaan</button>
                </div>
                <div id="additional-rows" class="row g-1"></div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-primary" id="btn-save-user">Simpan</button>
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
<script src="{{ url('/app-assets/vendors/js/tom-select.min.js') }}"></script>
<script>
    $(function () {
        window.applyAuthHeaders = () => {
            const token = localStorage.getItem('access_token');
            const headers = {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            };
            if (token) {
                headers['Authorization'] = `Bearer ${token}`;
            }
            $.ajaxSetup({ headers });
            $.ajaxPrefilter(function (options, originalOptions, xhr) {
                const t = localStorage.getItem('access_token');
                if (t) {
                    xhr.setRequestHeader('Authorization', `Bearer ${t}`);
                }
            });
        };

        applyAuthHeaders();

        const tsRole = new TomSelect('#user_role', {placeholder: 'Pilih Role', allowEmptyOption: true, plugins: ['remove_button']});
        const tsPerusahaan = new TomSelect('#user_perusahaan', {placeholder: 'Pilih Perusahaan', allowEmptyOption: true, plugins: ['dropdown_input']});
        const tsKaryawan = new TomSelect('#user_karyawan', {placeholder: 'Pilih Karyawan', allowEmptyOption: true, plugins: ['dropdown_input']});

        let roleCache = [];
        let roleCacheNonSuper = [];
        let perusahaanCache = [];

        const table = $('#table-user').DataTable({
            processing: true,
            serverSide: true,
            ajax: '{{ route('acl.users.data') }}',
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'username', name: 'username' },
                { data: 'nama', name: 'nama' },
                { data: 'role', name: 'role' },
                { data: 'perusahaan', name: 'perusahaan' },
                { data: 'status_toggle', name: 'status_toggle', orderable: false, searchable: false },
                { data: 'aksi', name: 'aksi', orderable: false, searchable: false },
            ]
        });

        const loadRoles = () => {
            $.get('{{ route('acl.roles.all') }}', function (res) {
                roleCache = res || [];
                roleCacheNonSuper = roleCache.filter(r => (r.is_superadmin || 0) != 1);

                tsRole.clearOptions();
                roleCache.forEach(r => tsRole.addOption({value: r.id, text: `${r.kode} - ${r.nama}`, is_superadmin: r.is_superadmin || 0}));
                tsRole.refreshOptions(false);
            });
        };

        const loadPerusahaan = () => {
            $.get('{{ url('/api/perusahaan/all') }}', function (res) {
                perusahaanCache = res || [];
                tsPerusahaan.clearOptions();
                tsPerusahaan.addOption({value: '', text: 'Pilih Perusahaan'});
                perusahaanCache.forEach(p => tsPerusahaan.addOption({value: p.id, text: p.nama, kode: p.kode}));
                tsPerusahaan.refreshOptions(false);
            });
        };

        const loadKaryawan = (perusahaanId = '') => {
            $.get('{{ url('/api/karyawan/all') }}', { perusahaan_id: perusahaanId }, function (res) {
                tsKaryawan.clearOptions();
                tsKaryawan.addOption({value: '', text: 'Pilih Karyawan'});
                res.forEach(k => tsKaryawan.addOption({value: k.id, text: `${k.nama} (${k.nik})`}));
                tsKaryawan.refreshOptions(false);
            });
        };

        tsRole.on('change', function () {
            const selected = tsRole.getValue();
            const hasSuper = (selected || []).some(v => (tsRole.options[v] || {}).is_superadmin == 1);
            if (hasSuper) {
                $('#wrap-perusahaan, #wrap-karyawan').hide();
            } else {
                $('#wrap-perusahaan, #wrap-karyawan').show();
            }
        });

        tsPerusahaan.on('change', function (value) {
            loadKaryawan(value);
            refreshAdditionalCompanies();
        });

        const resetModal = () => {
            $('#user_username, #user_password, #user_nama').val('');
            tsRole.clear(true);
            tsPerusahaan.clear(true);
            tsKaryawan.clear(true);
            $('#additional-rows').empty();
        };

        $('#btn-add-user').on('click', function () {
            resetModal();
            $('#modal-user').modal('show');
        });

        const renderAdditionalRow = (idx) => {
            return `
                <div class="row g-1 additional-row" data-idx="${idx}">
                    <div class="col-md-6">
                        <label class="form-label">Perusahaan</label>
                        <select class="additional-perusahaan"></select>
                    </div>
                    <div class="col-md-5">
                        <label class="form-label">Role</label>
                        <select class="additional-role" multiple></select>
                    </div>
                    <div class="col-md-1 d-flex align-items-end">
                        <button class="btn btn-sm btn-outline-danger btn-remove-additional" type="button">&times;</button>
                    </div>
                </div>
            `;
        };

        $('#btn-add-additional').on('click', function () {
            const idx = Date.now();
            $('#additional-rows').append(renderAdditionalRow(idx));
            const $row = $(`.additional-row[data-idx='${idx}']`);

            // load options
            $.get('{{ url('/api/perusahaan/all') }}', function (res) {
                const sel = $row.parent().find('.additional-perusahaan').last();
                const ts = new TomSelect(sel[0], {placeholder: 'Pilih Perusahaan', allowEmptyOption: true, plugins: ['dropdown_input']});
                const current = ts.getValue();
                const selectedIds = getSelectedCompanyIds();
                ts.clearOptions();
                ts.addOption({value: '', text: 'Pilih Perusahaan'});
                perusahaanCache.forEach(p => {
                    if (selectedIds.includes(String(p.id)) && String(p.id) !== String(current)) return;
                    ts.addOption({value: p.id, text: p.nama});
                });
                ts.refreshOptions(false);
                ts.on('change', refreshAdditionalCompanies);
            });
            $.get('{{ route('acl.roles.all') }}', function (res) {
                const sel = $row.parent().find('.additional-role').last();
                const ts = new TomSelect(sel[0], {placeholder: 'Pilih Role', allowEmptyOption: true, plugins: ['remove_button']});
                ts.clearOptions();
                roleCacheNonSuper.forEach(r => ts.addOption({value: r.id, text: `${r.kode} - ${r.nama}`}));
                ts.refreshOptions(false);
            });
        });

        $(document).on('click', '.btn-remove-additional', function () {
            const $row = $(this).closest('.additional-row');
            $row.remove();
            refreshAdditionalCompanies();
        });

        $('#btn-save-user').on('click', function () {
            const payload = {
                username: $('#user_username').val(),
                password: $('#user_password').val(),
                nama: $('#user_nama').val(),
                role_ids: tsRole.getValue(),
                perusahaan_id: tsPerusahaan.getValue(),
                karyawan_id: tsKaryawan.getValue(),
                additional: []
            };

            $('#additional-rows').find('.additional-row').each(function () {
                const perusahaanId = $(this).find('.additional-perusahaan').val();
                const roleIds = $(this).find('.additional-role').val();
                if (perusahaanId && roleIds && roleIds.length) {
                    roleIds.forEach(rid => {
                        payload.additional.push({ perusahaan_id: perusahaanId, role_ids: [rid] });
                    });
                }
            });

            const mode = $('#btn-save-user').data('mode') || 'create';
            const id = $('#btn-save-user').data('id');
            const ajaxOpts = {
                url: mode === 'edit' ? '{{ url('acl/users') }}/' + id : '{{ route('acl.users.store') }}',
                type: mode === 'edit' ? 'PUT' : 'POST',
                data: payload
            };

            $.ajax(ajaxOpts)
                .done(() => {
                    Swal.fire('Sukses', mode === 'edit' ? 'User berhasil diperbarui' : 'User berhasil ditambahkan', 'success');
                    $('#modal-user').modal('hide');
                    table.ajax.reload(null, false);
                })
                .fail(xhr => {
                    Swal.fire('Gagal', xhr.responseJSON?.message || 'Terjadi kesalahan', 'error');
                });
        });

        $(document).on('click', '.btn-edit-user', function () {
            const id = $(this).data('id');
            resetModal();
            $('#modal-user-label').text('Edit User');
            $.get('{{ url('acl/users') }}/' + id, function (res) {
                const user = res.user;
                const roleIds = res.role_ids || [];
                const additional = res.additional || [];

                $('#user_username').val(user.username).prop('disabled', true);
                $('#user_nama').val(user.nama);
                tsRole.setValue(roleIds);

                const hasSuper = (roleIds || []).some(rid => (tsRole.options[rid] || {}).is_superadmin == 1);
                if (hasSuper) {
                    $('#wrap-perusahaan, #wrap-karyawan').hide();
                    tsPerusahaan.clear(true);
                    tsKaryawan.clear(true);
                } else {
                    tsPerusahaan.setValue(user.id_perusahaan || '');
                    tsKaryawan.setValue(user.id_karyawan || '');
                    $('#wrap-perusahaan, #wrap-karyawan').show();
                }

                additional.forEach(item => {
                    const idx = Date.now() + Math.random();
                    $('#additional-rows').append(renderAdditionalRow(idx));
                    const $row = $(`.additional-row[data-idx='${idx}']`);

                    const selP = $row.find('.additional-perusahaan');
                    const tsP = new TomSelect(selP[0], {placeholder: 'Pilih Perusahaan', allowEmptyOption: true, plugins: ['dropdown_input']});
                    tsP.clearOptions();
                    perusahaanCache.forEach(p => tsP.addOption({value: p.id, text: p.nama}));
                    tsP.setValue(item.perusahaan_id, true);

                    const selR = $row.find('.additional-role');
                    const tsR = new TomSelect(selR[0], {placeholder: 'Pilih Role', allowEmptyOption: true, plugins: ['remove_button']});
                    tsR.clearOptions();
                    roleCacheNonSuper.forEach(r => tsR.addOption({value: r.id, text: `${r.kode} - ${r.nama}`}));
                    tsR.setValue(item.role_ids, true);
                });

                $('#modal-user').modal('show');
                $('#btn-save-user').data('mode', 'edit').data('id', id);
            });
        });

        $(document).on('click', '.btn-delete-user', function () {
            const id = $(this).data('id');
            Swal.fire({
                title: 'Hapus user ini?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (!result.isConfirmed) return;
                $.ajax({
                    url: '{{ url('acl/users') }}/' + id,
                    type: 'DELETE',
                    success: function () {
                        Swal.fire('Terhapus', 'User dihapus (soft delete)', 'success');
                        table.ajax.reload(null, false);
                    },
                    error: function (xhr) {
                        Swal.fire('Gagal', xhr.responseJSON?.message || 'Terjadi kesalahan', 'error');
                    }
                });
            });
        });

        loadRoles();
        loadPerusahaan();
        loadKaryawan();

        $(document).on('change', '.toggle-user-status', function () {
            const id = $(this).data('id');
            const status = $(this).is(':checked');
            $.ajax({
                url: '{{ url('acl/users') }}/' + id + '/status',
                type: 'PUT',
                data: { status },
                success: function () {
                    Swal.fire('Sukses', 'Status user diperbarui', 'success');
                    table.ajax.reload(null, false);
                },
                error: function (xhr) {
                    Swal.fire('Gagal', xhr.responseJSON?.message || 'Terjadi kesalahan', 'error');
                }
            });
        });

        const getSelectedCompanyIds = () => {
            const ids = [];
            const mainId = tsPerusahaan.getValue();
            if (mainId) ids.push(String(mainId));
            $('#additional-rows').find('.additional-row').each(function () {
                const val = $(this).find('.additional-perusahaan').val();
                if (val) ids.push(String(val));
            });
            return ids;
        };

        const refreshAdditionalCompanies = () => {
            const selected = getSelectedCompanyIds();
            $('#additional-rows').find('.additional-row').each(function () {
                const sel = $(this).find('.additional-perusahaan');
                const ts = sel[0]?.tomselect;
                if (!ts) return;
                const current = ts.getValue();
                ts.clearOptions();
                ts.addOption({value: '', text: 'Pilih Perusahaan'});
                perusahaanCache.forEach(p => {
                    if (selected.includes(String(p.id)) && String(p.id) !== String(current)) return;
                    ts.addOption({value: p.id, text: p.nama});
                });
                ts.refreshOptions(false);
                if (current) {
                    ts.setValue(current, true);
                }
            });
        };
    });
</script>
@endsection
