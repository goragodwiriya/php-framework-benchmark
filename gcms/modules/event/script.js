// modules/event/script.js
function inintEventCalendar() {
	var patt = /^(prev|next)_([0-9]+)_([0-9]+)$/;
	var _calendarClick = function () {
		send(WEB_URL + 'modules/event/get.php', 'id=' + this.id, function (xhr) {
			var calendar = $G('event-calendar');
			calendar.setHTML(xhr.responseText);
			inintEventCalendar();
			if (loader) {
				loader.inint(calendar);
			}
		});
	};
	var w = ($G('event-calendar').getWidth() / 8) + 'px';
	forEach($E('event-calendar').getElementsByTagName('*'), function () {
		var tag = this.tagName.toLowerCase();
		if (tag == 'td') {
			this.style.width = w;
		} else if (patt.test(this.id)) {
			callClick(this, _calendarClick);
		} else if (tag == 'a' && this.className == 'cuttext') {
			this.style.width = w;
		}
	});
}