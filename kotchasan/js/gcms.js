/*
 * Javascript Libraly for GCMS (front-end)
 *
 * @filesource js/gcms.js
 * @link http://www.kotchasan.com/
 * @copyright 2016 Goragod.com
 * @license http://www.kotchasan.com/license/
 */
function inintSearch(form, input, module) {
	var doSubmit = function (e) {
		input = $G(input);
		var v = input.value.trim();
		if (v.length < 2) {
			input.invalid();
			alert(input.title);
			input.focus();
		} else {
			loaddoc(WEB_URL + 'index.php?module=' + $E(module).value + '&q=' + encodeURIComponent(v));
		}
		GEvent.stop(e);
		return false;
	};
	$G(form).addEvent('submit', doSubmit);
}
function loaddoc(url) {
	window.location = url;
}
function inintIndex(id) {
	$G(window).Ready(function () {
		if (Object.isNull(G_Lightbox)) {
			G_Lightbox = new GLightbox();
		} else {
			G_Lightbox.clear();
		}
		forEach($G(id || 'content').elems('img'), function (item, index) {
			if (!$G(item).hasClass('nozoom')) {
				new preload(item, function () {
					if (floatval(this.width) > floatval(item.width)) {
						G_Lightbox.add(item);
					}
				});
			}
		});
	});
}
function getCurrentURL() {
	var patt = /^(.*)=(.*)$/;
	var urls = new Object();
	var u = window.location.href;
	var us2 = u.split('#');
	u = us2.length == 2 ? us2[0] : u;
	var us1 = u.split('?');
	u = us1.length == 2 ? us1[0] : u;
	if (us1.length == 2) {
		forEach(us1[1].split('&'), function () {
			hs = patt.exec(this);
			if (hs) {
				urls[hs[1].toLowerCase()] = this;
			} else {
				urls[this] = this;
			}
		});
	}
	if (us2.length == 2) {
		forEach(us2[1].split('&'), function () {
			hs = patt.exec(this);
			if (hs) {
				if (MODULE_URL == '1' && hs[1] == 'module') {
					if (hs[2] == FIRST_MODULE) {
						u = WEB_URL + 'index.php';
					} else {
						u = WEB_URL + hs[2].replace('-', '/') + '.html';
					}
				} else {
					urls[hs[1].toLowerCase()] = this;
				}
			} else {
				urls[this] = this;
			}
		});
	}
	var us = new Array();
	for (var p in urls) {
		us.push(urls[p]);
	}
	if (us.length > 0) {
		u += '?' + us.join('&');
	}
	return u;
}
var doLoginSubmit = function (xhr) {
	var ds = xhr.responseText.toJSON();
	if (ds) {
		if (ds.alert && ds.alert != '') {
			alert(ds.alert);
		}
		if (ds.action) {
			if (ds.action == 2) {
				if (loader) {
					loader.back();
				} else {
					window.history.back();
				}
			} else if (ds.action == 1) {
				window.location = replaceURL('action', 'login');
			}
		}
		if (ds.content) {
			hideModal();
			var content = decodeURIComponent(ds.content);
			var login = $G('login-box');
			login.setHTML(content);
			content.evalScript();
			if (loader) {
				loader.inint(login);
			}
		}
		if (ds.input) {
			$G(ds.input).invalid().focus();
		}
	} else if (xhr.responseText != '') {
		alert(xhr.responseText);
	}
};
var doLogout = function (e) {
	setQueryURL('action', 'logout');
};
var doMember = function (e) {
	GEvent.stop(e);
	var action = $G(this).id;
	if (this.hasClass('register')) {
		action = 'register';
	} else if (this.hasClass('forgot')) {
		action = 'forgot';
	}
	showModal(WEB_URL + 'xhr.php', 'class=Index\\Member\\Controller&method=modal&action=' + action);
	return false;
};
function setQueryURL(key, value) {
	var a = new Array();
	var patt = new RegExp(key + '=.*');
	var ls = window.location.toString().split(/[\?\#]/);
	if (ls.length == 1) {
		window.location = ls[0] + '?' + key + '=' + value;
	} else {
		forEach(ls[1].split('&'), function (item) {
			if (!patt.test(item)) {
				a.push(item);
			}
		});
		var url = ls[0] + '?' + key + '=' + value + (a.length == 0 ? '' : '&' + a.join('&'));
		if (key == 'action' && value == 'logout') {
			window.location = url;
		} else {
			loaddoc(url);
		}
	}
}
var fbLogin = function () {
	FB.login(function (response) {
		FB.api('/me', function (response) {
			if (!response.error) {
				if (!response.email || response.email == '') {
					alert(trans('Invalid email'));
				} else {
					var q = new Array();
					for (var prop in response) {
						q.push(prop + '=' + response[prop]);
					}
					send(WEB_URL + 'xhr.php/index/model/fblogin/chklogin', 'u=' + encodeURIComponent(getCurrentURL()) + '&data=' + encodeURIComponent(q.join('&')), function (xhr) {
						var ds = xhr.responseText.toJSON();
						if (ds) {
							if (ds.alert) {
								alert(ds.alert);
							} else if (ds.isMember == 1) {
								if ($E('login_next')) {
									ds.location = $E('login_next').value;
								}
								if (ds.location) {
									if (ds.location == 'back') {
										if (loader) {
											loader.back();
										} else {
											window.history.go(-1);
										}
									} else {
										window.location = ds.location;
									}
								} else {
									window.location = replaceURL('action', 'login');
								}
							}
						} else if (xhr.responseText != '') {
							alert(xhr.responseText);
						}
					});
				}
			}
		});
	}, {scope: 'email,user_birthday'});
};
function inintFacebook(appId, lng) {
	window.fbAsyncInit = function () {
		FB.init({
			appId: appId,
			cookie: false,
			xfbml: true,
			version: 'v2.3'
		});
	};
	(function (d, s, id) {
		var js, fjs = d.getElementsByTagName(s)[0];
		if (d.getElementById(id)) {
			return;
		}
		js = d.createElement(s);
		js.id = id;
		js.src = "//connect.facebook.net/" + (lng == 'th' ? 'th_TH' : 'en_US') + "/sdk.js";
		fjs.parentNode.insertBefore(js, fjs);
	}(document, 'script', 'facebook-jssdk'));
}
function getWidgetNews(id, module, interval, callback) {
	var req = new GAjax();
	var _callback = function (xhr) {
		if (xhr.responseText !== '') {
			if ($E(id)) {
				var div = $G(id);
				div.setHTML(xhr.responseText);
				if (Object.isFunction(callback)) {
					callback.call(div);
				}
				if (loader) {
					loader.inint(div);
				}
			} else {
				req.abort();
			}
		}
	};
	var _getRequest = function () {
		return 'class=Widgets\\' + module + '\\Controllers\\Index&method=getWidgetNews&id=' + id;
	};
	if (interval == 0) {
		req.send(WEB_URL + 'xhr.php', _getRequest(), _callback);
	} else {
		req.autoupdate(WEB_URL + 'xhr.php', floatval(interval), _getRequest, _callback);
	}
}
var G_editor = null;
function inintEditor(frm, editor, action) {
	$G(window).Ready(function () {
		if ($E(editor)) {
			G_editor = editor;
			new GForm(frm, action).onsubmit(doFormSubmit);
		}
	});
}
function inintDocumentView(id, module) {
	$G(id).Ready(function () {
		var patt = /(quote|edit|delete|pin|lock|print|pdf)-([0-9]+)-([0-9]+)-([0-9]+)-(.*)$/;
		var viewAction = function (action) {
			var temp = this;
			send(WEB_URL + 'xhr.php/' + module + '/model/action/view', action, function (xhr) {
				var ds = xhr.responseText.toJSON();
				if (ds) {
					if (ds.action == 'quote') {
						var editor = $E(G_editor);
						if (editor && ds.detail !== '') {
							editor.value = editor.value + ds.detail;
							editor.focus();
						}
					} else if ((ds.action == 'pin' || ds.action == 'lock') && $E(module + '_' + ds.action)) {
						var a = $E(module + '_' + ds.action);
						a.className = a.className.replace(/(un)?(pin|lock)\s/, (ds.value == 0 ? 'un' : '') + '$2 ');
						a.title = ds.title;
					}
					if (ds.confirm) {
						if (confirm(ds.confirm)) {
							if (ds.action == 'deleting') {
								viewAction.call(temp, 'id=' + temp.className.replace('delete-', 'deleting-'));
							}
						}
					}
					if (ds.alert) {
						alert(ds.alert);
					}
					if (ds.location) {
						loaddoc(ds.location.replace(/&amp;/g, '&'));
					}
					if (ds.remove && $E(ds.remove)) {
						$G(ds.remove).remove();
					}
				} else if (xhr.responseText != '') {
					alert(xhr.responseText);
				}
			});
		};
		var viewExport = function (action) {
			var hs = patt.exec(action);
			window.open(WEB_URL + 'print.php?action=' + hs[1] + '&id=' + hs[2] + '&module=' + hs[5], 'print');
		};
		forEach($G(id).elems('a'), function (item, index) {
			if (patt.exec(item.className)) {
				callClick(item, function () {
					var hs = patt.exec(this.className);
					if (hs[1] == 'print' || hs[1] == 'pdf') {
						viewExport(this.className);
					} else {
						viewAction.call(this, 'id=' + this.className);
					}
				});
			}
		});
		inintIndex(id);
	});
}