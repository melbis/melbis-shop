/*!
  * Bootstrap v4.6.2 (https://getbootstrap.com/)
  * Copyright 2011-2022 The Bootstrap Authors (https://github.com/twbs/bootstrap/graphs/contributors)
  * Licensed under MIT (https://github.com/twbs/bootstrap/blob/main/LICENSE)
  */
!function(t,e){"object"==typeof exports&&"undefined"!=typeof module?e(exports,require("jquery")):"function"==typeof define&&define.amd?define(["exports","jquery"],e):e((t="undefined"!=typeof globalThis?globalThis:t||self).bootstrap={},t.jQuery)}(this,(function(t,e){"use strict";function n(t){return t&&"object"==typeof t&&"default"in t?t:{default:t}}var i=n(e);function o(t,e){for(var n=0;n<e.length;n++){var i=e[n];i.enumerable=i.enumerable||!1,i.configurable=!0,"value"in i&&(i.writable=!0),Object.defineProperty(t,i.key,i)}}function r(t,e,n){return e&&o(t.prototype,e),n&&o(t,n),Object.defineProperty(t,"prototype",{writable:!1}),t}function a(){return a=Object.assign?Object.assign.bind():function(t){for(var e=1;e<arguments.length;e++){var n=arguments[e];for(var i in n)Object.prototype.hasOwnProperty.call(n,i)&&(t[i]=n[i])}return t},a.apply(this,arguments)}function s(t,e){return s=Object.setPrototypeOf?Object.setPrototypeOf.bind():function(t,e){return t.__proto__=e,t},s(t,e)}var l="transitionend";var u={TRANSITION_END:"bsTransitionEnd",getUID:function(t){do{t+=~~(1e6*Math.random())}while(document.getElementById(t));return t},getSelectorFromElement:function(t){var e=t.getAttribute("data-target");if(!e||"#"===e){var n=t.getAttribute("href");e=n&&"#"!==n?n.trim():""}try{return document.querySelector(e)?e:null}catch(t){return null}},getTransitionDurationFromElement:function(t){if(!t)return 0;var e=i.default(t).css("transition-duration"),n=i.default(t).css("transition-delay"),o=parseFloat(e),r=parseFloat(n);return o||r?(e=e.split(",")[0],n=n.split(",")[0],1e3*(parseFloat(e)+parseFloat(n))):0},reflow:function(t){return t.offsetHeight},triggerTransitionEnd:function(t){i.default(t).trigger(l)},supportsTransitionEnd:function(){return Boolean(l)},isElement:function(t){return(t[0]||t).nodeType},typeCheckConfig:function(t,e,n){for(var i in n)if(Object.prototype.hasOwnProperty.call(n,i)){var o=n[i],r=e[i],a=r&&u.isElement(r)?"element":null===(s=r)||"undefined"==typeof s?""+s:{}.toString.call(s).match(/\s([a-z]+)/i)[1].toLowerCase();if(!new RegExp(o).test(a))throw new Error(t.toUpperCase()+': Option "'+i+'" provided type "'+a+'" but expected type "'+o+'".')}var s},findShadowRoot:function(t){if(!document.documentElement.attachShadow)return null;if("function"==typeof t.getRootNode){var e=t.getRootNode();return e instanceof ShadowRoot?e:null}return t instanceof ShadowRoot?t:t.parentNode?u.findShadowRoot(t.parentNode):null},jQueryDetection:function(){if("undefined"==typeof i.default)throw new TypeError("Bootstrap's JavaScript requires jQuery. jQuery must be included before Bootstrap's JavaScript.");var t=i.default.fn.jquery.split(" ")[0].split(".");if(t[0]<2&&t[1]<9||1===t[0]&&9===t[1]&&t[2]<1||t[0]>=4)throw new Error("Bootstrap's JavaScript requires at least jQuery v1.9.1 but less than v4.0.0")}};u.jQueryDetection(),i.default.fn.emulateTransitionEnd=function(t){var e=this,n=!1;return i.default(this).one(u.TRANSITION_END,(function(){n=!0})),setTimeout((function(){n||u.triggerTransitionEnd(e)}),t),this},i.default.event.special[u.TRANSITION_END]={bindType:l,delegateType:l,handle:function(t){if(i.default(t.target).is(this))return t.handleObj.handler.apply(this,arguments)}};var f="bs.alert",d=i.default.fn.alert,c=function(){function t(t){this._element=t}var e=t.prototype;return e.close=function(t){var e=this._element;t&&(e=this._getRootElement(t)),this._triggerCloseEvent(e).isDefaultPrevented()||this._removeElement(e)},e.dispose=function(){i.default.removeData(this._element,f),this._element=null},e._getRootElement=function(t){var e=u.getSelectorFromElement(t),n=!1;return e&&(n=document.querySelector(e)),n||(n=i.default(t).closest(".alert")[0]),n},e._triggerCloseEvent=function(t){var e=i.default.Event("close.bs.alert");return i.default(t).trigger(e),e},e._removeElement=function(t){var e=this;if(i.default(t).removeClass("show"),i.default(t).hasClass("fade")){var n=u.getTransitionDurationFromElement(t);i.default(t).one(u.TRANSITION_END,(function(n){return e._destroyElement(t,n)})).emulateTransitionEnd(n)}else this._destroyElement(t)},e._destroyElement=function(t){i.default(t).detach().trigger("closed.bs.alert").remove()},t._jQueryInterface=function(e){return this.each((function(){var n=i.default(this),o=n.data(f);o||(o=new t(this),n.data(f,o)),"close"===e&&o[e](this)}))},t._handleDismiss=function(t){return function(e){e&&e.preventDefault(),t.close(this)}},r(t,null,[{key:"VERSION",get:function(){return"4.6.2"}}]),t}();i.default(document).on("click.bs.alert.data-api",'[data-dismiss="alert"]',c._handleDismiss(new c)),i.default.fn.alert=c._jQueryInterface,i.default.fn.alert.Constructor=c,i.default.fn.alert.noConflict=function(){return i.default.fn.alert=d,c._jQueryInterface};var h="bs.button",p=i.default.fn.button,m="active",g='[data-toggle^="button"]',_='input:not([type="hidden"])',v=".btn",b=function(){function t(t){this._element=t,this.shouldAvoidTriggerChange=!1}var e=t.prototype;return e.toggle=function(){var t=!0,e=!0,n=i.default(this._element).closest('[data-toggle="buttons"]')[0];if(n){var o=this._element.querySelector(_);if(o){if("radio"===o.type)if(o.checked&&this._element.classList.contains(m))t=!1;else{var r=n.querySelector(".active");r&&i.default(r).removeClass(m)}t&&("checkbox"!==o.type&&"radio"!==o.type||(o.checked=!this._element.classList.contains(m)),this.shouldAvoidTriggerChange||i.default(o).trigger("change")),o.focus(),e=!1}}this._element.hasAttribute("disabled")||this._element.classList.contains("disabled")||(e&&this._element.setAttribute("aria-pressed",!this._element.classList.contains(m)),t&&i.default(this._element).toggleClass(m))},e.dispose=function(){i.default.removeData(this._element,h),this._element=null},t._jQueryInterface=function(e,n){return this.each((function(){var o=i.default(this),r=o.data(h);r||(r=new t(this),o.data(h,r)),r.shouldAvoidTriggerChange=n,"toggle"===e&&r[e]()}))},r(t,null,[{key:"VERSION",get:function(){return"4.6.2"}}]),t}();i.default(document).on("click.bs.button.data-api",g,(function(t){var e=t.target,n=e;if(i.default(e).hasClass("btn")||(e=i.default(e).closest(v)[0]),!e||e.hasAttribute("disabled")||e.classList.contains("disabled"))t.preventDefault();else{var o=e.querySelector(_);if(o&&(o.hasAttribute("disabled")||o.classList.contains("disabled")))return void t.preventDefault();"INPUT"!==n.tagName&&"LABEL"===e.tagName||b._jQueryInterface.call(i.default(e),"toggle","INPUT"===n.tagName)}})).on("focus.bs.button.data-api blur.bs.button.data-api",g,(function(t){var e=i.default(t.target).closest(v)[0];i.default(e).toggleClass("focus",/^focus(in)?$/.test(t.type))})),i.default(window).on("load.bs.button.data-api",(function(){for(var t=[].slice.call(document.querySelectorAll('[data-toggle="buttons"] .btn')),e=0,n=t.length;e<n;e++){var i=t[e],o=i.querySelector(_);o.checked||o.hasAttribute("checked")?i.classList.add(m):i.classList.remove(m)}for(var r=0,a=(t=[].slice.call(document.querySelectorAll('[data-toggle="button"]'))).length;r<a;r++){var s=t[r];"true"===s.getAttribute("aria-pressed")?s.classList.add(m):s.classList.remove(m)}})),i.default.fn.button=b._jQueryInterface,i.default.fn.button.Constructor=b,i.default.fn.button.noConflict=function(){return i.default.fn.button=p,b._jQueryInterface};var y="carousel",E="bs.carousel",w=i.default.fn[y],T="active",C="next",S="prev",N="slid.bs.carousel",D=".active.carousel-item",A={interval:5e3,keyboard:!0,slide:!1,pause:"hover",wrap:!0,touch:!0},k={interval:"(number|boolean)",keyboard:"boolean",slide:"(boolean|string)",pause:"(string|boolean)",wrap:"boolean",touch:"boolean"},I={TOUCH:"touch",PEN:"pen"},O=function(){function t(t,e){this._items=null,this._interval=null,this._activeElement=null,this._isPaused=!1,this._isSliding=!1,this.touchTimeout=null,this.touchStartX=0,this.touchDeltaX=0,this._config=this._getConfig(e),this._element=t,this._indicatorsElement=this._element.querySelector(".carousel-indicators"),this._touchSupported="ontouchstart"in document.documentElement||navigator.maxTouchPoints>0,this._pointerEvent=Boolean(window.PointerEvent||window.MSPointerEvent),this._addEventListeners()}var e=t.prototype;return e.next=function(){this._isSliding||this._slide(C)},e.nextWhenVisible=function(){var t=i.default(this._element);!document.hidden&&t.is(":visible")&&"hidden"!==t.css("visibility")&&this.next()},e.prev=function(){this._isSliding||this._slide(S)},e.pause=function(t){t||(this._isPaused=!0),this._element.querySelector(".carousel-item-next, .carousel-item-prev")&&(u.triggerTransitionEnd(this._element),this.cycle(!0)),clearInterval(this._interval),this._interval=null},e.cycle=function(t){t||(this._isPaused=!1),this._interval&&(clearInterval(this._interval),this._interval=null),this._config.interval&&!this._isPaused&&(this._updateInterval(),this._interval=setInterval((document.visibilityState?this.nextWhenVisible:this.next).bind(this),this._config.interval))},e.to=function(t){var e=this;this._activeElement=this._element.querySelector(D);var n=this._getItemIndex(this._activeElement);if(!(t>this._items.length-1||t<0))if(this._isSliding)i.default(this._element).one(N,(function(){return e.to(t)}));else{if(n===t)return this.pause(),void this.cycle();var o=t>n?C:S;this._slide(o,this._items[t])}},e.dispose=function(){i.default(this._element).off(".bs.carousel"),i.default.removeData(this._element,E),this._items=null,this._config=null,this._element=null,this._interval=null,this._isPaused=null,this._isSliding=null,this._activeElement=null,this._indicatorsElement=null},e._getConfig=function(t){return t=a({},A,t),u.typeCheckConfig(y,t,k),t},e._handleSwipe=function(){var t=Math.abs(this.touchDeltaX);if(!(t<=40)){var e=t/this.touchDeltaX;this.touchDeltaX=0,e>0&&this.prev(),e<0&&this.next()}},e._addEventListeners=function(){var t=this;this._config.keyboard&&i.default(this._element).on("keydown.bs.carousel",(function(e){return t._keydown(e)})),"hover"===this._config.pause&&i.default(this._element).on("mouseenter.bs.carousel",(function(e){return t.pause(e)})).on("mouseleave.bs.carousel",(function(e){return t.cycle(e)})),this._config.touch&&this._addTouchEventListeners()},e._addTouchEventListeners=function(){var t=this;if(this._touchSupported){var e=function(e){t._pointerEvent&&I[e.originalEvent.pointerType.toUpperCase()]?t.touchStartX=e.originalEvent.clientX:t._pointerEvent||(t.touchStartX=e.originalEvent.touches[0].clientX)},n=function(e){t._pointerEvent&&I[e.originalEvent.pointerType.toUpperCase()]&&(t.touchDeltaX=e.originalEvent.clientX-t.touchStartX),t._handleSwipe(),"hover"===t._config.pause&&(t.pause(),t.touchTimeout&&clearTimeout(t.touchTimeout),t.touchTimeout=setTimeout((function(e){return t.cycle(e)}),500+t._config.interval))};i.default(this._element.querySelectorAll(".carousel-item img")).on("dragstart.bs.carousel",(function(t){return t.preventDefault()})),this._pointerEvent?(i.default(this._element).on("pointerdown.bs.carousel",(function(t){return e(t)})),i.default(this._element).on("pointerup.bs.carousel",(function(t){return n(t)})),this._element.classList.add("pointer-event")):(i.default(this._element).on("touchstart.bs.carousel",(function(t){return e(t)})),i.default(this._element).on("touchmove.bs.carousel",(function(e){return function(e){t.touchDeltaX=e.originalEvent.touches&&e.originalEvent.touches.length>1?0:e.originalEvent.touches[0].clientX-t.touchStartX}(e)})),i.default(this._element).on("touchend.bs.carousel",(function(t){return n(t)})))}},e._keydown=function(t){if(!/input|textarea/i.test(t.target.tagName))switch(t.which){case 37:t.preventDefault(),this.prev();break;case 39:t.preventDefault(),this.next()}},e._getItemIndex=function(t){return this._items=t&&t.parentNode?[].slice.call(t.parentNode.querySelectorAll(".carousel-item")):[],this._items.indexOf(t)},e._getItemByDirection=function(t,e){var n=t===C,i=t===S,o=this._getItemIndex(e),r=this._items.length-1;if((i&&0===o||n&&o===r)&&!this._config.wrap)return e;var a=(o+(t===S?-1:1))%this._items.length;return-1===a?this._items[this._items.length-1]:this._items[a]},e._triggerSlideEvent=function(t,e){var n=this._getItemIndex(t),o=this._getItemIndex(this._element.querySelector(D)),r=i.default.Event("slide.bs.carousel",{relatedTarget:t,direction:e,from:o,to:n});return i.default(this._element).trigger(r),r},e._setActiveIndicatorElement=function(t){if(this._indicatorsElement){var e=[].slice.call(this._indicatorsElement.querySelectorAll(".active"));i.default(e).removeClass(T);var n=this._indicatorsElement.children[this._getItemIndex(t)];n&&i.default(n).addClass(T)}},e._updateInterval=function(){var t=this._activeElement||this._element.querySelector(D);if(t){var e=parseInt(t.getAttribute("data-interval"),10);e?(this._config.defaultInterval=this._config.defaultInterval||this._config.interval,this._config.interval=e):this._config.interval=this._config.defaultInterval||this._config.interval}},e._slide=function(t,e){var n,o,r,a=this,s=this._element.querySelector(D),l=this._getItemIndex(s),f=e||s&&this._getItemByDirection(t,s),d=this._getItemIndex(f),c=Boolean(this._interval);if(t===C?(n="carousel-item-left",o="carousel-item-next",r="left"):(n="carousel-item-right",o="carousel-item-prev",r="right"),f&&i.default(f).hasClass(T))this._isSliding=!1;else if(!this._triggerSlideEvent(f,r).isDefaultPrevented()&&s&&f){this._isSliding=!0,c&&this.pause(),this._setActiveIndicatorElement(f),this._activeElement=f;var h=i.default.Event(N,{relatedTarget:f,direction:r,from:l,to:d});if(i.default(this._element).hasClass("slide")){i.default(f).addClass(o),u.reflow(f),i.default(s).addClass(n),i.default(f).addClass(n);var p=u.getTransitionDurationFromElement(s);i.default(s).one(u.TRANSITION_END,(function(){i.default(f).removeClass(n+" "+o).addClass(T),i.default(s).removeClass("active "+o+" "+n),a._isSliding=!1,setTimeout((function(){return i.default(a._element).trigger(h)}),0)})).emulateTransitionEnd(p)}else i.default(s).removeClass(T),i.default(f).addClass(T),this._isSliding=!1,i.default(this._element).trigger(h);c&&this.cycle()}},t._jQueryInterface=function(e){return this.each((function(){var n=i.default(this).data(E),o=a({},A,i.default(this).data());"object"==typeof e&&(o=a({},o,e));var r="string"==typeof e?e:o.slide;if(n||(n=new t(this,o),i.default(this).data(E,n)),"number"==typeof e)n.to(e);else if("string"==typeof r){if("undefined"==typeof n[r])throw new TypeError('No method named "'+r+'"');n[r]()}else o.interval&&o.ride&&(n.pause(),n.cycle())}))},t._dataApiClickHandler=function(e){var n=u.getSelectorFromElement(this);if(n){var o=i.default(n)[0];if(o&&i.default(o).hasClass("carousel")){var r=a({},i.default(o).data(),i.default(this).data()),s=this.getAttribute("data-slide-to");s&&(r.interval=!1),t._jQueryInterface.call(i.default(o),r),s&&i.default(o).data(E).to(s),e.preventDefault()}}},r(t,null,[{key:"VERSION",get:function(){return"4.6.2"}},{key:"Default",get:function(){return A}}]),t}();i.default(document).on("click.bs.carousel.data-api","[data-slide], [data-slide-to]",O._dataApiClickHandler),i.default(window).on("load.bs.carousel.data-api",(function(){for(var t=[].slice.call(document.querySelectorAll('[data-ride="carousel"]')),e=0,n=t.length;e<n;e++){var o=i.default(t[e]);O._jQueryInterface.call(o,o.data())}})),i.default.fn[y]=O._jQueryInterface,i.default.fn[y].Constructor=O,i.default.fn[y].noConflict=function(){return i.default.fn[y]=w,O._jQueryInterface};var x="collapse",j="bs.collapse",L=i.default.fn[x],P="show",F="collapse",R="collapsing",B="collapsed",H="width",M='[data-toggle="collapse"]',q={toggle:!0,parent:""},Q={toggle:"boolean",parent:"(string|element)"},W=function(){function t(t,e){this._isTransitioning=!1,this._element=t,this._config=this._getConfig(e),this._triggerArray=[].slice.call(document.querySelectorAll('[data-toggle="collapse"][href="#'+t.id+'"],[data-toggle="collapse"][data-target="#'+t.id+'"]'));for(var n=[].slice.call(document.querySelectorAll(M)),i=0,o=n.length;i<o;i++){var r=n[i],a=u.getSelectorFromElement(r),s=[].slice.call(document.querySelectorAll(a)).filter((function(e){return e===t}));null!==a&&s.length>0&&(this._selector=a,this._triggerArray.push(r))}this._parent=this._config.parent?this._getParent():null,this._config.parent||this._addAriaAndCollapsedClass(this._element,this._triggerArray),this._config.toggle&&this.toggle()}var e=t.prototype;return e.toggle=function(){i.default(this._element).hasClass(P)?this.hide():this.show()},e.show=function(){var e,n,o=this;if(!(this._isTransitioning||i.default(this._element).hasClass(P)||(this._parent&&0===(e=[].slice.call(this._parent.querySelectorAll(".show, .collapsing")).filter((function(t){return"string"==typeof o._config.parent?t.getAttribute("data-parent")===o._config.parent:t.classList.contains(F)}))).length&&(e=null),e&&(n=i.default(e).not(this._selector).data(j))&&n._isTransitioning))){var r=i.default.Event("show.bs.collapse");if(i.default(this._element).trigger(r),!r.isDefaultPrevented()){e&&(t._jQueryInterface.call(i.default(e).not(this._selector),"hide"),n||i.default(e).data(j,null));var a=this._getDimension();i.default(this._element).removeClass(F).addClass(R),this._element.style[a]=0,this._triggerArray.length&&i.default(this._triggerArray).removeClass(B).attr("aria-expanded",!0),this.setTransitioning(!0);var s="scroll"+(a[0].toUpperCase()+a.slice(1)),l=u.getTransitionDurationFromElement(this._element);i.default(this._element).one(u.TRANSITION_END,(function(){i.default(o._element).removeClass(R).addClass("collapse show"),o._element.style[a]="",o.setTransitioning(!1),i.default(o._element).trigger("shown.bs.collapse")})).emulateTransitionEnd(l),this._element.style[a]=this._element[s]+"px"}}},e.hide=function(){var t=this;if(!this._isTransitioning&&i.default(this._element).hasClass(P)){var e=i.default.Event("hide.bs.collapse");if(i.default(this._element).trigger(e),!e.isDefaultPrevented()){var n=this._getDimension();this._element.style[n]=this._element.getBoundingClientRect()[n]+"px",u.reflow(this._element),i.default(this._element).addClass(R).removeClass("collapse show");var o=this._triggerArray.length;if(o>0)for(var r=0;r<o;r++){var a=this._triggerArray[r],s=u.getSelectorFromElement(a);null!==s&&(i.default([].slice.call(document.querySelectorAll(s))).hasClass(P)||i.default(a).addClass(B).attr("aria-expanded",!1))}this.setTransitioning(!0),this._element.style[n]="";var l=u.getTransitionDurationFromElement(this._element);i.default(this._element).one(u.TRANSITION_END,(function(){t.setTransitioning(!1),i.default(t._element).removeClass(R).addClass(F).trigger("hidden.bs.collapse")})).emulateTransitionEnd(l)}}},e.setTransitioning=function(t){this._isTransitioning=t},e.dispose=function(){i.default.removeData(this._element,j),this._config=null,this._parent=null,this._element=null,this._triggerArray=null,this._isTransitioning=null},e._getConfig=function(t){return(t=a({},q,t)).toggle=Boolean(t.toggle),u.typeCheckConfig(x,t,Q),t},e._getDimension=function(){return i.default(this._element).hasClass(H)?H:"height"},e._getParent=function(){var e,n=this;u.isElement(this._config.parent)?(e=this._config.parent,"undefined"!=typeof this._config.parent.jquery&&(e=this._config.parent[0])):e=document.querySelector(this._config.parent);var o='[data-toggle="collapse"][data-parent="'+this._config.parent+'"]',r=[].slice.call(e.querySelectorAll(o));return i.default(r).each((function(e,i){n._addAriaAndCollapsedClass(t._getTargetFromElement(i),[i])})),e},e._addAriaAndCollapsedClass=function(t,e){var n=i.default(t).hasClass(P);e.length&&i.default(e).toggleClass(B,!n).attr("aria-expanded",n)},t._getTargetFromElement=function(t){var e=u.getSelectorFromElement(t);return e?document.querySelector(e):null},t._jQueryInterface=function(e){return this.each((function(){var n=i.default(this),o=n.data(j),r=a({},q,n.data(),"object"==typeof e&&e?e:{});if(!o&&r.toggle&&"string"==typeof e&&/show|hide/.test(e)&&(r.toggle=!1),o||(o=new t(this,r),n.data(j,o)),"string"==typeof e){if("undefined"==typeof o[e])throw new TypeError('No method named "'+e+'"');o[e]()}}))},r(t,null,[{key:"VERSION",get:function(){return"4.6.2"}},{key:"Default",get:function(){return q}}]),t}();i.default(document).on("click.bs.collapse.data-api",M,(function(t){"A"===t.currentTarget.tagName&&t.preventDefault();var e=i.default(this),n=u.getSelectorFromElement(this),o=[].slice.call(document.querySelectorAll(n));i.default(o).each((function(){var t=i.default(this),n=t.data(j)?"toggle":e.data();W._jQueryInterface.call(t,n)}))})),i.default.fn[x]=W._jQueryInterface,i.default.fn[x].Constructor=W,i.default.fn[x].noConflict=function(){return i.default.fn[x]=L,W._jQueryInterface};var U="undefined"!=typeof window&&"undefined"!=typeof document&&"undefined"!=typeof navigator,V=function(){for(var t=["Edge","Trident","Firefox"],e=0;e<t.length;e+=1)if(U&&navigator.userAgent.indexOf(t[e])>=0)return 1;return 0}(),Y=U&&window.Promise?function(t){var e=!1;return function(){e||(e=!0,window.Promise.resolve().then((function(){e=!1,t()})))}}:function(t){var e=!1;return function(){e||(e=!0,setTimeout((function(){e=!1,t()}),V))}};function z(t){return t&&"[object Function]"==={}.toString.call(t)}function K(t,e){if(1!==t.nodeType)return[];var n=t.ownerDocument.defaultView.getComputedStyle(t,null);return e?n[e]:n}function X(t){return"HTML"===t.nodeName?t:t.parentNode||t.host}function G(t){if(!t)return document.body;switch(t.nodeName){case"HTML":case"BODY":return t.ownerDocument.body;case"#document":return t.body}var e=K(t),n=e.overflow,i=e.overflowX,o=e.overflowY;return/(auto|scroll|overlay)/.test(n+o+i)?t:G(X(t))}function $(t){return t&&t.referenceNode?t.referenceNode:t}var J=U&&!(!window.MSInputMethodContext||!document.documentMode),Z=U&&/MSIE 10/.test(navigator.userAgent);function tt(t){return 11===t?J:10===t?Z:J||Z}function et(t){if(!t)return document.documentElement;for(var e=tt(10)?document.body:null,n=t.offsetParent||null;n===e&&t.nextElementSibling;)n=(t=t.nextElementSibling).offsetParent;var i=n&&n.nodeName;return i&&"BODY"!==i&&"HTML"!==i?-1!==["TH","TD","TABLE"].indexOf(n.nodeName)&&"static"===K(n,"position")?et(n):n:t?t.ownerDocument.documentElement:document.documentElement}function nt(t){return null!==t.parentNode?nt(t.parentNode):t}function it(t,e){if(!(t&&t.nodeType&&e&&e.nodeType))return document.documentElement;var n=t.compareDocumentPosition(e)&Node.DOCUMENT_POSITION_FOLLOWING,i=n?t:e,o=n?e:t,r=document.createRange();r.setStart(i,0),r.setEnd(o,0);var a,s,l=r.commonAncestorContainer;if(t!==l&&e!==l||i.contains(o))return"BODY"===(s=(a=l).nodeName)||"HTML"!==s&&et(a.firstElementChild)!==a?et(l):l;var u=nt(t);return u.host?it(u.host,e):it(t,nt(e).host)}function ot(t){var e=arguments.length>1&&void 0!==arguments[1]?arguments[1]:"top",n="top"===e?"scrollTop":"scrollLeft",i=t.nodeName;if("BODY"===i||"HTML"===i){var o=t.ownerDocument.documentElement,r=t.ownerDocument.scrollingElement||o;return r[n]}return t[n]}function rt(t,e){var n=arguments.length>2&&void 0!==arguments[2]&&arguments[2],i=ot(e,"top"),o=ot(e,"left"),r=n?-1:1;return t.top+=i*r,t.bottom+=i*r,t.left+=o*r,t.right+=o*r,t}function at(t,e){var n="x"===e?"Left":"Top",i="Left"===n?"Right":"Bottom";return parseFloat(t["border"+n+"Width"])+parseFloat(t["border"+i+"Width"])}function st(t,e,n,i){return Math.max(e["offset"+t],e["scroll"+t],n["client"+t],n["offset"+t],n["scroll"+t],tt(10)?parseInt(n["offset"+t])+parseInt(i["margin"+("Height"===t?"Top":"Left")])+parseInt(i["margin"+("Height"===t?"Bottom":"Right")]):0)}function lt(t){var e=t.body,n=t.documentElement,i=tt(10)&&getComputedStyle(n);return{height:st("Height",e,n,i),width:st("Width",e,n,i)}}var ut=function(t,e){if(!(t instanceof e))throw new TypeError("Cannot call a class as a function")},ft=function(){function t(t,e){for(var n=0;n<e.length;n++){var i=e[n];i.enumerable=i.enumerable||!1,i.configurable=!0,"value"in i&&(i.writable=!0),Object.defineProperty(t,i.key,i)}}return function(e,n,i){return n&&t(e.prototype,n),i&&t(e,i),e}}(),dt=function(t,e,n){return e in t?Object.defineProperty(t,e,{value:n,enumerable:!0,configurable:!0,writable:!0}):t[e]=n,t},ct=Object.assign||function(t){for(var e=1;e<arguments.length;e++){var n=arguments[e];for(var i in n)Object.prototype.hasOwnProperty.call(n,i)&&(t[i]=n[i])}return t};function ht(t){return ct({},t,{right:t.left+t.width,bottom:t.top+t.height})}function pt(t){var e={};try{if(tt(10)){e=t.getBoundingClientRect();var n=ot(t,"top"),i=ot(t,"left");e.top+=n,e.left+=i,e.bottom+=n,e.right+=i}else e=t.getBoundingClientRect()}catch(t){}var o={left:e.left,top:e.top,width:e.right-e.left,height:e.bottom-e.top},r="HTML"===t.nodeName?lt(t.ownerDocument):{},a=r.width||t.clientWidth||o.width,s=r.height||t.clientHeight||o.height,l=t.offsetWidth-a,u=t.offsetHeight-s;if(l||u){var f=K(t);l-=at(f,"x"),u-=at(f,"y"),o.width-=l,o.height-=u}return ht(o)}function mt(t,e){var n=arguments.length>2&&void 0!==arguments[2]&&arguments[2],i=tt(10),o="HTML"===e.nodeName,r=pt(t),a=pt(e),s=G(t),l=K(e),u=parseFloat(l.borderTopWidth),f=parseFloat(l.borderLeftWidth);n&&o&&(a.top=Math.max(a.top,0),a.left=Math.max(a.left,0));var d=ht({top:r.top-a.top-u,left:r.left-a.left-f,width:r.width,height:r.height});if(d.marginTop=0,d.marginLeft=0,!i&&o){var c=parseFloat(l.marginTop),h=parseFloat(l.marginLeft);d.top-=u-c,d.bottom-=u-c,d.left-=f-h,d.right-=f-h,d.marginTop=c,d.marginLeft=h}return(i&&!n?e.contains(s):e===s&&"BODY"!==s.nodeName)&&(d=rt(d,e)),d}function gt(t){var e=arguments.length>1&&void 0!==arguments[1]&&arguments[1],n=t.ownerDocument.documentElement,i=mt(t,n),o=Math.max(n.clientWidth,window.innerWidth||0),r=Math.max(n.clientHeight,window.innerHeight||0),a=e?0:ot(n),s=e?0:ot(n,"left"),l={top:a-i.top+i.marginTop,left:s-i.left+i.marginLeft,width:o,height:r};return ht(l)}function _t(t){var e=t.nodeName;if("BODY"===e||"HTML"===e)return!1;if("fixed"===K(t,"position"))return!0;var n=X(t);return!!n&&_t(n)}function vt(t){if(!t||!t.parentElement||tt())return document.documentElement;for(var e=t.parentElement;e&&"none"===K(e,"transform");)e=e.parentElement;return e||document.documentElement}function bt(t,e,n,i){var o=arguments.length>4&&void 0!==arguments[4]&&arguments[4],r={top:0,left:0},a=o?vt(t):it(t,$(e));if("viewport"===i)r=gt(a,o);else{var s=void 0;"scrollParent"===i?"BODY"===(s=G(X(e))).nodeName&&(s=t.ownerDocument.documentElement):s="window"===i?t.ownerDocument.documentElement:i;var l=mt(s,a,o);if("HTML"!==s.nodeName||_t(a))r=l;else{var u=lt(t.ownerDocument),f=u.height,d=u.width;r.top+=l.top-l.marginTop,r.bottom=f+l.top,r.left+=l.left-l.marginLeft,r.right=d+l.left}}var c="number"==typeof(n=n||0);return r.left+=c?n:n.left||0,r.top+=c?n:n.top||0,r.right-=c?n:n.right||0,r.bottom-=c?n:n.bottom||0,r}function yt(t){return t.width*t.height}function Et(t,e,n,i,o){var r=arguments.length>5&&void 0!==arguments[5]?arguments[5]:0;if(-1===t.indexOf("auto"))return t;var a=bt(n,i,r,o),s={top:{width:a.width,height:e.top-a.top},right:{width:a.right-e.right,height:a.height},bottom:{width:a.width,height:a.bottom-e.bottom},left:{width:e.left-a.left,height:a.height}},l=Object.keys(s).map((function(t){return ct({key:t},s[t],{area:yt(s[t])})})).sort((function(t,e){return e.area-t.area})),u=l.filter((function(t){var e=t.width,i=t.height;return e>=n.clientWidth&&i>=n.clientHeight})),f=u.length>0?u[0].key:l[0].key,d=t.split("-")[1];return f+(d?"-"+d:"")}function wt(t,e,n){var i=arguments.length>3&&void 0!==arguments[3]?arguments[3]:null,o=i?vt(e):it(e,$(n));return mt(n,o,i)}function Tt(t){var e=t.ownerDocument.defaultView.getComputedStyle(t),n=parseFloat(e.marginTop||0)+parseFloat(e.marginBottom||0),i=parseFloat(e.marginLeft||0)+parseFloat(e.marginRight||0);return{width:t.offsetWidth+i,height:t.offsetHeight+n}}function Ct(t){var e={left:"right",right:"left",bottom:"top",top:"bottom"};return t.replace(/left|right|bottom|top/g,(function(t){return e[t]}))}function St(t,e,n){n=n.split("-")[0];var i=Tt(t),o={width:i.width,height:i.height},r=-1!==["right","left"].indexOf(n),a=r?"top":"left",s=r?"left":"top",l=r?"height":"width",u=r?"width":"height";return o[a]=e[a]+e[l]/2-i[l]/2,o[s]=n===s?e[s]-i[u]:e[Ct(s)],o}function Nt(t,e){return Array.prototype.find?t.find(e):t.filter(e)[0]}function Dt(t,e,n){return(void 0===n?t:t.slice(0,function(t,e,n){if(Array.prototype.findIndex)return t.findIndex((function(t){return t.name===n}));var i=Nt(t,(function(t){return t.name===n}));return t.indexOf(i)}(t,0,n))).forEach((function(t){t.function&&console.warn("`modifier.function` is deprecated, use `modifier.fn`!");var n=t.function||t.fn;t.enabled&&z(n)&&(e.offsets.popper=ht(e.offsets.popper),e.offsets.reference=ht(e.offsets.reference),e=n(e,t))})),e}function At(){if(!this.state.isDestroyed){var t={instance:this,styles:{},arrowStyles:{},attributes:{},flipped:!1,offsets:{}};t.offsets.reference=wt(this.state,this.popper,this.reference,this.options.positionFixed),t.placement=Et(this.options.placement,t.offsets.reference,this.popper,this.reference,this.options.modifiers.flip.boundariesElement,this.options.modifiers.flip.padding),t.originalPlacement=t.placement,t.positionFixed=this.options.positionFixed,t.offsets.popper=St(this.popper,t.offsets.reference,t.placement),t.offsets.popper.position=this.options.positionFixed?"fixed":"absolute",t=Dt(this.modifiers,t),this.state.isCreated?this.options.onUpdate(t):(this.state.isCreated=!0,this.options.onCreate(t))}}function kt(t,e){return t.some((function(t){var n=t.name;return t.enabled&&n===e}))}function It(t){for(var e=[!1,"ms","Webkit","Moz","O"],n=t.charAt(0).toUpperCase()+t.slice(1),i=0;i<e.length;i++){var o=e[i],r=o?""+o+n:t;if("undefined"!=typeof document.body.style[r])return r}return null}function Ot(){return this.state.isDestroyed=!0,kt(this.modifiers,"applyStyle")&&(this.popper.removeAttribute("x-placement"),this.popper.style.position="",this.popper.style.top="",this.popper.style.left="",this.popper.style.right="",this.popper.style.bottom="",this.popper.style.willChange="",this.popper.style[It("transform")]=""),this.disableEventListeners(),this.options.removeOnDestroy&&this.popper.parentNode.removeChild(this.popper),this}function xt(t){var e=t.ownerDocument;return e?e.defaultView:window}function jt(t,e,n,i){var o="BODY"===t.nodeName,r=o?t.ownerDocument.defaultView:t;r.addEventListener(e,n,{passive:!0}),o||jt(G(r.parentNode),e,n,i),i.push(r)}function Lt(t,e,n,i){n.updateBound=i,xt(t).addEventListener("resize",n.updateBound,{passive:!0});var o=G(t);return jt(o,"scroll",n.updateBound,n.scrollParents),n.scrollElement=o,n.eventsEnabled=!0,n}function Pt(){this.state.eventsEnabled||(this.state=Lt(this.reference,this.options,this.state,this.scheduleUpdate))}function Ft(){var t,e;this.state.eventsEnabled&&(cancelAnimationFrame(this.scheduleUpdate),this.state=(t=this.reference,e=this.state,xt(t).removeEventListener("resize",e.updateBound),e.scrollParents.forEach((function(t){t.removeEventListener("scroll",e.updateBound)})),e.updateBound=null,e.scrollParents=[],e.scrollElement=null,e.eventsEnabled=!1,e))}function Rt(t){return""!==t&&!isNaN(parseFloat(t))&&isFinite(t)}function Bt(t,e){Object.keys(e).forEach((function(n){var i="";-1!==["width","height","top","right","bottom","left"].indexOf(n)&&Rt(e[n])&&(i="px"),t.style[n]=e[n]+i}))}var Ht=U&&/Firefox/i.test(navigator.userAgent);function Mt(t,e,n){var i=Nt(t,(function(t){return t.name===e})),o=!!i&&t.some((function(t){return t.name===n&&t.enabled&&t.order<i.order}));if(!o){var r="`"+e+"`",a="`"+n+"`";console.warn(a+" modifier is required by "+r+" modifier in order to work, be sure to include it before "+r+"!")}return o}var qt=["auto-start","auto","auto-end","top-start","top","top-end","right-start","right","right-end","bottom-end","bottom","bottom-start","left-end","left","left-start"],Qt=qt.slice(3);function Wt(t){var e=arguments.length>1&&void 0!==arguments[1]&&arguments[1],n=Qt.indexOf(t),i=Qt.slice(n+1).concat(Qt.slice(0,n));return e?i.reverse():i}var Ut={placement:"bottom",positionFixed:!1,eventsEnabled:!0,removeOnDestroy:!1,onCreate:function(){},onUpdate:function(){},modifiers:{shift:{order:100,enabled:!0,fn:function(t){var e=t.placement,n=e.split("-")[0],i=e.split("-")[1];if(i){var o=t.offsets,r=o.reference,a=o.popper,s=-1!==["bottom","top"].indexOf(n),l=s?"left":"top",u=s?"width":"height",f={start:dt({},l,r[l]),end:dt({},l,r[l]+r[u]-a[u])};t.offsets.popper=ct({},a,f[i])}return t}},offset:{order:200,enabled:!0,fn:function(t,e){var n,i=e.offset,o=t.placement,r=t.offsets,a=r.popper,s=r.reference,l=o.split("-")[0];return n=Rt(+i)?[+i,0]:function(t,e,n,i){var o=[0,0],r=-1!==["right","left"].indexOf(i),a=t.split(/(\+|\-)/).map((function(t){return t.trim()})),s=a.indexOf(Nt(a,(function(t){return-1!==t.search(/,|\s/)})));a[s]&&-1===a[s].indexOf(",")&&console.warn("Offsets separated by white space(s) are deprecated, use a comma (,) instead.");var l=/\s*,\s*|\s+/,u=-1!==s?[a.slice(0,s).concat([a[s].split(l)[0]]),[a[s].split(l)[1]].concat(a.slice(s+1))]:[a];return u=u.map((function(t,i){var o=(1===i?!r:r)?"height":"width",a=!1;return t.reduce((function(t,e){return""===t[t.length-1]&&-1!==["+","-"].indexOf(e)?(t[t.length-1]=e,a=!0,t):a?(t[t.length-1]+=e,a=!1,t):t.concat(e)}),[]).map((function(t){return function(t,e,n,i){var o=t.match(/((?:\-|\+)?\d*\.?\d*)(.*)/),r=+o[1],a=o[2];return r?0===a.indexOf("%")?ht("%p"===a?n:i)[e]/100*r:"vh"===a||"vw"===a?("vh"===a?Math.max(document.documentElement.clientHeight,window.innerHeight||0):Math.max(document.documentElement.clientWidth,window.innerWidth||0))/100*r:r:t}(t,o,e,n)}))})),u.forEach((function(t,e){t.forEach((function(n,i){Rt(n)&&(o[e]+=n*("-"===t[i-1]?-1:1))}))})),o}(i,a,s,l),"left"===l?(a.top+=n[0],a.left-=n[1]):"right"===l?(a.top+=n[0],a.left+=n[1]):"top"===l?(a.left+=n[0],a.top-=n[1]):"bottom"===l&&(a.left+=n[0],a.top+=n[1]),t.popper=a,t},offset:0},preventOverflow:{order:300,enabled:!0,fn:function(t,e){var n=e.boundariesElement||et(t.instance.popper);t.instance.reference===n&&(n=et(n));var i=It("transform"),o=t.instance.popper.style,r=o.top,a=o.left,s=o[i];o.top="",o.left="",o[i]="";var l=bt(t.instance.popper,t.instance.reference,e.padding,n,t.positionFixed);o.top=r,o.left=a,o[i]=s,e.boundaries=l;var u=e.priority,f=t.offsets.popper,d={primary:function(t){var n=f[t];return f[t]<l[t]&&!e.escapeWithReference&&(n=Math.max(f[t],l[t])),dt({},t,n)},secondary:function(t){var n="right"===t?"left":"top",i=f[n];return f[t]>l[t]&&!e.escapeWithReference&&(i=Math.min(f[n],l[t]-("right"===t?f.width:f.height))),dt({},n,i)}};return u.forEach((function(t){var e=-1!==["left","top"].indexOf(t)?"primary":"secondary";f=ct({},f,d[e](t))})),t.offsets.popper=f,t},priority:["left","right","top","bottom"],padding:5,boundariesElement:"scrollParent"},keepTogether:{order:400,enabled:!0,fn:function(t){var e=t.offsets,n=e.popper,i=e.reference,o=t.placement.split("-")[0],r=Math.floor,a=-1!==["top","bottom"].indexOf(o),s=a?"right":"bottom",l=a?"left":"top",u=a?"width":"height";return n[s]<r(i[l])&&(t.offsets.popper[l]=r(i[l])-n[u]),n[l]>r(i[s])&&(t.offsets.popper[l]=r(i[s])),t}},arrow:{order:500,enabled:!0,fn:function(t,e){var n;if(!Mt(t.instance.modifiers,"arrow","keepTogether"))return t;var i=e.element;if("string"==typeof i){if(!(i=t.instance.popper.querySelector(i)))return t}else if(!t.instance.popper.contains(i))return console.warn("WARNING: `arrow.element` must be child of its popper element!"),t;var o=t.placement.split("-")[0],r=t.offsets,a=r.popper,s=r.reference,l=-1!==["left","right"].indexOf(o),u=l?"height":"width",f=l?"Top":"Left",d=f.toLowerCase(),c=l?"left":"top",h=l?"bottom":"right",p=Tt(i)[u];s[h]-p<a[d]&&(t.offsets.popper[d]-=a[d]-(s[h]-p)),s[d]+p>a[h]&&(t.offsets.popper[d]+=s[d]+p-a[h]),t.offsets.popper=ht(t.offsets.popper);var m=s[d]+s[u]/2-p/2,g=K(t.instance.popper),_=parseFloat(g["margin"+f]),v=parseFloat(g["border"+f+"Width"]),b=m-t.offsets.popper[d]-_-v;return b=Math.max(Math.min(a[u]-p,b),0),t.arrowElement=i,t.offsets.arrow=(dt(n={},d,Math.round(b)),dt(n,c,""),n),t},element:"[x-arrow]"},flip:{order:600,enabled:!0,fn:function(t,e){if(kt(t.instance.modifiers,"inner"))return t;if(t.flipped&&t.placement===t.originalPlacement)return t;var n=bt(t.instance.popper,t.instance.reference,e.padding,e.boundariesElement,t.positionFixed),i=t.placement.split("-")[0],o=Ct(i),r=t.placement.split("-")[1]||"",a=[];switch(e.behavior){case"flip":a=[i,o];break;case"clockwise":a=Wt(i);break;case"counterclockwise":a=Wt(i,!0);break;default:a=e.behavior}return a.forEach((function(s,l){if(i!==s||a.length===l+1)return t;i=t.placement.split("-")[0],o=Ct(i);var u=t.offsets.popper,f=t.offsets.reference,d=Math.floor,c="left"===i&&d(u.right)>d(f.left)||"right"===i&&d(u.left)<d(f.right)||"top"===i&&d(u.bottom)>d(f.top)||"bottom"===i&&d(u.top)<d(f.bottom),h=d(u.left)<d(n.left),p=d(u.right)>d(n.right),m=d(u.top)<d(n.top),g=d(u.bottom)>d(n.bottom),_="left"===i&&h||"right"===i&&p||"top"===i&&m||"bottom"===i&&g,v=-1!==["top","bottom"].indexOf(i),b=!!e.flipVariations&&(v&&"start"===r&&h||v&&"end"===r&&p||!v&&"start"===r&&m||!v&&"end"===r&&g),y=!!e.flipVariationsByContent&&(v&&"start"===r&&p||v&&"end"===r&&h||!v&&"start"===r&&g||!v&&"end"===r&&m),E=b||y;(c||_||E)&&(t.flipped=!0,(c||_)&&(i=a[l+1]),E&&(r=function(t){return"end"===t?"start":"start"===t?"end":t}(r)),t.placement=i+(r?"-"+r:""),t.offsets.popper=ct({},t.offsets.popper,St(t.instance.popper,t.offsets.reference,t.placement)),t=Dt(t.instance.modifiers,t,"flip"))})),t},behavior:"flip",padding:5,boundariesElement:"viewport",flipVariations:!1,flipVariationsByContent:!1},inner:{order:700,enabled:!1,fn:function(t){var e=t.placement,n=e.split("-")[0],i=t.offsets,o=i.popper,r=i.reference,a=-1!==["left","right"].indexOf(n),s=-1===["top","left"].indexOf(n);return o[a?"left":"top"]=r[n]-(s?o[a?"width":"height"]:0),t.placement=Ct(e),t.offsets.popper=ht(o),t}},hide:{order:800,enabled:!0,fn:function(t){if(!Mt(t.instance.modifiers,"hide","preventOverflow"))return t;var e=t.offsets.reference,n=Nt(t.instance.modifiers,(function(t){return"preventOverflow"===t.name})).boundaries;if(e.bottom<n.top||e.left>n.right||e.top>n.bottom||e.right<n.left){if(!0===t.hide)return t;t.hide=!0,t.attributes["x-out-of-boundaries"]=""}else{if(!1===t.hide)return t;t.hide=!1,t.attributes["x-out-of-boundaries"]=!1}return t}},computeStyle:{order:850,enabled:!0,fn:function(t,e){var n=e.x,i=e.y,o=t.offsets.popper,r=Nt(t.instance.modifiers,(function(t){return"applyStyle"===t.name})).gpuAcceleration;void 0!==r&&console.warn("WARNING: `gpuAcceleration` option moved to `computeStyle` modifier and will not be supported in future versions of Popper.js!");var a,s,l=void 0!==r?r:e.gpuAcceleration,u=et(t.instance.popper),f=pt(u),d={position:o.position},c=function(t,e){var n=t.offsets,i=n.popper,o=n.reference,r=Math.round,a=Math.floor,s=function(t){return t},l=r(o.width),u=r(i.width),f=-1!==["left","right"].indexOf(t.placement),d=-1!==t.placement.indexOf("-"),c=e?f||d||l%2==u%2?r:a:s,h=e?r:s;return{left:c(l%2==1&&u%2==1&&!d&&e?i.left-1:i.left),top:h(i.top),bottom:h(i.bottom),right:c(i.right)}}(t,window.devicePixelRatio<2||!Ht),h="bottom"===n?"top":"bottom",p="right"===i?"left":"right",m=It("transform");if(s="bottom"===h?"HTML"===u.nodeName?-u.clientHeight+c.bottom:-f.height+c.bottom:c.top,a="right"===p?"HTML"===u.nodeName?-u.clientWidth+c.right:-f.width+c.right:c.left,l&&m)d[m]="translate3d("+a+"px, "+s+"px, 0)",d[h]=0,d[p]=0,d.willChange="transform";else{var g="bottom"===h?-1:1,_="right"===p?-1:1;d[h]=s*g,d[p]=a*_,d.willChange=h+", "+p}var v={"x-placement":t.placement};return t.attributes=ct({},v,t.attributes),t.styles=ct({},d,t.styles),t.arrowStyles=ct({},t.offsets.arrow,t.arrowStyles),t},gpuAcceleration:!0,x:"bottom",y:"right"},applyStyle:{order:900,enabled:!0,fn:function(t){var e,n;return Bt(t.instance.popper,t.styles),e=t.instance.popper,n=t.attributes,Object.keys(n).forEach((function(t){!1!==n[t]?e.setAttribute(t,n[t]):e.removeAttribute(t)})),t.arrowElement&&Object.keys(t.arrowStyles).length&&Bt(t.arrowElement,t.arrowStyles),t},onLoad:function(t,e,n,i,o){var r=wt(o,e,t,n.positionFixed),a=Et(n.placement,r,e,t,n.modifiers.flip.boundariesElement,n.modifiers.flip.padding);return e.setAttribute("x-placement",a),Bt(e,{position:n.positionFixed?"fixed":"absolute"}),n},gpuAcceleration:void 0}}},Vt=function(){function t(e,n){var i=this,o=arguments.length>2&&void 0!==arguments[2]?arguments[2]:{};ut(this,t),this.scheduleUpdate=function(){return requestAnimationFrame(i.update)},this.update=Y(this.update.bind(this)),this.options=ct({},t.Defaults,o),this.state={isDestroyed:!1,isCreated:!1,scrollParents:[]},this.reference=e&&e.jquery?e[0]:e,this.popper=n&&n.jquery?n[0]:n,this.options.modifiers={},Object.keys(ct({},t.Defaults.modifiers,o.modifiers)).forEach((function(e){i.options.modifiers[e]=ct({},t.Defaults.modifiers[e]||{},o.modifiers?o.modifiers[e]:{})})),this.modifiers=Object.keys(this.options.modifiers).map((function(t){return ct({name:t},i.options.modifiers[t])})).sort((function(t,e){return t.order-e.order})),this.modifiers.forEach((function(t){t.enabled&&z(t.onLoad)&&t.onLoad(i.reference,i.popper,i.options,t,i.state)})),this.update();var r=this.options.eventsEnabled;r&&this.enableEventListeners(),this.state.eventsEnabled=r}return ft(t,[{key:"update",value:function(){return At.call(this)}},{key:"destroy",value:function(){return Ot.call(this)}},{key:"enableEventListeners",value:function(){return Pt.call(this)}},{key:"disableEventListeners",value:function(){return Ft.call(this)}}]),t}();Vt.Utils=("undefined"!=typeof window?window:global).PopperUtils,Vt.placements=qt,Vt.Defaults=Ut;var Yt=Vt,zt="dropdown",Kt="bs.dropdown",Xt=i.default.fn[zt],Gt=new RegExp("38|40|27"),$t="disabled",Jt="show",Zt="dropdown-menu-right",te="hide.bs.dropdown",ee="hidden.bs.dropdown",ne="click.bs.dropdown.data-api",ie="keydown.bs.dropdown.data-api",oe='[data-toggle="dropdown"]',re=".dropdown-menu",ae={offset:0,flip:!0,boundary:"scrollParent",reference:"toggle",display:"dynamic",popperConfig:null},se={offset:"(number|string|function)",flip:"boolean",boundary:"(string|element)",reference:"(string|element)",display:"string",popperConfig:"(null|object)"},le=function(){function t(t,e){this._element=t,this._popper=null,this._config=this._getConfig(e),this._menu=this._getMenuElement(),this._inNavbar=this._detectNavbar(),this._addEventListeners()}var e=t.prototype;return e.toggle=function(){if(!this._element.disabled&&!i.default(this._element).hasClass($t)){var e=i.default(this._menu).hasClass(Jt);t._clearMenus(),e||this.show(!0)}},e.show=function(e){if(void 0===e&&(e=!1),!(this._element.disabled||i.default(this._element).hasClass($t)||i.default(this._menu).hasClass(Jt))){var n={relatedTarget:this._element},o=i.default.Event("show.bs.dropdown",n),r=t._getParentFromElement(this._element);if(i.default(r).trigger(o),!o.isDefaultPrevented()){if(!this._inNavbar&&e){if("undefined"==typeof Yt)throw new TypeError("Bootstrap's dropdowns require Popper (https://popper.js.org)");var a=this._element;"parent"===this._config.reference?a=r:u.isElement(this._config.reference)&&(a=this._config.reference,"undefined"!=typeof this._config.reference.jquery&&(a=this._config.reference[0])),"scrollParent"!==this._config.boundary&&i.default(r).addClass("position-static"),this._popper=new Yt(a,this._menu,this._getPopperConfig())}"ontouchstart"in document.documentElement&&0===i.default(r).closest(".navbar-nav").length&&i.default(document.body).children().on("mouseover",null,i.default.noop),this._element.focus(),this._element.setAttribute("aria-expanded",!0),i.default(this._menu).toggleClass(Jt),i.default(r).toggleClass(Jt).trigger(i.default.Event("shown.bs.dropdown",n))}}},e.hide=function(){if(!this._element.disabled&&!i.default(this._element).hasClass($t)&&i.default(this._menu).hasClass(Jt)){var e={relatedTarget:this._element},n=i.default.Event(te,e),o=t._getParentFromElement(this._element);i.default(o).trigger(n),n.isDefaultPrevented()||(this._popper&&this._popper.destroy(),i.default(this._menu).toggleClass(Jt),i.default(o).toggleClass(Jt).trigger(i.default.Event(ee,e)))}},e.dispose=function(){i.default.removeData(this._element,Kt),i.default(this._element).off(".bs.dropdown"),this._element=null,this._menu=null,null!==this._popper&&(this._popper.destroy(),this._popper=null)},e.update=function(){this._inNavbar=this._detectNavbar(),null!==this._popper&&this._popper.scheduleUpdate()},e._addEventListeners=function(){var t=this;i.default(this._element).on("click.bs.dropdown",(function(e){e.preventDefault(),e.stopPropagation(),t.toggle()}))},e._getConfig=function(t){return t=a({},this.constructor.Default,i.default(this._element).data(),t),u.typeCheckConfig(zt,t,this.constructor.DefaultType),t},e._getMenuElement=function(){if(!this._menu){var e=t._getParentFromElement(this._element);e&&(this._menu=e.querySelector(re))}return this._menu},e._getPlacement=function(){var t=i.default(this._element.parentNode),e="bottom-start";return t.hasClass("dropup")?e=i.default(this._menu).hasClass(Zt)?"top-end":"top-start":t.hasClass("dropright")?e="right-start":t.hasClass("dropleft")?e="left-start":i.default(this._menu).hasClass(Zt)&&(e="bottom-end"),e},e._detectNavbar=function(){return i.default(this._element).closest(".navbar").length>0},e._getOffset=function(){var t=this,e={};return"function"==typeof this._config.offset?e.fn=function(e){return e.offsets=a({},e.offsets,t._config.offset(e.offsets,t._element)),e}:e.offset=this._config.offset,e},e._getPopperConfig=function(){var t={placement:this._getPlacement(),modifiers:{offset:this._getOffset(),flip:{enabled:this._config.flip},preventOverflow:{boundariesElement:this._config.boundary}}};return"static"===this._config.display&&(t.modifiers.applyStyle={enabled:!1}),a({},t,this._config.popperConfig)},t._jQueryInterface=function(e){return this.each((function(){var n=i.default(this).data(Kt);if(n||(n=new t(this,"object"==typeof e?e:null),i.default(this).data(Kt,n)),"string"==typeof e){if("undefined"==typeof n[e])throw new TypeError('No method named "'+e+'"');n[e]()}}))},t._clearMenus=function(e){if(!e||3!==e.which&&("keyup"!==e.type||9===e.which))for(var n=[].slice.call(document.querySelectorAll(oe)),o=0,r=n.length;o<r;o++){var a=t._getParentFromElement(n[o]),s=i.default(n[o]).data(Kt),l={relatedTarget:n[o]};if(e&&"click"===e.type&&(l.clickEvent=e),s){var u=s._menu;if(i.default(a).hasClass(Jt)&&!(e&&("click"===e.type&&/input|textarea/i.test(e.target.tagName)||"keyup"===e.type&&9===e.which)&&i.default.contains(a,e.target))){var f=i.default.Event(te,l);i.default(a).trigger(f),f.isDefaultPrevented()||("ontouchstart"in document.documentElement&&i.default(document.body).children().off("mouseover",null,i.default.noop),n[o].setAttribute("aria-expanded","false"),s._popper&&s._popper.destroy(),i.default(u).removeClass(Jt),i.default(a).removeClass(Jt).trigger(i.default.Event(ee,l)))}}}},t._getParentFromElement=function(t){var e,n=u.getSelectorFromElement(t);return n&&(e=document.querySelector(n)),e||t.parentNode},t._dataApiKeydownHandler=function(e){if(!(/input|textarea/i.test(e.target.tagName)?32===e.which||27!==e.which&&(40!==e.which&&38!==e.which||i.default(e.target).closest(re).length):!Gt.test(e.which))&&!this.disabled&&!i.default(this).hasClass($t)){var n=t._getParentFromElement(this),o=i.default(n).hasClass(Jt);if(o||27!==e.which){if(e.preventDefault(),e.stopPropagation(),!o||27===e.which||32===e.which)return 27===e.which&&i.default(n.querySelector(oe)).trigger("focus"),void i.default(this).trigger("click");var r=[].slice.call(n.querySelectorAll(".dropdown-menu .dropdown-item:not(.disabled):not(:disabled)")).filter((function(t){return i.default(t).is(":visible")}));if(0!==r.length){var a=r.indexOf(e.target);38===e.which&&a>0&&a--,40===e.which&&a<r.length-1&&a++,a<0&&(a=0),r[a].focus()}}}},r(t,null,[{key:"VERSION",get:function(){return"4.6.2"}},{key:"Default",get:function(){return ae}},{key:"DefaultType",get:function(){return se}}]),t}();i.default(document).on(ie,oe,le._dataApiKeydownHandler).on(ie,re,le._dataApiKeydownHandler).on(ne+" keyup.bs.dropdown.data-api",le._clearMenus).on(ne,oe,(function(t){t.preventDefault(),t.stopPropagation(),le._jQueryInterface.call(i.default(this),"toggle")})).on(ne,".dropdown form",(function(t){t.stopPropagation()})),i.default.fn[zt]=le._jQueryInterface,i.default.fn[zt].Constructor=le,i.default.fn[zt].noConflict=function(){return i.default.fn[zt]=Xt,le._jQueryInterface};var ue="bs.modal",fe=i.default.fn.modal,de="modal-open",ce="fade",he="show",pe="modal-static",me="hidden.bs.modal",ge="show.bs.modal",_e="focusin.bs.modal",ve="resize.bs.modal",be="click.dismiss.bs.modal",ye="keydown.dismiss.bs.modal",Ee="mousedown.dismiss.bs.modal",we=".fixed-top, .fixed-bottom, .is-fixed, .sticky-top",Te={backdrop:!0,keyboard:!0,focus:!0,show:!0},Ce={backdrop:"(boolean|string)",keyboard:"boolean",focus:"boolean",show:"boolean"},Se=function(){function t(t,e){this._config=this._getConfig(e),this._element=t,this._dialog=t.querySelector(".modal-dialog"),this._backdrop=null,this._isShown=!1,this._isBodyOverflowing=!1,this._ignoreBackdropClick=!1,this._isTransitioning=!1,this._scrollbarWidth=0}var e=t.prototype;return e.toggle=function(t){return this._isShown?this.hide():this.show(t)},e.show=function(t){var e=this;if(!this._isShown&&!this._isTransitioning){var n=i.default.Event(ge,{relatedTarget:t});i.default(this._element).trigger(n),n.isDefaultPrevented()||(this._isShown=!0,i.default(this._element).hasClass(ce)&&(this._isTransitioning=!0),this._checkScrollbar(),this._setScrollbar(),this._adjustDialog(),this._setEscapeEvent(),this._setResizeEvent(),i.default(this._element).on(be,'[data-dismiss="modal"]',(function(t){return e.hide(t)})),i.default(this._dialog).on(Ee,(function(){i.default(e._element).one("mouseup.dismiss.bs.modal",(function(t){i.default(t.target).is(e._element)&&(e._ignoreBackdropClick=!0)}))})),this._showBackdrop((function(){return e._showElement(t)})))}},e.hide=function(t){var e=this;if(t&&t.preventDefault(),this._isShown&&!this._isTransitioning){var n=i.default.Event("hide.bs.modal");if(i.default(this._element).trigger(n),this._isShown&&!n.isDefaultPrevented()){this._isShown=!1;var o=i.default(this._element).hasClass(ce);if(o&&(this._isTransitioning=!0),this._setEscapeEvent(),this._setResizeEvent(),i.default(document).off(_e),i.default(this._element).removeClass(he),i.default(this._element).off(be),i.default(this._dialog).off(Ee),o){var r=u.getTransitionDurationFromElement(this._element);i.default(this._element).one(u.TRANSITION_END,(function(t){return e._hideModal(t)})).emulateTransitionEnd(r)}else this._hideModal()}}},e.dispose=function(){[window,this._element,this._dialog].forEach((function(t){return i.default(t).off(".bs.modal")})),i.default(document).off(_e),i.default.removeData(this._element,ue),this._config=null,this._element=null,this._dialog=null,this._backdrop=null,this._isShown=null,this._isBodyOverflowing=null,this._ignoreBackdropClick=null,this._isTransitioning=null,this._scrollbarWidth=null},e.handleUpdate=function(){this._adjustDialog()},e._getConfig=function(t){return t=a({},Te,t),u.typeCheckConfig("modal",t,Ce),t},e._triggerBackdropTransition=function(){var t=this,e=i.default.Event("hidePrevented.bs.modal");if(i.default(this._element).trigger(e),!e.isDefaultPrevented()){var n=this._element.scrollHeight>document.documentElement.clientHeight;n||(this._element.style.overflowY="hidden"),this._element.classList.add(pe);var o=u.getTransitionDurationFromElement(this._dialog);i.default(this._element).off(u.TRANSITION_END),i.default(this._element).one(u.TRANSITION_END,(function(){t._element.classList.remove(pe),n||i.default(t._element).one(u.TRANSITION_END,(function(){t._element.style.overflowY=""})).emulateTransitionEnd(t._element,o)})).emulateTransitionEnd(o),this._element.focus()}},e._showElement=function(t){var e=this,n=i.default(this._element).hasClass(ce),o=this._dialog?this._dialog.querySelector(".modal-body"):null;this._element.parentNode&&this._element.parentNode.nodeType===Node.ELEMENT_NODE||document.body.appendChild(this._element),this._element.style.display="block",this._element.removeAttribute("aria-hidden"),this._element.setAttribute("aria-modal",!0),this._element.setAttribute("role","dialog"),i.default(this._dialog).hasClass("modal-dialog-scrollable")&&o?o.scrollTop=0:this._element.scrollTop=0,n&&u.reflow(this._element),i.default(this._element).addClass(he),this._config.focus&&this._enforceFocus();var r=i.default.Event("shown.bs.modal",{relatedTarget:t}),a=function(){e._config.focus&&e._element.focus(),e._isTransitioning=!1,i.default(e._element).trigger(r)};if(n){var s=u.getTransitionDurationFromElement(this._dialog);i.default(this._dialog).one(u.TRANSITION_END,a).emulateTransitionEnd(s)}else a()},e._enforceFocus=function(){var t=this;i.default(document).off(_e).on(_e,(function(e){document!==e.target&&t._element!==e.target&&0===i.default(t._element).has(e.target).length&&t._element.focus()}))},e._setEscapeEvent=function(){var t=this;this._isShown?i.default(this._element).on(ye,(function(e){t._config.keyboard&&27===e.which?(e.preventDefault(),t.hide()):t._config.keyboard||27!==e.which||t._triggerBackdropTransition()})):this._isShown||i.default(this._element).off(ye)},e._setResizeEvent=function(){var t=this;this._isShown?i.default(window).on(ve,(function(e){return t.handleUpdate(e)})):i.default(window).off(ve)},e._hideModal=function(){var t=this;this._element.style.display="none",this._element.setAttribute("aria-hidden",!0),this._element.removeAttribute("aria-modal"),this._element.removeAttribute("role"),this._isTransitioning=!1,this._showBackdrop((function(){i.default(document.body).removeClass(de),t._resetAdjustments(),t._resetScrollbar(),i.default(t._element).trigger(me)}))},e._removeBackdrop=function(){this._backdrop&&(i.default(this._backdrop).remove(),this._backdrop=null)},e._showBackdrop=function(t){var e=this,n=i.default(this._element).hasClass(ce)?ce:"";if(this._isShown&&this._config.backdrop){if(this._backdrop=document.createElement("div"),this._backdrop.className="modal-backdrop",n&&this._backdrop.classList.add(n),i.default(this._backdrop).appendTo(document.body),i.default(this._element).on(be,(function(t){e._ignoreBackdropClick?e._ignoreBackdropClick=!1:t.target===t.currentTarget&&("static"===e._config.backdrop?e._triggerBackdropTransition():e.hide())})),n&&u.reflow(this._backdrop),i.default(this._backdrop).addClass(he),!t)return;if(!n)return void t();var o=u.getTransitionDurationFromElement(this._backdrop);i.default(this._backdrop).one(u.TRANSITION_END,t).emulateTransitionEnd(o)}else if(!this._isShown&&this._backdrop){i.default(this._backdrop).removeClass(he);var r=function(){e._removeBackdrop(),t&&t()};if(i.default(this._element).hasClass(ce)){var a=u.getTransitionDurationFromElement(this._backdrop);i.default(this._backdrop).one(u.TRANSITION_END,r).emulateTransitionEnd(a)}else r()}else t&&t()},e._adjustDialog=function(){var t=this._element.scrollHeight>document.documentElement.clientHeight;!this._isBodyOverflowing&&t&&(this._element.style.paddingLeft=this._scrollbarWidth+"px"),this._isBodyOverflowing&&!t&&(this._element.style.paddingRight=this._scrollbarWidth+"px")},e._resetAdjustments=function(){this._element.style.paddingLeft="",this._element.style.paddingRight=""},e._checkScrollbar=function(){var t=document.body.getBoundingClientRect();this._isBodyOverflowing=Math.round(t.left+t.right)<window.innerWidth,this._scrollbarWidth=this._getScrollbarWidth()},e._setScrollbar=function(){var t=this;if(this._isBodyOverflowing){var e=[].slice.call(document.querySelectorAll(we)),n=[].slice.call(document.querySelectorAll(".sticky-top"));i.default(e).each((function(e,n){var o=n.style.paddingRight,r=i.default(n).css("padding-right");i.default(n).data("padding-right",o).css("padding-right",parseFloat(r)+t._scrollbarWidth+"px")})),i.default(n).each((function(e,n){var o=n.style.marginRight,r=i.default(n).css("margin-right");i.default(n).data("margin-right",o).css("margin-right",parseFloat(r)-t._scrollbarWidth+"px")}));var o=document.body.style.paddingRight,r=i.default(document.body).css("padding-right");i.default(document.body).data("padding-right",o).css("padding-right",parseFloat(r)+this._scrollbarWidth+"px")}i.default(document.body).addClass(de)},e._resetScrollbar=function(){var t=[].slice.call(document.querySelectorAll(we));i.default(t).each((function(t,e){var n=i.default(e).data("padding-right");i.default(e).removeData("padding-right"),e.style.paddingRight=n||""}));var e=[].slice.call(document.querySelectorAll(".sticky-top"));i.default(e).each((function(t,e){var n=i.default(e).data("margin-right");"undefined"!=typeof n&&i.default(e).css("margin-right",n).removeData("margin-right")}));var n=i.default(document.body).data("padding-right");i.default(document.body).removeData("padding-right"),document.body.style.paddingRight=n||""},e._getScrollbarWidth=function(){var t=document.createElement("div");t.className="modal-scrollbar-measure",document.body.appendChild(t);var e=t.getBoundingClientRect().width-t.clientWidth;return document.body.removeChild(t),e},t._jQueryInterface=function(e,n){return this.each((function(){var o=i.default(this).data(ue),r=a({},Te,i.default(this).data(),"object"==typeof e&&e?e:{});if(o||(o=new t(this,r),i.default(this).data(ue,o)),"string"==typeof e){if("undefined"==typeof o[e])throw new TypeError('No method named "'+e+'"');o[e](n)}else r.show&&o.show(n)}))},r(t,null,[{key:"VERSION",get:function(){return"4.6.2"}},{key:"Default",get:function(){return Te}}]),t}();i.default(document).on("click.bs.modal.data-api",'[data-toggle="modal"]',(function(t){var e,n=this,o=u.getSelectorFromElement(this);o&&(e=document.querySelector(o));var r=i.default(e).data(ue)?"toggle":a({},i.default(e).data(),i.default(this).data());"A"!==this.tagName&&"AREA"!==this.tagName||t.preventDefault();var s=i.default(e).one(ge,(function(t){t.isDefaultPrevented()||s.one(me,(function(){i.default(n).is(":visible")&&n.focus()}))}));Se._jQueryInterface.call(i.default(e),r,this)})),i.default.fn.modal=Se._jQueryInterface,i.default.fn.modal.Constructor=Se,i.default.fn.modal.noConflict=function(){return i.default.fn.modal=fe,Se._jQueryInterface};var Ne=["background","cite","href","itemtype","longdesc","poster","src","xlink:href"],De=/^(?:(?:https?|mailto|ftp|tel|file|sms):|[^#&/:?]*(?:[#/?]|$))/i,Ae=/^data:(?:image\/(?:bmp|gif|jpeg|jpg|png|tiff|webp)|video\/(?:mpeg|mp4|ogg|webm)|audio\/(?:mp3|oga|ogg|opus));base64,[\d+/a-z]+=*$/i;function ke(t,e,n){if(0===t.length)return t;if(n&&"function"==typeof n)return n(t);for(var i=(new window.DOMParser).parseFromString(t,"text/html"),o=Object.keys(e),r=[].slice.call(i.body.querySelectorAll("*")),a=function(t,n){var i=r[t],a=i.nodeName.toLowerCase();if(-1===o.indexOf(i.nodeName.toLowerCase()))return i.parentNode.removeChild(i),"continue";var s=[].slice.call(i.attributes),l=[].concat(e["*"]||[],e[a]||[]);s.forEach((function(t){(function(t,e){var n=t.nodeName.toLowerCase();if(-1!==e.indexOf(n))return-1===Ne.indexOf(n)||Boolean(De.test(t.nodeValue)||Ae.test(t.nodeValue));for(var i=e.filter((function(t){return t instanceof RegExp})),o=0,r=i.length;o<r;o++)if(i[o].test(n))return!0;return!1})(t,l)||i.removeAttribute(t.nodeName)}))},s=0,l=r.length;s<l;s++)a(s);return i.body.innerHTML}var Ie="tooltip",Oe="bs.tooltip",xe=i.default.fn.tooltip,je=new RegExp("(^|\\s)bs-tooltip\\S+","g"),Le=["sanitize","whiteList","sanitizeFn"],Pe="fade",Fe="show",Re="show",Be="out",He="hover",Me="focus",qe={AUTO:"auto",TOP:"top",RIGHT:"right",BOTTOM:"bottom",LEFT:"left"},Qe={animation:!0,template:'<div class="tooltip" role="tooltip"><div class="arrow"></div><div class="tooltip-inner"></div></div>',trigger:"hover focus",title:"",delay:0,html:!1,selector:!1,placement:"top",offset:0,container:!1,fallbackPlacement:"flip",boundary:"scrollParent",customClass:"",sanitize:!0,sanitizeFn:null,whiteList:{"*":["class","dir","id","lang","role",/^aria-[\w-]*$/i],a:["target","href","title","rel"],area:[],b:[],br:[],col:[],code:[],div:[],em:[],hr:[],h1:[],h2:[],h3:[],h4:[],h5:[],h6:[],i:[],img:["src","srcset","alt","title","width","height"],li:[],ol:[],p:[],pre:[],s:[],small:[],span:[],sub:[],sup:[],strong:[],u:[],ul:[]},popperConfig:null},We={animation:"boolean",template:"string",title:"(string|element|function)",trigger:"string",delay:"(number|object)",html:"boolean",selector:"(string|boolean)",placement:"(string|function)",offset:"(number|string|function)",container:"(string|element|boolean)",fallbackPlacement:"(string|array)",boundary:"(string|element)",customClass:"(string|function)",sanitize:"boolean",sanitizeFn:"(null|function)",whiteList:"object",popperConfig:"(null|object)"},Ue={HIDE:"hide.bs.tooltip",HIDDEN:"hidden.bs.tooltip",SHOW:"show.bs.tooltip",SHOWN:"shown.bs.tooltip",INSERTED:"inserted.bs.tooltip",CLICK:"click.bs.tooltip",FOCUSIN:"focusin.bs.tooltip",FOCUSOUT:"focusout.bs.tooltip",MOUSEENTER:"mouseenter.bs.tooltip",MOUSELEAVE:"mouseleave.bs.tooltip"},Ve=function(){function t(t,e){if("undefined"==typeof Yt)throw new TypeError("Bootstrap's tooltips require Popper (https://popper.js.org)");this._isEnabled=!0,this._timeout=0,this._hoverState="",this._activeTrigger={},this._popper=null,this.element=t,this.config=this._getConfig(e),this.tip=null,this._setListeners()}var e=t.prototype;return e.enable=function(){this._isEnabled=!0},e.disable=function(){this._isEnabled=!1},e.toggleEnabled=function(){this._isEnabled=!this._isEnabled},e.toggle=function(t){if(this._isEnabled)if(t){var e=this.constructor.DATA_KEY,n=i.default(t.currentTarget).data(e);n||(n=new this.constructor(t.currentTarget,this._getDelegateConfig()),i.default(t.currentTarget).data(e,n)),n._activeTrigger.click=!n._activeTrigger.click,n._isWithActiveTrigger()?n._enter(null,n):n._leave(null,n)}else{if(i.default(this.getTipElement()).hasClass(Fe))return void this._leave(null,this);this._enter(null,this)}},e.dispose=function(){clearTimeout(this._timeout),i.default.removeData(this.element,this.constructor.DATA_KEY),i.default(this.element).off(this.constructor.EVENT_KEY),i.default(this.element).closest(".modal").off("hide.bs.modal",this._hideModalHandler),this.tip&&i.default(this.tip).remove(),this._isEnabled=null,this._timeout=null,this._hoverState=null,this._activeTrigger=null,this._popper&&this._popper.destroy(),this._popper=null,this.element=null,this.config=null,this.tip=null},e.show=function(){var t=this;if("none"===i.default(this.element).css("display"))throw new Error("Please use show on visible elements");var e=i.default.Event(this.constructor.Event.SHOW);if(this.isWithContent()&&this._isEnabled){i.default(this.element).trigger(e);var n=u.findShadowRoot(this.element),o=i.default.contains(null!==n?n:this.element.ownerDocument.documentElement,this.element);if(e.isDefaultPrevented()||!o)return;var r=this.getTipElement(),a=u.getUID(this.constructor.NAME);r.setAttribute("id",a),this.element.setAttribute("aria-describedby",a),this.setContent(),this.config.animation&&i.default(r).addClass(Pe);var s="function"==typeof this.config.placement?this.config.placement.call(this,r,this.element):this.config.placement,l=this._getAttachment(s);this.addAttachmentClass(l);var f=this._getContainer();i.default(r).data(this.constructor.DATA_KEY,this),i.default.contains(this.element.ownerDocument.documentElement,this.tip)||i.default(r).appendTo(f),i.default(this.element).trigger(this.constructor.Event.INSERTED),this._popper=new Yt(this.element,r,this._getPopperConfig(l)),i.default(r).addClass(Fe),i.default(r).addClass(this.config.customClass),"ontouchstart"in document.documentElement&&i.default(document.body).children().on("mouseover",null,i.default.noop);var d=function(){t.config.animation&&t._fixTransition();var e=t._hoverState;t._hoverState=null,i.default(t.element).trigger(t.constructor.Event.SHOWN),e===Be&&t._leave(null,t)};if(i.default(this.tip).hasClass(Pe)){var c=u.getTransitionDurationFromElement(this.tip);i.default(this.tip).one(u.TRANSITION_END,d).emulateTransitionEnd(c)}else d()}},e.hide=function(t){var e=this,n=this.getTipElement(),o=i.default.Event(this.constructor.Event.HIDE),r=function(){e._hoverState!==Re&&n.parentNode&&n.parentNode.removeChild(n),e._cleanTipClass(),e.element.removeAttribute("aria-describedby"),i.default(e.element).trigger(e.constructor.Event.HIDDEN),null!==e._popper&&e._popper.destroy(),t&&t()};if(i.default(this.element).trigger(o),!o.isDefaultPrevented()){if(i.default(n).removeClass(Fe),"ontouchstart"in document.documentElement&&i.default(document.body).children().off("mouseover",null,i.default.noop),this._activeTrigger.click=!1,this._activeTrigger.focus=!1,this._activeTrigger.hover=!1,i.default(this.tip).hasClass(Pe)){var a=u.getTransitionDurationFromElement(n);i.default(n).one(u.TRANSITION_END,r).emulateTransitionEnd(a)}else r();this._hoverState=""}},e.update=function(){null!==this._popper&&this._popper.scheduleUpdate()},e.isWithContent=function(){return Boolean(this.getTitle())},e.addAttachmentClass=function(t){i.default(this.getTipElement()).addClass("bs-tooltip-"+t)},e.getTipElement=function(){return this.tip=this.tip||i.default(this.config.template)[0],this.tip},e.setContent=function(){var t=this.getTipElement();this.setElementContent(i.default(t.querySelectorAll(".tooltip-inner")),this.getTitle()),i.default(t).removeClass("fade show")},e.setElementContent=function(t,e){"object"!=typeof e||!e.nodeType&&!e.jquery?this.config.html?(this.config.sanitize&&(e=ke(e,this.config.whiteList,this.config.sanitizeFn)),t.html(e)):t.text(e):this.config.html?i.default(e).parent().is(t)||t.empty().append(e):t.text(i.default(e).text())},e.getTitle=function(){var t=this.element.getAttribute("data-original-title");return t||(t="function"==typeof this.config.title?this.config.title.call(this.element):this.config.title),t},e._getPopperConfig=function(t){var e=this;return a({},{placement:t,modifiers:{offset:this._getOffset(),flip:{behavior:this.config.fallbackPlacement},arrow:{element:".arrow"},preventOverflow:{boundariesElement:this.config.boundary}},onCreate:function(t){t.originalPlacement!==t.placement&&e._handlePopperPlacementChange(t)},onUpdate:function(t){return e._handlePopperPlacementChange(t)}},this.config.popperConfig)},e._getOffset=function(){var t=this,e={};return"function"==typeof this.config.offset?e.fn=function(e){return e.offsets=a({},e.offsets,t.config.offset(e.offsets,t.element)),e}:e.offset=this.config.offset,e},e._getContainer=function(){return!1===this.config.container?document.body:u.isElement(this.config.container)?i.default(this.config.container):i.default(document).find(this.config.container)},e._getAttachment=function(t){return qe[t.toUpperCase()]},e._setListeners=function(){var t=this;this.config.trigger.split(" ").forEach((function(e){if("click"===e)i.default(t.element).on(t.constructor.Event.CLICK,t.config.selector,(function(e){return t.toggle(e)}));else if("manual"!==e){var n=e===He?t.constructor.Event.MOUSEENTER:t.constructor.Event.FOCUSIN,o=e===He?t.constructor.Event.MOUSELEAVE:t.constructor.Event.FOCUSOUT;i.default(t.element).on(n,t.config.selector,(function(e){return t._enter(e)})).on(o,t.config.selector,(function(e){return t._leave(e)}))}})),this._hideModalHandler=function(){t.element&&t.hide()},i.default(this.element).closest(".modal").on("hide.bs.modal",this._hideModalHandler),this.config.selector?this.config=a({},this.config,{trigger:"manual",selector:""}):this._fixTitle()},e._fixTitle=function(){var t=typeof this.element.getAttribute("data-original-title");(this.element.getAttribute("title")||"string"!==t)&&(this.element.setAttribute("data-original-title",this.element.getAttribute("title")||""),this.element.setAttribute("title",""))},e._enter=function(t,e){var n=this.constructor.DATA_KEY;(e=e||i.default(t.currentTarget).data(n))||(e=new this.constructor(t.currentTarget,this._getDelegateConfig()),i.default(t.currentTarget).data(n,e)),t&&(e._activeTrigger["focusin"===t.type?Me:He]=!0),i.default(e.getTipElement()).hasClass(Fe)||e._hoverState===Re?e._hoverState=Re:(clearTimeout(e._timeout),e._hoverState=Re,e.config.delay&&e.config.delay.show?e._timeout=setTimeout((function(){e._hoverState===Re&&e.show()}),e.config.delay.show):e.show())},e._leave=function(t,e){var n=this.constructor.DATA_KEY;(e=e||i.default(t.currentTarget).data(n))||(e=new this.constructor(t.currentTarget,this._getDelegateConfig()),i.default(t.currentTarget).data(n,e)),t&&(e._activeTrigger["focusout"===t.type?Me:He]=!1),e._isWithActiveTrigger()||(clearTimeout(e._timeout),e._hoverState=Be,e.config.delay&&e.config.delay.hide?e._timeout=setTimeout((function(){e._hoverState===Be&&e.hide()}),e.config.delay.hide):e.hide())},e._isWithActiveTrigger=function(){for(var t in this._activeTrigger)if(this._activeTrigger[t])return!0;return!1},e._getConfig=function(t){var e=i.default(this.element).data();return Object.keys(e).forEach((function(t){-1!==Le.indexOf(t)&&delete e[t]})),"number"==typeof(t=a({},this.constructor.Default,e,"object"==typeof t&&t?t:{})).delay&&(t.delay={show:t.delay,hide:t.delay}),"number"==typeof t.title&&(t.title=t.title.toString()),"number"==typeof t.content&&(t.content=t.content.toString()),u.typeCheckConfig(Ie,t,this.constructor.DefaultType),t.sanitize&&(t.template=ke(t.template,t.whiteList,t.sanitizeFn)),t},e._getDelegateConfig=function(){var t={};if(this.config)for(var e in this.config)this.constructor.Default[e]!==this.config[e]&&(t[e]=this.config[e]);return t},e._cleanTipClass=function(){var t=i.default(this.getTipElement()),e=t.attr("class").match(je);null!==e&&e.length&&t.removeClass(e.join(""))},e._handlePopperPlacementChange=function(t){this.tip=t.instance.popper,this._cleanTipClass(),this.addAttachmentClass(this._getAttachment(t.placement))},e._fixTransition=function(){var t=this.getTipElement(),e=this.config.animation;null===t.getAttribute("x-placement")&&(i.default(t).removeClass(Pe),this.config.animation=!1,this.hide(),this.show(),this.config.animation=e)},t._jQueryInterface=function(e){return this.each((function(){var n=i.default(this),o=n.data(Oe),r="object"==typeof e&&e;if((o||!/dispose|hide/.test(e))&&(o||(o=new t(this,r),n.data(Oe,o)),"string"==typeof e)){if("undefined"==typeof o[e])throw new TypeError('No method named "'+e+'"');o[e]()}}))},r(t,null,[{key:"VERSION",get:function(){return"4.6.2"}},{key:"Default",get:function(){return Qe}},{key:"NAME",get:function(){return Ie}},{key:"DATA_KEY",get:function(){return Oe}},{key:"Event",get:function(){return Ue}},{key:"EVENT_KEY",get:function(){return".bs.tooltip"}},{key:"DefaultType",get:function(){return We}}]),t}();i.default.fn.tooltip=Ve._jQueryInterface,i.default.fn.tooltip.Constructor=Ve,i.default.fn.tooltip.noConflict=function(){return i.default.fn.tooltip=xe,Ve._jQueryInterface};var Ye="bs.popover",ze=i.default.fn.popover,Ke=new RegExp("(^|\\s)bs-popover\\S+","g"),Xe=a({},Ve.Default,{placement:"right",trigger:"click",content:"",template:'<div class="popover" role="tooltip"><div class="arrow"></div><h3 class="popover-header"></h3><div class="popover-body"></div></div>'}),Ge=a({},Ve.DefaultType,{content:"(string|element|function)"}),$e={HIDE:"hide.bs.popover",HIDDEN:"hidden.bs.popover",SHOW:"show.bs.popover",SHOWN:"shown.bs.popover",INSERTED:"inserted.bs.popover",CLICK:"click.bs.popover",FOCUSIN:"focusin.bs.popover",FOCUSOUT:"focusout.bs.popover",MOUSEENTER:"mouseenter.bs.popover",MOUSELEAVE:"mouseleave.bs.popover"},Je=function(t){var e,n;function o(){return t.apply(this,arguments)||this}n=t,(e=o).prototype=Object.create(n.prototype),e.prototype.constructor=e,s(e,n);var a=o.prototype;return a.isWithContent=function(){return this.getTitle()||this._getContent()},a.addAttachmentClass=function(t){i.default(this.getTipElement()).addClass("bs-popover-"+t)},a.getTipElement=function(){return this.tip=this.tip||i.default(this.config.template)[0],this.tip},a.setContent=function(){var t=i.default(this.getTipElement());this.setElementContent(t.find(".popover-header"),this.getTitle());var e=this._getContent();"function"==typeof e&&(e=e.call(this.element)),this.setElementContent(t.find(".popover-body"),e),t.removeClass("fade show")},a._getContent=function(){return this.element.getAttribute("data-content")||this.config.content},a._cleanTipClass=function(){var t=i.default(this.getTipElement()),e=t.attr("class").match(Ke);null!==e&&e.length>0&&t.removeClass(e.join(""))},o._jQueryInterface=function(t){return this.each((function(){var e=i.default(this).data(Ye),n="object"==typeof t?t:null;if((e||!/dispose|hide/.test(t))&&(e||(e=new o(this,n),i.default(this).data(Ye,e)),"string"==typeof t)){if("undefined"==typeof e[t])throw new TypeError('No method named "'+t+'"');e[t]()}}))},r(o,null,[{key:"VERSION",get:function(){return"4.6.2"}},{key:"Default",get:function(){return Xe}},{key:"NAME",get:function(){return"popover"}},{key:"DATA_KEY",get:function(){return Ye}},{key:"Event",get:function(){return $e}},{key:"EVENT_KEY",get:function(){return".bs.popover"}},{key:"DefaultType",get:function(){return Ge}}]),o}(Ve);i.default.fn.popover=Je._jQueryInterface,i.default.fn.popover.Constructor=Je,i.default.fn.popover.noConflict=function(){return i.default.fn.popover=ze,Je._jQueryInterface};var Ze="scrollspy",tn="bs.scrollspy",en=i.default.fn[Ze],nn="active",on="position",rn=".nav, .list-group",an={offset:10,method:"auto",target:""},sn={offset:"number",method:"string",target:"(string|element)"},ln=function(){function t(t,e){var n=this;this._element=t,this._scrollElement="BODY"===t.tagName?window:t,this._config=this._getConfig(e),this._selector=this._config.target+" .nav-link,"+this._config.target+" .list-group-item,"+this._config.target+" .dropdown-item",this._offsets=[],this._targets=[],this._activeTarget=null,this._scrollHeight=0,i.default(this._scrollElement).on("scroll.bs.scrollspy",(function(t){return n._process(t)})),this.refresh(),this._process()}var e=t.prototype;return e.refresh=function(){var t=this,e=this._scrollElement===this._scrollElement.window?"offset":on,n="auto"===this._config.method?e:this._config.method,o=n===on?this._getScrollTop():0;this._offsets=[],this._targets=[],this._scrollHeight=this._getScrollHeight(),[].slice.call(document.querySelectorAll(this._selector)).map((function(t){var e,r=u.getSelectorFromElement(t);if(r&&(e=document.querySelector(r)),e){var a=e.getBoundingClientRect();if(a.width||a.height)return[i.default(e)[n]().top+o,r]}return null})).filter(Boolean).sort((function(t,e){return t[0]-e[0]})).forEach((function(e){t._offsets.push(e[0]),t._targets.push(e[1])}))},e.dispose=function(){i.default.removeData(this._element,tn),i.default(this._scrollElement).off(".bs.scrollspy"),this._element=null,this._scrollElement=null,this._config=null,this._selector=null,this._offsets=null,this._targets=null,this._activeTarget=null,this._scrollHeight=null},e._getConfig=function(t){if("string"!=typeof(t=a({},an,"object"==typeof t&&t?t:{})).target&&u.isElement(t.target)){var e=i.default(t.target).attr("id");e||(e=u.getUID(Ze),i.default(t.target).attr("id",e)),t.target="#"+e}return u.typeCheckConfig(Ze,t,sn),t},e._getScrollTop=function(){return this._scrollElement===window?this._scrollElement.pageYOffset:this._scrollElement.scrollTop},e._getScrollHeight=function(){return this._scrollElement.scrollHeight||Math.max(document.body.scrollHeight,document.documentElement.scrollHeight)},e._getOffsetHeight=function(){return this._scrollElement===window?window.innerHeight:this._scrollElement.getBoundingClientRect().height},e._process=function(){var t=this._getScrollTop()+this._config.offset,e=this._getScrollHeight(),n=this._config.offset+e-this._getOffsetHeight();if(this._scrollHeight!==e&&this.refresh(),t>=n){var i=this._targets[this._targets.length-1];this._activeTarget!==i&&this._activate(i)}else{if(this._activeTarget&&t<this._offsets[0]&&this._offsets[0]>0)return this._activeTarget=null,void this._clear();for(var o=this._offsets.length;o--;)this._activeTarget!==this._targets[o]&&t>=this._offsets[o]&&("undefined"==typeof this._offsets[o+1]||t<this._offsets[o+1])&&this._activate(this._targets[o])}},e._activate=function(t){this._activeTarget=t,this._clear();var e=this._selector.split(",").map((function(e){return e+'[data-target="'+t+'"],'+e+'[href="'+t+'"]'})),n=i.default([].slice.call(document.querySelectorAll(e.join(","))));n.hasClass("dropdown-item")?(n.closest(".dropdown").find(".dropdown-toggle").addClass(nn),n.addClass(nn)):(n.addClass(nn),n.parents(rn).prev(".nav-link, .list-group-item").addClass(nn),n.parents(rn).prev(".nav-item").children(".nav-link").addClass(nn)),i.default(this._scrollElement).trigger("activate.bs.scrollspy",{relatedTarget:t})},e._clear=function(){[].slice.call(document.querySelectorAll(this._selector)).filter((function(t){return t.classList.contains(nn)})).forEach((function(t){return t.classList.remove(nn)}))},t._jQueryInterface=function(e){return this.each((function(){var n=i.default(this).data(tn);if(n||(n=new t(this,"object"==typeof e&&e),i.default(this).data(tn,n)),"string"==typeof e){if("undefined"==typeof n[e])throw new TypeError('No method named "'+e+'"');n[e]()}}))},r(t,null,[{key:"VERSION",get:function(){return"4.6.2"}},{key:"Default",get:function(){return an}}]),t}();i.default(window).on("load.bs.scrollspy.data-api",(function(){for(var t=[].slice.call(document.querySelectorAll('[data-spy="scroll"]')),e=t.length;e--;){var n=i.default(t[e]);ln._jQueryInterface.call(n,n.data())}})),i.default.fn[Ze]=ln._jQueryInterface,i.default.fn[Ze].Constructor=ln,i.default.fn[Ze].noConflict=function(){return i.default.fn[Ze]=en,ln._jQueryInterface};var un="bs.tab",fn=i.default.fn.tab,dn="active",cn="fade",hn="show",pn=".active",mn="> li > .active",gn=function(){function t(t){this._element=t}var e=t.prototype;return e.show=function(){var t=this;if(!(this._element.parentNode&&this._element.parentNode.nodeType===Node.ELEMENT_NODE&&i.default(this._element).hasClass(dn)||i.default(this._element).hasClass("disabled")||this._element.hasAttribute("disabled"))){var e,n,o=i.default(this._element).closest(".nav, .list-group")[0],r=u.getSelectorFromElement(this._element);if(o){var a="UL"===o.nodeName||"OL"===o.nodeName?mn:pn;n=(n=i.default.makeArray(i.default(o).find(a)))[n.length-1]}var s=i.default.Event("hide.bs.tab",{relatedTarget:this._element}),l=i.default.Event("show.bs.tab",{relatedTarget:n});if(n&&i.default(n).trigger(s),i.default(this._element).trigger(l),!l.isDefaultPrevented()&&!s.isDefaultPrevented()){r&&(e=document.querySelector(r)),this._activate(this._element,o);var f=function(){var e=i.default.Event("hidden.bs.tab",{relatedTarget:t._element}),o=i.default.Event("shown.bs.tab",{relatedTarget:n});i.default(n).trigger(e),i.default(t._element).trigger(o)};e?this._activate(e,e.parentNode,f):f()}}},e.dispose=function(){i.default.removeData(this._element,un),this._element=null},e._activate=function(t,e,n){var o=this,r=(!e||"UL"!==e.nodeName&&"OL"!==e.nodeName?i.default(e).children(pn):i.default(e).find(mn))[0],a=n&&r&&i.default(r).hasClass(cn),s=function(){return o._transitionComplete(t,r,n)};if(r&&a){var l=u.getTransitionDurationFromElement(r);i.default(r).removeClass(hn).one(u.TRANSITION_END,s).emulateTransitionEnd(l)}else s()},e._transitionComplete=function(t,e,n){if(e){i.default(e).removeClass(dn);var o=i.default(e.parentNode).find("> .dropdown-menu .active")[0];o&&i.default(o).removeClass(dn),"tab"===e.getAttribute("role")&&e.setAttribute("aria-selected",!1)}i.default(t).addClass(dn),"tab"===t.getAttribute("role")&&t.setAttribute("aria-selected",!0),u.reflow(t),t.classList.contains(cn)&&t.classList.add(hn);var r=t.parentNode;if(r&&"LI"===r.nodeName&&(r=r.parentNode),r&&i.default(r).hasClass("dropdown-menu")){var a=i.default(t).closest(".dropdown")[0];if(a){var s=[].slice.call(a.querySelectorAll(".dropdown-toggle"));i.default(s).addClass(dn)}t.setAttribute("aria-expanded",!0)}n&&n()},t._jQueryInterface=function(e){return this.each((function(){var n=i.default(this),o=n.data(un);if(o||(o=new t(this),n.data(un,o)),"string"==typeof e){if("undefined"==typeof o[e])throw new TypeError('No method named "'+e+'"');o[e]()}}))},r(t,null,[{key:"VERSION",get:function(){return"4.6.2"}}]),t}();i.default(document).on("click.bs.tab.data-api",'[data-toggle="tab"], [data-toggle="pill"], [data-toggle="list"]',(function(t){t.preventDefault(),gn._jQueryInterface.call(i.default(this),"show")})),i.default.fn.tab=gn._jQueryInterface,i.default.fn.tab.Constructor=gn,i.default.fn.tab.noConflict=function(){return i.default.fn.tab=fn,gn._jQueryInterface};var _n="bs.toast",vn=i.default.fn.toast,bn="hide",yn="show",En="showing",wn="click.dismiss.bs.toast",Tn={animation:!0,autohide:!0,delay:500},Cn={animation:"boolean",autohide:"boolean",delay:"number"},Sn=function(){function t(t,e){this._element=t,this._config=this._getConfig(e),this._timeout=null,this._setListeners()}var e=t.prototype;return e.show=function(){var t=this,e=i.default.Event("show.bs.toast");if(i.default(this._element).trigger(e),!e.isDefaultPrevented()){this._clearTimeout(),this._config.animation&&this._element.classList.add("fade");var n=function(){t._element.classList.remove(En),t._element.classList.add(yn),i.default(t._element).trigger("shown.bs.toast"),t._config.autohide&&(t._timeout=setTimeout((function(){t.hide()}),t._config.delay))};if(this._element.classList.remove(bn),u.reflow(this._element),this._element.classList.add(En),this._config.animation){var o=u.getTransitionDurationFromElement(this._element);i.default(this._element).one(u.TRANSITION_END,n).emulateTransitionEnd(o)}else n()}},e.hide=function(){if(this._element.classList.contains(yn)){var t=i.default.Event("hide.bs.toast");i.default(this._element).trigger(t),t.isDefaultPrevented()||this._close()}},e.dispose=function(){this._clearTimeout(),this._element.classList.contains(yn)&&this._element.classList.remove(yn),i.default(this._element).off(wn),i.default.removeData(this._element,_n),this._element=null,this._config=null},e._getConfig=function(t){return t=a({},Tn,i.default(this._element).data(),"object"==typeof t&&t?t:{}),u.typeCheckConfig("toast",t,this.constructor.DefaultType),t},e._setListeners=function(){var t=this;i.default(this._element).on(wn,'[data-dismiss="toast"]',(function(){return t.hide()}))},e._close=function(){var t=this,e=function(){t._element.classList.add(bn),i.default(t._element).trigger("hidden.bs.toast")};if(this._element.classList.remove(yn),this._config.animation){var n=u.getTransitionDurationFromElement(this._element);i.default(this._element).one(u.TRANSITION_END,e).emulateTransitionEnd(n)}else e()},e._clearTimeout=function(){clearTimeout(this._timeout),this._timeout=null},t._jQueryInterface=function(e){return this.each((function(){var n=i.default(this),o=n.data(_n);if(o||(o=new t(this,"object"==typeof e&&e),n.data(_n,o)),"string"==typeof e){if("undefined"==typeof o[e])throw new TypeError('No method named "'+e+'"');o[e](this)}}))},r(t,null,[{key:"VERSION",get:function(){return"4.6.2"}},{key:"DefaultType",get:function(){return Cn}},{key:"Default",get:function(){return Tn}}]),t}();i.default.fn.toast=Sn._jQueryInterface,i.default.fn.toast.Constructor=Sn,i.default.fn.toast.noConflict=function(){return i.default.fn.toast=vn,Sn._jQueryInterface},t.Alert=c,t.Button=b,t.Carousel=O,t.Collapse=W,t.Dropdown=le,t.Modal=Se,t.Popover=Je,t.Scrollspy=ln,t.Tab=gn,t.Toast=Sn,t.Tooltip=Ve,t.Util=u,Object.defineProperty(t,"__esModule",{value:!0})}));
//# sourceMappingURL=bootstrap.bundle.min.js.map

(function (global, factory) {
  typeof exports === 'object' && typeof module !== 'undefined' ? module.exports = factory(require('jquery')) :
  typeof define === 'function' && define.amd ? define(['jquery'], factory) :
  (global = typeof globalThis !== 'undefined' ? globalThis : global || self, global.BootstrapTable = factory(global.jQuery));
})(this, (function ($$q) { 'use strict';

  function _iterableToArrayLimit(r, l) {
    var t = null == r ? null : "undefined" != typeof Symbol && r[Symbol.iterator] || r["@@iterator"];
    if (null != t) {
      var e,
        n,
        i,
        u,
        a = [],
        f = !0,
        o = !1;
      try {
        if (i = (t = t.call(r)).next, 0 === l) {
          if (Object(t) !== t) return;
          f = !1;
        } else for (; !(f = (e = i.call(t)).done) && (a.push(e.value), a.length !== l); f = !0);
      } catch (r) {
        o = !0, n = r;
      } finally {
        try {
          if (!f && null != t.return && (u = t.return(), Object(u) !== u)) return;
        } finally {
          if (o) throw n;
        }
      }
      return a;
    }
  }
  function _toPrimitive(t, r) {
    if ("object" != typeof t || !t) return t;
    var e = t[Symbol.toPrimitive];
    if (void 0 !== e) {
      var i = e.call(t, r || "default");
      if ("object" != typeof i) return i;
      throw new TypeError("@@toPrimitive must return a primitive value.");
    }
    return ("string" === r ? String : Number)(t);
  }
  function _toPropertyKey(t) {
    var i = _toPrimitive(t, "string");
    return "symbol" == typeof i ? i : i + "";
  }
  function _typeof(o) {
    "@babel/helpers - typeof";

    return _typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (o) {
      return typeof o;
    } : function (o) {
      return o && "function" == typeof Symbol && o.constructor === Symbol && o !== Symbol.prototype ? "symbol" : typeof o;
    }, _typeof(o);
  }
  function _classCallCheck(instance, Constructor) {
    if (!(instance instanceof Constructor)) {
      throw new TypeError("Cannot call a class as a function");
    }
  }
  function _defineProperties(target, props) {
    for (var i = 0; i < props.length; i++) {
      var descriptor = props[i];
      descriptor.enumerable = descriptor.enumerable || false;
      descriptor.configurable = true;
      if ("value" in descriptor) descriptor.writable = true;
      Object.defineProperty(target, _toPropertyKey(descriptor.key), descriptor);
    }
  }
  function _createClass(Constructor, protoProps, staticProps) {
    if (protoProps) _defineProperties(Constructor.prototype, protoProps);
    if (staticProps) _defineProperties(Constructor, staticProps);
    Object.defineProperty(Constructor, "prototype", {
      writable: false
    });
    return Constructor;
  }
  function _slicedToArray(arr, i) {
    return _arrayWithHoles(arr) || _iterableToArrayLimit(arr, i) || _unsupportedIterableToArray(arr, i) || _nonIterableRest();
  }
  function _toConsumableArray(arr) {
    return _arrayWithoutHoles(arr) || _iterableToArray(arr) || _unsupportedIterableToArray(arr) || _nonIterableSpread();
  }
  function _arrayWithoutHoles(arr) {
    if (Array.isArray(arr)) return _arrayLikeToArray(arr);
  }
  function _arrayWithHoles(arr) {
    if (Array.isArray(arr)) return arr;
  }
  function _iterableToArray(iter) {
    if (typeof Symbol !== "undefined" && iter[Symbol.iterator] != null || iter["@@iterator"] != null) return Array.from(iter);
  }
  function _unsupportedIterableToArray(o, minLen) {
    if (!o) return;
    if (typeof o === "string") return _arrayLikeToArray(o, minLen);
    var n = Object.prototype.toString.call(o).slice(8, -1);
    if (n === "Object" && o.constructor) n = o.constructor.name;
    if (n === "Map" || n === "Set") return Array.from(o);
    if (n === "Arguments" || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(n)) return _arrayLikeToArray(o, minLen);
  }
  function _arrayLikeToArray(arr, len) {
    if (len == null || len > arr.length) len = arr.length;
    for (var i = 0, arr2 = new Array(len); i < len; i++) arr2[i] = arr[i];
    return arr2;
  }
  function _nonIterableSpread() {
    throw new TypeError("Invalid attempt to spread non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method.");
  }
  function _nonIterableRest() {
    throw new TypeError("Invalid attempt to destructure non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method.");
  }
  function _createForOfIteratorHelper(o, allowArrayLike) {
    var it = typeof Symbol !== "undefined" && o[Symbol.iterator] || o["@@iterator"];
    if (!it) {
      if (Array.isArray(o) || (it = _unsupportedIterableToArray(o)) || allowArrayLike && o && typeof o.length === "number") {
        if (it) o = it;
        var i = 0;
        var F = function () {};
        return {
          s: F,
          n: function () {
            if (i >= o.length) return {
              done: true
            };
            return {
              done: false,
              value: o[i++]
            };
          },
          e: function (e) {
            throw e;
          },
          f: F
        };
      }
      throw new TypeError("Invalid attempt to iterate non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method.");
    }
    var normalCompletion = true,
      didErr = false,
      err;
    return {
      s: function () {
        it = it.call(o);
      },
      n: function () {
        var step = it.next();
        normalCompletion = step.done;
        return step;
      },
      e: function (e) {
        didErr = true;
        err = e;
      },
      f: function () {
        try {
          if (!normalCompletion && it.return != null) it.return();
        } finally {
          if (didErr) throw err;
        }
      }
    };
  }

  var commonjsGlobal = typeof globalThis !== 'undefined' ? globalThis : typeof window !== 'undefined' ? window : typeof global !== 'undefined' ? global : typeof self !== 'undefined' ? self : {};

  var check = function (it) {
    return it && it.Math === Math && it;
  };

  // https://github.com/zloirock/core-js/issues/86#issuecomment-115759028
  var global$k =
    // eslint-disable-next-line es/no-global-this -- safe
    check(typeof globalThis == 'object' && globalThis) ||
    check(typeof window == 'object' && window) ||
    // eslint-disable-next-line no-restricted-globals -- safe
    check(typeof self == 'object' && self) ||
    check(typeof commonjsGlobal == 'object' && commonjsGlobal) ||
    check(typeof commonjsGlobal == 'object' && commonjsGlobal) ||
    // eslint-disable-next-line no-new-func -- fallback
    (function () { return this; })() || Function('return this')();

  var objectGetOwnPropertyDescriptor = {};

  var fails$x = function (exec) {
    try {
      return !!exec();
    } catch (error) {
      return true;
    }
  };

  var fails$w = fails$x;

  // Detect IE8's incomplete defineProperty implementation
  var descriptors = !fails$w(function () {
    // eslint-disable-next-line es/no-object-defineproperty -- required for testing
    return Object.defineProperty({}, 1, { get: function () { return 7; } })[1] !== 7;
  });

  var fails$v = fails$x;

  var functionBindNative = !fails$v(function () {
    // eslint-disable-next-line es/no-function-prototype-bind -- safe
    var test = (function () { /* empty */ }).bind();
    // eslint-disable-next-line no-prototype-builtins -- safe
    return typeof test != 'function' || test.hasOwnProperty('prototype');
  });

  var NATIVE_BIND$3 = functionBindNative;

  var call$f = Function.prototype.call;

  var functionCall = NATIVE_BIND$3 ? call$f.bind(call$f) : function () {
    return call$f.apply(call$f, arguments);
  };

  var objectPropertyIsEnumerable = {};

  var $propertyIsEnumerable$1 = {}.propertyIsEnumerable;
  // eslint-disable-next-line es/no-object-getownpropertydescriptor -- safe
  var getOwnPropertyDescriptor$5 = Object.getOwnPropertyDescriptor;

  // Nashorn ~ JDK8 bug
  var NASHORN_BUG = getOwnPropertyDescriptor$5 && !$propertyIsEnumerable$1.call({ 1: 2 }, 1);

  // `Object.prototype.propertyIsEnumerable` method implementation
  // https://tc39.es/ecma262/#sec-object.prototype.propertyisenumerable
  objectPropertyIsEnumerable.f = NASHORN_BUG ? function propertyIsEnumerable(V) {
    var descriptor = getOwnPropertyDescriptor$5(this, V);
    return !!descriptor && descriptor.enumerable;
  } : $propertyIsEnumerable$1;

  var createPropertyDescriptor$4 = function (bitmap, value) {
    return {
      enumerable: !(bitmap & 1),
      configurable: !(bitmap & 2),
      writable: !(bitmap & 4),
      value: value
    };
  };

  var NATIVE_BIND$2 = functionBindNative;

  var FunctionPrototype$2 = Function.prototype;
  var call$e = FunctionPrototype$2.call;
  var uncurryThisWithBind = NATIVE_BIND$2 && FunctionPrototype$2.bind.bind(call$e, call$e);

  var functionUncurryThis = NATIVE_BIND$2 ? uncurryThisWithBind : function (fn) {
    return function () {
      return call$e.apply(fn, arguments);
    };
  };

  var uncurryThis$y = functionUncurryThis;

  var toString$h = uncurryThis$y({}.toString);
  var stringSlice$9 = uncurryThis$y(''.slice);

  var classofRaw$2 = function (it) {
    return stringSlice$9(toString$h(it), 8, -1);
  };

  var uncurryThis$x = functionUncurryThis;
  var fails$u = fails$x;
  var classof$7 = classofRaw$2;

  var $Object$4 = Object;
  var split = uncurryThis$x(''.split);

  // fallback for non-array-like ES3 and non-enumerable old V8 strings
  var indexedObject = fails$u(function () {
    // throws an error in rhino, see https://github.com/mozilla/rhino/issues/346
    // eslint-disable-next-line no-prototype-builtins -- safe
    return !$Object$4('z').propertyIsEnumerable(0);
  }) ? function (it) {
    return classof$7(it) === 'String' ? split(it, '') : $Object$4(it);
  } : $Object$4;

  // we can't use just `it == null` since of `document.all` special case
  // https://tc39.es/ecma262/#sec-IsHTMLDDA-internal-slot-aec
  var isNullOrUndefined$7 = function (it) {
    return it === null || it === undefined;
  };

  var isNullOrUndefined$6 = isNullOrUndefined$7;

  var $TypeError$c = TypeError;

  // `RequireObjectCoercible` abstract operation
  // https://tc39.es/ecma262/#sec-requireobjectcoercible
  var requireObjectCoercible$c = function (it) {
    if (isNullOrUndefined$6(it)) throw new $TypeError$c("Can't call method on " + it);
    return it;
  };

  // toObject with fallback for non-array-like ES3 strings
  var IndexedObject$3 = indexedObject;
  var requireObjectCoercible$b = requireObjectCoercible$c;

  var toIndexedObject$8 = function (it) {
    return IndexedObject$3(requireObjectCoercible$b(it));
  };

  // https://tc39.es/ecma262/#sec-IsHTMLDDA-internal-slot
  var documentAll = typeof document == 'object' && document.all;

  // `IsCallable` abstract operation
  // https://tc39.es/ecma262/#sec-iscallable
  // eslint-disable-next-line unicorn/no-typeof-undefined -- required for testing
  var isCallable$i = typeof documentAll == 'undefined' && documentAll !== undefined ? function (argument) {
    return typeof argument == 'function' || argument === documentAll;
  } : function (argument) {
    return typeof argument == 'function';
  };

  var isCallable$h = isCallable$i;

  var isObject$d = function (it) {
    return typeof it == 'object' ? it !== null : isCallable$h(it);
  };

  var global$j = global$k;
  var isCallable$g = isCallable$i;

  var aFunction = function (argument) {
    return isCallable$g(argument) ? argument : undefined;
  };

  var getBuiltIn$5 = function (namespace, method) {
    return arguments.length < 2 ? aFunction(global$j[namespace]) : global$j[namespace] && global$j[namespace][method];
  };

  var uncurryThis$w = functionUncurryThis;

  var objectIsPrototypeOf = uncurryThis$w({}.isPrototypeOf);

  var engineUserAgent = typeof navigator != 'undefined' && String(navigator.userAgent) || '';

  var global$i = global$k;
  var userAgent$2 = engineUserAgent;

  var process = global$i.process;
  var Deno = global$i.Deno;
  var versions = process && process.versions || Deno && Deno.version;
  var v8 = versions && versions.v8;
  var match, version;

  if (v8) {
    match = v8.split('.');
    // in old Chrome, versions of V8 isn't V8 = Chrome / 10
    // but their correct versions are not interesting for us
    version = match[0] > 0 && match[0] < 4 ? 1 : +(match[0] + match[1]);
  }

  // BrowserFS NodeJS `process` polyfill incorrectly set `.v8` to `0.0`
  // so check `userAgent` even if `.v8` exists, but 0
  if (!version && userAgent$2) {
    match = userAgent$2.match(/Edge\/(\d+)/);
    if (!match || match[1] >= 74) {
      match = userAgent$2.match(/Chrome\/(\d+)/);
      if (match) version = +match[1];
    }
  }

  var engineV8Version = version;

  /* eslint-disable es/no-symbol -- required for testing */
  var V8_VERSION$2 = engineV8Version;
  var fails$t = fails$x;
  var global$h = global$k;

  var $String$5 = global$h.String;

  // eslint-disable-next-line es/no-object-getownpropertysymbols -- required for testing
  var symbolConstructorDetection = !!Object.getOwnPropertySymbols && !fails$t(function () {
    var symbol = Symbol('symbol detection');
    // Chrome 38 Symbol has incorrect toString conversion
    // `get-own-property-symbols` polyfill symbols converted to object are not Symbol instances
    // nb: Do not call `String` directly to avoid this being optimized out to `symbol+''` which will,
    // of course, fail.
    return !$String$5(symbol) || !(Object(symbol) instanceof Symbol) ||
      // Chrome 38-40 symbols are not inherited from DOM collections prototypes to instances
      !Symbol.sham && V8_VERSION$2 && V8_VERSION$2 < 41;
  });

  /* eslint-disable es/no-symbol -- required for testing */
  var NATIVE_SYMBOL$1 = symbolConstructorDetection;

  var useSymbolAsUid = NATIVE_SYMBOL$1
    && !Symbol.sham
    && typeof Symbol.iterator == 'symbol';

  var getBuiltIn$4 = getBuiltIn$5;
  var isCallable$f = isCallable$i;
  var isPrototypeOf$3 = objectIsPrototypeOf;
  var USE_SYMBOL_AS_UID$1 = useSymbolAsUid;

  var $Object$3 = Object;

  var isSymbol$3 = USE_SYMBOL_AS_UID$1 ? function (it) {
    return typeof it == 'symbol';
  } : function (it) {
    var $Symbol = getBuiltIn$4('Symbol');
    return isCallable$f($Symbol) && isPrototypeOf$3($Symbol.prototype, $Object$3(it));
  };

  var $String$4 = String;

  var tryToString$3 = function (argument) {
    try {
      return $String$4(argument);
    } catch (error) {
      return 'Object';
    }
  };

  var isCallable$e = isCallable$i;
  var tryToString$2 = tryToString$3;

  var $TypeError$b = TypeError;

  // `Assert: IsCallable(argument) is true`
  var aCallable$4 = function (argument) {
    if (isCallable$e(argument)) return argument;
    throw new $TypeError$b(tryToString$2(argument) + ' is not a function');
  };

  var aCallable$3 = aCallable$4;
  var isNullOrUndefined$5 = isNullOrUndefined$7;

  // `GetMethod` abstract operation
  // https://tc39.es/ecma262/#sec-getmethod
  var getMethod$5 = function (V, P) {
    var func = V[P];
    return isNullOrUndefined$5(func) ? undefined : aCallable$3(func);
  };

  var call$d = functionCall;
  var isCallable$d = isCallable$i;
  var isObject$c = isObject$d;

  var $TypeError$a = TypeError;

  // `OrdinaryToPrimitive` abstract operation
  // https://tc39.es/ecma262/#sec-ordinarytoprimitive
  var ordinaryToPrimitive$1 = function (input, pref) {
    var fn, val;
    if (pref === 'string' && isCallable$d(fn = input.toString) && !isObject$c(val = call$d(fn, input))) return val;
    if (isCallable$d(fn = input.valueOf) && !isObject$c(val = call$d(fn, input))) return val;
    if (pref !== 'string' && isCallable$d(fn = input.toString) && !isObject$c(val = call$d(fn, input))) return val;
    throw new $TypeError$a("Can't convert object to primitive value");
  };

  var sharedStore = {exports: {}};

  var isPure = false;

  var global$g = global$k;

  // eslint-disable-next-line es/no-object-defineproperty -- safe
  var defineProperty$8 = Object.defineProperty;

  var defineGlobalProperty$3 = function (key, value) {
    try {
      defineProperty$8(global$g, key, { value: value, configurable: true, writable: true });
    } catch (error) {
      global$g[key] = value;
    } return value;
  };

  var globalThis$1 = global$k;
  var defineGlobalProperty$2 = defineGlobalProperty$3;

  var SHARED = '__core-js_shared__';
  var store$3 = sharedStore.exports = globalThis$1[SHARED] || defineGlobalProperty$2(SHARED, {});

  (store$3.versions || (store$3.versions = [])).push({
    version: '3.36.1',
    mode: 'global',
    copyright: '© 2014-2024 Denis Pushkarev (zloirock.ru)',
    license: 'https://github.com/zloirock/core-js/blob/v3.36.1/LICENSE',
    source: 'https://github.com/zloirock/core-js'
  });

  var sharedStoreExports = sharedStore.exports;

  var store$2 = sharedStoreExports;

  var shared$4 = function (key, value) {
    return store$2[key] || (store$2[key] = value || {});
  };

  var requireObjectCoercible$a = requireObjectCoercible$c;

  var $Object$2 = Object;

  // `ToObject` abstract operation
  // https://tc39.es/ecma262/#sec-toobject
  var toObject$b = function (argument) {
    return $Object$2(requireObjectCoercible$a(argument));
  };

  var uncurryThis$v = functionUncurryThis;
  var toObject$a = toObject$b;

  var hasOwnProperty = uncurryThis$v({}.hasOwnProperty);

  // `HasOwnProperty` abstract operation
  // https://tc39.es/ecma262/#sec-hasownproperty
  // eslint-disable-next-line es/no-object-hasown -- safe
  var hasOwnProperty_1 = Object.hasOwn || function hasOwn(it, key) {
    return hasOwnProperty(toObject$a(it), key);
  };

  var uncurryThis$u = functionUncurryThis;

  var id = 0;
  var postfix = Math.random();
  var toString$g = uncurryThis$u(1.0.toString);

  var uid$2 = function (key) {
    return 'Symbol(' + (key === undefined ? '' : key) + ')_' + toString$g(++id + postfix, 36);
  };

  var global$f = global$k;
  var shared$3 = shared$4;
  var hasOwn$b = hasOwnProperty_1;
  var uid$1 = uid$2;
  var NATIVE_SYMBOL = symbolConstructorDetection;
  var USE_SYMBOL_AS_UID = useSymbolAsUid;

  var Symbol$3 = global$f.Symbol;
  var WellKnownSymbolsStore = shared$3('wks');
  var createWellKnownSymbol = USE_SYMBOL_AS_UID ? Symbol$3['for'] || Symbol$3 : Symbol$3 && Symbol$3.withoutSetter || uid$1;

  var wellKnownSymbol$j = function (name) {
    if (!hasOwn$b(WellKnownSymbolsStore, name)) {
      WellKnownSymbolsStore[name] = NATIVE_SYMBOL && hasOwn$b(Symbol$3, name)
        ? Symbol$3[name]
        : createWellKnownSymbol('Symbol.' + name);
    } return WellKnownSymbolsStore[name];
  };

  var call$c = functionCall;
  var isObject$b = isObject$d;
  var isSymbol$2 = isSymbol$3;
  var getMethod$4 = getMethod$5;
  var ordinaryToPrimitive = ordinaryToPrimitive$1;
  var wellKnownSymbol$i = wellKnownSymbol$j;

  var $TypeError$9 = TypeError;
  var TO_PRIMITIVE = wellKnownSymbol$i('toPrimitive');

  // `ToPrimitive` abstract operation
  // https://tc39.es/ecma262/#sec-toprimitive
  var toPrimitive$3 = function (input, pref) {
    if (!isObject$b(input) || isSymbol$2(input)) return input;
    var exoticToPrim = getMethod$4(input, TO_PRIMITIVE);
    var result;
    if (exoticToPrim) {
      if (pref === undefined) pref = 'default';
      result = call$c(exoticToPrim, input, pref);
      if (!isObject$b(result) || isSymbol$2(result)) return result;
      throw new $TypeError$9("Can't convert object to primitive value");
    }
    if (pref === undefined) pref = 'number';
    return ordinaryToPrimitive(input, pref);
  };

  var toPrimitive$2 = toPrimitive$3;
  var isSymbol$1 = isSymbol$3;

  // `ToPropertyKey` abstract operation
  // https://tc39.es/ecma262/#sec-topropertykey
  var toPropertyKey$2 = function (argument) {
    var key = toPrimitive$2(argument, 'string');
    return isSymbol$1(key) ? key : key + '';
  };

  var global$e = global$k;
  var isObject$a = isObject$d;

  var document$1 = global$e.document;
  // typeof document.createElement is 'object' in old IE
  var EXISTS$1 = isObject$a(document$1) && isObject$a(document$1.createElement);

  var documentCreateElement$2 = function (it) {
    return EXISTS$1 ? document$1.createElement(it) : {};
  };

  var DESCRIPTORS$f = descriptors;
  var fails$s = fails$x;
  var createElement = documentCreateElement$2;

  // Thanks to IE8 for its funny defineProperty
  var ie8DomDefine = !DESCRIPTORS$f && !fails$s(function () {
    // eslint-disable-next-line es/no-object-defineproperty -- required for testing
    return Object.defineProperty(createElement('div'), 'a', {
      get: function () { return 7; }
    }).a !== 7;
  });

  var DESCRIPTORS$e = descriptors;
  var call$b = functionCall;
  var propertyIsEnumerableModule$1 = objectPropertyIsEnumerable;
  var createPropertyDescriptor$3 = createPropertyDescriptor$4;
  var toIndexedObject$7 = toIndexedObject$8;
  var toPropertyKey$1 = toPropertyKey$2;
  var hasOwn$a = hasOwnProperty_1;
  var IE8_DOM_DEFINE$1 = ie8DomDefine;

  // eslint-disable-next-line es/no-object-getownpropertydescriptor -- safe
  var $getOwnPropertyDescriptor$1 = Object.getOwnPropertyDescriptor;

  // `Object.getOwnPropertyDescriptor` method
  // https://tc39.es/ecma262/#sec-object.getownpropertydescriptor
  objectGetOwnPropertyDescriptor.f = DESCRIPTORS$e ? $getOwnPropertyDescriptor$1 : function getOwnPropertyDescriptor(O, P) {
    O = toIndexedObject$7(O);
    P = toPropertyKey$1(P);
    if (IE8_DOM_DEFINE$1) try {
      return $getOwnPropertyDescriptor$1(O, P);
    } catch (error) { /* empty */ }
    if (hasOwn$a(O, P)) return createPropertyDescriptor$3(!call$b(propertyIsEnumerableModule$1.f, O, P), O[P]);
  };

  var objectDefineProperty = {};

  var DESCRIPTORS$d = descriptors;
  var fails$r = fails$x;

  // V8 ~ Chrome 36-
  // https://bugs.chromium.org/p/v8/issues/detail?id=3334
  var v8PrototypeDefineBug = DESCRIPTORS$d && fails$r(function () {
    // eslint-disable-next-line es/no-object-defineproperty -- required for testing
    return Object.defineProperty(function () { /* empty */ }, 'prototype', {
      value: 42,
      writable: false
    }).prototype !== 42;
  });

  var isObject$9 = isObject$d;

  var $String$3 = String;
  var $TypeError$8 = TypeError;

  // `Assert: Type(argument) is Object`
  var anObject$c = function (argument) {
    if (isObject$9(argument)) return argument;
    throw new $TypeError$8($String$3(argument) + ' is not an object');
  };

  var DESCRIPTORS$c = descriptors;
  var IE8_DOM_DEFINE = ie8DomDefine;
  var V8_PROTOTYPE_DEFINE_BUG$1 = v8PrototypeDefineBug;
  var anObject$b = anObject$c;
  var toPropertyKey = toPropertyKey$2;

  var $TypeError$7 = TypeError;
  // eslint-disable-next-line es/no-object-defineproperty -- safe
  var $defineProperty = Object.defineProperty;
  // eslint-disable-next-line es/no-object-getownpropertydescriptor -- safe
  var $getOwnPropertyDescriptor = Object.getOwnPropertyDescriptor;
  var ENUMERABLE = 'enumerable';
  var CONFIGURABLE$1 = 'configurable';
  var WRITABLE = 'writable';

  // `Object.defineProperty` method
  // https://tc39.es/ecma262/#sec-object.defineproperty
  objectDefineProperty.f = DESCRIPTORS$c ? V8_PROTOTYPE_DEFINE_BUG$1 ? function defineProperty(O, P, Attributes) {
    anObject$b(O);
    P = toPropertyKey(P);
    anObject$b(Attributes);
    if (typeof O === 'function' && P === 'prototype' && 'value' in Attributes && WRITABLE in Attributes && !Attributes[WRITABLE]) {
      var current = $getOwnPropertyDescriptor(O, P);
      if (current && current[WRITABLE]) {
        O[P] = Attributes.value;
        Attributes = {
          configurable: CONFIGURABLE$1 in Attributes ? Attributes[CONFIGURABLE$1] : current[CONFIGURABLE$1],
          enumerable: ENUMERABLE in Attributes ? Attributes[ENUMERABLE] : current[ENUMERABLE],
          writable: false
        };
      }
    } return $defineProperty(O, P, Attributes);
  } : $defineProperty : function defineProperty(O, P, Attributes) {
    anObject$b(O);
    P = toPropertyKey(P);
    anObject$b(Attributes);
    if (IE8_DOM_DEFINE) try {
      return $defineProperty(O, P, Attributes);
    } catch (error) { /* empty */ }
    if ('get' in Attributes || 'set' in Attributes) throw new $TypeError$7('Accessors not supported');
    if ('value' in Attributes) O[P] = Attributes.value;
    return O;
  };

  var DESCRIPTORS$b = descriptors;
  var definePropertyModule$4 = objectDefineProperty;
  var createPropertyDescriptor$2 = createPropertyDescriptor$4;

  var createNonEnumerableProperty$7 = DESCRIPTORS$b ? function (object, key, value) {
    return definePropertyModule$4.f(object, key, createPropertyDescriptor$2(1, value));
  } : function (object, key, value) {
    object[key] = value;
    return object;
  };

  var makeBuiltIn$3 = {exports: {}};

  var DESCRIPTORS$a = descriptors;
  var hasOwn$9 = hasOwnProperty_1;

  var FunctionPrototype$1 = Function.prototype;
  // eslint-disable-next-line es/no-object-getownpropertydescriptor -- safe
  var getDescriptor = DESCRIPTORS$a && Object.getOwnPropertyDescriptor;

  var EXISTS = hasOwn$9(FunctionPrototype$1, 'name');
  // additional protection from minified / mangled / dropped function names
  var PROPER = EXISTS && (function something() { /* empty */ }).name === 'something';
  var CONFIGURABLE = EXISTS && (!DESCRIPTORS$a || (DESCRIPTORS$a && getDescriptor(FunctionPrototype$1, 'name').configurable));

  var functionName = {
    EXISTS: EXISTS,
    PROPER: PROPER,
    CONFIGURABLE: CONFIGURABLE
  };

  var uncurryThis$t = functionUncurryThis;
  var isCallable$c = isCallable$i;
  var store$1 = sharedStoreExports;

  var functionToString = uncurryThis$t(Function.toString);

  // this helper broken in `core-js@3.4.1-3.4.4`, so we can't use `shared` helper
  if (!isCallable$c(store$1.inspectSource)) {
    store$1.inspectSource = function (it) {
      return functionToString(it);
    };
  }

  var inspectSource$2 = store$1.inspectSource;

  var global$d = global$k;
  var isCallable$b = isCallable$i;

  var WeakMap$1 = global$d.WeakMap;

  var weakMapBasicDetection = isCallable$b(WeakMap$1) && /native code/.test(String(WeakMap$1));

  var shared$2 = shared$4;
  var uid = uid$2;

  var keys$1 = shared$2('keys');

  var sharedKey$3 = function (key) {
    return keys$1[key] || (keys$1[key] = uid(key));
  };

  var hiddenKeys$4 = {};

  var NATIVE_WEAK_MAP = weakMapBasicDetection;
  var global$c = global$k;
  var isObject$8 = isObject$d;
  var createNonEnumerableProperty$6 = createNonEnumerableProperty$7;
  var hasOwn$8 = hasOwnProperty_1;
  var shared$1 = sharedStoreExports;
  var sharedKey$2 = sharedKey$3;
  var hiddenKeys$3 = hiddenKeys$4;

  var OBJECT_ALREADY_INITIALIZED = 'Object already initialized';
  var TypeError$2 = global$c.TypeError;
  var WeakMap = global$c.WeakMap;
  var set, get, has;

  var enforce = function (it) {
    return has(it) ? get(it) : set(it, {});
  };

  var getterFor = function (TYPE) {
    return function (it) {
      var state;
      if (!isObject$8(it) || (state = get(it)).type !== TYPE) {
        throw new TypeError$2('Incompatible receiver, ' + TYPE + ' required');
      } return state;
    };
  };

  if (NATIVE_WEAK_MAP || shared$1.state) {
    var store = shared$1.state || (shared$1.state = new WeakMap());
    /* eslint-disable no-self-assign -- prototype methods protection */
    store.get = store.get;
    store.has = store.has;
    store.set = store.set;
    /* eslint-enable no-self-assign -- prototype methods protection */
    set = function (it, metadata) {
      if (store.has(it)) throw new TypeError$2(OBJECT_ALREADY_INITIALIZED);
      metadata.facade = it;
      store.set(it, metadata);
      return metadata;
    };
    get = function (it) {
      return store.get(it) || {};
    };
    has = function (it) {
      return store.has(it);
    };
  } else {
    var STATE = sharedKey$2('state');
    hiddenKeys$3[STATE] = true;
    set = function (it, metadata) {
      if (hasOwn$8(it, STATE)) throw new TypeError$2(OBJECT_ALREADY_INITIALIZED);
      metadata.facade = it;
      createNonEnumerableProperty$6(it, STATE, metadata);
      return metadata;
    };
    get = function (it) {
      return hasOwn$8(it, STATE) ? it[STATE] : {};
    };
    has = function (it) {
      return hasOwn$8(it, STATE);
    };
  }

  var internalState = {
    set: set,
    get: get,
    has: has,
    enforce: enforce,
    getterFor: getterFor
  };

  var uncurryThis$s = functionUncurryThis;
  var fails$q = fails$x;
  var isCallable$a = isCallable$i;
  var hasOwn$7 = hasOwnProperty_1;
  var DESCRIPTORS$9 = descriptors;
  var CONFIGURABLE_FUNCTION_NAME$1 = functionName.CONFIGURABLE;
  var inspectSource$1 = inspectSource$2;
  var InternalStateModule$1 = internalState;

  var enforceInternalState$1 = InternalStateModule$1.enforce;
  var getInternalState$2 = InternalStateModule$1.get;
  var $String$2 = String;
  // eslint-disable-next-line es/no-object-defineproperty -- safe
  var defineProperty$7 = Object.defineProperty;
  var stringSlice$8 = uncurryThis$s(''.slice);
  var replace$4 = uncurryThis$s(''.replace);
  var join = uncurryThis$s([].join);

  var CONFIGURABLE_LENGTH = DESCRIPTORS$9 && !fails$q(function () {
    return defineProperty$7(function () { /* empty */ }, 'length', { value: 8 }).length !== 8;
  });

  var TEMPLATE = String(String).split('String');

  var makeBuiltIn$2 = makeBuiltIn$3.exports = function (value, name, options) {
    if (stringSlice$8($String$2(name), 0, 7) === 'Symbol(') {
      name = '[' + replace$4($String$2(name), /^Symbol\(([^)]*)\).*$/, '$1') + ']';
    }
    if (options && options.getter) name = 'get ' + name;
    if (options && options.setter) name = 'set ' + name;
    if (!hasOwn$7(value, 'name') || (CONFIGURABLE_FUNCTION_NAME$1 && value.name !== name)) {
      if (DESCRIPTORS$9) defineProperty$7(value, 'name', { value: name, configurable: true });
      else value.name = name;
    }
    if (CONFIGURABLE_LENGTH && options && hasOwn$7(options, 'arity') && value.length !== options.arity) {
      defineProperty$7(value, 'length', { value: options.arity });
    }
    try {
      if (options && hasOwn$7(options, 'constructor') && options.constructor) {
        if (DESCRIPTORS$9) defineProperty$7(value, 'prototype', { writable: false });
      // in V8 ~ Chrome 53, prototypes of some methods, like `Array.prototype.values`, are non-writable
      } else if (value.prototype) value.prototype = undefined;
    } catch (error) { /* empty */ }
    var state = enforceInternalState$1(value);
    if (!hasOwn$7(state, 'source')) {
      state.source = join(TEMPLATE, typeof name == 'string' ? name : '');
    } return value;
  };

  // add fake Function#toString for correct work wrapped methods / constructors with methods like LoDash isNative
  // eslint-disable-next-line no-extend-native -- required
  Function.prototype.toString = makeBuiltIn$2(function toString() {
    return isCallable$a(this) && getInternalState$2(this).source || inspectSource$1(this);
  }, 'toString');

  var makeBuiltInExports = makeBuiltIn$3.exports;

  var isCallable$9 = isCallable$i;
  var definePropertyModule$3 = objectDefineProperty;
  var makeBuiltIn$1 = makeBuiltInExports;
  var defineGlobalProperty$1 = defineGlobalProperty$3;

  var defineBuiltIn$7 = function (O, key, value, options) {
    if (!options) options = {};
    var simple = options.enumerable;
    var name = options.name !== undefined ? options.name : key;
    if (isCallable$9(value)) makeBuiltIn$1(value, name, options);
    if (options.global) {
      if (simple) O[key] = value;
      else defineGlobalProperty$1(key, value);
    } else {
      try {
        if (!options.unsafe) delete O[key];
        else if (O[key]) simple = true;
      } catch (error) { /* empty */ }
      if (simple) O[key] = value;
      else definePropertyModule$3.f(O, key, {
        value: value,
        enumerable: false,
        configurable: !options.nonConfigurable,
        writable: !options.nonWritable
      });
    } return O;
  };

  var objectGetOwnPropertyNames = {};

  var ceil = Math.ceil;
  var floor$2 = Math.floor;

  // `Math.trunc` method
  // https://tc39.es/ecma262/#sec-math.trunc
  // eslint-disable-next-line es/no-math-trunc -- safe
  var mathTrunc = Math.trunc || function trunc(x) {
    var n = +x;
    return (n > 0 ? floor$2 : ceil)(n);
  };

  var trunc = mathTrunc;

  // `ToIntegerOrInfinity` abstract operation
  // https://tc39.es/ecma262/#sec-tointegerorinfinity
  var toIntegerOrInfinity$5 = function (argument) {
    var number = +argument;
    // eslint-disable-next-line no-self-compare -- NaN check
    return number !== number || number === 0 ? 0 : trunc(number);
  };

  var toIntegerOrInfinity$4 = toIntegerOrInfinity$5;

  var max$3 = Math.max;
  var min$6 = Math.min;

  // Helper for a popular repeating case of the spec:
  // Let integer be ? ToInteger(index).
  // If integer < 0, let result be max((length + integer), 0); else let result be min(integer, length).
  var toAbsoluteIndex$3 = function (index, length) {
    var integer = toIntegerOrInfinity$4(index);
    return integer < 0 ? max$3(integer + length, 0) : min$6(integer, length);
  };

  var toIntegerOrInfinity$3 = toIntegerOrInfinity$5;

  var min$5 = Math.min;

  // `ToLength` abstract operation
  // https://tc39.es/ecma262/#sec-tolength
  var toLength$6 = function (argument) {
    var len = toIntegerOrInfinity$3(argument);
    return len > 0 ? min$5(len, 0x1FFFFFFFFFFFFF) : 0; // 2 ** 53 - 1 == 9007199254740991
  };

  var toLength$5 = toLength$6;

  // `LengthOfArrayLike` abstract operation
  // https://tc39.es/ecma262/#sec-lengthofarraylike
  var lengthOfArrayLike$6 = function (obj) {
    return toLength$5(obj.length);
  };

  var toIndexedObject$6 = toIndexedObject$8;
  var toAbsoluteIndex$2 = toAbsoluteIndex$3;
  var lengthOfArrayLike$5 = lengthOfArrayLike$6;

  // `Array.prototype.{ indexOf, includes }` methods implementation
  var createMethod$4 = function (IS_INCLUDES) {
    return function ($this, el, fromIndex) {
      var O = toIndexedObject$6($this);
      var length = lengthOfArrayLike$5(O);
      if (length === 0) return !IS_INCLUDES && -1;
      var index = toAbsoluteIndex$2(fromIndex, length);
      var value;
      // Array#includes uses SameValueZero equality algorithm
      // eslint-disable-next-line no-self-compare -- NaN check
      if (IS_INCLUDES && el !== el) while (length > index) {
        value = O[index++];
        // eslint-disable-next-line no-self-compare -- NaN check
        if (value !== value) return true;
      // Array#indexOf ignores holes, Array#includes - not
      } else for (;length > index; index++) {
        if ((IS_INCLUDES || index in O) && O[index] === el) return IS_INCLUDES || index || 0;
      } return !IS_INCLUDES && -1;
    };
  };

  var arrayIncludes = {
    // `Array.prototype.includes` method
    // https://tc39.es/ecma262/#sec-array.prototype.includes
    includes: createMethod$4(true),
    // `Array.prototype.indexOf` method
    // https://tc39.es/ecma262/#sec-array.prototype.indexof
    indexOf: createMethod$4(false)
  };

  var uncurryThis$r = functionUncurryThis;
  var hasOwn$6 = hasOwnProperty_1;
  var toIndexedObject$5 = toIndexedObject$8;
  var indexOf$1 = arrayIncludes.indexOf;
  var hiddenKeys$2 = hiddenKeys$4;

  var push$5 = uncurryThis$r([].push);

  var objectKeysInternal = function (object, names) {
    var O = toIndexedObject$5(object);
    var i = 0;
    var result = [];
    var key;
    for (key in O) !hasOwn$6(hiddenKeys$2, key) && hasOwn$6(O, key) && push$5(result, key);
    // Don't enum bug & hidden keys
    while (names.length > i) if (hasOwn$6(O, key = names[i++])) {
      ~indexOf$1(result, key) || push$5(result, key);
    }
    return result;
  };

  // IE8- don't enum bug keys
  var enumBugKeys$3 = [
    'constructor',
    'hasOwnProperty',
    'isPrototypeOf',
    'propertyIsEnumerable',
    'toLocaleString',
    'toString',
    'valueOf'
  ];

  var internalObjectKeys$1 = objectKeysInternal;
  var enumBugKeys$2 = enumBugKeys$3;

  var hiddenKeys$1 = enumBugKeys$2.concat('length', 'prototype');

  // `Object.getOwnPropertyNames` method
  // https://tc39.es/ecma262/#sec-object.getownpropertynames
  // eslint-disable-next-line es/no-object-getownpropertynames -- safe
  objectGetOwnPropertyNames.f = Object.getOwnPropertyNames || function getOwnPropertyNames(O) {
    return internalObjectKeys$1(O, hiddenKeys$1);
  };

  var objectGetOwnPropertySymbols = {};

  // eslint-disable-next-line es/no-object-getownpropertysymbols -- safe
  objectGetOwnPropertySymbols.f = Object.getOwnPropertySymbols;

  var getBuiltIn$3 = getBuiltIn$5;
  var uncurryThis$q = functionUncurryThis;
  var getOwnPropertyNamesModule = objectGetOwnPropertyNames;
  var getOwnPropertySymbolsModule$1 = objectGetOwnPropertySymbols;
  var anObject$a = anObject$c;

  var concat$2 = uncurryThis$q([].concat);

  // all object keys, includes non-enumerable and symbols
  var ownKeys$1 = getBuiltIn$3('Reflect', 'ownKeys') || function ownKeys(it) {
    var keys = getOwnPropertyNamesModule.f(anObject$a(it));
    var getOwnPropertySymbols = getOwnPropertySymbolsModule$1.f;
    return getOwnPropertySymbols ? concat$2(keys, getOwnPropertySymbols(it)) : keys;
  };

  var hasOwn$5 = hasOwnProperty_1;
  var ownKeys = ownKeys$1;
  var getOwnPropertyDescriptorModule = objectGetOwnPropertyDescriptor;
  var definePropertyModule$2 = objectDefineProperty;

  var copyConstructorProperties$2 = function (target, source, exceptions) {
    var keys = ownKeys(source);
    var defineProperty = definePropertyModule$2.f;
    var getOwnPropertyDescriptor = getOwnPropertyDescriptorModule.f;
    for (var i = 0; i < keys.length; i++) {
      var key = keys[i];
      if (!hasOwn$5(target, key) && !(exceptions && hasOwn$5(exceptions, key))) {
        defineProperty(target, key, getOwnPropertyDescriptor(source, key));
      }
    }
  };

  var fails$p = fails$x;
  var isCallable$8 = isCallable$i;

  var replacement = /#|\.prototype\./;

  var isForced$3 = function (feature, detection) {
    var value = data[normalize(feature)];
    return value === POLYFILL ? true
      : value === NATIVE ? false
      : isCallable$8(detection) ? fails$p(detection)
      : !!detection;
  };

  var normalize = isForced$3.normalize = function (string) {
    return String(string).replace(replacement, '.').toLowerCase();
  };

  var data = isForced$3.data = {};
  var NATIVE = isForced$3.NATIVE = 'N';
  var POLYFILL = isForced$3.POLYFILL = 'P';

  var isForced_1 = isForced$3;

  var global$b = global$k;
  var getOwnPropertyDescriptor$4 = objectGetOwnPropertyDescriptor.f;
  var createNonEnumerableProperty$5 = createNonEnumerableProperty$7;
  var defineBuiltIn$6 = defineBuiltIn$7;
  var defineGlobalProperty = defineGlobalProperty$3;
  var copyConstructorProperties$1 = copyConstructorProperties$2;
  var isForced$2 = isForced_1;

  /*
    options.target         - name of the target object
    options.global         - target is the global object
    options.stat           - export as static methods of target
    options.proto          - export as prototype methods of target
    options.real           - real prototype method for the `pure` version
    options.forced         - export even if the native feature is available
    options.bind           - bind methods to the target, required for the `pure` version
    options.wrap           - wrap constructors to preventing global pollution, required for the `pure` version
    options.unsafe         - use the simple assignment of property instead of delete + defineProperty
    options.sham           - add a flag to not completely full polyfills
    options.enumerable     - export as enumerable property
    options.dontCallGetSet - prevent calling a getter on target
    options.name           - the .name of the function if it does not match the key
  */
  var _export = function (options, source) {
    var TARGET = options.target;
    var GLOBAL = options.global;
    var STATIC = options.stat;
    var FORCED, target, key, targetProperty, sourceProperty, descriptor;
    if (GLOBAL) {
      target = global$b;
    } else if (STATIC) {
      target = global$b[TARGET] || defineGlobalProperty(TARGET, {});
    } else {
      target = global$b[TARGET] && global$b[TARGET].prototype;
    }
    if (target) for (key in source) {
      sourceProperty = source[key];
      if (options.dontCallGetSet) {
        descriptor = getOwnPropertyDescriptor$4(target, key);
        targetProperty = descriptor && descriptor.value;
      } else targetProperty = target[key];
      FORCED = isForced$2(GLOBAL ? key : TARGET + (STATIC ? '.' : '#') + key, options.forced);
      // contained in target
      if (!FORCED && targetProperty !== undefined) {
        if (typeof sourceProperty == typeof targetProperty) continue;
        copyConstructorProperties$1(sourceProperty, targetProperty);
      }
      // add a flag to not completely full polyfills
      if (options.sham || (targetProperty && targetProperty.sham)) {
        createNonEnumerableProperty$5(sourceProperty, 'sham', true);
      }
      defineBuiltIn$6(target, key, sourceProperty, options);
    }
  };

  var classof$6 = classofRaw$2;

  // `IsArray` abstract operation
  // https://tc39.es/ecma262/#sec-isarray
  // eslint-disable-next-line es/no-array-isarray -- safe
  var isArray$5 = Array.isArray || function isArray(argument) {
    return classof$6(argument) === 'Array';
  };

  var $TypeError$6 = TypeError;
  var MAX_SAFE_INTEGER = 0x1FFFFFFFFFFFFF; // 2 ** 53 - 1 == 9007199254740991

  var doesNotExceedSafeInteger$2 = function (it) {
    if (it > MAX_SAFE_INTEGER) throw $TypeError$6('Maximum allowed index exceeded');
    return it;
  };

  var DESCRIPTORS$8 = descriptors;
  var definePropertyModule$1 = objectDefineProperty;
  var createPropertyDescriptor$1 = createPropertyDescriptor$4;

  var createProperty$3 = function (object, key, value) {
    if (DESCRIPTORS$8) definePropertyModule$1.f(object, key, createPropertyDescriptor$1(0, value));
    else object[key] = value;
  };

  var wellKnownSymbol$h = wellKnownSymbol$j;

  var TO_STRING_TAG$2 = wellKnownSymbol$h('toStringTag');
  var test$2 = {};

  test$2[TO_STRING_TAG$2] = 'z';

  var toStringTagSupport = String(test$2) === '[object z]';

  var TO_STRING_TAG_SUPPORT$2 = toStringTagSupport;
  var isCallable$7 = isCallable$i;
  var classofRaw$1 = classofRaw$2;
  var wellKnownSymbol$g = wellKnownSymbol$j;

  var TO_STRING_TAG$1 = wellKnownSymbol$g('toStringTag');
  var $Object$1 = Object;

  // ES3 wrong here
  var CORRECT_ARGUMENTS = classofRaw$1(function () { return arguments; }()) === 'Arguments';

  // fallback for IE11 Script Access Denied error
  var tryGet = function (it, key) {
    try {
      return it[key];
    } catch (error) { /* empty */ }
  };

  // getting tag from ES6+ `Object.prototype.toString`
  var classof$5 = TO_STRING_TAG_SUPPORT$2 ? classofRaw$1 : function (it) {
    var O, tag, result;
    return it === undefined ? 'Undefined' : it === null ? 'Null'
      // @@toStringTag case
      : typeof (tag = tryGet(O = $Object$1(it), TO_STRING_TAG$1)) == 'string' ? tag
      // builtinTag case
      : CORRECT_ARGUMENTS ? classofRaw$1(O)
      // ES3 arguments fallback
      : (result = classofRaw$1(O)) === 'Object' && isCallable$7(O.callee) ? 'Arguments' : result;
  };

  var uncurryThis$p = functionUncurryThis;
  var fails$o = fails$x;
  var isCallable$6 = isCallable$i;
  var classof$4 = classof$5;
  var getBuiltIn$2 = getBuiltIn$5;
  var inspectSource = inspectSource$2;

  var noop = function () { /* empty */ };
  var construct = getBuiltIn$2('Reflect', 'construct');
  var constructorRegExp = /^\s*(?:class|function)\b/;
  var exec$3 = uncurryThis$p(constructorRegExp.exec);
  var INCORRECT_TO_STRING = !constructorRegExp.test(noop);

  var isConstructorModern = function isConstructor(argument) {
    if (!isCallable$6(argument)) return false;
    try {
      construct(noop, [], argument);
      return true;
    } catch (error) {
      return false;
    }
  };

  var isConstructorLegacy = function isConstructor(argument) {
    if (!isCallable$6(argument)) return false;
    switch (classof$4(argument)) {
      case 'AsyncFunction':
      case 'GeneratorFunction':
      case 'AsyncGeneratorFunction': return false;
    }
    try {
      // we can't check .prototype since constructors produced by .bind haven't it
      // `Function#toString` throws on some built-it function in some legacy engines
      // (for example, `DOMQuad` and similar in FF41-)
      return INCORRECT_TO_STRING || !!exec$3(constructorRegExp, inspectSource(argument));
    } catch (error) {
      return true;
    }
  };

  isConstructorLegacy.sham = true;

  // `IsConstructor` abstract operation
  // https://tc39.es/ecma262/#sec-isconstructor
  var isConstructor$3 = !construct || fails$o(function () {
    var called;
    return isConstructorModern(isConstructorModern.call)
      || !isConstructorModern(Object)
      || !isConstructorModern(function () { called = true; })
      || called;
  }) ? isConstructorLegacy : isConstructorModern;

  var isArray$4 = isArray$5;
  var isConstructor$2 = isConstructor$3;
  var isObject$7 = isObject$d;
  var wellKnownSymbol$f = wellKnownSymbol$j;

  var SPECIES$5 = wellKnownSymbol$f('species');
  var $Array$1 = Array;

  // a part of `ArraySpeciesCreate` abstract operation
  // https://tc39.es/ecma262/#sec-arrayspeciescreate
  var arraySpeciesConstructor$1 = function (originalArray) {
    var C;
    if (isArray$4(originalArray)) {
      C = originalArray.constructor;
      // cross-realm fallback
      if (isConstructor$2(C) && (C === $Array$1 || isArray$4(C.prototype))) C = undefined;
      else if (isObject$7(C)) {
        C = C[SPECIES$5];
        if (C === null) C = undefined;
      }
    } return C === undefined ? $Array$1 : C;
  };

  var arraySpeciesConstructor = arraySpeciesConstructor$1;

  // `ArraySpeciesCreate` abstract operation
  // https://tc39.es/ecma262/#sec-arrayspeciescreate
  var arraySpeciesCreate$3 = function (originalArray, length) {
    return new (arraySpeciesConstructor(originalArray))(length === 0 ? 0 : length);
  };

  var fails$n = fails$x;
  var wellKnownSymbol$e = wellKnownSymbol$j;
  var V8_VERSION$1 = engineV8Version;

  var SPECIES$4 = wellKnownSymbol$e('species');

  var arrayMethodHasSpeciesSupport$5 = function (METHOD_NAME) {
    // We can't use this feature detection in V8 since it causes
    // deoptimization and serious performance degradation
    // https://github.com/zloirock/core-js/issues/677
    return V8_VERSION$1 >= 51 || !fails$n(function () {
      var array = [];
      var constructor = array.constructor = {};
      constructor[SPECIES$4] = function () {
        return { foo: 1 };
      };
      return array[METHOD_NAME](Boolean).foo !== 1;
    });
  };

  var $$p = _export;
  var fails$m = fails$x;
  var isArray$3 = isArray$5;
  var isObject$6 = isObject$d;
  var toObject$9 = toObject$b;
  var lengthOfArrayLike$4 = lengthOfArrayLike$6;
  var doesNotExceedSafeInteger$1 = doesNotExceedSafeInteger$2;
  var createProperty$2 = createProperty$3;
  var arraySpeciesCreate$2 = arraySpeciesCreate$3;
  var arrayMethodHasSpeciesSupport$4 = arrayMethodHasSpeciesSupport$5;
  var wellKnownSymbol$d = wellKnownSymbol$j;
  var V8_VERSION = engineV8Version;

  var IS_CONCAT_SPREADABLE = wellKnownSymbol$d('isConcatSpreadable');

  // We can't use this feature detection in V8 since it causes
  // deoptimization and serious performance degradation
  // https://github.com/zloirock/core-js/issues/679
  var IS_CONCAT_SPREADABLE_SUPPORT = V8_VERSION >= 51 || !fails$m(function () {
    var array = [];
    array[IS_CONCAT_SPREADABLE] = false;
    return array.concat()[0] !== array;
  });

  var isConcatSpreadable = function (O) {
    if (!isObject$6(O)) return false;
    var spreadable = O[IS_CONCAT_SPREADABLE];
    return spreadable !== undefined ? !!spreadable : isArray$3(O);
  };

  var FORCED$7 = !IS_CONCAT_SPREADABLE_SUPPORT || !arrayMethodHasSpeciesSupport$4('concat');

  // `Array.prototype.concat` method
  // https://tc39.es/ecma262/#sec-array.prototype.concat
  // with adding support of @@isConcatSpreadable and @@species
  $$p({ target: 'Array', proto: true, arity: 1, forced: FORCED$7 }, {
    // eslint-disable-next-line no-unused-vars -- required for `.length`
    concat: function concat(arg) {
      var O = toObject$9(this);
      var A = arraySpeciesCreate$2(O, 0);
      var n = 0;
      var i, k, length, len, E;
      for (i = -1, length = arguments.length; i < length; i++) {
        E = i === -1 ? O : arguments[i];
        if (isConcatSpreadable(E)) {
          len = lengthOfArrayLike$4(E);
          doesNotExceedSafeInteger$1(n + len);
          for (k = 0; k < len; k++, n++) if (k in E) createProperty$2(A, n, E[k]);
        } else {
          doesNotExceedSafeInteger$1(n + 1);
          createProperty$2(A, n++, E);
        }
      }
      A.length = n;
      return A;
    }
  });

  var classofRaw = classofRaw$2;
  var uncurryThis$o = functionUncurryThis;

  var functionUncurryThisClause = function (fn) {
    // Nashorn bug:
    //   https://github.com/zloirock/core-js/issues/1128
    //   https://github.com/zloirock/core-js/issues/1130
    if (classofRaw(fn) === 'Function') return uncurryThis$o(fn);
  };

  var uncurryThis$n = functionUncurryThisClause;
  var aCallable$2 = aCallable$4;
  var NATIVE_BIND$1 = functionBindNative;

  var bind$1 = uncurryThis$n(uncurryThis$n.bind);

  // optional / simple context binding
  var functionBindContext = function (fn, that) {
    aCallable$2(fn);
    return that === undefined ? fn : NATIVE_BIND$1 ? bind$1(fn, that) : function (/* ...args */) {
      return fn.apply(that, arguments);
    };
  };

  var bind = functionBindContext;
  var uncurryThis$m = functionUncurryThis;
  var IndexedObject$2 = indexedObject;
  var toObject$8 = toObject$b;
  var lengthOfArrayLike$3 = lengthOfArrayLike$6;
  var arraySpeciesCreate$1 = arraySpeciesCreate$3;

  var push$4 = uncurryThis$m([].push);

  // `Array.prototype.{ forEach, map, filter, some, every, find, findIndex, filterReject }` methods implementation
  var createMethod$3 = function (TYPE) {
    var IS_MAP = TYPE === 1;
    var IS_FILTER = TYPE === 2;
    var IS_SOME = TYPE === 3;
    var IS_EVERY = TYPE === 4;
    var IS_FIND_INDEX = TYPE === 6;
    var IS_FILTER_REJECT = TYPE === 7;
    var NO_HOLES = TYPE === 5 || IS_FIND_INDEX;
    return function ($this, callbackfn, that, specificCreate) {
      var O = toObject$8($this);
      var self = IndexedObject$2(O);
      var length = lengthOfArrayLike$3(self);
      var boundFunction = bind(callbackfn, that);
      var index = 0;
      var create = specificCreate || arraySpeciesCreate$1;
      var target = IS_MAP ? create($this, length) : IS_FILTER || IS_FILTER_REJECT ? create($this, 0) : undefined;
      var value, result;
      for (;length > index; index++) if (NO_HOLES || index in self) {
        value = self[index];
        result = boundFunction(value, index, O);
        if (TYPE) {
          if (IS_MAP) target[index] = result; // map
          else if (result) switch (TYPE) {
            case 3: return true;              // some
            case 5: return value;             // find
            case 6: return index;             // findIndex
            case 2: push$4(target, value);      // filter
          } else switch (TYPE) {
            case 4: return false;             // every
            case 7: push$4(target, value);      // filterReject
          }
        }
      }
      return IS_FIND_INDEX ? -1 : IS_SOME || IS_EVERY ? IS_EVERY : target;
    };
  };

  var arrayIteration = {
    // `Array.prototype.forEach` method
    // https://tc39.es/ecma262/#sec-array.prototype.foreach
    forEach: createMethod$3(0),
    // `Array.prototype.map` method
    // https://tc39.es/ecma262/#sec-array.prototype.map
    map: createMethod$3(1),
    // `Array.prototype.filter` method
    // https://tc39.es/ecma262/#sec-array.prototype.filter
    filter: createMethod$3(2),
    // `Array.prototype.some` method
    // https://tc39.es/ecma262/#sec-array.prototype.some
    some: createMethod$3(3),
    // `Array.prototype.every` method
    // https://tc39.es/ecma262/#sec-array.prototype.every
    every: createMethod$3(4),
    // `Array.prototype.find` method
    // https://tc39.es/ecma262/#sec-array.prototype.find
    find: createMethod$3(5),
    // `Array.prototype.findIndex` method
    // https://tc39.es/ecma262/#sec-array.prototype.findIndex
    findIndex: createMethod$3(6),
    // `Array.prototype.filterReject` method
    // https://github.com/tc39/proposal-array-filtering
    filterReject: createMethod$3(7)
  };

  var $$o = _export;
  var $filter = arrayIteration.filter;
  var arrayMethodHasSpeciesSupport$3 = arrayMethodHasSpeciesSupport$5;

  var HAS_SPECIES_SUPPORT$3 = arrayMethodHasSpeciesSupport$3('filter');

  // `Array.prototype.filter` method
  // https://tc39.es/ecma262/#sec-array.prototype.filter
  // with adding support of @@species
  $$o({ target: 'Array', proto: true, forced: !HAS_SPECIES_SUPPORT$3 }, {
    filter: function filter(callbackfn /* , thisArg */) {
      return $filter(this, callbackfn, arguments.length > 1 ? arguments[1] : undefined);
    }
  });

  var objectDefineProperties = {};

  var internalObjectKeys = objectKeysInternal;
  var enumBugKeys$1 = enumBugKeys$3;

  // `Object.keys` method
  // https://tc39.es/ecma262/#sec-object.keys
  // eslint-disable-next-line es/no-object-keys -- safe
  var objectKeys$3 = Object.keys || function keys(O) {
    return internalObjectKeys(O, enumBugKeys$1);
  };

  var DESCRIPTORS$7 = descriptors;
  var V8_PROTOTYPE_DEFINE_BUG = v8PrototypeDefineBug;
  var definePropertyModule = objectDefineProperty;
  var anObject$9 = anObject$c;
  var toIndexedObject$4 = toIndexedObject$8;
  var objectKeys$2 = objectKeys$3;

  // `Object.defineProperties` method
  // https://tc39.es/ecma262/#sec-object.defineproperties
  // eslint-disable-next-line es/no-object-defineproperties -- safe
  objectDefineProperties.f = DESCRIPTORS$7 && !V8_PROTOTYPE_DEFINE_BUG ? Object.defineProperties : function defineProperties(O, Properties) {
    anObject$9(O);
    var props = toIndexedObject$4(Properties);
    var keys = objectKeys$2(Properties);
    var length = keys.length;
    var index = 0;
    var key;
    while (length > index) definePropertyModule.f(O, key = keys[index++], props[key]);
    return O;
  };

  var getBuiltIn$1 = getBuiltIn$5;

  var html$1 = getBuiltIn$1('document', 'documentElement');

  /* global ActiveXObject -- old IE, WSH */
  var anObject$8 = anObject$c;
  var definePropertiesModule = objectDefineProperties;
  var enumBugKeys = enumBugKeys$3;
  var hiddenKeys = hiddenKeys$4;
  var html = html$1;
  var documentCreateElement$1 = documentCreateElement$2;
  var sharedKey$1 = sharedKey$3;

  var GT = '>';
  var LT = '<';
  var PROTOTYPE = 'prototype';
  var SCRIPT = 'script';
  var IE_PROTO$1 = sharedKey$1('IE_PROTO');

  var EmptyConstructor = function () { /* empty */ };

  var scriptTag = function (content) {
    return LT + SCRIPT + GT + content + LT + '/' + SCRIPT + GT;
  };

  // Create object with fake `null` prototype: use ActiveX Object with cleared prototype
  var NullProtoObjectViaActiveX = function (activeXDocument) {
    activeXDocument.write(scriptTag(''));
    activeXDocument.close();
    var temp = activeXDocument.parentWindow.Object;
    activeXDocument = null; // avoid memory leak
    return temp;
  };

  // Create object with fake `null` prototype: use iframe Object with cleared prototype
  var NullProtoObjectViaIFrame = function () {
    // Thrash, waste and sodomy: IE GC bug
    var iframe = documentCreateElement$1('iframe');
    var JS = 'java' + SCRIPT + ':';
    var iframeDocument;
    iframe.style.display = 'none';
    html.appendChild(iframe);
    // https://github.com/zloirock/core-js/issues/475
    iframe.src = String(JS);
    iframeDocument = iframe.contentWindow.document;
    iframeDocument.open();
    iframeDocument.write(scriptTag('document.F=Object'));
    iframeDocument.close();
    return iframeDocument.F;
  };

  // Check for document.domain and active x support
  // No need to use active x approach when document.domain is not set
  // see https://github.com/es-shims/es5-shim/issues/150
  // variation of https://github.com/kitcambridge/es5-shim/commit/4f738ac066346
  // avoid IE GC bug
  var activeXDocument;
  var NullProtoObject = function () {
    try {
      activeXDocument = new ActiveXObject('htmlfile');
    } catch (error) { /* ignore */ }
    NullProtoObject = typeof document != 'undefined'
      ? document.domain && activeXDocument
        ? NullProtoObjectViaActiveX(activeXDocument) // old IE
        : NullProtoObjectViaIFrame()
      : NullProtoObjectViaActiveX(activeXDocument); // WSH
    var length = enumBugKeys.length;
    while (length--) delete NullProtoObject[PROTOTYPE][enumBugKeys[length]];
    return NullProtoObject();
  };

  hiddenKeys[IE_PROTO$1] = true;

  // `Object.create` method
  // https://tc39.es/ecma262/#sec-object.create
  // eslint-disable-next-line es/no-object-create -- safe
  var objectCreate = Object.create || function create(O, Properties) {
    var result;
    if (O !== null) {
      EmptyConstructor[PROTOTYPE] = anObject$8(O);
      result = new EmptyConstructor();
      EmptyConstructor[PROTOTYPE] = null;
      // add "__proto__" for Object.getPrototypeOf polyfill
      result[IE_PROTO$1] = O;
    } else result = NullProtoObject();
    return Properties === undefined ? result : definePropertiesModule.f(result, Properties);
  };

  var wellKnownSymbol$c = wellKnownSymbol$j;
  var create$3 = objectCreate;
  var defineProperty$6 = objectDefineProperty.f;

  var UNSCOPABLES = wellKnownSymbol$c('unscopables');
  var ArrayPrototype = Array.prototype;

  // Array.prototype[@@unscopables]
  // https://tc39.es/ecma262/#sec-array.prototype-@@unscopables
  if (ArrayPrototype[UNSCOPABLES] === undefined) {
    defineProperty$6(ArrayPrototype, UNSCOPABLES, {
      configurable: true,
      value: create$3(null)
    });
  }

  // add a key to Array.prototype[@@unscopables]
  var addToUnscopables$4 = function (key) {
    ArrayPrototype[UNSCOPABLES][key] = true;
  };

  var $$n = _export;
  var $find = arrayIteration.find;
  var addToUnscopables$3 = addToUnscopables$4;

  var FIND = 'find';
  var SKIPS_HOLES$1 = true;

  // Shouldn't skip holes
  // eslint-disable-next-line es/no-array-prototype-find -- testing
  if (FIND in []) Array(1)[FIND](function () { SKIPS_HOLES$1 = false; });

  // `Array.prototype.find` method
  // https://tc39.es/ecma262/#sec-array.prototype.find
  $$n({ target: 'Array', proto: true, forced: SKIPS_HOLES$1 }, {
    find: function find(callbackfn /* , that = undefined */) {
      return $find(this, callbackfn, arguments.length > 1 ? arguments[1] : undefined);
    }
  });

  // https://tc39.es/ecma262/#sec-array.prototype-@@unscopables
  addToUnscopables$3(FIND);

  var $$m = _export;
  var $findIndex = arrayIteration.findIndex;
  var addToUnscopables$2 = addToUnscopables$4;

  var FIND_INDEX = 'findIndex';
  var SKIPS_HOLES = true;

  // Shouldn't skip holes
  // eslint-disable-next-line es/no-array-prototype-findindex -- testing
  if (FIND_INDEX in []) Array(1)[FIND_INDEX](function () { SKIPS_HOLES = false; });

  // `Array.prototype.findIndex` method
  // https://tc39.es/ecma262/#sec-array.prototype.findindex
  $$m({ target: 'Array', proto: true, forced: SKIPS_HOLES }, {
    findIndex: function findIndex(callbackfn /* , that = undefined */) {
      return $findIndex(this, callbackfn, arguments.length > 1 ? arguments[1] : undefined);
    }
  });

  // https://tc39.es/ecma262/#sec-array.prototype-@@unscopables
  addToUnscopables$2(FIND_INDEX);

  var $$l = _export;
  var $includes = arrayIncludes.includes;
  var fails$l = fails$x;
  var addToUnscopables$1 = addToUnscopables$4;

  // FF99+ bug
  var BROKEN_ON_SPARSE = fails$l(function () {
    // eslint-disable-next-line es/no-array-prototype-includes -- detection
    return !Array(1).includes();
  });

  // `Array.prototype.includes` method
  // https://tc39.es/ecma262/#sec-array.prototype.includes
  $$l({ target: 'Array', proto: true, forced: BROKEN_ON_SPARSE }, {
    includes: function includes(el /* , fromIndex = 0 */) {
      return $includes(this, el, arguments.length > 1 ? arguments[1] : undefined);
    }
  });

  // https://tc39.es/ecma262/#sec-array.prototype-@@unscopables
  addToUnscopables$1('includes');

  var fails$k = fails$x;

  var arrayMethodIsStrict$4 = function (METHOD_NAME, argument) {
    var method = [][METHOD_NAME];
    return !!method && fails$k(function () {
      // eslint-disable-next-line no-useless-call -- required for testing
      method.call(null, argument || function () { return 1; }, 1);
    });
  };

  /* eslint-disable es/no-array-prototype-indexof -- required for testing */
  var $$k = _export;
  var uncurryThis$l = functionUncurryThisClause;
  var $indexOf = arrayIncludes.indexOf;
  var arrayMethodIsStrict$3 = arrayMethodIsStrict$4;

  var nativeIndexOf = uncurryThis$l([].indexOf);

  var NEGATIVE_ZERO = !!nativeIndexOf && 1 / nativeIndexOf([1], 1, -0) < 0;
  var FORCED$6 = NEGATIVE_ZERO || !arrayMethodIsStrict$3('indexOf');

  // `Array.prototype.indexOf` method
  // https://tc39.es/ecma262/#sec-array.prototype.indexof
  $$k({ target: 'Array', proto: true, forced: FORCED$6 }, {
    indexOf: function indexOf(searchElement /* , fromIndex = 0 */) {
      var fromIndex = arguments.length > 1 ? arguments[1] : undefined;
      return NEGATIVE_ZERO
        // convert -0 to +0
        ? nativeIndexOf(this, searchElement, fromIndex) || 0
        : $indexOf(this, searchElement, fromIndex);
    }
  });

  var iterators = {};

  var fails$j = fails$x;

  var correctPrototypeGetter = !fails$j(function () {
    function F() { /* empty */ }
    F.prototype.constructor = null;
    // eslint-disable-next-line es/no-object-getprototypeof -- required for testing
    return Object.getPrototypeOf(new F()) !== F.prototype;
  });

  var hasOwn$4 = hasOwnProperty_1;
  var isCallable$5 = isCallable$i;
  var toObject$7 = toObject$b;
  var sharedKey = sharedKey$3;
  var CORRECT_PROTOTYPE_GETTER$1 = correctPrototypeGetter;

  var IE_PROTO = sharedKey('IE_PROTO');
  var $Object = Object;
  var ObjectPrototype = $Object.prototype;

  // `Object.getPrototypeOf` method
  // https://tc39.es/ecma262/#sec-object.getprototypeof
  // eslint-disable-next-line es/no-object-getprototypeof -- safe
  var objectGetPrototypeOf$1 = CORRECT_PROTOTYPE_GETTER$1 ? $Object.getPrototypeOf : function (O) {
    var object = toObject$7(O);
    if (hasOwn$4(object, IE_PROTO)) return object[IE_PROTO];
    var constructor = object.constructor;
    if (isCallable$5(constructor) && object instanceof constructor) {
      return constructor.prototype;
    } return object instanceof $Object ? ObjectPrototype : null;
  };

  var fails$i = fails$x;
  var isCallable$4 = isCallable$i;
  var isObject$5 = isObject$d;
  var getPrototypeOf$1 = objectGetPrototypeOf$1;
  var defineBuiltIn$5 = defineBuiltIn$7;
  var wellKnownSymbol$b = wellKnownSymbol$j;

  var ITERATOR$4 = wellKnownSymbol$b('iterator');
  var BUGGY_SAFARI_ITERATORS$1 = false;

  // `%IteratorPrototype%` object
  // https://tc39.es/ecma262/#sec-%iteratorprototype%-object
  var IteratorPrototype$2, PrototypeOfArrayIteratorPrototype, arrayIterator;

  /* eslint-disable es/no-array-prototype-keys -- safe */
  if ([].keys) {
    arrayIterator = [].keys();
    // Safari 8 has buggy iterators w/o `next`
    if (!('next' in arrayIterator)) BUGGY_SAFARI_ITERATORS$1 = true;
    else {
      PrototypeOfArrayIteratorPrototype = getPrototypeOf$1(getPrototypeOf$1(arrayIterator));
      if (PrototypeOfArrayIteratorPrototype !== Object.prototype) IteratorPrototype$2 = PrototypeOfArrayIteratorPrototype;
    }
  }

  var NEW_ITERATOR_PROTOTYPE = !isObject$5(IteratorPrototype$2) || fails$i(function () {
    var test = {};
    // FF44- legacy iterators case
    return IteratorPrototype$2[ITERATOR$4].call(test) !== test;
  });

  if (NEW_ITERATOR_PROTOTYPE) IteratorPrototype$2 = {};

  // `%IteratorPrototype%[@@iterator]()` method
  // https://tc39.es/ecma262/#sec-%iteratorprototype%-@@iterator
  if (!isCallable$4(IteratorPrototype$2[ITERATOR$4])) {
    defineBuiltIn$5(IteratorPrototype$2, ITERATOR$4, function () {
      return this;
    });
  }

  var iteratorsCore = {
    IteratorPrototype: IteratorPrototype$2,
    BUGGY_SAFARI_ITERATORS: BUGGY_SAFARI_ITERATORS$1
  };

  var defineProperty$5 = objectDefineProperty.f;
  var hasOwn$3 = hasOwnProperty_1;
  var wellKnownSymbol$a = wellKnownSymbol$j;

  var TO_STRING_TAG = wellKnownSymbol$a('toStringTag');

  var setToStringTag$3 = function (target, TAG, STATIC) {
    if (target && !STATIC) target = target.prototype;
    if (target && !hasOwn$3(target, TO_STRING_TAG)) {
      defineProperty$5(target, TO_STRING_TAG, { configurable: true, value: TAG });
    }
  };

  var IteratorPrototype$1 = iteratorsCore.IteratorPrototype;
  var create$2 = objectCreate;
  var createPropertyDescriptor = createPropertyDescriptor$4;
  var setToStringTag$2 = setToStringTag$3;
  var Iterators$2 = iterators;

  var returnThis$1 = function () { return this; };

  var iteratorCreateConstructor = function (IteratorConstructor, NAME, next, ENUMERABLE_NEXT) {
    var TO_STRING_TAG = NAME + ' Iterator';
    IteratorConstructor.prototype = create$2(IteratorPrototype$1, { next: createPropertyDescriptor(+!ENUMERABLE_NEXT, next) });
    setToStringTag$2(IteratorConstructor, TO_STRING_TAG, false);
    Iterators$2[TO_STRING_TAG] = returnThis$1;
    return IteratorConstructor;
  };

  var uncurryThis$k = functionUncurryThis;
  var aCallable$1 = aCallable$4;

  var functionUncurryThisAccessor = function (object, key, method) {
    try {
      // eslint-disable-next-line es/no-object-getownpropertydescriptor -- safe
      return uncurryThis$k(aCallable$1(Object.getOwnPropertyDescriptor(object, key)[method]));
    } catch (error) { /* empty */ }
  };

  var isObject$4 = isObject$d;

  var isPossiblePrototype$1 = function (argument) {
    return isObject$4(argument) || argument === null;
  };

  var isPossiblePrototype = isPossiblePrototype$1;

  var $String$1 = String;
  var $TypeError$5 = TypeError;

  var aPossiblePrototype$1 = function (argument) {
    if (isPossiblePrototype(argument)) return argument;
    throw new $TypeError$5("Can't set " + $String$1(argument) + ' as a prototype');
  };

  /* eslint-disable no-proto -- safe */
  var uncurryThisAccessor = functionUncurryThisAccessor;
  var isObject$3 = isObject$d;
  var requireObjectCoercible$9 = requireObjectCoercible$c;
  var aPossiblePrototype = aPossiblePrototype$1;

  // `Object.setPrototypeOf` method
  // https://tc39.es/ecma262/#sec-object.setprototypeof
  // Works with __proto__ only. Old v8 can't work with null proto objects.
  // eslint-disable-next-line es/no-object-setprototypeof -- safe
  var objectSetPrototypeOf = Object.setPrototypeOf || ('__proto__' in {} ? function () {
    var CORRECT_SETTER = false;
    var test = {};
    var setter;
    try {
      setter = uncurryThisAccessor(Object.prototype, '__proto__', 'set');
      setter(test, []);
      CORRECT_SETTER = test instanceof Array;
    } catch (error) { /* empty */ }
    return function setPrototypeOf(O, proto) {
      requireObjectCoercible$9(O);
      aPossiblePrototype(proto);
      if (!isObject$3(O)) return O;
      if (CORRECT_SETTER) setter(O, proto);
      else O.__proto__ = proto;
      return O;
    };
  }() : undefined);

  var $$j = _export;
  var call$a = functionCall;
  var FunctionName = functionName;
  var isCallable$3 = isCallable$i;
  var createIteratorConstructor = iteratorCreateConstructor;
  var getPrototypeOf = objectGetPrototypeOf$1;
  var setPrototypeOf$1 = objectSetPrototypeOf;
  var setToStringTag$1 = setToStringTag$3;
  var createNonEnumerableProperty$4 = createNonEnumerableProperty$7;
  var defineBuiltIn$4 = defineBuiltIn$7;
  var wellKnownSymbol$9 = wellKnownSymbol$j;
  var Iterators$1 = iterators;
  var IteratorsCore = iteratorsCore;

  var PROPER_FUNCTION_NAME$2 = FunctionName.PROPER;
  var CONFIGURABLE_FUNCTION_NAME = FunctionName.CONFIGURABLE;
  var IteratorPrototype = IteratorsCore.IteratorPrototype;
  var BUGGY_SAFARI_ITERATORS = IteratorsCore.BUGGY_SAFARI_ITERATORS;
  var ITERATOR$3 = wellKnownSymbol$9('iterator');
  var KEYS = 'keys';
  var VALUES = 'values';
  var ENTRIES = 'entries';

  var returnThis = function () { return this; };

  var iteratorDefine = function (Iterable, NAME, IteratorConstructor, next, DEFAULT, IS_SET, FORCED) {
    createIteratorConstructor(IteratorConstructor, NAME, next);

    var getIterationMethod = function (KIND) {
      if (KIND === DEFAULT && defaultIterator) return defaultIterator;
      if (!BUGGY_SAFARI_ITERATORS && KIND && KIND in IterablePrototype) return IterablePrototype[KIND];

      switch (KIND) {
        case KEYS: return function keys() { return new IteratorConstructor(this, KIND); };
        case VALUES: return function values() { return new IteratorConstructor(this, KIND); };
        case ENTRIES: return function entries() { return new IteratorConstructor(this, KIND); };
      }

      return function () { return new IteratorConstructor(this); };
    };

    var TO_STRING_TAG = NAME + ' Iterator';
    var INCORRECT_VALUES_NAME = false;
    var IterablePrototype = Iterable.prototype;
    var nativeIterator = IterablePrototype[ITERATOR$3]
      || IterablePrototype['@@iterator']
      || DEFAULT && IterablePrototype[DEFAULT];
    var defaultIterator = !BUGGY_SAFARI_ITERATORS && nativeIterator || getIterationMethod(DEFAULT);
    var anyNativeIterator = NAME === 'Array' ? IterablePrototype.entries || nativeIterator : nativeIterator;
    var CurrentIteratorPrototype, methods, KEY;

    // fix native
    if (anyNativeIterator) {
      CurrentIteratorPrototype = getPrototypeOf(anyNativeIterator.call(new Iterable()));
      if (CurrentIteratorPrototype !== Object.prototype && CurrentIteratorPrototype.next) {
        if (getPrototypeOf(CurrentIteratorPrototype) !== IteratorPrototype) {
          if (setPrototypeOf$1) {
            setPrototypeOf$1(CurrentIteratorPrototype, IteratorPrototype);
          } else if (!isCallable$3(CurrentIteratorPrototype[ITERATOR$3])) {
            defineBuiltIn$4(CurrentIteratorPrototype, ITERATOR$3, returnThis);
          }
        }
        // Set @@toStringTag to native iterators
        setToStringTag$1(CurrentIteratorPrototype, TO_STRING_TAG, true);
      }
    }

    // fix Array.prototype.{ values, @@iterator }.name in V8 / FF
    if (PROPER_FUNCTION_NAME$2 && DEFAULT === VALUES && nativeIterator && nativeIterator.name !== VALUES) {
      if (CONFIGURABLE_FUNCTION_NAME) {
        createNonEnumerableProperty$4(IterablePrototype, 'name', VALUES);
      } else {
        INCORRECT_VALUES_NAME = true;
        defaultIterator = function values() { return call$a(nativeIterator, this); };
      }
    }

    // export additional methods
    if (DEFAULT) {
      methods = {
        values: getIterationMethod(VALUES),
        keys: IS_SET ? defaultIterator : getIterationMethod(KEYS),
        entries: getIterationMethod(ENTRIES)
      };
      if (FORCED) for (KEY in methods) {
        if (BUGGY_SAFARI_ITERATORS || INCORRECT_VALUES_NAME || !(KEY in IterablePrototype)) {
          defineBuiltIn$4(IterablePrototype, KEY, methods[KEY]);
        }
      } else $$j({ target: NAME, proto: true, forced: BUGGY_SAFARI_ITERATORS || INCORRECT_VALUES_NAME }, methods);
    }

    // define iterator
    if (IterablePrototype[ITERATOR$3] !== defaultIterator) {
      defineBuiltIn$4(IterablePrototype, ITERATOR$3, defaultIterator, { name: DEFAULT });
    }
    Iterators$1[NAME] = defaultIterator;

    return methods;
  };

  // `CreateIterResultObject` abstract operation
  // https://tc39.es/ecma262/#sec-createiterresultobject
  var createIterResultObject$1 = function (value, done) {
    return { value: value, done: done };
  };

  var toIndexedObject$3 = toIndexedObject$8;
  var addToUnscopables = addToUnscopables$4;
  var Iterators = iterators;
  var InternalStateModule = internalState;
  var defineProperty$4 = objectDefineProperty.f;
  var defineIterator = iteratorDefine;
  var createIterResultObject = createIterResultObject$1;
  var DESCRIPTORS$6 = descriptors;

  var ARRAY_ITERATOR = 'Array Iterator';
  var setInternalState = InternalStateModule.set;
  var getInternalState$1 = InternalStateModule.getterFor(ARRAY_ITERATOR);

  // `Array.prototype.entries` method
  // https://tc39.es/ecma262/#sec-array.prototype.entries
  // `Array.prototype.keys` method
  // https://tc39.es/ecma262/#sec-array.prototype.keys
  // `Array.prototype.values` method
  // https://tc39.es/ecma262/#sec-array.prototype.values
  // `Array.prototype[@@iterator]` method
  // https://tc39.es/ecma262/#sec-array.prototype-@@iterator
  // `CreateArrayIterator` internal method
  // https://tc39.es/ecma262/#sec-createarrayiterator
  var es_array_iterator = defineIterator(Array, 'Array', function (iterated, kind) {
    setInternalState(this, {
      type: ARRAY_ITERATOR,
      target: toIndexedObject$3(iterated), // target
      index: 0,                          // next index
      kind: kind                         // kind
    });
  // `%ArrayIteratorPrototype%.next` method
  // https://tc39.es/ecma262/#sec-%arrayiteratorprototype%.next
  }, function () {
    var state = getInternalState$1(this);
    var target = state.target;
    var index = state.index++;
    if (!target || index >= target.length) {
      state.target = undefined;
      return createIterResultObject(undefined, true);
    }
    switch (state.kind) {
      case 'keys': return createIterResultObject(index, false);
      case 'values': return createIterResultObject(target[index], false);
    } return createIterResultObject([index, target[index]], false);
  }, 'values');

  // argumentsList[@@iterator] is %ArrayProto_values%
  // https://tc39.es/ecma262/#sec-createunmappedargumentsobject
  // https://tc39.es/ecma262/#sec-createmappedargumentsobject
  var values = Iterators.Arguments = Iterators.Array;

  // https://tc39.es/ecma262/#sec-array.prototype-@@unscopables
  addToUnscopables('keys');
  addToUnscopables('values');
  addToUnscopables('entries');

  // V8 ~ Chrome 45- bug
  if (DESCRIPTORS$6 && values.name !== 'values') try {
    defineProperty$4(values, 'name', { value: 'values' });
  } catch (error) { /* empty */ }

  var $$i = _export;
  var uncurryThis$j = functionUncurryThis;
  var IndexedObject$1 = indexedObject;
  var toIndexedObject$2 = toIndexedObject$8;
  var arrayMethodIsStrict$2 = arrayMethodIsStrict$4;

  var nativeJoin = uncurryThis$j([].join);

  var ES3_STRINGS = IndexedObject$1 !== Object;
  var FORCED$5 = ES3_STRINGS || !arrayMethodIsStrict$2('join', ',');

  // `Array.prototype.join` method
  // https://tc39.es/ecma262/#sec-array.prototype.join
  $$i({ target: 'Array', proto: true, forced: FORCED$5 }, {
    join: function join(separator) {
      return nativeJoin(toIndexedObject$2(this), separator === undefined ? ',' : separator);
    }
  });

  var $$h = _export;
  var $map = arrayIteration.map;
  var arrayMethodHasSpeciesSupport$2 = arrayMethodHasSpeciesSupport$5;

  var HAS_SPECIES_SUPPORT$2 = arrayMethodHasSpeciesSupport$2('map');

  // `Array.prototype.map` method
  // https://tc39.es/ecma262/#sec-array.prototype.map
  // with adding support of @@species
  $$h({ target: 'Array', proto: true, forced: !HAS_SPECIES_SUPPORT$2 }, {
    map: function map(callbackfn /* , thisArg */) {
      return $map(this, callbackfn, arguments.length > 1 ? arguments[1] : undefined);
    }
  });

  var $$g = _export;
  var uncurryThis$i = functionUncurryThis;
  var isArray$2 = isArray$5;

  var nativeReverse = uncurryThis$i([].reverse);
  var test$1 = [1, 2];

  // `Array.prototype.reverse` method
  // https://tc39.es/ecma262/#sec-array.prototype.reverse
  // fix for Safari 12.0 bug
  // https://bugs.webkit.org/show_bug.cgi?id=188794
  $$g({ target: 'Array', proto: true, forced: String(test$1) === String(test$1.reverse()) }, {
    reverse: function reverse() {
      // eslint-disable-next-line no-self-assign -- dirty hack
      if (isArray$2(this)) this.length = this.length;
      return nativeReverse(this);
    }
  });

  var uncurryThis$h = functionUncurryThis;

  var arraySlice$1 = uncurryThis$h([].slice);

  var $$f = _export;
  var isArray$1 = isArray$5;
  var isConstructor$1 = isConstructor$3;
  var isObject$2 = isObject$d;
  var toAbsoluteIndex$1 = toAbsoluteIndex$3;
  var lengthOfArrayLike$2 = lengthOfArrayLike$6;
  var toIndexedObject$1 = toIndexedObject$8;
  var createProperty$1 = createProperty$3;
  var wellKnownSymbol$8 = wellKnownSymbol$j;
  var arrayMethodHasSpeciesSupport$1 = arrayMethodHasSpeciesSupport$5;
  var nativeSlice = arraySlice$1;

  var HAS_SPECIES_SUPPORT$1 = arrayMethodHasSpeciesSupport$1('slice');

  var SPECIES$3 = wellKnownSymbol$8('species');
  var $Array = Array;
  var max$2 = Math.max;

  // `Array.prototype.slice` method
  // https://tc39.es/ecma262/#sec-array.prototype.slice
  // fallback for not array-like ES3 strings and DOM objects
  $$f({ target: 'Array', proto: true, forced: !HAS_SPECIES_SUPPORT$1 }, {
    slice: function slice(start, end) {
      var O = toIndexedObject$1(this);
      var length = lengthOfArrayLike$2(O);
      var k = toAbsoluteIndex$1(start, length);
      var fin = toAbsoluteIndex$1(end === undefined ? length : end, length);
      // inline `ArraySpeciesCreate` for usage native `Array#slice` where it's possible
      var Constructor, result, n;
      if (isArray$1(O)) {
        Constructor = O.constructor;
        // cross-realm fallback
        if (isConstructor$1(Constructor) && (Constructor === $Array || isArray$1(Constructor.prototype))) {
          Constructor = undefined;
        } else if (isObject$2(Constructor)) {
          Constructor = Constructor[SPECIES$3];
          if (Constructor === null) Constructor = undefined;
        }
        if (Constructor === $Array || Constructor === undefined) {
          return nativeSlice(O, k, fin);
        }
      }
      result = new (Constructor === undefined ? $Array : Constructor)(max$2(fin - k, 0));
      for (n = 0; k < fin; k++, n++) if (k in O) createProperty$1(result, n, O[k]);
      result.length = n;
      return result;
    }
  });

  var tryToString$1 = tryToString$3;

  var $TypeError$4 = TypeError;

  var deletePropertyOrThrow$2 = function (O, P) {
    if (!delete O[P]) throw new $TypeError$4('Cannot delete property ' + tryToString$1(P) + ' of ' + tryToString$1(O));
  };

  var classof$3 = classof$5;

  var $String = String;

  var toString$f = function (argument) {
    if (classof$3(argument) === 'Symbol') throw new TypeError('Cannot convert a Symbol value to a string');
    return $String(argument);
  };

  var arraySlice = arraySlice$1;

  var floor$1 = Math.floor;

  var sort = function (array, comparefn) {
    var length = array.length;

    if (length < 8) {
      // insertion sort
      var i = 1;
      var element, j;

      while (i < length) {
        j = i;
        element = array[i];
        while (j && comparefn(array[j - 1], element) > 0) {
          array[j] = array[--j];
        }
        if (j !== i++) array[j] = element;
      }
    } else {
      // merge sort
      var middle = floor$1(length / 2);
      var left = sort(arraySlice(array, 0, middle), comparefn);
      var right = sort(arraySlice(array, middle), comparefn);
      var llength = left.length;
      var rlength = right.length;
      var lindex = 0;
      var rindex = 0;

      while (lindex < llength || rindex < rlength) {
        array[lindex + rindex] = (lindex < llength && rindex < rlength)
          ? comparefn(left[lindex], right[rindex]) <= 0 ? left[lindex++] : right[rindex++]
          : lindex < llength ? left[lindex++] : right[rindex++];
      }
    }

    return array;
  };

  var arraySort = sort;

  var userAgent$1 = engineUserAgent;

  var firefox = userAgent$1.match(/firefox\/(\d+)/i);

  var engineFfVersion = !!firefox && +firefox[1];

  var UA = engineUserAgent;

  var engineIsIeOrEdge = /MSIE|Trident/.test(UA);

  var userAgent = engineUserAgent;

  var webkit = userAgent.match(/AppleWebKit\/(\d+)\./);

  var engineWebkitVersion = !!webkit && +webkit[1];

  var $$e = _export;
  var uncurryThis$g = functionUncurryThis;
  var aCallable = aCallable$4;
  var toObject$6 = toObject$b;
  var lengthOfArrayLike$1 = lengthOfArrayLike$6;
  var deletePropertyOrThrow$1 = deletePropertyOrThrow$2;
  var toString$e = toString$f;
  var fails$h = fails$x;
  var internalSort = arraySort;
  var arrayMethodIsStrict$1 = arrayMethodIsStrict$4;
  var FF = engineFfVersion;
  var IE_OR_EDGE = engineIsIeOrEdge;
  var V8 = engineV8Version;
  var WEBKIT = engineWebkitVersion;

  var test = [];
  var nativeSort = uncurryThis$g(test.sort);
  var push$3 = uncurryThis$g(test.push);

  // IE8-
  var FAILS_ON_UNDEFINED = fails$h(function () {
    test.sort(undefined);
  });
  // V8 bug
  var FAILS_ON_NULL = fails$h(function () {
    test.sort(null);
  });
  // Old WebKit
  var STRICT_METHOD$1 = arrayMethodIsStrict$1('sort');

  var STABLE_SORT = !fails$h(function () {
    // feature detection can be too slow, so check engines versions
    if (V8) return V8 < 70;
    if (FF && FF > 3) return;
    if (IE_OR_EDGE) return true;
    if (WEBKIT) return WEBKIT < 603;

    var result = '';
    var code, chr, value, index;

    // generate an array with more 512 elements (Chakra and old V8 fails only in this case)
    for (code = 65; code < 76; code++) {
      chr = String.fromCharCode(code);

      switch (code) {
        case 66: case 69: case 70: case 72: value = 3; break;
        case 68: case 71: value = 4; break;
        default: value = 2;
      }

      for (index = 0; index < 47; index++) {
        test.push({ k: chr + index, v: value });
      }
    }

    test.sort(function (a, b) { return b.v - a.v; });

    for (index = 0; index < test.length; index++) {
      chr = test[index].k.charAt(0);
      if (result.charAt(result.length - 1) !== chr) result += chr;
    }

    return result !== 'DGBEFHACIJK';
  });

  var FORCED$4 = FAILS_ON_UNDEFINED || !FAILS_ON_NULL || !STRICT_METHOD$1 || !STABLE_SORT;

  var getSortCompare = function (comparefn) {
    return function (x, y) {
      if (y === undefined) return -1;
      if (x === undefined) return 1;
      if (comparefn !== undefined) return +comparefn(x, y) || 0;
      return toString$e(x) > toString$e(y) ? 1 : -1;
    };
  };

  // `Array.prototype.sort` method
  // https://tc39.es/ecma262/#sec-array.prototype.sort
  $$e({ target: 'Array', proto: true, forced: FORCED$4 }, {
    sort: function sort(comparefn) {
      if (comparefn !== undefined) aCallable(comparefn);

      var array = toObject$6(this);

      if (STABLE_SORT) return comparefn === undefined ? nativeSort(array) : nativeSort(array, comparefn);

      var items = [];
      var arrayLength = lengthOfArrayLike$1(array);
      var itemsLength, index;

      for (index = 0; index < arrayLength; index++) {
        if (index in array) push$3(items, array[index]);
      }

      internalSort(items, getSortCompare(comparefn));

      itemsLength = lengthOfArrayLike$1(items);
      index = 0;

      while (index < itemsLength) array[index] = items[index++];
      while (index < arrayLength) deletePropertyOrThrow$1(array, index++);

      return array;
    }
  });

  var DESCRIPTORS$5 = descriptors;
  var isArray = isArray$5;

  var $TypeError$3 = TypeError;
  // eslint-disable-next-line es/no-object-getownpropertydescriptor -- safe
  var getOwnPropertyDescriptor$3 = Object.getOwnPropertyDescriptor;

  // Safari < 13 does not throw an error in this case
  var SILENT_ON_NON_WRITABLE_LENGTH_SET = DESCRIPTORS$5 && !function () {
    // makes no sense without proper strict mode support
    if (this !== undefined) return true;
    try {
      // eslint-disable-next-line es/no-object-defineproperty -- safe
      Object.defineProperty([], 'length', { writable: false }).length = 1;
    } catch (error) {
      return error instanceof TypeError;
    }
  }();

  var arraySetLength = SILENT_ON_NON_WRITABLE_LENGTH_SET ? function (O, length) {
    if (isArray(O) && !getOwnPropertyDescriptor$3(O, 'length').writable) {
      throw new $TypeError$3('Cannot set read only .length');
    } return O.length = length;
  } : function (O, length) {
    return O.length = length;
  };

  var $$d = _export;
  var toObject$5 = toObject$b;
  var toAbsoluteIndex = toAbsoluteIndex$3;
  var toIntegerOrInfinity$2 = toIntegerOrInfinity$5;
  var lengthOfArrayLike = lengthOfArrayLike$6;
  var setArrayLength = arraySetLength;
  var doesNotExceedSafeInteger = doesNotExceedSafeInteger$2;
  var arraySpeciesCreate = arraySpeciesCreate$3;
  var createProperty = createProperty$3;
  var deletePropertyOrThrow = deletePropertyOrThrow$2;
  var arrayMethodHasSpeciesSupport = arrayMethodHasSpeciesSupport$5;

  var HAS_SPECIES_SUPPORT = arrayMethodHasSpeciesSupport('splice');

  var max$1 = Math.max;
  var min$4 = Math.min;

  // `Array.prototype.splice` method
  // https://tc39.es/ecma262/#sec-array.prototype.splice
  // with adding support of @@species
  $$d({ target: 'Array', proto: true, forced: !HAS_SPECIES_SUPPORT }, {
    splice: function splice(start, deleteCount /* , ...items */) {
      var O = toObject$5(this);
      var len = lengthOfArrayLike(O);
      var actualStart = toAbsoluteIndex(start, len);
      var argumentsLength = arguments.length;
      var insertCount, actualDeleteCount, A, k, from, to;
      if (argumentsLength === 0) {
        insertCount = actualDeleteCount = 0;
      } else if (argumentsLength === 1) {
        insertCount = 0;
        actualDeleteCount = len - actualStart;
      } else {
        insertCount = argumentsLength - 2;
        actualDeleteCount = min$4(max$1(toIntegerOrInfinity$2(deleteCount), 0), len - actualStart);
      }
      doesNotExceedSafeInteger(len + insertCount - actualDeleteCount);
      A = arraySpeciesCreate(O, actualDeleteCount);
      for (k = 0; k < actualDeleteCount; k++) {
        from = actualStart + k;
        if (from in O) createProperty(A, k, O[from]);
      }
      A.length = actualDeleteCount;
      if (insertCount < actualDeleteCount) {
        for (k = actualStart; k < len - actualDeleteCount; k++) {
          from = k + actualDeleteCount;
          to = k + insertCount;
          if (from in O) O[to] = O[from];
          else deletePropertyOrThrow(O, to);
        }
        for (k = len; k > len - actualDeleteCount + insertCount; k--) deletePropertyOrThrow(O, k - 1);
      } else if (insertCount > actualDeleteCount) {
        for (k = len - actualDeleteCount; k > actualStart; k--) {
          from = k + actualDeleteCount - 1;
          to = k + insertCount - 1;
          if (from in O) O[to] = O[from];
          else deletePropertyOrThrow(O, to);
        }
      }
      for (k = 0; k < insertCount; k++) {
        O[k + actualStart] = arguments[k + 2];
      }
      setArrayLength(O, len - actualDeleteCount + insertCount);
      return A;
    }
  });

  var $$c = _export;
  var fails$g = fails$x;
  var toObject$4 = toObject$b;
  var toPrimitive$1 = toPrimitive$3;

  var FORCED$3 = fails$g(function () {
    return new Date(NaN).toJSON() !== null
      || Date.prototype.toJSON.call({ toISOString: function () { return 1; } }) !== 1;
  });

  // `Date.prototype.toJSON` method
  // https://tc39.es/ecma262/#sec-date.prototype.tojson
  $$c({ target: 'Date', proto: true, arity: 1, forced: FORCED$3 }, {
    // eslint-disable-next-line no-unused-vars -- required for `.length`
    toJSON: function toJSON(key) {
      var O = toObject$4(this);
      var pv = toPrimitive$1(O, 'number');
      return typeof pv == 'number' && !isFinite(pv) ? null : O.toISOString();
    }
  });

  var global$a = global$k;

  var path$1 = global$a;

  var isCallable$2 = isCallable$i;
  var isObject$1 = isObject$d;
  var setPrototypeOf = objectSetPrototypeOf;

  // makes subclassing work correct for wrapped built-ins
  var inheritIfRequired$2 = function ($this, dummy, Wrapper) {
    var NewTarget, NewTargetPrototype;
    if (
      // it can work only with native `setPrototypeOf`
      setPrototypeOf &&
      // we haven't completely correct pre-ES6 way for getting `new.target`, so use this
      isCallable$2(NewTarget = dummy.constructor) &&
      NewTarget !== Wrapper &&
      isObject$1(NewTargetPrototype = NewTarget.prototype) &&
      NewTargetPrototype !== Wrapper.prototype
    ) setPrototypeOf($this, NewTargetPrototype);
    return $this;
  };

  var uncurryThis$f = functionUncurryThis;

  // `thisNumberValue` abstract operation
  // https://tc39.es/ecma262/#sec-thisnumbervalue
  var thisNumberValue$1 = uncurryThis$f(1.0.valueOf);

  // a string of all valid unicode whitespaces
  var whitespaces$4 = '\u0009\u000A\u000B\u000C\u000D\u0020\u00A0\u1680\u2000\u2001\u2002' +
    '\u2003\u2004\u2005\u2006\u2007\u2008\u2009\u200A\u202F\u205F\u3000\u2028\u2029\uFEFF';

  var uncurryThis$e = functionUncurryThis;
  var requireObjectCoercible$8 = requireObjectCoercible$c;
  var toString$d = toString$f;
  var whitespaces$3 = whitespaces$4;

  var replace$3 = uncurryThis$e(''.replace);
  var ltrim = RegExp('^[' + whitespaces$3 + ']+');
  var rtrim = RegExp('(^|[^' + whitespaces$3 + '])[' + whitespaces$3 + ']+$');

  // `String.prototype.{ trim, trimStart, trimEnd, trimLeft, trimRight }` methods implementation
  var createMethod$2 = function (TYPE) {
    return function ($this) {
      var string = toString$d(requireObjectCoercible$8($this));
      if (TYPE & 1) string = replace$3(string, ltrim, '');
      if (TYPE & 2) string = replace$3(string, rtrim, '$1');
      return string;
    };
  };

  var stringTrim = {
    // `String.prototype.{ trimLeft, trimStart }` methods
    // https://tc39.es/ecma262/#sec-string.prototype.trimstart
    start: createMethod$2(1),
    // `String.prototype.{ trimRight, trimEnd }` methods
    // https://tc39.es/ecma262/#sec-string.prototype.trimend
    end: createMethod$2(2),
    // `String.prototype.trim` method
    // https://tc39.es/ecma262/#sec-string.prototype.trim
    trim: createMethod$2(3)
  };

  var $$b = _export;
  var IS_PURE = isPure;
  var DESCRIPTORS$4 = descriptors;
  var global$9 = global$k;
  var path = path$1;
  var uncurryThis$d = functionUncurryThis;
  var isForced$1 = isForced_1;
  var hasOwn$2 = hasOwnProperty_1;
  var inheritIfRequired$1 = inheritIfRequired$2;
  var isPrototypeOf$2 = objectIsPrototypeOf;
  var isSymbol = isSymbol$3;
  var toPrimitive = toPrimitive$3;
  var fails$f = fails$x;
  var getOwnPropertyNames$1 = objectGetOwnPropertyNames.f;
  var getOwnPropertyDescriptor$2 = objectGetOwnPropertyDescriptor.f;
  var defineProperty$3 = objectDefineProperty.f;
  var thisNumberValue = thisNumberValue$1;
  var trim$2 = stringTrim.trim;

  var NUMBER = 'Number';
  var NativeNumber = global$9[NUMBER];
  path[NUMBER];
  var NumberPrototype = NativeNumber.prototype;
  var TypeError$1 = global$9.TypeError;
  var stringSlice$7 = uncurryThis$d(''.slice);
  var charCodeAt$1 = uncurryThis$d(''.charCodeAt);

  // `ToNumeric` abstract operation
  // https://tc39.es/ecma262/#sec-tonumeric
  var toNumeric = function (value) {
    var primValue = toPrimitive(value, 'number');
    return typeof primValue == 'bigint' ? primValue : toNumber(primValue);
  };

  // `ToNumber` abstract operation
  // https://tc39.es/ecma262/#sec-tonumber
  var toNumber = function (argument) {
    var it = toPrimitive(argument, 'number');
    var first, third, radix, maxCode, digits, length, index, code;
    if (isSymbol(it)) throw new TypeError$1('Cannot convert a Symbol value to a number');
    if (typeof it == 'string' && it.length > 2) {
      it = trim$2(it);
      first = charCodeAt$1(it, 0);
      if (first === 43 || first === 45) {
        third = charCodeAt$1(it, 2);
        if (third === 88 || third === 120) return NaN; // Number('+0x1') should be NaN, old V8 fix
      } else if (first === 48) {
        switch (charCodeAt$1(it, 1)) {
          // fast equal of /^0b[01]+$/i
          case 66:
          case 98:
            radix = 2;
            maxCode = 49;
            break;
          // fast equal of /^0o[0-7]+$/i
          case 79:
          case 111:
            radix = 8;
            maxCode = 55;
            break;
          default:
            return +it;
        }
        digits = stringSlice$7(it, 2);
        length = digits.length;
        for (index = 0; index < length; index++) {
          code = charCodeAt$1(digits, index);
          // parseInt parses a string to a first unavailable symbol
          // but ToNumber should return NaN if a string contains unavailable symbols
          if (code < 48 || code > maxCode) return NaN;
        } return parseInt(digits, radix);
      }
    } return +it;
  };

  var FORCED$2 = isForced$1(NUMBER, !NativeNumber(' 0o1') || !NativeNumber('0b1') || NativeNumber('+0x1'));

  var calledWithNew = function (dummy) {
    // includes check on 1..constructor(foo) case
    return isPrototypeOf$2(NumberPrototype, dummy) && fails$f(function () { thisNumberValue(dummy); });
  };

  // `Number` constructor
  // https://tc39.es/ecma262/#sec-number-constructor
  var NumberWrapper = function Number(value) {
    var n = arguments.length < 1 ? 0 : NativeNumber(toNumeric(value));
    return calledWithNew(this) ? inheritIfRequired$1(Object(n), this, NumberWrapper) : n;
  };

  NumberWrapper.prototype = NumberPrototype;
  if (FORCED$2 && !IS_PURE) NumberPrototype.constructor = NumberWrapper;

  $$b({ global: true, constructor: true, wrap: true, forced: FORCED$2 }, {
    Number: NumberWrapper
  });

  // Use `internal/copy-constructor-properties` helper in `core-js@4`
  var copyConstructorProperties = function (target, source) {
    for (var keys = DESCRIPTORS$4 ? getOwnPropertyNames$1(source) : (
      // ES3:
      'MAX_VALUE,MIN_VALUE,NaN,NEGATIVE_INFINITY,POSITIVE_INFINITY,' +
      // ES2015 (in case, if modules with ES2015 Number statics required before):
      'EPSILON,MAX_SAFE_INTEGER,MIN_SAFE_INTEGER,isFinite,isInteger,isNaN,isSafeInteger,parseFloat,parseInt,' +
      // ESNext
      'fromString,range'
    ).split(','), j = 0, key; keys.length > j; j++) {
      if (hasOwn$2(source, key = keys[j]) && !hasOwn$2(target, key)) {
        defineProperty$3(target, key, getOwnPropertyDescriptor$2(source, key));
      }
    }
  };
  if (FORCED$2 || IS_PURE) copyConstructorProperties(path[NUMBER], NativeNumber);

  var DESCRIPTORS$3 = descriptors;
  var uncurryThis$c = functionUncurryThis;
  var call$9 = functionCall;
  var fails$e = fails$x;
  var objectKeys$1 = objectKeys$3;
  var getOwnPropertySymbolsModule = objectGetOwnPropertySymbols;
  var propertyIsEnumerableModule = objectPropertyIsEnumerable;
  var toObject$3 = toObject$b;
  var IndexedObject = indexedObject;

  // eslint-disable-next-line es/no-object-assign -- safe
  var $assign = Object.assign;
  // eslint-disable-next-line es/no-object-defineproperty -- required for testing
  var defineProperty$2 = Object.defineProperty;
  var concat$1 = uncurryThis$c([].concat);

  // `Object.assign` method
  // https://tc39.es/ecma262/#sec-object.assign
  var objectAssign = !$assign || fails$e(function () {
    // should have correct order of operations (Edge bug)
    if (DESCRIPTORS$3 && $assign({ b: 1 }, $assign(defineProperty$2({}, 'a', {
      enumerable: true,
      get: function () {
        defineProperty$2(this, 'b', {
          value: 3,
          enumerable: false
        });
      }
    }), { b: 2 })).b !== 1) return true;
    // should work with symbols and should have deterministic property order (V8 bug)
    var A = {};
    var B = {};
    // eslint-disable-next-line es/no-symbol -- safe
    var symbol = Symbol('assign detection');
    var alphabet = 'abcdefghijklmnopqrst';
    A[symbol] = 7;
    alphabet.split('').forEach(function (chr) { B[chr] = chr; });
    return $assign({}, A)[symbol] !== 7 || objectKeys$1($assign({}, B)).join('') !== alphabet;
  }) ? function assign(target, source) { // eslint-disable-line no-unused-vars -- required for `.length`
    var T = toObject$3(target);
    var argumentsLength = arguments.length;
    var index = 1;
    var getOwnPropertySymbols = getOwnPropertySymbolsModule.f;
    var propertyIsEnumerable = propertyIsEnumerableModule.f;
    while (argumentsLength > index) {
      var S = IndexedObject(arguments[index++]);
      var keys = getOwnPropertySymbols ? concat$1(objectKeys$1(S), getOwnPropertySymbols(S)) : objectKeys$1(S);
      var length = keys.length;
      var j = 0;
      var key;
      while (length > j) {
        key = keys[j++];
        if (!DESCRIPTORS$3 || call$9(propertyIsEnumerable, S, key)) T[key] = S[key];
      }
    } return T;
  } : $assign;

  var $$a = _export;
  var assign = objectAssign;

  // `Object.assign` method
  // https://tc39.es/ecma262/#sec-object.assign
  // eslint-disable-next-line es/no-object-assign -- required for testing
  $$a({ target: 'Object', stat: true, arity: 2, forced: Object.assign !== assign }, {
    assign: assign
  });

  var DESCRIPTORS$2 = descriptors;
  var fails$d = fails$x;
  var uncurryThis$b = functionUncurryThis;
  var objectGetPrototypeOf = objectGetPrototypeOf$1;
  var objectKeys = objectKeys$3;
  var toIndexedObject = toIndexedObject$8;
  var $propertyIsEnumerable = objectPropertyIsEnumerable.f;

  var propertyIsEnumerable = uncurryThis$b($propertyIsEnumerable);
  var push$2 = uncurryThis$b([].push);

  // in some IE versions, `propertyIsEnumerable` returns incorrect result on integer keys
  // of `null` prototype objects
  var IE_BUG = DESCRIPTORS$2 && fails$d(function () {
    // eslint-disable-next-line es/no-object-create -- safe
    var O = Object.create(null);
    O[2] = 2;
    return !propertyIsEnumerable(O, 2);
  });

  // `Object.{ entries, values }` methods implementation
  var createMethod$1 = function (TO_ENTRIES) {
    return function (it) {
      var O = toIndexedObject(it);
      var keys = objectKeys(O);
      var IE_WORKAROUND = IE_BUG && objectGetPrototypeOf(O) === null;
      var length = keys.length;
      var i = 0;
      var result = [];
      var key;
      while (length > i) {
        key = keys[i++];
        if (!DESCRIPTORS$2 || (IE_WORKAROUND ? key in O : propertyIsEnumerable(O, key))) {
          push$2(result, TO_ENTRIES ? [key, O[key]] : O[key]);
        }
      }
      return result;
    };
  };

  var objectToArray = {
    // `Object.entries` method
    // https://tc39.es/ecma262/#sec-object.entries
    entries: createMethod$1(true),
    // `Object.values` method
    // https://tc39.es/ecma262/#sec-object.values
    values: createMethod$1(false)
  };

  var $$9 = _export;
  var $entries = objectToArray.entries;

  // `Object.entries` method
  // https://tc39.es/ecma262/#sec-object.entries
  $$9({ target: 'Object', stat: true }, {
    entries: function entries(O) {
      return $entries(O);
    }
  });

  var $$8 = _export;
  var toObject$2 = toObject$b;
  var nativeKeys = objectKeys$3;
  var fails$c = fails$x;

  var FAILS_ON_PRIMITIVES$1 = fails$c(function () { nativeKeys(1); });

  // `Object.keys` method
  // https://tc39.es/ecma262/#sec-object.keys
  $$8({ target: 'Object', stat: true, forced: FAILS_ON_PRIMITIVES$1 }, {
    keys: function keys(it) {
      return nativeKeys(toObject$2(it));
    }
  });

  var TO_STRING_TAG_SUPPORT$1 = toStringTagSupport;
  var classof$2 = classof$5;

  // `Object.prototype.toString` method implementation
  // https://tc39.es/ecma262/#sec-object.prototype.tostring
  var objectToString = TO_STRING_TAG_SUPPORT$1 ? {}.toString : function toString() {
    return '[object ' + classof$2(this) + ']';
  };

  var TO_STRING_TAG_SUPPORT = toStringTagSupport;
  var defineBuiltIn$3 = defineBuiltIn$7;
  var toString$c = objectToString;

  // `Object.prototype.toString` method
  // https://tc39.es/ecma262/#sec-object.prototype.tostring
  if (!TO_STRING_TAG_SUPPORT) {
    defineBuiltIn$3(Object.prototype, 'toString', toString$c, { unsafe: true });
  }

  var global$8 = global$k;
  var fails$b = fails$x;
  var uncurryThis$a = functionUncurryThis;
  var toString$b = toString$f;
  var trim$1 = stringTrim.trim;
  var whitespaces$2 = whitespaces$4;

  var charAt$5 = uncurryThis$a(''.charAt);
  var $parseFloat$1 = global$8.parseFloat;
  var Symbol$2 = global$8.Symbol;
  var ITERATOR$2 = Symbol$2 && Symbol$2.iterator;
  var FORCED$1 = 1 / $parseFloat$1(whitespaces$2 + '-0') !== -Infinity
    // MS Edge 18- broken with boxed symbols
    || (ITERATOR$2 && !fails$b(function () { $parseFloat$1(Object(ITERATOR$2)); }));

  // `parseFloat` method
  // https://tc39.es/ecma262/#sec-parsefloat-string
  var numberParseFloat = FORCED$1 ? function parseFloat(string) {
    var trimmedString = trim$1(toString$b(string));
    var result = $parseFloat$1(trimmedString);
    return result === 0 && charAt$5(trimmedString, 0) === '-' ? -0 : result;
  } : $parseFloat$1;

  var $$7 = _export;
  var $parseFloat = numberParseFloat;

  // `parseFloat` method
  // https://tc39.es/ecma262/#sec-parsefloat-string
  $$7({ global: true, forced: parseFloat !== $parseFloat }, {
    parseFloat: $parseFloat
  });

  var global$7 = global$k;
  var fails$a = fails$x;
  var uncurryThis$9 = functionUncurryThis;
  var toString$a = toString$f;
  var trim = stringTrim.trim;
  var whitespaces$1 = whitespaces$4;

  var $parseInt$1 = global$7.parseInt;
  var Symbol$1 = global$7.Symbol;
  var ITERATOR$1 = Symbol$1 && Symbol$1.iterator;
  var hex = /^[+-]?0x/i;
  var exec$2 = uncurryThis$9(hex.exec);
  var FORCED = $parseInt$1(whitespaces$1 + '08') !== 8 || $parseInt$1(whitespaces$1 + '0x16') !== 22
    // MS Edge 18- broken with boxed symbols
    || (ITERATOR$1 && !fails$a(function () { $parseInt$1(Object(ITERATOR$1)); }));

  // `parseInt` method
  // https://tc39.es/ecma262/#sec-parseint-string-radix
  var numberParseInt = FORCED ? function parseInt(string, radix) {
    var S = trim(toString$a(string));
    return $parseInt$1(S, (radix >>> 0) || (exec$2(hex, S) ? 16 : 10));
  } : $parseInt$1;

  var $$6 = _export;
  var $parseInt = numberParseInt;

  // `parseInt` method
  // https://tc39.es/ecma262/#sec-parseint-string-radix
  $$6({ global: true, forced: parseInt !== $parseInt }, {
    parseInt: $parseInt
  });

  var isObject = isObject$d;
  var classof$1 = classofRaw$2;
  var wellKnownSymbol$7 = wellKnownSymbol$j;

  var MATCH$2 = wellKnownSymbol$7('match');

  // `IsRegExp` abstract operation
  // https://tc39.es/ecma262/#sec-isregexp
  var isRegexp = function (it) {
    var isRegExp;
    return isObject(it) && ((isRegExp = it[MATCH$2]) !== undefined ? !!isRegExp : classof$1(it) === 'RegExp');
  };

  var anObject$7 = anObject$c;

  // `RegExp.prototype.flags` getter implementation
  // https://tc39.es/ecma262/#sec-get-regexp.prototype.flags
  var regexpFlags$1 = function () {
    var that = anObject$7(this);
    var result = '';
    if (that.hasIndices) result += 'd';
    if (that.global) result += 'g';
    if (that.ignoreCase) result += 'i';
    if (that.multiline) result += 'm';
    if (that.dotAll) result += 's';
    if (that.unicode) result += 'u';
    if (that.unicodeSets) result += 'v';
    if (that.sticky) result += 'y';
    return result;
  };

  var call$8 = functionCall;
  var hasOwn$1 = hasOwnProperty_1;
  var isPrototypeOf$1 = objectIsPrototypeOf;
  var regExpFlags = regexpFlags$1;

  var RegExpPrototype$3 = RegExp.prototype;

  var regexpGetFlags = function (R) {
    var flags = R.flags;
    return flags === undefined && !('flags' in RegExpPrototype$3) && !hasOwn$1(R, 'flags') && isPrototypeOf$1(RegExpPrototype$3, R)
      ? call$8(regExpFlags, R) : flags;
  };

  var fails$9 = fails$x;
  var global$6 = global$k;

  // babel-minify and Closure Compiler transpiles RegExp('a', 'y') -> /a/y and it causes SyntaxError
  var $RegExp$2 = global$6.RegExp;

  var UNSUPPORTED_Y$3 = fails$9(function () {
    var re = $RegExp$2('a', 'y');
    re.lastIndex = 2;
    return re.exec('abcd') !== null;
  });

  // UC Browser bug
  // https://github.com/zloirock/core-js/issues/1008
  var MISSED_STICKY$1 = UNSUPPORTED_Y$3 || fails$9(function () {
    return !$RegExp$2('a', 'y').sticky;
  });

  var BROKEN_CARET = UNSUPPORTED_Y$3 || fails$9(function () {
    // https://bugzilla.mozilla.org/show_bug.cgi?id=773687
    var re = $RegExp$2('^r', 'gy');
    re.lastIndex = 2;
    return re.exec('str') !== null;
  });

  var regexpStickyHelpers = {
    BROKEN_CARET: BROKEN_CARET,
    MISSED_STICKY: MISSED_STICKY$1,
    UNSUPPORTED_Y: UNSUPPORTED_Y$3
  };

  var defineProperty$1 = objectDefineProperty.f;

  var proxyAccessor$1 = function (Target, Source, key) {
    key in Target || defineProperty$1(Target, key, {
      configurable: true,
      get: function () { return Source[key]; },
      set: function (it) { Source[key] = it; }
    });
  };

  var makeBuiltIn = makeBuiltInExports;
  var defineProperty = objectDefineProperty;

  var defineBuiltInAccessor$1 = function (target, name, descriptor) {
    if (descriptor.get) makeBuiltIn(descriptor.get, name, { getter: true });
    if (descriptor.set) makeBuiltIn(descriptor.set, name, { setter: true });
    return defineProperty.f(target, name, descriptor);
  };

  var getBuiltIn = getBuiltIn$5;
  var defineBuiltInAccessor = defineBuiltInAccessor$1;
  var wellKnownSymbol$6 = wellKnownSymbol$j;
  var DESCRIPTORS$1 = descriptors;

  var SPECIES$2 = wellKnownSymbol$6('species');

  var setSpecies$1 = function (CONSTRUCTOR_NAME) {
    var Constructor = getBuiltIn(CONSTRUCTOR_NAME);

    if (DESCRIPTORS$1 && Constructor && !Constructor[SPECIES$2]) {
      defineBuiltInAccessor(Constructor, SPECIES$2, {
        configurable: true,
        get: function () { return this; }
      });
    }
  };

  var fails$8 = fails$x;
  var global$5 = global$k;

  // babel-minify and Closure Compiler transpiles RegExp('.', 's') -> /./s and it causes SyntaxError
  var $RegExp$1 = global$5.RegExp;

  var regexpUnsupportedDotAll = fails$8(function () {
    var re = $RegExp$1('.', 's');
    return !(re.dotAll && re.test('\n') && re.flags === 's');
  });

  var fails$7 = fails$x;
  var global$4 = global$k;

  // babel-minify and Closure Compiler transpiles RegExp('(?<a>b)', 'g') -> /(?<a>b)/g and it causes SyntaxError
  var $RegExp = global$4.RegExp;

  var regexpUnsupportedNcg = fails$7(function () {
    var re = $RegExp('(?<a>b)', 'g');
    return re.exec('b').groups.a !== 'b' ||
      'b'.replace(re, '$<a>c') !== 'bc';
  });

  var DESCRIPTORS = descriptors;
  var global$3 = global$k;
  var uncurryThis$8 = functionUncurryThis;
  var isForced = isForced_1;
  var inheritIfRequired = inheritIfRequired$2;
  var createNonEnumerableProperty$3 = createNonEnumerableProperty$7;
  var create$1 = objectCreate;
  var getOwnPropertyNames = objectGetOwnPropertyNames.f;
  var isPrototypeOf = objectIsPrototypeOf;
  var isRegExp$1 = isRegexp;
  var toString$9 = toString$f;
  var getRegExpFlags$1 = regexpGetFlags;
  var stickyHelpers$2 = regexpStickyHelpers;
  var proxyAccessor = proxyAccessor$1;
  var defineBuiltIn$2 = defineBuiltIn$7;
  var fails$6 = fails$x;
  var hasOwn = hasOwnProperty_1;
  var enforceInternalState = internalState.enforce;
  var setSpecies = setSpecies$1;
  var wellKnownSymbol$5 = wellKnownSymbol$j;
  var UNSUPPORTED_DOT_ALL$1 = regexpUnsupportedDotAll;
  var UNSUPPORTED_NCG$1 = regexpUnsupportedNcg;

  var MATCH$1 = wellKnownSymbol$5('match');
  var NativeRegExp = global$3.RegExp;
  var RegExpPrototype$2 = NativeRegExp.prototype;
  var SyntaxError = global$3.SyntaxError;
  var exec$1 = uncurryThis$8(RegExpPrototype$2.exec);
  var charAt$4 = uncurryThis$8(''.charAt);
  var replace$2 = uncurryThis$8(''.replace);
  var stringIndexOf$2 = uncurryThis$8(''.indexOf);
  var stringSlice$6 = uncurryThis$8(''.slice);
  // TODO: Use only proper RegExpIdentifierName
  var IS_NCG = /^\?<[^\s\d!#%&*+<=>@^][^\s!#%&*+<=>@^]*>/;
  var re1 = /a/g;
  var re2 = /a/g;

  // "new" should create a new object, old webkit bug
  var CORRECT_NEW = new NativeRegExp(re1) !== re1;

  var MISSED_STICKY = stickyHelpers$2.MISSED_STICKY;
  var UNSUPPORTED_Y$2 = stickyHelpers$2.UNSUPPORTED_Y;

  var BASE_FORCED = DESCRIPTORS &&
    (!CORRECT_NEW || MISSED_STICKY || UNSUPPORTED_DOT_ALL$1 || UNSUPPORTED_NCG$1 || fails$6(function () {
      re2[MATCH$1] = false;
      // RegExp constructor can alter flags and IsRegExp works correct with @@match
      return NativeRegExp(re1) !== re1 || NativeRegExp(re2) === re2 || String(NativeRegExp(re1, 'i')) !== '/a/i';
    }));

  var handleDotAll = function (string) {
    var length = string.length;
    var index = 0;
    var result = '';
    var brackets = false;
    var chr;
    for (; index <= length; index++) {
      chr = charAt$4(string, index);
      if (chr === '\\') {
        result += chr + charAt$4(string, ++index);
        continue;
      }
      if (!brackets && chr === '.') {
        result += '[\\s\\S]';
      } else {
        if (chr === '[') {
          brackets = true;
        } else if (chr === ']') {
          brackets = false;
        } result += chr;
      }
    } return result;
  };

  var handleNCG = function (string) {
    var length = string.length;
    var index = 0;
    var result = '';
    var named = [];
    var names = create$1(null);
    var brackets = false;
    var ncg = false;
    var groupid = 0;
    var groupname = '';
    var chr;
    for (; index <= length; index++) {
      chr = charAt$4(string, index);
      if (chr === '\\') {
        chr += charAt$4(string, ++index);
      } else if (chr === ']') {
        brackets = false;
      } else if (!brackets) switch (true) {
        case chr === '[':
          brackets = true;
          break;
        case chr === '(':
          if (exec$1(IS_NCG, stringSlice$6(string, index + 1))) {
            index += 2;
            ncg = true;
          }
          result += chr;
          groupid++;
          continue;
        case chr === '>' && ncg:
          if (groupname === '' || hasOwn(names, groupname)) {
            throw new SyntaxError('Invalid capture group name');
          }
          names[groupname] = true;
          named[named.length] = [groupname, groupid];
          ncg = false;
          groupname = '';
          continue;
      }
      if (ncg) groupname += chr;
      else result += chr;
    } return [result, named];
  };

  // `RegExp` constructor
  // https://tc39.es/ecma262/#sec-regexp-constructor
  if (isForced('RegExp', BASE_FORCED)) {
    var RegExpWrapper = function RegExp(pattern, flags) {
      var thisIsRegExp = isPrototypeOf(RegExpPrototype$2, this);
      var patternIsRegExp = isRegExp$1(pattern);
      var flagsAreUndefined = flags === undefined;
      var groups = [];
      var rawPattern = pattern;
      var rawFlags, dotAll, sticky, handled, result, state;

      if (!thisIsRegExp && patternIsRegExp && flagsAreUndefined && pattern.constructor === RegExpWrapper) {
        return pattern;
      }

      if (patternIsRegExp || isPrototypeOf(RegExpPrototype$2, pattern)) {
        pattern = pattern.source;
        if (flagsAreUndefined) flags = getRegExpFlags$1(rawPattern);
      }

      pattern = pattern === undefined ? '' : toString$9(pattern);
      flags = flags === undefined ? '' : toString$9(flags);
      rawPattern = pattern;

      if (UNSUPPORTED_DOT_ALL$1 && 'dotAll' in re1) {
        dotAll = !!flags && stringIndexOf$2(flags, 's') > -1;
        if (dotAll) flags = replace$2(flags, /s/g, '');
      }

      rawFlags = flags;

      if (MISSED_STICKY && 'sticky' in re1) {
        sticky = !!flags && stringIndexOf$2(flags, 'y') > -1;
        if (sticky && UNSUPPORTED_Y$2) flags = replace$2(flags, /y/g, '');
      }

      if (UNSUPPORTED_NCG$1) {
        handled = handleNCG(pattern);
        pattern = handled[0];
        groups = handled[1];
      }

      result = inheritIfRequired(NativeRegExp(pattern, flags), thisIsRegExp ? this : RegExpPrototype$2, RegExpWrapper);

      if (dotAll || sticky || groups.length) {
        state = enforceInternalState(result);
        if (dotAll) {
          state.dotAll = true;
          state.raw = RegExpWrapper(handleDotAll(pattern), rawFlags);
        }
        if (sticky) state.sticky = true;
        if (groups.length) state.groups = groups;
      }

      if (pattern !== rawPattern) try {
        // fails in old engines, but we have no alternatives for unsupported regex syntax
        createNonEnumerableProperty$3(result, 'source', rawPattern === '' ? '(?:)' : rawPattern);
      } catch (error) { /* empty */ }

      return result;
    };

    for (var keys = getOwnPropertyNames(NativeRegExp), index = 0; keys.length > index;) {
      proxyAccessor(RegExpWrapper, NativeRegExp, keys[index++]);
    }

    RegExpPrototype$2.constructor = RegExpWrapper;
    RegExpWrapper.prototype = RegExpPrototype$2;
    defineBuiltIn$2(global$3, 'RegExp', RegExpWrapper, { constructor: true });
  }

  // https://tc39.es/ecma262/#sec-get-regexp-@@species
  setSpecies('RegExp');

  /* eslint-disable regexp/no-empty-capturing-group, regexp/no-empty-group, regexp/no-lazy-ends -- testing */
  /* eslint-disable regexp/no-useless-quantifier -- testing */
  var call$7 = functionCall;
  var uncurryThis$7 = functionUncurryThis;
  var toString$8 = toString$f;
  var regexpFlags = regexpFlags$1;
  var stickyHelpers$1 = regexpStickyHelpers;
  var shared = shared$4;
  var create = objectCreate;
  var getInternalState = internalState.get;
  var UNSUPPORTED_DOT_ALL = regexpUnsupportedDotAll;
  var UNSUPPORTED_NCG = regexpUnsupportedNcg;

  var nativeReplace = shared('native-string-replace', String.prototype.replace);
  var nativeExec = RegExp.prototype.exec;
  var patchedExec = nativeExec;
  var charAt$3 = uncurryThis$7(''.charAt);
  var indexOf = uncurryThis$7(''.indexOf);
  var replace$1 = uncurryThis$7(''.replace);
  var stringSlice$5 = uncurryThis$7(''.slice);

  var UPDATES_LAST_INDEX_WRONG = (function () {
    var re1 = /a/;
    var re2 = /b*/g;
    call$7(nativeExec, re1, 'a');
    call$7(nativeExec, re2, 'a');
    return re1.lastIndex !== 0 || re2.lastIndex !== 0;
  })();

  var UNSUPPORTED_Y$1 = stickyHelpers$1.BROKEN_CARET;

  // nonparticipating capturing group, copied from es5-shim's String#split patch.
  var NPCG_INCLUDED = /()??/.exec('')[1] !== undefined;

  var PATCH = UPDATES_LAST_INDEX_WRONG || NPCG_INCLUDED || UNSUPPORTED_Y$1 || UNSUPPORTED_DOT_ALL || UNSUPPORTED_NCG;

  if (PATCH) {
    patchedExec = function exec(string) {
      var re = this;
      var state = getInternalState(re);
      var str = toString$8(string);
      var raw = state.raw;
      var result, reCopy, lastIndex, match, i, object, group;

      if (raw) {
        raw.lastIndex = re.lastIndex;
        result = call$7(patchedExec, raw, str);
        re.lastIndex = raw.lastIndex;
        return result;
      }

      var groups = state.groups;
      var sticky = UNSUPPORTED_Y$1 && re.sticky;
      var flags = call$7(regexpFlags, re);
      var source = re.source;
      var charsAdded = 0;
      var strCopy = str;

      if (sticky) {
        flags = replace$1(flags, 'y', '');
        if (indexOf(flags, 'g') === -1) {
          flags += 'g';
        }

        strCopy = stringSlice$5(str, re.lastIndex);
        // Support anchored sticky behavior.
        if (re.lastIndex > 0 && (!re.multiline || re.multiline && charAt$3(str, re.lastIndex - 1) !== '\n')) {
          source = '(?: ' + source + ')';
          strCopy = ' ' + strCopy;
          charsAdded++;
        }
        // ^(? + rx + ) is needed, in combination with some str slicing, to
        // simulate the 'y' flag.
        reCopy = new RegExp('^(?:' + source + ')', flags);
      }

      if (NPCG_INCLUDED) {
        reCopy = new RegExp('^' + source + '$(?!\\s)', flags);
      }
      if (UPDATES_LAST_INDEX_WRONG) lastIndex = re.lastIndex;

      match = call$7(nativeExec, sticky ? reCopy : re, strCopy);

      if (sticky) {
        if (match) {
          match.input = stringSlice$5(match.input, charsAdded);
          match[0] = stringSlice$5(match[0], charsAdded);
          match.index = re.lastIndex;
          re.lastIndex += match[0].length;
        } else re.lastIndex = 0;
      } else if (UPDATES_LAST_INDEX_WRONG && match) {
        re.lastIndex = re.global ? match.index + match[0].length : lastIndex;
      }
      if (NPCG_INCLUDED && match && match.length > 1) {
        // Fix browsers whose `exec` methods don't consistently return `undefined`
        // for NPCG, like IE8. NOTE: This doesn't work for /(.?)?/
        call$7(nativeReplace, match[0], reCopy, function () {
          for (i = 1; i < arguments.length - 2; i++) {
            if (arguments[i] === undefined) match[i] = undefined;
          }
        });
      }

      if (match && groups) {
        match.groups = object = create(null);
        for (i = 0; i < groups.length; i++) {
          group = groups[i];
          object[group[0]] = match[group[1]];
        }
      }

      return match;
    };
  }

  var regexpExec$2 = patchedExec;

  var $$5 = _export;
  var exec = regexpExec$2;

  // `RegExp.prototype.exec` method
  // https://tc39.es/ecma262/#sec-regexp.prototype.exec
  $$5({ target: 'RegExp', proto: true, forced: /./.exec !== exec }, {
    exec: exec
  });

  var PROPER_FUNCTION_NAME$1 = functionName.PROPER;
  var defineBuiltIn$1 = defineBuiltIn$7;
  var anObject$6 = anObject$c;
  var $toString = toString$f;
  var fails$5 = fails$x;
  var getRegExpFlags = regexpGetFlags;

  var TO_STRING = 'toString';
  var RegExpPrototype$1 = RegExp.prototype;
  var nativeToString = RegExpPrototype$1[TO_STRING];

  var NOT_GENERIC = fails$5(function () { return nativeToString.call({ source: 'a', flags: 'b' }) !== '/a/b'; });
  // FF44- RegExp#toString has a wrong name
  var INCORRECT_NAME = PROPER_FUNCTION_NAME$1 && nativeToString.name !== TO_STRING;

  // `RegExp.prototype.toString` method
  // https://tc39.es/ecma262/#sec-regexp.prototype.tostring
  if (NOT_GENERIC || INCORRECT_NAME) {
    defineBuiltIn$1(RegExpPrototype$1, TO_STRING, function toString() {
      var R = anObject$6(this);
      var pattern = $toString(R.source);
      var flags = $toString(getRegExpFlags(R));
      return '/' + pattern + '/' + flags;
    }, { unsafe: true });
  }

  var isRegExp = isRegexp;

  var $TypeError$2 = TypeError;

  var notARegexp = function (it) {
    if (isRegExp(it)) {
      throw new $TypeError$2("The method doesn't accept regular expressions");
    } return it;
  };

  var wellKnownSymbol$4 = wellKnownSymbol$j;

  var MATCH = wellKnownSymbol$4('match');

  var correctIsRegexpLogic = function (METHOD_NAME) {
    var regexp = /./;
    try {
      '/./'[METHOD_NAME](regexp);
    } catch (error1) {
      try {
        regexp[MATCH] = false;
        return '/./'[METHOD_NAME](regexp);
      } catch (error2) { /* empty */ }
    } return false;
  };

  var $$4 = _export;
  var uncurryThis$6 = functionUncurryThis;
  var notARegExp$2 = notARegexp;
  var requireObjectCoercible$7 = requireObjectCoercible$c;
  var toString$7 = toString$f;
  var correctIsRegExpLogic$2 = correctIsRegexpLogic;

  var stringIndexOf$1 = uncurryThis$6(''.indexOf);

  // `String.prototype.includes` method
  // https://tc39.es/ecma262/#sec-string.prototype.includes
  $$4({ target: 'String', proto: true, forced: !correctIsRegExpLogic$2('includes') }, {
    includes: function includes(searchString /* , position = 0 */) {
      return !!~stringIndexOf$1(
        toString$7(requireObjectCoercible$7(this)),
        toString$7(notARegExp$2(searchString)),
        arguments.length > 1 ? arguments[1] : undefined
      );
    }
  });

  var NATIVE_BIND = functionBindNative;

  var FunctionPrototype = Function.prototype;
  var apply$1 = FunctionPrototype.apply;
  var call$6 = FunctionPrototype.call;

  // eslint-disable-next-line es/no-reflect -- safe
  var functionApply = typeof Reflect == 'object' && Reflect.apply || (NATIVE_BIND ? call$6.bind(apply$1) : function () {
    return call$6.apply(apply$1, arguments);
  });

  // TODO: Remove from `core-js@4` since it's moved to entry points

  var call$5 = functionCall;
  var defineBuiltIn = defineBuiltIn$7;
  var regexpExec$1 = regexpExec$2;
  var fails$4 = fails$x;
  var wellKnownSymbol$3 = wellKnownSymbol$j;
  var createNonEnumerableProperty$2 = createNonEnumerableProperty$7;

  var SPECIES$1 = wellKnownSymbol$3('species');
  var RegExpPrototype = RegExp.prototype;

  var fixRegexpWellKnownSymbolLogic = function (KEY, exec, FORCED, SHAM) {
    var SYMBOL = wellKnownSymbol$3(KEY);

    var DELEGATES_TO_SYMBOL = !fails$4(function () {
      // String methods call symbol-named RegExp methods
      var O = {};
      O[SYMBOL] = function () { return 7; };
      return ''[KEY](O) !== 7;
    });

    var DELEGATES_TO_EXEC = DELEGATES_TO_SYMBOL && !fails$4(function () {
      // Symbol-named RegExp methods call .exec
      var execCalled = false;
      var re = /a/;

      if (KEY === 'split') {
        // We can't use real regex here since it causes deoptimization
        // and serious performance degradation in V8
        // https://github.com/zloirock/core-js/issues/306
        re = {};
        // RegExp[@@split] doesn't call the regex's exec method, but first creates
        // a new one. We need to return the patched regex when creating the new one.
        re.constructor = {};
        re.constructor[SPECIES$1] = function () { return re; };
        re.flags = '';
        re[SYMBOL] = /./[SYMBOL];
      }

      re.exec = function () {
        execCalled = true;
        return null;
      };

      re[SYMBOL]('');
      return !execCalled;
    });

    if (
      !DELEGATES_TO_SYMBOL ||
      !DELEGATES_TO_EXEC ||
      FORCED
    ) {
      var nativeRegExpMethod = /./[SYMBOL];
      var methods = exec(SYMBOL, ''[KEY], function (nativeMethod, regexp, str, arg2, forceStringMethod) {
        var $exec = regexp.exec;
        if ($exec === regexpExec$1 || $exec === RegExpPrototype.exec) {
          if (DELEGATES_TO_SYMBOL && !forceStringMethod) {
            // The native String method already delegates to @@method (this
            // polyfilled function), leasing to infinite recursion.
            // We avoid it by directly calling the native @@method method.
            return { done: true, value: call$5(nativeRegExpMethod, regexp, str, arg2) };
          }
          return { done: true, value: call$5(nativeMethod, str, regexp, arg2) };
        }
        return { done: false };
      });

      defineBuiltIn(String.prototype, KEY, methods[0]);
      defineBuiltIn(RegExpPrototype, SYMBOL, methods[1]);
    }

    if (SHAM) createNonEnumerableProperty$2(RegExpPrototype[SYMBOL], 'sham', true);
  };

  var uncurryThis$5 = functionUncurryThis;
  var toIntegerOrInfinity$1 = toIntegerOrInfinity$5;
  var toString$6 = toString$f;
  var requireObjectCoercible$6 = requireObjectCoercible$c;

  var charAt$2 = uncurryThis$5(''.charAt);
  var charCodeAt = uncurryThis$5(''.charCodeAt);
  var stringSlice$4 = uncurryThis$5(''.slice);

  var createMethod = function (CONVERT_TO_STRING) {
    return function ($this, pos) {
      var S = toString$6(requireObjectCoercible$6($this));
      var position = toIntegerOrInfinity$1(pos);
      var size = S.length;
      var first, second;
      if (position < 0 || position >= size) return CONVERT_TO_STRING ? '' : undefined;
      first = charCodeAt(S, position);
      return first < 0xD800 || first > 0xDBFF || position + 1 === size
        || (second = charCodeAt(S, position + 1)) < 0xDC00 || second > 0xDFFF
          ? CONVERT_TO_STRING
            ? charAt$2(S, position)
            : first
          : CONVERT_TO_STRING
            ? stringSlice$4(S, position, position + 2)
            : (first - 0xD800 << 10) + (second - 0xDC00) + 0x10000;
    };
  };

  var stringMultibyte = {
    // `String.prototype.codePointAt` method
    // https://tc39.es/ecma262/#sec-string.prototype.codepointat
    codeAt: createMethod(false),
    // `String.prototype.at` method
    // https://github.com/mathiasbynens/String.prototype.at
    charAt: createMethod(true)
  };

  var charAt$1 = stringMultibyte.charAt;

  // `AdvanceStringIndex` abstract operation
  // https://tc39.es/ecma262/#sec-advancestringindex
  var advanceStringIndex$3 = function (S, index, unicode) {
    return index + (unicode ? charAt$1(S, index).length : 1);
  };

  var uncurryThis$4 = functionUncurryThis;
  var toObject$1 = toObject$b;

  var floor = Math.floor;
  var charAt = uncurryThis$4(''.charAt);
  var replace = uncurryThis$4(''.replace);
  var stringSlice$3 = uncurryThis$4(''.slice);
  // eslint-disable-next-line redos/no-vulnerable -- safe
  var SUBSTITUTION_SYMBOLS = /\$([$&'`]|\d{1,2}|<[^>]*>)/g;
  var SUBSTITUTION_SYMBOLS_NO_NAMED = /\$([$&'`]|\d{1,2})/g;

  // `GetSubstitution` abstract operation
  // https://tc39.es/ecma262/#sec-getsubstitution
  var getSubstitution$1 = function (matched, str, position, captures, namedCaptures, replacement) {
    var tailPos = position + matched.length;
    var m = captures.length;
    var symbols = SUBSTITUTION_SYMBOLS_NO_NAMED;
    if (namedCaptures !== undefined) {
      namedCaptures = toObject$1(namedCaptures);
      symbols = SUBSTITUTION_SYMBOLS;
    }
    return replace(replacement, symbols, function (match, ch) {
      var capture;
      switch (charAt(ch, 0)) {
        case '$': return '$';
        case '&': return matched;
        case '`': return stringSlice$3(str, 0, position);
        case "'": return stringSlice$3(str, tailPos);
        case '<':
          capture = namedCaptures[stringSlice$3(ch, 1, -1)];
          break;
        default: // \d\d?
          var n = +ch;
          if (n === 0) return match;
          if (n > m) {
            var f = floor(n / 10);
            if (f === 0) return match;
            if (f <= m) return captures[f - 1] === undefined ? charAt(ch, 1) : captures[f - 1] + charAt(ch, 1);
            return match;
          }
          capture = captures[n - 1];
      }
      return capture === undefined ? '' : capture;
    });
  };

  var call$4 = functionCall;
  var anObject$5 = anObject$c;
  var isCallable$1 = isCallable$i;
  var classof = classofRaw$2;
  var regexpExec = regexpExec$2;

  var $TypeError$1 = TypeError;

  // `RegExpExec` abstract operation
  // https://tc39.es/ecma262/#sec-regexpexec
  var regexpExecAbstract = function (R, S) {
    var exec = R.exec;
    if (isCallable$1(exec)) {
      var result = call$4(exec, R, S);
      if (result !== null) anObject$5(result);
      return result;
    }
    if (classof(R) === 'RegExp') return call$4(regexpExec, R, S);
    throw new $TypeError$1('RegExp#exec called on incompatible receiver');
  };

  var apply = functionApply;
  var call$3 = functionCall;
  var uncurryThis$3 = functionUncurryThis;
  var fixRegExpWellKnownSymbolLogic$3 = fixRegexpWellKnownSymbolLogic;
  var fails$3 = fails$x;
  var anObject$4 = anObject$c;
  var isCallable = isCallable$i;
  var isNullOrUndefined$4 = isNullOrUndefined$7;
  var toIntegerOrInfinity = toIntegerOrInfinity$5;
  var toLength$4 = toLength$6;
  var toString$5 = toString$f;
  var requireObjectCoercible$5 = requireObjectCoercible$c;
  var advanceStringIndex$2 = advanceStringIndex$3;
  var getMethod$3 = getMethod$5;
  var getSubstitution = getSubstitution$1;
  var regExpExec$3 = regexpExecAbstract;
  var wellKnownSymbol$2 = wellKnownSymbol$j;

  var REPLACE = wellKnownSymbol$2('replace');
  var max = Math.max;
  var min$3 = Math.min;
  var concat = uncurryThis$3([].concat);
  var push$1 = uncurryThis$3([].push);
  var stringIndexOf = uncurryThis$3(''.indexOf);
  var stringSlice$2 = uncurryThis$3(''.slice);

  var maybeToString = function (it) {
    return it === undefined ? it : String(it);
  };

  // IE <= 11 replaces $0 with the whole match, as if it was $&
  // https://stackoverflow.com/questions/6024666/getting-ie-to-replace-a-regex-with-the-literal-string-0
  var REPLACE_KEEPS_$0 = (function () {
    // eslint-disable-next-line regexp/prefer-escape-replacement-dollar-char -- required for testing
    return 'a'.replace(/./, '$0') === '$0';
  })();

  // Safari <= 13.0.3(?) substitutes nth capture where n>m with an empty string
  var REGEXP_REPLACE_SUBSTITUTES_UNDEFINED_CAPTURE = (function () {
    if (/./[REPLACE]) {
      return /./[REPLACE]('a', '$0') === '';
    }
    return false;
  })();

  var REPLACE_SUPPORTS_NAMED_GROUPS = !fails$3(function () {
    var re = /./;
    re.exec = function () {
      var result = [];
      result.groups = { a: '7' };
      return result;
    };
    // eslint-disable-next-line regexp/no-useless-dollar-replacements -- false positive
    return ''.replace(re, '$<a>') !== '7';
  });

  // @@replace logic
  fixRegExpWellKnownSymbolLogic$3('replace', function (_, nativeReplace, maybeCallNative) {
    var UNSAFE_SUBSTITUTE = REGEXP_REPLACE_SUBSTITUTES_UNDEFINED_CAPTURE ? '$' : '$0';

    return [
      // `String.prototype.replace` method
      // https://tc39.es/ecma262/#sec-string.prototype.replace
      function replace(searchValue, replaceValue) {
        var O = requireObjectCoercible$5(this);
        var replacer = isNullOrUndefined$4(searchValue) ? undefined : getMethod$3(searchValue, REPLACE);
        return replacer
          ? call$3(replacer, searchValue, O, replaceValue)
          : call$3(nativeReplace, toString$5(O), searchValue, replaceValue);
      },
      // `RegExp.prototype[@@replace]` method
      // https://tc39.es/ecma262/#sec-regexp.prototype-@@replace
      function (string, replaceValue) {
        var rx = anObject$4(this);
        var S = toString$5(string);

        if (
          typeof replaceValue == 'string' &&
          stringIndexOf(replaceValue, UNSAFE_SUBSTITUTE) === -1 &&
          stringIndexOf(replaceValue, '$<') === -1
        ) {
          var res = maybeCallNative(nativeReplace, rx, S, replaceValue);
          if (res.done) return res.value;
        }

        var functionalReplace = isCallable(replaceValue);
        if (!functionalReplace) replaceValue = toString$5(replaceValue);

        var global = rx.global;
        var fullUnicode;
        if (global) {
          fullUnicode = rx.unicode;
          rx.lastIndex = 0;
        }

        var results = [];
        var result;
        while (true) {
          result = regExpExec$3(rx, S);
          if (result === null) break;

          push$1(results, result);
          if (!global) break;

          var matchStr = toString$5(result[0]);
          if (matchStr === '') rx.lastIndex = advanceStringIndex$2(S, toLength$4(rx.lastIndex), fullUnicode);
        }

        var accumulatedResult = '';
        var nextSourcePosition = 0;
        for (var i = 0; i < results.length; i++) {
          result = results[i];

          var matched = toString$5(result[0]);
          var position = max(min$3(toIntegerOrInfinity(result.index), S.length), 0);
          var captures = [];
          var replacement;
          // NOTE: This is equivalent to
          //   captures = result.slice(1).map(maybeToString)
          // but for some reason `nativeSlice.call(result, 1, result.length)` (called in
          // the slice polyfill when slicing native arrays) "doesn't work" in safari 9 and
          // causes a crash (https://pastebin.com/N21QzeQA) when trying to debug it.
          for (var j = 1; j < result.length; j++) push$1(captures, maybeToString(result[j]));
          var namedCaptures = result.groups;
          if (functionalReplace) {
            var replacerArgs = concat([matched], captures, position, S);
            if (namedCaptures !== undefined) push$1(replacerArgs, namedCaptures);
            replacement = toString$5(apply(replaceValue, undefined, replacerArgs));
          } else {
            replacement = getSubstitution(matched, S, position, captures, namedCaptures, replaceValue);
          }
          if (position >= nextSourcePosition) {
            accumulatedResult += stringSlice$2(S, nextSourcePosition, position) + replacement;
            nextSourcePosition = position + matched.length;
          }
        }

        return accumulatedResult + stringSlice$2(S, nextSourcePosition);
      }
    ];
  }, !REPLACE_SUPPORTS_NAMED_GROUPS || !REPLACE_KEEPS_$0 || REGEXP_REPLACE_SUBSTITUTES_UNDEFINED_CAPTURE);

  // `SameValue` abstract operation
  // https://tc39.es/ecma262/#sec-samevalue
  // eslint-disable-next-line es/no-object-is -- safe
  var sameValue$1 = Object.is || function is(x, y) {
    // eslint-disable-next-line no-self-compare -- NaN check
    return x === y ? x !== 0 || 1 / x === 1 / y : x !== x && y !== y;
  };

  var call$2 = functionCall;
  var fixRegExpWellKnownSymbolLogic$2 = fixRegexpWellKnownSymbolLogic;
  var anObject$3 = anObject$c;
  var isNullOrUndefined$3 = isNullOrUndefined$7;
  var requireObjectCoercible$4 = requireObjectCoercible$c;
  var sameValue = sameValue$1;
  var toString$4 = toString$f;
  var getMethod$2 = getMethod$5;
  var regExpExec$2 = regexpExecAbstract;

  // @@search logic
  fixRegExpWellKnownSymbolLogic$2('search', function (SEARCH, nativeSearch, maybeCallNative) {
    return [
      // `String.prototype.search` method
      // https://tc39.es/ecma262/#sec-string.prototype.search
      function search(regexp) {
        var O = requireObjectCoercible$4(this);
        var searcher = isNullOrUndefined$3(regexp) ? undefined : getMethod$2(regexp, SEARCH);
        return searcher ? call$2(searcher, regexp, O) : new RegExp(regexp)[SEARCH](toString$4(O));
      },
      // `RegExp.prototype[@@search]` method
      // https://tc39.es/ecma262/#sec-regexp.prototype-@@search
      function (string) {
        var rx = anObject$3(this);
        var S = toString$4(string);
        var res = maybeCallNative(nativeSearch, rx, S);

        if (res.done) return res.value;

        var previousLastIndex = rx.lastIndex;
        if (!sameValue(previousLastIndex, 0)) rx.lastIndex = 0;
        var result = regExpExec$2(rx, S);
        if (!sameValue(rx.lastIndex, previousLastIndex)) rx.lastIndex = previousLastIndex;
        return result === null ? -1 : result.index;
      }
    ];
  });

  var isConstructor = isConstructor$3;
  var tryToString = tryToString$3;

  var $TypeError = TypeError;

  // `Assert: IsConstructor(argument) is true`
  var aConstructor$1 = function (argument) {
    if (isConstructor(argument)) return argument;
    throw new $TypeError(tryToString(argument) + ' is not a constructor');
  };

  var anObject$2 = anObject$c;
  var aConstructor = aConstructor$1;
  var isNullOrUndefined$2 = isNullOrUndefined$7;
  var wellKnownSymbol$1 = wellKnownSymbol$j;

  var SPECIES = wellKnownSymbol$1('species');

  // `SpeciesConstructor` abstract operation
  // https://tc39.es/ecma262/#sec-speciesconstructor
  var speciesConstructor$1 = function (O, defaultConstructor) {
    var C = anObject$2(O).constructor;
    var S;
    return C === undefined || isNullOrUndefined$2(S = anObject$2(C)[SPECIES]) ? defaultConstructor : aConstructor(S);
  };

  var call$1 = functionCall;
  var uncurryThis$2 = functionUncurryThis;
  var fixRegExpWellKnownSymbolLogic$1 = fixRegexpWellKnownSymbolLogic;
  var anObject$1 = anObject$c;
  var isNullOrUndefined$1 = isNullOrUndefined$7;
  var requireObjectCoercible$3 = requireObjectCoercible$c;
  var speciesConstructor = speciesConstructor$1;
  var advanceStringIndex$1 = advanceStringIndex$3;
  var toLength$3 = toLength$6;
  var toString$3 = toString$f;
  var getMethod$1 = getMethod$5;
  var regExpExec$1 = regexpExecAbstract;
  var stickyHelpers = regexpStickyHelpers;
  var fails$2 = fails$x;

  var UNSUPPORTED_Y = stickyHelpers.UNSUPPORTED_Y;
  var MAX_UINT32 = 0xFFFFFFFF;
  var min$2 = Math.min;
  var push = uncurryThis$2([].push);
  var stringSlice$1 = uncurryThis$2(''.slice);

  // Chrome 51 has a buggy "split" implementation when RegExp#exec !== nativeExec
  // Weex JS has frozen built-in prototypes, so use try / catch wrapper
  var SPLIT_WORKS_WITH_OVERWRITTEN_EXEC = !fails$2(function () {
    // eslint-disable-next-line regexp/no-empty-group -- required for testing
    var re = /(?:)/;
    var originalExec = re.exec;
    re.exec = function () { return originalExec.apply(this, arguments); };
    var result = 'ab'.split(re);
    return result.length !== 2 || result[0] !== 'a' || result[1] !== 'b';
  });

  var BUGGY = 'abbc'.split(/(b)*/)[1] === 'c' ||
    // eslint-disable-next-line regexp/no-empty-group -- required for testing
    'test'.split(/(?:)/, -1).length !== 4 ||
    'ab'.split(/(?:ab)*/).length !== 2 ||
    '.'.split(/(.?)(.?)/).length !== 4 ||
    // eslint-disable-next-line regexp/no-empty-capturing-group, regexp/no-empty-group -- required for testing
    '.'.split(/()()/).length > 1 ||
    ''.split(/.?/).length;

  // @@split logic
  fixRegExpWellKnownSymbolLogic$1('split', function (SPLIT, nativeSplit, maybeCallNative) {
    var internalSplit = '0'.split(undefined, 0).length ? function (separator, limit) {
      return separator === undefined && limit === 0 ? [] : call$1(nativeSplit, this, separator, limit);
    } : nativeSplit;

    return [
      // `String.prototype.split` method
      // https://tc39.es/ecma262/#sec-string.prototype.split
      function split(separator, limit) {
        var O = requireObjectCoercible$3(this);
        var splitter = isNullOrUndefined$1(separator) ? undefined : getMethod$1(separator, SPLIT);
        return splitter
          ? call$1(splitter, separator, O, limit)
          : call$1(internalSplit, toString$3(O), separator, limit);
      },
      // `RegExp.prototype[@@split]` method
      // https://tc39.es/ecma262/#sec-regexp.prototype-@@split
      //
      // NOTE: This cannot be properly polyfilled in engines that don't support
      // the 'y' flag.
      function (string, limit) {
        var rx = anObject$1(this);
        var S = toString$3(string);

        if (!BUGGY) {
          var res = maybeCallNative(internalSplit, rx, S, limit, internalSplit !== nativeSplit);
          if (res.done) return res.value;
        }

        var C = speciesConstructor(rx, RegExp);
        var unicodeMatching = rx.unicode;
        var flags = (rx.ignoreCase ? 'i' : '') +
                    (rx.multiline ? 'm' : '') +
                    (rx.unicode ? 'u' : '') +
                    (UNSUPPORTED_Y ? 'g' : 'y');
        // ^(? + rx + ) is needed, in combination with some S slicing, to
        // simulate the 'y' flag.
        var splitter = new C(UNSUPPORTED_Y ? '^(?:' + rx.source + ')' : rx, flags);
        var lim = limit === undefined ? MAX_UINT32 : limit >>> 0;
        if (lim === 0) return [];
        if (S.length === 0) return regExpExec$1(splitter, S) === null ? [S] : [];
        var p = 0;
        var q = 0;
        var A = [];
        while (q < S.length) {
          splitter.lastIndex = UNSUPPORTED_Y ? 0 : q;
          var z = regExpExec$1(splitter, UNSUPPORTED_Y ? stringSlice$1(S, q) : S);
          var e;
          if (
            z === null ||
            (e = min$2(toLength$3(splitter.lastIndex + (UNSUPPORTED_Y ? q : 0)), S.length)) === p
          ) {
            q = advanceStringIndex$1(S, q, unicodeMatching);
          } else {
            push(A, stringSlice$1(S, p, q));
            if (A.length === lim) return A;
            for (var i = 1; i <= z.length - 1; i++) {
              push(A, z[i]);
              if (A.length === lim) return A;
            }
            q = p = e;
          }
        }
        push(A, stringSlice$1(S, p));
        return A;
      }
    ];
  }, BUGGY || !SPLIT_WORKS_WITH_OVERWRITTEN_EXEC, UNSUPPORTED_Y);

  var PROPER_FUNCTION_NAME = functionName.PROPER;
  var fails$1 = fails$x;
  var whitespaces = whitespaces$4;

  var non = '\u200B\u0085\u180E';

  // check that a method works with the correct list
  // of whitespaces and has a correct name
  var stringTrimForced = function (METHOD_NAME) {
    return fails$1(function () {
      return !!whitespaces[METHOD_NAME]()
        || non[METHOD_NAME]() !== non
        || (PROPER_FUNCTION_NAME && whitespaces[METHOD_NAME].name !== METHOD_NAME);
    });
  };

  var $$3 = _export;
  var $trim = stringTrim.trim;
  var forcedStringTrimMethod = stringTrimForced;

  // `String.prototype.trim` method
  // https://tc39.es/ecma262/#sec-string.prototype.trim
  $$3({ target: 'String', proto: true, forced: forcedStringTrimMethod('trim') }, {
    trim: function trim() {
      return $trim(this);
    }
  });

  // iterable DOM collections
  // flag - `iterable` interface - 'entries', 'keys', 'values', 'forEach' methods
  var domIterables = {
    CSSRuleList: 0,
    CSSStyleDeclaration: 0,
    CSSValueList: 0,
    ClientRectList: 0,
    DOMRectList: 0,
    DOMStringList: 0,
    DOMTokenList: 1,
    DataTransferItemList: 0,
    FileList: 0,
    HTMLAllCollection: 0,
    HTMLCollection: 0,
    HTMLFormElement: 0,
    HTMLSelectElement: 0,
    MediaList: 0,
    MimeTypeArray: 0,
    NamedNodeMap: 0,
    NodeList: 1,
    PaintRequestList: 0,
    Plugin: 0,
    PluginArray: 0,
    SVGLengthList: 0,
    SVGNumberList: 0,
    SVGPathSegList: 0,
    SVGPointList: 0,
    SVGStringList: 0,
    SVGTransformList: 0,
    SourceBufferList: 0,
    StyleSheetList: 0,
    TextTrackCueList: 0,
    TextTrackList: 0,
    TouchList: 0
  };

  // in old WebKit versions, `element.classList` is not an instance of global `DOMTokenList`
  var documentCreateElement = documentCreateElement$2;

  var classList = documentCreateElement('span').classList;
  var DOMTokenListPrototype$2 = classList && classList.constructor && classList.constructor.prototype;

  var domTokenListPrototype = DOMTokenListPrototype$2 === Object.prototype ? undefined : DOMTokenListPrototype$2;

  var $forEach = arrayIteration.forEach;
  var arrayMethodIsStrict = arrayMethodIsStrict$4;

  var STRICT_METHOD = arrayMethodIsStrict('forEach');

  // `Array.prototype.forEach` method implementation
  // https://tc39.es/ecma262/#sec-array.prototype.foreach
  var arrayForEach = !STRICT_METHOD ? function forEach(callbackfn /* , thisArg */) {
    return $forEach(this, callbackfn, arguments.length > 1 ? arguments[1] : undefined);
  // eslint-disable-next-line es/no-array-prototype-foreach -- safe
  } : [].forEach;

  var global$2 = global$k;
  var DOMIterables$1 = domIterables;
  var DOMTokenListPrototype$1 = domTokenListPrototype;
  var forEach = arrayForEach;
  var createNonEnumerableProperty$1 = createNonEnumerableProperty$7;

  var handlePrototype$1 = function (CollectionPrototype) {
    // some Chrome versions have non-configurable methods on DOMTokenList
    if (CollectionPrototype && CollectionPrototype.forEach !== forEach) try {
      createNonEnumerableProperty$1(CollectionPrototype, 'forEach', forEach);
    } catch (error) {
      CollectionPrototype.forEach = forEach;
    }
  };

  for (var COLLECTION_NAME$1 in DOMIterables$1) {
    if (DOMIterables$1[COLLECTION_NAME$1]) {
      handlePrototype$1(global$2[COLLECTION_NAME$1] && global$2[COLLECTION_NAME$1].prototype);
    }
  }

  handlePrototype$1(DOMTokenListPrototype$1);

  var global$1 = global$k;
  var DOMIterables = domIterables;
  var DOMTokenListPrototype = domTokenListPrototype;
  var ArrayIteratorMethods = es_array_iterator;
  var createNonEnumerableProperty = createNonEnumerableProperty$7;
  var setToStringTag = setToStringTag$3;
  var wellKnownSymbol = wellKnownSymbol$j;

  var ITERATOR = wellKnownSymbol('iterator');
  var ArrayValues = ArrayIteratorMethods.values;

  var handlePrototype = function (CollectionPrototype, COLLECTION_NAME) {
    if (CollectionPrototype) {
      // some Chrome versions have non-configurable methods on DOMTokenList
      if (CollectionPrototype[ITERATOR] !== ArrayValues) try {
        createNonEnumerableProperty(CollectionPrototype, ITERATOR, ArrayValues);
      } catch (error) {
        CollectionPrototype[ITERATOR] = ArrayValues;
      }
      setToStringTag(CollectionPrototype, COLLECTION_NAME, true);
      if (DOMIterables[COLLECTION_NAME]) for (var METHOD_NAME in ArrayIteratorMethods) {
        // some Chrome versions have non-configurable methods on DOMTokenList
        if (CollectionPrototype[METHOD_NAME] !== ArrayIteratorMethods[METHOD_NAME]) try {
          createNonEnumerableProperty(CollectionPrototype, METHOD_NAME, ArrayIteratorMethods[METHOD_NAME]);
        } catch (error) {
          CollectionPrototype[METHOD_NAME] = ArrayIteratorMethods[METHOD_NAME];
        }
      }
    }
  };

  for (var COLLECTION_NAME in DOMIterables) {
    handlePrototype(global$1[COLLECTION_NAME] && global$1[COLLECTION_NAME].prototype, COLLECTION_NAME);
  }

  handlePrototype(DOMTokenListPrototype, 'DOMTokenList');

  var $$2 = _export;
  var fails = fails$x;
  var toObject = toObject$b;
  var nativeGetPrototypeOf = objectGetPrototypeOf$1;
  var CORRECT_PROTOTYPE_GETTER = correctPrototypeGetter;

  var FAILS_ON_PRIMITIVES = fails(function () { nativeGetPrototypeOf(1); });

  // `Object.getPrototypeOf` method
  // https://tc39.es/ecma262/#sec-object.getprototypeof
  $$2({ target: 'Object', stat: true, forced: FAILS_ON_PRIMITIVES, sham: !CORRECT_PROTOTYPE_GETTER }, {
    getPrototypeOf: function getPrototypeOf(it) {
      return nativeGetPrototypeOf(toObject(it));
    }
  });

  var $$1 = _export;
  var uncurryThis$1 = functionUncurryThisClause;
  var getOwnPropertyDescriptor$1 = objectGetOwnPropertyDescriptor.f;
  var toLength$2 = toLength$6;
  var toString$2 = toString$f;
  var notARegExp$1 = notARegexp;
  var requireObjectCoercible$2 = requireObjectCoercible$c;
  var correctIsRegExpLogic$1 = correctIsRegexpLogic;

  var slice = uncurryThis$1(''.slice);
  var min$1 = Math.min;

  var CORRECT_IS_REGEXP_LOGIC$1 = correctIsRegExpLogic$1('endsWith');
  // https://github.com/zloirock/core-js/pull/702
  var MDN_POLYFILL_BUG$1 = !CORRECT_IS_REGEXP_LOGIC$1 && !!function () {
    var descriptor = getOwnPropertyDescriptor$1(String.prototype, 'endsWith');
    return descriptor && !descriptor.writable;
  }();

  // `String.prototype.endsWith` method
  // https://tc39.es/ecma262/#sec-string.prototype.endswith
  $$1({ target: 'String', proto: true, forced: !MDN_POLYFILL_BUG$1 && !CORRECT_IS_REGEXP_LOGIC$1 }, {
    endsWith: function endsWith(searchString /* , endPosition = @length */) {
      var that = toString$2(requireObjectCoercible$2(this));
      notARegExp$1(searchString);
      var endPosition = arguments.length > 1 ? arguments[1] : undefined;
      var len = that.length;
      var end = endPosition === undefined ? len : min$1(toLength$2(endPosition), len);
      var search = toString$2(searchString);
      return slice(that, end - search.length, end) === search;
    }
  });

  var call = functionCall;
  var fixRegExpWellKnownSymbolLogic = fixRegexpWellKnownSymbolLogic;
  var anObject = anObject$c;
  var isNullOrUndefined = isNullOrUndefined$7;
  var toLength$1 = toLength$6;
  var toString$1 = toString$f;
  var requireObjectCoercible$1 = requireObjectCoercible$c;
  var getMethod = getMethod$5;
  var advanceStringIndex = advanceStringIndex$3;
  var regExpExec = regexpExecAbstract;

  // @@match logic
  fixRegExpWellKnownSymbolLogic('match', function (MATCH, nativeMatch, maybeCallNative) {
    return [
      // `String.prototype.match` method
      // https://tc39.es/ecma262/#sec-string.prototype.match
      function match(regexp) {
        var O = requireObjectCoercible$1(this);
        var matcher = isNullOrUndefined(regexp) ? undefined : getMethod(regexp, MATCH);
        return matcher ? call(matcher, regexp, O) : new RegExp(regexp)[MATCH](toString$1(O));
      },
      // `RegExp.prototype[@@match]` method
      // https://tc39.es/ecma262/#sec-regexp.prototype-@@match
      function (string) {
        var rx = anObject(this);
        var S = toString$1(string);
        var res = maybeCallNative(nativeMatch, rx, S);

        if (res.done) return res.value;

        if (!rx.global) return regExpExec(rx, S);

        var fullUnicode = rx.unicode;
        rx.lastIndex = 0;
        var A = [];
        var n = 0;
        var result;
        while ((result = regExpExec(rx, S)) !== null) {
          var matchStr = toString$1(result[0]);
          A[n] = matchStr;
          if (matchStr === '') rx.lastIndex = advanceStringIndex(S, toLength$1(rx.lastIndex), fullUnicode);
          n++;
        }
        return n === 0 ? null : A;
      }
    ];
  });

  var $ = _export;
  var uncurryThis = functionUncurryThisClause;
  var getOwnPropertyDescriptor = objectGetOwnPropertyDescriptor.f;
  var toLength = toLength$6;
  var toString = toString$f;
  var notARegExp = notARegexp;
  var requireObjectCoercible = requireObjectCoercible$c;
  var correctIsRegExpLogic = correctIsRegexpLogic;

  var stringSlice = uncurryThis(''.slice);
  var min = Math.min;

  var CORRECT_IS_REGEXP_LOGIC = correctIsRegExpLogic('startsWith');
  // https://github.com/zloirock/core-js/pull/702
  var MDN_POLYFILL_BUG = !CORRECT_IS_REGEXP_LOGIC && !!function () {
    var descriptor = getOwnPropertyDescriptor(String.prototype, 'startsWith');
    return descriptor && !descriptor.writable;
  }();

  // `String.prototype.startsWith` method
  // https://tc39.es/ecma262/#sec-string.prototype.startswith
  $({ target: 'String', proto: true, forced: !MDN_POLYFILL_BUG && !CORRECT_IS_REGEXP_LOGIC }, {
    startsWith: function startsWith(searchString /* , position = 0 */) {
      var that = toString(requireObjectCoercible(this));
      notARegExp(searchString);
      var index = toLength(min(arguments.length > 1 ? arguments[1] : undefined, that.length));
      var search = toString(searchString);
      return stringSlice(that, index, index + search.length) === search;
    }
  });

  var Utils = {
    getBootstrapVersion: function getBootstrapVersion() {
      var bootstrapVersion = 5;
      try {
        var rawVersion = $$q.fn.dropdown.Constructor.VERSION;

        // Only try to parse VERSION if it is defined.
        // It is undefined in older versions of Bootstrap (tested with 3.1.1).
        if (rawVersion !== undefined) {
          bootstrapVersion = parseInt(rawVersion, 10);
        }
      } catch (e) {
        // ignore
      }
      try {
        // eslint-disable-next-line no-undef
        var _rawVersion = bootstrap.Tooltip.VERSION;
        if (_rawVersion !== undefined) {
          bootstrapVersion = parseInt(_rawVersion, 10);
        }
      } catch (e) {
        // ignore
      }
      return bootstrapVersion;
    },
    getIconsPrefix: function getIconsPrefix(theme) {
      return {
        bootstrap3: 'glyphicon',
        bootstrap4: 'fa',
        bootstrap5: 'bi',
        'bootstrap-table': 'icon',
        bulma: 'fa',
        foundation: 'fa',
        materialize: 'material-icons',
        semantic: 'fa'
      }[theme] || 'fa';
    },
    getIcons: function getIcons(prefix) {
      return {
        glyphicon: {
          paginationSwitchDown: 'glyphicon-collapse-down icon-chevron-down',
          paginationSwitchUp: 'glyphicon-collapse-up icon-chevron-up',
          refresh: 'glyphicon-refresh icon-refresh',
          toggleOff: 'glyphicon-list-alt icon-list-alt',
          toggleOn: 'glyphicon-list-alt icon-list-alt',
          columns: 'glyphicon-th icon-th',
          detailOpen: 'glyphicon-plus icon-plus',
          detailClose: 'glyphicon-minus icon-minus',
          fullscreen: 'glyphicon-fullscreen',
          search: 'glyphicon-search',
          clearSearch: 'glyphicon-trash'
        },
        fa: {
          paginationSwitchDown: 'fa-caret-square-down',
          paginationSwitchUp: 'fa-caret-square-up',
          refresh: 'fa-sync',
          toggleOff: 'fa-toggle-off',
          toggleOn: 'fa-toggle-on',
          columns: 'fa-th-list',
          detailOpen: 'fa-plus',
          detailClose: 'fa-minus',
          fullscreen: 'fa-arrows-alt',
          search: 'fa-search',
          clearSearch: 'fa-trash'
        },
        bi: {
          paginationSwitchDown: 'bi-caret-down-square',
          paginationSwitchUp: 'bi-caret-up-square',
          refresh: 'bi-arrow-clockwise',
          toggleOff: 'bi-toggle-off',
          toggleOn: 'bi-toggle-on',
          columns: 'bi-list-ul',
          detailOpen: 'bi-plus',
          detailClose: 'bi-dash',
          fullscreen: 'bi-arrows-move',
          search: 'bi-search',
          clearSearch: 'bi-trash'
        },
        icon: {
          paginationSwitchDown: 'icon-arrow-up-circle',
          paginationSwitchUp: 'icon-arrow-down-circle',
          refresh: 'icon-refresh-cw',
          toggleOff: 'icon-toggle-right',
          toggleOn: 'icon-toggle-right',
          columns: 'icon-list',
          detailOpen: 'icon-plus',
          detailClose: 'icon-minus',
          fullscreen: 'icon-maximize',
          search: 'icon-search',
          clearSearch: 'icon-trash-2'
        },
        'material-icons': {
          paginationSwitchDown: 'grid_on',
          paginationSwitchUp: 'grid_off',
          refresh: 'refresh',
          toggleOff: 'tablet',
          toggleOn: 'tablet_android',
          columns: 'view_list',
          detailOpen: 'add',
          detailClose: 'remove',
          fullscreen: 'fullscreen',
          sort: 'sort',
          search: 'search',
          clearSearch: 'delete'
        }
      }[prefix] || {};
    },
    getSearchInput: function getSearchInput(that) {
      if (typeof that.options.searchSelector === 'string') {
        return $$q(that.options.searchSelector);
      }
      return that.$toolbar.find('.search input');
    },
    // $.extend: https://github.com/jquery/jquery/blob/3.6.2/src/core.js#L132
    extend: function extend() {
      var _this = this;
      for (var _len = arguments.length, args = new Array(_len), _key = 0; _key < _len; _key++) {
        args[_key] = arguments[_key];
      }
      var target = args[0] || {};
      var i = 1;
      var deep = false;
      var clone;

      // Handle a deep copy situation
      if (typeof target === 'boolean') {
        deep = target;

        // Skip the boolean and the target
        target = args[i] || {};
        i++;
      }

      // Handle case when target is a string or something (possible in deep copy)
      if (_typeof(target) !== 'object' && typeof target !== 'function') {
        target = {};
      }
      for (; i < args.length; i++) {
        var options = args[i];

        // Ignore undefined/null values
        if (typeof options === 'undefined' || options === null) {
          continue;
        }

        // Extend the base object
        // eslint-disable-next-line guard-for-in
        for (var name in options) {
          var copy = options[name];

          // Prevent Object.prototype pollution
          // Prevent never-ending loop
          if (name === '__proto__' || target === copy) {
            continue;
          }
          var copyIsArray = Array.isArray(copy);

          // Recurse if we're merging plain objects or arrays
          if (deep && copy && (this.isObject(copy) || copyIsArray)) {
            var src = target[name];
            if (copyIsArray && Array.isArray(src)) {
              if (src.every(function (it) {
                return !_this.isObject(it) && !Array.isArray(it);
              })) {
                target[name] = copy;
                continue;
              }
            }
            if (copyIsArray && !Array.isArray(src)) {
              clone = [];
            } else if (!copyIsArray && !this.isObject(src)) {
              clone = {};
            } else {
              clone = src;
            }

            // Never move original objects, clone them
            target[name] = this.extend(deep, clone, copy);

            // Don't bring in undefined values
          } else if (copy !== undefined) {
            target[name] = copy;
          }
        }
      }
      return target;
    },
    // it only does '%s', and return '' when arguments are undefined
    sprintf: function sprintf(_str) {
      for (var _len2 = arguments.length, args = new Array(_len2 > 1 ? _len2 - 1 : 0), _key2 = 1; _key2 < _len2; _key2++) {
        args[_key2 - 1] = arguments[_key2];
      }
      var flag = true;
      var i = 0;
      var str = _str.replace(/%s/g, function () {
        var arg = args[i++];
        if (typeof arg === 'undefined') {
          flag = false;
          return '';
        }
        return arg;
      });
      return flag ? str : '';
    },
    isObject: function isObject(obj) {
      if (_typeof(obj) !== 'object' || obj === null) {
        return false;
      }
      var proto = obj;
      while (Object.getPrototypeOf(proto) !== null) {
        proto = Object.getPrototypeOf(proto);
      }
      return Object.getPrototypeOf(obj) === proto;
    },
    isEmptyObject: function isEmptyObject() {
      var obj = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {};
      return Object.entries(obj).length === 0 && obj.constructor === Object;
    },
    isNumeric: function isNumeric(n) {
      return !isNaN(parseFloat(n)) && isFinite(n);
    },
    getFieldTitle: function getFieldTitle(list, value) {
      var _iterator = _createForOfIteratorHelper(list),
        _step;
      try {
        for (_iterator.s(); !(_step = _iterator.n()).done;) {
          var item = _step.value;
          if (item.field === value) {
            return item.title;
          }
        }
      } catch (err) {
        _iterator.e(err);
      } finally {
        _iterator.f();
      }
      return '';
    },
    setFieldIndex: function setFieldIndex(columns) {
      var totalCol = 0;
      var flag = [];
      var _iterator2 = _createForOfIteratorHelper(columns[0]),
        _step2;
      try {
        for (_iterator2.s(); !(_step2 = _iterator2.n()).done;) {
          var column = _step2.value;
          totalCol += column.colspan || 1;
        }
      } catch (err) {
        _iterator2.e(err);
      } finally {
        _iterator2.f();
      }
      for (var i = 0; i < columns.length; i++) {
        flag[i] = [];
        for (var j = 0; j < totalCol; j++) {
          flag[i][j] = false;
        }
      }
      for (var _i = 0; _i < columns.length; _i++) {
        var _iterator3 = _createForOfIteratorHelper(columns[_i]),
          _step3;
        try {
          for (_iterator3.s(); !(_step3 = _iterator3.n()).done;) {
            var r = _step3.value;
            var rowspan = r.rowspan || 1;
            var colspan = r.colspan || 1;
            var index = flag[_i].indexOf(false);
            r.colspanIndex = index;
            if (colspan === 1) {
              r.fieldIndex = index;
              // when field is undefined, use index instead
              if (typeof r.field === 'undefined') {
                r.field = index;
              }
            } else {
              r.colspanGroup = r.colspan;
            }
            for (var _j = 0; _j < rowspan; _j++) {
              for (var k = 0; k < colspan; k++) {
                flag[_i + _j][index + k] = true;
              }
            }
          }
        } catch (err) {
          _iterator3.e(err);
        } finally {
          _iterator3.f();
        }
      }
    },
    normalizeAccent: function normalizeAccent(value) {
      if (typeof value !== 'string') {
        return value;
      }
      return value.normalize('NFD').replace(/[\u0300-\u036f]/g, '');
    },
    updateFieldGroup: function updateFieldGroup(columns, fieldColumns) {
      var _ref;
      var allColumns = (_ref = []).concat.apply(_ref, _toConsumableArray(columns));
      var _iterator4 = _createForOfIteratorHelper(columns),
        _step4;
      try {
        for (_iterator4.s(); !(_step4 = _iterator4.n()).done;) {
          var c = _step4.value;
          var _iterator6 = _createForOfIteratorHelper(c),
            _step6;
          try {
            for (_iterator6.s(); !(_step6 = _iterator6.n()).done;) {
              var r = _step6.value;
              if (r.colspanGroup > 1) {
                var colspan = 0;
                var _loop = function _loop(i) {
                  var underColumns = allColumns.filter(function (col) {
                    return col.fieldIndex === i;
                  });
                  var column = underColumns[underColumns.length - 1];
                  if (underColumns.length > 1) {
                    for (var j = 0; j < underColumns.length - 1; j++) {
                      underColumns[j].visible = column.visible;
                    }
                  }
                  if (column.visible) {
                    colspan++;
                  }
                };
                for (var i = r.colspanIndex; i < r.colspanIndex + r.colspanGroup; i++) {
                  _loop(i);
                }
                r.colspan = colspan;
                r.visible = colspan > 0;
              }
            }
          } catch (err) {
            _iterator6.e(err);
          } finally {
            _iterator6.f();
          }
        }
      } catch (err) {
        _iterator4.e(err);
      } finally {
        _iterator4.f();
      }
      if (columns.length < 2) {
        return;
      }
      var _iterator5 = _createForOfIteratorHelper(fieldColumns),
        _step5;
      try {
        var _loop2 = function _loop2() {
          var column = _step5.value;
          var sameColumns = allColumns.filter(function (col) {
            return col.fieldIndex === column.fieldIndex;
          });
          if (sameColumns.length > 1) {
            var _iterator7 = _createForOfIteratorHelper(sameColumns),
              _step7;
            try {
              for (_iterator7.s(); !(_step7 = _iterator7.n()).done;) {
                var _c = _step7.value;
                _c.visible = column.visible;
              }
            } catch (err) {
              _iterator7.e(err);
            } finally {
              _iterator7.f();
            }
          }
        };
        for (_iterator5.s(); !(_step5 = _iterator5.n()).done;) {
          _loop2();
        }
      } catch (err) {
        _iterator5.e(err);
      } finally {
        _iterator5.f();
      }
    },
    getScrollBarWidth: function getScrollBarWidth() {
      if (this.cachedWidth === undefined) {
        var $inner = $$q('<div/>').addClass('fixed-table-scroll-inner');
        var $outer = $$q('<div/>').addClass('fixed-table-scroll-outer');
        $outer.append($inner);
        $$q('body').append($outer);
        var w1 = $inner[0].offsetWidth;
        $outer.css('overflow', 'scroll');
        var w2 = $inner[0].offsetWidth;
        if (w1 === w2) {
          w2 = $outer[0].clientWidth;
        }
        $outer.remove();
        this.cachedWidth = w1 - w2;
      }
      return this.cachedWidth;
    },
    calculateObjectValue: function calculateObjectValue(self, name, args, defaultValue) {
      var func = name;
      if (typeof name === 'string') {
        // support obj.func1.func2
        var names = name.split('.');
        if (names.length > 1) {
          func = window;
          var _iterator8 = _createForOfIteratorHelper(names),
            _step8;
          try {
            for (_iterator8.s(); !(_step8 = _iterator8.n()).done;) {
              var f = _step8.value;
              func = func[f];
            }
          } catch (err) {
            _iterator8.e(err);
          } finally {
            _iterator8.f();
          }
        } else {
          func = window[name];
        }
      }
      if (func !== null && _typeof(func) === 'object') {
        return func;
      }
      if (typeof func === 'function') {
        return func.apply(self, args || []);
      }
      if (!func && typeof name === 'string' && args && this.sprintf.apply(this, [name].concat(_toConsumableArray(args)))) {
        return this.sprintf.apply(this, [name].concat(_toConsumableArray(args)));
      }
      return defaultValue;
    },
    compareObjects: function compareObjects(objectA, objectB, compareLength) {
      var aKeys = Object.keys(objectA);
      var bKeys = Object.keys(objectB);
      if (compareLength && aKeys.length !== bKeys.length) {
        return false;
      }
      for (var _i2 = 0, _aKeys = aKeys; _i2 < _aKeys.length; _i2++) {
        var key = _aKeys[_i2];
        if (bKeys.includes(key) && objectA[key] !== objectB[key]) {
          return false;
        }
      }
      return true;
    },
    regexCompare: function regexCompare(value, search) {
      try {
        var regexpParts = search.match(/^\/(.*?)\/([gim]*)$/);
        if (value.toString().search(regexpParts ? new RegExp(regexpParts[1], regexpParts[2]) : new RegExp(search, 'gim')) !== -1) {
          return true;
        }
      } catch (e) {
        return false;
      }
      return false;
    },
    escapeApostrophe: function escapeApostrophe(value) {
      return value.toString().replace(/'/g, '&#39;');
    },
    escapeHTML: function escapeHTML(text) {
      if (!text) {
        return text;
      }
      return text.toString().replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;').replace(/'/g, '&#39;');
    },
    unescapeHTML: function unescapeHTML(text) {
      if (typeof text !== 'string' || !text) {
        return text;
      }
      return text.toString().replace(/&amp;/g, '&').replace(/&lt;/g, '<').replace(/&gt;/g, '>').replace(/&quot;/g, '"').replace(/&#39;/g, '\'');
    },
    removeHTML: function removeHTML(text) {
      if (!text) {
        return text;
      }
      return text.toString().replace(/(<([^>]+)>)/ig, '').replace(/&[#A-Za-z0-9]+;/gi, '').trim();
    },
    getRealDataAttr: function getRealDataAttr(dataAttr) {
      for (var _i3 = 0, _Object$entries = Object.entries(dataAttr); _i3 < _Object$entries.length; _i3++) {
        var _Object$entries$_i = _slicedToArray(_Object$entries[_i3], 2),
          attr = _Object$entries$_i[0],
          value = _Object$entries$_i[1];
        var auxAttr = attr.split(/(?=[A-Z])/).join('-').toLowerCase();
        if (auxAttr !== attr) {
          dataAttr[auxAttr] = value;
          delete dataAttr[attr];
        }
      }
      return dataAttr;
    },
    getItemField: function getItemField(item, field, escape) {
      var columnEscape = arguments.length > 3 && arguments[3] !== undefined ? arguments[3] : undefined;
      var value = item;

      // use column escape if it is defined
      if (typeof columnEscape !== 'undefined') {
        escape = columnEscape;
      }
      if (typeof field !== 'string' || item.hasOwnProperty(field)) {
        return escape ? this.escapeHTML(item[field]) : item[field];
      }
      var props = field.split('.');
      var _iterator9 = _createForOfIteratorHelper(props),
        _step9;
      try {
        for (_iterator9.s(); !(_step9 = _iterator9.n()).done;) {
          var p = _step9.value;
          value = value && value[p];
        }
      } catch (err) {
        _iterator9.e(err);
      } finally {
        _iterator9.f();
      }
      return escape ? this.escapeHTML(value) : value;
    },
    isIEBrowser: function isIEBrowser() {
      return navigator.userAgent.includes('MSIE ') || /Trident.*rv:11\./.test(navigator.userAgent);
    },
    findIndex: function findIndex(items, item) {
      var _iterator10 = _createForOfIteratorHelper(items),
        _step10;
      try {
        for (_iterator10.s(); !(_step10 = _iterator10.n()).done;) {
          var it = _step10.value;
          if (JSON.stringify(it) === JSON.stringify(item)) {
            return items.indexOf(it);
          }
        }
      } catch (err) {
        _iterator10.e(err);
      } finally {
        _iterator10.f();
      }
      return -1;
    },
    trToData: function trToData(columns, $els) {
      var _this2 = this;
      var data = [];
      var m = [];
      $els.each(function (y, el) {
        var $el = $$q(el);
        var row = {};

        // save tr's id, class and data-* attributes
        row._id = $el.attr('id');
        row._class = $el.attr('class');
        row._data = _this2.getRealDataAttr($el.data());
        row._style = $el.attr('style');
        $el.find('>td,>th').each(function (_x, el) {
          var $el = $$q(el);
          var colspan = +$el.attr('colspan') || 1;
          var rowspan = +$el.attr('rowspan') || 1;
          var x = _x;

          // skip already occupied cells in current row
          for (; m[y] && m[y][x]; x++) {
            // ignore
          }

          // mark matrix elements occupied by current cell with true
          for (var tx = x; tx < x + colspan; tx++) {
            for (var ty = y; ty < y + rowspan; ty++) {
              if (!m[ty]) {
                // fill missing rows
                m[ty] = [];
              }
              m[ty][tx] = true;
            }
          }
          var field = columns[x].field;
          row[field] = _this2.escapeApostrophe($el.html().trim());
          // save td's id, class and data-* attributes
          row["_".concat(field, "_id")] = $el.attr('id');
          row["_".concat(field, "_class")] = $el.attr('class');
          row["_".concat(field, "_rowspan")] = $el.attr('rowspan');
          row["_".concat(field, "_colspan")] = $el.attr('colspan');
          row["_".concat(field, "_title")] = $el.attr('title');
          row["_".concat(field, "_data")] = _this2.getRealDataAttr($el.data());
          row["_".concat(field, "_style")] = $el.attr('style');
        });
        data.push(row);
      });
      return data;
    },
    sort: function sort(a, b, order, options, aPosition, bPosition) {
      if (a === undefined || a === null) {
        a = '';
      }
      if (b === undefined || b === null) {
        b = '';
      }
      if (options.sortStable && a === b) {
        a = aPosition;
        b = bPosition;
      }

      // If both values are numeric, do a numeric comparison
      if (this.isNumeric(a) && this.isNumeric(b)) {
        // Convert numerical values form string to float.
        a = parseFloat(a);
        b = parseFloat(b);
        if (a < b) {
          return order * -1;
        }
        if (a > b) {
          return order;
        }
        return 0;
      }
      if (options.sortEmptyLast) {
        if (a === '') {
          return 1;
        }
        if (b === '') {
          return -1;
        }
      }
      if (a === b) {
        return 0;
      }

      // If value is not a string, convert to string
      if (typeof a !== 'string') {
        a = a.toString();
      }
      if (a.localeCompare(b) === -1) {
        return order * -1;
      }
      return order;
    },
    getEventName: function getEventName(eventPrefix) {
      var id = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : '';
      id = id || "".concat(+new Date()).concat(~~(Math.random() * 1000000));
      return "".concat(eventPrefix, "-").concat(id);
    },
    hasDetailViewIcon: function hasDetailViewIcon(options) {
      return options.detailView && options.detailViewIcon && !options.cardView;
    },
    getDetailViewIndexOffset: function getDetailViewIndexOffset(options) {
      return this.hasDetailViewIcon(options) && options.detailViewAlign !== 'right' ? 1 : 0;
    },
    checkAutoMergeCells: function checkAutoMergeCells(data) {
      var _iterator11 = _createForOfIteratorHelper(data),
        _step11;
      try {
        for (_iterator11.s(); !(_step11 = _iterator11.n()).done;) {
          var row = _step11.value;
          for (var _i4 = 0, _Object$keys = Object.keys(row); _i4 < _Object$keys.length; _i4++) {
            var key = _Object$keys[_i4];
            if (key.startsWith('_') && (key.endsWith('_rowspan') || key.endsWith('_colspan'))) {
              return true;
            }
          }
        }
      } catch (err) {
        _iterator11.e(err);
      } finally {
        _iterator11.f();
      }
      return false;
    },
    deepCopy: function deepCopy(arg) {
      if (arg === undefined) {
        return arg;
      }
      return this.extend(true, Array.isArray(arg) ? [] : {}, arg);
    },
    debounce: function debounce(func, wait, immediate) {
      var timeout;
      return function executedFunction() {
        var context = this;
        var args = arguments;
        var later = function later() {
          timeout = null;
          if (!immediate) func.apply(context, args);
        };
        var callNow = immediate && !timeout;
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
        if (callNow) func.apply(context, args);
      };
    }
  };

  var VERSION = '1.22.6';
  var bootstrapVersion = Utils.getBootstrapVersion();
  var CONSTANTS = {
    3: {
      classes: {
        buttonsPrefix: 'btn',
        buttons: 'default',
        buttonsGroup: 'btn-group',
        buttonsDropdown: 'btn-group',
        pull: 'pull',
        inputGroup: 'input-group',
        inputPrefix: 'input-',
        input: 'form-control',
        select: 'form-control',
        paginationDropdown: 'btn-group dropdown',
        dropup: 'dropup',
        dropdownActive: 'active',
        paginationActive: 'active',
        buttonActive: 'active'
      },
      html: {
        toolbarDropdown: ['<ul class="dropdown-menu" role="menu">', '</ul>'],
        toolbarDropdownItem: '<li class="dropdown-item-marker" role="menuitem"><label>%s</label></li>',
        toolbarDropdownSeparator: '<li class="divider"></li>',
        pageDropdown: ['<ul class="dropdown-menu" role="menu">', '</ul>'],
        pageDropdownItem: '<li role="menuitem" class="%s"><a href="#">%s</a></li>',
        dropdownCaret: '<span class="caret"></span>',
        pagination: ['<ul class="pagination%s">', '</ul>'],
        paginationItem: '<li class="page-item%s"><a class="page-link" aria-label="%s" href="javascript:void(0)">%s</a></li>',
        icon: '<i class="%s %s"></i>',
        inputGroup: '<div class="input-group">%s<span class="input-group-btn">%s</span></div>',
        searchInput: '<input class="%s%s" type="text" placeholder="%s">',
        searchButton: '<button class="%s" type="button" name="search" title="%s">%s %s</button>',
        searchClearButton: '<button class="%s" type="button" name="clearSearch" title="%s">%s %s</button>'
      }
    },
    4: {
      classes: {
        buttonsPrefix: 'btn',
        buttons: 'secondary',
        buttonsGroup: 'btn-group',
        buttonsDropdown: 'btn-group',
        pull: 'float',
        inputGroup: 'btn-group',
        inputPrefix: 'form-control-',
        input: 'form-control',
        select: 'form-control',
        paginationDropdown: 'btn-group dropdown',
        dropup: 'dropup',
        dropdownActive: 'active',
        paginationActive: 'active',
        buttonActive: 'active'
      },
      html: {
        toolbarDropdown: ['<div class="dropdown-menu dropdown-menu-right">', '</div>'],
        toolbarDropdownItem: '<label class="dropdown-item dropdown-item-marker">%s</label>',
        pageDropdown: ['<div class="dropdown-menu">', '</div>'],
        pageDropdownItem: '<a class="dropdown-item %s" href="#">%s</a>',
        toolbarDropdownSeparator: '<div class="dropdown-divider"></div>',
        dropdownCaret: '<span class="caret"></span>',
        pagination: ['<ul class="pagination%s">', '</ul>'],
        paginationItem: '<li class="page-item%s"><a class="page-link" aria-label="%s" href="javascript:void(0)">%s</a></li>',
        icon: '<i class="%s %s"></i>',
        inputGroup: '<div class="input-group">%s<div class="input-group-append">%s</div></div>',
        searchInput: '<input class="%s%s" type="text" placeholder="%s">',
        searchButton: '<button class="%s" type="button" name="search" title="%s">%s %s</button>',
        searchClearButton: '<button class="%s" type="button" name="clearSearch" title="%s">%s %s</button>'
      }
    },
    5: {
      classes: {
        buttonsPrefix: 'btn',
        buttons: 'secondary',
        buttonsGroup: 'btn-group',
        buttonsDropdown: 'btn-group',
        pull: 'float',
        inputGroup: 'btn-group',
        inputPrefix: 'form-control-',
        input: 'form-control',
        select: 'form-select',
        paginationDropdown: 'btn-group dropdown',
        dropup: 'dropup',
        dropdownActive: 'active',
        paginationActive: 'active',
        buttonActive: 'active'
      },
      html: {
        dataToggle: 'data-bs-toggle',
        toolbarDropdown: ['<div class="dropdown-menu dropdown-menu-end">', '</div>'],
        toolbarDropdownItem: '<label class="dropdown-item dropdown-item-marker">%s</label>',
        pageDropdown: ['<div class="dropdown-menu">', '</div>'],
        pageDropdownItem: '<a class="dropdown-item %s" href="#">%s</a>',
        toolbarDropdownSeparator: '<div class="dropdown-divider"></div>',
        dropdownCaret: '<span class="caret"></span>',
        pagination: ['<ul class="pagination%s">', '</ul>'],
        paginationItem: '<li class="page-item%s"><a class="page-link" aria-label="%s" href="javascript:void(0)">%s</a></li>',
        icon: '<i class="%s %s"></i>',
        inputGroup: '<div class="input-group">%s%s</div>',
        searchInput: '<input class="%s%s" type="text" placeholder="%s">',
        searchButton: '<button class="%s" type="button" name="search" title="%s">%s %s</button>',
        searchClearButton: '<button class="%s" type="button" name="clearSearch" title="%s">%s %s</button>'
      }
    }
  }[bootstrapVersion];
  var DEFAULTS = {
    height: undefined,
    classes: 'table table-bordered table-hover',
    buttons: {},
    theadClasses: '',
    headerStyle: function headerStyle(column) {
      return {};
    },
    rowStyle: function rowStyle(row, index) {
      return {};
    },
    rowAttributes: function rowAttributes(row, index) {
      return {};
    },
    undefinedText: '-',
    locale: undefined,
    virtualScroll: false,
    virtualScrollItemHeight: undefined,
    sortable: true,
    sortClass: undefined,
    silentSort: true,
    sortEmptyLast: false,
    sortName: undefined,
    sortOrder: undefined,
    sortReset: false,
    sortStable: false,
    sortResetPage: false,
    rememberOrder: false,
    serverSort: true,
    customSort: undefined,
    columns: [[]],
    data: [],
    url: undefined,
    method: 'get',
    cache: true,
    contentType: 'application/json',
    dataType: 'json',
    ajax: undefined,
    ajaxOptions: {},
    queryParams: function queryParams(params) {
      return params;
    },
    queryParamsType: 'limit',
    // 'limit', undefined
    responseHandler: function responseHandler(res) {
      return res;
    },
    totalField: 'total',
    totalNotFilteredField: 'totalNotFiltered',
    dataField: 'rows',
    footerField: 'footer',
    pagination: false,
    paginationParts: ['pageInfo', 'pageSize', 'pageList'],
    showExtendedPagination: false,
    paginationLoop: true,
    sidePagination: 'client',
    // client or server
    totalRows: 0,
    totalNotFiltered: 0,
    pageNumber: 1,
    pageSize: 10,
    pageList: [10, 25, 50, 100],
    paginationHAlign: 'right',
    // right, left
    paginationVAlign: 'bottom',
    // bottom, top, both
    paginationDetailHAlign: 'left',
    // right, left
    paginationPreText: '&lsaquo;',
    paginationNextText: '&rsaquo;',
    paginationSuccessivelySize: 5,
    // Maximum successively number of pages in a row
    paginationPagesBySide: 1,
    // Number of pages on each side (right, left) of the current page.
    paginationUseIntermediate: false,
    // Calculate intermediate pages for quick access
    paginationLoadMore: false,
    search: false,
    searchable: false,
    searchHighlight: false,
    searchOnEnterKey: false,
    strictSearch: false,
    regexSearch: false,
    searchSelector: false,
    visibleSearch: false,
    showButtonIcons: true,
    showButtonText: false,
    showSearchButton: false,
    showSearchClearButton: false,
    trimOnSearch: true,
    searchAlign: 'right',
    searchTimeOut: 500,
    searchText: '',
    customSearch: undefined,
    showHeader: true,
    showFooter: false,
    footerStyle: function footerStyle(column) {
      return {};
    },
    searchAccentNeutralise: false,
    showColumns: false,
    showColumnsToggleAll: false,
    showColumnsSearch: false,
    minimumCountColumns: 1,
    showPaginationSwitch: false,
    showRefresh: false,
    showToggle: false,
    showFullscreen: false,
    smartDisplay: true,
    escape: false,
    escapeTitle: true,
    filterOptions: {
      filterAlgorithm: 'and'
    },
    idField: undefined,
    selectItemName: 'btSelectItem',
    clickToSelect: false,
    ignoreClickToSelectOn: function ignoreClickToSelectOn(_ref) {
      var tagName = _ref.tagName;
      return ['A', 'BUTTON'].includes(tagName);
    },
    singleSelect: false,
    checkboxHeader: true,
    maintainMetaData: false,
    multipleSelectRow: false,
    uniqueId: undefined,
    cardView: false,
    detailView: false,
    detailViewIcon: true,
    detailViewByClick: false,
    detailViewAlign: 'left',
    detailFormatter: function detailFormatter(index, row) {
      return '';
    },
    detailFilter: function detailFilter(index, row) {
      return true;
    },
    toolbar: undefined,
    toolbarAlign: 'left',
    buttonsToolbar: undefined,
    buttonsAlign: 'right',
    buttonsOrder: ['paginationSwitch', 'refresh', 'toggle', 'fullscreen', 'columns'],
    buttonsPrefix: CONSTANTS.classes.buttonsPrefix,
    buttonsClass: CONSTANTS.classes.buttons,
    iconsPrefix: undefined,
    // init in initConstants
    icons: {},
    // init in initConstants
    iconSize: undefined,
    fixedScroll: false,
    loadingFontSize: 'auto',
    loadingTemplate: function loadingTemplate(loadingMessage) {
      return "<span class=\"loading-wrap\">\n      <span class=\"loading-text\">".concat(loadingMessage, "</span>\n      <span class=\"animation-wrap\"><span class=\"animation-dot\"></span></span>\n      </span>\n    ");
    },
    onAll: function onAll(name, args) {
      return false;
    },
    onClickCell: function onClickCell(field, value, row, $element) {
      return false;
    },
    onDblClickCell: function onDblClickCell(field, value, row, $element) {
      return false;
    },
    onClickRow: function onClickRow(item, $element) {
      return false;
    },
    onDblClickRow: function onDblClickRow(item, $element) {
      return false;
    },
    onSort: function onSort(name, order) {
      return false;
    },
    onCheck: function onCheck(row) {
      return false;
    },
    onUncheck: function onUncheck(row) {
      return false;
    },
    onCheckAll: function onCheckAll(rows) {
      return false;
    },
    onUncheckAll: function onUncheckAll(rows) {
      return false;
    },
    onCheckSome: function onCheckSome(rows) {
      return false;
    },
    onUncheckSome: function onUncheckSome(rows) {
      return false;
    },
    onLoadSuccess: function onLoadSuccess(data) {
      return false;
    },
    onLoadError: function onLoadError(status) {
      return false;
    },
    onColumnSwitch: function onColumnSwitch(field, checked) {
      return false;
    },
    onColumnSwitchAll: function onColumnSwitchAll(checked) {
      return false;
    },
    onPageChange: function onPageChange(number, size) {
      return false;
    },
    onSearch: function onSearch(text) {
      return false;
    },
    onToggle: function onToggle(cardView) {
      return false;
    },
    onPreBody: function onPreBody(data) {
      return false;
    },
    onPostBody: function onPostBody() {
      return false;
    },
    onPostHeader: function onPostHeader() {
      return false;
    },
    onPostFooter: function onPostFooter() {
      return false;
    },
    onExpandRow: function onExpandRow(index, row, $detail) {
      return false;
    },
    onCollapseRow: function onCollapseRow(index, row) {
      return false;
    },
    onRefreshOptions: function onRefreshOptions(options) {
      return false;
    },
    onRefresh: function onRefresh(params) {
      return false;
    },
    onResetView: function onResetView() {
      return false;
    },
    onScrollBody: function onScrollBody() {
      return false;
    },
    onTogglePagination: function onTogglePagination(newState) {
      return false;
    },
    onVirtualScroll: function onVirtualScroll(startIndex, endIndex) {
      return false;
    }
  };
  var EN = {
    formatLoadingMessage: function formatLoadingMessage() {
      return 'Loading, please wait';
    },
    formatRecordsPerPage: function formatRecordsPerPage(pageNumber) {
      return "".concat(pageNumber, " rows per page");
    },
    formatShowingRows: function formatShowingRows(pageFrom, pageTo, totalRows, totalNotFiltered) {
      if (totalNotFiltered !== undefined && totalNotFiltered > 0 && totalNotFiltered > totalRows) {
        return "Showing ".concat(pageFrom, " to ").concat(pageTo, " of ").concat(totalRows, " rows (filtered from ").concat(totalNotFiltered, " total rows)");
      }
      return "Showing ".concat(pageFrom, " to ").concat(pageTo, " of ").concat(totalRows, " rows");
    },
    formatSRPaginationPreText: function formatSRPaginationPreText() {
      return 'previous page';
    },
    formatSRPaginationPageText: function formatSRPaginationPageText(page) {
      return "to page ".concat(page);
    },
    formatSRPaginationNextText: function formatSRPaginationNextText() {
      return 'next page';
    },
    formatDetailPagination: function formatDetailPagination(totalRows) {
      return "Showing ".concat(totalRows, " rows");
    },
    formatSearch: function formatSearch() {
      return 'Search';
    },
    formatClearSearch: function formatClearSearch() {
      return 'Clear Search';
    },
    formatNoMatches: function formatNoMatches() {
      return 'No matching records found';
    },
    formatPaginationSwitch: function formatPaginationSwitch() {
      return 'Hide/Show pagination';
    },
    formatPaginationSwitchDown: function formatPaginationSwitchDown() {
      return 'Show pagination';
    },
    formatPaginationSwitchUp: function formatPaginationSwitchUp() {
      return 'Hide pagination';
    },
    formatRefresh: function formatRefresh() {
      return 'Refresh';
    },
    formatToggleOn: function formatToggleOn() {
      return 'Show card view';
    },
    formatToggleOff: function formatToggleOff() {
      return 'Hide card view';
    },
    formatColumns: function formatColumns() {
      return 'Columns';
    },
    formatColumnsToggleAll: function formatColumnsToggleAll() {
      return 'Toggle all';
    },
    formatFullscreen: function formatFullscreen() {
      return 'Fullscreen';
    },
    formatAllRows: function formatAllRows() {
      return 'All';
    }
  };
  var COLUMN_DEFAULTS = {
    field: undefined,
    title: undefined,
    titleTooltip: undefined,
    class: undefined,
    width: undefined,
    widthUnit: 'px',
    rowspan: undefined,
    colspan: undefined,
    align: undefined,
    // left, right, center
    halign: undefined,
    // left, right, center
    falign: undefined,
    // left, right, center
    valign: undefined,
    // top, middle, bottom
    cellStyle: undefined,
    radio: false,
    checkbox: false,
    checkboxEnabled: true,
    clickToSelect: true,
    showSelectTitle: false,
    sortable: false,
    sortName: undefined,
    order: 'asc',
    // asc, desc
    sorter: undefined,
    visible: true,
    switchable: true,
    switchableLabel: undefined,
    cardVisible: true,
    searchable: true,
    formatter: undefined,
    footerFormatter: undefined,
    footerStyle: undefined,
    detailFormatter: undefined,
    searchFormatter: true,
    searchHighlightFormatter: false,
    escape: undefined,
    events: undefined
  };
  var METHODS = ['getOptions', 'refreshOptions', 'getData', 'getSelections', 'load', 'append', 'prepend', 'remove', 'removeAll', 'insertRow', 'updateRow', 'getRowByUniqueId', 'updateByUniqueId', 'removeByUniqueId', 'updateCell', 'updateCellByUniqueId', 'showRow', 'hideRow', 'getHiddenRows', 'showColumn', 'hideColumn', 'getVisibleColumns', 'getHiddenColumns', 'showAllColumns', 'hideAllColumns', 'mergeCells', 'checkAll', 'uncheckAll', 'checkInvert', 'check', 'uncheck', 'checkBy', 'uncheckBy', 'refresh', 'destroy', 'resetView', 'showLoading', 'hideLoading', 'togglePagination', 'toggleFullscreen', 'toggleView', 'resetSearch', 'filterBy', 'sortBy', 'scrollTo', 'getScrollPosition', 'selectPage', 'prevPage', 'nextPage', 'toggleDetailView', 'expandRow', 'collapseRow', 'expandRowByUniqueId', 'collapseRowByUniqueId', 'expandAllRows', 'collapseAllRows', 'updateColumnTitle', 'updateFormatText'];
  var EVENTS = {
    'all.bs.table': 'onAll',
    'click-row.bs.table': 'onClickRow',
    'dbl-click-row.bs.table': 'onDblClickRow',
    'click-cell.bs.table': 'onClickCell',
    'dbl-click-cell.bs.table': 'onDblClickCell',
    'sort.bs.table': 'onSort',
    'check.bs.table': 'onCheck',
    'uncheck.bs.table': 'onUncheck',
    'check-all.bs.table': 'onCheckAll',
    'uncheck-all.bs.table': 'onUncheckAll',
    'check-some.bs.table': 'onCheckSome',
    'uncheck-some.bs.table': 'onUncheckSome',
    'load-success.bs.table': 'onLoadSuccess',
    'load-error.bs.table': 'onLoadError',
    'column-switch.bs.table': 'onColumnSwitch',
    'column-switch-all.bs.table': 'onColumnSwitchAll',
    'page-change.bs.table': 'onPageChange',
    'search.bs.table': 'onSearch',
    'toggle.bs.table': 'onToggle',
    'pre-body.bs.table': 'onPreBody',
    'post-body.bs.table': 'onPostBody',
    'post-header.bs.table': 'onPostHeader',
    'post-footer.bs.table': 'onPostFooter',
    'expand-row.bs.table': 'onExpandRow',
    'collapse-row.bs.table': 'onCollapseRow',
    'refresh-options.bs.table': 'onRefreshOptions',
    'reset-view.bs.table': 'onResetView',
    'refresh.bs.table': 'onRefresh',
    'scroll-body.bs.table': 'onScrollBody',
    'toggle-pagination.bs.table': 'onTogglePagination',
    'virtual-scroll.bs.table': 'onVirtualScroll'
  };
  Object.assign(DEFAULTS, EN);
  var Constants = {
    VERSION: VERSION,
    THEME: "bootstrap".concat(bootstrapVersion),
    CONSTANTS: CONSTANTS,
    DEFAULTS: DEFAULTS,
    COLUMN_DEFAULTS: COLUMN_DEFAULTS,
    METHODS: METHODS,
    EVENTS: EVENTS,
    LOCALES: {
      en: EN,
      'en-US': EN
    }
  };

  var BLOCK_ROWS = 50;
  var CLUSTER_BLOCKS = 4;
  var VirtualScroll = /*#__PURE__*/function () {
    function VirtualScroll(options) {
      var _this = this;
      _classCallCheck(this, VirtualScroll);
      this.rows = options.rows;
      this.scrollEl = options.scrollEl;
      this.contentEl = options.contentEl;
      this.callback = options.callback;
      this.itemHeight = options.itemHeight;
      this.cache = {};
      this.scrollTop = this.scrollEl.scrollTop;
      this.initDOM(this.rows, options.fixedScroll);
      this.scrollEl.scrollTop = this.scrollTop;
      this.lastCluster = 0;
      var onScroll = function onScroll() {
        if (_this.lastCluster !== (_this.lastCluster = _this.getNum())) {
          _this.initDOM(_this.rows);
          _this.callback(_this.startIndex, _this.endIndex);
        }
      };
      this.scrollEl.addEventListener('scroll', onScroll, false);
      this.destroy = function () {
        _this.contentEl.innerHtml = '';
        _this.scrollEl.removeEventListener('scroll', onScroll, false);
      };
    }
    return _createClass(VirtualScroll, [{
      key: "initDOM",
      value: function initDOM(rows, fixedScroll) {
        if (typeof this.clusterHeight === 'undefined') {
          this.cache.scrollTop = this.scrollEl.scrollTop;
          this.cache.data = this.contentEl.innerHTML = rows[0] + rows[0] + rows[0];
          this.getRowsHeight(rows);
        } else if (this.blockHeight === 0) {
          this.getRowsHeight(rows);
        }
        var data = this.initData(rows, this.getNum(fixedScroll));
        var thisRows = data.rows.join('');
        var dataChanged = this.checkChanges('data', thisRows);
        var topOffsetChanged = this.checkChanges('top', data.topOffset);
        var bottomOffsetChanged = this.checkChanges('bottom', data.bottomOffset);
        var html = [];
        if (dataChanged && topOffsetChanged) {
          if (data.topOffset) {
            html.push(this.getExtra('top', data.topOffset));
          }
          html.push(thisRows);
          if (data.bottomOffset) {
            html.push(this.getExtra('bottom', data.bottomOffset));
          }
          this.startIndex = data.start;
          this.endIndex = data.end;
          this.contentEl.innerHTML = html.join('');
          if (fixedScroll) {
            this.contentEl.scrollTop = this.cache.scrollTop;
          }
        } else if (bottomOffsetChanged) {
          this.contentEl.lastChild.style.height = "".concat(data.bottomOffset, "px");
        }
      }
    }, {
      key: "getRowsHeight",
      value: function getRowsHeight() {
        if (typeof this.itemHeight === 'undefined' || this.itemHeight === 0) {
          var nodes = this.contentEl.children;
          var node = nodes[Math.floor(nodes.length / 2)];
          this.itemHeight = node.offsetHeight;
        }
        this.blockHeight = this.itemHeight * BLOCK_ROWS;
        this.clusterRows = BLOCK_ROWS * CLUSTER_BLOCKS;
        this.clusterHeight = this.blockHeight * CLUSTER_BLOCKS;
      }
    }, {
      key: "getNum",
      value: function getNum(fixedScroll) {
        this.scrollTop = fixedScroll ? this.cache.scrollTop : this.scrollEl.scrollTop;
        return Math.floor(this.scrollTop / (this.clusterHeight - this.blockHeight)) || 0;
      }
    }, {
      key: "initData",
      value: function initData(rows, num) {
        if (rows.length < BLOCK_ROWS) {
          return {
            topOffset: 0,
            bottomOffset: 0,
            rowsAbove: 0,
            rows: rows
          };
        }
        var start = Math.max((this.clusterRows - BLOCK_ROWS) * num, 0);
        var end = start + this.clusterRows;
        var topOffset = Math.max(start * this.itemHeight, 0);
        var bottomOffset = Math.max((rows.length - end) * this.itemHeight, 0);
        var thisRows = [];
        var rowsAbove = start;
        if (topOffset < 1) {
          rowsAbove++;
        }
        for (var i = start; i < end; i++) {
          rows[i] && thisRows.push(rows[i]);
        }
        return {
          start: start,
          end: end,
          topOffset: topOffset,
          bottomOffset: bottomOffset,
          rowsAbove: rowsAbove,
          rows: thisRows
        };
      }
    }, {
      key: "checkChanges",
      value: function checkChanges(type, value) {
        var changed = value !== this.cache[type];
        this.cache[type] = value;
        return changed;
      }
    }, {
      key: "getExtra",
      value: function getExtra(className, height) {
        var tag = document.createElement('tr');
        tag.className = "virtual-scroll-".concat(className);
        if (height) {
          tag.style.height = "".concat(height, "px");
        }
        return tag.outerHTML;
      }
    }]);
  }();

  var BootstrapTable = /*#__PURE__*/function () {
    function BootstrapTable(el, options) {
      _classCallCheck(this, BootstrapTable);
      this.options = options;
      this.$el = $$q(el);
      this.$el_ = this.$el.clone();
      this.timeoutId_ = 0;
      this.timeoutFooter_ = 0;
    }
    return _createClass(BootstrapTable, [{
      key: "init",
      value: function init() {
        this.initConstants();
        this.initLocale();
        this.initContainer();
        this.initTable();
        this.initHeader();
        this.initData();
        this.initHiddenRows();
        this.initToolbar();
        this.initPagination();
        this.initBody();
        this.initSearchText();
        this.initServer();
      }
    }, {
      key: "initConstants",
      value: function initConstants() {
        var opts = this.options;
        this.constants = Constants.CONSTANTS;
        this.constants.theme = $$q.fn.bootstrapTable.theme;
        this.constants.dataToggle = this.constants.html.dataToggle || 'data-toggle';

        // init iconsPrefix and icons
        var iconsPrefix = Utils.getIconsPrefix($$q.fn.bootstrapTable.theme);
        if (typeof opts.icons === 'string') {
          opts.icons = Utils.calculateObjectValue(null, opts.icons);
        }
        opts.iconsPrefix = opts.iconsPrefix || $$q.fn.bootstrapTable.defaults.iconsPrefix || iconsPrefix;
        opts.icons = Object.assign(Utils.getIcons(opts.iconsPrefix), $$q.fn.bootstrapTable.defaults.icons, opts.icons);

        // init buttons class
        var buttonsPrefix = opts.buttonsPrefix ? "".concat(opts.buttonsPrefix, "-") : '';
        this.constants.buttonsClass = [opts.buttonsPrefix, buttonsPrefix + opts.buttonsClass, Utils.sprintf("".concat(buttonsPrefix, "%s"), opts.iconSize)].join(' ').trim();
        this.buttons = Utils.calculateObjectValue(this, opts.buttons, [], {});
        if (_typeof(this.buttons) !== 'object') {
          this.buttons = {};
        }
      }
    }, {
      key: "initLocale",
      value: function initLocale() {
        if (this.options.locale) {
          var locales = $$q.fn.bootstrapTable.locales;
          var parts = this.options.locale.split(/-|_/);
          parts[0] = parts[0].toLowerCase();
          if (parts[1]) {
            parts[1] = parts[1].toUpperCase();
          }
          var localesToExtend = {};
          if (locales[this.options.locale]) {
            localesToExtend = locales[this.options.locale];
          } else if (locales[parts.join('-')]) {
            localesToExtend = locales[parts.join('-')];
          } else if (locales[parts[0]]) {
            localesToExtend = locales[parts[0]];
          }
          this._defaultLocales = this._defaultLocales || {};
          for (var _i = 0, _Object$entries = Object.entries(localesToExtend); _i < _Object$entries.length; _i++) {
            var _Object$entries$_i = _slicedToArray(_Object$entries[_i], 2),
              formatName = _Object$entries$_i[0],
              func = _Object$entries$_i[1];
            var defaultLocale = this._defaultLocales.hasOwnProperty(formatName) ? this._defaultLocales[formatName] : BootstrapTable.DEFAULTS[formatName];
            if (this.options[formatName] !== defaultLocale) {
              continue;
            }
            this.options[formatName] = func;
            this._defaultLocales[formatName] = func;
          }
        }
      }
    }, {
      key: "initContainer",
      value: function initContainer() {
        var topPagination = ['top', 'both'].includes(this.options.paginationVAlign) ? '<div class="fixed-table-pagination clearfix"></div>' : '';
        var bottomPagination = ['bottom', 'both'].includes(this.options.paginationVAlign) ? '<div class="fixed-table-pagination"></div>' : '';
        var loadingTemplate = Utils.calculateObjectValue(this.options, this.options.loadingTemplate, [this.options.formatLoadingMessage()]);
        this.$container = $$q("\n      <div class=\"bootstrap-table ".concat(this.constants.theme, "\">\n      <div class=\"fixed-table-toolbar\"></div>\n      ").concat(topPagination, "\n      <div class=\"fixed-table-container\">\n      <div class=\"fixed-table-header\"><table></table></div>\n      <div class=\"fixed-table-body\">\n      <div class=\"fixed-table-loading\">\n      ").concat(loadingTemplate, "\n      </div>\n      </div>\n      <div class=\"fixed-table-footer\"></div>\n      </div>\n      ").concat(bottomPagination, "\n      </div>\n    "));
        this.$container.insertAfter(this.$el);
        this.$tableContainer = this.$container.find('.fixed-table-container');
        this.$tableHeader = this.$container.find('.fixed-table-header');
        this.$tableBody = this.$container.find('.fixed-table-body');
        this.$tableLoading = this.$container.find('.fixed-table-loading');
        this.$tableFooter = this.$el.find('tfoot');
        // checking if custom table-toolbar exists or not
        if (this.options.buttonsToolbar) {
          this.$toolbar = $$q('body').find(this.options.buttonsToolbar);
        } else {
          this.$toolbar = this.$container.find('.fixed-table-toolbar');
        }
        this.$pagination = this.$container.find('.fixed-table-pagination');
        this.$tableBody.append(this.$el);
        this.$container.after('<div class="clearfix"></div>');
        this.$el.addClass(this.options.classes);
        this.$tableLoading.addClass(this.options.classes);
        if (this.options.height) {
          this.$tableContainer.addClass('fixed-height');
          if (this.options.showFooter) {
            this.$tableContainer.addClass('has-footer');
          }
          if (this.options.classes.split(' ').includes('table-bordered')) {
            this.$tableBody.append('<div class="fixed-table-border"></div>');
            this.$tableBorder = this.$tableBody.find('.fixed-table-border');
            this.$tableLoading.addClass('fixed-table-border');
          }
          this.$tableFooter = this.$container.find('.fixed-table-footer');
        }
      }
    }, {
      key: "initTable",
      value: function initTable() {
        var _this = this;
        var columns = [];
        this.$header = this.$el.find('>thead');
        if (!this.$header.length) {
          this.$header = $$q("<thead class=\"".concat(this.options.theadClasses, "\"></thead>")).appendTo(this.$el);
        } else if (this.options.theadClasses) {
          this.$header.addClass(this.options.theadClasses);
        }
        this._headerTrClasses = [];
        this._headerTrStyles = [];
        this.$header.find('tr').each(function (i, el) {
          var $tr = $$q(el);
          var column = [];
          $tr.find('th').each(function (i, el) {
            var $th = $$q(el);

            // #2014: getFieldIndex and elsewhere assume this is string, causes issues if not
            if (typeof $th.data('field') !== 'undefined') {
              $th.data('field', "".concat($th.data('field')));
            }
            var _data = Object.assign({}, $th.data());
            for (var key in _data) {
              if ($$q.fn.bootstrapTable.columnDefaults.hasOwnProperty(key)) {
                delete _data[key];
              }
            }
            column.push(Utils.extend({}, {
              _data: Utils.getRealDataAttr(_data),
              title: $th.html(),
              class: $th.attr('class'),
              titleTooltip: $th.attr('title'),
              rowspan: $th.attr('rowspan') ? +$th.attr('rowspan') : undefined,
              colspan: $th.attr('colspan') ? +$th.attr('colspan') : undefined
            }, $th.data()));
          });
          columns.push(column);
          if ($tr.attr('class')) {
            _this._headerTrClasses.push($tr.attr('class'));
          }
          if ($tr.attr('style')) {
            _this._headerTrStyles.push($tr.attr('style'));
          }
        });
        if (!Array.isArray(this.options.columns[0])) {
          this.options.columns = [this.options.columns];
        }
        this.options.columns = Utils.extend(true, [], columns, this.options.columns);
        this.columns = [];
        this.fieldsColumnsIndex = [];
        Utils.setFieldIndex(this.options.columns);
        this.options.columns.forEach(function (columns, i) {
          columns.forEach(function (_column, j) {
            var column = Utils.extend({}, BootstrapTable.COLUMN_DEFAULTS, _column, {
              passed: _column
            });
            if (typeof column.fieldIndex !== 'undefined') {
              _this.columns[column.fieldIndex] = column;
              _this.fieldsColumnsIndex[column.field] = column.fieldIndex;
            }
            _this.options.columns[i][j] = column;
          });
        });

        // if options.data is setting, do not process tbody and tfoot data
        if (!this.options.data.length) {
          var htmlData = Utils.trToData(this.columns, this.$el.find('>tbody>tr'));
          if (htmlData.length) {
            this.options.data = htmlData;
            this.fromHtml = true;
          }
        }
        if (!(this.options.pagination && this.options.sidePagination !== 'server')) {
          this.footerData = Utils.trToData(this.columns, this.$el.find('>tfoot>tr'));
        }
        if (this.footerData) {
          this.$el.find('tfoot').html('<tr></tr>');
        }
        if (!this.options.showFooter || this.options.cardView) {
          this.$tableFooter.hide();
        } else {
          this.$tableFooter.show();
        }
      }
    }, {
      key: "initHeader",
      value: function initHeader() {
        var _this2 = this;
        var visibleColumns = {};
        var headerHtml = [];
        this.header = {
          fields: [],
          styles: [],
          classes: [],
          formatters: [],
          detailFormatters: [],
          events: [],
          sorters: [],
          sortNames: [],
          cellStyles: [],
          searchables: []
        };
        Utils.updateFieldGroup(this.options.columns, this.columns);
        this.options.columns.forEach(function (columns, i) {
          var html = [];
          html.push("<tr".concat(Utils.sprintf(' class="%s"', _this2._headerTrClasses[i]), " ").concat(Utils.sprintf(' style="%s"', _this2._headerTrStyles[i]), ">"));
          var detailViewTemplate = '';
          if (i === 0 && Utils.hasDetailViewIcon(_this2.options)) {
            var rowspan = _this2.options.columns.length > 1 ? " rowspan=\"".concat(_this2.options.columns.length, "\"") : '';
            detailViewTemplate = "<th class=\"detail\"".concat(rowspan, ">\n          <div class=\"fht-cell\"></div>\n          </th>");
          }
          if (detailViewTemplate && _this2.options.detailViewAlign !== 'right') {
            html.push(detailViewTemplate);
          }
          columns.forEach(function (column, j) {
            var class_ = Utils.sprintf(' class="%s"', column['class']);
            var unitWidth = column.widthUnit;
            var width = parseFloat(column.width);
            var columnHalign = column.halign ? column.halign : column.align;
            var halign = Utils.sprintf('text-align: %s; ', columnHalign);
            var align = Utils.sprintf('text-align: %s; ', column.align);
            var style = Utils.sprintf('vertical-align: %s; ', column.valign);
            style += Utils.sprintf('width: %s; ', (column.checkbox || column.radio) && !width ? !column.showSelectTitle ? '36px' : undefined : width ? width + unitWidth : undefined);
            if (typeof column.fieldIndex === 'undefined' && !column.visible) {
              return;
            }
            var headerStyle = Utils.calculateObjectValue(null, _this2.options.headerStyle, [column]);
            var csses = [];
            var data_ = [];
            var classes = '';
            if (headerStyle && headerStyle.css) {
              for (var _i2 = 0, _Object$entries2 = Object.entries(headerStyle.css); _i2 < _Object$entries2.length; _i2++) {
                var _Object$entries2$_i = _slicedToArray(_Object$entries2[_i2], 2),
                  key = _Object$entries2$_i[0],
                  value = _Object$entries2$_i[1];
                csses.push("".concat(key, ": ").concat(value));
              }
            }
            if (headerStyle && headerStyle.classes) {
              classes = Utils.sprintf(' class="%s"', column['class'] ? [column['class'], headerStyle.classes].join(' ') : headerStyle.classes);
            }
            if (typeof column.fieldIndex !== 'undefined') {
              _this2.header.fields[column.fieldIndex] = column.field;
              _this2.header.styles[column.fieldIndex] = align + style;
              _this2.header.classes[column.fieldIndex] = class_;
              _this2.header.formatters[column.fieldIndex] = column.formatter;
              _this2.header.detailFormatters[column.fieldIndex] = column.detailFormatter;
              _this2.header.events[column.fieldIndex] = column.events;
              _this2.header.sorters[column.fieldIndex] = column.sorter;
              _this2.header.sortNames[column.fieldIndex] = column.sortName;
              _this2.header.cellStyles[column.fieldIndex] = column.cellStyle;
              _this2.header.searchables[column.fieldIndex] = column.searchable;
              if (!column.visible) {
                return;
              }
              if (_this2.options.cardView && !column.cardVisible) {
                return;
              }
              visibleColumns[column.field] = column;
            }
            if (Object.keys(column._data || {}).length > 0) {
              for (var _i3 = 0, _Object$entries3 = Object.entries(column._data); _i3 < _Object$entries3.length; _i3++) {
                var _Object$entries3$_i = _slicedToArray(_Object$entries3[_i3], 2),
                  k = _Object$entries3$_i[0],
                  v = _Object$entries3$_i[1];
                data_.push("data-".concat(k, "='").concat(_typeof(v) === 'object' ? JSON.stringify(v) : v, "'"));
              }
            }
            html.push("<th".concat(Utils.sprintf(' title="%s"', column.titleTooltip)), column.checkbox || column.radio ? Utils.sprintf(' class="bs-checkbox %s"', column['class'] || '') : classes || class_, Utils.sprintf(' style="%s"', halign + style + csses.join('; ') || undefined), Utils.sprintf(' rowspan="%s"', column.rowspan), Utils.sprintf(' colspan="%s"', column.colspan), Utils.sprintf(' data-field="%s"', column.field),
            // If `column` is not the first element of `this.options.columns[0]`, then className 'data-not-first-th' should be added.
            j === 0 && i > 0 ? ' data-not-first-th' : '', data_.length > 0 ? data_.join(' ') : '', '>');
            html.push(Utils.sprintf('<div class="th-inner %s">', _this2.options.sortable && column.sortable ? "sortable".concat(columnHalign === 'center' ? ' sortable-center' : '', " both") : ''));
            var text = _this2.options.escape && _this2.options.escapeTitle ? Utils.escapeHTML(column.title) : column.title;
            var title = text;
            if (column.checkbox) {
              text = '';
              if (!_this2.options.singleSelect && _this2.options.checkboxHeader) {
                text = '<label><input name="btSelectAll" type="checkbox" /><span></span></label>';
              }
              _this2.header.stateField = column.field;
            }
            if (column.radio) {
              text = '';
              _this2.header.stateField = column.field;
            }
            if (!text && column.showSelectTitle) {
              text += title;
            }
            html.push(text);
            html.push('</div>');
            html.push('<div class="fht-cell"></div>');
            html.push('</div>');
            html.push('</th>');
          });
          if (detailViewTemplate && _this2.options.detailViewAlign === 'right') {
            html.push(detailViewTemplate);
          }
          html.push('</tr>');
          if (html.length > 3) {
            headerHtml.push(html.join(''));
          }
        });
        this.$header.html(headerHtml.join(''));
        this.$header.find('th[data-field]').each(function (i, el) {
          $$q(el).data(visibleColumns[$$q(el).data('field')]);
        });
        this.$container.off('click', '.th-inner').on('click', '.th-inner', function (e) {
          var $this = $$q(e.currentTarget);
          if (_this2.options.detailView && !$this.parent().hasClass('bs-checkbox')) {
            if ($this.closest('.bootstrap-table')[0] !== _this2.$container[0]) {
              return false;
            }
          }
          if (_this2.options.sortable && $this.parent().data().sortable) {
            _this2.onSort(e);
          }
        });
        var resizeEvent = Utils.getEventName('resize.bootstrap-table', this.$el.attr('id'));
        $$q(window).off(resizeEvent);
        if (!this.options.showHeader || this.options.cardView) {
          this.$header.hide();
          this.$tableHeader.hide();
          this.$tableLoading.css('top', 0);
        } else {
          this.$header.show();
          this.$tableHeader.show();
          this.$tableLoading.css('top', this.$header.outerHeight() + 1);
          // Assign the correct sortable arrow
          this.getCaret();
          $$q(window).on(resizeEvent, function () {
            return _this2.resetView();
          });
        }
        this.$selectAll = this.$header.find('[name="btSelectAll"]');
        this.$selectAll.off('click').on('click', function (e) {
          e.stopPropagation();
          var checked = $$q(e.currentTarget).prop('checked');
          _this2[checked ? 'checkAll' : 'uncheckAll']();
          _this2.updateSelected();
        });
      }
    }, {
      key: "initData",
      value: function initData(data, type) {
        if (type === 'append') {
          this.options.data = this.options.data.concat(data);
        } else if (type === 'prepend') {
          this.options.data = [].concat(data).concat(this.options.data);
        } else {
          data = data || Utils.deepCopy(this.options.data);
          this.options.data = Array.isArray(data) ? data : data[this.options.dataField];
        }
        this.data = _toConsumableArray(this.options.data);
        if (this.options.sortReset) {
          this.unsortedData = _toConsumableArray(this.data);
        }
        if (this.options.sidePagination === 'server') {
          return;
        }
        this.initSort();
      }
    }, {
      key: "initSort",
      value: function initSort() {
        var _this3 = this;
        var name = this.options.sortName;
        var order = this.options.sortOrder === 'desc' ? -1 : 1;
        var index = this.header.fields.indexOf(this.options.sortName);
        var timeoutId = 0;
        if (index !== -1) {
          if (this.options.sortStable) {
            this.data.forEach(function (row, i) {
              if (!row.hasOwnProperty('_position')) {
                row._position = i;
              }
            });
          }
          if (this.options.customSort) {
            Utils.calculateObjectValue(this.options, this.options.customSort, [this.options.sortName, this.options.sortOrder, this.data]);
          } else {
            this.data.sort(function (a, b) {
              if (_this3.header.sortNames[index]) {
                name = _this3.header.sortNames[index];
              }
              var aa = Utils.getItemField(a, name, _this3.options.escape);
              var bb = Utils.getItemField(b, name, _this3.options.escape);
              var value = Utils.calculateObjectValue(_this3.header, _this3.header.sorters[index], [aa, bb, a, b]);
              if (value !== undefined) {
                if (_this3.options.sortStable && value === 0) {
                  return order * (a._position - b._position);
                }
                return order * value;
              }
              return Utils.sort(aa, bb, order, _this3.options, a._position, b._position);
            });
          }
          if (this.options.sortClass !== undefined) {
            clearTimeout(timeoutId);
            timeoutId = setTimeout(function () {
              _this3.$el.removeClass(_this3.options.sortClass);
              var index = _this3.$header.find("[data-field=\"".concat(_this3.options.sortName, "\"]")).index();
              _this3.$el.find("tr td:nth-child(".concat(index + 1, ")")).addClass(_this3.options.sortClass);
            }, 250);
          }
        } else if (this.options.sortReset) {
          this.data = _toConsumableArray(this.unsortedData);
        }
      }
    }, {
      key: "sortBy",
      value: function sortBy(params) {
        this.options.sortName = params.field;
        this.options.sortOrder = params.hasOwnProperty('sortOrder') ? params.sortOrder : 'asc';
        this._sort();
      }
    }, {
      key: "onSort",
      value: function onSort(_ref) {
        var type = _ref.type,
          currentTarget = _ref.currentTarget;
        var $this = type === 'keypress' ? $$q(currentTarget) : $$q(currentTarget).parent();
        var $this_ = this.$header.find('th').eq($this.index());
        this.$header.add(this.$header_).find('span.order').remove();
        if (this.options.sortName === $this.data('field')) {
          var currentSortOrder = this.options.sortOrder;
          var initialSortOrder = this.columns[this.fieldsColumnsIndex[$this.data('field')]].sortOrder || this.columns[this.fieldsColumnsIndex[$this.data('field')]].order;
          if (currentSortOrder === undefined) {
            this.options.sortOrder = 'asc';
          } else if (currentSortOrder === 'asc') {
            this.options.sortOrder = this.options.sortReset ? initialSortOrder === 'asc' ? 'desc' : undefined : 'desc';
          } else if (this.options.sortOrder === 'desc') {
            this.options.sortOrder = this.options.sortReset ? initialSortOrder === 'desc' ? 'asc' : undefined : 'asc';
          }
          if (this.options.sortOrder === undefined) {
            this.options.sortName = undefined;
          }
        } else {
          this.options.sortName = $this.data('field');
          if (this.options.rememberOrder) {
            this.options.sortOrder = $this.data('order') === 'asc' ? 'desc' : 'asc';
          } else {
            this.options.sortOrder = this.columns[this.fieldsColumnsIndex[$this.data('field')]].sortOrder || this.columns[this.fieldsColumnsIndex[$this.data('field')]].order;
          }
        }
        $this.add($this_).data('order', this.options.sortOrder);

        // Assign the correct sortable arrow
        this.getCaret();
        this._sort();
      }
    }, {
      key: "_sort",
      value: function _sort() {
        if (this.options.sidePagination === 'server' && this.options.serverSort) {
          this.options.pageNumber = 1;
          this.trigger('sort', this.options.sortName, this.options.sortOrder);
          this.initServer(this.options.silentSort);
          return;
        }
        if (this.options.pagination && this.options.sortResetPage) {
          this.options.pageNumber = 1;
          this.initPagination();
        }
        this.trigger('sort', this.options.sortName, this.options.sortOrder);
        this.initSort();
        this.initBody();
      }
    }, {
      key: "initToolbar",
      value: function initToolbar() {
        var _this4 = this;
        var opts = this.options;
        var html = [];
        var timeoutId = 0;
        var $keepOpen;
        var switchableCount = 0;
        if (this.$toolbar.find('.bs-bars').children().length) {
          $$q('body').append($$q(opts.toolbar));
        }
        this.$toolbar.html('');
        if (typeof opts.toolbar === 'string' || _typeof(opts.toolbar) === 'object') {
          $$q(Utils.sprintf('<div class="bs-bars %s-%s"></div>', this.constants.classes.pull, opts.toolbarAlign)).appendTo(this.$toolbar).append($$q(opts.toolbar));
        }

        // showColumns, showToggle, showRefresh
        html = ["<div class=\"".concat(['columns', "columns-".concat(opts.buttonsAlign), this.constants.classes.buttonsGroup, "".concat(this.constants.classes.pull, "-").concat(opts.buttonsAlign)].join(' '), "\">")];
        if (typeof opts.buttonsOrder === 'string') {
          opts.buttonsOrder = opts.buttonsOrder.replace(/\[|\]| |'/g, '').split(',');
        }
        this.buttons = Object.assign(this.buttons, {
          paginationSwitch: {
            text: opts.pagination ? opts.formatPaginationSwitchUp() : opts.formatPaginationSwitchDown(),
            icon: opts.pagination ? opts.icons.paginationSwitchDown : opts.icons.paginationSwitchUp,
            render: false,
            event: this.togglePagination,
            attributes: {
              'aria-label': opts.formatPaginationSwitch(),
              title: opts.formatPaginationSwitch()
            }
          },
          refresh: {
            text: opts.formatRefresh(),
            icon: opts.icons.refresh,
            render: false,
            event: this.refresh,
            attributes: {
              'aria-label': opts.formatRefresh(),
              title: opts.formatRefresh()
            }
          },
          toggle: {
            text: opts.formatToggleOn(),
            icon: opts.icons.toggleOff,
            render: false,
            event: this.toggleView,
            attributes: {
              'aria-label': opts.formatToggleOn(),
              title: opts.formatToggleOn()
            }
          },
          fullscreen: {
            text: opts.formatFullscreen(),
            icon: opts.icons.fullscreen,
            render: false,
            event: this.toggleFullscreen,
            attributes: {
              'aria-label': opts.formatFullscreen(),
              title: opts.formatFullscreen()
            }
          },
          columns: {
            render: false,
            html: function html() {
              var html = [];
              html.push("<div class=\"keep-open ".concat(_this4.constants.classes.buttonsDropdown, "\">\n            <button class=\"").concat(_this4.constants.buttonsClass, " dropdown-toggle\" type=\"button\" ").concat(_this4.constants.dataToggle, "=\"dropdown\"\n            aria-label=\"").concat(opts.formatColumns(), "\" title=\"").concat(opts.formatColumns(), "\">\n            ").concat(opts.showButtonIcons ? Utils.sprintf(_this4.constants.html.icon, opts.iconsPrefix, opts.icons.columns) : '', "\n            ").concat(opts.showButtonText ? opts.formatColumns() : '', "\n            ").concat(_this4.constants.html.dropdownCaret, "\n            </button>\n            ").concat(_this4.constants.html.toolbarDropdown[0]));
              if (opts.showColumnsSearch) {
                html.push(Utils.sprintf(_this4.constants.html.toolbarDropdownItem, Utils.sprintf('<input type="text" class="%s" name="columnsSearch" placeholder="%s" autocomplete="off">', _this4.constants.classes.input, opts.formatSearch())));
                html.push(_this4.constants.html.toolbarDropdownSeparator);
              }
              if (opts.showColumnsToggleAll) {
                var allFieldsVisible = _this4.getVisibleColumns().length === _this4.columns.filter(function (column) {
                  return !_this4.isSelectionColumn(column);
                }).length;
                html.push(Utils.sprintf(_this4.constants.html.toolbarDropdownItem, Utils.sprintf('<input type="checkbox" class="toggle-all" %s> <span>%s</span>', allFieldsVisible ? 'checked="checked"' : '', opts.formatColumnsToggleAll())));
                html.push(_this4.constants.html.toolbarDropdownSeparator);
              }
              var visibleColumns = 0;
              _this4.columns.forEach(function (column) {
                if (column.visible) {
                  visibleColumns++;
                }
              });
              _this4.columns.forEach(function (column, i) {
                if (_this4.isSelectionColumn(column)) {
                  return;
                }
                if (opts.cardView && !column.cardVisible) {
                  return;
                }
                var checked = column.visible ? ' checked="checked"' : '';
                var disabled = visibleColumns <= opts.minimumCountColumns && checked ? ' disabled="disabled"' : '';
                if (column.switchable) {
                  html.push(Utils.sprintf(_this4.constants.html.toolbarDropdownItem, Utils.sprintf('<input type="checkbox" data-field="%s" value="%s"%s%s> <span>%s</span>', column.field, i, checked, disabled, column.switchableLabel || column.title)));
                  switchableCount++;
                }
              });
              html.push(_this4.constants.html.toolbarDropdown[1], '</div>');
              return html.join('');
            }
          }
        });
        var buttonsHtml = {};
        for (var _i4 = 0, _Object$entries4 = Object.entries(this.buttons); _i4 < _Object$entries4.length; _i4++) {
          var _Object$entries4$_i = _slicedToArray(_Object$entries4[_i4], 2),
            buttonName = _Object$entries4$_i[0],
            buttonConfig = _Object$entries4$_i[1];
          var buttonHtml = void 0;
          if (buttonConfig.hasOwnProperty('html')) {
            if (typeof buttonConfig.html === 'function') {
              buttonHtml = buttonConfig.html();
            } else if (typeof buttonConfig.html === 'string') {
              buttonHtml = buttonConfig.html;
            }
          } else {
            var buttonClass = this.constants.buttonsClass;
            if (buttonConfig.hasOwnProperty('attributes') && buttonConfig.attributes.class) {
              buttonClass += " ".concat(buttonConfig.attributes.class);
            }
            buttonHtml = "<button class=\"".concat(buttonClass, "\" type=\"button\" name=\"").concat(buttonName, "\"");
            if (buttonConfig.hasOwnProperty('attributes')) {
              for (var _i5 = 0, _Object$entries5 = Object.entries(buttonConfig.attributes); _i5 < _Object$entries5.length; _i5++) {
                var _Object$entries5$_i = _slicedToArray(_Object$entries5[_i5], 2),
                  attributeName = _Object$entries5$_i[0],
                  value = _Object$entries5$_i[1];
                if (attributeName === 'class') {
                  continue;
                }
                buttonHtml += " ".concat(attributeName, "=\"").concat(value, "\"");
              }
            }
            buttonHtml += '>';
            if (opts.showButtonIcons && buttonConfig.hasOwnProperty('icon')) {
              buttonHtml += "".concat(Utils.sprintf(this.constants.html.icon, opts.iconsPrefix, buttonConfig.icon), " ");
            }
            if (opts.showButtonText && buttonConfig.hasOwnProperty('text')) {
              buttonHtml += buttonConfig.text;
            }
            buttonHtml += '</button>';
          }
          buttonsHtml[buttonName] = buttonHtml;
          var optionName = "show".concat(buttonName.charAt(0).toUpperCase()).concat(buttonName.substring(1));
          var showOption = opts[optionName];
          if ((!buttonConfig.hasOwnProperty('render') || buttonConfig.hasOwnProperty('render') && buttonConfig.render) && (showOption === undefined || showOption === true)) {
            opts[optionName] = true;
          }
          if (!opts.buttonsOrder.includes(buttonName)) {
            opts.buttonsOrder.push(buttonName);
          }
        }

        // Adding the button html to the final toolbar html when the showOption is true
        var _iterator = _createForOfIteratorHelper(opts.buttonsOrder),
          _step;
        try {
          for (_iterator.s(); !(_step = _iterator.n()).done;) {
            var button = _step.value;
            var _showOption = opts["show".concat(button.charAt(0).toUpperCase()).concat(button.substring(1))];
            if (_showOption) {
              html.push(buttonsHtml[button]);
            }
          }
        } catch (err) {
          _iterator.e(err);
        } finally {
          _iterator.f();
        }
        html.push('</div>');

        // Fix #188: this.showToolbar is for extensions
        if (this.showToolbar || html.length > 2) {
          this.$toolbar.append(html.join(''));
        }
        var _loop = function _loop() {
          var _Object$entries6$_i = _slicedToArray(_Object$entries6[_i6], 2),
            buttonName = _Object$entries6$_i[0],
            buttonConfig = _Object$entries6$_i[1];
          if (buttonConfig.hasOwnProperty('event')) {
            if (typeof buttonConfig.event === 'function' || typeof buttonConfig.event === 'string') {
              var event = typeof buttonConfig.event === 'string' ? window[buttonConfig.event] : buttonConfig.event;
              _this4.$toolbar.find("button[name=\"".concat(buttonName, "\"]")).off('click').on('click', function () {
                return event.call(_this4);
              });
              return 1; // continue
            }
            var _loop2 = function _loop2() {
              var _Object$entries7$_i = _slicedToArray(_Object$entries7[_i7], 2),
                eventType = _Object$entries7$_i[0],
                eventFunction = _Object$entries7$_i[1];
              var event = typeof eventFunction === 'string' ? window[eventFunction] : eventFunction;
              _this4.$toolbar.find("button[name=\"".concat(buttonName, "\"]")).off(eventType).on(eventType, function () {
                return event.call(_this4);
              });
            };
            for (var _i7 = 0, _Object$entries7 = Object.entries(buttonConfig.event); _i7 < _Object$entries7.length; _i7++) {
              _loop2();
            }
          }
        };
        for (var _i6 = 0, _Object$entries6 = Object.entries(this.buttons); _i6 < _Object$entries6.length; _i6++) {
          if (_loop()) continue;
        }
        if (opts.showColumns) {
          $keepOpen = this.$toolbar.find('.keep-open');
          var $checkboxes = $keepOpen.find('input[type="checkbox"]:not(".toggle-all")');
          var $toggleAll = $keepOpen.find('input[type="checkbox"].toggle-all');
          if (switchableCount <= opts.minimumCountColumns) {
            $keepOpen.find('input').prop('disabled', true);
          }
          $keepOpen.find('li, label').off('click').on('click', function (e) {
            e.stopImmediatePropagation();
          });
          $checkboxes.off('click').on('click', function (_ref2) {
            var currentTarget = _ref2.currentTarget;
            var $this = $$q(currentTarget);
            _this4._toggleColumn($this.val(), $this.prop('checked'), false);
            _this4.trigger('column-switch', $this.data('field'), $this.prop('checked'));
            $toggleAll.prop('checked', $checkboxes.filter(':checked').length === _this4.columns.filter(function (column) {
              return !_this4.isSelectionColumn(column);
            }).length);
          });
          $toggleAll.off('click').on('click', function (_ref3) {
            var currentTarget = _ref3.currentTarget;
            _this4._toggleAllColumns($$q(currentTarget).prop('checked'));
            _this4.trigger('column-switch-all', $$q(currentTarget).prop('checked'));
          });
          if (opts.showColumnsSearch) {
            var $columnsSearch = $keepOpen.find('[name="columnsSearch"]');
            var $listItems = $keepOpen.find('.dropdown-item-marker');
            $columnsSearch.on('keyup paste change', function (_ref4) {
              var currentTarget = _ref4.currentTarget;
              var $this = $$q(currentTarget);
              var searchValue = $this.val().toLowerCase();
              $listItems.show();
              $checkboxes.each(function (i, el) {
                var $checkbox = $$q(el);
                var $listItem = $checkbox.parents('.dropdown-item-marker');
                var text = $listItem.text().toLowerCase();
                if (!text.includes(searchValue)) {
                  $listItem.hide();
                }
              });
            });
          }
        }
        var handleInputEvent = function handleInputEvent($searchInput) {
          var eventTriggers = $searchInput.is('select') ? 'change' : 'keyup drop blur mouseup';
          $searchInput.off(eventTriggers).on(eventTriggers, function (event) {
            if (opts.searchOnEnterKey && event.keyCode !== 13) {
              return;
            }
            if ([37, 38, 39, 40].includes(event.keyCode)) {
              return;
            }
            clearTimeout(timeoutId); // doesn't matter if it's 0
            timeoutId = setTimeout(function () {
              _this4.onSearch({
                currentTarget: event.currentTarget
              });
            }, opts.searchTimeOut);
          });
        };

        // Fix #4516: this.showSearchClearButton is for extensions
        if ((opts.search || this.showSearchClearButton) && typeof opts.searchSelector !== 'string') {
          html = [];
          var showSearchButton = Utils.sprintf(this.constants.html.searchButton, this.constants.buttonsClass, opts.formatSearch(), opts.showButtonIcons ? Utils.sprintf(this.constants.html.icon, opts.iconsPrefix, opts.icons.search) : '', opts.showButtonText ? opts.formatSearch() : '');
          var showSearchClearButton = Utils.sprintf(this.constants.html.searchClearButton, this.constants.buttonsClass, opts.formatClearSearch(), opts.showButtonIcons ? Utils.sprintf(this.constants.html.icon, opts.iconsPrefix, opts.icons.clearSearch) : '', opts.showButtonText ? opts.formatClearSearch() : '');
          var searchInputHtml = "<input class=\"".concat(this.constants.classes.input, "\n        ").concat(Utils.sprintf(' %s%s', this.constants.classes.inputPrefix, opts.iconSize), "\n        search-input\" type=\"search\" aria-label=\"").concat(opts.formatSearch(), "\" placeholder=\"").concat(opts.formatSearch(), "\" autocomplete=\"off\">");
          var searchInputFinalHtml = searchInputHtml;
          if (opts.showSearchButton || opts.showSearchClearButton) {
            var _buttonsHtml = (opts.showSearchButton ? showSearchButton : '') + (opts.showSearchClearButton ? showSearchClearButton : '');
            searchInputFinalHtml = opts.search ? Utils.sprintf(this.constants.html.inputGroup, searchInputHtml, _buttonsHtml) : _buttonsHtml;
          }
          html.push(Utils.sprintf("\n        <div class=\"".concat(this.constants.classes.pull, "-").concat(opts.searchAlign, " search ").concat(this.constants.classes.inputGroup, "\">\n          %s\n        </div>\n      "), searchInputFinalHtml));
          this.$toolbar.append(html.join(''));
          var $searchInput = Utils.getSearchInput(this);
          if (opts.showSearchButton) {
            this.$toolbar.find('.search button[name=search]').off('click').on('click', function () {
              clearTimeout(timeoutId); // doesn't matter if it's 0
              timeoutId = setTimeout(function () {
                _this4.onSearch({
                  currentTarget: $searchInput
                });
              }, opts.searchTimeOut);
            });
            if (opts.searchOnEnterKey) {
              handleInputEvent($searchInput);
            }
          } else {
            handleInputEvent($searchInput);
          }
          if (opts.showSearchClearButton) {
            this.$toolbar.find('.search button[name=clearSearch]').click(function () {
              _this4.resetSearch();
            });
          }
        } else if (typeof opts.searchSelector === 'string') {
          handleInputEvent(Utils.getSearchInput(this));
        }
      }
    }, {
      key: "onSearch",
      value: function onSearch() {
        var _ref5 = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {},
          currentTarget = _ref5.currentTarget,
          firedByInitSearchText = _ref5.firedByInitSearchText;
        var overwriteSearchText = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : true;
        if (currentTarget !== undefined && $$q(currentTarget).length && overwriteSearchText) {
          var text = $$q(currentTarget).val().trim();
          if (this.options.trimOnSearch && $$q(currentTarget).val() !== text) {
            $$q(currentTarget).val(text);
          }
          if (this.searchText === text) {
            return;
          }
          var $searchInput = Utils.getSearchInput(this);
          var $currentTarget = currentTarget instanceof jQuery ? currentTarget : $$q(currentTarget);
          if ($currentTarget.is($searchInput) || $currentTarget.hasClass('search-input')) {
            this.searchText = text;
            this.options.searchText = text;
          }
        }
        if (!firedByInitSearchText) {
          this.options.pageNumber = 1;
        }
        this.initSearch();
        if (firedByInitSearchText) {
          if (this.options.sidePagination === 'client') {
            this.updatePagination();
          }
        } else {
          this.updatePagination();
        }
        this.trigger('search', this.searchText);
      }
    }, {
      key: "initSearch",
      value: function initSearch() {
        var _this5 = this;
        this.filterOptions = this.filterOptions || this.options.filterOptions;
        if (this.options.sidePagination !== 'server') {
          if (this.options.customSearch) {
            this.data = Utils.calculateObjectValue(this.options, this.options.customSearch, [this.options.data, this.searchText, this.filterColumns]);
            if (this.options.sortReset) {
              this.unsortedData = _toConsumableArray(this.data);
            }
            this.initSort();
            return;
          }
          var rawSearchText = this.searchText && (this.fromHtml ? Utils.escapeHTML(this.searchText) : this.searchText);
          var searchText = rawSearchText ? rawSearchText.toLowerCase() : '';
          var f = Utils.isEmptyObject(this.filterColumns) ? null : this.filterColumns;
          if (this.options.searchAccentNeutralise) {
            searchText = Utils.normalizeAccent(searchText);
          }

          // Check filter
          if (typeof this.filterOptions.filterAlgorithm === 'function') {
            this.data = this.options.data.filter(function (item) {
              return _this5.filterOptions.filterAlgorithm.apply(null, [item, f]);
            });
          } else if (typeof this.filterOptions.filterAlgorithm === 'string') {
            this.data = f ? this.options.data.filter(function (item) {
              var filterAlgorithm = _this5.filterOptions.filterAlgorithm;
              if (filterAlgorithm === 'and') {
                for (var key in f) {
                  if (Array.isArray(f[key]) && !f[key].includes(item[key]) || !Array.isArray(f[key]) && item[key] !== f[key]) {
                    return false;
                  }
                }
              } else if (filterAlgorithm === 'or') {
                var match = false;
                for (var _key in f) {
                  if (Array.isArray(f[_key]) && f[_key].includes(item[_key]) || !Array.isArray(f[_key]) && item[_key] === f[_key]) {
                    match = true;
                  }
                }
                return match;
              }
              return true;
            }) : _toConsumableArray(this.options.data);
          }
          var visibleFields = this.getVisibleFields();
          this.data = searchText ? this.data.filter(function (item, i) {
            for (var j = 0; j < _this5.header.fields.length; j++) {
              if (!_this5.header.searchables[j] || _this5.options.visibleSearch && visibleFields.indexOf(_this5.header.fields[j]) === -1) {
                continue;
              }
              var key = Utils.isNumeric(_this5.header.fields[j]) ? parseInt(_this5.header.fields[j], 10) : _this5.header.fields[j];
              var column = _this5.columns[_this5.fieldsColumnsIndex[key]];
              var value = void 0;
              if (typeof key === 'string' && !item.hasOwnProperty(key)) {
                value = item;
                var props = key.split('.');
                for (var _i8 = 0; _i8 < props.length; _i8++) {
                  if (value[props[_i8]] !== null) {
                    value = value[props[_i8]];
                  } else {
                    value = null;
                    break;
                  }
                }
              } else {
                value = item[key];
              }
              if (_this5.options.searchAccentNeutralise) {
                value = Utils.normalizeAccent(value);
              }

              // Fix #142: respect searchFormatter boolean
              if (column && column.searchFormatter) {
                value = Utils.calculateObjectValue(column, _this5.header.formatters[j], [value, item, i, column.field], value);
              }
              if (typeof value === 'string' || typeof value === 'number') {
                if (_this5.options.strictSearch && "".concat(value).toLowerCase() === searchText || _this5.options.regexSearch && Utils.regexCompare(value, rawSearchText)) {
                  return true;
                }
                var largerSmallerEqualsRegex = /(?:(<=|=>|=<|>=|>|<)(?:\s+)?(-?\d+)?|(-?\d+)?(\s+)?(<=|=>|=<|>=|>|<))/gm;
                var matches = largerSmallerEqualsRegex.exec(_this5.searchText);
                var comparisonCheck = false;
                if (matches) {
                  var operator = matches[1] || "".concat(matches[5], "l");
                  var comparisonValue = matches[2] || matches[3];
                  var int = parseInt(value, 10);
                  var comparisonInt = parseInt(comparisonValue, 10);
                  switch (operator) {
                    case '>':
                    case '<l':
                      comparisonCheck = int > comparisonInt;
                      break;
                    case '<':
                    case '>l':
                      comparisonCheck = int < comparisonInt;
                      break;
                    case '<=':
                    case '=<':
                    case '>=l':
                    case '=>l':
                      comparisonCheck = int <= comparisonInt;
                      break;
                    case '>=':
                    case '=>':
                    case '<=l':
                    case '=<l':
                      comparisonCheck = int >= comparisonInt;
                      break;
                  }
                }
                if (comparisonCheck || "".concat(value).toLowerCase().includes(searchText)) {
                  return true;
                }
              }
            }
            return false;
          }) : this.data;
          if (this.options.sortReset) {
            this.unsortedData = _toConsumableArray(this.data);
          }
          this.initSort();
        }
      }
    }, {
      key: "initPagination",
      value: function initPagination() {
        var _this6 = this;
        var opts = this.options;
        if (!opts.pagination) {
          this.$pagination.hide();
          return;
        }
        this.$pagination.show();
        var html = [];
        var allSelected = false;
        var i;
        var from;
        var to;
        var $pageList;
        var $pre;
        var $next;
        var $number;
        var data = this.getData({
          includeHiddenRows: false
        });
        var pageList = opts.pageList;
        if (typeof pageList === 'string') {
          pageList = pageList.replace(/\[|\]| /g, '').toLowerCase().split(',');
        }
        pageList = pageList.map(function (value) {
          if (typeof value === 'string') {
            return value.toLowerCase() === opts.formatAllRows().toLowerCase() || ['all', 'unlimited'].includes(value.toLowerCase()) ? opts.formatAllRows() : +value;
          }
          return value;
        });
        this.paginationParts = opts.paginationParts;
        if (typeof this.paginationParts === 'string') {
          this.paginationParts = this.paginationParts.replace(/\[|\]| |'/g, '').split(',');
        }
        if (opts.sidePagination !== 'server') {
          opts.totalRows = data.length;
        }
        this.totalPages = 0;
        if (opts.totalRows) {
          if (opts.pageSize === opts.formatAllRows()) {
            opts.pageSize = opts.totalRows;
            allSelected = true;
          }
          this.totalPages = ~~((opts.totalRows - 1) / opts.pageSize) + 1;
          opts.totalPages = this.totalPages;
        }
        if (this.totalPages > 0 && opts.pageNumber > this.totalPages) {
          opts.pageNumber = this.totalPages;
        }
        this.pageFrom = (opts.pageNumber - 1) * opts.pageSize + 1;
        this.pageTo = opts.pageNumber * opts.pageSize;
        if (this.pageTo > opts.totalRows) {
          this.pageTo = opts.totalRows;
        }
        if (this.options.pagination && this.options.sidePagination !== 'server') {
          this.options.totalNotFiltered = this.options.data.length;
        }
        if (!this.options.showExtendedPagination) {
          this.options.totalNotFiltered = undefined;
        }
        if (this.paginationParts.includes('pageInfo') || this.paginationParts.includes('pageInfoShort') || this.paginationParts.includes('pageSize')) {
          html.push("<div class=\"".concat(this.constants.classes.pull, "-").concat(opts.paginationDetailHAlign, " pagination-detail\">"));
        }
        if (this.paginationParts.includes('pageInfo') || this.paginationParts.includes('pageInfoShort')) {
          var totalRows = this.options.totalRows + (this.options.sidePagination === 'client' && this.options.paginationLoadMore && !this._paginationLoaded ? ' +' : '');
          var paginationInfo = this.paginationParts.includes('pageInfoShort') ? opts.formatDetailPagination(totalRows) : opts.formatShowingRows(this.pageFrom, this.pageTo, totalRows, opts.totalNotFiltered);
          html.push("<span class=\"pagination-info\">\n      ".concat(paginationInfo, "\n      </span>"));
        }
        if (this.paginationParts.includes('pageSize')) {
          html.push('<div class="page-list">');
          var pageNumber = ["<div class=\"".concat(this.constants.classes.paginationDropdown, "\">\n        <button class=\"").concat(this.constants.buttonsClass, " dropdown-toggle\" type=\"button\" ").concat(this.constants.dataToggle, "=\"dropdown\">\n        <span class=\"page-size\">\n        ").concat(allSelected ? opts.formatAllRows() : opts.pageSize, "\n        </span>\n        ").concat(this.constants.html.dropdownCaret, "\n        </button>\n        ").concat(this.constants.html.pageDropdown[0])];
          pageList.forEach(function (page, i) {
            if (!opts.smartDisplay || i === 0 || pageList[i - 1] < opts.totalRows || page === opts.formatAllRows()) {
              var active;
              if (allSelected) {
                active = page === opts.formatAllRows() ? _this6.constants.classes.dropdownActive : '';
              } else {
                active = page === opts.pageSize ? _this6.constants.classes.dropdownActive : '';
              }
              pageNumber.push(Utils.sprintf(_this6.constants.html.pageDropdownItem, active, page));
            }
          });
          pageNumber.push("".concat(this.constants.html.pageDropdown[1], "</div>"));
          html.push(opts.formatRecordsPerPage(pageNumber.join('')));
        }
        if (this.paginationParts.includes('pageInfo') || this.paginationParts.includes('pageInfoShort') || this.paginationParts.includes('pageSize')) {
          html.push('</div></div>');
        }
        if (this.paginationParts.includes('pageList')) {
          html.push("<div class=\"".concat(this.constants.classes.pull, "-").concat(opts.paginationHAlign, " pagination\">"), Utils.sprintf(this.constants.html.pagination[0], Utils.sprintf(' pagination-%s', opts.iconSize)), Utils.sprintf(this.constants.html.paginationItem, ' page-pre', opts.formatSRPaginationPreText(), opts.paginationPreText));
          if (this.totalPages < opts.paginationSuccessivelySize) {
            from = 1;
            to = this.totalPages;
          } else {
            from = opts.pageNumber - opts.paginationPagesBySide;
            to = from + opts.paginationPagesBySide * 2;
          }
          if (opts.pageNumber < opts.paginationSuccessivelySize - 1) {
            to = opts.paginationSuccessivelySize;
          }
          if (opts.paginationSuccessivelySize > this.totalPages - from) {
            from = from - (opts.paginationSuccessivelySize - (this.totalPages - from)) + 1;
          }
          if (from < 1) {
            from = 1;
          }
          if (to > this.totalPages) {
            to = this.totalPages;
          }
          var middleSize = Math.round(opts.paginationPagesBySide / 2);
          var pageItem = function pageItem(i) {
            var classes = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : '';
            return Utils.sprintf(_this6.constants.html.paginationItem, classes + (i === opts.pageNumber ? " ".concat(_this6.constants.classes.paginationActive) : ''), opts.formatSRPaginationPageText(i), i);
          };
          if (from > 1) {
            var max = opts.paginationPagesBySide;
            if (max >= from) max = from - 1;
            for (i = 1; i <= max; i++) {
              html.push(pageItem(i));
            }
            if (from - 1 === max + 1) {
              i = from - 1;
              html.push(pageItem(i));
            } else if (from - 1 > max) {
              if (from - opts.paginationPagesBySide * 2 > opts.paginationPagesBySide && opts.paginationUseIntermediate) {
                i = Math.round((from - middleSize) / 2 + middleSize);
                html.push(pageItem(i, ' page-intermediate'));
              } else {
                html.push(Utils.sprintf(this.constants.html.paginationItem, ' page-first-separator disabled', '', '...'));
              }
            }
          }
          for (i = from; i <= to; i++) {
            html.push(pageItem(i));
          }
          if (this.totalPages > to) {
            var min = this.totalPages - (opts.paginationPagesBySide - 1);
            if (to >= min) min = to + 1;
            if (to + 1 === min - 1) {
              i = to + 1;
              html.push(pageItem(i));
            } else if (min > to + 1) {
              if (this.totalPages - to > opts.paginationPagesBySide * 2 && opts.paginationUseIntermediate) {
                i = Math.round((this.totalPages - middleSize - to) / 2 + to);
                html.push(pageItem(i, ' page-intermediate'));
              } else {
                html.push(Utils.sprintf(this.constants.html.paginationItem, ' page-last-separator disabled', '', '...'));
              }
            }
            for (i = min; i <= this.totalPages; i++) {
              html.push(pageItem(i));
            }
          }
          html.push(Utils.sprintf(this.constants.html.paginationItem, ' page-next', opts.formatSRPaginationNextText(), opts.paginationNextText));
          html.push(this.constants.html.pagination[1], '</div>');
        }
        this.$pagination.html(html.join(''));
        var dropupClass = ['bottom', 'both'].includes(opts.paginationVAlign) ? " ".concat(this.constants.classes.dropup) : '';
        this.$pagination.last().find('.page-list > div').addClass(dropupClass);
        if (!opts.onlyInfoPagination) {
          $pageList = this.$pagination.find('.page-list a');
          $pre = this.$pagination.find('.page-pre');
          $next = this.$pagination.find('.page-next');
          $number = this.$pagination.find('.page-item').not('.page-next, .page-pre, .page-last-separator, .page-first-separator');
          if (this.totalPages <= 1) {
            this.$pagination.find('div.pagination').hide();
          }
          if (opts.smartDisplay) {
            if (pageList.length < 2 || opts.totalRows <= pageList[0]) {
              this.$pagination.find('div.page-list').hide();
            }
          }

          // when data is empty, hide the pagination
          this.$pagination[this.getData().length ? 'show' : 'hide']();
          if (!opts.paginationLoop) {
            if (opts.pageNumber === 1) {
              $pre.addClass('disabled');
            }
            if (opts.pageNumber === this.totalPages) {
              $next.addClass('disabled');
            }
          }
          if (allSelected) {
            opts.pageSize = opts.formatAllRows();
          }
          $pageList.off('click').on('click', function (e) {
            return _this6.onPageListChange(e);
          });
          $pre.off('click').on('click', function (e) {
            return _this6.onPagePre(e);
          });
          $next.off('click').on('click', function (e) {
            return _this6.onPageNext(e);
          });
          $number.off('click').on('click', function (e) {
            return _this6.onPageNumber(e);
          });
        }
      }
    }, {
      key: "updatePagination",
      value: function updatePagination(event) {
        // Fix #171: IE disabled button can be clicked bug.
        if (event && $$q(event.currentTarget).hasClass('disabled')) {
          return;
        }
        if (!this.options.maintainMetaData) {
          this.resetRows();
        }
        this.initPagination();
        this.trigger('page-change', this.options.pageNumber, this.options.pageSize);
        if (this.options.sidePagination === 'server' || this.options.sidePagination === 'client' && this.options.paginationLoadMore && !this._paginationLoaded && this.options.pageNumber === this.totalPages) {
          this.initServer();
        } else {
          this.initBody();
        }
      }
    }, {
      key: "onPageListChange",
      value: function onPageListChange(event) {
        event.preventDefault();
        var $this = $$q(event.currentTarget);
        $this.parent().addClass(this.constants.classes.dropdownActive).siblings().removeClass(this.constants.classes.dropdownActive);
        this.options.pageSize = $this.text().toUpperCase() === this.options.formatAllRows().toUpperCase() ? this.options.formatAllRows() : +$this.text();
        this.$toolbar.find('.page-size').text(this.options.pageSize);
        this.updatePagination(event);
        return false;
      }
    }, {
      key: "onPagePre",
      value: function onPagePre(event) {
        if ($$q(event.target).hasClass('disabled')) {
          return;
        }
        event.preventDefault();
        if (this.options.pageNumber - 1 === 0) {
          this.options.pageNumber = this.options.totalPages;
        } else {
          this.options.pageNumber--;
        }
        this.updatePagination(event);
        return false;
      }
    }, {
      key: "onPageNext",
      value: function onPageNext(event) {
        if ($$q(event.target).hasClass('disabled')) {
          return;
        }
        event.preventDefault();
        if (this.options.pageNumber + 1 > this.options.totalPages) {
          this.options.pageNumber = 1;
        } else {
          this.options.pageNumber++;
        }
        this.updatePagination(event);
        return false;
      }
    }, {
      key: "onPageNumber",
      value: function onPageNumber(event) {
        event.preventDefault();
        if (this.options.pageNumber === +$$q(event.currentTarget).text()) {
          return;
        }
        this.options.pageNumber = +$$q(event.currentTarget).text();
        this.updatePagination(event);
        return false;
      }

      // eslint-disable-next-line no-unused-vars
    }, {
      key: "initRow",
      value: function initRow(item, i, data, trFragments) {
        var _this7 = this;
        var html = [];
        var style = {};
        var csses = [];
        var data_ = '';
        var attributes = {};
        var htmlAttributes = [];
        if (Utils.findIndex(this.hiddenRows, item) > -1) {
          return;
        }
        style = Utils.calculateObjectValue(this.options, this.options.rowStyle, [item, i], style);
        if (style && style.css) {
          for (var _i9 = 0, _Object$entries8 = Object.entries(style.css); _i9 < _Object$entries8.length; _i9++) {
            var _Object$entries8$_i = _slicedToArray(_Object$entries8[_i9], 2),
              key = _Object$entries8$_i[0],
              value = _Object$entries8$_i[1];
            csses.push("".concat(key, ": ").concat(value));
          }
        }
        attributes = Utils.calculateObjectValue(this.options, this.options.rowAttributes, [item, i], attributes);
        if (attributes) {
          for (var _i10 = 0, _Object$entries9 = Object.entries(attributes); _i10 < _Object$entries9.length; _i10++) {
            var _Object$entries9$_i = _slicedToArray(_Object$entries9[_i10], 2),
              _key2 = _Object$entries9$_i[0],
              _value = _Object$entries9$_i[1];
            htmlAttributes.push("".concat(_key2, "=\"").concat(Utils.escapeHTML(_value), "\""));
          }
        }
        if (item._data && !Utils.isEmptyObject(item._data)) {
          for (var _i11 = 0, _Object$entries10 = Object.entries(item._data); _i11 < _Object$entries10.length; _i11++) {
            var _Object$entries10$_i = _slicedToArray(_Object$entries10[_i11], 2),
              k = _Object$entries10$_i[0],
              v = _Object$entries10$_i[1];
            // ignore data-index
            if (k === 'index') {
              return;
            }
            data_ += " data-".concat(k, "='").concat(_typeof(v) === 'object' ? JSON.stringify(v) : v, "'");
          }
        }
        html.push('<tr', Utils.sprintf(' %s', htmlAttributes.length ? htmlAttributes.join(' ') : undefined), Utils.sprintf(' id="%s"', Array.isArray(item) ? undefined : item._id), Utils.sprintf(' class="%s"', style.classes || (Array.isArray(item) ? undefined : item._class)), Utils.sprintf(' style="%s"', Array.isArray(item) ? undefined : item._style), " data-index=\"".concat(i, "\""), Utils.sprintf(' data-uniqueid="%s"', Utils.getItemField(item, this.options.uniqueId, false)), Utils.sprintf(' data-has-detail-view="%s"', this.options.detailView && Utils.calculateObjectValue(null, this.options.detailFilter, [i, item]) ? 'true' : undefined), Utils.sprintf('%s', data_), '>');
        if (this.options.cardView) {
          html.push("<td colspan=\"".concat(this.header.fields.length, "\"><div class=\"card-views\">"));
        }
        var detailViewTemplate = '';
        if (Utils.hasDetailViewIcon(this.options)) {
          detailViewTemplate = '<td>';
          if (Utils.calculateObjectValue(null, this.options.detailFilter, [i, item])) {
            detailViewTemplate += "\n          <a class=\"detail-icon\" href=\"#\">\n          ".concat(Utils.sprintf(this.constants.html.icon, this.options.iconsPrefix, this.options.icons.detailOpen), "\n          </a>\n        ");
          }
          detailViewTemplate += '</td>';
        }
        if (detailViewTemplate && this.options.detailViewAlign !== 'right') {
          html.push(detailViewTemplate);
        }
        this.header.fields.forEach(function (field, j) {
          var column = _this7.columns[j];
          var text = '';
          var value_ = Utils.getItemField(item, field, _this7.options.escape, column.escape);
          var value = '';
          var type = '';
          var cellStyle = {};
          var id_ = '';
          var class_ = _this7.header.classes[j];
          var style_ = '';
          var styleToAdd_ = '';
          var data_ = '';
          var rowspan_ = '';
          var colspan_ = '';
          var title_ = '';
          if ((_this7.fromHtml || _this7.autoMergeCells) && typeof value_ === 'undefined') {
            if (!column.checkbox && !column.radio) {
              return;
            }
          }
          if (!column.visible) {
            return;
          }
          if (_this7.options.cardView && !column.cardVisible) {
            return;
          }

          // Style concat
          if (csses.concat([_this7.header.styles[j]]).length) {
            styleToAdd_ += "".concat(csses.concat([_this7.header.styles[j]]).join('; '));
          }
          if (item["_".concat(field, "_style")]) {
            styleToAdd_ += "".concat(item["_".concat(field, "_style")]);
          }
          if (styleToAdd_) {
            style_ = " style=\"".concat(styleToAdd_, "\"");
          }
          // Style concat

          // handle id and class of td
          if (item["_".concat(field, "_id")]) {
            id_ = Utils.sprintf(' id="%s"', item["_".concat(field, "_id")]);
          }
          if (item["_".concat(field, "_class")]) {
            class_ = Utils.sprintf(' class="%s"', item["_".concat(field, "_class")]);
          }
          if (item["_".concat(field, "_rowspan")]) {
            rowspan_ = Utils.sprintf(' rowspan="%s"', item["_".concat(field, "_rowspan")]);
          }
          if (item["_".concat(field, "_colspan")]) {
            colspan_ = Utils.sprintf(' colspan="%s"', item["_".concat(field, "_colspan")]);
          }
          if (item["_".concat(field, "_title")]) {
            title_ = Utils.sprintf(' title="%s"', item["_".concat(field, "_title")]);
          }
          cellStyle = Utils.calculateObjectValue(_this7.header, _this7.header.cellStyles[j], [value_, item, i, field], cellStyle);
          if (cellStyle.classes) {
            class_ = " class=\"".concat(cellStyle.classes, "\"");
          }
          if (cellStyle.css) {
            var csses_ = [];
            for (var _i12 = 0, _Object$entries11 = Object.entries(cellStyle.css); _i12 < _Object$entries11.length; _i12++) {
              var _Object$entries11$_i = _slicedToArray(_Object$entries11[_i12], 2),
                _key3 = _Object$entries11$_i[0],
                _value2 = _Object$entries11$_i[1];
              csses_.push("".concat(_key3, ": ").concat(_value2));
            }
            style_ = " style=\"".concat(csses_.concat(_this7.header.styles[j]).join('; '), "\"");
          }
          value = Utils.calculateObjectValue(column, _this7.header.formatters[j], [value_, item, i, field], value_);
          if (!(column.checkbox || column.radio)) {
            value = typeof value === 'undefined' || value === null ? _this7.options.undefinedText : value;
          }
          if (column.searchable && _this7.searchText && _this7.options.searchHighlight && !(column.checkbox || column.radio)) {
            var defValue = '';
            var searchText = _this7.searchText.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
            if (_this7.options.searchAccentNeutralise) {
              var indexRegex = new RegExp("".concat(Utils.normalizeAccent(searchText)), 'gmi');
              var match = indexRegex.exec(Utils.normalizeAccent(value));
              if (match) {
                searchText = value.substring(match.index, match.index + searchText.length);
              }
            }
            var regExp = new RegExp("(".concat(searchText, ")"), 'gim');
            var marker = '<mark>$1</mark>';
            var isHTML = value && /<(?=.*? .*?\/ ?>|br|hr|input|!--|wbr)[a-z]+.*?>|<([a-z]+).*?<\/\1>/i.test(value);
            if (isHTML) {
              // value can contains a HTML tags
              var textContent = new DOMParser().parseFromString(value.toString(), 'text/html').documentElement.textContent;
              var textReplaced = textContent.replace(regExp, marker);
              textContent = textContent.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
              defValue = value.replace(new RegExp("(>\\s*)(".concat(textContent, ")(\\s*)"), 'gm'), "$1".concat(textReplaced, "$3"));
            } else {
              // but usually not
              defValue = value.toString().replace(regExp, marker);
            }
            value = Utils.calculateObjectValue(column, column.searchHighlightFormatter, [value, _this7.searchText], defValue);
          }
          if (item["_".concat(field, "_data")] && !Utils.isEmptyObject(item["_".concat(field, "_data")])) {
            for (var _i13 = 0, _Object$entries12 = Object.entries(item["_".concat(field, "_data")]); _i13 < _Object$entries12.length; _i13++) {
              var _Object$entries12$_i = _slicedToArray(_Object$entries12[_i13], 2),
                _k = _Object$entries12$_i[0],
                _v = _Object$entries12$_i[1];
              // ignore data-index
              if (_k === 'index') {
                return;
              }
              data_ += " data-".concat(_k, "=\"").concat(_v, "\"");
            }
          }
          if (column.checkbox || column.radio) {
            type = column.checkbox ? 'checkbox' : type;
            type = column.radio ? 'radio' : type;
            var c = column['class'] || '';
            var isChecked = Utils.isObject(value) && value.hasOwnProperty('checked') ? value.checked : (value === true || value_) && value !== false;
            var isDisabled = !column.checkboxEnabled || value && value.disabled;
            text = [_this7.options.cardView ? "<div class=\"card-view ".concat(c, "\">") : "<td class=\"bs-checkbox ".concat(c, "\"").concat(class_).concat(style_, ">"), "<label>\n            <input\n            data-index=\"".concat(i, "\"\n            name=\"").concat(_this7.options.selectItemName, "\"\n            type=\"").concat(type, "\"\n            ").concat(Utils.sprintf('value="%s"', item[_this7.options.idField]), "\n            ").concat(Utils.sprintf('checked="%s"', isChecked ? 'checked' : undefined), "\n            ").concat(Utils.sprintf('disabled="%s"', isDisabled ? 'disabled' : undefined), " />\n            <span></span>\n            </label>"), _this7.header.formatters[j] && typeof value === 'string' ? value : '', _this7.options.cardView ? '</div>' : '</td>'].join('');
            item[_this7.header.stateField] = value === true || !!value_ || value && value.checked;
          } else if (_this7.options.cardView) {
            var cardTitle = _this7.options.showHeader ? "<span class=\"card-view-title ".concat(cellStyle.classes || '', "\"").concat(style_, ">").concat(Utils.getFieldTitle(_this7.columns, field), "</span>") : '';
            text = "<div class=\"card-view\">".concat(cardTitle, "<span class=\"card-view-value ").concat(cellStyle.classes || '', "\"").concat(style_, ">").concat(value, "</span></div>");
            if (_this7.options.smartDisplay && value === '') {
              text = '<div class="card-view"></div>';
            }
          } else {
            text = "<td".concat(id_).concat(class_).concat(style_).concat(data_).concat(rowspan_).concat(colspan_).concat(title_, ">").concat(value, "</td>");
          }
          html.push(text);
        });
        if (detailViewTemplate && this.options.detailViewAlign === 'right') {
          html.push(detailViewTemplate);
        }
        if (this.options.cardView) {
          html.push('</div></td>');
        }
        html.push('</tr>');
        return html.join('');
      }
    }, {
      key: "initBody",
      value: function initBody(fixedScroll, updatedUid) {
        var _this8 = this;
        var data = this.getData();
        this.trigger('pre-body', data);
        this.$body = this.$el.find('>tbody');
        if (!this.$body.length) {
          this.$body = $$q('<tbody></tbody>').appendTo(this.$el);
        }

        // Fix #389 Bootstrap-table-flatJSON is not working
        if (!this.options.pagination || this.options.sidePagination === 'server') {
          this.pageFrom = 1;
          this.pageTo = data.length;
        }
        var rows = [];
        var trFragments = $$q(document.createDocumentFragment());
        var hasTr = false;
        var toExpand = [];
        this.autoMergeCells = Utils.checkAutoMergeCells(data.slice(this.pageFrom - 1, this.pageTo));
        for (var i = this.pageFrom - 1; i < this.pageTo; i++) {
          var item = data[i];
          var tr = this.initRow(item, i, data, trFragments);
          hasTr = hasTr || !!tr;
          if (tr && typeof tr === 'string') {
            var uniqueId = this.options.uniqueId;
            if (uniqueId && item.hasOwnProperty(uniqueId)) {
              var itemUniqueId = item[uniqueId];
              var oldTr = this.$body.find(Utils.sprintf('> tr[data-uniqueid="%s"][data-has-detail-view]', itemUniqueId));
              var oldTrNext = oldTr.next();
              if (oldTrNext.is('tr.detail-view')) {
                toExpand.push(i);
                if (!updatedUid || itemUniqueId !== updatedUid) {
                  tr += oldTrNext[0].outerHTML;
                }
              }
            }
            if (!this.options.virtualScroll) {
              trFragments.append(tr);
            } else {
              rows.push(tr);
            }
          }
        }

        // show no records
        if (!hasTr) {
          this.$body.html("<tr class=\"no-records-found\">".concat(Utils.sprintf('<td colspan="%s">%s</td>', this.getVisibleFields().length + Utils.getDetailViewIndexOffset(this.options), this.options.formatNoMatches()), "</tr>"));
        } else if (!this.options.virtualScroll) {
          this.$body.html(trFragments);
        } else {
          if (this.virtualScroll) {
            this.virtualScroll.destroy();
          }
          this.virtualScroll = new VirtualScroll({
            rows: rows,
            fixedScroll: fixedScroll,
            scrollEl: this.$tableBody[0],
            contentEl: this.$body[0],
            itemHeight: this.options.virtualScrollItemHeight,
            callback: function callback(startIndex, endIndex) {
              _this8.fitHeader();
              _this8.initBodyEvent();
              _this8.trigger('virtual-scroll', startIndex, endIndex);
            }
          });
        }
        toExpand.forEach(function (index) {
          _this8.expandRow(index);
        });
        if (!fixedScroll) {
          this.scrollTo(0);
        }
        this.initBodyEvent();
        this.initFooter();
        this.resetView();
        this.updateSelected();
        if (this.options.sidePagination !== 'server') {
          this.options.totalRows = data.length;
        }
        this.trigger('post-body', data);
      }
    }, {
      key: "initBodyEvent",
      value: function initBodyEvent() {
        var _this9 = this;
        // click to select by column
        this.$body.find('> tr[data-index] > td').off('click dblclick').on('click dblclick', function (e) {
          var $td = $$q(e.currentTarget);
          if ($td.find('.detail-icon').length || $td.index() - Utils.getDetailViewIndexOffset(_this9.options) < 0) {
            return;
          }
          var $tr = $td.parent();
          var $cardViewArr = $$q(e.target).parents('.card-views').children();
          var $cardViewTarget = $$q(e.target).parents('.card-view');
          var rowIndex = $tr.data('index');
          var item = _this9.data[rowIndex];
          var index = _this9.options.cardView ? $cardViewArr.index($cardViewTarget) : $td[0].cellIndex;
          var fields = _this9.getVisibleFields();
          var field = fields[index - Utils.getDetailViewIndexOffset(_this9.options)];
          var column = _this9.columns[_this9.fieldsColumnsIndex[field]];
          var value = Utils.getItemField(item, field, _this9.options.escape, column.escape);
          _this9.trigger(e.type === 'click' ? 'click-cell' : 'dbl-click-cell', field, value, item, $td);
          _this9.trigger(e.type === 'click' ? 'click-row' : 'dbl-click-row', item, $tr, field);

          // if click to select - then trigger the checkbox/radio click
          if (e.type === 'click' && _this9.options.clickToSelect && column.clickToSelect && !Utils.calculateObjectValue(_this9.options, _this9.options.ignoreClickToSelectOn, [e.target])) {
            var $selectItem = $tr.find(Utils.sprintf('[name="%s"]', _this9.options.selectItemName));
            if ($selectItem.length) {
              $selectItem[0].click();
            }
          }
          if (e.type === 'click' && _this9.options.detailViewByClick) {
            _this9.toggleDetailView(rowIndex, _this9.header.detailFormatters[_this9.fieldsColumnsIndex[field]]);
          }
        }).off('mousedown').on('mousedown', function (e) {
          // https://github.com/jquery/jquery/issues/1741
          _this9.multipleSelectRowCtrlKey = e.ctrlKey || e.metaKey;
          _this9.multipleSelectRowShiftKey = e.shiftKey;
        });
        this.$body.find('> tr[data-index] > td > .detail-icon').off('click').on('click', function (e) {
          e.preventDefault();
          _this9.toggleDetailView($$q(e.currentTarget).parent().parent().data('index'));
          return false;
        });
        this.$selectItem = this.$body.find(Utils.sprintf('[name="%s"]', this.options.selectItemName));
        this.$selectItem.off('click').on('click', function (e) {
          e.stopImmediatePropagation();
          var $this = $$q(e.currentTarget);
          _this9._toggleCheck($this.prop('checked'), $this.data('index'));
        });
        this.header.events.forEach(function (_events, i) {
          var events = _events;
          if (!events) {
            return;
          }
          // fix bug, if events is defined with namespace
          if (typeof events === 'string') {
            events = Utils.calculateObjectValue(null, events);
          }
          if (!events) {
            throw new Error("Unknown event in the scope: ".concat(_events));
          }
          var field = _this9.header.fields[i];
          var fieldIndex = _this9.getVisibleFields().indexOf(field);
          if (fieldIndex === -1) {
            return;
          }
          fieldIndex += Utils.getDetailViewIndexOffset(_this9.options);
          var _loop3 = function _loop3(key) {
            if (!events.hasOwnProperty(key)) {
              return 1; // continue
            }
            var event = events[key];
            _this9.$body.find('>tr:not(.no-records-found)').each(function (i, tr) {
              var $tr = $$q(tr);
              var $td = $tr.find(_this9.options.cardView ? '.card-views>.card-view' : '>td').eq(fieldIndex);
              var index = key.indexOf(' ');
              var name = key.substring(0, index);
              var el = key.substring(index + 1);
              $td.find(el).off(name).on(name, function (e) {
                var index = $tr.data('index');
                var row = _this9.data[index];
                var value = row[field];
                event.apply(_this9, [e, value, row, index]);
              });
            });
          };
          for (var key in events) {
            if (_loop3(key)) continue;
          }
        });
      }
    }, {
      key: "initServer",
      value: function initServer(silent, query, url) {
        var _this10 = this;
        var data = {};
        var index = this.header.fields.indexOf(this.options.sortName);
        var params = {
          searchText: this.searchText,
          sortName: this.options.sortName,
          sortOrder: this.options.sortOrder
        };
        if (this.header.sortNames[index]) {
          params.sortName = this.header.sortNames[index];
        }
        if (this.options.pagination && this.options.sidePagination === 'server') {
          params.pageSize = this.options.pageSize === this.options.formatAllRows() ? this.options.totalRows : this.options.pageSize;
          params.pageNumber = this.options.pageNumber;
        }
        if (!(url || this.options.url) && !this.options.ajax) {
          return;
        }
        if (this.options.queryParamsType === 'limit') {
          params = {
            search: params.searchText,
            sort: params.sortName,
            order: params.sortOrder
          };
          if (this.options.pagination && this.options.sidePagination === 'server') {
            params.offset = this.options.pageSize === this.options.formatAllRows() ? 0 : this.options.pageSize * (this.options.pageNumber - 1);
            params.limit = this.options.pageSize;
            if (params.limit === 0 || this.options.pageSize === this.options.formatAllRows()) {
              delete params.limit;
            }
          }
        }
        if (this.options.search && this.options.sidePagination === 'server' && this.options.searchable && this.columns.filter(function (column) {
          return column.searchable;
        }).length) {
          params.searchable = [];
          var _iterator2 = _createForOfIteratorHelper(this.columns),
            _step2;
          try {
            for (_iterator2.s(); !(_step2 = _iterator2.n()).done;) {
              var column = _step2.value;
              if (!column.checkbox && column.searchable && (this.options.visibleSearch && column.visible || !this.options.visibleSearch)) {
                params.searchable.push(column.field);
              }
            }
          } catch (err) {
            _iterator2.e(err);
          } finally {
            _iterator2.f();
          }
        }
        if (!Utils.isEmptyObject(this.filterColumnsPartial)) {
          params.filter = JSON.stringify(this.filterColumnsPartial, null);
        }
        Utils.extend(params, query || {});
        data = Utils.calculateObjectValue(this.options, this.options.queryParams, [params], data);

        // false to stop request
        if (data === false) {
          return;
        }
        if (!silent) {
          this.showLoading();
        }
        var request = Utils.extend({}, Utils.calculateObjectValue(null, this.options.ajaxOptions), {
          type: this.options.method,
          url: url || this.options.url,
          data: this.options.contentType === 'application/json' && this.options.method === 'post' ? JSON.stringify(data) : data,
          cache: this.options.cache,
          contentType: this.options.contentType,
          dataType: this.options.dataType,
          success: function success(_res, textStatus, jqXHR) {
            var res = Utils.calculateObjectValue(_this10.options, _this10.options.responseHandler, [_res, jqXHR], _res);
            if (_this10.options.sidePagination === 'client' && _this10.options.paginationLoadMore) {
              _this10._paginationLoaded = _this10.data.length === res.length;
            }
            _this10.load(res);
            _this10.trigger('load-success', res, jqXHR && jqXHR.status, jqXHR);
            if (!silent) {
              _this10.hideLoading();
            }
            if (_this10.options.sidePagination === 'server' && _this10.options.pageNumber > 1 && res[_this10.options.totalField] > 0 && !res[_this10.options.dataField].length) {
              _this10.updatePagination();
            }
          },
          error: function error(jqXHR) {
            // abort ajax by multiple request
            if (jqXHR && jqXHR.status === 0 && _this10._xhrAbort) {
              _this10._xhrAbort = false;
              return;
            }
            var data = [];
            if (_this10.options.sidePagination === 'server') {
              data = {};
              data[_this10.options.totalField] = 0;
              data[_this10.options.dataField] = [];
            }
            _this10.load(data);
            _this10.trigger('load-error', jqXHR && jqXHR.status, jqXHR);
            if (!silent) {
              _this10.hideLoading();
            }
          }
        });
        if (this.options.ajax) {
          Utils.calculateObjectValue(this, this.options.ajax, [request], null);
        } else {
          if (this._xhr && this._xhr.readyState !== 4) {
            this._xhrAbort = true;
            this._xhr.abort();
          }
          this._xhr = $$q.ajax(request);
        }
        return data;
      }
    }, {
      key: "initSearchText",
      value: function initSearchText() {
        if (this.options.search) {
          this.searchText = '';
          if (this.options.searchText !== '') {
            var $search = Utils.getSearchInput(this);
            $search.val(this.options.searchText);
            this.onSearch({
              currentTarget: $search,
              firedByInitSearchText: true
            });
          }
        }
      }
    }, {
      key: "getCaret",
      value: function getCaret() {
        var _this11 = this;
        this.$header.find('th').each(function (i, th) {
          $$q(th).find('.sortable').removeClass('desc asc').addClass($$q(th).data('field') === _this11.options.sortName ? _this11.options.sortOrder : 'both');
        });
      }
    }, {
      key: "updateSelected",
      value: function updateSelected() {
        var checkAll = this.$selectItem.filter(':enabled').length && this.$selectItem.filter(':enabled').length === this.$selectItem.filter(':enabled').filter(':checked').length;
        this.$selectAll.add(this.$selectAll_).prop('checked', checkAll);
        this.$selectItem.each(function (i, el) {
          $$q(el).closest('tr')[$$q(el).prop('checked') ? 'addClass' : 'removeClass']('selected');
        });
      }
    }, {
      key: "updateRows",
      value: function updateRows() {
        var _this12 = this;
        this.$selectItem.each(function (i, el) {
          _this12.data[$$q(el).data('index')][_this12.header.stateField] = $$q(el).prop('checked');
        });
      }
    }, {
      key: "resetRows",
      value: function resetRows() {
        var _iterator3 = _createForOfIteratorHelper(this.data),
          _step3;
        try {
          for (_iterator3.s(); !(_step3 = _iterator3.n()).done;) {
            var row = _step3.value;
            this.$selectAll.prop('checked', false);
            this.$selectItem.prop('checked', false);
            if (this.header.stateField) {
              row[this.header.stateField] = false;
            }
          }
        } catch (err) {
          _iterator3.e(err);
        } finally {
          _iterator3.f();
        }
        this.initHiddenRows();
      }
    }, {
      key: "trigger",
      value: function trigger(_name) {
        var _this$options, _this$options2;
        var name = "".concat(_name, ".bs.table");
        for (var _len = arguments.length, args = new Array(_len > 1 ? _len - 1 : 0), _key4 = 1; _key4 < _len; _key4++) {
          args[_key4 - 1] = arguments[_key4];
        }
        (_this$options = this.options)[BootstrapTable.EVENTS[name]].apply(_this$options, [].concat(args, [this]));
        this.$el.trigger($$q.Event(name, {
          sender: this
        }), args);
        (_this$options2 = this.options).onAll.apply(_this$options2, [name].concat([].concat(args, [this])));
        this.$el.trigger($$q.Event('all.bs.table', {
          sender: this
        }), [name, args]);
      }
    }, {
      key: "resetHeader",
      value: function resetHeader() {
        var _this13 = this;
        // fix #61: the hidden table reset header bug.
        // fix bug: get $el.css('width') error sometime (height = 500)
        clearTimeout(this.timeoutId_);
        this.timeoutId_ = setTimeout(function () {
          return _this13.fitHeader();
        }, this.$el.is(':hidden') ? 100 : 0);
      }
    }, {
      key: "fitHeader",
      value: function fitHeader() {
        var _this14 = this;
        if (this.$el.is(':hidden')) {
          this.timeoutId_ = setTimeout(function () {
            return _this14.fitHeader();
          }, 100);
          return;
        }
        var fixedBody = this.$tableBody.get(0);
        var scrollWidth = this.hasScrollBar && fixedBody.scrollHeight > fixedBody.clientHeight + this.$header.outerHeight() ? Utils.getScrollBarWidth() : 0;
        this.$el.css('margin-top', -this.$header.outerHeight());
        var focused = this.$tableHeader.find(':focus');
        if (focused.length > 0) {
          var $th = focused.parents('th');
          if ($th.length > 0) {
            var dataField = $th.attr('data-field');
            if (dataField !== undefined) {
              var $headerTh = this.$header.find("[data-field='".concat(dataField, "']"));
              if ($headerTh.length > 0) {
                $headerTh.find(':input').addClass('focus-temp');
              }
            }
          }
        }
        this.$header_ = this.$header.clone(true, true);
        this.$selectAll_ = this.$header_.find('[name="btSelectAll"]');
        this.$tableHeader.css('margin-right', scrollWidth).find('table').css('width', this.$el.outerWidth()).html('').attr('class', this.$el.attr('class')).append(this.$header_);
        this.$tableLoading.css('width', this.$el.outerWidth());
        var focusedTemp = $$q('.focus-temp:visible:eq(0)');
        if (focusedTemp.length > 0) {
          focusedTemp.focus();
          this.$header.find('.focus-temp').removeClass('focus-temp');
        }

        // fix bug: $.data() is not working as expected after $.append()
        this.$header.find('th[data-field]').each(function (i, el) {
          _this14.$header_.find(Utils.sprintf('th[data-field="%s"]', $$q(el).data('field'))).data($$q(el).data());
        });
        var visibleFields = this.getVisibleFields();
        var $ths = this.$header_.find('th');
        var $tr = this.$body.find('>tr:not(.no-records-found,.virtual-scroll-top)').eq(0);
        while ($tr.length && $tr.find('>td[colspan]:not([colspan="1"])').length) {
          $tr = $tr.next();
        }
        var trLength = $tr.find('> *').length;
        $tr.find('> *').each(function (i, el) {
          var $this = $$q(el);
          if (Utils.hasDetailViewIcon(_this14.options)) {
            if (i === 0 && _this14.options.detailViewAlign !== 'right' || i === trLength - 1 && _this14.options.detailViewAlign === 'right') {
              var $thDetail = $ths.filter('.detail');
              var _zoomWidth = $thDetail.innerWidth() - $thDetail.find('.fht-cell').width();
              $thDetail.find('.fht-cell').width($this.innerWidth() - _zoomWidth);
              return;
            }
          }
          var index = i - Utils.getDetailViewIndexOffset(_this14.options);
          var $th = _this14.$header_.find(Utils.sprintf('th[data-field="%s"]', visibleFields[index]));
          if ($th.length > 1) {
            $th = $$q($ths[$this[0].cellIndex]);
          }
          var zoomWidth = $th.innerWidth() - $th.find('.fht-cell').width();
          $th.find('.fht-cell').width($this.innerWidth() - zoomWidth);
        });
        this.horizontalScroll();
        this.trigger('post-header');
      }
    }, {
      key: "initFooter",
      value: function initFooter() {
        if (!this.options.showFooter || this.options.cardView) {
          // do nothing
          return;
        }
        var data = this.getData();
        var html = [];
        var detailTemplate = '';
        if (Utils.hasDetailViewIcon(this.options)) {
          detailTemplate = '<th class="detail"><div class="th-inner"></div><div class="fht-cell"></div></th>';
        }
        if (detailTemplate && this.options.detailViewAlign !== 'right') {
          html.push(detailTemplate);
        }
        var _iterator4 = _createForOfIteratorHelper(this.columns),
          _step4;
        try {
          for (_iterator4.s(); !(_step4 = _iterator4.n()).done;) {
            var column = _step4.value;
            var falign = '';
            var valign = '';
            var csses = [];
            var style = {};
            var class_ = Utils.sprintf(' class="%s"', column['class']);
            if (!column.visible || this.footerData && this.footerData.length > 0 && !(column.field in this.footerData[0])) {
              continue;
            }
            if (this.options.cardView && !column.cardVisible) {
              return;
            }
            falign = Utils.sprintf('text-align: %s; ', column.falign ? column.falign : column.align);
            valign = Utils.sprintf('vertical-align: %s; ', column.valign);
            style = Utils.calculateObjectValue(null, column.footerStyle || this.options.footerStyle, [column]);
            if (style && style.css) {
              for (var _i14 = 0, _Object$entries13 = Object.entries(style.css); _i14 < _Object$entries13.length; _i14++) {
                var _Object$entries13$_i = _slicedToArray(_Object$entries13[_i14], 2),
                  key = _Object$entries13$_i[0],
                  _value3 = _Object$entries13$_i[1];
                csses.push("".concat(key, ": ").concat(_value3));
              }
            }
            if (style && style.classes) {
              class_ = Utils.sprintf(' class="%s"', column['class'] ? [column['class'], style.classes].join(' ') : style.classes);
            }
            html.push('<th', class_, Utils.sprintf(' style="%s"', falign + valign + csses.concat().join('; ') || undefined));
            var colspan = 0;
            if (this.footerData && this.footerData.length > 0) {
              colspan = this.footerData[0]["_".concat(column.field, "_colspan")] || 0;
            }
            if (colspan) {
              html.push(" colspan=\"".concat(colspan, "\" "));
            }
            html.push('>');
            html.push('<div class="th-inner">');
            var value = '';
            if (this.footerData && this.footerData.length > 0) {
              value = this.footerData[0][column.field] || '';
            }
            html.push(Utils.calculateObjectValue(column, column.footerFormatter, [data, value], value));
            html.push('</div>');
            html.push('<div class="fht-cell"></div>');
            html.push('</div>');
            html.push('</th>');
          }
        } catch (err) {
          _iterator4.e(err);
        } finally {
          _iterator4.f();
        }
        if (detailTemplate && this.options.detailViewAlign === 'right') {
          html.push(detailTemplate);
        }
        if (!this.options.height && !this.$tableFooter.length) {
          this.$el.append('<tfoot><tr></tr></tfoot>');
          this.$tableFooter = this.$el.find('tfoot');
        }
        if (!this.$tableFooter.find('tr').length) {
          this.$tableFooter.html('<table><thead><tr></tr></thead></table>');
        }
        this.$tableFooter.find('tr').html(html.join(''));
        this.trigger('post-footer', this.$tableFooter);
      }
    }, {
      key: "fitFooter",
      value: function fitFooter() {
        var _this15 = this;
        if (this.$el.is(':hidden')) {
          setTimeout(function () {
            return _this15.fitFooter();
          }, 100);
          return;
        }
        var fixedBody = this.$tableBody.get(0);
        var scrollWidth = this.hasScrollBar && fixedBody.scrollHeight > fixedBody.clientHeight + this.$header.outerHeight() ? Utils.getScrollBarWidth() : 0;
        this.$tableFooter.css('margin-right', scrollWidth).find('table').css('width', this.$el.outerWidth()).attr('class', this.$el.attr('class'));
        var $ths = this.$tableFooter.find('th');
        var $tr = this.$body.find('>tr:first-child:not(.no-records-found)');
        $ths.find('.fht-cell').width('auto');
        while ($tr.length && $tr.find('>td[colspan]:not([colspan="1"])').length) {
          $tr = $tr.next();
        }
        var trLength = $tr.find('> *').length;
        $tr.find('> *').each(function (i, el) {
          var $this = $$q(el);
          if (Utils.hasDetailViewIcon(_this15.options)) {
            if (i === 0 && _this15.options.detailViewAlign === 'left' || i === trLength - 1 && _this15.options.detailViewAlign === 'right') {
              var $thDetail = $ths.filter('.detail');
              var _zoomWidth2 = $thDetail.innerWidth() - $thDetail.find('.fht-cell').width();
              $thDetail.find('.fht-cell').width($this.innerWidth() - _zoomWidth2);
              return;
            }
          }
          var $th = $ths.eq(i);
          var zoomWidth = $th.innerWidth() - $th.find('.fht-cell').width();
          $th.find('.fht-cell').width($this.innerWidth() - zoomWidth);
        });
        this.horizontalScroll();
      }
    }, {
      key: "horizontalScroll",
      value: function horizontalScroll() {
        var _this16 = this;
        // horizontal scroll event
        // TODO: it's probably better improving the layout than binding to scroll event
        this.$tableBody.off('scroll').on('scroll', function () {
          var scrollLeft = _this16.$tableBody.scrollLeft();
          if (_this16.options.showHeader && _this16.options.height) {
            _this16.$tableHeader.scrollLeft(scrollLeft);
          }
          if (_this16.options.showFooter && !_this16.options.cardView) {
            _this16.$tableFooter.scrollLeft(scrollLeft);
          }
          _this16.trigger('scroll-body', _this16.$tableBody);
        });
      }
    }, {
      key: "getVisibleFields",
      value: function getVisibleFields() {
        var visibleFields = [];
        var _iterator5 = _createForOfIteratorHelper(this.header.fields),
          _step5;
        try {
          for (_iterator5.s(); !(_step5 = _iterator5.n()).done;) {
            var field = _step5.value;
            var column = this.columns[this.fieldsColumnsIndex[field]];
            if (!column || !column.visible || this.options.cardView && !column.cardVisible) {
              continue;
            }
            visibleFields.push(field);
          }
        } catch (err) {
          _iterator5.e(err);
        } finally {
          _iterator5.f();
        }
        return visibleFields;
      }
    }, {
      key: "initHiddenRows",
      value: function initHiddenRows() {
        this.hiddenRows = [];
      }

      // PUBLIC FUNCTION DEFINITION
      // =======================
    }, {
      key: "getOptions",
      value: function getOptions() {
        // deep copy and remove data
        var options = Utils.extend({}, this.options);
        delete options.data;
        return Utils.extend(true, {}, options);
      }
    }, {
      key: "refreshOptions",
      value: function refreshOptions(options) {
        // If the objects are equivalent then avoid the call of destroy / init methods
        if (Utils.compareObjects(this.options, options, true)) {
          return;
        }
        this.options = Utils.extend(this.options, options);
        this.trigger('refresh-options', this.options);
        this.destroy();
        this.init();
      }
    }, {
      key: "getData",
      value: function getData(params) {
        var _this17 = this;
        var data = this.options.data;
        if ((this.searchText || this.options.customSearch || this.options.sortName !== undefined || this.enableCustomSort ||
        // Fix #4616: this.enableCustomSort is for extensions
        !Utils.isEmptyObject(this.filterColumns) || typeof this.options.filterOptions.filterAlgorithm === 'function' || !Utils.isEmptyObject(this.filterColumnsPartial)) && (!params || !params.unfiltered)) {
          data = this.data;
        }
        if (params && !params.includeHiddenRows) {
          var hiddenRows = this.getHiddenRows();
          data = data.filter(function (row) {
            return Utils.findIndex(hiddenRows, row) === -1;
          });
        }
        if (params && params.useCurrentPage) {
          data = data.slice(this.pageFrom - 1, this.pageTo);
        }
        if (params && params.formatted) {
          data.forEach(function (row) {
            for (var _i15 = 0, _Object$entries14 = Object.entries(row); _i15 < _Object$entries14.length; _i15++) {
              var _Object$entries14$_i = _slicedToArray(_Object$entries14[_i15], 2),
                key = _Object$entries14$_i[0],
                value = _Object$entries14$_i[1];
              var column = _this17.columns[_this17.fieldsColumnsIndex[key]];
              if (!column) {
                return;
              }
              row[key] = Utils.calculateObjectValue(column, _this17.header.formatters[column.fieldIndex], [value, row, row.index, column.field], value);
            }
          });
        }
        return data;
      }
    }, {
      key: "getSelections",
      value: function getSelections() {
        var _this18 = this;
        return (this.options.maintainMetaData ? this.options.data : this.data).filter(function (row) {
          return row[_this18.header.stateField] === true;
        });
      }
    }, {
      key: "load",
      value: function load(_data) {
        var fixedScroll = false;
        var data = _data;

        // #431: support pagination
        if (this.options.pagination && this.options.sidePagination === 'server') {
          this.options.totalRows = data[this.options.totalField];
          this.options.totalNotFiltered = data[this.options.totalNotFilteredField];
          this.footerData = data[this.options.footerField] ? [data[this.options.footerField]] : undefined;
        }
        fixedScroll = this.options.fixedScroll || data.fixedScroll;
        data = Array.isArray(data) ? data : data[this.options.dataField];
        this.initData(data);
        this.initSearch();
        this.initPagination();
        this.initBody(fixedScroll);
      }
    }, {
      key: "append",
      value: function append(data) {
        this.initData(data, 'append');
        this.initSearch();
        this.initPagination();
        this.initSort();
        this.initBody(true);
      }
    }, {
      key: "prepend",
      value: function prepend(data) {
        this.initData(data, 'prepend');
        this.initSearch();
        this.initPagination();
        this.initSort();
        this.initBody(true);
      }
    }, {
      key: "remove",
      value: function remove(params) {
        var removed = 0;
        for (var i = this.options.data.length - 1; i >= 0; i--) {
          var row = this.options.data[i];
          var value = Utils.getItemField(row, params.field, this.options.escape, row.escape);
          if (value === undefined && params.field !== '$index') {
            continue;
          }
          if (!row.hasOwnProperty(params.field) && params.field === '$index' && params.values.includes(i) || params.values.includes(value)) {
            removed++;
            this.options.data.splice(i, 1);
          }
        }
        if (!removed) {
          return;
        }
        if (this.options.sidePagination === 'server') {
          this.options.totalRows -= removed;
          this.data = _toConsumableArray(this.options.data);
        }
        this.initSearch();
        this.initPagination();
        this.initSort();
        this.initBody(true);
      }
    }, {
      key: "removeAll",
      value: function removeAll() {
        if (this.options.data.length > 0) {
          this.options.data.splice(0, this.options.data.length);
          this.initSearch();
          this.initPagination();
          this.initBody(true);
        }
      }
    }, {
      key: "insertRow",
      value: function insertRow(params) {
        if (!params.hasOwnProperty('index') || !params.hasOwnProperty('row')) {
          return;
        }
        this.options.data.splice(params.index, 0, params.row);
        this.initSearch();
        this.initPagination();
        this.initSort();
        this.initBody(true);
      }
    }, {
      key: "updateRow",
      value: function updateRow(params) {
        var allParams = Array.isArray(params) ? params : [params];
        var _iterator6 = _createForOfIteratorHelper(allParams),
          _step6;
        try {
          for (_iterator6.s(); !(_step6 = _iterator6.n()).done;) {
            var _params = _step6.value;
            if (!_params.hasOwnProperty('index') || !_params.hasOwnProperty('row')) {
              continue;
            }
            if (_params.hasOwnProperty('replace') && _params.replace) {
              this.options.data[_params.index] = _params.row;
            } else {
              Utils.extend(this.options.data[_params.index], _params.row);
            }
          }
        } catch (err) {
          _iterator6.e(err);
        } finally {
          _iterator6.f();
        }
        this.initSearch();
        this.initPagination();
        this.initSort();
        this.initBody(true);
      }
    }, {
      key: "getRowByUniqueId",
      value: function getRowByUniqueId(_id) {
        var uniqueId = this.options.uniqueId;
        var len = this.options.data.length;
        var id = _id;
        var dataRow = null;
        var i;
        var row;
        for (i = len - 1; i >= 0; i--) {
          row = this.options.data[i];
          var rowUniqueId = Utils.getItemField(row, uniqueId, this.options.escape, row.escape);
          if (rowUniqueId === undefined) {
            continue;
          }
          if (typeof rowUniqueId === 'string') {
            id = _id.toString();
          } else if (typeof rowUniqueId === 'number') {
            if (Number(rowUniqueId) === rowUniqueId && rowUniqueId % 1 === 0) {
              id = parseInt(_id, 10);
            } else if (rowUniqueId === Number(rowUniqueId) && rowUniqueId !== 0) {
              id = parseFloat(_id);
            }
          }
          if (rowUniqueId === id) {
            dataRow = row;
            break;
          }
        }
        return dataRow;
      }
    }, {
      key: "updateByUniqueId",
      value: function updateByUniqueId(params) {
        var allParams = Array.isArray(params) ? params : [params];
        var updatedUid = null;
        var _iterator7 = _createForOfIteratorHelper(allParams),
          _step7;
        try {
          for (_iterator7.s(); !(_step7 = _iterator7.n()).done;) {
            var _params2 = _step7.value;
            if (!_params2.hasOwnProperty('id') || !_params2.hasOwnProperty('row')) {
              continue;
            }
            var rowId = this.options.data.indexOf(this.getRowByUniqueId(_params2.id));
            if (rowId === -1) {
              continue;
            }
            if (_params2.hasOwnProperty('replace') && _params2.replace) {
              this.options.data[rowId] = _params2.row;
            } else {
              Utils.extend(this.options.data[rowId], _params2.row);
            }
            updatedUid = _params2.id;
          }
        } catch (err) {
          _iterator7.e(err);
        } finally {
          _iterator7.f();
        }
        this.initSearch();
        this.initPagination();
        this.initSort();
        this.initBody(true, updatedUid);
      }
    }, {
      key: "removeByUniqueId",
      value: function removeByUniqueId(id) {
        var len = this.options.data.length;
        var row = this.getRowByUniqueId(id);
        if (row) {
          this.options.data.splice(this.options.data.indexOf(row), 1);
        }
        if (len === this.options.data.length) {
          return;
        }
        if (this.options.sidePagination === 'server') {
          this.options.totalRows -= 1;
          this.data = _toConsumableArray(this.options.data);
        }
        this.initSearch();
        this.initPagination();
        this.initBody(true);
      }
    }, {
      key: "_updateCellOnly",
      value: function _updateCellOnly(field, index) {
        var rowHtml = this.initRow(this.options.data[index], index);
        var fieldIndex = this.getVisibleFields().indexOf(field);
        if (fieldIndex === -1) {
          return;
        }
        fieldIndex += Utils.getDetailViewIndexOffset(this.options);
        this.$body.find(">tr[data-index=".concat(index, "]")).find(">td:eq(".concat(fieldIndex, ")")).replaceWith($$q(rowHtml).find(">td:eq(".concat(fieldIndex, ")")));
        this.initBodyEvent();
        this.initFooter();
        this.resetView();
        this.updateSelected();
      }
    }, {
      key: "updateCell",
      value: function updateCell(params) {
        if (!params.hasOwnProperty('index') || !params.hasOwnProperty('field') || !params.hasOwnProperty('value')) {
          return;
        }
        this.options.data[params.index][params.field] = params.value;
        if (params.reinit === false) {
          this._updateCellOnly(params.field, params.index);
          return;
        }
        this.initSort();
        this.initBody(true);
      }
    }, {
      key: "updateCellByUniqueId",
      value: function updateCellByUniqueId(params) {
        var _this19 = this;
        var allParams = Array.isArray(params) ? params : [params];
        allParams.forEach(function (_ref6) {
          var id = _ref6.id,
            field = _ref6.field,
            value = _ref6.value;
          var index = _this19.options.data.indexOf(_this19.getRowByUniqueId(id));
          if (index === -1) {
            return;
          }
          _this19.options.data[index][field] = value;
        });
        if (params.reinit === false) {
          this._updateCellOnly(params.field, this.options.data.indexOf(this.getRowByUniqueId(params.id)));
          return;
        }
        this.initSort();
        this.initBody(true);
      }
    }, {
      key: "showRow",
      value: function showRow(params) {
        this._toggleRow(params, true);
      }
    }, {
      key: "hideRow",
      value: function hideRow(params) {
        this._toggleRow(params, false);
      }
    }, {
      key: "_toggleRow",
      value: function _toggleRow(params, visible) {
        var row;
        if (params.hasOwnProperty('index')) {
          row = this.getData()[params.index];
        } else if (params.hasOwnProperty('uniqueId')) {
          row = this.getRowByUniqueId(params.uniqueId);
        }
        if (!row) {
          return;
        }
        var index = Utils.findIndex(this.hiddenRows, row);
        if (!visible && index === -1) {
          this.hiddenRows.push(row);
        } else if (visible && index > -1) {
          this.hiddenRows.splice(index, 1);
        }
        this.initBody(true);
        this.initPagination();
      }
    }, {
      key: "getHiddenRows",
      value: function getHiddenRows(show) {
        if (show) {
          this.initHiddenRows();
          this.initBody(true);
          this.initPagination();
          return;
        }
        var data = this.getData();
        var rows = [];
        var _iterator8 = _createForOfIteratorHelper(data),
          _step8;
        try {
          for (_iterator8.s(); !(_step8 = _iterator8.n()).done;) {
            var row = _step8.value;
            if (this.hiddenRows.includes(row)) {
              rows.push(row);
            }
          }
        } catch (err) {
          _iterator8.e(err);
        } finally {
          _iterator8.f();
        }
        this.hiddenRows = rows;
        return rows;
      }
    }, {
      key: "showColumn",
      value: function showColumn(field) {
        var _this20 = this;
        var fields = Array.isArray(field) ? field : [field];
        fields.forEach(function (field) {
          _this20._toggleColumn(_this20.fieldsColumnsIndex[field], true, true);
        });
      }
    }, {
      key: "hideColumn",
      value: function hideColumn(field) {
        var _this21 = this;
        var fields = Array.isArray(field) ? field : [field];
        fields.forEach(function (field) {
          _this21._toggleColumn(_this21.fieldsColumnsIndex[field], false, true);
        });
      }
    }, {
      key: "_toggleColumn",
      value: function _toggleColumn(index, checked, needUpdate) {
        if (index === -1 || this.columns[index].visible === checked) {
          return;
        }
        this.columns[index].visible = checked;
        this.initHeader();
        this.initSearch();
        this.initPagination();
        this.initBody();
        if (this.options.showColumns) {
          var $items = this.$toolbar.find('.keep-open input:not(".toggle-all")').prop('disabled', false);
          if (needUpdate) {
            $items.filter(Utils.sprintf('[value="%s"]', index)).prop('checked', checked);
          }
          if ($items.filter(':checked').length <= this.options.minimumCountColumns) {
            $items.filter(':checked').prop('disabled', true);
          }
        }
      }
    }, {
      key: "getVisibleColumns",
      value: function getVisibleColumns() {
        var _this22 = this;
        return this.columns.filter(function (column) {
          return column.visible && !_this22.isSelectionColumn(column);
        });
      }
    }, {
      key: "getHiddenColumns",
      value: function getHiddenColumns() {
        return this.columns.filter(function (_ref7) {
          var visible = _ref7.visible;
          return !visible;
        });
      }
    }, {
      key: "isSelectionColumn",
      value: function isSelectionColumn(column) {
        return column.radio || column.checkbox;
      }
    }, {
      key: "showAllColumns",
      value: function showAllColumns() {
        this._toggleAllColumns(true);
      }
    }, {
      key: "hideAllColumns",
      value: function hideAllColumns() {
        this._toggleAllColumns(false);
      }
    }, {
      key: "_toggleAllColumns",
      value: function _toggleAllColumns(visible) {
        var _this23 = this;
        var _iterator9 = _createForOfIteratorHelper(this.columns.slice().reverse()),
          _step9;
        try {
          for (_iterator9.s(); !(_step9 = _iterator9.n()).done;) {
            var column = _step9.value;
            if (column.switchable) {
              if (!visible && this.options.showColumns && this.getVisibleColumns().filter(function (it) {
                return it.switchable;
              }).length === this.options.minimumCountColumns) {
                continue;
              }
              column.visible = visible;
            }
          }
        } catch (err) {
          _iterator9.e(err);
        } finally {
          _iterator9.f();
        }
        this.initHeader();
        this.initSearch();
        this.initPagination();
        this.initBody();
        if (this.options.showColumns) {
          var $items = this.$toolbar.find('.keep-open input[type="checkbox"]:not(".toggle-all")').prop('disabled', false);
          if (visible) {
            $items.prop('checked', visible);
          } else {
            $items.get().reverse().forEach(function (item) {
              if ($items.filter(':checked').length > _this23.options.minimumCountColumns) {
                $$q(item).prop('checked', visible);
              }
            });
          }
          if ($items.filter(':checked').length <= this.options.minimumCountColumns) {
            $items.filter(':checked').prop('disabled', true);
          }
        }
      }
    }, {
      key: "mergeCells",
      value: function mergeCells(options) {
        var row = options.index;
        var col = this.getVisibleFields().indexOf(options.field);
        var rowspan = options.rowspan || 1;
        var colspan = options.colspan || 1;
        var i;
        var j;
        var $tr = this.$body.find('>tr[data-index]');
        col += Utils.getDetailViewIndexOffset(this.options);
        var $td = $tr.eq(row).find('>td').eq(col);
        if (row < 0 || col < 0 || row >= this.data.length) {
          return;
        }
        for (i = row; i < row + rowspan; i++) {
          for (j = col; j < col + colspan; j++) {
            $tr.eq(i).find('>td').eq(j).hide();
          }
        }
        $td.attr('rowspan', rowspan).attr('colspan', colspan).show();
      }
    }, {
      key: "checkAll",
      value: function checkAll() {
        this._toggleCheckAll(true);
      }
    }, {
      key: "uncheckAll",
      value: function uncheckAll() {
        this._toggleCheckAll(false);
      }
    }, {
      key: "_toggleCheckAll",
      value: function _toggleCheckAll(checked) {
        var rowsBefore = this.getSelections();
        this.$selectAll.add(this.$selectAll_).prop('checked', checked);
        this.$selectItem.filter(':enabled').prop('checked', checked);
        this.updateRows();
        this.updateSelected();
        var rowsAfter = this.getSelections();
        if (checked) {
          this.trigger('check-all', rowsAfter, rowsBefore);
          return;
        }
        this.trigger('uncheck-all', rowsAfter, rowsBefore);
      }
    }, {
      key: "checkInvert",
      value: function checkInvert() {
        var $items = this.$selectItem.filter(':enabled');
        var checked = $items.filter(':checked');
        $items.each(function (i, el) {
          $$q(el).prop('checked', !$$q(el).prop('checked'));
        });
        this.updateRows();
        this.updateSelected();
        this.trigger('uncheck-some', checked);
        checked = this.getSelections();
        this.trigger('check-some', checked);
      }
    }, {
      key: "check",
      value: function check(index) {
        this._toggleCheck(true, index);
      }
    }, {
      key: "uncheck",
      value: function uncheck(index) {
        this._toggleCheck(false, index);
      }
    }, {
      key: "_toggleCheck",
      value: function _toggleCheck(checked, index) {
        var $el = this.$selectItem.filter("[data-index=\"".concat(index, "\"]"));
        var row = this.data[index];
        if ($el.is(':radio') || this.options.singleSelect || this.options.multipleSelectRow && !this.multipleSelectRowCtrlKey && !this.multipleSelectRowShiftKey) {
          var _iterator10 = _createForOfIteratorHelper(this.options.data),
            _step10;
          try {
            for (_iterator10.s(); !(_step10 = _iterator10.n()).done;) {
              var r = _step10.value;
              r[this.header.stateField] = false;
            }
          } catch (err) {
            _iterator10.e(err);
          } finally {
            _iterator10.f();
          }
          this.$selectItem.filter(':checked').not($el).prop('checked', false);
        }
        row[this.header.stateField] = checked;
        if (this.options.multipleSelectRow) {
          if (this.multipleSelectRowShiftKey && this.multipleSelectRowLastSelectedIndex >= 0) {
            var _ref8 = this.multipleSelectRowLastSelectedIndex < index ? [this.multipleSelectRowLastSelectedIndex, index] : [index, this.multipleSelectRowLastSelectedIndex],
              _ref9 = _slicedToArray(_ref8, 2),
              fromIndex = _ref9[0],
              toIndex = _ref9[1];
            for (var i = fromIndex + 1; i < toIndex; i++) {
              this.data[i][this.header.stateField] = true;
              this.$selectItem.filter("[data-index=\"".concat(i, "\"]")).prop('checked', true);
            }
          }
          this.multipleSelectRowCtrlKey = false;
          this.multipleSelectRowShiftKey = false;
          this.multipleSelectRowLastSelectedIndex = checked ? index : -1;
        }
        $el.prop('checked', checked);
        this.updateSelected();
        this.trigger(checked ? 'check' : 'uncheck', this.data[index], $el);
      }
    }, {
      key: "checkBy",
      value: function checkBy(obj) {
        this._toggleCheckBy(true, obj);
      }
    }, {
      key: "uncheckBy",
      value: function uncheckBy(obj) {
        this._toggleCheckBy(false, obj);
      }
    }, {
      key: "_toggleCheckBy",
      value: function _toggleCheckBy(checked, obj) {
        var _this24 = this;
        if (!obj.hasOwnProperty('field') || !obj.hasOwnProperty('values')) {
          return;
        }
        var rows = [];
        this.data.forEach(function (row, i) {
          if (!row.hasOwnProperty(obj.field)) {
            return false;
          }
          if (obj.values.includes(row[obj.field])) {
            var $el = _this24.$selectItem.filter(':enabled').filter(Utils.sprintf('[data-index="%s"]', i));
            var onlyCurrentPage = obj.hasOwnProperty('onlyCurrentPage') ? obj.onlyCurrentPage : false;
            $el = checked ? $el.not(':checked') : $el.filter(':checked');
            if (!$el.length && onlyCurrentPage) {
              return;
            }
            $el.prop('checked', checked);
            row[_this24.header.stateField] = checked;
            rows.push(row);
            _this24.trigger(checked ? 'check' : 'uncheck', row, $el);
          }
        });
        this.updateSelected();
        this.trigger(checked ? 'check-some' : 'uncheck-some', rows);
      }
    }, {
      key: "refresh",
      value: function refresh(params) {
        if (params && params.url) {
          this.options.url = params.url;
        }
        if (params && params.pageNumber) {
          this.options.pageNumber = params.pageNumber;
        }
        if (params && params.pageSize) {
          this.options.pageSize = params.pageSize;
        }
        this.trigger('refresh', this.initServer(params && params.silent, params && params.query, params && params.url));
      }
    }, {
      key: "destroy",
      value: function destroy() {
        this.$el.insertBefore(this.$container);
        $$q(this.options.toolbar).insertBefore(this.$el);
        this.$container.next().remove();
        this.$container.remove();
        this.$el.html(this.$el_.html()).css('margin-top', '0').attr('class', this.$el_.attr('class') || ''); // reset the class

        var resizeEvent = Utils.getEventName('resize.bootstrap-table', this.$el.attr('id'));
        $$q(window).off(resizeEvent);
      }
    }, {
      key: "resetView",
      value: function resetView(params) {
        var padding = 0;
        if (params && params.height) {
          this.options.height = params.height;
        }
        this.$tableContainer.toggleClass('has-card-view', this.options.cardView);
        if (this.options.height) {
          var fixedBody = this.$tableBody.get(0);
          this.hasScrollBar = fixedBody.scrollWidth > fixedBody.clientWidth;
        }
        if (!this.options.cardView && this.options.showHeader && this.options.height) {
          this.$tableHeader.show();
          this.resetHeader();
          padding += this.$header.outerHeight(true) + 1;
        } else {
          this.$tableHeader.hide();
          this.trigger('post-header');
        }
        if (!this.options.cardView && this.options.showFooter) {
          this.$tableFooter.show();
          this.fitFooter();
          if (this.options.height) {
            padding += this.$tableFooter.outerHeight(true);
          }
        }
        if (this.$container.hasClass('fullscreen')) {
          this.$tableContainer.css('height', '');
          this.$tableContainer.css('width', '');
        } else if (this.options.height) {
          if (this.$tableBorder) {
            this.$tableBorder.css('width', '');
            this.$tableBorder.css('height', '');
          }
          var toolbarHeight = this.$toolbar.outerHeight(true);
          var paginationHeight = this.$pagination.outerHeight(true);
          var height = this.options.height - toolbarHeight - paginationHeight;
          var $bodyTable = this.$tableBody.find('>table');
          var tableHeight = $bodyTable.outerHeight();
          this.$tableContainer.css('height', "".concat(height, "px"));
          if (this.$tableBorder && $bodyTable.is(':visible')) {
            var tableBorderHeight = height - tableHeight - 2;
            if (this.hasScrollBar) {
              tableBorderHeight -= Utils.getScrollBarWidth();
            }
            this.$tableBorder.css('width', "".concat($bodyTable.outerWidth(), "px"));
            this.$tableBorder.css('height', "".concat(tableBorderHeight, "px"));
          }
        }
        if (this.options.cardView) {
          // remove the element css
          this.$el.css('margin-top', '0');
          this.$tableContainer.css('padding-bottom', '0');
          this.$tableFooter.hide();
        } else {
          // Assign the correct sortable arrow
          this.getCaret();
          this.$tableContainer.css('padding-bottom', "".concat(padding, "px"));
        }
        this.trigger('reset-view');
      }
    }, {
      key: "showLoading",
      value: function showLoading() {
        this.$tableLoading.toggleClass('open', true);
        var fontSize = this.options.loadingFontSize;
        if (this.options.loadingFontSize === 'auto') {
          fontSize = this.$tableLoading.width() * 0.04;
          fontSize = Math.max(12, fontSize);
          fontSize = Math.min(32, fontSize);
          fontSize = "".concat(fontSize, "px");
        }
        this.$tableLoading.find('.loading-text').css('font-size', fontSize);
      }
    }, {
      key: "hideLoading",
      value: function hideLoading() {
        this.$tableLoading.toggleClass('open', false);
      }
    }, {
      key: "togglePagination",
      value: function togglePagination() {
        this.options.pagination = !this.options.pagination;
        var icon = this.options.showButtonIcons ? this.options.pagination ? this.options.icons.paginationSwitchDown : this.options.icons.paginationSwitchUp : '';
        var text = this.options.showButtonText ? this.options.pagination ? this.options.formatPaginationSwitchUp() : this.options.formatPaginationSwitchDown() : '';
        this.$toolbar.find('button[name="paginationSwitch"]').html("".concat(Utils.sprintf(this.constants.html.icon, this.options.iconsPrefix, icon), " ").concat(text));
        this.updatePagination();
        this.trigger('toggle-pagination', this.options.pagination);
      }
    }, {
      key: "toggleFullscreen",
      value: function toggleFullscreen() {
        this.$el.closest('.bootstrap-table').toggleClass('fullscreen');
        this.resetView();
      }
    }, {
      key: "toggleView",
      value: function toggleView() {
        this.options.cardView = !this.options.cardView;
        this.initHeader();
        var icon = this.options.showButtonIcons ? this.options.cardView ? this.options.icons.toggleOn : this.options.icons.toggleOff : '';
        var text = this.options.showButtonText ? this.options.cardView ? this.options.formatToggleOff() : this.options.formatToggleOn() : '';
        this.$toolbar.find('button[name="toggle"]').html("".concat(Utils.sprintf(this.constants.html.icon, this.options.iconsPrefix, icon), " ").concat(text)).attr('aria-label', text).attr('title', text);
        this.initBody();
        this.trigger('toggle', this.options.cardView);
      }
    }, {
      key: "resetSearch",
      value: function resetSearch(text) {
        var $search = Utils.getSearchInput(this);
        var textToUse = text || '';
        $search.val(textToUse);
        this.searchText = textToUse;
        this.onSearch({
          currentTarget: $search
        }, false);
      }
    }, {
      key: "filterBy",
      value: function filterBy(columns, options) {
        this.filterOptions = Utils.isEmptyObject(options) ? this.options.filterOptions : Utils.extend(this.options.filterOptions, options);
        this.filterColumns = Utils.isEmptyObject(columns) ? {} : columns;
        this.options.pageNumber = 1;
        this.initSearch();
        this.updatePagination();
      }
    }, {
      key: "scrollTo",
      value: function scrollTo(params) {
        var options = {
          unit: 'px',
          value: 0
        };
        if (_typeof(params) === 'object') {
          options = Object.assign(options, params);
        } else if (typeof params === 'string' && params === 'bottom') {
          options.value = this.$tableBody[0].scrollHeight;
        } else if (typeof params === 'string' || typeof params === 'number') {
          options.value = params;
        }
        var scrollTo = options.value;
        if (options.unit === 'rows') {
          scrollTo = 0;
          this.$body.find("> tr:lt(".concat(options.value, ")")).each(function (i, el) {
            scrollTo += $$q(el).outerHeight(true);
          });
        }
        this.$tableBody.scrollTop(scrollTo);
      }
    }, {
      key: "getScrollPosition",
      value: function getScrollPosition() {
        return this.$tableBody.scrollTop();
      }
    }, {
      key: "selectPage",
      value: function selectPage(page) {
        if (page > 0 && page <= this.options.totalPages) {
          this.options.pageNumber = page;
          this.updatePagination();
        }
      }
    }, {
      key: "prevPage",
      value: function prevPage() {
        if (this.options.pageNumber > 1) {
          this.options.pageNumber--;
          this.updatePagination();
        }
      }
    }, {
      key: "nextPage",
      value: function nextPage() {
        if (this.options.pageNumber < this.options.totalPages) {
          this.options.pageNumber++;
          this.updatePagination();
        }
      }
    }, {
      key: "toggleDetailView",
      value: function toggleDetailView(index, _columnDetailFormatter) {
        var $tr = this.$body.find(Utils.sprintf('> tr[data-index="%s"]', index));
        if ($tr.next().is('tr.detail-view')) {
          this.collapseRow(index);
        } else {
          this.expandRow(index, _columnDetailFormatter);
        }
        this.resetView();
      }
    }, {
      key: "expandRow",
      value: function expandRow(index, _columnDetailFormatter) {
        var row = this.data[index];
        var $tr = this.$body.find(Utils.sprintf('> tr[data-index="%s"][data-has-detail-view]', index));
        if (this.options.detailViewIcon) {
          $tr.find('a.detail-icon').html(Utils.sprintf(this.constants.html.icon, this.options.iconsPrefix, this.options.icons.detailClose));
        }
        if ($tr.next().is('tr.detail-view')) {
          return;
        }
        $tr.after(Utils.sprintf('<tr class="detail-view"><td colspan="%s"></td></tr>', $tr.children('td').length));
        var $element = $tr.next().find('td');
        var detailFormatter = _columnDetailFormatter || this.options.detailFormatter;
        var content = Utils.calculateObjectValue(this.options, detailFormatter, [index, row, $element], '');
        if ($element.length === 1) {
          $element.append(content);
        }
        this.trigger('expand-row', index, row, $element);
      }
    }, {
      key: "expandRowByUniqueId",
      value: function expandRowByUniqueId(uniqueId) {
        var row = this.getRowByUniqueId(uniqueId);
        if (!row) {
          return;
        }
        this.expandRow(this.data.indexOf(row));
      }
    }, {
      key: "collapseRow",
      value: function collapseRow(index) {
        var row = this.data[index];
        var $tr = this.$body.find(Utils.sprintf('> tr[data-index="%s"][data-has-detail-view]', index));
        if (!$tr.next().is('tr.detail-view')) {
          return;
        }
        if (this.options.detailViewIcon) {
          $tr.find('a.detail-icon').html(Utils.sprintf(this.constants.html.icon, this.options.iconsPrefix, this.options.icons.detailOpen));
        }
        this.trigger('collapse-row', index, row, $tr.next());
        $tr.next().remove();
      }
    }, {
      key: "collapseRowByUniqueId",
      value: function collapseRowByUniqueId(uniqueId) {
        var row = this.getRowByUniqueId(uniqueId);
        if (!row) {
          return;
        }
        this.collapseRow(this.data.indexOf(row));
      }
    }, {
      key: "expandAllRows",
      value: function expandAllRows() {
        var trs = this.$body.find('> tr[data-index][data-has-detail-view]');
        for (var i = 0; i < trs.length; i++) {
          this.expandRow($$q(trs[i]).data('index'));
        }
      }
    }, {
      key: "collapseAllRows",
      value: function collapseAllRows() {
        var trs = this.$body.find('> tr[data-index][data-has-detail-view]');
        for (var i = 0; i < trs.length; i++) {
          this.collapseRow($$q(trs[i]).data('index'));
        }
      }
    }, {
      key: "updateColumnTitle",
      value: function updateColumnTitle(params) {
        if (!params.hasOwnProperty('field') || !params.hasOwnProperty('title')) {
          return;
        }
        this.columns[this.fieldsColumnsIndex[params.field]].title = this.options.escape && this.options.escapeTitle ? Utils.escapeHTML(params.title) : params.title;
        if (this.columns[this.fieldsColumnsIndex[params.field]].visible) {
          this.$header.find('th[data-field]').each(function (i, el) {
            if ($$q(el).data('field') === params.field) {
              $$q($$q(el).find('.th-inner')[0]).html(params.title);
              return false;
            }
          });
          this.resetView();
        }
      }
    }, {
      key: "updateFormatText",
      value: function updateFormatText(formatName, text) {
        if (!/^format/.test(formatName) || !this.options[formatName]) {
          return;
        }
        if (typeof text === 'string') {
          this.options[formatName] = function () {
            return text;
          };
        } else if (typeof text === 'function') {
          this.options[formatName] = text;
        }
        this.initToolbar();
        this.initPagination();
        this.initBody();
      }
    }]);
  }();
  BootstrapTable.VERSION = Constants.VERSION;
  BootstrapTable.DEFAULTS = Constants.DEFAULTS;
  BootstrapTable.LOCALES = Constants.LOCALES;
  BootstrapTable.COLUMN_DEFAULTS = Constants.COLUMN_DEFAULTS;
  BootstrapTable.METHODS = Constants.METHODS;
  BootstrapTable.EVENTS = Constants.EVENTS;

  // BOOTSTRAP TABLE PLUGIN DEFINITION
  // =======================

  $$q.BootstrapTable = BootstrapTable;
  $$q.fn.bootstrapTable = function (option) {
    for (var _len2 = arguments.length, args = new Array(_len2 > 1 ? _len2 - 1 : 0), _key5 = 1; _key5 < _len2; _key5++) {
      args[_key5 - 1] = arguments[_key5];
    }
    var value;
    this.each(function (i, el) {
      var data = $$q(el).data('bootstrap.table');
      if (typeof option === 'string') {
        var _data2;
        if (!Constants.METHODS.includes(option)) {
          throw new Error("Unknown method: ".concat(option));
        }
        if (!data) {
          return;
        }
        value = (_data2 = data)[option].apply(_data2, args);
        if (option === 'destroy') {
          $$q(el).removeData('bootstrap.table');
        }
        return;
      }
      if (data) {
        console.warn('You cannot initialize the table more than once!');
        return;
      }
      var options = Utils.extend(true, {}, BootstrapTable.DEFAULTS, $$q(el).data(), _typeof(option) === 'object' && option);
      data = new $$q.BootstrapTable(el, options);
      $$q(el).data('bootstrap.table', data);
      data.init();
    });
    return typeof value === 'undefined' ? this : value;
  };
  $$q.fn.bootstrapTable.Constructor = BootstrapTable;
  $$q.fn.bootstrapTable.theme = Constants.THEME;
  $$q.fn.bootstrapTable.VERSION = Constants.VERSION;
  $$q.fn.bootstrapTable.defaults = BootstrapTable.DEFAULTS;
  $$q.fn.bootstrapTable.columnDefaults = BootstrapTable.COLUMN_DEFAULTS;
  $$q.fn.bootstrapTable.events = BootstrapTable.EVENTS;
  $$q.fn.bootstrapTable.locales = BootstrapTable.LOCALES;
  $$q.fn.bootstrapTable.methods = BootstrapTable.METHODS;
  $$q.fn.bootstrapTable.utils = Utils;

  // BOOTSTRAP TABLE INIT
  // =======================

  $$q(function () {
    $$q('[data-toggle="table"]').bootstrapTable();
  });

  return BootstrapTable;

}));


/*! @preserve
 * bootbox.js
 * version: 5.5.3
 * author: Nick Payne <nick@kurai.co.uk>
 * license: MIT
 * http://bootboxjs.com/
 */
(function (root, factory) {
  'use strict';
  if (typeof define === 'function' && define.amd) {
    // AMD
    define(['jquery'], factory);
  } else if (typeof exports === 'object') {
    // Node, CommonJS-like
    module.exports = factory(require('jquery'));
  } else {
    // Browser globals (root is window)
    root.bootbox = factory(root.jQuery);
  }
}(this, function init($, undefined) {
  'use strict';

  //  Polyfills Object.keys, if necessary.
  //  @see https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/Object/keys
  if (!Object.keys) {
    Object.keys = (function () {
      var hasOwnProperty = Object.prototype.hasOwnProperty,
        hasDontEnumBug = !({ toString: null }).propertyIsEnumerable('toString'),
        dontEnums = [
          'toString',
          'toLocaleString',
          'valueOf',
          'hasOwnProperty',
          'isPrototypeOf',
          'propertyIsEnumerable',
          'constructor'
        ],
        dontEnumsLength = dontEnums.length;

      return function (obj) {
        if (typeof obj !== 'function' && (typeof obj !== 'object' || obj === null)) {
          throw new TypeError('Object.keys called on non-object');
        }

        var result = [], prop, i;

        for (prop in obj) {
          if (hasOwnProperty.call(obj, prop)) {
            result.push(prop);
          }
        }

        if (hasDontEnumBug) {
          for (i = 0; i < dontEnumsLength; i++) {
            if (hasOwnProperty.call(obj, dontEnums[i])) {
              result.push(dontEnums[i]);
            }
          }
        }

        return result;
      };
    }());
  }

  var exports = {};

  var VERSION = '5.5.3';
  exports.VERSION = VERSION;

  var locales = {
    en : {
      OK      : 'OK',
      CANCEL  : 'Cancel',
      CONFIRM : 'OK'
    }
  };

  var templates = {
    dialog:
    '<div class="bootbox modal" tabindex="-1" role="dialog" aria-hidden="true">' +
    '<div class="modal-dialog">' +
    '<div class="modal-content">' +
    '<div class="modal-body"><div class="bootbox-body"></div></div>' +
    '</div>' +
    '</div>' +
    '</div>',
    header:
    '<div class="modal-header">' +
    '<h5 class="modal-title"></h5>' +
    '</div>',
    footer:
    '<div class="modal-footer"></div>',
    closeButton:
    '<button type="button" class="bootbox-close-button close" aria-hidden="true">&times;</button>',
    form:
    '<form class="bootbox-form"></form>',
    button:
    '<button type="button" class="btn"></button>',
    option:
    '<option></option>',
    promptMessage:
    '<div class="bootbox-prompt-message"></div>',
    inputs: {
      text:
      '<input class="bootbox-input bootbox-input-text form-control" autocomplete="off" type="text" />',
      textarea:
      '<textarea class="bootbox-input bootbox-input-textarea form-control"></textarea>',
      email:
      '<input class="bootbox-input bootbox-input-email form-control" autocomplete="off" type="email" />',
      select:
      '<select class="bootbox-input bootbox-input-select form-control"></select>',
      checkbox:
      '<div class="form-check checkbox"><label class="form-check-label"><input class="form-check-input bootbox-input bootbox-input-checkbox" type="checkbox" /></label></div>',
      radio:
      '<div class="form-check radio"><label class="form-check-label"><input class="form-check-input bootbox-input bootbox-input-radio" type="radio" name="bootbox-radio" /></label></div>',
      date:
      '<input class="bootbox-input bootbox-input-date form-control" autocomplete="off" type="date" />',
      time:
      '<input class="bootbox-input bootbox-input-time form-control" autocomplete="off" type="time" />',
      number:
      '<input class="bootbox-input bootbox-input-number form-control" autocomplete="off" type="number" />',
      password:
      '<input class="bootbox-input bootbox-input-password form-control" autocomplete="off" type="password" />',
      range:
      '<input class="bootbox-input bootbox-input-range form-control-range" autocomplete="off" type="range" />'
    }
  };


  var defaults = {
    // default language
    locale: 'en',
    // show backdrop or not. Default to static so user has to interact with dialog
    backdrop: 'static',
    // animate the modal in/out
    animate: true,
    // additional class string applied to the top level dialog
    className: null,
    // whether or not to include a close button
    closeButton: true,
    // show the dialog immediately by default
    show: true,
    // dialog container
    container: 'body',
    // default value (used by the prompt helper)
    value: '',
    // default input type (used by the prompt helper)
    inputType: 'text',
    // switch button order from cancel/confirm (default) to confirm/cancel
    swapButtonOrder: false,
    // center modal vertically in page
    centerVertical: false,
    // Append "multiple" property to the select when using the "prompt" helper
    multiple: false,
    // Automatically scroll modal content when height exceeds viewport height
    scrollable: false,
    // whether or not to destroy the modal on hide
    reusable: false,
    // the element that triggered the dialog opening
    relatedTarget: null
  };


  // PUBLIC FUNCTIONS
  // *************************************************************************************************************

  // Return all currently registered locales, or a specific locale if "name" is defined
  exports.locales = function (name) {
    return name ? locales[name] : locales;
  };


  // Register localized strings for the OK, CONFIRM, and CANCEL buttons
  exports.addLocale = function (name, values) {
    $.each(['OK', 'CANCEL', 'CONFIRM'], function (_, v) {
      if (!values[v]) {
        throw new Error('Please supply a translation for "' + v + '"');
      }
    });

    locales[name] = {
      OK: values.OK,
      CANCEL: values.CANCEL,
      CONFIRM: values.CONFIRM
    };

    return exports;
  };


  // Remove a previously-registered locale
  exports.removeLocale = function (name) {
    if (name !== 'en') {
      delete locales[name];
    }
    else {
      throw new Error('"en" is used as the default and fallback locale and cannot be removed.');
    }

    return exports;
  };


  // Set the default locale
  exports.setLocale = function (name) {
    return exports.setDefaults('locale', name);
  };


  // Override default value(s) of Bootbox.
  exports.setDefaults = function () {
    var values = {};

    if (arguments.length === 2) {
      // allow passing of single key/value...
      values[arguments[0]] = arguments[1];
    } else {
      // ... and as an object too
      values = arguments[0];
    }

    $.extend(defaults, values);

    return exports;
  };


  // Hides all currently active Bootbox modals
  exports.hideAll = function () {
    $('.bootbox').modal('hide');

    return exports;
  };


  // Allows the base init() function to be overridden
  exports.init = function (_$) {
    return init(_$ || $);
  };


  // CORE HELPER FUNCTIONS
  // *************************************************************************************************************

  // Core dialog function
  exports.dialog = function (options) {
    if ($.fn.modal === undefined) {
      throw new Error(
        '"$.fn.modal" is not defined; please double check you have included ' +
        'the Bootstrap JavaScript library. See https://getbootstrap.com/docs/4.4/getting-started/javascript/ ' +
        'for more details.'
      );
    }

    options = sanitize(options);

    if ($.fn.modal.Constructor.VERSION) {
      options.fullBootstrapVersion = $.fn.modal.Constructor.VERSION;
      var i = options.fullBootstrapVersion.indexOf('.');
      options.bootstrap = options.fullBootstrapVersion.substring(0, i);
    }
    else {
      // Assuming version 2.3.2, as that was the last "supported" 2.x version
      options.bootstrap = '2';
      options.fullBootstrapVersion = '2.3.2';
      console.warn('Bootbox will *mostly* work with Bootstrap 2, but we do not officially support it. Please upgrade, if possible.');
    }

    var dialog = $(templates.dialog);
    var innerDialog = dialog.find('.modal-dialog');
    var body = dialog.find('.modal-body');
    var header = $(templates.header);
    var footer = $(templates.footer);
    var buttons = options.buttons;

    var callbacks = {
      onEscape: options.onEscape
    };

    body.find('.bootbox-body').html(options.message);

    // Only attempt to create buttons if at least one has 
    // been defined in the options object
    if (getKeyLength(options.buttons) > 0) {
      each(buttons, function (key, b) {
        var button = $(templates.button);
        button.data('bb-handler', key);
        button.addClass(b.className);

        switch (key) {
          case 'ok':
          case 'confirm':
            button.addClass('bootbox-accept');
            break;

          case 'cancel':
            button.addClass('bootbox-cancel');
            break;
        }

        button.html(b.label);
        footer.append(button);

        callbacks[key] = b.callback;
      });

      body.after(footer);
    }

    if (options.animate === true) {
      dialog.addClass('fade');
    }

    if (options.className) {
      dialog.addClass(options.className);
    }

    if (options.size) {
      // Requires Bootstrap 3.1.0 or higher
      if (options.fullBootstrapVersion.substring(0, 3) < '3.1') {
        console.warn('"size" requires Bootstrap 3.1.0 or higher. You appear to be using ' + options.fullBootstrapVersion + '. Please upgrade to use this option.');
      }

      switch (options.size) {
        case 'small':
        case 'sm':
          innerDialog.addClass('modal-sm');
          break;

        case 'large':
        case 'lg':
          innerDialog.addClass('modal-lg');
          break;

        case 'extra-large':
        case 'xl':
          innerDialog.addClass('modal-xl');

          // Requires Bootstrap 4.2.0 or higher
          if (options.fullBootstrapVersion.substring(0, 3) < '4.2') {
            console.warn('Using size "xl"/"extra-large" requires Bootstrap 4.2.0 or higher. You appear to be using ' + options.fullBootstrapVersion + '. Please upgrade to use this option.');
          }
          break;
      }
    }

    if (options.scrollable) {
      innerDialog.addClass('modal-dialog-scrollable');

      // Requires Bootstrap 4.3.0 or higher
      if (options.fullBootstrapVersion.substring(0, 3) < '4.3') {
        console.warn('Using "scrollable" requires Bootstrap 4.3.0 or higher. You appear to be using ' + options.fullBootstrapVersion + '. Please upgrade to use this option.');
      }
    }

    if (options.title) {
      body.before(header);
      dialog.find('.modal-title').html(options.title);
    }

    if (options.closeButton) {
      var closeButton = $(templates.closeButton);

      if (options.title) {
        if (options.bootstrap > 3) {
          dialog.find('.modal-header').append(closeButton);
        }
        else {
          dialog.find('.modal-header').prepend(closeButton);
        }
      } else {
        closeButton.prependTo(body);
      }
    }

    if (options.centerVertical) {
      innerDialog.addClass('modal-dialog-centered');

      // Requires Bootstrap 4.0.0-beta.3 or higher
      if (options.fullBootstrapVersion < '4.0.0') {
        console.warn('"centerVertical" requires Bootstrap 4.0.0-beta.3 or higher. You appear to be using ' + options.fullBootstrapVersion + '. Please upgrade to use this option.');
      }
    }

    // Bootstrap event listeners; these handle extra
    // setup & teardown required after the underlying
    // modal has performed certain actions.

    if(!options.reusable) {
      // make sure we unbind any listeners once the dialog has definitively been dismissed
      dialog.one('hide.bs.modal', { dialog: dialog }, unbindModal);
    }

    if (options.onHide) {
      if ($.isFunction(options.onHide)) {
        dialog.on('hide.bs.modal', options.onHide);
      }
      else {
        throw new Error('Argument supplied to "onHide" must be a function');
      }
    }

    if(!options.reusable) {
      dialog.one('hidden.bs.modal', { dialog: dialog }, destroyModal);
    }

    if (options.onHidden) {
      if ($.isFunction(options.onHidden)) {
        dialog.on('hidden.bs.modal', options.onHidden);
      }
      else {
        throw new Error('Argument supplied to "onHidden" must be a function');
      }
    }

    if (options.onShow) {
      if ($.isFunction(options.onShow)) {
        dialog.on('show.bs.modal', options.onShow);
      }
      else {
        throw new Error('Argument supplied to "onShow" must be a function');
      }
    }

    dialog.one('shown.bs.modal', { dialog: dialog }, focusPrimaryButton);

    if (options.onShown) {
      if ($.isFunction(options.onShown)) {
        dialog.on('shown.bs.modal', options.onShown);
      }
      else {
        throw new Error('Argument supplied to "onShown" must be a function');
      }
    }

    // Bootbox event listeners; used to decouple some
    // behaviours from their respective triggers

    if (options.backdrop === true) {
      // A boolean true/false according to the Bootstrap docs
      // should show a dialog the user can dismiss by clicking on
      // the background.
      // We always only ever pass static/false to the actual
      // $.modal function because with "true" we can't trap
      // this event (the .modal-backdrop swallows it)
      // However, we still want to sort-of respect true
      // and invoke the escape mechanism instead
      dialog.on('click.dismiss.bs.modal', function (e) {
        // @NOTE: the target varies in >= 3.3.x releases since the modal backdrop
        // moved *inside* the outer dialog rather than *alongside* it
        if (dialog.children('.modal-backdrop').length) {
          e.currentTarget = dialog.children('.modal-backdrop').get(0);
        }

        if (e.target !== e.currentTarget) {
          return;
        }

        dialog.trigger('escape.close.bb');
      });
    }

    dialog.on('escape.close.bb', function (e) {
      // the if statement looks redundant but it isn't; without it
      // if we *didn't* have an onEscape handler then processCallback
      // would automatically dismiss the dialog
      if (callbacks.onEscape) {
        processCallback(e, dialog, callbacks.onEscape);
      }
    });


    dialog.on('click', '.modal-footer button:not(.disabled)', function (e) {
      var callbackKey = $(this).data('bb-handler');

      if (callbackKey !== undefined) {
        // Only process callbacks for buttons we recognize:
        processCallback(e, dialog, callbacks[callbackKey]);
      }
    });

    dialog.on('click', '.bootbox-close-button', function (e) {
      // onEscape might be falsy but that's fine; the fact is
      // if the user has managed to click the close button we
      // have to close the dialog, callback or not
      processCallback(e, dialog, callbacks.onEscape);
    });

    dialog.on('keyup', function (e) {
      if (e.which === 27) {
        dialog.trigger('escape.close.bb');
      }
    });

    // the remainder of this method simply deals with adding our
    // dialog element to the DOM, augmenting it with Bootstrap's modal
    // functionality and then giving the resulting object back
    // to our caller

    $(options.container).append(dialog);

    dialog.modal({
      backdrop: options.backdrop,
      keyboard: false,
      show: false
    });

    if (options.show) {
      dialog.modal('show', options.relatedTarget);
    }

    return dialog;
  };


  // Helper function to simulate the native alert() behavior. **NOTE**: This is non-blocking, so any
  // code that must happen after the alert is dismissed should be placed within the callback function 
  // for this alert.
  exports.alert = function () {
    var options;

    options = mergeDialogOptions('alert', ['ok'], ['message', 'callback'], arguments);

    // @TODO: can this move inside exports.dialog when we're iterating over each
    // button and checking its button.callback value instead?
    if (options.callback && !$.isFunction(options.callback)) {
      throw new Error('alert requires the "callback" property to be a function when provided');
    }

    // override the ok and escape callback to make sure they just invoke
    // the single user-supplied one (if provided)
    options.buttons.ok.callback = options.onEscape = function () {
      if ($.isFunction(options.callback)) {
        return options.callback.call(this);
      }

      return true;
    };

    return exports.dialog(options);
  };


  // Helper function to simulate the native confirm() behavior. **NOTE**: This is non-blocking, so any
  // code that must happen after the confirm is dismissed should be placed within the callback function 
  // for this confirm.
  exports.confirm = function () {
    var options;

    options = mergeDialogOptions('confirm', ['cancel', 'confirm'], ['message', 'callback'], arguments);

    // confirm specific validation; they don't make sense without a callback so make
    // sure it's present
    if (!$.isFunction(options.callback)) {
      throw new Error('confirm requires a callback');
    }

    // overrides; undo anything the user tried to set they shouldn't have
    options.buttons.cancel.callback = options.onEscape = function () {
      return options.callback.call(this, false);
    };

    options.buttons.confirm.callback = function () {
      return options.callback.call(this, true);
    };

    return exports.dialog(options);
  };


  // Helper function to simulate the native prompt() behavior. **NOTE**: This is non-blocking, so any
  // code that must happen after the prompt is dismissed should be placed within the callback function 
  // for this prompt.
  exports.prompt = function () {
    var options;
    var promptDialog;
    var form;
    var input;
    var shouldShow;
    var inputOptions;

    // we have to create our form first otherwise
    // its value is undefined when gearing up our options
    // @TODO this could be solved by allowing message to
    // be a function instead...
    form = $(templates.form);

    // prompt defaults are more complex than others in that
    // users can override more defaults
    options = mergeDialogOptions('prompt', ['cancel', 'confirm'], ['title', 'callback'], arguments);

    if (!options.value) {
      options.value = defaults.value;
    }

    if (!options.inputType) {
      options.inputType = defaults.inputType;
    }

    // capture the user's show value; we always set this to false before
    // spawning the dialog to give us a chance to attach some handlers to
    // it, but we need to make sure we respect a preference not to show it
    shouldShow = (options.show === undefined) ? defaults.show : options.show;

    // This is required prior to calling the dialog builder below - we need to 
    // add an event handler just before the prompt is shown
    options.show = false;

    // Handles the 'cancel' action
    options.buttons.cancel.callback = options.onEscape = function () {
      return options.callback.call(this, null);
    };

    // Prompt submitted - extract the prompt value. This requires a bit of work, 
    // given the different input types available.
    options.buttons.confirm.callback = function () {
      var value;

      if (options.inputType === 'checkbox') {
        value = input.find('input:checked').map(function () {
          return $(this).val();
        }).get();
      } else if (options.inputType === 'radio') {
        value = input.find('input:checked').val();
      }
      else {
        if (input[0].checkValidity && !input[0].checkValidity()) {
          // prevents button callback from being called
          return false;
        } else {
          if (options.inputType === 'select' && options.multiple === true) {
            value = input.find('option:selected').map(function () {
              return $(this).val();
            }).get();
          }
          else {
            value = input.val();
          }
        }
      }

      return options.callback.call(this, value);
    };

    // prompt-specific validation
    if (!options.title) {
      throw new Error('prompt requires a title');
    }

    if (!$.isFunction(options.callback)) {
      throw new Error('prompt requires a callback');
    }

    if (!templates.inputs[options.inputType]) {
      throw new Error('Invalid prompt type');
    }

    // create the input based on the supplied type
    input = $(templates.inputs[options.inputType]);

    switch (options.inputType) {
      case 'text':
      case 'textarea':
      case 'email':
      case 'password':
        input.val(options.value);

        if (options.placeholder) {
          input.attr('placeholder', options.placeholder);
        }

        if (options.pattern) {
          input.attr('pattern', options.pattern);
        }

        if (options.maxlength) {
          input.attr('maxlength', options.maxlength);
        }

        if (options.required) {
          input.prop({ 'required': true });
        }

        if (options.rows && !isNaN(parseInt(options.rows))) {
          if (options.inputType === 'textarea') {
            input.attr({ 'rows': options.rows });
          }
        }

        break;


      case 'date':
      case 'time':
      case 'number':
      case 'range':
        input.val(options.value);

        if (options.placeholder) {
          input.attr('placeholder', options.placeholder);
        }

        if (options.pattern) {
          input.attr('pattern', options.pattern);
        }

        if (options.required) {
          input.prop({ 'required': true });
        }

        // These input types have extra attributes which affect their input validation.
        // Warning: For most browsers, date inputs are buggy in their implementation of 'step', so 
        // this attribute will have no effect. Therefore, we don't set the attribute for date inputs.
        // @see https://developer.mozilla.org/en-US/docs/Web/HTML/Element/input/date#Setting_maximum_and_minimum_dates
        if (options.inputType !== 'date') {
          if (options.step) {
            if (options.step === 'any' || (!isNaN(options.step) && parseFloat(options.step) > 0)) {
              input.attr('step', options.step);
            }
            else {
              throw new Error('"step" must be a valid positive number or the value "any". See https://developer.mozilla.org/en-US/docs/Web/HTML/Element/input#attr-step for more information.');
            }
          }
        }

        if (minAndMaxAreValid(options.inputType, options.min, options.max)) {
          if (options.min !== undefined) {
            input.attr('min', options.min);
          }
          if (options.max !== undefined) {
            input.attr('max', options.max);
          }
        }

        break;


      case 'select':
        var groups = {};
        inputOptions = options.inputOptions || [];

        if (!$.isArray(inputOptions)) {
          throw new Error('Please pass an array of input options');
        }

        if (!inputOptions.length) {
          throw new Error('prompt with "inputType" set to "select" requires at least one option');
        }

        // placeholder is not actually a valid attribute for select,
        // but we'll allow it, assuming it might be used for a plugin
        if (options.placeholder) {
          input.attr('placeholder', options.placeholder);
        }

        if (options.required) {
          input.prop({ 'required': true });
        }

        if (options.multiple) {
          input.prop({ 'multiple': true });
        }

        each(inputOptions, function (_, option) {
          // assume the element to attach to is the input...
          var elem = input;

          if (option.value === undefined || option.text === undefined) {
            throw new Error('each option needs a "value" property and a "text" property');
          }

          // ... but override that element if this option sits in a group

          if (option.group) {
            // initialise group if necessary
            if (!groups[option.group]) {
              groups[option.group] = $('<optgroup />').attr('label', option.group);
            }

            elem = groups[option.group];
          }

          var o = $(templates.option);
          o.attr('value', option.value).text(option.text);
          elem.append(o);
        });

        each(groups, function (_, group) {
          input.append(group);
        });

        // safe to set a select's value as per a normal input
        input.val(options.value);

        break;


      case 'checkbox':
        var checkboxValues = $.isArray(options.value) ? options.value : [options.value];
        inputOptions = options.inputOptions || [];

        if (!inputOptions.length) {
          throw new Error('prompt with "inputType" set to "checkbox" requires at least one option');
        }

        // checkboxes have to nest within a containing element, so
        // they break the rules a bit and we end up re-assigning
        // our 'input' element to this container instead
        input = $('<div class="bootbox-checkbox-list"></div>');

        each(inputOptions, function (_, option) {
          if (option.value === undefined || option.text === undefined) {
            throw new Error('each option needs a "value" property and a "text" property');
          }

          var checkbox = $(templates.inputs[options.inputType]);

          checkbox.find('input').attr('value', option.value);
          checkbox.find('label').append('\n' + option.text);

          // we've ensured values is an array so we can always iterate over it
          each(checkboxValues, function (_, value) {
            if (value === option.value) {
              checkbox.find('input').prop('checked', true);
            }
          });

          input.append(checkbox);
        });
        break;


      case 'radio':
        // Make sure that value is not an array (only a single radio can ever be checked)
        if (options.value !== undefined && $.isArray(options.value)) {
          throw new Error('prompt with "inputType" set to "radio" requires a single, non-array value for "value"');
        }

        inputOptions = options.inputOptions || [];

        if (!inputOptions.length) {
          throw new Error('prompt with "inputType" set to "radio" requires at least one option');
        }

        // Radiobuttons have to nest within a containing element, so
        // they break the rules a bit and we end up re-assigning
        // our 'input' element to this container instead
        input = $('<div class="bootbox-radiobutton-list"></div>');

        // Radiobuttons should always have an initial checked input checked in a "group".
        // If value is undefined or doesn't match an input option, select the first radiobutton
        var checkFirstRadio = true;

        each(inputOptions, function (_, option) {
          if (option.value === undefined || option.text === undefined) {
            throw new Error('each option needs a "value" property and a "text" property');
          }

          var radio = $(templates.inputs[options.inputType]);

          radio.find('input').attr('value', option.value);
          radio.find('label').append('\n' + option.text);

          if (options.value !== undefined) {
            if (option.value === options.value) {
              radio.find('input').prop('checked', true);
              checkFirstRadio = false;
            }
          }

          input.append(radio);
        });

        if (checkFirstRadio) {
          input.find('input[type="radio"]').first().prop('checked', true);
        }
        break;
    }

    // now place it in our form
    form.append(input);

    form.on('submit', function (e) {
      e.preventDefault();
      // Fix for SammyJS (or similar JS routing library) hijacking the form post.
      e.stopPropagation();

      // @TODO can we actually click *the* button object instead?
      // e.g. buttons.confirm.click() or similar
      promptDialog.find('.bootbox-accept').trigger('click');
    });

    if ($.trim(options.message) !== '') {
      // Add the form to whatever content the user may have added.
      var message = $(templates.promptMessage).html(options.message);
      form.prepend(message);
      options.message = form;
    }
    else {
      options.message = form;
    }

    // Generate the dialog
    promptDialog = exports.dialog(options);

    // clear the existing handler focusing the submit button...
    promptDialog.off('shown.bs.modal', focusPrimaryButton);

    // ...and replace it with one focusing our input, if possible
    promptDialog.on('shown.bs.modal', function () {
      // need the closure here since input isn't
      // an object otherwise
      input.focus();
    });

    if (shouldShow === true) {
      promptDialog.modal('show');
    }

    return promptDialog;
  };


  // INTERNAL FUNCTIONS
  // *************************************************************************************************************

  // Map a flexible set of arguments into a single returned object
  // If args.length is already one just return it, otherwise
  // use the properties argument to map the unnamed args to
  // object properties.
  // So in the latter case:
  //  mapArguments(["foo", $.noop], ["message", "callback"])
  //  -> { message: "foo", callback: $.noop }
  function mapArguments(args, properties) {
    var argn = args.length;
    var options = {};

    if (argn < 1 || argn > 2) {
      throw new Error('Invalid argument length');
    }

    if (argn === 2 || typeof args[0] === 'string') {
      options[properties[0]] = args[0];
      options[properties[1]] = args[1];
    } else {
      options = args[0];
    }

    return options;
  }


  //  Merge a set of default dialog options with user supplied arguments
  function mergeArguments(defaults, args, properties) {
    return $.extend(
      // deep merge
      true,
      // ensure the target is an empty, unreferenced object
      {},
      // the base options object for this type of dialog (often just buttons)
      defaults,
      // args could be an object or array; if it's an array properties will
      // map it to a proper options object
      mapArguments(
        args,
        properties
      )
    );
  }


  //  This entry-level method makes heavy use of composition to take a simple
  //  range of inputs and return valid options suitable for passing to bootbox.dialog
  function mergeDialogOptions(className, labels, properties, args) {
    var locale;
    if (args && args[0]) {
      locale = args[0].locale || defaults.locale;
      var swapButtons = args[0].swapButtonOrder || defaults.swapButtonOrder;

      if (swapButtons) {
        labels = labels.reverse();
      }
    }

    //  build up a base set of dialog properties
    var baseOptions = {
      className: 'bootbox-' + className,
      buttons: createLabels(labels, locale)
    };

    // Ensure the buttons properties generated, *after* merging
    // with user args are still valid against the supplied labels
    return validateButtons(
      // merge the generated base properties with user supplied arguments
      mergeArguments(
        baseOptions,
        args,
        // if args.length > 1, properties specify how each arg maps to an object key
        properties
      ),
      labels
    );
  }


  //  Checks each button object to see if key is valid. 
  //  This function will only be called by the alert, confirm, and prompt helpers. 
  function validateButtons(options, buttons) {
    var allowedButtons = {};
    each(buttons, function (key, value) {
      allowedButtons[value] = true;
    });

    each(options.buttons, function (key) {
      if (allowedButtons[key] === undefined) {
        throw new Error('button key "' + key + '" is not allowed (options are ' + buttons.join(' ') + ')');
      }
    });

    return options;
  }



  //  From a given list of arguments, return a suitable object of button labels.
  //  All this does is normalise the given labels and translate them where possible.
  //  e.g. "ok", "confirm" -> { ok: "OK", cancel: "Annuleren" }
  function createLabels(labels, locale) {
    var buttons = {};

    for (var i = 0, j = labels.length; i < j; i++) {
      var argument = labels[i];
      var key = argument.toLowerCase();
      var value = argument.toUpperCase();

      buttons[key] = {
        label: getText(value, locale)
      };
    }

    return buttons;
  }



  //  Get localized text from a locale. Defaults to 'en' locale if no locale 
  //  provided or a non-registered locale is requested
  function getText(key, locale) {
    var labels = locales[locale];

    return labels ? labels[key] : locales.en[key];
  }



  //  Filter and tidy up any user supplied parameters to this dialog.
  //  Also looks for any shorthands used and ensures that the options
  //  which are returned are all normalized properly
  function sanitize(options) {
    var buttons;
    var total;

    if (typeof options !== 'object') {
      throw new Error('Please supply an object of options');
    }

    if (!options.message) {
      throw new Error('"message" option must not be null or an empty string.');
    }

    // make sure any supplied options take precedence over defaults
    options = $.extend({}, defaults, options);

    //make sure backdrop is either true, false, or 'static'
    if (!options.backdrop) {
      options.backdrop = (options.backdrop === false || options.backdrop === 0) ? false : 'static';
    } else {
      options.backdrop = typeof options.backdrop === 'string' && options.backdrop.toLowerCase() === 'static' ? 'static' : true;
    } 

    // no buttons is still a valid dialog but it's cleaner to always have
    // a buttons object to iterate over, even if it's empty
    if (!options.buttons) {
      options.buttons = {};
    }
    
    buttons = options.buttons;

    total = getKeyLength(buttons);

    each(buttons, function (key, button, index) {
      if ($.isFunction(button)) {
        // short form, assume value is our callback. Since button
        // isn't an object it isn't a reference either so re-assign it
        button = buttons[key] = {
          callback: button
        };
      }

      // before any further checks make sure by now button is the correct type
      if ($.type(button) !== 'object') {
        throw new Error('button with key "' + key + '" must be an object');
      }

      if (!button.label) {
        // the lack of an explicit label means we'll assume the key is good enough
        button.label = key;
      }

      if (!button.className) {
        var isPrimary = false;
        if (options.swapButtonOrder) {
          isPrimary = index === 0;
        }
        else {
          isPrimary = index === total - 1;
        }

        if (total <= 2 && isPrimary) {
          // always add a primary to the main option in a one or two-button dialog
          button.className = 'btn-primary';
        } else {
          // adding both classes allows us to target both BS3 and BS4 without needing to check the version
          button.className = 'btn-secondary btn-default';
        }
      }
    });

    return options;
  }


  //  Returns a count of the properties defined on the object
  function getKeyLength(obj) {
    return Object.keys(obj).length;
  }


  //  Tiny wrapper function around jQuery.each; just adds index as the third parameter
  function each(collection, iterator) {
    var index = 0;
    $.each(collection, function (key, value) {
      iterator(key, value, index++);
    });
  }


  function focusPrimaryButton(e) {
    e.data.dialog.find('.bootbox-accept').first().trigger('focus');
  }


  function destroyModal(e) {
    // ensure we don't accidentally intercept hidden events triggered
    // by children of the current dialog. We shouldn't need to handle this anymore, 
    // now that Bootstrap namespaces its events, but still worth doing.
    if (e.target === e.data.dialog[0]) {
      e.data.dialog.remove();
    }
  }


  function unbindModal(e) {
    if (e.target === e.data.dialog[0]) {
      e.data.dialog.off('escape.close.bb');
      e.data.dialog.off('click');
    }
  }


  //  Handle the invoked dialog callback
  function processCallback(e, dialog, callback) {
    e.stopPropagation();
    e.preventDefault();

    // by default we assume a callback will get rid of the dialog,
    // although it is given the opportunity to override this

    // so, if the callback can be invoked and it *explicitly returns false*
    // then we'll set a flag to keep the dialog active...
    var preserveDialog = $.isFunction(callback) && callback.call(dialog, e) === false;

    // ... otherwise we'll bin it
    if (!preserveDialog) {
      dialog.modal('hide');
    }
  }

  // Validate `min` and `max` values based on the current `inputType` value
  function minAndMaxAreValid(type, min, max) {
    var result = false;
    var minValid = true;
    var maxValid = true;

    if (type === 'date') {
      if (min !== undefined && !(minValid = dateIsValid(min))) {
        console.warn('Browsers which natively support the "date" input type expect date values to be of the form "YYYY-MM-DD" (see ISO-8601 https://www.iso.org/iso-8601-date-and-time-format.html). Bootbox does not enforce this rule, but your min value may not be enforced by this browser.');
      }
      else if (max !== undefined && !(maxValid = dateIsValid(max))) {
        console.warn('Browsers which natively support the "date" input type expect date values to be of the form "YYYY-MM-DD" (see ISO-8601 https://www.iso.org/iso-8601-date-and-time-format.html). Bootbox does not enforce this rule, but your max value may not be enforced by this browser.');
      }
    }
    else if (type === 'time') {
      if (min !== undefined && !(minValid = timeIsValid(min))) {
        throw new Error('"min" is not a valid time. See https://www.w3.org/TR/2012/WD-html-markup-20120315/datatypes.html#form.data.time for more information.');
      }
      else if (max !== undefined && !(maxValid = timeIsValid(max))) {
        throw new Error('"max" is not a valid time. See https://www.w3.org/TR/2012/WD-html-markup-20120315/datatypes.html#form.data.time for more information.');
      }
    }
    else {
      if (min !== undefined && isNaN(min)) {
        minValid = false;
        throw new Error('"min" must be a valid number. See https://developer.mozilla.org/en-US/docs/Web/HTML/Element/input#attr-min for more information.');
      }

      if (max !== undefined && isNaN(max)) {
        maxValid = false;
        throw new Error('"max" must be a valid number. See https://developer.mozilla.org/en-US/docs/Web/HTML/Element/input#attr-max for more information.');
      }
    }

    if (minValid && maxValid) {
      if (max <= min) {
        throw new Error('"max" must be greater than "min". See https://developer.mozilla.org/en-US/docs/Web/HTML/Element/input#attr-max for more information.');
      }
      else {
        result = true;
      }
    }

    return result;
  }

  function timeIsValid(value) {
    return /([01][0-9]|2[0-3]):[0-5][0-9]?:[0-5][0-9]/.test(value);
  }

  function dateIsValid(value) {
    return /(\d{4})-(\d{2})-(\d{2})/.test(value);
  }

  //  The Bootbox object
  return exports;
}));
