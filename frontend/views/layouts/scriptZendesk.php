<?php
/**
 * Created by PhpStorm.
 * User: Igor
 * Date: 11/12/2015
 * Time: 12:07
 */
?>
<!-- Start of pecaagora Zendesk Widget script -->
<script>
    /*<![CDATA[*/
    window.zEmbed || function (e, t) {
        var n, o, d, i, s, a = [], r = document.createElement("iframe");
        window.zEmbed = function () {
            a.push(arguments)
        }, window.zE = window.zE || window.zEmbed, r.src = "javascript:false", r.title = "", r.role = "presentation", (r.frameElement || r).style.cssText = "display: none", d = document.getElementsByTagName("script"), d = d[d.length - 1], d.parentNode.insertBefore(r, d), i = r.contentWindow, s = i.document;
        try {
            o = s
        } catch (c) {
            n = document.domain, r.src = 'javascript:var d=document.open();d.domain="' + n + '";void(0);', o = s
        }
        o.open()._l = function () {
            var o = this.createElement("script");
            n && (this.domain = n), o.id = "js-iframe-async", o.src = e, this.t = +new Date, this.zendeskHost = t, this.zEQueue = a, this.body.appendChild(o)
        }, o.write('<body onload="document._l();">'), o.close()
    }("//assets.zendesk.com/embeddable_framework/main.js", "pecaagora.zendesk.com");
    /*]]>*/

    //  BLOQUEIO DA SELEÇÃO DE CONTEÚDO

    //    function bloquear(e) {
    //        return false
    //    }
    //    function desbloquear() {
    //        return true
    //    }
    //    document.onselectstart = new Function("return false");
    //    if (window.sidebar) {
    //        document.onmousedown = bloquear;
    //        document.onclick = desbloquear
    //    }

</script>
<!-- End of pecaagora Zendesk Widget script -->

