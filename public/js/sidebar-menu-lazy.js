$(document).ready(function () {
    const active = window.activeMenu || "";

    $.ajax({
        url: "/get-menu/parents",
        method: "GET",
        success: function (menus) {
            renderMenu(menus, $("#sidebarMenu"));
            feather.replace();
        }
    });

    function renderMenu(menus, $container) {
        menus.forEach(menu => {
            let $li = $("<li>").addClass("nav-item");
            let isActiveParent = active === menu.kode;

            let $a = $("<a>")
                .attr("href", menu.link || "#")
                .addClass("d-flex align-items-center")
                .html((menu.icon || "") + `<span class="menu-title text-truncate">${menu.nama}</span>`);

            $li.append($a);

            // kasih active class kalau parent
            if (isActiveParent) {
                $li.addClass("active");
            }

            if (menu.has_child) {
                let $toggleIcon = $("<i>")
                    .attr("data-feather", "chevron-right")
                    .addClass("ms-auto toggle-icon");

                $a.append($toggleIcon);

                let $ul = $("<ul>")
                    .addClass("menu-content")
                    .css("display", "none");

                $li.append($ul);

                // load children via ajax
                $a.on("click", function (e) {
                    e.preventDefault();

                    if (!$ul.data("loaded")) {
                        $.ajax({
                            url: "/get-menu/" + menu.id,
                            method: "GET",
                            success: function (children) {
                                renderMenu(children, $ul);
                                feather.replace();
                                $ul.data("loaded", true);

                                // cek kalau ada child aktif, auto open parent
                                if ($ul.find("li.active").length > 0) {
                                    $ul.show();
                                    $li.addClass("open active");
                                } else {
                                    $ul.slideDown(300);
                                    $li.toggleClass("open");
                                }
                            }
                        });
                    } else {
                        $ul.slideToggle(300);
                        $li.toggleClass("open");
                    }
                });

                // kalau parent punya child aktif → auto load child
                if (!isActiveParent && active.startsWith(menu.kode)) {
                    $a.trigger("click");
                }
            }

            // cek kalau current menu adalah child aktif
            if (active === menu.kode) {
                $li.addClass("active");
                $container.closest("li.nav-item").addClass("open active"); // open parent
                $container.show(); // biar submenunya keliatan
            }

            $container.append($li);
        });
    }
});
