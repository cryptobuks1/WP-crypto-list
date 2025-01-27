! function ($) {
    "use strict";
    var numbersType = $.fn.dataTable.absoluteOrderNumber([{
            value: "0",
            position: "bottom"
        }]),
        currencyType = $.fn.dataTable.absoluteOrderNumber([{
            value: "0.000000",
            position: "bottom"
        }]);
    columnDefs: [{
        targets: 3,
        type: numbersType
    }, {
        targets: 4,
        type: numbersType
    }, {
        targets: 6,
        type: currencyType
    }], $.fn.celpDatatable = function () {
        var $celp_table = $(this),
            coin_symbol = $celp_table.data("coin-symbol"),
            coin_price = $celp_table.data("coin-price"),
            prevLbl = $celp_table.data("prev"),
            nextLbl = $celp_table.data("next"),
            showEntriesLbl = $celp_table.data("show-entries"),
            searchLbl = $celp_table.data("search"),
            zeroRecords = $celp_table.data("zero-records"),
            ShowingEntries = $celp_table.data("showing_entries"),
            FilterEntries = $celp_table.data("filter_entries"),
            loadingText = $celp_table.data("loading_records"),
            perPage = $celp_table.data("per-page"),
            defaultLogo = $celp_table.parents(".celp_container").data("default-logo"),
            columns = [];
        $celp_table.find("thead th").each(function (index) {
            var index = $(this).data("index"),
                thisTH = $(this),
                classes = $(this).data("classes"),
                fiatSymbol = "$";
            var type = '';
            if (index == "coin_supports" || index == "trading_pairs") {
                type = numbersType
            } else if (index == "name" || index == "official_website") {
                type = "html"
            } else if (index == "btc_price") {
                type = currencyType
            } else if (index == "id" || index == "volume_24h" || index == "alexa_rank" ) {
                type = 'num-fmt'
            } else {
                type = "string"
            }
            columns.push({
                data: index,
                name: index,
                type: type,
                render: function (data, type, row, meta) {
                    if (void 0 === meta.settings.json) return data;
                    if ("display" === type) switch (index) {
                        case "id":
                            return row.id;
                        case "coin_supports":
                            return parseInt(data) <= 0 ? '<div class="' + classes + '">N/A</div>' : '<div class="' + classes + '">' + data + "</div>";
                        case "trading_pairs":
                            return parseInt(data) <= 0 ? '<div class="' + classes + '">N/A</div>' : '<div class="' + classes + '">' + data + '</div>';
                        case "alexa_rank":
                            return parseInt(data) <= 0 ? '<div class="' + classes + '">N/A</div>' : '<div data-val="'+row.alexa_rank+'" class="' + classes + '"><span class="alexa_rank">' + data + '</span></div>';
                        case "name":
                            var singleUrl, url = thisTH.data("single-page-url") + "/" + row.ex_id + '/',
                                html;
                            return html = '<div class="' + classes + '"><a title ="' + data + '" href = "' + url + '"> <img id="' + data + '"  src="' + row.logo + '"  onerror="this.src=`'+defaultLogo+'`">' + data + " </a></div>";
                        case "btc_price":
                            if (void 0 !== data && null != data) {
                                if (parseFloat(data) <= 0) return '<div class="' + classes + '"><span class="cmc-formatted-price">N/A</span></div>';
                                var formatedVal = coin_price * data;
                                return formatedVal = numeral(formatedVal).format("0,0.00"), '<div data-val="' + row.btc_price + '" class="' + classes + '"><span class="cmc-formatted-price">' + coin_symbol + formatedVal + "</span></div>"
                            }
                            return '<div class="' + classes + '">?</div>';
                        case "volume_24h":
                            var formatedVal = coin_price * data,
                                btcVol = row.btc_volume,
                                formatedbtcVol = numeral(btcVol).format("0,0.00");
                            return formatedVal = numeral(formatedVal).format("0,0.00"), html = void 0 !== data && null != data ? '<div data-val="' + row.volume_24h + '" class="' + classes + '">' + coin_symbol + formatedVal.toUpperCase() + " <br/><span>" + formatedbtcVol + " BTC</span></div>" : '<div class="' + classes + '">?</span></div>';
                        case "official_website":
                            var html;
                            return html = '<div class="' + classes + '"> <a target="_blank"  rel="nofollow"  title ="' + row.ex_id + '" href="' + data + '"><i class="cmc_icon-link"></i></a> <a target="_blank"  rel="nofollow" title ="' + row.ex_id + '" href="' + row.twitter + '"><i class="cmc_icon-twitter"></i></i></a>  </div>'
                    }
                    return data
                }
            })
        }), $celp_table.DataTable({
            deferRender: !0,
            ajax: {
                url: ajax_object.ajax_url,
                type: "POST",
                dataType: "JSON",
                async: !0,
                data: function (d) {
                    d.action = "celp_get_ex_list"
                },
                error: function (xhr, error, thrown) {}
            },
            columns: columns,
            ordering: !0,
            searching: !0,
            pageLength: perPage,
            pagingType: "simple",
            processing: !0,
            language: {
                info: ShowingEntries,
                infoFiltered: "(" + FilterEntries + ")",
                loadingRecords: loadingText + "...",
                processing: "",
                loading: loadingText + "...",
                paginate: {
                    next: nextLbl,
                    previous: prevLbl
                },
                lengthMenu: showEntriesLbl,
                search: searchLbl,
                zeroRecords: zeroRecords
            },
            drawCallback: function (settings) {
                $celp_table.tableHeadFixer({
                    head: !1,
                    left: 2,
                    "z-index": 1
                })
            }
        })
    }, $(document).ready(function () {
        $("#celp_main_list").celpDatatable()
    })
}(jQuery)