/* Helper.js v1.7.2 */

(function($) {
    $.fn.preLoadImages = function(cb) {
        var urls = [], promises = [], $imgs = $(this).find('img');
        $imgs.each(function(){
            var promise = $.Deferred();
            var img = new Image();
            img.onload = function(){
                promise.resolve(img.src);
            };
            img.src = $(this).attr('src');
            promises.push(promise);
        });
        $.when.apply(null, promises).done(cb);
    }
})(jQuery);

String.prototype.trim = String.prototype.trim || function() {
    return this.replace(/^\s+/, '').replace(/\s+$/, '');
};

String.prototype.replaceAll = function(search, replace) {
	var string = this;  
	if (typeof search === "object") {
		for (var i = 0; i < search.length; i++) {
			string = string.replace(new RegExp(search[i], "g"), replace[i]);
		}
	} else if (typeof search === "string") {
		string.replace(new RegExp(search, 'g'), replace);
	}
	return string;
};

jQuery.fn.swap = function(b) {
    b = jQuery(b)[0];
    var a = this[0],
        a2 = a.cloneNode(true),
        stack = this;

    stack[0] = a2;
    return this.pushStack( stack );
};

(function($) {
	$.fn.isInViewport = function(debug) {
		var debug = (debug) ? true : false;
		var $window = $(window);

		var _this = $(this);
		if(!_this && debug) {
			console.log('isOnScreen: element undefined.');
			return false;
		}


		var viewport = {
			top: ($window.scrollTop() || document.body.scrollTop || document.documentElement.scrollTop),
			left: ($window.scrollLeft() || document.body.scrollLeft || document.documentElement.scrollLeft)
		};
		viewport.right = viewport.left + ($window.width() || Math.max(document.body.scrollWidth, document.documentElement.scrollWidth, document.body.offsetWidth, document.documentElement.offsetWidth, document.body.clientWidth, document.documentElement.clientWidth));
		viewport.bottom = viewport.top + ($window.height() || Math.max(document.body.scrollHeight, document.documentElement.scrollHeight, document.body.offsetHeight, document.documentElement.offsetHeight, document.body.clientHeight, document.documentElement.clientHeight));

		if(debug)
			console.log('Viewport have bounds, top: '+viewport.top+', left: '+viewport.left+', right: '+viewport.right+', bottom: '+viewport.bottom);

		var bounds = {
			top: Math.round(_this.offset().top),
			left: Math.round(_this.offset().left),
		};
		bounds.right = Math.round(bounds.left + _this.outerWidth());
		bounds.bottom = Math.round(bounds.top + _this.outerHeight());

		if(debug)
			console.log('Element have bounds, top: '+bounds.top+', left: '+bounds.left+', right: '+bounds.right+', bottom: '+bounds.bottom);

		var inviewport = !(viewport.right < bounds.left || viewport.left > bounds.right || viewport.bottom < bounds.top || viewport.top > bounds.bottom);

		if(debug)
			console.log('Element in viewport: '+inviewport);

		return inviewport;
	}
})(jQuery);

jQuery.fn.viewport = function() {
    var e = window, a = 'inner';
    if (!('innerWidth' in window)) {
        a = 'client';
        e = document.documentElement || document.body;
    }
    return {
		width: e[a+'Width'],
		height: e[a+'Height']
	};
}

jQuery.fn.getStyle = function(elem, prop, debug) {
	var value = jQuery(elem).css(prop);
	var debug = (debug) ? true : false;

	if(debug)
		console.log(prop+': '+value);

    return this.css(prop, value);
};

jQuery.fn.isEmpty = function() {
    return !jQuery.trim(this.html());
};

jQuery.fn.id = function() {
    if(this.attr('class'))
        return "#"+jQuery.trim(this.attr('id'));
    else
        return null;
};

jQuery.fn.class = function() {
    if(this.attr('class'))
        return "."+this.attr('class').replace(/\s/g, ".");
    else
        return null;
};

jQuery.fn.size = function() {
    var length = jQuery(this).length;
	if (length)
    	return length;
	else
		return 0;
};

(function() {
	this.leadZero = function (number, period, debug) {
		var number = number || 0,
			period = period || 10,
			debug = debug || false,
			result;

		result = (parseInt(number) < parseInt(period) ? '0' : '') + number;

		if(debug)
			console.log('leadZero: '+result);

		return result;
	};
})();

(function($) {
	var defaults = {
		groups: 3,
		classname: ".item",
		find_elem: ".sub-item",
		min: 1
	};
	$.fn.autoGroup = function (custom, debug) {
		var debug = debug || false;
		var options = $.extend({}, defaults, custom);
		return this.each(function () {
			var elements = $(this).find(options.find_elem);
			var count = elements.length;

			if(debug)
				console.log('autoGroup count: '+count);

			if (count > 0) {
				var min = Math.ceil(count / options.groups);
				min < options.min && (min = options.min);

				var current = 0;
				var step = min;

				for (i = 0; i < options.groups; i++) {
					elements.slice(current, step).wrapAll(i + 1 == options.groups ? '<div class="' + options.classname + ' last" />' : '<div class="' + options.classname + '" />');
					current += min;
					step += min;
				}
			} else if(debug) {
				console.log('autoGroup: no have child elements for group.');
			}
		});
	};
})(jQuery);

jQuery.fn.nextOrFirst = function(selector){
    var next = this.next(selector);
    return (next.length) ? next : this.prevAll(selector).last();
};

jQuery.fn.prevOrLast = function(selector){
    var prev = this.prev(selector);
    return (prev.length) ? prev : this.nextAll(selector).last();
};

(function($) {
	$.fn.countUp = function(custom, debug) {
		var debug = debug || false;
		var options = $.extend({}, $.fn.countUp.defaults, custom);
		return this.each(function () {
			var _this = $(this);
			var loop = 0,
				current = 0,
				value = parseInt(_this.text()),
				loops = Math.ceil(options.time / options.interval),
				increment = value / loops;

			if(value > 0) {
				if(debug)
					console.log('countUp start of lops, count: '+loops);

				var intervalId = setInterval(function() {
					if (loop < loops) {
						current += increment;
						_this.text(Math.round(current));
					} else {
						clearInterval(intervalId);
						_this.text(value);

						if(debug)
							console.log('countUp end of lops, current: '+loop);

					}
					loop++;
				}, options.interval);
			} else if(debug) {
				console.log('countUp: element no have int value.');
			}
		});
	};
	$.fn.countUp.defaults = {
		interval: 100,
		time: 3000
	};
})(jQuery);

(function($) {
	$.fn.countDown = function(custom, debug) {
		var debug = debug || false;
		var options = $.extend({}, $.fn.countDown.defaults, custom);
		return this.each(function () {
			var _this = $(this);
			var loop = 0,
				current = 0,
				value = parseInt(_this.text()),
				loops = Math.ceil(options.time / options.interval),
				increment = value / loops;

			if(value > 0) {
				if(debug)
					console.log('countDown start of lops, count: '+loops);

				current = value;

				var intervalId = setInterval(function() {
					if (loop < loops) {
						current -= increment;
						_this.text(Math.round(current));
					} else {
						clearInterval(intervalId);
						_this.text(0);

						if(debug)
							console.log('countDown end of lops, current: '+loop);

					}
					loop++;
				}, options.interval);
			} else if(debug) {
				console.log('countDown: element no have int value.');
			}
		});
	};
	$.fn.countDown.defaults = {
		interval: 100,
		time: 3000
	};
})(jQuery);

(function() {
	this.uniqID = function (prefix, entropy, numeric, debug) {
		var prefix = prefix || '',
			entropy = entropy || false,
			numeric = numeric || false,
			debug = debug || false,
			result;

		this.seed = function (s, w) {
			s = parseInt(s, 10).toString(16);
			return w < s.length ? s.slice(s.length - w) :
			(w > s.length) ? new Array(1 + (w - s.length)).join('0') + s : s;
		};

		if(numeric)
			result = prefix + (String.fromCharCode(Math.floor(Math.random() * 11)) + Math.floor(Math.random() * 1000000)).trim();
		else
			result = prefix + (this.seed(parseInt((new Date().getTime() / 1000), 10), 8) + this.seed(Math.floor(Math.random() * 0x75bcd15) + 1, 5)).trim();

		if (entropy)
			result += (Math.random() * 10).toFixed(8).toString();

		if(debug)
			console.log('uniqID: '+result);

		return result;
	};
})();

(function($) {
	$.fn.horizontalScroll = function (amount, mixin) {
		mixin = mixin || false;
		amount = amount || 120;
		$(this).bind("DOMMouseScroll mousewheel", function (event) {
			var oEvent = event.originalEvent,
				direction = oEvent.detail ? oEvent.detail * -amount : oEvent.wheelDelta,
				position = $(this).scrollLeft();
			position += direction > 0 ? -amount : amount;
			$(this).scrollLeft(position);

			if(mixin && position == ($(this).scrollLeft() + amount))
				return;
			else if(mixin && position == -(amount))
				return;
			else
				event.preventDefault();
		});
	}
})(jQuery);

jQuery.fn.outerHtml = function() {
    return jQuery('<div />').append(jQuery(this).clone()).html();
};

(function($) {
	function elementText(el, separator) {
		var textContents = [];
		for (var chld = el.firstChild; chld; chld = chld.nextSibling) {

			if (chld.nodeType == 3)
				textContents.push(chld.nodeValue);

		}
		return textContents.join(separator);
	}
	$.fn.textNotChild = function(elementSeparator, nodeSeparator) {

		if (arguments.length < 2)
			nodeSeparator = "";

		if (arguments.length < 1)
			elementSeparator = "";

		return $.map(this, function(el) {
			return elementText(el, nodeSeparator);
		}).join(elementSeparator);
	}
})(jQuery);

jQuery.fn.readingTime = function(amount, debug) {
    var post = this[0],
		amount = jQuery(amount)[0] || 120,
		debug = (debug) ? true : false,
		estimated_time;

		var words = jQuery(post).text().toString().replace(/\r\n?|\n/g, ' ').replace(/ {2,}/g, ' ').replace(/^ /, '').replace(/ $/, '').split(' ').length;
		var minutes = Math.floor(words / amount);
		var seconds = Math.floor(words % amount / (amount / 60));

		if (1 <= minutes)
			estimated_time = minutes + ' minute' + (minutes == 1 ? '' : 's') + ', ' + seconds + ' second' + (seconds == 1 ? '' : 's');
		else
			estimated_time = minutes + ' second' + (minutes == 1 ? '' : 's');

		if(debug)
			console.log('readingTime() words: ' + words + ', reading by' + estimated_time);

		return estimated_time;
};

var declOfNum = (function() {
	// https://gist.github.com/realmyst/1262561
    var cases = [2, 0, 1, 1, 1, 2];
    var declOfNumSubFunction = function(titles, number) {
        number = Math.abs(number);
        return titles[ (number%100>4 && number%100<20)? 2 : cases[(number%10<5)?number%10:5] ];
    }
    return function(_titles) {
        if (arguments.length === 1) {
            return function(_number) {
                return declOfNumSubFunction(_titles, _number)
            }
        } else {
            return declOfNumSubFunction.apply(null, arguments)
        }
    }
})();

jQuery.fn.autoCurrying = function(number, titles, only_ends, debug) {
	var $elem = jQuery(this),
		_number = (number) ? number : false,
		_titles = (titles) ? titles : false,
		only_ends = (only_ends) ? true : false,
		debug = (debug) ? true : false;

	if(debug)
		console.log(_number +' '+  declOfNum(_titles, _number));

	if(onlyends)
    	return $elem.text(declOfNum(_titles, _number));
	else
    	return $elem.text(_number +' '+  declOfNum(_titles, _number));

};

var loadJSONP = (function(){
	var unique = 0;
	return function(url, callback, context) {

		var name = "_jsonp_" + unique++;

		if (url.match(/\?/))
			url += "&callback="+name;
		else
			url += "?callback="+name;

		var script = document.createElement('script');
		script.type = 'text/javascript';
		script.src = url;

		window[name] = function(data) {
			callback.call((context || window), data);
			document.getElementsByTagName('head')[0].removeChild(script);
			script = null;
			delete window[name];
		};

		document.getElementsByTagName('head')[0].appendChild(script);
	};
})();

const fetchJSONP = (unique => url =>
	new Promise(rs => {
		const script = document.createElement('script');
		const name = `_jsonp_${unique++}`;

		if (url.match(/\?/)) {
			url += `&callback=${name}`;
		} else {
			url += `?callback=${name}`;
		}

		script.src = url;
		window[name] = json => {
			rs(new Response(JSON.stringify(json)));
			script.remove();
			delete window[name];
		};

		document.body.appendChild(script);
	})
)(0);

const getOS = () => {

	let userAgent = window.navigator.userAgent,
		platform = window.navigator.platform,
		macPlatforms = ['Macintosh', 'MacIntel', 'MacPPC', 'Mac68K'],
		windowsPlatforms = ['Win32', 'Win64', 'Windows', 'WinCE'],
		iosPlatforms = ['iPhone', 'iPad', 'iPod'],
		os = null;

	if (macPlatforms.indexOf(platform) !== -1) {
		os = 'Mac OS';
	} else if (iosPlatforms.indexOf(platform) !== -1) {
		os = 'iOS';
	} else if (windowsPlatforms.indexOf(platform) !== -1) {
		os = 'Windows';
	} else if (/Android/.test(userAgent)) {
		os = 'Android';
	} else if (!os && /Linux/.test(platform)) {
		os = 'Linux';
	}

	return os;
}

jQuery.fn.checkSVG = function() {
	if(document.createElementNS("http://www.w3.org/2000/svg", 'svg').createSVGRect !== undefined)
		return jQuery(this).removeClass('no-svg');
	else
		return jQuery(this).addClass('no-svg');
};

jQuery.fn.checkFlexbox = function() {
	if (('flexWrap' in document.documentElement.style) || ('WebkitFlexWrap' in document.documentElement.style) || ('msFlexWrap' in document.documentElement.style))
		return jQuery(this).removeClass('no-flex');
	else
		return jQuery(this).addClass('no-flex');
};

jQuery.fn.cloneItems = function(selector, num, debug) {
	var $elem = jQuery(this),
		num = (num) ? num : 2,
		debug = (debug) ? true : false;

		$elem.find(selector).each(function () {
			var $item = $(this);
			for (var i = 1; i < num; i++) {
				$item.after($(this).clone());
			}
		});

		if(debug)
			console.log($elem);

		return $elem;
};

jQuery.fn.splitClone = function(selector, num, debug) {
	var $elem = jQuery(this),
		num = (num) ? num : 2,
		debug = (debug) ? true : false;

		$elem.find(selector).each(function () {
			var $item = $(this);
			for (var i = 1; i < num; i++) {
				$item = $item.next();
				if (!$item.length) {
					$item = $(this).siblings(':first');
				}
				$item.children(':first-child').clone().appendTo($(this));
			}
		});

		if(debug)
			console.log($elem);

		return $elem;
};

jQuery.fn.detectCollisions = function(selector, debug) {
	var $elem = jQuery(this),
    	$target = jQuery(selector),
		debug = (debug) ? true : false;

	var c = {
		offsetX1: $elem.offset().left,
		offsetY1: $elem.offset().top,
		height1: $elem.outerHeight(true),
		width1: $elem.outerWidth(true),
		boundingBoxY1: $elem.offset().top + $elem.outerHeight(true),
		boundingBoxX1: $elem.offset().left + $elem.outerWidth(true),
		offsetX2: $target.offset().left + 1,
		offsetY2: $target.offset().top + 1,
		height2: $target.outerHeight(true),
		width2: $target.outerWidth(true),
		boundingBoxY2: $target.offset().top + 1 + $target.outerHeight(true),
		boundingBoxX2: $target.offset().left + 1 + $target.outerWidth(true)
	};

	if(debug)
		console.log(c);

	if (c.boundingBoxY1 < c.offsetY2 || c.offsetX1 > c.boundingBoxY2 || c.boundingBoxX1 < c.offsetX2 || c.offsetX1 > c.boundingBoxX2)
		return false;
	else
		return true;
};

jQuery.fn.splitByWidth = function(selector, destination, offset, outer, debug) {

	var summaryWidth = 0,
		container = $(this),
		$destination = (destination) ? $(destination) : false,
		offset = (offset) ? offset : 0,
		outer = (outer) ? true : false,
		debug = (debug) ? true : false,
		countainerWidth = (outer) ? $(container).outerWidth(true) : $(container).width(),
		debug = (debug) ? true : false;

	if(offset)
		summaryWidth = offset;

	if(debug && outer)
		console.log('Countainer outer width: '+countainerWidth);
	else if(debug)
		console.log('Countainer width: '+countainerWidth);

	$(this).find(selector).each(function() {

		var elementWidth = (outer) ? $(this).outerWidth(true) : $(this).width();
		summaryWidth = summaryWidth + elementWidth;

		if (summaryWidth >= countainerWidth) {

			if($destination)
				$destination.append($(this).outerHtml());

			if(debug)
				console.log('Element out of container width and has been removed.');

			$(this).remove();
		}

	});

	if(debug && outer)
		console.log('Summary outer width: '+summaryWidth);
	else if(debug)
		console.log('Summary width: '+summaryWidth);

	return this;
};

jQuery.fn.splitByHeight = function(selector, destination, offset, outer, debug) {

	var summaryHeight = 0,
		container = $(this),
		$destination = (destination) ? $(destination) : false,
		offset = (offset) ? offset : 0,
		outer = (outer) ? true : false,
		debug = (debug) ? true : false,
		countainerHeight = (outer) ? $(container).outerHeight(true) : $(container).height(),
		debug = (debug) ? true : false;

	if(offset)
		summaryHeight = offset;

	if(debug && outer)
		console.log('Countainer outer height: '+countainerHeight);
	else if(debug)
		console.log('Countainer height: '+countainerHeight);

	$(this).find(selector).each(function() {

		var elementHeight = (outer) ? $(this).outerHeight(true) : $(this).height();
		summaryHeight = summaryHeight + elementHeight;

		if (summaryHeight >= countainerHeight) {

			if($destination)
				$destination.append($(this).outerHtml());

			if(debug)
				console.log('Element out of container height and has been removed.');

			$(this).remove();
		}

	});

	if(debug && outer)
		console.log('Summary outer height: '+summaryHeight);
	else if(debug)
		console.log('Summary height: '+summaryHeight);

	return this;
};

jQuery.fn.maxHeight = function(isouter, debug) {
	var isouter = (isouter) ? true : false;
	var debug = (debug) ? true : false;
    var height = 0;
    this.each(function() {

        if(isouter)
            var block_height = $(this).outerHeight();
        else
            var block_height = $(this).height();

        if(block_height > height)
            height = block_height;

    });

	if(debug)
		console.log('Max height of elements: '+height);

    return height;
}

function readCookie(name) {
    var cookies = document.cookie.split('; '),
    vars = {}, indx, cookie;

    for (indx = cookies.length - 1; indx >= 0; indx--) {
        cookie = cookies[indx].split('=');
        vars[cookie[0]] = cookie[1];
    }

    return vars[name];
}

function locationHash(param) {
	var vars = {};
	window.location.href.replace(location.hash, '').replace(
		/[?&]+([^=&]+)=?([^&]*)?/gi, // regexp
		function(m, key, value) { // callback
			vars[key] = value !== undefined ? value : '';
		}
	);

	if (param) {
		return vars[param] ? vars[param] : null;
	}
	return vars;
}

/* jQuery.browser */
var matched, browser;
jQuery.uaMatch = function(ua) {
    ua = ua.toLowerCase();
    var match = /(chrome)[ \/]([\w.]+)/.exec(ua) ||
        /(webkit)[ \/]([\w.]+)/.exec(ua) ||
        /(opera)(?:.*version|)[ \/]([\w.]+)/.exec(ua) ||
        /(msie) ([\w.]+)/.exec(ua) ||
        ua.indexOf("compatible") < 0 && /(mozilla)(?:.*? rv:([\w.]+)|)/.exec(ua) ||
        [];
    return {
        browser: match[ 1 ] || "",
        version: match[ 2 ] || "0"
    };
};
matched = jQuery.uaMatch( navigator.userAgent );
browser = {};
if (matched.browser) {
    browser[matched.browser] = true;
    browser.version = matched.version;
}

// Chrome is Webkit, but Webkit is also Safari.
if (browser.chrome) {
    browser.webkit = true;
} else if (browser.webkit) {
    browser.safari = true;
}
jQuery.browser = browser;


// Smooth scroll plugin
function smoothScroll() {

	if (window.addEventListener)
		window.addEventListener('DOMMouseScroll', wheel, false);

	window.onmousewheel = document.onmousewheel = wheel;

	var hb = {
		sTop: 0,
		sDelta: 0
	};

	function wheel(event) {

		var distance = jQuery.browser.webkit ? 60 : 120;
		if (event.wheelDelta)
			delta = event.wheelDelta / 120;
		else if (event.detail)
			delta = -event.detail / 3;

		hb.sTop = jQuery(window).scrollTop();
		hb.sDelta = hb.sDelta + delta * distance;

		jQuery(hb).stop().animate({
			sTop: jQuery(window).scrollTop() - hb.sDelta,
			sDelta: 0
		}, {
			duration: 200,
			easing: 'linear',
			step: function(now, ex) {
				if (ex.prop == 'sTop') jQuery('html, body').scrollTop(now)
			},
		});

		if (event.preventDefault)
			event.preventDefault();

		event.returnValue = false
	}

}

/*
* jQuery Mobile v1.5.0-pre
* http://jquerymobile.com
*
* Copyright jQuery Foundation, Inc. and other contributors
* Released under the MIT license.
* http://jquery.org/license
*
*/

(function ( root, doc, factory ) {
	if ( typeof define === "function" && define.amd ) {
		// AMD. Register as an anonymous module.
		define( [ "jquery" ], function ( $ ) {
			factory( $, root, doc );
			return $.mobile;
		});
	} else {
		// Browser globals
		factory( root.jQuery, root, doc );
	}
}( this, document, function ( jQuery, window, document, undefined ) {/*!
 * jQuery Mobile Virtual Mouse @VERSION
 * http://jquerymobile.com
 *
 * Copyright jQuery Foundation and other contributors
 * Released under the MIT license.
 * http://jquery.org/license
 */

//>>label: Virtual Mouse (vmouse) Bindings
//>>group: Core
//>>description: Normalizes touch/mouse events.
//>>docs: http://api.jquerymobile.com/?s=vmouse

// This plugin is an experiment for abstracting away the touch and mouse
// events so that developers don't have to worry about which method of input
// the device their document is loaded on supports.
//
// The idea here is to allow the developer to register listeners for the
// basic mouse events, such as mousedown, mousemove, mouseup, and click,
// and the plugin will take care of registering the correct listeners
// behind the scenes to invoke the listener at the fastest possible time
// for that device, while still retaining the order of event firing in
// the traditional mouse environment, should multiple handlers be registered
// on the same element for different events.
//
// The current version exposes the following virtual events to jQuery bind methods:
// "vmouseover vmousedown vmousemove vmouseup vclick vmouseout vmousecancel"

( function( factory ) {
	if ( typeof define === "function" && define.amd ) {

		// AMD. Register as an anonymous module.
		define( 'vmouse',[ "jquery" ], factory );
	} else {

		// Browser globals
		factory( jQuery );
	}
} )( function( $ ) {

var dataPropertyName = "virtualMouseBindings",
	touchTargetPropertyName = "virtualTouchID",
	touchEventProps = "clientX clientY pageX pageY screenX screenY".split( " " ),
	virtualEventNames = "vmouseover vmousedown vmousemove vmouseup vclick vmouseout vmousecancel".split( " " ),
	generalProps = ( "altKey bubbles cancelable ctrlKey currentTarget detail eventPhase " +
		"metaKey relatedTarget shiftKey target timeStamp view which" ).split( " " ),
	mouseHookProps = $.event.mouseHooks ? $.event.mouseHooks.props : [],
	mouseEventProps = generalProps.concat( mouseHookProps ),
	activeDocHandlers = {},
	resetTimerID = 0,
	startX = 0,
	startY = 0,
	didScroll = false,
	clickBlockList = [],
	blockMouseTriggers = false,
	blockTouchTriggers = false,
	eventCaptureSupported = "addEventListener" in document,
	$document = $( document ),
	nextTouchID = 1,
	lastTouchID = 0, threshold,
	i;

$.vmouse = {
	moveDistanceThreshold: 10,
	clickDistanceThreshold: 10,
	resetTimerDuration: 1500,
	maximumTimeBetweenTouches: 100
};

function getNativeEvent( event ) {

	while ( event && typeof event.originalEvent !== "undefined" ) {
		event = event.originalEvent;
	}
	return event;
}

function createVirtualEvent( event, eventType ) {

	var t = event.type,
		oe, props, ne, prop, ct, touch, i, j, len;

	event = $.Event( event );
	event.type = eventType;

	oe = event.originalEvent;
	props = generalProps;

	// addresses separation of $.event.props in to $.event.mouseHook.props and Issue 3280
	// https://github.com/jquery/jquery-mobile/issues/3280
	if ( t.search( /^(mouse|click)/ ) > -1 ) {
		props = mouseEventProps;
	}

	// copy original event properties over to the new event
	// this would happen if we could call $.event.fix instead of $.Event
	// but we don't have a way to force an event to be fixed multiple times
	if ( oe ) {
		for ( i = props.length; i; ) {
			prop = props[ --i ];
			event[ prop ] = oe[ prop ];
		}
	}

	// make sure that if the mouse and click virtual events are generated
	// without a .which one is defined
	if ( t.search( /mouse(down|up)|click/ ) > -1 && !event.which ) {
		event.which = 1;
	}

	if ( t.search( /^touch/ ) !== -1 ) {
		ne = getNativeEvent( oe );
		t = ne.touches;
		ct = ne.changedTouches;
		touch = ( t && t.length ) ? t[ 0 ] : ( ( ct && ct.length ) ? ct[ 0 ] : undefined );

		if ( touch ) {
			for ( j = 0, len = touchEventProps.length; j < len; j++ ) {
				prop = touchEventProps[ j ];
				event[ prop ] = touch[ prop ];
			}
		}
	}

	return event;
}

function getVirtualBindingFlags( element ) {

	var flags = {},
		b, k;

	while ( element ) {

		b = $.data( element, dataPropertyName );

		for ( k in b ) {
			if ( b[ k ] ) {
				flags[ k ] = flags.hasVirtualBinding = true;
			}
		}
		element = element.parentNode;
	}
	return flags;
}

function getClosestElementWithVirtualBinding( element, eventType ) {
	var b;
	while ( element ) {

		b = $.data( element, dataPropertyName );

		if ( b && ( !eventType || b[ eventType ] ) ) {
			return element;
		}
		element = element.parentNode;
	}
	return null;
}

function enableTouchBindings() {
	blockTouchTriggers = false;
}

function disableTouchBindings() {
	blockTouchTriggers = true;
}

function enableMouseBindings() {
	lastTouchID = 0;
	clickBlockList.length = 0;
	blockMouseTriggers = false;

	// When mouse bindings are enabled, our
	// touch bindings are disabled.
	disableTouchBindings();
}

function disableMouseBindings() {
	// When mouse bindings are disabled, our
	// touch bindings are enabled.
	enableTouchBindings();
}

function clearResetTimer() {
	if ( resetTimerID ) {
		clearTimeout( resetTimerID );
		resetTimerID = 0;
	}
}

function startResetTimer() {
	clearResetTimer();
	resetTimerID = setTimeout( function() {
		resetTimerID = 0;
		enableMouseBindings();
	}, $.vmouse.resetTimerDuration );
}

function triggerVirtualEvent( eventType, event, flags ) {
	var ve;

	if ( ( flags && flags[ eventType ] ) ||
			( !flags && getClosestElementWithVirtualBinding( event.target, eventType ) ) ) {

		ve = createVirtualEvent( event, eventType );

		$( event.target ).trigger( ve );
	}

	return ve;
}

function mouseEventCallback( event ) {
	var touchID = $.data( event.target, touchTargetPropertyName ),
		ve;

	// It is unexpected if a click event is received before a touchend
	// or touchmove event, however this is a known behavior in Mobile
	// Safari when Mobile VoiceOver (as of iOS 8) is enabled and the user
	// double taps to activate a link element. In these cases if a touch
	// event is not received within the maximum time between touches,
	// re-enable mouse bindings and call the mouse event handler again.
	if ( event.type === "click" && $.data( event.target, "lastTouchType" ) === "touchstart" ) {
		setTimeout( function() {
			if ( $.data( event.target, "lastTouchType" ) === "touchstart" ) {
				enableMouseBindings();
				delete $.data( event.target ).lastTouchType;
				mouseEventCallback( event );
			}
		}, $.vmouse.maximumTimeBetweenTouches );
	}

	if ( !blockMouseTriggers && ( !lastTouchID || lastTouchID !== touchID ) ) {
		ve = triggerVirtualEvent( "v" + event.type, event );
		if ( ve ) {
			if ( ve.isDefaultPrevented() ) {
				event.preventDefault();
			}
			if ( ve.isPropagationStopped() ) {
				event.stopPropagation();
			}
			if ( ve.isImmediatePropagationStopped() ) {
				event.stopImmediatePropagation();
			}
		}
	}
}

function handleTouchStart( event ) {

	var touches = getNativeEvent( event ).touches,
		target, flags, t;

	if ( touches && touches.length === 1 ) {

		target = event.target;
		flags = getVirtualBindingFlags( target );

		$.data( event.target, "lastTouchType", event.type );

		if ( flags.hasVirtualBinding ) {

			lastTouchID = nextTouchID++;
			$.data( target, touchTargetPropertyName, lastTouchID );

			clearResetTimer();

			disableMouseBindings();
			didScroll = false;

			t = getNativeEvent( event ).touches[ 0 ];
			startX = t.pageX;
			startY = t.pageY;

			triggerVirtualEvent( "vmouseover", event, flags );
			triggerVirtualEvent( "vmousedown", event, flags );
		}
	}
}

function handleScroll( event ) {
	if ( blockTouchTriggers ) {
		return;
	}

	if ( !didScroll ) {
		triggerVirtualEvent( "vmousecancel", event, getVirtualBindingFlags( event.target ) );
	}

	$.data( event.target, "lastTouchType", event.type );

	didScroll = true;
	startResetTimer();
}

function handleTouchMove( event ) {
	if ( blockTouchTriggers ) {
		return;
	}

	var t = getNativeEvent( event ).touches[ 0 ],
		didCancel = didScroll,
		moveThreshold = $.vmouse.moveDistanceThreshold,
		flags = getVirtualBindingFlags( event.target );

	$.data( event.target, "lastTouchType", event.type );

	didScroll = didScroll ||
		( Math.abs( t.pageX - startX ) > moveThreshold ||
		Math.abs( t.pageY - startY ) > moveThreshold );

	if ( didScroll && !didCancel ) {
		triggerVirtualEvent( "vmousecancel", event, flags );
	}

	triggerVirtualEvent( "vmousemove", event, flags );
	startResetTimer();
}

function handleTouchEnd( event ) {
	if ( blockTouchTriggers || $.data( event.target, "lastTouchType" ) === undefined ) {
		return;
	}

	disableTouchBindings();
	delete $.data( event.target ).lastTouchType;

	var flags = getVirtualBindingFlags( event.target ),
		ve, t;
	triggerVirtualEvent( "vmouseup", event, flags );

	if ( !didScroll ) {
		ve = triggerVirtualEvent( "vclick", event, flags );
		if ( ve && ve.isDefaultPrevented() ) {
			// The target of the mouse events that follow the touchend
			// event don't necessarily match the target used during the
			// touch. This means we need to rely on coordinates for blocking
			// any click that is generated.
			t = getNativeEvent( event ).changedTouches[ 0 ];
			clickBlockList.push( {
				touchID: lastTouchID,
				x: t.clientX,
				y: t.clientY
			} );

			// Prevent any mouse events that follow from triggering
			// virtual event notifications.
			blockMouseTriggers = true;
		}
	}
	triggerVirtualEvent( "vmouseout", event, flags );
	didScroll = false;

	startResetTimer();
}

function hasVirtualBindings( ele ) {
	var bindings = $.data( ele, dataPropertyName ),
		k;

	if ( bindings ) {
		for ( k in bindings ) {
			if ( bindings[ k ] ) {
				return true;
			}
		}
	}
	return false;
}

function dummyMouseHandler() {
}

function getSpecialEventObject( eventType ) {
	var realType = eventType.substr( 1 );

	return {
		setup: function( /* data, namespace */ ) {
			// If this is the first virtual mouse binding for this element,
			// add a bindings object to its data.

			if ( !hasVirtualBindings( this ) ) {
				$.data( this, dataPropertyName, {} );
			}

			// If setup is called, we know it is the first binding for this
			// eventType, so initialize the count for the eventType to zero.
			var bindings = $.data( this, dataPropertyName );
			bindings[ eventType ] = true;

			// If this is the first virtual mouse event for this type,
			// register a global handler on the document.

			activeDocHandlers[ eventType ] = ( activeDocHandlers[ eventType ] || 0 ) + 1;

			if ( activeDocHandlers[ eventType ] === 1 ) {
				$document.bind( realType, mouseEventCallback );
			}

			// Some browsers, like Opera Mini, won't dispatch mouse/click events
			// for elements unless they actually have handlers registered on them.
			// To get around this, we register dummy handlers on the elements.

			$( this ).bind( realType, dummyMouseHandler );

			// For now, if event capture is not supported, we rely on mouse handlers.
			if ( eventCaptureSupported ) {
				// If this is the first virtual mouse binding for the document,
				// register our touchstart handler on the document.

				activeDocHandlers[ "touchstart" ] = ( activeDocHandlers[ "touchstart" ] || 0 ) + 1;

				if ( activeDocHandlers[ "touchstart" ] === 1 ) {
					$document.bind( "touchstart", handleTouchStart )
						.bind( "touchend", handleTouchEnd )

						// On touch platforms, touching the screen and then dragging your finger
						// causes the window content to scroll after some distance threshold is
						// exceeded. On these platforms, a scroll prevents a click event from being
						// dispatched, and on some platforms, even the touchend is suppressed. To
						// mimic the suppression of the click event, we need to watch for a scroll
						// event. Unfortunately, some platforms like iOS don't dispatch scroll
						// events until *AFTER* the user lifts their finger (touchend). This means
						// we need to watch both scroll and touchmove events to figure out whether
						// or not a scroll happenens before the touchend event is fired.

						.bind( "touchmove", handleTouchMove )
						.bind( "scroll", handleScroll );
				}
			}
		},

		teardown: function( /* data, namespace */ ) {
			// If this is the last virtual binding for this eventType,
			// remove its global handler from the document.

			--activeDocHandlers[eventType];

			if ( !activeDocHandlers[ eventType ] ) {
				$document.unbind( realType, mouseEventCallback );
			}

			if ( eventCaptureSupported ) {
				// If this is the last virtual mouse binding in existence,
				// remove our document touchstart listener.

				--activeDocHandlers["touchstart"];

				if ( !activeDocHandlers[ "touchstart" ] ) {
					$document.unbind( "touchstart", handleTouchStart )
						.unbind( "touchmove", handleTouchMove )
						.unbind( "touchend", handleTouchEnd )
						.unbind( "scroll", handleScroll );
				}
			}

			var $this = $( this ),
				bindings = $.data( this, dataPropertyName );

			// teardown may be called when an element was
			// removed from the DOM. If this is the case,
			// jQuery core may have already stripped the element
			// of any data bindings so we need to check it before
			// using it.
			if ( bindings ) {
				bindings[ eventType ] = false;
			}

			// Unregister the dummy event handler.

			$this.unbind( realType, dummyMouseHandler );

			// If this is the last virtual mouse binding on the
			// element, remove the binding data from the element.

			if ( !hasVirtualBindings( this ) ) {
				$this.removeData( dataPropertyName );
			}
		}
	};
}

// Expose our custom events to the jQuery bind/unbind mechanism.

for ( i = 0; i < virtualEventNames.length; i++ ) {
	$.event.special[ virtualEventNames[ i ] ] = getSpecialEventObject( virtualEventNames[ i ] );
}

// Add a capture click handler to block clicks.
// Note that we require event capture support for this so if the device
// doesn't support it, we punt for now and rely solely on mouse events.
if ( eventCaptureSupported ) {
	document.addEventListener( "click", function( e ) {
		var cnt = clickBlockList.length,
			target = e.target,
			x, y, ele, i, o, touchID;

		if ( cnt ) {
			x = e.clientX;
			y = e.clientY;
			threshold = $.vmouse.clickDistanceThreshold;

			// The idea here is to run through the clickBlockList to see if
			// the current click event is in the proximity of one of our
			// vclick events that had preventDefault() called on it. If we find
			// one, then we block the click.
			//
			// Why do we have to rely on proximity?
			//
			// Because the target of the touch event that triggered the vclick
			// can be different from the target of the click event synthesized
			// by the browser. The target of a mouse/click event that is synthesized
			// from a touch event seems to be implementation specific. For example,
			// some browsers will fire mouse/click events for a link that is near
			// a touch event, even though the target of the touchstart/touchend event
			// says the user touched outside the link. Also, it seems that with most
			// browsers, the target of the mouse/click event is not calculated until the
			// time it is dispatched, so if you replace an element that you touched
			// with another element, the target of the mouse/click will be the new
			// element underneath that point.
			//
			// Aside from proximity, we also check to see if the target and any
			// of its ancestors were the ones that blocked a click. This is necessary
			// because of the strange mouse/click target calculation done in the
			// Android 2.1 browser, where if you click on an element, and there is a
			// mouse/click handler on one of its ancestors, the target will be the
			// innermost child of the touched element, even if that child is no where
			// near the point of touch.

			ele = target;

			while ( ele ) {
				for ( i = 0; i < cnt; i++ ) {
					o = clickBlockList[ i ];
					touchID = 0;

					if ( ( ele === target && Math.abs( o.x - x ) < threshold && Math.abs( o.y - y ) < threshold ) ||
							$.data( ele, touchTargetPropertyName ) === o.touchID ) {
						// XXX: We may want to consider removing matches from the block list
						//      instead of waiting for the reset timer to fire.
						e.preventDefault();
						e.stopPropagation();
						return;
					}
				}
				ele = ele.parentNode;
			}
		}
	}, true );
}
} );

/*!
 * jQuery Mobile Namespace @VERSION
 * http://jquerymobile.com
 *
 * Copyright jQuery Foundation and other contributors
 * Released under the MIT license.
 * http://jquery.org/license
 */

//>>label: Namespace
//>>group: Core
//>>description: The mobile namespace on the jQuery object

( function( factory ) {
	if ( typeof define === "function" && define.amd ) {

		// AMD. Register as an anonymous module.
		define( 'ns',[ "jquery" ], factory );
	} else {

		// Browser globals
		factory( jQuery );
	}
} )( function( $ ) {

$.mobile = { version: "@VERSION" };

return $.mobile;
} );

/*!
 * jQuery Mobile Touch Support Test @VERSION
 * http://jquerymobile.com
 *
 * Copyright jQuery Foundation and other contributors
 * Released under the MIT license.
 * http://jquery.org/license
 */

//>>label: Touch support test
//>>group: Core
//>>description: Touch feature test

( function( factory ) {
	if ( typeof define === "function" && define.amd ) {

		// AMD. Register as an anonymous module.
		define( 'support/touch',[
			"jquery",
			"../ns" ], factory );
	} else {

		// Browser globals
		factory( jQuery );
	}
} )( function( $ ) {

var support = {
	touch: "ontouchend" in document
};

$.mobile.support = $.mobile.support || {};
$.extend( $.support, support );
$.extend( $.mobile.support, support );

return $.support;
} );

/*!
 * jQuery Mobile Touch Events @VERSION
 * http://jquerymobile.com
 *
 * Copyright jQuery Foundation and other contributors
 * Released under the MIT license.
 * http://jquery.org/license
 */

//>>label: Touch
//>>group: Events
//>>description: Touch events including: touchstart, touchmove, touchend, tap, taphold, swipe, swipeleft, swiperight

( function( factory ) {
	if ( typeof define === "function" && define.amd ) {

		// AMD. Register as an anonymous module.
		define( 'events/touch',[
			"jquery",
			"../vmouse",
			"../support/touch" ], factory );
	} else {

		// Browser globals
		factory( jQuery );
	}
} )( function( $ ) {
var $document = $( document ),
	supportTouch = $.mobile.support.touch,
	touchStartEvent = supportTouch ? "touchstart" : "mousedown",
	touchStopEvent = supportTouch ? "touchend" : "mouseup",
	touchMoveEvent = supportTouch ? "touchmove" : "mousemove";

// setup new event shortcuts
$.each( ( "touchstart touchmove touchend " +
"tap taphold " +
"swipe swipeleft swiperight" ).split( " " ), function( i, name ) {

	$.fn[ name ] = function( fn ) {
		return fn ? this.bind( name, fn ) : this.trigger( name );
	};

	// jQuery < 1.8
	if ( $.attrFn ) {
		$.attrFn[ name ] = true;
	}
} );

function triggerCustomEvent( obj, eventType, event, bubble ) {
	var originalType = event.type;
	event.type = eventType;
	if ( bubble ) {
		$.event.trigger( event, undefined, obj );
	} else {
		$.event.dispatch.call( obj, event );
	}
	event.type = originalType;
}

// also handles taphold
$.event.special.tap = {
	tapholdThreshold: 750,
	emitTapOnTaphold: true,
	setup: function() {
		var thisObject = this,
			$this = $( thisObject ),
			isTaphold = false;

		$this.bind( "vmousedown", function( event ) {
			isTaphold = false;
			if ( event.which && event.which !== 1 ) {
				return true;
			}

			var origTarget = event.target,
				timer, clickHandler;

			function clearTapTimer() {
				if ( timer ) {
					$this.bind( "vclick", clickHandler );
					clearTimeout( timer );
				}
			}

			function clearTapHandlers() {
				clearTapTimer();

				$this.unbind( "vclick", clickHandler )
					.unbind( "vmouseup", clearTapTimer );
				$document.unbind( "vmousecancel", clearTapHandlers );
			}

			clickHandler = function( event ) {
				clearTapHandlers();

				// ONLY trigger a 'tap' event if the start target is
				// the same as the stop target.
				if ( !isTaphold && origTarget === event.target ) {
					triggerCustomEvent( thisObject, "tap", event );
				} else if ( isTaphold ) {
					event.preventDefault();
				}
			};

			$this.bind( "vmouseup", clearTapTimer );

			$document.bind( "vmousecancel", clearTapHandlers );

			timer = setTimeout( function() {
				if ( !$.event.special.tap.emitTapOnTaphold ) {
					isTaphold = true;
				}
				timer = 0;
				triggerCustomEvent( thisObject, "taphold", $.Event( "taphold", { target: origTarget } ) );
			}, $.event.special.tap.tapholdThreshold );
		} );
	},
	teardown: function() {
		$( this ).unbind( "vmousedown" ).unbind( "vclick" ).unbind( "vmouseup" );
		$document.unbind( "vmousecancel" );
	}
};

// Also handles swipeleft, swiperight
$.event.special.swipe = {

	// More than this horizontal displacement, and we will suppress scrolling.
	scrollSupressionThreshold: 30,

	// More time than this, and it isn't a swipe.
	durationThreshold: 1000,

	// Swipe horizontal displacement must be more than this.
	horizontalDistanceThreshold: window.devicePixelRatio >= 2 ? 15 : 30,

	// Swipe vertical displacement must be less than this.
	verticalDistanceThreshold: window.devicePixelRatio >= 2 ? 15 : 30,

	getLocation: function( event ) {
		var winPageX = window.pageXOffset,
			winPageY = window.pageYOffset,
			x = event.clientX,
			y = event.clientY;

		if ( event.pageY === 0 && Math.floor( y ) > Math.floor( event.pageY ) ||
				event.pageX === 0 && Math.floor( x ) > Math.floor( event.pageX ) ) {

			// iOS4 clientX/clientY have the value that should have been
			// in pageX/pageY. While pageX/page/ have the value 0
			x = x - winPageX;
			y = y - winPageY;
		} else if ( y < ( event.pageY - winPageY ) || x < ( event.pageX - winPageX ) ) {

			// Some Android browsers have totally bogus values for clientX/Y
			// when scrolling/zooming a page. Detectable since clientX/clientY
			// should never be smaller than pageX/pageY minus page scroll
			x = event.pageX - winPageX;
			y = event.pageY - winPageY;
		}

		return {
			x: x,
			y: y
		};
	},

	start: function( event ) {
		var data = event.originalEvent.touches ?
				event.originalEvent.touches[ 0 ] : event,
			location = $.event.special.swipe.getLocation( data );
		return {
			time: ( new Date() ).getTime(),
			coords: [ location.x, location.y ],
			origin: $( event.target )
		};
	},

	stop: function( event ) {
		var data = event.originalEvent.touches ?
				event.originalEvent.touches[ 0 ] : event,
			location = $.event.special.swipe.getLocation( data );
		return {
			time: ( new Date() ).getTime(),
			coords: [ location.x, location.y ]
		};
	},

	handleSwipe: function( start, stop, thisObject, origTarget ) {
		if ( stop.time - start.time < $.event.special.swipe.durationThreshold &&
				Math.abs( start.coords[ 0 ] - stop.coords[ 0 ] ) > $.event.special.swipe.horizontalDistanceThreshold &&
				Math.abs( start.coords[ 1 ] - stop.coords[ 1 ] ) < $.event.special.swipe.verticalDistanceThreshold ) {
			var direction = start.coords[ 0 ] > stop.coords[ 0 ] ? "swipeleft" : "swiperight";

			triggerCustomEvent( thisObject, "swipe", $.Event( "swipe", { target: origTarget, swipestart: start, swipestop: stop } ), true );
			triggerCustomEvent( thisObject, direction, $.Event( direction, { target: origTarget, swipestart: start, swipestop: stop } ), true );
			return true;
		}
		return false;

	},

	// This serves as a flag to ensure that at most one swipe event event is
	// in work at any given time
	eventInProgress: false,

	setup: function() {
		var events,
			thisObject = this,
			$this = $( thisObject ),
			context = {};

		// Retrieve the events data for this element and add the swipe context
		events = $.data( this, "mobile-events" );
		if ( !events ) {
			events = { length: 0 };
			$.data( this, "mobile-events", events );
		}
		events.length++;
		events.swipe = context;

		context.start = function( event ) {

			// Bail if we're already working on a swipe event
			if ( $.event.special.swipe.eventInProgress ) {
				return;
			}
			$.event.special.swipe.eventInProgress = true;

			var stop,
				start = $.event.special.swipe.start( event ),
				origTarget = event.target,
				emitted = false;

			context.move = function( event ) {
				if ( !start || event.isDefaultPrevented() ) {
					return;
				}

				stop = $.event.special.swipe.stop( event );
				if ( !emitted ) {
					emitted = $.event.special.swipe.handleSwipe( start, stop, thisObject, origTarget );
					if ( emitted ) {

						// Reset the context to make way for the next swipe event
						$.event.special.swipe.eventInProgress = false;
					}
				}
				// prevent scrolling
				if ( Math.abs( start.coords[ 0 ] - stop.coords[ 0 ] ) > $.event.special.swipe.scrollSupressionThreshold ) {
					event.preventDefault();
				}
			};

			context.stop = function() {
				emitted = true;

				// Reset the context to make way for the next swipe event
				$.event.special.swipe.eventInProgress = false;
				$document.off( touchMoveEvent, context.move );
				context.move = null;
			};

			$document.on( touchMoveEvent, context.move )
				.one( touchStopEvent, context.stop );
		};
		$this.on( touchStartEvent, context.start );
	},

	teardown: function() {
		var events, context;

		events = $.data( this, "mobile-events" );
		if ( events ) {
			context = events.swipe;
			delete events.swipe;
			events.length--;
			if ( events.length === 0 ) {
				$.removeData( this, "mobile-events" );
			}
		}

		if ( context ) {
			if ( context.start ) {
				$( this ).off( touchStartEvent, context.start );
			}
			if ( context.move ) {
				$document.off( touchMoveEvent, context.move );
			}
			if ( context.stop ) {
				$document.off( touchStopEvent, context.stop );
			}
		}
	}
};
$.each( {
	taphold: "tap",
	swipeleft: "swipe.left",
	swiperight: "swipe.right"
}, function( event, sourceEvent ) {

	$.event.special[ event ] = {
		setup: function() {
			$( this ).bind( sourceEvent, $.noop );
		},
		teardown: function() {
			$( this ).unbind( sourceEvent );
		}
	};
} );

return $.event.special;
} );



}));
