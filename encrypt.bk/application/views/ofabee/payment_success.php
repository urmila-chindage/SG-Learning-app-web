<?php include_once 'header.php'; ?>
<script>
    (function (global) { 

        if(typeof (global) === "undefined") {
            throw new Error("window is undefined");
        }

        var _hash = "!";
        var noBackPlease = function () {
            global.location.href += "#";

            // making sure we have the fruit available for juice (^__^)
            global.setTimeout(function () {
                global.location.href += "!";
            }, 50);
        };

        global.onhashchange = function () {
            if (global.location.hash !== _hash) {
                global.location.hash = _hash;
            }
        };

        global.onload = function () {            
            noBackPlease();

            // disables backspace on page except on input fields and textarea..
            document.body.onkeydown = function (e) {
                var elm = e.target.nodeName.toLowerCase();
                if (e.which === 8 && (elm !== 'input' && elm  !== 'textarea')) {
                    e.preventDefault();
                }
                // stopping event bubbling up the DOM tree..
                e.stopPropagation();
            };          
        }

        })(window);
</script>
<section>
    <div class="container-reduce-width">
        <div class="container container-res-chnger-frorm-page no-padding-xs">
            <div class="changed-container-for-forum explorer payment-acknowlede" style="min-height:350px;">
                <div class="row" style="padding-top:85px;">
                    <div class="col-md-12">
                        <h1 style="text-align:center; font-weight:bold; font-size:42px; color:green; margin-bottom:30px;">Thank You</h1>
                        <p style="text-align:center; font-weight:bold; font-size:20px;">Your Payment has been completed successfully!</p>
                        <p style="text-align:center; font-size:17px;" id="invoiceDownloadButton"></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
    // Opera 8.0+
    //var isOpera = (!!window.opr && !!opr.addons) || !!window.opera || navigator.userAgent.indexOf(' OPR/') >= 0;

    // Firefox 1.0+
    var isFirefox = typeof InstallTrigger !== 'undefined';

    // Safari 3.0+ "[object HTMLElementConstructor]" 
    //var isSafari = /constructor/i.test(window.HTMLElement) || (function (p) { return p.toString() === "[object SafariRemoteNotification]"; })(!window['safari'] || (typeof safari !== 'undefined' && safari.pushNotification));

    // Internet Explorer 6-11
    //var isIE = /*@cc_on!@*/false || !!document.documentMode;

    // Edge 20+
    //var isEdge = !isIE && !!window.StyleMedia;

    // Chrome 1 - 71
    //var isChrome = !!window.chrome && (!!window.chrome.webstore || !!window.chrome.runtime);

    // Blink engine detection
    //var isBlink = (isChrome || isOpera) && !!window.CSS;
    var order_id = "<?php echo $order_id;?>";
    var baseUrl  = "<?php echo site_url();?>";
    var url      = "<?php echo site_url('checkout/download_invoice/'.$order_id);?>";

    if(isFirefox){
        console.log('Isfirefox', isFirefox);
        $('#invoiceDownloadButton').html('<a target="_blank" href="'+url+'">Click here</a> to download the invoice');
        //document.getElementById('invoiceDownloadButton').removeAttribute("download");
        //document.getElementById('invoiceDownloadButton').removeAttribute("onclick");
    }else{
        console.log('Isfirefox', isFirefox);
        $('#invoiceDownloadButton').html(`<a target="_blank" href="${url}" download="invoice_${order_id}" onclick="location.href = '${baseUrl}/dashboard/courses/${order_id}'" >Click here</a> to download the invoice`);
    }

</script>
<?php include_once 'footer.php'; ?>