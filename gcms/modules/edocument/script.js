// modules/edocument/script.js
var EDocument_patt = /^(icon\-)?(download|delete)\s([0-9]+)$/;
function inintEDocumentMain(id) {
	forEach($E(id).getElementsByTagName('a'), function () {
		if (EDocument_patt.test(this.className)) {
			callClick(this, doEDocumentClick);
		}
	});
}
var doEDocumentClick = function () {
	var hs = EDocument_patt.exec(this.className);
	if (hs[2] == 'delete' && !confirm(CONFIRM_DELETE)) {
		return false;
	}
	var req = new GAjax({
		asynchronous: false
	});
	req.send(WEB_URL + 'modules/edocument/action.php', 'id=' + this.className);
	var ds = req.responseText.toJSON();
	if (ds) {
		if (ds.confirm) {
			if (confirm(eval(ds.confirm))) {
				req.send(WEB_URL + 'modules/edocument/action.php', 'id=downloading ' + ds.id);
				ds = req.responseText.toJSON();
				if (ds.id) {
					this.href = decodeURIComponent(ds.href);
					return true;
				}
			}
		}
		if (ds.action == 'delete' && $E('edocument_' + ds.id)) {
			$G('edocument_' + ds.id).remove();
		}
		if (ds.error) {
			alert(eval(ds.error));
		}
	} else if (req.responseText != '') {
		alert(req.responseText);
	}
	return false;
};
