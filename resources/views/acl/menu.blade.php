@extends('main')

@section('scriptheader')
  <link rel="stylesheet" type="text/css" href="{{ url('/vuexy/app-assets/vendors/css/tables/datatable/dataTables.bootstrap5.min.css')}}">
  <link rel="stylesheet" type="text/css" href="{{ url('/vuexy/app-assets/vendors/css/tables/datatable/responsive.bootstrap5.min.css')}}">
  <link rel="stylesheet" type="text/css" href="{{ url('/vuexy/app-assets/vendors/css/tables/datatable/buttons.bootstrap5.min.css')}}">
@endsection

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h4 class="card-title">ACL - Menu</h4>
        <button class="btn btn-primary btn-sm" id="btn-add-menu">Tambah Menu</button>
    </div>
    <div class="card-body">
        <table id="table-menu" class="table table-striped w-100">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama</th>
                    <th>Kode</th>
                    <th>Link</th>
                    <th>Parent</th>
                    <th>No Urut</th>
                    <th>Menu User</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>

<div class="modal fade" id="modal-menu" tabindex="-1" aria-labelledby="modal-menu-label" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal-menu-label">Tambah Menu</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row g-1">
                    <div class="col-md-6">
                        <label class="form-label">Nama</label>
                        <input type="text" id="menu_nama" class="form-control">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Kode</label>
                        <input type="text" id="menu_kode" class="form-control">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">No Urut</label>
                        <input type="number" id="menu_no" class="form-control" value="1">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Link</label>
                        <input type="text" id="menu_link" class="form-control" placeholder="/path">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Icon (Feather)</label>
                        <select id="menu_icon">
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Parent</label>
                        <select id="menu_parent">
                            <option value="0">(Root)</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label d-block">Menu User</label>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="menu_user_toggle">
                            <label class="form-check-label" for="menu_user_toggle">Ya</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label d-block">Status</label>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="menu_status_toggle" checked>
                            <label class="form-check-label" for="menu_status_toggle">Aktif</label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-primary" id="btn-save-menu">Simpan</button>
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
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        const parseFeather = (val = '') => {
            const match = String(val).match(/data-feather=['"]([^'"]+)/);
            return match ? match[1] : String(val || '').replace(/<[^>]+>/g, '') || '';
        };

        const icons_feather = [
                "activity",
                "airplay",
                "alert-circle",
                "alert-octagon",
                "alert-triangle",
                "align-center",
                "align-justify",
                "align-left",
                "align-right",
                "anchor",
                "aperture",
                "archive",
                "arrow-down-circle",
                "arrow-down-left",
                "arrow-down-right",
                "arrow-down",
                "arrow-left-circle",
                "arrow-left",
                "arrow-right-circle",
                "arrow-right",
                "arrow-up-circle",
                "arrow-up-left",
                "arrow-up-right",
                "arrow-up",
                "at-sign",
                "award",
                "bar-chart-2",
                "bar-chart",
                "battery-charging",
                "battery",
                "bell-off",
                "bell",
                "bluetooth",
                "bold",
                "book-open",
                "book",
                "bookmark",
                "box",
                "briefcase",
                "calendar",
                "camera-off",
                "camera",
                "cast",
                "check-circle",
                "check-square",
                "check",
                "chevron-down",
                "chevron-left",
                "chevron-right",
                "chevron-up",
                "chevrons-down",
                "chevrons-left",
                "chevrons-right",
                "chevrons-up",
                "chrome",
                "circle",
                "clipboard",
                "clock",
                "cloud-drizzle",
                "cloud-lightning",
                "cloud-off",
                "cloud-rain",
                "cloud-snow",
                "cloud",
                "code",
                "codepen",
                "codesandbox",
                "coffee",
                "columns",
                "command",
                "compass",
                "copy",
                "corner-down-left",
                "corner-down-right",
                "corner-left-down",
                "corner-left-up",
                "corner-right-down",
                "corner-right-up",
                "corner-up-left",
                "corner-up-right",
                "cpu",
                "credit-card",
                "crop",
                "crosshair",
                "database",
                "delete",
                "disc",
                "divide-circle",
                "divide-square",
                "divide",
                "dollar-sign",
                "download-cloud",
                "download",
                "dribbble",
                "droplet",
                "edit-2",
                "edit-3",
                "edit",
                "external-link",
                "eye-off",
                "eye",
                "facebook",
                "fast-forward",
                "feather",
                "figma",
                "file-minus",
                "file-plus",
                "file-text",
                "file",
                "film",
                "filter",
                "flag",
                "folder-minus",
                "folder-plus",
                "folder",
                "framer",
                "frown",
                "gift",
                "git-branch",
                "git-commit",
                "git-merge",
                "git-pull-request",
                "github",
                "gitlab",
                "globe",
                "grid",
                "hard-drive",
                "hash",
                "headphones",
                "heart",
                "help-circle",
                "hexagon",
                "home",
                "image",
                "inbox",
                "info",
                "instagram",
                "italic",
                "key",
                "layers",
                "layout",
                "life-buoy",
                "link-2",
                "link",
                "linkedin",
                "list",
                "loader",
                "lock",
                "log-in",
                "log-out",
                "mail",
                "map-pin",
                "map",
                "maximize-2",
                "maximize",
                "meh",
                "menu",
                "message-circle",
                "message-square",
                "mic-off",
                "mic",
                "minimize-2",
                "minimize",
                "minus-circle",
                "minus-square",
                "minus",
                "monitor",
                "moon",
                "more-horizontal",
                "more-vertical",
                "mouse-pointer",
                "move",
                "music",
                "navigation-2",
                "navigation",
                "octagon",
                "package",
                "paperclip",
                "pause-circle",
                "pause",
                "pen-tool",
                "percent",
                "phone-call",
                "phone-forwarded",
                "phone-incoming",
                "phone-missed",
                "phone-off",
                "phone-outgoing",
                "phone",
                "pie-chart",
                "play-circle",
                "play",
                "plus-circle",
                "plus-square",
                "plus",
                "pocket",
                "power",
                "printer",
                "radio",
                "refresh-ccw",
                "refresh-cw",
                "repeat",
                "rewind",
                "rotate-ccw",
                "rotate-cw",
                "rss",
                "save",
                "scissors",
                "search",
                "send",
                "server",
                "settings",
                "share-2",
                "share",
                "shield-off",
                "shield",
                "shopping-bag",
                "shopping-cart",
                "shuffle",
                "sidebar",
                "skip-back",
                "skip-forward",
                "slack",
                "slash",
                "sliders",
                "smartphone",
                "smile",
                "speaker",
                "square",
                "star",
                "stop-circle",
                "sun",
                "sunrise",
                "sunset",
                "table",
                "tablet",
                "tag",
                "target",
                "terminal",
                "thermometer",
                "thumbs-down",
                "thumbs-up",
                "toggle-left",
                "toggle-right",
                "tool",
                "trash-2",
                "trash",
                "trello",
                "trending-down",
                "trending-up",
                "triangle",
                "truck",
                "tv",
                "twitch",
                "twitter",
                "type",
                "umbrella",
                "underline",
                "unlock",
                "upload-cloud",
                "upload",
                "user-check",
                "user-minus",
                "user-plus",
                "user-x",
                "user",
                "users",
                "video-off",
                "video",
                "voicemail",
                "volume-1",
                "volume-2",
                "volume-x",
                "volume",
                "watch",
                "wifi-off",
                "wifi",
                "wind",
                "x-circle",
                "x-octagon",
                "x-square",
                "x",
                "youtube",
                "zap-off",
                "zap",
                "zoom-in",
                "zoom-out"
        ]

        const iconOptions = icons_feather.map((name) => ({
            value: `<i data-feather="${name}"></i>`,
            text: name,
            feather: name,
        }));

        const tsIcons = new TomSelect('#menu_icon', {
            placeholder: 'Pilih Icons',
            allowEmptyOption: true,
            options: iconOptions,
            render: {
                option: function (data, escape) {
                    const iconName = data.feather || parseFeather(data.value);
                    return `<div class="d-flex align-items-center gap-1"><i data-feather="${escape(iconName)}"></i><span>${escape(iconName)}</span></div>`;
                },
                item: function (data, escape) {
                    const iconName = data.feather || parseFeather(data.value);
                    return `<div class="d-flex align-items-center gap-1"><i data-feather="${escape(iconName)}"></i><span>${escape(iconName)}</span></div>`;
                }
            },
            onDropdownOpen: () => { if (window.feather) feather.replace(); },
            onItemAdd: () => { if (window.feather) feather.replace(); },
        });
        tsIcons.clearOptions();
        iconOptions.forEach(opt => tsIcons.addOption(opt));

        const tsParent = new TomSelect('#menu_parent', {
            allowEmptyOption: true,
            placeholder: '(Root)',
            maxItems: 1,
            render: {
                option: (data, escape) => `<div>${data.prefix || ''}${escape(data.text)}</div>`,
                item: (data, escape) => `<div>${data.prefix || ''}${escape(data.text)}</div>`
            }
        });

        const table = $('#table-menu').DataTable({
            processing: true,
            serverSide: true,
            ajax: '{{ route('acl.menu.data') }}',
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'nama', name: 'nama' },
                { data: 'kode', name: 'kode' },
                { data: 'link', name: 'link' },
                { data: 'parent', name: 'parent' },
                { data: 'no', name: 'no' },
                { data: 'menu_user', name: 'menu_user' },
                { data: 'status', name: 'status' },
                { data: 'aksi', name: 'aksi', orderable: false, searchable: false }
            ]
        });

        const resetForm = () => {
            $('#modal-menu-label').text('Tambah Menu');
            $('#menu_nama').val('');
            $('#menu_kode').val('');
            $('#menu_link').val('');
            const def = `<i data-feather="home"></i>`;
            if (!tsIcons.options[def]) {
                tsIcons.addOption({value: def, text: 'home', feather: 'home'});
            }
            tsIcons.setValue(def, true);
            tsParent.setValue('0', true);
            $('#menu_no').val(1);
            $('#menu_user_toggle').prop('checked', false);
            $('#menu_status_toggle').prop('checked', true);
            $('#btn-save-menu').data('mode', 'create').data('id', '');
        };

        $('#btn-add-menu').on('click', function () {
            resetForm();
            $('#modal-menu').modal('show');
        });

        $('#btn-save-menu').on('click', function () {
            const mode = $(this).data('mode') || 'create';
            const id = $(this).data('id');
            const payload = {
                nama: $('#menu_nama').val(),
                kode: $('#menu_kode').val(),
                link: $('#menu_link').val(),
                icon: $('#menu_icon').val(),
                id_parent: tsParent.getValue() || 0,
                no: $('#menu_no').val(),
                menu_user: $('#menu_user_toggle').is(':checked') ? 'Y' : 'N',
                status: $('#menu_status_toggle').is(':checked') ? 'A' : 'D',
            };

            const ajaxOpts = {
                url: mode === 'edit' ? '{{ url('data_master/menu') }}/' + id : '{{ route('acl.menu.store') }}',
                type: mode === 'edit' ? 'PUT' : 'POST',
                data: payload
            };

            $.ajax(ajaxOpts)
            .done(() => {
                Swal.fire('Sukses', mode === 'edit' ? 'Menu berhasil diperbarui' : 'Menu berhasil ditambahkan', 'success');
                $('#modal-menu').modal('hide');
                table.ajax.reload(null, false);
                loadParentOptions();
            })
            .fail(xhr => {
                Swal.fire('Gagal', xhr.responseJSON?.message || 'Terjadi kesalahan', 'error');
            });
        });

        $(document).on('click', '.btn-edit-menu', function () {
            const btn = $(this);
            $('#modal-menu-label').text('Edit Menu');
            $('#menu_nama').val(btn.data('nama'));
            $('#menu_kode').val(btn.data('kode'));
            $('#menu_link').val(btn.data('link'));
            const iconValRaw = btn.data('icon') || '';
            if (!tsIcons.options[iconValRaw]) {
                const iconName = parseFeather(iconValRaw) || 'home';
                tsIcons.addOption({value: iconValRaw, text: iconName, feather: iconName});
            }
            tsIcons.setValue(iconValRaw, true);
            const parentVal = String(btn.data('parent') ?? '0');
            if (!tsParent.options[parentVal]) {
                tsParent.addOption({value: parentVal, text: parentVal});
            }
            tsParent.setValue(parentVal, true);
            $('#menu_no').val(btn.data('no'));
            $('#menu_user_toggle').prop('checked', String(btn.data('menu_user')) === 'Y');
            $('#menu_status_toggle').prop('checked', String(btn.data('status')) === 'A');
            $('#btn-save-menu').data('mode', 'edit').data('id', btn.data('id'));
            $('#modal-menu').modal('show');
        });

        $(document).on('click', '.btn-delete-menu', function () {
            const id = $(this).data('id');
            Swal.fire({
                title: 'Hapus menu ini?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (!result.isConfirmed) return;
                $.ajax({
                    url: '{{ url('data_master/menu') }}/' + id,
                    type: 'DELETE',
                    success: function () {
                        Swal.fire('Terhapus', 'Menu berhasil dihapus', 'success');
                        table.ajax.reload(null, false);
                        loadParentOptions();
                    },
                    error: function (xhr) {
                        Swal.fire('Gagal', xhr.responseJSON?.message || 'Terjadi kesalahan', 'error');
                    }
                });
            });
        });

        // build hierarchical options and tree for parent selection
        const buildOptions = (items, parent = 0, depth = 0) => {
            const children = items.filter(i => parseInt(i.id_parent || 0) === parseInt(parent));
            let opts = [];
            children.forEach(c => {
                const prefix = '&nbsp;'.repeat(depth * 3) + (depth ? '└ ' : '');
                opts.push({
                    value: String(c.id),
                    text: `${c.nama} (${c.kode})`,
                    prefix,
                });
                opts = opts.concat(buildOptions(items, c.id, depth + 1));
            });
            return opts;
        };

        const loadParentOptions = () => {
            $.get('{{ route('acl.menu.data') }}', function (res) {
                const data = res.data || [];
                const opts = [{value: '0', text: '(Root)', prefix: ''}].concat(buildOptions(data, 0, 0));
                tsParent.clearOptions();
                opts.forEach(o => tsParent.addOption(o));
                if (!tsParent.getValue()) {
                    tsParent.setValue('0', true);
                }
            });
        };

        // initial load options
        loadParentOptions();
    });
</script>
@endsection
