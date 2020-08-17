var siteTracker = {

	lastTrackParam: null,
	yaMetrikaId: null,
	yaMetrikaClientId: null,

	init: function()
	{
		siteTracker.detectYaMetrikaId();

		if(typeof window['ym'] == 'undefined'){
			return;
		}
		// attache events
		$('[href*="/go/"]').click(function (e) {
			siteTracker.setClientId($(this), 'href');
			siteTracker.goal($(this).attr('href'));
		});

		$('[data-href*="/go/"]').click(function (e) {
			siteTracker.setClientId($(this), 'data-href');
			siteTracker.goal($(this).attr('data-href'));
		});
	},

	detectYaMetrikaId: function()
	{
		for (var i in window) {
			if (/^yaCounter\d?/.test(i)) {
				siteTracker.yaMetrikaId = i.substr(9);
				siteTracker.yaMetrikaClientId = window[i] instanceof Object ? window[i].getClientID() : null;
			}
		}
	},

	setClientId: function (element, attribute) {
		var href;
		//сохраняем исходный адрес ссылки
		if ($(element).attr('data-source-href') === undefined) {
			href = $(element).attr(attribute);
			$(element).attr('data-source-href', href);
		} else {
			href = $(element).attr('data-source-href');
		}
		var timestamp = Math.floor(new Date().getTime() / 1000);
		siteTracker.lastTrackParam = siteTracker.yaMetrikaClientId + timestamp;
		var yaClientIDParam = "track=" + siteTracker.lastTrackParam;
		if (href.indexOf("?") >= 0) {
			href += "&" + yaClientIDParam;
		} else {
			href += "?" + yaClientIDParam;
		}
		$(element).attr(attribute, href);
	},

	goal: function(href)
	{
		let seoName = siteTracker.getReflinkSeoName(href);
		let goalParams = {
			href: href,
			track: siteTracker.lastTrackParam
		};
		if(window['ym'] instanceof Object){
			ym(siteTracker.yaMetrikaId, 'reachGoal', seoName, goalParams);
		}
		if(window['gtag'] instanceof Object){
			gtag('event', 'click', {
				'event_category': 'reflink',
				'event_label': seoName,
				'value': 1
			});
		}
	},

	getReflinkSeoName: function(href)
	{
		let result = href.match('\/go\/(?<seo_name>[^\/]+)/?');
		return result === null ? null : result.groups.seo_name;
	}
};

window.onload = function() {
	try {
		siteTracker.init();
	} catch (err) {
		console.log(err);
	}
}
