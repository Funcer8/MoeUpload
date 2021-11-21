$(function() {
    var wpUploadDescription = $("#wpUploadDescription");
    if (wpUploadDescription.length > 0) {
        var toggleLink = $("<a/>");
        toggleLink.attr("href", "javascript:void(0);").text(mw.msg("moeupload-ForManagementOnly")).on("click", function() {
            wpUploadDescription.slideToggle();
            return false;
        });
        wpUploadDescription.hide();
        $('.mw-htmlform-field-HTMLTextAreaField .mw-input').prepend(toggleLink);
    }
    var errorP = $("<p/>").addClass("error");
    var uploadFormMsgRow = $("<tr/>").addClass("error uploadFormMsg");
    var uploadFormMsgCol = $("<td/>").attr("colspan", "2");
    uploadFormMsgRow.append(uploadFormMsgCol);
    /*url输入验证*/
    var wpSrcUrl = $('#wpSrcUrl');
    var upLoadFileUrlmsg = uploadFormMsgRow.clone().removeClass("uploadFormMsg").hide();
    wpSrcUrl.closest("tr").after(upLoadFileUrlmsg);
    wpSrcUrl.on("change blur", function() {
        var str = wpSrcUrl.val().trim();
		const supported = mw.config.get('wgFileExtensions').join('|');
        if (new RegExp('\.(?:' + supported + ')$', 'i').test(str)) {
            upLoadFileUrlmsg.show().find("td").text(mw.msg("moeupload-PageInsteadOfImg"));
        } else if ($("#wpUploadFileURL").val() === str) {
            upLoadFileUrlmsg.show().find("td").text(mw.msg("moeupload-SameAsSourceURL"));
        } else {
            upLoadFileUrlmsg.hide();
        }
    });
    /* XpAhH同学写的上传页面检测，未写注释禁止上传。管理员，巡查员不检测 */
    $.extend($.easing, {
        easeOutCirc: function(x, t, b, c, d) {
            return c * Math.sqrt(1 - (t = t / d - 1) * t) + b;
        }
    });
    var uploadForm = $("#mw-upload-form");
    uploadForm.on("submit", function() {
        uploadForm.find(".inputError").removeClass("inputError");
        uploadForm.find(".uploadFormMsg").remove();
        var wgUserGroups = mw.config.get("wgUserGroups");
        if (wgUserGroups.incldes("sysop") || wgUserGroups.incldes("patroller") || mw.util.getParamValue("disableUploadCheck") === "true") {
            return true;
        }
        var returnValue = true;
        var ifHaveFile = $($('input[name="wpSourceType"]:checked').val() == "url" ? "#wpUploadFileURL" : "#wpUploadFile").val() !== "";
        if (!ifHaveFile) {
            $("#mw-htmlform-source").parent().before(errorP.clone().addClass("uploadFormMsg").text(mw.msg("moeupload-NoFile")));
            returnValue = false;
        }
        //三选一
        var haveNoDetail = $("#wpCharName, #wpAuthor, #wpSrcUrl").filter(function() { return $(this).val().length === 0; });
        if (haveNoDetail.length === 3) {
            haveNoDetail.addClass("inputError");
            var noDetailRow = uploadFormMsgRow.clone();
            noDetailRow.find("td").text(mw.msg("moeupload-NoDetail"));
            haveNoDetail.first().closest("tr").before(noDetailRow);
            returnValue = false;
        }
        //符号
        var haveSymbol = $("#wpCharName, #wpAuthor, #wpSrcUrl").filter(function() { return /[。（）#$%^&*]/.test($(this).val()); });
        if (haveSymbol.length > 0) {
            haveSymbol.addClass("inputError");
            var haveSymbolRow = uploadFormMsgRow.clone();
            haveSymbolRow.find("td").text(mw.msg("moeupload-HaveSymbol"));
            haveSymbol.first().closest("tr").before(haveSymbolRow);
            returnValue = false;
        }
        /*url提交验证*/
        if (upLoadFileUrlmsg.is(":visible")) {
            returnValue = false;
        }
        $('html,body').animate({
            scrollTop: uploadForm.find(".uploadFormMsg").first().offset().top - 48
        }, 0);
        uploadForm.find(".uploadFormMsg");
        if (!returnValue) {
            var body = $("body");
            for (var i = 0; i < 20; i++) {
                body.append(
                    $("<div/>").text(mw.msg("moeupload-Moe")).css({
                        color: "#3FFC2E",
                        "font-size": 54 + 30 * Math.random(),
                        position: "fixed",
                        left: screen.availWidth * Math.random(),
                        top: screen.availHeight * Math.random()
                    }).animate(
                        //
                        {
                            opacity: 0.2,
                            "font-size": 54 + 80 * Math.random(),
                            left: screen.availWidth * Math.random(),
                            top: -100 - Math.random() * 300
                        },
                        2000,
                        "easeOutCirc",
                        function() {
                            this.remove();
                        }
                    )
                );
            }
        }
        return returnValue;
    });
});
