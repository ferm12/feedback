(function(){
    // "use strict";

    // function whenAvailable(name, callback) {
    //     var interval = 10; // ms
    //     window.setTimeout(function() {
    //         if (window[name]) {
    //             console.log('ready');
    //             callback();
    //         } else {
    //             console.log('not ready');
    //             window.setTimeout(arguments.callee, interval);
    //         }
    //     }, interval);
    // }

    // function resizePreview(){
    //   var preview = $("#preview");
    //   preview.height($(window).height() - preview.offset().top - 2);
    // }
    // function getPath(dom_html) {
    //     var html = "<html><head><style></style></head><body>"+dom_html+"</body></html>";
    //     return location.origin+'/viewpdf?video=20150428196_chimpped&html='+html;
    // }
    // function loadIframe(){
    //                 var preview = $("#preview");
    // preview.attr("src", 'viewpdf');
    // preview.on('load', function(){
    //     $(this).contents().find("head").append($("<style>.page-break{left:-48px;position:absolute;height:22px;width:1160px;background:url(img/pdf-break.png)repeat-x 0px 0px;}#break-outer{height:22px;position:relative;}#preview-container,#inner-preview{border:none!important;}</style>"));
    // });
    // }

    //load the iframe only when the dompready=true
    // whenAvailable('domready', function(){
    //     var single_feedback = $('.single-feedback');
    //     var height= 115;
    //     var pages_size = 1100;
    //     var pages =1;
    //     single_feedback.each(function(){
    //         height += $(this).height();
    //         if (height > pages * pages_size ){
    //             $(this).after('<div id="break-outer"><div class="page-break"></div></div>');
    //             pages++;
    //         }
    //     });
    // });
    // var preview = document.getElementsByTagName('iframe')[0];
    // preview.src = 'viewpdf?video=20150428196_chimpped';
    // preview.onload = function(){
    //     var cssLink = document.createElement("link"); 
    //     cssLink.href = "css/pdfiframe.css"; 
    //     cssLink.rel = "stylesheet"; 
    //     cssLink.type = "text/css"; 
    //     preview.document.body.appendChild(cssLink);


    // };
    // var preview = $("#preview");
    // preview.attr("src", 'viewpdf?video=20150428196_chimpped');
    // var $head = preview.contents().find("head");

    // $head.append($("<link/>", { rel: "stylesheet", href: "css/pdfiframe.css", type: "text/css" }));

    // var frameListener;
    // $(window).load(function () {
    //     frameListener = setInterval(function(){frameLoaded()}, 50);
    // });

    // function frameLoaded() {
    //     var frame = $('iframe').get(0);
    //     frame.attr('src', 'viewpdf?video=20150428196_chimpped');
    //     

    //     if (frame != null) {
    //         var frmHead = $(frame).contents().find('head');
    //         if (frmHead != null) {
    //             clearInterval(frameListener); // stop the listener
    //             frmHead.append($("<link/>", { rel: "stylesheet", href: "css/pdfiframe.css", type: "text/css" })); 
    //         }
    //     }
    //     clearInterval(frameListener);
    // } 
    // var preview = $("#preview");
    // // preview.attr("src", 'viewpdf');
    // preview.attr("src", 'viewpdf?video=20150428196_chimpped');
  
//     preview.load( function() {
//
//         $(this).contents().find("head").appen($("<style>.page-break{left:-48px;position:absolute;height:22px;width:1160px;background:url(img/pdf-break.png)repeat-x 0px 0px;}#break-outer{height:22px;position:relative;}#preview-container,#inner-preview{border:none!important;}</style>"));
// });




    $('#print-pdf-button').on('click', function(e){
        e.preventDefault();
        $(this).hide();
        window.print();
        
        // $('#preview-container').hide();
        // var pane_hidden = setInterval(function(){
        //     if ( !($('#preview-container').is(":visible")) ){ 
        //         window.print();
        //         window.y=$('#preview-pane');
        //         clearInterval(pane_hidden);
        //     }
        // }, 100);
    });

    //mechanism to listen after the print pop up window, either the user has print something or cancel printing
    var beforePrint = function() {
        $('#print-pdf-button').hide();
        $('#collapse-all').hide();
        $('.collapse-parent').hide();
        $('.collapse-children').hide();
    };
    var afterPrint = function() {
        $('#print-pdf-button').show();
        $('#collapse-all').show();
        $('.collapse-parent').show();
        $('.collapse-children').show();
    };
    if (window.matchMedia) {
        var mediaQueryList = window.matchMedia('print');
        mediaQueryList.addListener(function(mql) {
            if (mql.matches) {
                beforePrint();
            } else {
                afterPrint();
            }
        });
    }
    window.onbeforeprint = beforePrint;
    window.onafterprint = afterPrint;

    // window.onload = window.print();

})();

