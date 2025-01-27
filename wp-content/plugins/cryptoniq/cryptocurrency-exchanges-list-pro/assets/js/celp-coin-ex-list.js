! function ($) {
    // "use strict";
     jQuery.fn.celpCoinExchanges = function () {
         var $celp_table = $(this),
             defaultLogo = $celp_table.parents(".currecies-pairs").data("ex-default-logo"),
             prevLbl = $celp_table.data("prev"),
             nextLbl = $celp_table.data("next"),
             perPage = $celp_table.data("per-page"),
             showEntriesLbl = $celp_table.data("show-entries"),
             searchLbl = $celp_table.data("search"),
             zeroRecords = $celp_table.data("zero-records"),
             coin_id = $celp_table.data("coin-id"),
             coin_symbol = $celp_table.data("coin-symbol"),
             columns = [];
         $celp_table.find("thead th").each(function (index) {
             var index = $(this).data("index"),
                 thisTH = $(this),
                 classes = $(this).data("classes"),
                 fiatSymbol = "$";
             columns.push({
                 data: index,
                 name: index,
                 render: function (data, type, row, meta) {
                     if (void 0 === meta.settings.json) return data;
                     if ("display" === type) switch (index) {
                         case "id":
                             return row.id;
                         case "pair":
                             return data;
                         case "exchange_name":
                             var singleUrl, url = thisTH.data("ex-single-slug") + "/" + row.ex_id+"/",
                                 html;
                             return html = '<div class="' + classes + '"> <a title ="' + data + '" href = "' + url + '" > <img  id="' + data + '"  src="' + row.logo + '"  onerror="this.src =' + defaultLogo + '">' + data + " </a></div>";
                         case "price":
                             if (void 0 !== data && null != data) {
                                 if (data < .5) var formatedVal = numeral(data).format("0,0.000000");
                                 else var formatedVal = numeral(data).format("0,0.00");
                                 return '<div data-val="' + row.price + '" class="' + classes + '"><span class="cmc-formatted-price">$' + formatedVal + "</span></div>"
                             }
                             return '<div class="' + classes + '">?</div>';
                         case "volume_24h":
                             var formatedVal = data;
                             return formatedVal = numeral(data).format("(0.00 a)"), void 0 !== data && null != data ? '<div data-val="' + row.usd_volume + '" class="' + classes + '">$' + formatedVal.toUpperCase() + "</div>" : '<div class="' + classes + '">?</span></div>';
                         case "updated":
                             var html;
                             return html = '<div class="' + classes + '">' + data + "</div>"
                     }
                     return data
                 }
             })
         }), $celp_table.DataTable({
             deferRender: !0,
             ajax: {
                 url: ajax_object.ajax_url,
                 type: "GET",
                 dataType: "JSON",
                 async: !0,
                 data: function (d) {
                     d.action = "celp_get_coin_exchanges", d.coin_id = coin_id
                 },
                 error: function (xhr, error, thrown) {
                   //  alert("Something wrong with Server")
                 }
             },
             columns: columns,
             pageLength: perPage,
             pagingType: "simple",
             ordering: !0,
             searching: !0,
             processing: !0,
             renderer: {
                 header: "bootstrap"
             },
             drawCallback: function (settings) {
                 $celp_table.tableHeadFixer({
                     foot: !1,
                     left: 2,
                     right: !1,
                     "z-index": 1
                 })
             },
             language: {
                 paginate: {
                     next: nextLbl,
                     previous: prevLbl
                 },
                 lengthMenu: showEntriesLbl,
                 search: searchLbl
             }
         })
     }, $(document).ready(function () {

        if( $('.cmc-tabsBtn').length > 0 ){
            if( $("#celp_coin_exchanges").is(':visible') != false ){
                $("#celp_coin_exchanges").celpCoinExchanges()
            }
        }else{
            $("#celp_coin_exchanges").celpCoinExchanges()
        }
        
     })
 }(jQuery);