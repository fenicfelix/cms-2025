$(document).ready(function () {
    function openWindow(t) {
        if (window.innerWidth <= 320) {
            var e = document.createElement("a");
            e.setAttribute("href", t), e.setAttribute("target", "_blank");
            var n = document.createEvent("HTMLEvents");
            n.initEvent("click", !0, !0), e.dispatchEvent(n)
        } else {
            window.open(t, "newwindow", "width=500, height=340, top=" + (window.innerHeight - 340) / 2 + ", left=" + (window.innerWidth - 500) / 2)
        }
        return !1
    }
    $(document).on("click", ".social_share_wideweb", function (e) {
        e.preventDefault();
        return openWindow($(this).data("href"));
    });

    $(document).on("click", "#action-load-more", function (e) {
        e.preventDefault();
        $("#ajax-load-more").removeClass("hidden");
        var url = $(this).data("href");
        $.ajax({
            url: url,
            type: "GET",
            data: { page: page },
            success: function (obj) {
                $("#ajax-load-more").addClass("hidden");
                if (obj["status"] == "000") {
                    if (obj["data"] == "") $("#div-load-more").html("");
                    else {
                        page++;
                        $("#more-stories").append(obj["data"]);
                    }
                } else {
                    console.log("An error occurred.");
                }
            },
        });
    });

    $("#logout").on('click', function (e) {
        e.preventDefault();
        $("#logout-form").submit();
    });
});