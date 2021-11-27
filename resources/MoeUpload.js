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
	/* XpAhH同学写的上传页面检测，未写注释禁止上传 */
	var uploadForm = $("#mw-upload-form");
	uploadForm.on("submit", function() {
		uploadForm.find(".inputError").removeClass("inputError");
		uploadForm.find(".uploadFormMsg").remove();
		if (mw.config.get( 'wgMoeUploadSkipWarning' ) || mw.util.getParamValue("disableUploadCheck") === "true") {
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
		const regexp = new RegExp(mw.msg("moeupload-symbols-regexp"));
		var haveSymbol = $("#wpCharName, #wpAuthor, #wpSrcUrl").filter(function() {
			return regexp.test($(this).val());
		});
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
		return returnValue;
	});
});
