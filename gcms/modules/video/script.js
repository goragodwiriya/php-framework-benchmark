// modules/video/script.js
function inintVideoList(id) {
	var patt = /^youtube_([0-9]+)_([a-zA-Z0-9\-_]{11,11})$/;
	forEach($E(id).getElementsByTagName('a'), function () {
		if (patt.test(this.id)) {
			callClick(this, function () {
				showModal(WEB_URL + 'modules/video/action.php', 'id=' + this.id);
				return false;
			});
		}
	});
}