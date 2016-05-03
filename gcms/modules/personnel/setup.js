function inintPersonnel(id) {
	var patt = /order_[0-9]+/;
	forEach($E(id).getElementsByTagName('input'), function () {
		if (patt.test(this.id)) {
			$G(this).addEvent('change', function () {
				send(WEB_URL + 'modules/personnel/admin_action.php', 'id=' + this.id + '&value=' + this.value, doFormSubmit);
			});
		}
	});
}