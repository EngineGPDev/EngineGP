 /*

    Template Name:      Enabled Mobile & Tablet Templates
    Theme URL:          http://enableds.com
    Author:             Enabled
    Author URI:         http://themeforest.net/user/Enabled?ref=Enabled 
    Author Facebook:    https://www.facebook.com/enabled.labs
    Author Twitter:     https://twitter.com/iEnabled
    Version:            4.0
    Envato License:     Regular or Extended via ThemeForest
    Plugin Licenses:    GPL / MIT - Redistribution Allowed 
                        Each Plugin has it's indivudal license attached
    
    Description:        The framework plugins is built into one single JS file to allow the 
                        template universal, fast access to all the items. As -webkit- browsers 
                        cache the JS on load, this asures best loading times on all platforms 
                        and at the same time asures you will find everything you need in one 
                        single place. 

                        The custom.js file is where all scripts should be imported for usage
                        throughout the template. If you wish to import scripts inline that's 
                        completly up to you, but for a perfect function on all mobile devices. 
                        Mobile devices such as Nokia and Blackberrry handle JS better if it's 
                        implemented globally rather than inline throughout items.

                        Please Note! Not following the structure presented in the documentation
                        or altering custom.js and framework.plugins without proper experience 
                        can lead to the item malfunctioning. We are not responsible for any 
                        custom alterations and edits you make outside the as is item. 
    
    Warning:            All the plugins in the pack have been tested on numerous mobile devices
                        running different versions of their Native OS and differen browsers. 
                        Some of the plugins have been heavily altered to increase performance
                        or fix issues with differnet devices. We strongly recommend NOT to update
                        these plugins to newer versions. We constantly check and modify and update
                        them when a new, stable, proper quality version is released.
    
    Structure:
    
    01. FastClick        --   Version: 1.0.0
    02. Swipebox         --   Version: 1.1.2
    03. OWL Carousel     --   Version: 2.0.0
    04. Snap.JS          --   Version: 1.9.3
    05. CountdownJS      --   Version: 3.0.0
    06. ChartsJS         --   Version: 1.0.2
    07. SimpleModal      --   Version: 1.4.4 
    08. MouseWheel       --   Version: 3.1.2
    09. MorphText        --   Version: 2.3.4
    10. JustifiedGallery --   Version: 3.6.0
    11. AddToHomeScreen  --   Version: 3.2.2
    12. Contact Form     --   Version: 4.0.0
    
*/

(function($){
    /*
     * @preserve FastClick: polyfill to remove click delays on browsers with touch UIs.
     *
     * @codingstandard ftlabs-jsv2
     * @copyright The Financial Times Limited [All Rights Reserved]
     * @license MIT License (see LICENSE.txt)
     */

    !function(){"use strict";function t(e,o){function i(t,e){return function(){return t.apply(e,arguments)}}var r;if(o=o||{},this.trackingClick=!1,this.trackingClickStart=0,this.targetElement=null,this.touchStartX=0,this.touchStartY=0,this.lastTouchIdentifier=0,this.touchBoundary=o.touchBoundary||10,this.layer=e,this.tapDelay=o.tapDelay||200,this.tapTimeout=o.tapTimeout||700,!t.notNeeded(e)){for(var a=["onMouse","onClick","onTouchStart","onTouchMove","onTouchEnd","onTouchCancel"],c=this,s=0,u=a.length;u>s;s++)c[a[s]]=i(c[a[s]],c);n&&(e.addEventListener("mouseover",this.onMouse,!0),e.addEventListener("mousedown",this.onMouse,!0),e.addEventListener("mouseup",this.onMouse,!0)),e.addEventListener("click",this.onClick,!0),e.addEventListener("touchstart",this.onTouchStart,!1),e.addEventListener("touchmove",this.onTouchMove,!1),e.addEventListener("touchend",this.onTouchEnd,!1),e.addEventListener("touchcancel",this.onTouchCancel,!1),Event.prototype.stopImmediatePropagation||(e.removeEventListener=function(t,n,o){var i=Node.prototype.removeEventListener;"click"===t?i.call(e,t,n.hijacked||n,o):i.call(e,t,n,o)},e.addEventListener=function(t,n,o){var i=Node.prototype.addEventListener;"click"===t?i.call(e,t,n.hijacked||(n.hijacked=function(t){t.propagationStopped||n(t)}),o):i.call(e,t,n,o)}),"function"==typeof e.onclick&&(r=e.onclick,e.addEventListener("click",function(t){r(t)},!1),e.onclick=null)}}var e=navigator.userAgent.indexOf("Windows Phone")>=0,n=navigator.userAgent.indexOf("Android")>0&&!e,o=/iP(ad|hone|od)/.test(navigator.userAgent)&&!e,i=o&&/OS 4_\d(_\d)?/.test(navigator.userAgent),r=o&&/OS [6-7]_\d/.test(navigator.userAgent),a=navigator.userAgent.indexOf("BB10")>0;t.prototype.needsClick=function(t){switch(t.nodeName.toLowerCase()){case"button":case"select":case"textarea":if(t.disabled)return!0;break;case"input":if(o&&"file"===t.type||t.disabled)return!0;break;case"label":case"iframe":case"video":return!0}return/\bneedsclick\b/.test(t.className)},t.prototype.needsFocus=function(t){switch(t.nodeName.toLowerCase()){case"textarea":return!0;case"select":return!n;case"input":switch(t.type){case"button":case"checkbox":case"file":case"image":case"radio":case"submit":return!1}return!t.disabled&&!t.readOnly;default:return/\bneedsfocus\b/.test(t.className)}},t.prototype.sendClick=function(t,e){var n,o;document.activeElement&&document.activeElement!==t&&document.activeElement.blur(),o=e.changedTouches[0],n=document.createEvent("MouseEvents"),n.initMouseEvent(this.determineEventType(t),!0,!0,window,1,o.screenX,o.screenY,o.clientX,o.clientY,!1,!1,!1,!1,0,null),n.forwardedTouchEvent=!0,t.dispatchEvent(n)},t.prototype.determineEventType=function(t){return n&&"select"===t.tagName.toLowerCase()?"mousedown":"click"},t.prototype.focus=function(t){var e;o&&t.setSelectionRange&&0!==t.type.indexOf("date")&&"time"!==t.type&&"month"!==t.type?(e=t.value.length,t.setSelectionRange(e,e)):t.focus()},t.prototype.updateScrollParent=function(t){var e,n;if(e=t.fastClickScrollParent,!e||!e.contains(t)){n=t;do{if(n.scrollHeight>n.offsetHeight){e=n,t.fastClickScrollParent=n;break}n=n.parentElement}while(n)}e&&(e.fastClickLastScrollTop=e.scrollTop)},t.prototype.getTargetElementFromEventTarget=function(t){return t.nodeType===Node.TEXT_NODE?t.parentNode:t},t.prototype.onTouchStart=function(t){var e,n,r;if(t.targetTouches.length>1)return!0;if(e=this.getTargetElementFromEventTarget(t.target),n=t.targetTouches[0],o){if(r=window.getSelection(),r.rangeCount&&!r.isCollapsed)return!0;if(!i){if(n.identifier&&n.identifier===this.lastTouchIdentifier)return t.preventDefault(),!1;this.lastTouchIdentifier=n.identifier,this.updateScrollParent(e)}}return this.trackingClick=!0,this.trackingClickStart=t.timeStamp,this.targetElement=e,this.touchStartX=n.pageX,this.touchStartY=n.pageY,t.timeStamp-this.lastClickTime<this.tapDelay&&t.preventDefault(),!0},t.prototype.touchHasMoved=function(t){var e=t.changedTouches[0],n=this.touchBoundary;return Math.abs(e.pageX-this.touchStartX)>n||Math.abs(e.pageY-this.touchStartY)>n?!0:!1},t.prototype.onTouchMove=function(t){return this.trackingClick?((this.targetElement!==this.getTargetElementFromEventTarget(t.target)||this.touchHasMoved(t))&&(this.trackingClick=!1,this.targetElement=null),!0):!0},t.prototype.findControl=function(t){return void 0!==t.control?t.control:t.htmlFor?document.getElementById(t.htmlFor):t.querySelector("button, input:not([type=hidden]), keygen, meter, output, progress, select, textarea")},t.prototype.onTouchEnd=function(t){var e,a,c,s,u,l=this.targetElement;if(!this.trackingClick)return!0;if(t.timeStamp-this.lastClickTime<this.tapDelay)return this.cancelNextClick=!0,!0;if(t.timeStamp-this.trackingClickStart>this.tapTimeout)return!0;if(this.cancelNextClick=!1,this.lastClickTime=t.timeStamp,a=this.trackingClickStart,this.trackingClick=!1,this.trackingClickStart=0,r&&(u=t.changedTouches[0],l=document.elementFromPoint(u.pageX-window.pageXOffset,u.pageY-window.pageYOffset)||l,l.fastClickScrollParent=this.targetElement.fastClickScrollParent),c=l.tagName.toLowerCase(),"label"===c){if(e=this.findControl(l)){if(this.focus(l),n)return!1;l=e}}else if(this.needsFocus(l))return t.timeStamp-a>100||o&&window.top!==window&&"input"===c?(this.targetElement=null,!1):(this.focus(l),this.sendClick(l,t),o&&"select"===c||(this.targetElement=null,t.preventDefault()),!1);return o&&!i&&(s=l.fastClickScrollParent,s&&s.fastClickLastScrollTop!==s.scrollTop)?!0:(this.needsClick(l)||(t.preventDefault(),this.sendClick(l,t)),!1)},t.prototype.onTouchCancel=function(){this.trackingClick=!1,this.targetElement=null},t.prototype.onMouse=function(t){return this.targetElement?t.forwardedTouchEvent?!0:t.cancelable&&(!this.needsClick(this.targetElement)||this.cancelNextClick)?(t.stopImmediatePropagation?t.stopImmediatePropagation():t.propagationStopped=!0,t.stopPropagation(),t.preventDefault(),!1):!0:!0},t.prototype.onClick=function(t){var e;return this.trackingClick?(this.targetElement=null,this.trackingClick=!1,!0):"submit"===t.target.type&&0===t.detail?!0:(e=this.onMouse(t),e||(this.targetElement=null),e)},t.prototype.destroy=function(){var t=this.layer;n&&(t.removeEventListener("mouseover",this.onMouse,!0),t.removeEventListener("mousedown",this.onMouse,!0),t.removeEventListener("mouseup",this.onMouse,!0)),t.removeEventListener("click",this.onClick,!0),t.removeEventListener("touchstart",this.onTouchStart,!1),t.removeEventListener("touchmove",this.onTouchMove,!1),t.removeEventListener("touchend",this.onTouchEnd,!1),t.removeEventListener("touchcancel",this.onTouchCancel,!1)},t.notNeeded=function(t){var e,o,i,r;if("undefined"==typeof window.ontouchstart)return!0;if(o=+(/Chrome\/([0-9]+)/.exec(navigator.userAgent)||[,0])[1]){if(!n)return!0;if(e=document.querySelector("meta[name=viewport]")){if(-1!==e.content.indexOf("user-scalable=no"))return!0;if(o>31&&document.documentElement.scrollWidth<=window.outerWidth)return!0}}if(a&&(i=navigator.userAgent.match(/Version\/([0-9]*)\.([0-9]*)/),i[1]>=10&&i[2]>=3&&(e=document.querySelector("meta[name=viewport]")))){if(-1!==e.content.indexOf("user-scalable=no"))return!0;if(document.documentElement.scrollWidth<=window.outerWidth)return!0}return"none"===t.style.msTouchAction||"manipulation"===t.style.touchAction?!0:(r=+(/Firefox\/([0-9]+)/.exec(navigator.userAgent)||[,0])[1],r>=27&&(e=document.querySelector("meta[name=viewport]"),e&&(-1!==e.content.indexOf("user-scalable=no")||document.documentElement.scrollWidth<=window.outerWidth))?!0:"none"===t.style.touchAction||"manipulation"===t.style.touchAction?!0:!1)},t.attach=function(e,n){return new t(e,n)},"function"==typeof define&&"object"==typeof define.amd&&define.amd?define(function(){return t}):"undefined"!=typeof module&&module.exports?(module.exports=t.attach,module.exports.FastClick=t):window.FastClick=t}();   
}(jQuery));
    

/*
	*@author       Constantin Saguin - @brutaldesign
	*@link            http://bsign.co
	*@github        http://github.com/brutaldesign/swipebox
	*@version     1.1.2
	*@license      MIT License
*/


(function($){

(function(e, t, n, r) {
    n.swipebox = function(i, s) {
        var o = {
                useCSS: true,
                hideBarsDelay: 3e3
            },
            u = this,
            a = n(i),
            i = i,
            f = i.selector,
            l = n(f),
            c = t.createTouch !== r || "ontouchstart" in e || "onmsgesturechange" in e || navigator.msMaxTouchPoints,
            h = !!e.SVGSVGElement,
            p = '<div id="swipebox-overlay">					<div id="swipebox-slider"></div>					<div id="swipebox-caption"></div>					<div id="swipebox-action">						<a id="swipebox-close"></a>						<a id="swipebox-prev"></a>						<a id="swipebox-next"></a>					</div>			</div>';
        u.settings = {};
        u.init = function() {
            u.settings = n.extend({}, o, s);
            l.click(function(e) {
                e.preventDefault();
                e.stopPropagation();
                index = a.index(n(this));
                d.target = n(e.target);
                d.init(index)
            })
        };
        var d = {
            init: function(e) {
                this.target.trigger("swipebox-start");
                this.build();
                this.openSlide(e);
                this.openImg(e);
                this.preloadImg(e + 1);
                this.preloadImg(e - 1)
            },
            build: function() {
                var t = this;
                n("body").append(p);
                if (t.doCssTrans()) {
                    n("#swipebox-slider").css({
                        "-webkit-transition": "left 0.4s ease",
                        "-moz-transition": "left 0.4s ease",
                        "-o-transition": "left 0.4s ease",
                        "-khtml-transition": "left 0.4s ease",
                        transition: "left 0.4s ease"
                    });
                    n("#swipebox-overlay").css({
                        "-webkit-transition": "opacity 1s ease",
                        "-moz-transition": "opacity 1s ease",
                        "-o-transition": "opacity 1s ease",
                        "-khtml-transition": "opacity 1s ease",
                        transition: "opacity 1s ease"
                    });
                    n("#swipebox-action, #swipebox-caption").css({
                        "-webkit-transition": "0.5s",
                        "-moz-transition": "0.5s",
                        "-o-transition": "0.5s",
                        "-khtml-transition": "0.5s",
                        transition: "0.5s"
                    })
                }
                if (h) {
                    var r = n("#swipebox-action #swipebox-close").css("background-image");
                    r = r.replace("png", "svg");
                    n("#swipebox-action #swipebox-prev,#swipebox-action #swipebox-next,#swipebox-action #swipebox-close").css({
                        "background-image": r
                    })
                }
                a.each(function() {
                    n("#swipebox-slider").append('<div class="slide"></div>')
                });
                t.setDim();
                t.actions();
                t.keyboard();
                t.gesture();
                t.animBars();
                n(e).resize(function() {
                    t.setDim()
                }).resize()
            },
            setDim: function() {
                var t = {
                    width: n(e).width(),
                    height: e.innerHeight ? e.innerHeight : n(e).height()
                };
                n("#swipebox-overlay").css(t)
            },
            supportTransition: function() {
                var e = "transition WebkitTransition MozTransition OTransition msTransition KhtmlTransition".split(" ");
                for (var n = 0; n < e.length; n++) {
                    if (t.createElement("div").style[e[n]] !== r) {
                        return e[n]
                    }
                }
                return false
            },
            doCssTrans: function() {
                if (u.settings.useCSS && this.supportTransition()) {
                    return true
                }
            },
            gesture: function() {
                if (c) {
                    var e = this,
                        t = null,
                        r = 10,
                        i = {},
                        s = {};
                    var o = n("#swipebox-caption, #swipebox-action");
                    o.addClass("visible-bars");
                    e.setTimeout();
                    n("body").bind("touchstart", function(e) {
                        n(this).addClass("touching");
                        s = e.originalEvent.targetTouches[0];
                        i.pageX = e.originalEvent.targetTouches[0].pageX;
                        n(".touching").bind("touchmove", function(e) {
                            e.preventDefault();
                            e.stopPropagation();
                            s = e.originalEvent.targetTouches[0]
                        });
                        return false
                    }).bind("touchend", function(u) {
                        u.preventDefault();
                        u.stopPropagation();
                        t = s.pageX - i.pageX;
                        if (t >= r) {
                            e.getPrev()
                        } else if (t <= -r) {
                            e.getNext()
                        } else {
                            if (!o.hasClass("visible-bars")) {
                                e.showBars();
                                e.setTimeout()
                            } else {
                                e.clearTimeout();
                                e.hideBars()
                            }
                        }
                        n(".touching").off("touchmove").removeClass("touching")
                    })
                }
            },
            setTimeout: function() {
                if (u.settings.hideBarsDelay > 0) {
                    var t = this;
                    t.clearTimeout();
                    t.timeout = e.setTimeout(function() {
                        t.hideBars()
                    }, u.settings.hideBarsDelay)
                }
            },
            clearTimeout: function() {
                e.clearTimeout(this.timeout);
                this.timeout = null
            },
            showBars: function() {
                var e = n("#swipebox-caption, #swipebox-action");
                if (this.doCssTrans()) {
                    e.addClass("visible-bars")
                } else {
                    n("#swipebox-caption").animate({
                        top: 0
                    }, 500);
                    n("#swipebox-action").animate({
                        bottom: 0
                    }, 500);
                    setTimeout(function() {
                        e.addClass("visible-bars")
                    }, 1e3)
                }
            },
            hideBars: function() {
                var e = n("#swipebox-caption, #swipebox-action");
                if (this.doCssTrans()) {
                    e.removeClass("visible-bars")
                } else {
                    n("#swipebox-caption").animate({
                        top: "-50px"
                    }, 500);
                    n("#swipebox-action").animate({
                        bottom: "-50px"
                    }, 500);
                    setTimeout(function() {
                        e.removeClass("visible-bars")
                    }, 1e3)
                }
            },
            animBars: function() {
                var e = this;
                var t = n("#swipebox-caption, #swipebox-action");
                t.addClass("visible-bars");
                e.setTimeout();
                n("#swipebox-slider").click(function(n) {
                    if (!t.hasClass("visible-bars")) {
                        e.showBars();
                        e.setTimeout()
                    }
                });
                n("#swipebox-action").hover(function() {
                    e.showBars();
                    t.addClass("force-visible-bars");
                    e.clearTimeout()
                }, function() {
                    t.removeClass("force-visible-bars");
                    e.setTimeout()
                })
            },
            keyboard: function() {
                var t = this;
                n(e).bind("keyup", function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    if (e.keyCode == 37) {
                        t.getPrev()
                    } else if (e.keyCode == 39) {
                        t.getNext()
                    } else if (e.keyCode == 27) {
                        t.closeSlide()
                    }
                })
            },
            actions: function() {
                var e = this;
                if (a.length < 2) {
                    n("#swipebox-prev, #swipebox-next").hide()
                } else {
                    n("#swipebox-prev").bind("click touchend", function(t) {
                        t.preventDefault();
                        t.stopPropagation();
                        e.getPrev();
                        e.setTimeout()
                    });
                    n("#swipebox-next").bind("click touchend", function(t) {
                        t.preventDefault();
                        t.stopPropagation();
                        e.getNext();
                        e.setTimeout()
                    })
                }
                n("#swipebox-close").bind("click touchend", function(t) {
                    e.closeSlide()
                    $('.gallery-fix').delay(1).fadeOut(0);
                })
            },
            setSlide: function(e, t) {
                t = t || false;
                var r = n("#swipebox-slider");
                if (this.doCssTrans()) {
                    r.css({
                        left: -e * 100 + "%"
                    })
                } else {
                    r.animate({
                        left: -e * 100 + "%"
                    })
                }
                n("#swipebox-slider .slide").removeClass("current");
                n("#swipebox-slider .slide").eq(e).addClass("current");
                this.setTitle(e);
                if (t) {
                    r.fadeIn()
                }
                n("#swipebox-prev, #swipebox-next").removeClass("disabled");
                if (e == 0) {
                    n("#swipebox-prev").addClass("disabled")
                } else if (e == a.length - 1) {
                    n("#swipebox-next").addClass("disabled")
                }
            },
            openSlide: function(t) {
                n("html").addClass("swipebox");
                n(e).trigger("resize");
                this.setSlide(t, true)
            },
            preloadImg: function(e) {
                var t = this;
                setTimeout(function() {
                    t.openImg(e)
                }, 1e3)
            },
            openImg: function(e) {
                var t = this;
                if (e < 0 || e >= a.length) {
                    return false
                }
                t.loadImg(a.eq(e).attr("href"), function() {
                    n("#swipebox-slider .slide").eq(e).html(this)
                })
            },
            setTitle: function(e, t) {
                n("#swipebox-caption").empty();
                if (a.eq(e).attr("title")) {
                    n("#swipebox-caption").append(a.eq(e).attr("title"))
                }
            },
            loadImg: function(e, t) {
                var r = n("<img>").on("load", function() {
                    t.call(r)
                });
                r.attr("src", e)
            },
            getNext: function() {
                var e = this;
                index = n("#swipebox-slider .slide").index(n("#swipebox-slider .slide.current"));
                if (index + 1 < a.length) {
                    index++;
                    e.setSlide(index);
                    e.preloadImg(index + 1)
                } else {
                    n("#swipebox-slider").addClass("rightSpring");
                    setTimeout(function() {
                        n("#swipebox-slider").removeClass("rightSpring")
                    }, 500)
                }
            },
            getPrev: function() {
                var e = this;
                index = n("#swipebox-slider .slide").index(n("#swipebox-slider .slide.current"));
                if (index > 0) {
                    index--;
                    e.setSlide(index);
                    e.preloadImg(index - 1)
                } else {
                    n("#swipebox-slider").addClass("leftSpring");
                    setTimeout(function() {
                        n("#swipebox-slider").removeClass("leftSpring")
                    }, 500)
                }
            },
            closeSlide: function() {
                var t = this;
                n(e).trigger("resize");
                n("html").removeClass("swipebox");
                t.destroy()
            },
            destroy: function() {
                var t = this;
                n(e).unbind("keyup");
                n("body").unbind("touchstart");
                n("body").unbind("touchmove");
                n("body").unbind("touchend");
                n("#swipebox-slider").unbind();
                n("#swipebox-overlay").remove();
                a.removeData("_swipebox");
                t.target.trigger("swipebox-destroy")
            }
        };
        u.init()
    };
	

	
    n.fn.swipebox = function(e) {
        if (!n.data(this, "_swipebox")) {
            var t = new n.swipebox(this, e);
            this.data("_swipebox", t)
        }
    }
})(window, document, jQuery)

	

}(jQuery));



(function($){
    /**
     * Owl carousel
     * @version 2.0.0
     * @author Bartosz Wojciechowski
     * @license The MIT License (MIT)
     */

    !function(a,b,c,d){function e(b,c){this.settings=null,this.options=a.extend({},e.Defaults,c),this.$element=a(b),this.drag=a.extend({},m),this.state=a.extend({},n),this.e=a.extend({},o),this._plugins={},this._supress={},this._current=null,this._speed=null,this._coordinates=[],this._breakpoint=null,this._width=null,this._items=[],this._clones=[],this._mergers=[],this._invalidated={},this._pipe=[],a.each(e.Plugins,a.proxy(function(a,b){this._plugins[a[0].toLowerCase()+a.slice(1)]=new b(this)},this)),a.each(e.Pipe,a.proxy(function(b,c){this._pipe.push({filter:c.filter,run:a.proxy(c.run,this)})},this)),this.setup(),this.initialize()}function f(a){if(a.touches!==d)return{x:a.touches[0].pageX,y:a.touches[0].pageY};if(a.touches===d){if(a.pageX!==d)return{x:a.pageX,y:a.pageY};if(a.pageX===d)return{x:a.clientX,y:a.clientY}}}function g(a){var b,d,e=c.createElement("div"),f=a;for(b in f)if(d=f[b],"undefined"!=typeof e.style[d])return e=null,[d,b];return[!1]}function h(){return g(["transition","WebkitTransition","MozTransition","OTransition"])[1]}function i(){return g(["transform","WebkitTransform","MozTransform","OTransform","msTransform"])[0]}function j(){return g(["perspective","webkitPerspective","MozPerspective","OPerspective","MsPerspective"])[0]}function k(){return"ontouchstart"in b||!!navigator.msMaxTouchPoints}function l(){return b.navigator.msPointerEnabled}var m,n,o;m={start:0,startX:0,startY:0,current:0,currentX:0,currentY:0,offsetX:0,offsetY:0,distance:null,startTime:0,endTime:0,updatedX:0,targetEl:null},n={isTouch:!1,isScrolling:!1,isSwiping:!1,direction:!1,inMotion:!1},o={_onDragStart:null,_onDragMove:null,_onDragEnd:null,_transitionEnd:null,_resizer:null,_responsiveCall:null,_goToLoop:null,_checkVisibile:null},e.Defaults={items:3,loop:!1,center:!1,mouseDrag:!0,touchDrag:!0,pullDrag:!0,freeDrag:!1,margin:0,stagePadding:0,merge:!1,mergeFit:!0,autoWidth:!1,startPosition:0,rtl:!1,smartSpeed:250,fluidSpeed:!1,dragEndSpeed:!1,responsive:{},responsiveRefreshRate:200,responsiveBaseElement:b,responsiveClass:!1,fallbackEasing:"swing",info:!1,nestedItemSelector:!1,itemElement:"div",stageElement:"div",themeClass:"owl-theme",baseClass:"owl-carousel",itemClass:"owl-item",centerClass:"center",activeClass:"active"},e.Width={Default:"default",Inner:"inner",Outer:"outer"},e.Plugins={},e.Pipe=[{filter:["width","items","settings"],run:function(a){a.current=this._items&&this._items[this.relative(this._current)]}},{filter:["items","settings"],run:function(){var a=this._clones,b=this.$stage.children(".cloned");(b.length!==a.length||!this.settings.loop&&a.length>0)&&(this.$stage.children(".cloned").remove(),this._clones=[])}},{filter:["items","settings"],run:function(){var a,b,c=this._clones,d=this._items,e=this.settings.loop?c.length-Math.max(2*this.settings.items,4):0;for(a=0,b=Math.abs(e/2);b>a;a++)e>0?(this.$stage.children().eq(d.length+c.length-1).remove(),c.pop(),this.$stage.children().eq(0).remove(),c.pop()):(c.push(c.length/2),this.$stage.append(d[c[c.length-1]].clone().addClass("cloned")),c.push(d.length-1-(c.length-1)/2),this.$stage.prepend(d[c[c.length-1]].clone().addClass("cloned")))}},{filter:["width","items","settings"],run:function(){var a,b,c,d=this.settings.rtl?1:-1,e=(this.width()/this.settings.items).toFixed(3),f=0;for(this._coordinates=[],b=0,c=this._clones.length+this._items.length;c>b;b++)a=this._mergers[this.relative(b)],a=this.settings.mergeFit&&Math.min(a,this.settings.items)||a,f+=(this.settings.autoWidth?this._items[this.relative(b)].width()+this.settings.margin:e*a)*d,this._coordinates.push(f)}},{filter:["width","items","settings"],run:function(){var b,c,d=(this.width()/this.settings.items).toFixed(3),e={width:Math.abs(this._coordinates[this._coordinates.length-1])+2*this.settings.stagePadding,"padding-left":this.settings.stagePadding||"","padding-right":this.settings.stagePadding||""};if(this.$stage.css(e),e={width:this.settings.autoWidth?"auto":d-this.settings.margin},e[this.settings.rtl?"margin-left":"margin-right"]=this.settings.margin,!this.settings.autoWidth&&a.grep(this._mergers,function(a){return a>1}).length>0)for(b=0,c=this._coordinates.length;c>b;b++)e.width=Math.abs(this._coordinates[b])-Math.abs(this._coordinates[b-1]||0)-this.settings.margin,this.$stage.children().eq(b).css(e);else this.$stage.children().css(e)}},{filter:["width","items","settings"],run:function(a){a.current&&this.reset(this.$stage.children().index(a.current))}},{filter:["position"],run:function(){this.animate(this.coordinates(this._current))}},{filter:["width","position","items","settings"],run:function(){var a,b,c,d,e=this.settings.rtl?1:-1,f=2*this.settings.stagePadding,g=this.coordinates(this.current())+f,h=g+this.width()*e,i=[];for(c=0,d=this._coordinates.length;d>c;c++)a=this._coordinates[c-1]||0,b=Math.abs(this._coordinates[c])+f*e,(this.op(a,"<=",g)&&this.op(a,">",h)||this.op(b,"<",g)&&this.op(b,">",h))&&i.push(c);this.$stage.children("."+this.settings.activeClass).removeClass(this.settings.activeClass),this.$stage.children(":eq("+i.join("), :eq(")+")").addClass(this.settings.activeClass),this.settings.center&&(this.$stage.children("."+this.settings.centerClass).removeClass(this.settings.centerClass),this.$stage.children().eq(this.current()).addClass(this.settings.centerClass))}}],e.prototype.initialize=function(){if(this.trigger("initialize"),this.$element.addClass(this.settings.baseClass).addClass(this.settings.themeClass).toggleClass("owl-rtl",this.settings.rtl),this.browserSupport(),this.settings.autoWidth&&this.state.imagesLoaded!==!0){var b,c,e;if(b=this.$element.find("img"),c=this.settings.nestedItemSelector?"."+this.settings.nestedItemSelector:d,e=this.$element.children(c).width(),b.length&&0>=e)return this.preloadAutoWidthImages(b),!1}this.$element.addClass("owl-loading"),this.$stage=a("<"+this.settings.stageElement+' class="owl-stage"/>').wrap('<div class="owl-stage-outer">'),this.$element.append(this.$stage.parent()),this.replace(this.$element.children().not(this.$stage.parent())),this._width=this.$element.width(),this.refresh(),this.$element.removeClass("owl-loading").addClass("owl-loaded"),this.eventsCall(),this.internalEvents(),this.addTriggerableEvents(),this.trigger("initialized")},e.prototype.setup=function(){var b=this.viewport(),c=this.options.responsive,d=-1,e=null;c?(a.each(c,function(a){b>=a&&a>d&&(d=Number(a))}),e=a.extend({},this.options,c[d]),delete e.responsive,e.responsiveClass&&this.$element.attr("class",function(a,b){return b.replace(/\b owl-responsive-\S+/g,"")}).addClass("owl-responsive-"+d)):e=a.extend({},this.options),(null===this.settings||this._breakpoint!==d)&&(this.trigger("change",{property:{name:"settings",value:e}}),this._breakpoint=d,this.settings=e,this.invalidate("settings"),this.trigger("changed",{property:{name:"settings",value:this.settings}}))},e.prototype.optionsLogic=function(){this.$element.toggleClass("owl-center",this.settings.center),this.settings.loop&&this._items.length<this.settings.items&&(this.settings.loop=!1),this.settings.autoWidth&&(this.settings.stagePadding=!1,this.settings.merge=!1)},e.prototype.prepare=function(b){var c=this.trigger("prepare",{content:b});return c.data||(c.data=a("<"+this.settings.itemElement+"/>").addClass(this.settings.itemClass).append(b)),this.trigger("prepared",{content:c.data}),c.data},e.prototype.update=function(){for(var b=0,c=this._pipe.length,d=a.proxy(function(a){return this[a]},this._invalidated),e={};c>b;)(this._invalidated.all||a.grep(this._pipe[b].filter,d).length>0)&&this._pipe[b].run(e),b++;this._invalidated={}},e.prototype.width=function(a){switch(a=a||e.Width.Default){case e.Width.Inner:case e.Width.Outer:return this._width;default:return this._width-2*this.settings.stagePadding+this.settings.margin}},e.prototype.refresh=function(){if(0===this._items.length)return!1;(new Date).getTime();this.trigger("refresh"),this.setup(),this.optionsLogic(),this.$stage.addClass("owl-refresh"),this.update(),this.$stage.removeClass("owl-refresh"),this.state.orientation=b.orientation,this.watchVisibility(),this.trigger("refreshed")},e.prototype.eventsCall=function(){this.e._onDragStart=a.proxy(function(a){this.onDragStart(a)},this),this.e._onDragMove=a.proxy(function(a){this.onDragMove(a)},this),this.e._onDragEnd=a.proxy(function(a){this.onDragEnd(a)},this),this.e._onResize=a.proxy(function(a){this.onResize(a)},this),this.e._transitionEnd=a.proxy(function(a){this.transitionEnd(a)},this),this.e._preventClick=a.proxy(function(a){this.preventClick(a)},this)},e.prototype.onThrottledResize=function(){b.clearTimeout(this.resizeTimer),this.resizeTimer=b.setTimeout(this.e._onResize,this.settings.responsiveRefreshRate)},e.prototype.onResize=function(){return this._items.length?this._width===this.$element.width()?!1:this.trigger("resize").isDefaultPrevented()?!1:(this._width=this.$element.width(),this.invalidate("width"),this.refresh(),void this.trigger("resized")):!1},e.prototype.eventsRouter=function(a){var b=a.type;"mousedown"===b||"touchstart"===b?this.onDragStart(a):"mousemove"===b||"touchmove"===b?this.onDragMove(a):"mouseup"===b||"touchend"===b?this.onDragEnd(a):"touchcancel"===b&&this.onDragEnd(a)},e.prototype.internalEvents=function(){var c=(k(),l());this.settings.mouseDrag?(this.$stage.on("mousedown",a.proxy(function(a){this.eventsRouter(a)},this)),this.$stage.on("dragstart",function(){return!1}),this.$stage.get(0).onselectstart=function(){return!1}):this.$element.addClass("owl-text-select-on"),this.settings.touchDrag&&!c&&this.$stage.on("touchstart touchcancel",a.proxy(function(a){this.eventsRouter(a)},this)),this.transitionEndVendor&&this.on(this.$stage.get(0),this.transitionEndVendor,this.e._transitionEnd,!1),this.settings.responsive!==!1&&this.on(b,"resize",a.proxy(this.onThrottledResize,this))},e.prototype.onDragStart=function(d){var e,g,h,i;if(e=d.originalEvent||d||b.event,3===e.which||this.state.isTouch)return!1;if("mousedown"===e.type&&this.$stage.addClass("owl-grab"),this.trigger("drag"),this.drag.startTime=(new Date).getTime(),this.speed(0),this.state.isTouch=!0,this.state.isScrolling=!1,this.state.isSwiping=!1,this.drag.distance=0,g=f(e).x,h=f(e).y,this.drag.offsetX=this.$stage.position().left,this.drag.offsetY=this.$stage.position().top,this.settings.rtl&&(this.drag.offsetX=this.$stage.position().left+this.$stage.width()-this.width()+this.settings.margin),this.state.inMotion&&this.support3d)i=this.getTransformProperty(),this.drag.offsetX=i,this.animate(i),this.state.inMotion=!0;else if(this.state.inMotion&&!this.support3d)return this.state.inMotion=!1,!1;this.drag.startX=g-this.drag.offsetX,this.drag.startY=h-this.drag.offsetY,this.drag.start=g-this.drag.startX,this.drag.targetEl=e.target||e.srcElement,this.drag.updatedX=this.drag.start,("IMG"===this.drag.targetEl.tagName||"A"===this.drag.targetEl.tagName)&&(this.drag.targetEl.draggable=!1),a(c).on("mousemove.owl.dragEvents mouseup.owl.dragEvents touchmove.owl.dragEvents touchend.owl.dragEvents",a.proxy(function(a){this.eventsRouter(a)},this))},e.prototype.onDragMove=function(a){var c,e,g,h,i,j;this.state.isTouch&&(this.state.isScrolling||(c=a.originalEvent||a||b.event,e=f(c).x,g=f(c).y,this.drag.currentX=e-this.drag.startX,this.drag.currentY=g-this.drag.startY,this.drag.distance=this.drag.currentX-this.drag.offsetX,this.drag.distance<0?this.state.direction=this.settings.rtl?"right":"left":this.drag.distance>0&&(this.state.direction=this.settings.rtl?"left":"right"),this.settings.loop?this.op(this.drag.currentX,">",this.coordinates(this.minimum()))&&"right"===this.state.direction?this.drag.currentX-=(this.settings.center&&this.coordinates(0))-this.coordinates(this._items.length):this.op(this.drag.currentX,"<",this.coordinates(this.maximum()))&&"left"===this.state.direction&&(this.drag.currentX+=(this.settings.center&&this.coordinates(0))-this.coordinates(this._items.length)):(h=this.coordinates(this.settings.rtl?this.maximum():this.minimum()),i=this.coordinates(this.settings.rtl?this.minimum():this.maximum()),j=this.settings.pullDrag?this.drag.distance/5:0,this.drag.currentX=Math.max(Math.min(this.drag.currentX,h+j),i+j)),(this.drag.distance>8||this.drag.distance<-8)&&(c.preventDefault!==d?c.preventDefault():c.returnValue=!1,this.state.isSwiping=!0),this.drag.updatedX=this.drag.currentX,(this.drag.currentY>16||this.drag.currentY<-16)&&this.state.isSwiping===!1&&(this.state.isScrolling=!0,this.drag.updatedX=this.drag.start),this.animate(this.drag.updatedX)))},e.prototype.onDragEnd=function(b){var d,e,f;if(this.state.isTouch){if("mouseup"===b.type&&this.$stage.removeClass("owl-grab"),this.trigger("dragged"),this.drag.targetEl.removeAttribute("draggable"),this.state.isTouch=!1,this.state.isScrolling=!1,this.state.isSwiping=!1,0===this.drag.distance&&this.state.inMotion!==!0)return this.state.inMotion=!1,!1;this.drag.endTime=(new Date).getTime(),d=this.drag.endTime-this.drag.startTime,e=Math.abs(this.drag.distance),(e>3||d>300)&&this.removeClick(this.drag.targetEl),f=this.closest(this.drag.updatedX),this.speed(this.settings.dragEndSpeed||this.settings.smartSpeed),this.current(f),this.invalidate("position"),this.update(),this.settings.pullDrag||this.drag.updatedX!==this.coordinates(f)||this.transitionEnd(),this.drag.distance=0,a(c).off(".owl.dragEvents")}},e.prototype.removeClick=function(c){this.drag.targetEl=c,a(c).on("click.preventClick",this.e._preventClick),b.setTimeout(function(){a(c).off("click.preventClick")},300)},e.prototype.preventClick=function(b){b.preventDefault?b.preventDefault():b.returnValue=!1,b.stopPropagation&&b.stopPropagation(),a(b.target).off("click.preventClick")},e.prototype.getTransformProperty=function(){var a,c;return a=b.getComputedStyle(this.$stage.get(0),null).getPropertyValue(this.vendorName+"transform"),a=a.replace(/matrix(3d)?\(|\)/g,"").split(","),c=16===a.length,c!==!0?a[4]:a[12]},e.prototype.closest=function(b){var c=-1,d=30,e=this.width(),f=this.coordinates();return this.settings.freeDrag||a.each(f,a.proxy(function(a,g){return b>g-d&&g+d>b?c=a:this.op(b,"<",g)&&this.op(b,">",f[a+1]||g-e)&&(c="left"===this.state.direction?a+1:a),-1===c},this)),this.settings.loop||(this.op(b,">",f[this.minimum()])?c=b=this.minimum():this.op(b,"<",f[this.maximum()])&&(c=b=this.maximum())),c},e.prototype.animate=function(b){this.trigger("translate"),this.state.inMotion=this.speed()>0,this.support3d?this.$stage.css({transform:"translate3d("+b+"px,0px, 0px)",transition:this.speed()/1e3+"s"}):this.state.isTouch?this.$stage.css({left:b+"px"}):this.$stage.animate({left:b},this.speed()/1e3,this.settings.fallbackEasing,a.proxy(function(){this.state.inMotion&&this.transitionEnd()},this))},e.prototype.current=function(a){if(a===d)return this._current;if(0===this._items.length)return d;if(a=this.normalize(a),this._current!==a){var b=this.trigger("change",{property:{name:"position",value:a}});b.data!==d&&(a=this.normalize(b.data)),this._current=a,this.invalidate("position"),this.trigger("changed",{property:{name:"position",value:this._current}})}return this._current},e.prototype.invalidate=function(a){this._invalidated[a]=!0},e.prototype.reset=function(a){a=this.normalize(a),a!==d&&(this._speed=0,this._current=a,this.suppress(["translate","translated"]),this.animate(this.coordinates(a)),this.release(["translate","translated"]))},e.prototype.normalize=function(b,c){var e=c?this._items.length:this._items.length+this._clones.length;return!a.isNumeric(b)||1>e?d:b=this._clones.length?(b%e+e)%e:Math.max(this.minimum(c),Math.min(this.maximum(c),b))},e.prototype.relative=function(a){return a=this.normalize(a),a-=this._clones.length/2,this.normalize(a,!0)},e.prototype.maximum=function(a){var b,c,d,e=0,f=this.settings;if(a)return this._items.length-1;if(!f.loop&&f.center)b=this._items.length-1;else if(f.loop||f.center)if(f.loop||f.center)b=this._items.length+f.items;else{if(!f.autoWidth&&!f.merge)throw"Can not detect maximum absolute position.";for(revert=f.rtl?1:-1,c=this.$stage.width()-this.$element.width();(d=this.coordinates(e))&&!(d*revert>=c);)b=++e}else b=this._items.length-f.items;return b},e.prototype.minimum=function(a){return a?0:this._clones.length/2},e.prototype.items=function(a){return a===d?this._items.slice():(a=this.normalize(a,!0),this._items[a])},e.prototype.mergers=function(a){return a===d?this._mergers.slice():(a=this.normalize(a,!0),this._mergers[a])},e.prototype.clones=function(b){var c=this._clones.length/2,e=c+this._items.length,f=function(a){return a%2===0?e+a/2:c-(a+1)/2};return b===d?a.map(this._clones,function(a,b){return f(b)}):a.map(this._clones,function(a,c){return a===b?f(c):null})},e.prototype.speed=function(a){return a!==d&&(this._speed=a),this._speed},e.prototype.coordinates=function(b){var c=null;return b===d?a.map(this._coordinates,a.proxy(function(a,b){return this.coordinates(b)},this)):(this.settings.center?(c=this._coordinates[b],c+=(this.width()-c+(this._coordinates[b-1]||0))/2*(this.settings.rtl?-1:1)):c=this._coordinates[b-1]||0,c)},e.prototype.duration=function(a,b,c){return Math.min(Math.max(Math.abs(b-a),1),6)*Math.abs(c||this.settings.smartSpeed)},e.prototype.to=function(c,d){if(this.settings.loop){var e=c-this.relative(this.current()),f=this.current(),g=this.current(),h=this.current()+e,i=0>g-h?!0:!1,j=this._clones.length+this._items.length;h<this.settings.items&&i===!1?(f=g+this._items.length,this.reset(f)):h>=j-this.settings.items&&i===!0&&(f=g-this._items.length,this.reset(f)),b.clearTimeout(this.e._goToLoop),this.e._goToLoop=b.setTimeout(a.proxy(function(){this.speed(this.duration(this.current(),f+e,d)),this.current(f+e),this.update()},this),30)}else this.speed(this.duration(this.current(),c,d)),this.current(c),this.update()},e.prototype.next=function(a){a=a||!1,this.to(this.relative(this.current())+1,a)},e.prototype.prev=function(a){a=a||!1,this.to(this.relative(this.current())-1,a)},e.prototype.transitionEnd=function(a){return a!==d&&(a.stopPropagation(),(a.target||a.srcElement||a.originalTarget)!==this.$stage.get(0))?!1:(this.state.inMotion=!1,void this.trigger("translated"))},e.prototype.viewport=function(){var d;if(this.options.responsiveBaseElement!==b)d=a(this.options.responsiveBaseElement).width();else if(b.innerWidth)d=b.innerWidth;else{if(!c.documentElement||!c.documentElement.clientWidth)throw"Can not detect viewport width.";d=c.documentElement.clientWidth}return d},e.prototype.replace=function(b){this.$stage.empty(),this._items=[],b&&(b=b instanceof jQuery?b:a(b)),this.settings.nestedItemSelector&&(b=b.find("."+this.settings.nestedItemSelector)),b.filter(function(){return 1===this.nodeType}).each(a.proxy(function(a,b){b=this.prepare(b),this.$stage.append(b),this._items.push(b),this._mergers.push(1*b.find("[data-merge]").andSelf("[data-merge]").attr("data-merge")||1)},this)),this.reset(a.isNumeric(this.settings.startPosition)?this.settings.startPosition:0),this.invalidate("items")},e.prototype.add=function(a,b){b=b===d?this._items.length:this.normalize(b,!0),this.trigger("add",{content:a,position:b}),0===this._items.length||b===this._items.length?(this.$stage.append(a),this._items.push(a),this._mergers.push(1*a.find("[data-merge]").andSelf("[data-merge]").attr("data-merge")||1)):(this._items[b].before(a),this._items.splice(b,0,a),this._mergers.splice(b,0,1*a.find("[data-merge]").andSelf("[data-merge]").attr("data-merge")||1)),this.invalidate("items"),this.trigger("added",{content:a,position:b})},e.prototype.remove=function(a){a=this.normalize(a,!0),a!==d&&(this.trigger("remove",{content:this._items[a],position:a}),this._items[a].remove(),this._items.splice(a,1),this._mergers.splice(a,1),this.invalidate("items"),this.trigger("removed",{content:null,position:a}))},e.prototype.addTriggerableEvents=function(){var b=a.proxy(function(b,c){return a.proxy(function(a){a.relatedTarget!==this&&(this.suppress([c]),b.apply(this,[].slice.call(arguments,1)),this.release([c]))},this)},this);a.each({next:this.next,prev:this.prev,to:this.to,destroy:this.destroy,refresh:this.refresh,replace:this.replace,add:this.add,remove:this.remove},a.proxy(function(a,c){this.$element.on(a+".owl.carousel",b(c,a+".owl.carousel"))},this))},e.prototype.watchVisibility=function(){function c(a){return a.offsetWidth>0&&a.offsetHeight>0}function d(){c(this.$element.get(0))&&(this.$element.removeClass("owl-hidden"),this.refresh(),b.clearInterval(this.e._checkVisibile))}c(this.$element.get(0))||(this.$element.addClass("owl-hidden"),b.clearInterval(this.e._checkVisibile),this.e._checkVisibile=b.setInterval(a.proxy(d,this),500))},e.prototype.preloadAutoWidthImages=function(b){var c,d,e,f;c=0,d=this,b.each(function(g,h){e=a(h),f=new Image,f.onload=function(){c++,e.attr("src",f.src),e.css("opacity",1),c>=b.length&&(d.state.imagesLoaded=!0,d.initialize())},f.src=e.attr("src")||e.attr("data-src")||e.attr("data-src-retina")})},e.prototype.destroy=function(){this.$element.hasClass(this.settings.themeClass)&&this.$element.removeClass(this.settings.themeClass),this.settings.responsive!==!1&&a(b).off("resize.owl.carousel"),this.transitionEndVendor&&this.off(this.$stage.get(0),this.transitionEndVendor,this.e._transitionEnd);for(var d in this._plugins)this._plugins[d].destroy();(this.settings.mouseDrag||this.settings.touchDrag)&&(this.$stage.off("mousedown touchstart touchcancel"),a(c).off(".owl.dragEvents"),this.$stage.get(0).onselectstart=function(){},this.$stage.off("dragstart",function(){return!1})),this.$element.off(".owl"),this.$stage.children(".cloned").remove(),this.e=null,this.$element.removeData("owlCarousel"),this.$stage.children().contents().unwrap(),this.$stage.children().unwrap(),this.$stage.unwrap()},e.prototype.op=function(a,b,c){var d=this.settings.rtl;switch(b){case"<":return d?a>c:c>a;case">":return d?c>a:a>c;case">=":return d?c>=a:a>=c;case"<=":return d?a>=c:c>=a}},e.prototype.on=function(a,b,c,d){a.addEventListener?a.addEventListener(b,c,d):a.attachEvent&&a.attachEvent("on"+b,c)},e.prototype.off=function(a,b,c,d){a.removeEventListener?a.removeEventListener(b,c,d):a.detachEvent&&a.detachEvent("on"+b,c)},e.prototype.trigger=function(b,c,d){var e={item:{count:this._items.length,index:this.current()}},f=a.camelCase(a.grep(["on",b,d],function(a){return a}).join("-").toLowerCase()),g=a.Event([b,"owl",d||"carousel"].join(".").toLowerCase(),a.extend({relatedTarget:this},e,c));return this._supress[b]||(a.each(this._plugins,function(a,b){b.onTrigger&&b.onTrigger(g)}),this.$element.trigger(g),this.settings&&"function"==typeof this.settings[f]&&this.settings[f].apply(this,g)),g},e.prototype.suppress=function(b){a.each(b,a.proxy(function(a,b){this._supress[b]=!0},this))},e.prototype.release=function(b){a.each(b,a.proxy(function(a,b){delete this._supress[b]},this))},e.prototype.browserSupport=function(){if(this.support3d=j(),this.support3d){this.transformVendor=i();var a=["transitionend","webkitTransitionEnd","transitionend","oTransitionEnd"];this.transitionEndVendor=a[h()],this.vendorName=this.transformVendor.replace(/Transform/i,""),this.vendorName=""!==this.vendorName?"-"+this.vendorName.toLowerCase()+"-":""}this.state.orientation=b.orientation},a.fn.owlCarousel=function(b){return this.each(function(){a(this).data("owlCarousel")||a(this).data("owlCarousel",new e(this,b))})},a.fn.owlCarousel.Constructor=e}(window.Zepto||window.jQuery,window,document),function(a,b){var c=function(b){this._core=b,this._loaded=[],this._handlers={"initialized.owl.carousel change.owl.carousel":a.proxy(function(b){if(b.namespace&&this._core.settings&&this._core.settings.lazyLoad&&(b.property&&"position"==b.property.name||"initialized"==b.type))for(var c=this._core.settings,d=c.center&&Math.ceil(c.items/2)||c.items,e=c.center&&-1*d||0,f=(b.property&&b.property.value||this._core.current())+e,g=this._core.clones().length,h=a.proxy(function(a,b){this.load(b)},this);e++<d;)this.load(g/2+this._core.relative(f)),g&&a.each(this._core.clones(this._core.relative(f++)),h)},this)},this._core.options=a.extend({},c.Defaults,this._core.options),this._core.$element.on(this._handlers)};c.Defaults={lazyLoad:!1},c.prototype.load=function(c){var d=this._core.$stage.children().eq(c),e=d&&d.find(".owl-lazy");!e||a.inArray(d.get(0),this._loaded)>-1||(e.each(a.proxy(function(c,d){var e,f=a(d),g=b.devicePixelRatio>1&&f.attr("data-src-retina")||f.attr("data-src");this._core.trigger("load",{element:f,url:g},"lazy"),f.is("img")?f.one("load.owl.lazy",a.proxy(function(){f.css("opacity",1),this._core.trigger("loaded",{element:f,url:g},"lazy")},this)).attr("src",g):(e=new Image,e.onload=a.proxy(function(){f.css({"background-image":"url("+g+")",opacity:"1"}),this._core.trigger("loaded",{element:f,url:g},"lazy")},this),e.src=g)},this)),this._loaded.push(d.get(0)))},c.prototype.destroy=function(){var a,b;for(a in this.handlers)this._core.$element.off(a,this.handlers[a]);for(b in Object.getOwnPropertyNames(this))"function"!=typeof this[b]&&(this[b]=null)},a.fn.owlCarousel.Constructor.Plugins.Lazy=c}(window.Zepto||window.jQuery,window,document),function(a){var b=function(c){this._core=c,this._handlers={"initialized.owl.carousel":a.proxy(function(){this._core.settings.autoHeight&&this.update()},this),"changed.owl.carousel":a.proxy(function(a){this._core.settings.autoHeight&&"position"==a.property.name&&this.update()},this),"loaded.owl.lazy":a.proxy(function(a){this._core.settings.autoHeight&&a.element.closest("."+this._core.settings.itemClass)===this._core.$stage.children().eq(this._core.current())&&this.update()},this)},this._core.options=a.extend({},b.Defaults,this._core.options),this._core.$element.on(this._handlers)};b.Defaults={autoHeight:!1,autoHeightClass:"owl-height"},b.prototype.update=function(){this._core.$stage.parent().height(this._core.$stage.children().eq(this._core.current()).height()).addClass(this._core.settings.autoHeightClass)},b.prototype.destroy=function(){var a,b;for(a in this._handlers)this._core.$element.off(a,this._handlers[a]);for(b in Object.getOwnPropertyNames(this))"function"!=typeof this[b]&&(this[b]=null)},a.fn.owlCarousel.Constructor.Plugins.AutoHeight=b}(window.Zepto||window.jQuery,window,document),function(a,b,c){var d=function(b){this._core=b,this._videos={},this._playing=null,this._fullscreen=!1,this._handlers={"resize.owl.carousel":a.proxy(function(a){this._core.settings.video&&!this.isInFullScreen()&&a.preventDefault()},this),"refresh.owl.carousel changed.owl.carousel":a.proxy(function(){this._playing&&this.stop()},this),"prepared.owl.carousel":a.proxy(function(b){var c=a(b.content).find(".owl-video");c.length&&(c.css("display","none"),this.fetch(c,a(b.content)))},this)},this._core.options=a.extend({},d.Defaults,this._core.options),this._core.$element.on(this._handlers),this._core.$element.on("click.owl.video",".owl-video-play-icon",a.proxy(function(a){this.play(a)},this))};d.Defaults={video:!1,videoHeight:!1,videoWidth:!1},d.prototype.fetch=function(a,b){var c=a.attr("data-vimeo-id")?"vimeo":"youtube",d=a.attr("data-vimeo-id")||a.attr("data-youtube-id"),e=a.attr("data-width")||this._core.settings.videoWidth,f=a.attr("data-height")||this._core.settings.videoHeight,g=a.attr("href");if(!g)throw new Error("Missing video URL.");if(d=g.match(/(http:|https:|)\/\/(player.|www.)?(vimeo\.com|youtu(be\.com|\.be|be\.googleapis\.com))\/(video\/|embed\/|watch\?v=|v\/)?([A-Za-z0-9._%-]*)(\&\S+)?/),d[3].indexOf("youtu")>-1)c="youtube";else{if(!(d[3].indexOf("vimeo")>-1))throw new Error("Video URL not supported.");c="vimeo"}d=d[6],this._videos[g]={type:c,id:d,width:e,height:f},b.attr("data-video",g),this.thumbnail(a,this._videos[g])},d.prototype.thumbnail=function(b,c){var d,e,f,g=c.width&&c.height?'style="width:'+c.width+"px;height:"+c.height+'px;"':"",h=b.find("img"),i="src",j="",k=this._core.settings,l=function(a){e='<div class="owl-video-play-icon"></div>',d=k.lazyLoad?'<div class="owl-video-tn '+j+'" '+i+'="'+a+'"></div>':'<div class="owl-video-tn" style="opacity:1;background-image:url('+a+')"></div>',b.after(d),b.after(e)};return b.wrap('<div class="owl-video-wrapper"'+g+"></div>"),this._core.settings.lazyLoad&&(i="data-src",j="owl-lazy"),h.length?(l(h.attr(i)),h.remove(),!1):void("youtube"===c.type?(f="http://img.youtube.com/vi/"+c.id+"/hqdefault.jpg",l(f)):"vimeo"===c.type&&a.ajax({type:"GET",url:"http://vimeo.com/api/v2/video/"+c.id+".json",jsonp:"callback",dataType:"jsonp",success:function(a){f=a[0].thumbnail_large,l(f)}}))},d.prototype.stop=function(){this._core.trigger("stop",null,"video"),this._playing.find(".owl-video-frame").remove(),this._playing.removeClass("owl-video-playing"),this._playing=null},d.prototype.play=function(b){this._core.trigger("play",null,"video"),this._playing&&this.stop();var c,d,e=a(b.target||b.srcElement),f=e.closest("."+this._core.settings.itemClass),g=this._videos[f.attr("data-video")],h=g.width||"100%",i=g.height||this._core.$stage.height();"youtube"===g.type?c='<iframe width="'+h+'" height="'+i+'" src="http://www.youtube.com/embed/'+g.id+"?autoplay=1&v="+g.id+'" frameborder="0" allowfullscreen></iframe>':"vimeo"===g.type&&(c='<iframe src="http://player.vimeo.com/video/'+g.id+'?autoplay=1" width="'+h+'" height="'+i+'" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>'),f.addClass("owl-video-playing"),this._playing=f,d=a('<div style="height:'+i+"px; width:"+h+'px" class="owl-video-frame">'+c+"</div>"),e.after(d)},d.prototype.isInFullScreen=function(){var d=c.fullscreenElement||c.mozFullScreenElement||c.webkitFullscreenElement;return d&&a(d).parent().hasClass("owl-video-frame")&&(this._core.speed(0),this._fullscreen=!0),d&&this._fullscreen&&this._playing?!1:this._fullscreen?(this._fullscreen=!1,!1):this._playing&&this._core.state.orientation!==b.orientation?(this._core.state.orientation=b.orientation,!1):!0},d.prototype.destroy=function(){var a,b;this._core.$element.off("click.owl.video");for(a in this._handlers)this._core.$element.off(a,this._handlers[a]);for(b in Object.getOwnPropertyNames(this))"function"!=typeof this[b]&&(this[b]=null)},a.fn.owlCarousel.Constructor.Plugins.Video=d}(window.Zepto||window.jQuery,window,document),function(a,b,c,d){var e=function(b){this.core=b,this.core.options=a.extend({},e.Defaults,this.core.options),this.swapping=!0,this.previous=d,this.next=d,this.handlers={"change.owl.carousel":a.proxy(function(a){"position"==a.property.name&&(this.previous=this.core.current(),this.next=a.property.value)},this),"drag.owl.carousel dragged.owl.carousel translated.owl.carousel":a.proxy(function(a){this.swapping="translated"==a.type},this),"translate.owl.carousel":a.proxy(function(){this.swapping&&(this.core.options.animateOut||this.core.options.animateIn)&&this.swap()},this)},this.core.$element.on(this.handlers)};e.Defaults={animateOut:!1,animateIn:!1},e.prototype.swap=function(){if(1===this.core.settings.items&&this.core.support3d){this.core.speed(0);var b,c=a.proxy(this.clear,this),d=this.core.$stage.children().eq(this.previous),e=this.core.$stage.children().eq(this.next),f=this.core.settings.animateIn,g=this.core.settings.animateOut;this.core.current()!==this.previous&&(g&&(b=this.core.coordinates(this.previous)-this.core.coordinates(this.next),d.css({left:b+"px"}).addClass("animated owl-animated-out").addClass(g).one("webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend",c)),f&&e.addClass("animated owl-animated-in").addClass(f).one("webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend",c))}},e.prototype.clear=function(b){a(b.target).css({left:""}).removeClass("animated owl-animated-out owl-animated-in").removeClass(this.core.settings.animateIn).removeClass(this.core.settings.animateOut),this.core.transitionEnd()},e.prototype.destroy=function(){var a,b;for(a in this.handlers)this.core.$element.off(a,this.handlers[a]);for(b in Object.getOwnPropertyNames(this))"function"!=typeof this[b]&&(this[b]=null)},a.fn.owlCarousel.Constructor.Plugins.Animate=e}(window.Zepto||window.jQuery,window,document),function(a,b,c){var d=function(b){this.core=b,this.core.options=a.extend({},d.Defaults,this.core.options),this.handlers={"translated.owl.carousel refreshed.owl.carousel":a.proxy(function(){this.autoplay()
    },this),"play.owl.autoplay":a.proxy(function(a,b,c){this.play(b,c)},this),"stop.owl.autoplay":a.proxy(function(){this.stop()},this),"mouseover.owl.autoplay":a.proxy(function(){this.core.settings.autoplayHoverPause&&this.pause()},this),"mouseleave.owl.autoplay":a.proxy(function(){this.core.settings.autoplayHoverPause&&this.autoplay()},this)},this.core.$element.on(this.handlers)};d.Defaults={autoplay:!1,autoplayTimeout:5e3,autoplayHoverPause:!1,autoplaySpeed:!1},d.prototype.autoplay=function(){this.core.settings.autoplay&&!this.core.state.videoPlay?(b.clearInterval(this.interval),this.interval=b.setInterval(a.proxy(function(){this.play()},this),this.core.settings.autoplayTimeout)):b.clearInterval(this.interval)},d.prototype.play=function(){return c.hidden===!0||this.core.state.isTouch||this.core.state.isScrolling||this.core.state.isSwiping||this.core.state.inMotion?void 0:this.core.settings.autoplay===!1?void b.clearInterval(this.interval):void this.core.next(this.core.settings.autoplaySpeed)},d.prototype.stop=function(){b.clearInterval(this.interval)},d.prototype.pause=function(){b.clearInterval(this.interval)},d.prototype.destroy=function(){var a,c;b.clearInterval(this.interval);for(a in this.handlers)this.core.$element.off(a,this.handlers[a]);for(c in Object.getOwnPropertyNames(this))"function"!=typeof this[c]&&(this[c]=null)},a.fn.owlCarousel.Constructor.Plugins.autoplay=d}(window.Zepto||window.jQuery,window,document),function(a){"use strict";var b=function(c){this._core=c,this._initialized=!1,this._pages=[],this._controls={},this._templates=[],this.$element=this._core.$element,this._overrides={next:this._core.next,prev:this._core.prev,to:this._core.to},this._handlers={"prepared.owl.carousel":a.proxy(function(b){this._core.settings.dotsData&&this._templates.push(a(b.content).find("[data-dot]").andSelf("[data-dot]").attr("data-dot"))},this),"add.owl.carousel":a.proxy(function(b){this._core.settings.dotsData&&this._templates.splice(b.position,0,a(b.content).find("[data-dot]").andSelf("[data-dot]").attr("data-dot"))},this),"remove.owl.carousel prepared.owl.carousel":a.proxy(function(a){this._core.settings.dotsData&&this._templates.splice(a.position,1)},this),"change.owl.carousel":a.proxy(function(a){if("position"==a.property.name&&!this._core.state.revert&&!this._core.settings.loop&&this._core.settings.navRewind){var b=this._core.current(),c=this._core.maximum(),d=this._core.minimum();a.data=a.property.value>c?b>=c?d:c:a.property.value<d?c:a.property.value}},this),"changed.owl.carousel":a.proxy(function(a){"position"==a.property.name&&this.draw()},this),"refreshed.owl.carousel":a.proxy(function(){this._initialized||(this.initialize(),this._initialized=!0),this._core.trigger("refresh",null,"navigation"),this.update(),this.draw(),this._core.trigger("refreshed",null,"navigation")},this)},this._core.options=a.extend({},b.Defaults,this._core.options),this.$element.on(this._handlers)};b.Defaults={nav:!1,navRewind:!0,navText:["prev","next"],navSpeed:!1,navElement:"div",navContainer:!1,navContainerClass:"owl-nav",navClass:["owl-prev","owl-next"],slideBy:1,dotClass:"owl-dot",dotsClass:"owl-dots",dots:!0,dotsEach:!1,dotData:!1,dotsSpeed:!1,dotsContainer:!1,controlsClass:"owl-controls"},b.prototype.initialize=function(){var b,c,d=this._core.settings;d.dotsData||(this._templates=[a("<div>").addClass(d.dotClass).append(a("<span>")).prop("outerHTML")]),d.navContainer&&d.dotsContainer||(this._controls.$container=a("<div>").addClass(d.controlsClass).appendTo(this.$element)),this._controls.$indicators=d.dotsContainer?a(d.dotsContainer):a("<div>").hide().addClass(d.dotsClass).appendTo(this._controls.$container),this._controls.$indicators.on("click","div",a.proxy(function(b){var c=a(b.target).parent().is(this._controls.$indicators)?a(b.target).index():a(b.target).parent().index();b.preventDefault(),this.to(c,d.dotsSpeed)},this)),b=d.navContainer?a(d.navContainer):a("<div>").addClass(d.navContainerClass).prependTo(this._controls.$container),this._controls.$next=a("<"+d.navElement+">"),this._controls.$previous=this._controls.$next.clone(),this._controls.$previous.addClass(d.navClass[0]).html(d.navText[0]).hide().prependTo(b).on("click",a.proxy(function(){this.prev(d.navSpeed)},this)),this._controls.$next.addClass(d.navClass[1]).html(d.navText[1]).hide().appendTo(b).on("click",a.proxy(function(){this.next(d.navSpeed)},this));for(c in this._overrides)this._core[c]=a.proxy(this[c],this)},b.prototype.destroy=function(){var a,b,c,d;for(a in this._handlers)this.$element.off(a,this._handlers[a]);for(b in this._controls)this._controls[b].remove();for(d in this.overides)this._core[d]=this._overrides[d];for(c in Object.getOwnPropertyNames(this))"function"!=typeof this[c]&&(this[c]=null)},b.prototype.update=function(){var a,b,c,d=this._core.settings,e=this._core.clones().length/2,f=e+this._core.items().length,g=d.center||d.autoWidth||d.dotData?1:d.dotsEach||d.items;if("page"!==d.slideBy&&(d.slideBy=Math.min(d.slideBy,d.items)),d.dots||"page"==d.slideBy)for(this._pages=[],a=e,b=0,c=0;f>a;a++)(b>=g||0===b)&&(this._pages.push({start:a-e,end:a-e+g-1}),b=0,++c),b+=this._core.mergers(this._core.relative(a))},b.prototype.draw=function(){var b,c,d="",e=this._core.settings,f=(this._core.$stage.children(),this._core.relative(this._core.current()));if(!e.nav||e.loop||e.navRewind||(this._controls.$previous.toggleClass("disabled",0>=f),this._controls.$next.toggleClass("disabled",f>=this._core.maximum())),this._controls.$previous.toggle(e.nav),this._controls.$next.toggle(e.nav),e.dots){if(b=this._pages.length-this._controls.$indicators.children().length,e.dotData&&0!==b){for(c=0;c<this._controls.$indicators.children().length;c++)d+=this._templates[this._core.relative(c)];this._controls.$indicators.html(d)}else b>0?(d=new Array(b+1).join(this._templates[0]),this._controls.$indicators.append(d)):0>b&&this._controls.$indicators.children().slice(b).remove();this._controls.$indicators.find(".active").removeClass("active"),this._controls.$indicators.children().eq(a.inArray(this.current(),this._pages)).addClass("active")}this._controls.$indicators.toggle(e.dots)},b.prototype.onTrigger=function(b){var c=this._core.settings;b.page={index:a.inArray(this.current(),this._pages),count:this._pages.length,size:c&&(c.center||c.autoWidth||c.dotData?1:c.dotsEach||c.items)}},b.prototype.current=function(){var b=this._core.relative(this._core.current());return a.grep(this._pages,function(a){return a.start<=b&&a.end>=b}).pop()},b.prototype.getPosition=function(b){var c,d,e=this._core.settings;return"page"==e.slideBy?(c=a.inArray(this.current(),this._pages),d=this._pages.length,b?++c:--c,c=this._pages[(c%d+d)%d].start):(c=this._core.relative(this._core.current()),d=this._core.items().length,b?c+=e.slideBy:c-=e.slideBy),c},b.prototype.next=function(b){a.proxy(this._overrides.to,this._core)(this.getPosition(!0),b)},b.prototype.prev=function(b){a.proxy(this._overrides.to,this._core)(this.getPosition(!1),b)},b.prototype.to=function(b,c,d){var e;d?a.proxy(this._overrides.to,this._core)(b,c):(e=this._pages.length,a.proxy(this._overrides.to,this._core)(this._pages[(b%e+e)%e].start,c))},a.fn.owlCarousel.Constructor.Plugins.Navigation=b}(window.Zepto||window.jQuery,window,document),function(a,b){"use strict";var c=function(d){this._core=d,this._hashes={},this.$element=this._core.$element,this._handlers={"initialized.owl.carousel":a.proxy(function(){"URLHash"==this._core.settings.startPosition&&a(b).trigger("hashchange.owl.navigation")},this),"prepared.owl.carousel":a.proxy(function(b){var c=a(b.content).find("[data-hash]").andSelf("[data-hash]").attr("data-hash");this._hashes[c]=b.content},this)},this._core.options=a.extend({},c.Defaults,this._core.options),this.$element.on(this._handlers),a(b).on("hashchange.owl.navigation",a.proxy(function(){var a=b.location.hash.substring(1),c=this._core.$stage.children(),d=this._hashes[a]&&c.index(this._hashes[a])||0;return a?void this._core.to(d,!1,!0):!1},this))};c.Defaults={URLhashListener:!1},c.prototype.destroy=function(){var c,d;a(b).off("hashchange.owl.navigation");for(c in this._handlers)this._core.$element.off(c,this._handlers[c]);for(d in Object.getOwnPropertyNames(this))"function"!=typeof this[d]&&(this[d]=null)},a.fn.owlCarousel.Constructor.Plugins.Hash=c}(window.Zepto||window.jQuery,window,document);

}(jQuery));
    
    
(function($){
    /*! Lazy Load 1.9.5 - MIT license - Copyright 2010-2015 Mika Tuupola */
    !function(a,b,c,d){var e=a(b);a.fn.lazyload=function(f){function g(){var b=0;i.each(function(){var c=a(this);if(!j.skip_invisible||c.is(":visible"))if(a.abovethetop(this,j)||a.leftofbegin(this,j));else if(a.belowthefold(this,j)||a.rightoffold(this,j)){if(++b>j.failure_limit)return!1}else c.trigger("appear"),b=0})}var h,i=this,j={threshold:0,failure_limit:0,event:"scroll",effect:"show",container:b,data_attribute:"original",skip_invisible:!1,appear:null,load:null,placeholder:"data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAAAXNSR0IArs4c6QAAAARnQU1BAACxjwv8YQUAAAAJcEhZcwAADsQAAA7EAZUrDhsAAAANSURBVBhXYzh8+PB/AAffA0nNPuCLAAAAAElFTkSuQmCC"};return f&&(d!==f.failurelimit&&(f.failure_limit=f.failurelimit,delete f.failurelimit),d!==f.effectspeed&&(f.effect_speed=f.effectspeed,delete f.effectspeed),a.extend(j,f)),h=j.container===d||j.container===b?e:a(j.container),0===j.event.indexOf("scroll")&&h.bind(j.event,function(){return g()}),this.each(function(){var b=this,c=a(b);b.loaded=!1,(c.attr("src")===d||c.attr("src")===!1)&&c.is("img")&&c.attr("src",j.placeholder),c.one("appear",function(){if(!this.loaded){if(j.appear){var d=i.length;j.appear.call(b,d,j)}a("<img />").bind("load",function(){var d=c.attr("data-"+j.data_attribute);c.hide(),c.is("img")?c.attr("src",d):c.css("background-image","url('"+d+"')"),c[j.effect](j.effect_speed),b.loaded=!0;var e=a.grep(i,function(a){return!a.loaded});if(i=a(e),j.load){var f=i.length;j.load.call(b,f,j)}}).attr("src",c.attr("data-"+j.data_attribute))}}),0!==j.event.indexOf("scroll")&&c.bind(j.event,function(){b.loaded||c.trigger("appear")})}),e.bind("resize",function(){g()}),/(?:iphone|ipod|ipad).*os 5/gi.test(navigator.appVersion)&&e.bind("pageshow",function(b){b.originalEvent&&b.originalEvent.persisted&&i.each(function(){a(this).trigger("appear")})}),a(c).ready(function(){g()}),this},a.belowthefold=function(c,f){var g;return g=f.container===d||f.container===b?(b.innerHeight?b.innerHeight:e.height())+e.scrollTop():a(f.container).offset().top+a(f.container).height(),g<=a(c).offset().top-f.threshold},a.rightoffold=function(c,f){var g;return g=f.container===d||f.container===b?e.width()+e.scrollLeft():a(f.container).offset().left+a(f.container).width(),g<=a(c).offset().left-f.threshold},a.abovethetop=function(c,f){var g;return g=f.container===d||f.container===b?e.scrollTop():a(f.container).offset().top,g>=a(c).offset().top+f.threshold+a(c).height()},a.leftofbegin=function(c,f){var g;return g=f.container===d||f.container===b?e.scrollLeft():a(f.container).offset().left,g>=a(c).offset().left+f.threshold+a(c).width()},a.inviewport=function(b,c){return!(a.rightoffold(b,c)||a.leftofbegin(b,c)||a.belowthefold(b,c)||a.abovethetop(b,c))},a.extend(a.expr[":"],{"below-the-fold":function(b){return a.belowthefold(b,{threshold:0})},"above-the-top":function(b){return!a.belowthefold(b,{threshold:0})},"right-of-screen":function(b){return a.rightoffold(b,{threshold:0})},"left-of-screen":function(b){return!a.rightoffold(b,{threshold:0})},"in-viewport":function(b){return a.inviewport(b,{threshold:0})},"above-the-fold":function(b){return!a.belowthefold(b,{threshold:0})},"right-of-fold":function(b){return a.rightoffold(b,{threshold:0})},"left-of-fold":function(b){return!a.rightoffold(b,{threshold:0})}})}(jQuery,window,document);    

}(jQuery));


(function($){
/*
 * Snap.js
 *
 * Copyright 2013, Jacob Kelley - http://jakiestfu.com/
 * Released under the MIT Licence
 * http://opensource.org/licenses/MIT
 *
 * Github:  http://github.com/jakiestfu/Snap.js/
 * Version: 1.9.3 (with elementMirror for fixed navigation bars)
 */
/*jslint browser: true*/
/*global define, module, ender*/
(function(win, doc) {
    'use strict';
    var Snap = Snap || function(userOpts) {
        var settings = {
            element: null,
            elementMirror: null,
			elementMirror2: null,
            dragger: null,
            disable: 'none',
            addBodyClasses: true,
            hyperextensible: true,
            resistance: 0.5,
            flickThreshold: 50,
            transitionSpeed: 0.3,
            easing: 'ease',
            maxPosition: 266,
            minPosition: -266,
            tapToClose: true,
            touchToDrag: true,
            slideIntent: 40, // degrees
            minDragDistance: 5
        },
        cache = {
            simpleStates: {
                opening: null,
                towards: null,
                hyperExtending: null,
                halfway: null,
                flick: null,
                translation: {
                    absolute: 0,
                    relative: 0,
                    sinceDirectionChange: 0,
                    percentage: 0
                }
            }
        },
        eventList = {},
        utils = {
            hasTouch: ('ontouchstart' in doc.documentElement || win.navigator.msPointerEnabled),
            eventType: function(action) {
                var eventTypes = {
                        down: (utils.hasTouch ? 'touchstart' : 'mousedown'),
                        move: (utils.hasTouch ? 'touchmove' : 'mousemove'),
                        up: (utils.hasTouch ? 'touchend' : 'mouseup'),
                        out: (utils.hasTouch ? 'touchcancel' : 'mouseout')
                    };
                return eventTypes[action];
            },
            page: function(t, e){
                return (utils.hasTouch && e.touches.length && e.touches[0]) ? e.touches[0]['page'+t] : e['page'+t];
            },
            klass: {
                has: function(el, name){
                    return (el.className).indexOf(name) !== -1;
                },
                add: function(el, name){
                    if(!utils.klass.has(el, name) && settings.addBodyClasses){
                        el.className += " "+name;
                    }
                },
                remove: function(el, name){
                    if(settings.addBodyClasses){
                        el.className = (el.className).replace(name, "").replace(/^\s+|\s+$/g, '');
                    }
                }
            },
            dispatchEvent: function(type) {
                if (typeof eventList[type] === 'function') {
                    return eventList[type].call();
                }
            },
            vendor: function(){
                var tmp = doc.createElement("div"),
                    prefixes = 'webkit Moz O ms'.split(' '),
                    i;
                for (i in prefixes) {
                    if (typeof tmp.style[prefixes[i] + 'Transition'] !== 'undefined') {
                        return prefixes[i];
                    }
                }
            },
            transitionCallback: function(){
                return (cache.vendor==='Moz' || cache.vendor==='ms') ? 'transitionend' : cache.vendor+'TransitionEnd';
            },
            canTransform: function(){settings.element
                return typeof settings.element.style[cache.vendor+'Transform'] !== 'undefined';
            },
            deepExtend: function(destination, source) {
                var property;
                for (property in source) {
                    if (source[property] && source[property].constructor && source[property].constructor === Object) {
                        destination[property] = destination[property] || {};
                        utils.deepExtend(destination[property], source[property]);
                    } else {
                        destination[property] = source[property];
                    }
                }
                return destination;
            },
            angleOfDrag: function(x, y) {
                var degrees, theta;
                // Calc Theta
                theta = Math.atan2(-(cache.startDragY - y), (cache.startDragX - x));
                if (theta < 0) {
                    theta += 2 * Math.PI;
                }
                // Calc Degrees
                degrees = Math.floor(theta * (180 / Math.PI) - 180);
                if (degrees < 0 && degrees > -180) {
                    degrees = 360 - Math.abs(degrees);
                }
                return Math.abs(degrees);
            },
            events: {
                addEvent: function addEvent(element, eventName, func) {
                    if (element.addEventListener) {
                        return element.addEventListener(eventName, func, false);
                    } else if (element.attachEvent) {
                        return element.attachEvent("on" + eventName, func);
                    }
                },
                removeEvent: function addEvent(element, eventName, func) {
                    if (element.addEventListener) {
                        return element.removeEventListener(eventName, func, false);
                    } else if (element.attachEvent) {
                        return element.detachEvent("on" + eventName, func);
                    }
                },
                prevent: function(e) {
                    if (e.preventDefault) {
                        e.preventDefault();
                    } else {
                        e.returnValue = false;
                    }
                }
            },
            parentUntil: function(el, attr) {
                var isStr = typeof attr === 'string';
                while (el.parentNode) {
                    if (isStr && el.getAttribute && el.getAttribute(attr)){
                        return el;
                    } else if(!isStr && el === attr){
                        return el;
                    }
                    el = el.parentNode;
                }
                return null;
            }
        },
        action = {
            translate: {
                get: {
                    matrix: function(index) {

                        if( !utils.canTransform() ){
                            return parseInt(settings.element.style.left, 10);
                        } else {
                            var matrix = win.getComputedStyle(settings.element)[cache.vendor+'Transform'].match(/\((.*)\)/),
                                ieOffset = 8;
                            if (matrix) {
                                matrix = matrix[1].split(',');
                                if(matrix.length===16){
                                    index+=ieOffset;
                                }
                                return parseInt(matrix[index], 10);
                            }
                            return 0;
                        }
                    }
                },
                easeCallback: function(){
                    settings.element.style[cache.vendor+'Transition'] = '';
                    settings.elementMirror.style[cache.vendor+'Transition'] = '';
					settings.elementMirror2.style[cache.vendor+'Transition'] = '';
                    cache.translation = action.translate.get.matrix(4);
                    cache.easing = false;
                    clearInterval(cache.animatingInterval);

                    if(cache.easingTo===0){
                        utils.klass.remove(doc.body, 'snapjs-right');
                        utils.klass.remove(doc.body, 'snapjs-left');
                    }

                    utils.dispatchEvent('animated');
                    utils.events.removeEvent(settings.element, utils.transitionCallback(), action.translate.easeCallback);
                },
                easeTo: function(n) {

                    if( !utils.canTransform() ){
                        cache.translation = n;
                        action.translate.x(n);
                    } else {
                        cache.easing = true;
                        cache.easingTo = n;

                        settings.element.style[cache.vendor+'Transition'] = 'all ' + settings.transitionSpeed + 's ' + settings.easing;
                        settings.elementMirror.style[cache.vendor+'Transition'] = 'all ' + settings.transitionSpeed + 's ' + settings.easing;
						settings.elementMirror2.style[cache.vendor+'Transition'] = 'all ' + settings.transitionSpeed + 's ' + settings.easing;

                        cache.animatingInterval = setInterval(function() {
                            utils.dispatchEvent('animating');
                        }, 1);
                        
                        utils.events.addEvent(settings.element, utils.transitionCallback(), action.translate.easeCallback);
                        action.translate.x(n);
                    }
                    if(n===0){
                           settings.element.style[cache.vendor+'Transform'] = '';
                           settings.elementMirror.style[cache.vendor+'Transform'] = '';
						   settings.elementMirror2.style[cache.vendor+'Transform'] = '';
                       }
                },
                x: function(n) {
                    if( (settings.disable==='left' && n>0) ||
                        (settings.disable==='right' && n<0)
                    ){ return; }
                    
                    if( !settings.hyperextensible ){
                        if( n===settings.maxPosition || n>settings.maxPosition ){
                            n=settings.maxPosition;
                        } else if( n===settings.minPosition || n<settings.minPosition ){
                            n=settings.minPosition;
                        }
                    }
                    
                    n = parseInt(n, 10);
                    if(isNaN(n)){
                        n = 0;
                    }

                    if( utils.canTransform() ){
                        var theTranslate = 'translate3d(' + n + 'px, 0,0)';
                        settings.element.style[cache.vendor+'Transform'] = theTranslate;
                        settings.elementMirror.style[cache.vendor+'Transform'] = theTranslate;
						settings.elementMirror2.style[cache.vendor+'Transform'] = theTranslate;
                    } else {
                        settings.element.style.width = (win.innerWidth || doc.documentElement.clientWidth)+'px';
                        settings.elementMirror.style.width = (win.innerWidth || doc.documentElement.clientWidth)+'px';
						settings.elementMirror2.style.width = (win.innerWidth || doc.documentElement.clientWidth)+'px';
						
                        settings.element.style.left = n+'px';
                        settings.elementMirror.style.left = n+'px';
						settings.elementMirror2.style.left = n+'px';
                        settings.element.style.right = '';
                        settings.elementMirror.style.right = '';
						settings.elementMirror2.style.right = '';
						
                    }
                }
            },
            drag: {
                listen: function() {
                    cache.translation = 0;
                    cache.easing = false;
                    utils.events.addEvent(settings.element, utils.eventType('down'), action.drag.startDrag);
                    utils.events.addEvent(settings.element, utils.eventType('move'), action.drag.dragging);
                    utils.events.addEvent(settings.element, utils.eventType('up'), action.drag.endDrag);
                },
                stopListening: function() {
                    utils.events.removeEvent(settings.element, utils.eventType('down'), action.drag.startDrag);
                    utils.events.removeEvent(settings.element, utils.eventType('move'), action.drag.dragging);
                    utils.events.removeEvent(settings.element, utils.eventType('up'), action.drag.endDrag);
                },
                startDrag: function(e) {
                    // No drag on ignored elements
                    var target = e.target ? e.target : e.srcElement,
                        ignoreParent = utils.parentUntil(target, 'data-snap-ignore');
                    
                    if (ignoreParent) {
                        utils.dispatchEvent('ignore');
                        return;
                    }
                    
                    
                    if(settings.dragger){
                        var dragParent = utils.parentUntil(target, settings.dragger);
                        
                        // Only use dragger if we're in a closed state
                        if( !dragParent && 
                            (cache.translation !== settings.minPosition && 
                            cache.translation !== settings.maxPosition
                        )){
                            return;
                        }
                    }
                    
                    utils.dispatchEvent('start');
                    settings.element.style[cache.vendor+'Transition'] = '';
                    settings.elementMirror.style[cache.vendor+'Transition'] = '';
					settings.elementMirror2.style[cache.vendor+'Transition'] = '';
                    cache.isDragging = true;
                    cache.hasIntent = null;
                    cache.intentChecked = false;
                    cache.startDragX = utils.page('X', e);
                    cache.startDragY = utils.page('Y', e);
                    cache.dragWatchers = {
                        current: 0,
                        last: 0,
                        hold: 0,
                        state: ''
                    };
                    cache.simpleStates = {
                        opening: null,
                        towards: null,
                        hyperExtending: null,
                        halfway: null,
                        flick: null,
                        translation: {
                            absolute: 0,
                            relative: 0,
                            sinceDirectionChange: 0,
                            percentage: 0
                        }
                    };
                },
                dragging: function(e) {
                    if (cache.isDragging && settings.touchToDrag) {

                        var thePageX = utils.page('X', e),
                            thePageY = utils.page('Y', e),
                            translated = cache.translation,
                            absoluteTranslation = action.translate.get.matrix(4),
                            whileDragX = thePageX - cache.startDragX,
                            openingLeft = absoluteTranslation > 0,
                            translateTo = whileDragX,
                            diff;

                        // Shown no intent already
                        if((cache.intentChecked && !cache.hasIntent)){
                            return;
                        }

                        if(settings.addBodyClasses){
                            if((absoluteTranslation)>0){
                                utils.klass.add(doc.body, 'snapjs-left');
                                utils.klass.remove(doc.body, 'snapjs-right');
                            } else if((absoluteTranslation)<0){
                                utils.klass.add(doc.body, 'snapjs-right');
                                utils.klass.remove(doc.body, 'snapjs-left');
                            }
                        }

                        if (cache.hasIntent === false || cache.hasIntent === null) {
                            var deg = utils.angleOfDrag(thePageX, thePageY),
                                inRightRange = (deg >= 0 && deg <= settings.slideIntent) || (deg <= 360 && deg > (360 - settings.slideIntent)),
                                inLeftRange = (deg >= 180 && deg <= (180 + settings.slideIntent)) || (deg <= 180 && deg >= (180 - settings.slideIntent));
                            if (!inLeftRange && !inRightRange) {
                                cache.hasIntent = false;
                            } else {
                                cache.hasIntent = true;
                            }
                            cache.intentChecked = true;
                        }

                        if (
                            (settings.minDragDistance>=Math.abs(thePageX-cache.startDragX)) || // Has user met minimum drag distance?
                            (cache.hasIntent === false)
                        ) {
                            return;
                        }

                        utils.events.prevent(e);
                        utils.dispatchEvent('drag');

                        cache.dragWatchers.current = thePageX;
                        // Determine which direction we are going
                        if (cache.dragWatchers.last > thePageX) {
                            if (cache.dragWatchers.state !== 'left') {
                                cache.dragWatchers.state = 'left';
                                cache.dragWatchers.hold = thePageX;
                            }
                            cache.dragWatchers.last = thePageX;
                        } else if (cache.dragWatchers.last < thePageX) {
                            if (cache.dragWatchers.state !== 'right') {
                                cache.dragWatchers.state = 'right';
                                cache.dragWatchers.hold = thePageX;
                            }
                            cache.dragWatchers.last = thePageX;
                        }
                        if (openingLeft) {
                            // Pulling too far to the right
                            if (settings.maxPosition < absoluteTranslation) {
                                diff = (absoluteTranslation - settings.maxPosition) * settings.resistance;
                                translateTo = whileDragX - diff;
                            }
                            cache.simpleStates = {
                                opening: 'left',
                                towards: cache.dragWatchers.state,
                                hyperExtending: settings.maxPosition < absoluteTranslation,
                                halfway: absoluteTranslation > (settings.maxPosition / 2),
                                flick: Math.abs(cache.dragWatchers.current - cache.dragWatchers.hold) > settings.flickThreshold,
                                translation: {
                                    absolute: absoluteTranslation,
                                    relative: whileDragX,
                                    sinceDirectionChange: (cache.dragWatchers.current - cache.dragWatchers.hold),
                                    percentage: (absoluteTranslation/settings.maxPosition)*100
                                }
                            };
                        } else {
                            // Pulling too far to the left
                            if (settings.minPosition > absoluteTranslation) {
                                diff = (absoluteTranslation - settings.minPosition) * settings.resistance;
                                translateTo = whileDragX - diff;
                            }
                            cache.simpleStates = {
                                opening: 'right',
                                towards: cache.dragWatchers.state,
                                hyperExtending: settings.minPosition > absoluteTranslation,
                                halfway: absoluteTranslation < (settings.minPosition / 2),
                                flick: Math.abs(cache.dragWatchers.current - cache.dragWatchers.hold) > settings.flickThreshold,
                                translation: {
                                    absolute: absoluteTranslation,
                                    relative: whileDragX,
                                    sinceDirectionChange: (cache.dragWatchers.current - cache.dragWatchers.hold),
                                    percentage: (absoluteTranslation/settings.minPosition)*100
                                }
                            };
                        }
                        action.translate.x(translateTo + translated);
                    }
                },
                endDrag: function(e) {
                    if (cache.isDragging) {
                        utils.dispatchEvent('end');
                        var translated = action.translate.get.matrix(4);

                        // Tap Close
                        if (cache.dragWatchers.current === 0 && translated !== 0 && settings.tapToClose) {
                            utils.dispatchEvent('close');
                            utils.events.prevent(e);
                            action.translate.easeTo(0);
                            cache.isDragging = false;
                            cache.startDragX = 0;
                            return;
                        }

                        // Revealing Left
                        if (cache.simpleStates.opening === 'left') {
                            // Halfway, Flicking, or Too Far Out
                            if ((cache.simpleStates.halfway || cache.simpleStates.hyperExtending || cache.simpleStates.flick)) {
                                if (cache.simpleStates.flick && cache.simpleStates.towards === 'left') { // Flicking Closed
                                    action.translate.easeTo(0);
                                } else if (
                                    (cache.simpleStates.flick && cache.simpleStates.towards === 'right') || // Flicking Open OR
                                    (cache.simpleStates.halfway || cache.simpleStates.hyperExtending) // At least halfway open OR hyperextending
                                ) {
                                    action.translate.easeTo(settings.maxPosition); // Open Left
                                }
                            } else {
                                action.translate.easeTo(0); // Close Left
                            }
                            // Revealing Right
                        } else if (cache.simpleStates.opening === 'right') {
                            // Halfway, Flicking, or Too Far Out
                            if ((cache.simpleStates.halfway || cache.simpleStates.hyperExtending || cache.simpleStates.flick)) {
                                if (cache.simpleStates.flick && cache.simpleStates.towards === 'right') { // Flicking Closed
                                    action.translate.easeTo(0);
                                } else if (
                                    (cache.simpleStates.flick && cache.simpleStates.towards === 'left') || // Flicking Open OR
                                    (cache.simpleStates.halfway || cache.simpleStates.hyperExtending) // At least halfway open OR hyperextending
                                ) {
                                    action.translate.easeTo(settings.minPosition); // Open Right
                                }
                            } else {
                                action.translate.easeTo(0); // Close Right
                            }
                        }
                        cache.isDragging = false;
                        cache.startDragX = utils.page('X', e);
                    }
                }
            }
        },
        init = function(opts) {
            if (opts.element) {
                utils.deepExtend(settings, opts);
                cache.vendor = utils.vendor();
                action.drag.listen();
            }
        };
        /*
         * Public
         */
        this.open = function(side) {
            utils.dispatchEvent('open');
            utils.klass.remove(doc.body, 'snapjs-expand-left');
            utils.klass.remove(doc.body, 'snapjs-expand-right');

            if (side === 'left') {
                cache.simpleStates.opening = 'left';
                cache.simpleStates.towards = 'right';
                utils.klass.add(doc.body, 'snapjs-left');
                utils.klass.remove(doc.body, 'snapjs-right');
                action.translate.easeTo(settings.maxPosition);
            } else if (side === 'right') {
                cache.simpleStates.opening = 'right';
                cache.simpleStates.towards = 'left';
                utils.klass.remove(doc.body, 'snapjs-left');
                utils.klass.add(doc.body, 'snapjs-right');
                action.translate.easeTo(settings.minPosition);
            }
        };
        this.close = function() {
            utils.dispatchEvent('close');
            action.translate.easeTo(0);
        };
        this.expand = function(side){
            var to = win.innerWidth || doc.documentElement.clientWidth;

            if(side==='left'){
                utils.dispatchEvent('expandLeft');
                utils.klass.add(doc.body, 'snapjs-expand-left');
                utils.klass.remove(doc.body, 'snapjs-expand-right');
            } else {
                utils.dispatchEvent('expandRight');
                utils.klass.add(doc.body, 'snapjs-expand-right');
                utils.klass.remove(doc.body, 'snapjs-expand-left');
                to *= -1;
            }
            action.translate.easeTo(to);
        };

        this.on = function(evt, fn) {
            eventList[evt] = fn;
            return this;
        };
        this.off = function(evt) {
            if (eventList[evt]) {
                eventList[evt] = false;
            }
        };

        this.enable = function() {
            utils.dispatchEvent('enable');
            action.drag.listen();
        };
        this.disable = function() {
            utils.dispatchEvent('disable');
            action.drag.stopListening();
        };

        this.settings = function(opts){
            utils.deepExtend(settings, opts);
        };

        this.state = function() {
            var state,
                fromLeft = action.translate.get.matrix(4);
            if (fromLeft === settings.maxPosition) {
                state = 'left';
            } else if (fromLeft === settings.minPosition) {
                state = 'right';
            } else {
                state = 'closed';
            }
            return {
                state: state,
                info: cache.simpleStates
            };
        };
        init(userOpts);
    };
    if ((typeof module !== 'undefined') && module.exports) {
        module.exports = Snap;
    }
    if (typeof ender === 'undefined') {
        this.Snap = Snap;
    }
    if ((typeof define === "function") && define.amd) {
        define("snap", [], function() {
            return Snap;
        });
    }
}).call(this, window, document);
}(jQuery));// JavaScript Document
    
(function($){
    /*License MIT & GPL 3.0 Licenses.*/ 
    /* https://github.com/rendro/countdown */ 

    !function(t){if("object"==typeof exports&&"undefined"!=typeof module)module.exports=t();else if("function"==typeof define&&define.amd)define([],t);else{var e;"undefined"!=typeof window?e=window:"undefined"!=typeof global?e=global:"undefined"!=typeof self&&(e=self),e.Countdown=t()}}(function(){return function t(e,i,n){function s(r,a){if(!i[r]){if(!e[r]){var d="function"==typeof require&&require;if(!a&&d)return d(r,!0);if(o)return o(r,!0);var f=new Error("Cannot find module '"+r+"'");throw f.code="MODULE_NOT_FOUND",f}var h=i[r]={exports:{}};e[r][0].call(h.exports,function(t){var i=e[r][1][t];return s(i?i:t)},h,h.exports,t,e,i,n)}return i[r].exports}for(var o="function"==typeof require&&require,r=0;r<n.length;r++)s(n[r]);return s}({1:[function(t,e){var i={date:"June 7, 2087 15:03:25",refresh:1e3,offset:0,onEnd:function(){},render:function(t){this.el.innerHTML="<div class='date-years'>"+t.years+"<em>years</em> </div><div class='date-days'>"+t.days+"<em>days</em> </div><div class='date-hours'>"+this.leadingZeros(t.hours)+"<em>hours</em> </div><div class='date-minutes'>"+this.leadingZeros(t.min)+"<em>minutes</em> </div><div class='date-seconds'>"+this.leadingZeros(t.sec)+"<em>seconds</em> </div>"}},n=function(t,e){this.el=t,this.options={},this.interval=!1;for(var n in i)i.hasOwnProperty(n)&&(this.options[n]="undefined"!=typeof e[n]?e[n]:i[n],"date"===n&&"object"!=typeof this.options.date&&(this.options.date=new Date(this.options.date)),"function"==typeof this.options[n]&&(this.options[n]=this.options[n].bind(this)));this.getDiffDate=function(){var t=(this.options.date.getTime()-Date.now()+this.options.offset)/1e3,e={years:0,days:0,hours:0,min:0,sec:0,millisec:0};return 0>=t?(this.interval&&(this.stop(),this.options.onEnd()),e):(t>=31557600&&(e.years=Math.floor(t/31557600),t-=365.25*e.years*86400),t>=86400&&(e.days=Math.floor(t/86400),t-=86400*e.days),t>=3600&&(e.hours=Math.floor(t/3600),t-=3600*e.hours),t>=60&&(e.min=Math.floor(t/60),t-=60*e.min),e.sec=Math.round(t),e.millisec=t%1*1e3,e)}.bind(this),this.leadingZeros=function(t,e){return e=e||2,t=String(t),t.length>e?t:(Array(e+1).join("0")+t).substr(-e)},this.update=function(t){return"object"!=typeof t&&(t=new Date(t)),this.options.date=t,this.render(),this}.bind(this),this.stop=function(){return this.interval&&(clearInterval(this.interval),this.interval=!1),this}.bind(this),this.render=function(){return this.options.render(this.getDiffDate()),this}.bind(this),this.start=function(){return this.interval?void 0:(this.render(),this.options.refresh&&(this.interval=setInterval(this.render,this.options.refresh)),this)}.bind(this),this.updateOffset=function(t){return this.options.offset=t,this}.bind(this),this.start()};e.exports=n},{}],2:[function(t,e){var i=t("./countdown.js"),n="countdown",s="date";jQuery.fn.countdown=function(t){return $.each(this,function(e,o){var r=$(o);r.data(n)||(r.data(s)&&(t.date=r.data(s)),r.data(n,new i(o,t)))})},e.exports=i},{"./countdown.js":1}]},{},[2])(2)}); 
}(jQuery));
    
    
(function($){
    /*!
     * Chart.js
     * http://chartjs.org/
     * Version: 1.0.2
     *
     * Copyright 2015 Nick Downie
     * Released under the MIT license
     * https://github.com/nnnick/Chart.js/blob/master/LICENSE.md
     */
    (function(){"use strict";var t=this,i=t.Chart,e=function(t){this.canvas=t.canvas,this.ctx=t;var i=function(t,i){return t["offset"+i]?t["offset"+i]:document.defaultView.getComputedStyle(t).getPropertyValue(i)},e=this.width=i(t.canvas,"Width"),n=this.height=i(t.canvas,"Height");t.canvas.width=e,t.canvas.height=n;var e=this.width=t.canvas.width,n=this.height=t.canvas.height;return this.aspectRatio=this.width/this.height,s.retinaScale(this),this};e.defaults={global:{animation:false,animationSteps:60,animationEasing:"easeOutQuart",showScale:!0,scaleOverride:!1,scaleSteps:null,scaleStepWidth:null,scaleStartValue:null,scaleLineColor:"rgba(0,0,0,.1)",scaleLineWidth:1,scaleShowLabels:!0,scaleLabel:"<%=value%>",scaleIntegersOnly:!0,scaleBeginAtZero:!1,scaleFontFamily:"'Helvetica Neue', 'Helvetica', 'Arial', sans-serif",scaleFontSize:12,scaleFontStyle:"normal",scaleFontColor:"#666",responsive:true,maintainAspectRatio:true,showTooltips:!0,customTooltips:!1,tooltipEvents:["mousemove","touchstart","touchmove","mouseout"],tooltipFillColor:"rgba(0,0,0,0.8)",tooltipFontFamily:"'Helvetica Neue', 'Helvetica', 'Arial', sans-serif",tooltipFontSize:14,tooltipFontStyle:"normal",tooltipFontColor:"#fff",tooltipTitleFontFamily:"'Helvetica Neue', 'Helvetica', 'Arial', sans-serif",tooltipTitleFontSize:14,tooltipTitleFontStyle:"bold",tooltipTitleFontColor:"#fff",tooltipYPadding:6,tooltipXPadding:6,tooltipCaretSize:8,tooltipCornerRadius:6,tooltipXOffset:10,tooltipTemplate:"<%if (label){%><%=label%>: <%}%><%= value %>",multiTooltipTemplate:"<%= value %>",multiTooltipKeyBackground:"#fff",onAnimationProgress:function(){},onAnimationComplete:function(){}}},e.types={};var s=e.helpers={},n=s.each=function(t,i,e){var s=Array.prototype.slice.call(arguments,3);if(t)if(t.length===+t.length){var n;for(n=0;n<t.length;n++)i.apply(e,[t[n],n].concat(s))}else for(var o in t)i.apply(e,[t[o],o].concat(s))},o=s.clone=function(t){var i={};return n(t,function(e,s){t.hasOwnProperty(s)&&(i[s]=e)}),i},a=s.extend=function(t){return n(Array.prototype.slice.call(arguments,1),function(i){n(i,function(e,s){i.hasOwnProperty(s)&&(t[s]=e)})}),t},h=s.merge=function(){var t=Array.prototype.slice.call(arguments,0);return t.unshift({}),a.apply(null,t)},l=s.indexOf=function(t,i){if(Array.prototype.indexOf)return t.indexOf(i);for(var e=0;e<t.length;e++)if(t[e]===i)return e;return-1},r=(s.where=function(t,i){var e=[];return s.each(t,function(t){i(t)&&e.push(t)}),e},s.findNextWhere=function(t,i,e){e||(e=-1);for(var s=e+1;s<t.length;s++){var n=t[s];if(i(n))return n}},s.findPreviousWhere=function(t,i,e){e||(e=t.length);for(var s=e-1;s>=0;s--){var n=t[s];if(i(n))return n}},s.inherits=function(t){var i=this,e=t&&t.hasOwnProperty("constructor")?t.constructor:function(){return i.apply(this,arguments)},s=function(){this.constructor=e};return s.prototype=i.prototype,e.prototype=new s,e.extend=r,t&&a(e.prototype,t),e.__super__=i.prototype,e}),c=s.noop=function(){},u=s.uid=function(){var t=0;return function(){return"chart-"+t++}}(),d=s.warn=function(t){window.console&&"function"==typeof window.console.warn&&console.warn(t)},p=s.amd="function"==typeof define&&define.amd,f=s.isNumber=function(t){return!isNaN(parseFloat(t))&&isFinite(t)},g=s.max=function(t){return Math.max.apply(Math,t)},m=s.min=function(t){return Math.min.apply(Math,t)},v=(s.cap=function(t,i,e){if(f(i)){if(t>i)return i}else if(f(e)&&e>t)return e;return t},s.getDecimalPlaces=function(t){return t%1!==0&&f(t)?t.toString().split(".")[1].length:0}),S=s.radians=function(t){return t*(Math.PI/180)},x=(s.getAngleFromPoint=function(t,i){var e=i.x-t.x,s=i.y-t.y,n=Math.sqrt(e*e+s*s),o=2*Math.PI+Math.atan2(s,e);return 0>e&&0>s&&(o+=2*Math.PI),{angle:o,distance:n}},s.aliasPixel=function(t){return t%2===0?0:.5}),y=(s.splineCurve=function(t,i,e,s){var n=Math.sqrt(Math.pow(i.x-t.x,2)+Math.pow(i.y-t.y,2)),o=Math.sqrt(Math.pow(e.x-i.x,2)+Math.pow(e.y-i.y,2)),a=s*n/(n+o),h=s*o/(n+o);return{inner:{x:i.x-a*(e.x-t.x),y:i.y-a*(e.y-t.y)},outer:{x:i.x+h*(e.x-t.x),y:i.y+h*(e.y-t.y)}}},s.calculateOrderOfMagnitude=function(t){return Math.floor(Math.log(t)/Math.LN10)}),C=(s.calculateScaleRange=function(t,i,e,s,n){var o=2,a=Math.floor(i/(1.5*e)),h=o>=a,l=g(t),r=m(t);l===r&&(l+=.5,r>=.5&&!s?r-=.5:l+=.5);for(var c=Math.abs(l-r),u=y(c),d=Math.ceil(l/(1*Math.pow(10,u)))*Math.pow(10,u),p=s?0:Math.floor(r/(1*Math.pow(10,u)))*Math.pow(10,u),f=d-p,v=Math.pow(10,u),S=Math.round(f/v);(S>a||a>2*S)&&!h;)if(S>a)v*=2,S=Math.round(f/v),S%1!==0&&(h=!0);else if(n&&u>=0){if(v/2%1!==0)break;v/=2,S=Math.round(f/v)}else v/=2,S=Math.round(f/v);return h&&(S=o,v=f/S),{steps:S,stepValue:v,min:p,max:p+S*v}},s.template=function(t,i){function e(t,i){var e=/\W/.test(t)?new Function("obj","var p=[],print=function(){p.push.apply(p,arguments);};with(obj){p.push('"+t.replace(/[\r\t\n]/g," ").split("<%").join("	").replace(/((^|%>)[^\t]*)'/g,"$1\r").replace(/\t=(.*?)%>/g,"',$1,'").split("	").join("');").split("%>").join("p.push('").split("\r").join("\\'")+"');}return p.join('');"):s[t]=s[t];return i?e(i):e}if(t instanceof Function)return t(i);var s={};return e(t,i)}),w=(s.generateLabels=function(t,i,e,s){var o=new Array(i);return labelTemplateString&&n(o,function(i,n){o[n]=C(t,{value:e+s*(n+1)})}),o},s.easingEffects={linear:function(t){return t},easeInQuad:function(t){return t*t},easeOutQuad:function(t){return-1*t*(t-2)},easeInOutQuad:function(t){return(t/=.5)<1?.5*t*t:-0.5*(--t*(t-2)-1)},easeInCubic:function(t){return t*t*t},easeOutCubic:function(t){return 1*((t=t/1-1)*t*t+1)},easeInOutCubic:function(t){return(t/=.5)<1?.5*t*t*t:.5*((t-=2)*t*t+2)},easeInQuart:function(t){return t*t*t*t},easeOutQuart:function(t){return-1*((t=t/1-1)*t*t*t-1)},easeInOutQuart:function(t){return(t/=.5)<1?.5*t*t*t*t:-0.5*((t-=2)*t*t*t-2)},easeInQuint:function(t){return 1*(t/=1)*t*t*t*t},easeOutQuint:function(t){return 1*((t=t/1-1)*t*t*t*t+1)},easeInOutQuint:function(t){return(t/=.5)<1?.5*t*t*t*t*t:.5*((t-=2)*t*t*t*t+2)},easeInSine:function(t){return-1*Math.cos(t/1*(Math.PI/2))+1},easeOutSine:function(t){return 1*Math.sin(t/1*(Math.PI/2))},easeInOutSine:function(t){return-0.5*(Math.cos(Math.PI*t/1)-1)},easeInExpo:function(t){return 0===t?1:1*Math.pow(2,10*(t/1-1))},easeOutExpo:function(t){return 1===t?1:1*(-Math.pow(2,-10*t/1)+1)},easeInOutExpo:function(t){return 0===t?0:1===t?1:(t/=.5)<1?.5*Math.pow(2,10*(t-1)):.5*(-Math.pow(2,-10*--t)+2)},easeInCirc:function(t){return t>=1?t:-1*(Math.sqrt(1-(t/=1)*t)-1)},easeOutCirc:function(t){return 1*Math.sqrt(1-(t=t/1-1)*t)},easeInOutCirc:function(t){return(t/=.5)<1?-0.5*(Math.sqrt(1-t*t)-1):.5*(Math.sqrt(1-(t-=2)*t)+1)},easeInElastic:function(t){var i=1.70158,e=0,s=1;return 0===t?0:1==(t/=1)?1:(e||(e=.3),s<Math.abs(1)?(s=1,i=e/4):i=e/(2*Math.PI)*Math.asin(1/s),-(s*Math.pow(2,10*(t-=1))*Math.sin(2*(1*t-i)*Math.PI/e)))},easeOutElastic:function(t){var i=1.70158,e=0,s=1;return 0===t?0:1==(t/=1)?1:(e||(e=.3),s<Math.abs(1)?(s=1,i=e/4):i=e/(2*Math.PI)*Math.asin(1/s),s*Math.pow(2,-10*t)*Math.sin(2*(1*t-i)*Math.PI/e)+1)},easeInOutElastic:function(t){var i=1.70158,e=0,s=1;return 0===t?0:2==(t/=.5)?1:(e||(e=.3*1.5),s<Math.abs(1)?(s=1,i=e/4):i=e/(2*Math.PI)*Math.asin(1/s),1>t?-.5*s*Math.pow(2,10*(t-=1))*Math.sin(2*(1*t-i)*Math.PI/e):s*Math.pow(2,-10*(t-=1))*Math.sin(2*(1*t-i)*Math.PI/e)*.5+1)},easeInBack:function(t){var i=1.70158;return 1*(t/=1)*t*((i+1)*t-i)},easeOutBack:function(t){var i=1.70158;return 1*((t=t/1-1)*t*((i+1)*t+i)+1)},easeInOutBack:function(t){var i=1.70158;return(t/=.5)<1?.5*t*t*(((i*=1.525)+1)*t-i):.5*((t-=2)*t*(((i*=1.525)+1)*t+i)+2)},easeInBounce:function(t){return 1-w.easeOutBounce(1-t)},easeOutBounce:function(t){return(t/=1)<1/2.75?7.5625*t*t:2/2.75>t?1*(7.5625*(t-=1.5/2.75)*t+.75):2.5/2.75>t?1*(7.5625*(t-=2.25/2.75)*t+.9375):1*(7.5625*(t-=2.625/2.75)*t+.984375)},easeInOutBounce:function(t){return.5>t?.5*w.easeInBounce(2*t):.5*w.easeOutBounce(2*t-1)+.5}}),b=s.requestAnimFrame=function(){return window.requestAnimationFrame||window.webkitRequestAnimationFrame||window.mozRequestAnimationFrame||window.oRequestAnimationFrame||window.msRequestAnimationFrame||function(t){return window.setTimeout(t,1e3/60)}}(),P=s.cancelAnimFrame=function(){return window.cancelAnimationFrame||window.webkitCancelAnimationFrame||window.mozCancelAnimationFrame||window.oCancelAnimationFrame||window.msCancelAnimationFrame||function(t){return window.clearTimeout(t,1e3/60)}}(),L=(s.animationLoop=function(t,i,e,s,n,o){var a=0,h=w[e]||w.linear,l=function(){a++;var e=a/i,r=h(e);t.call(o,r,e,a),s.call(o,r,e),i>a?o.animationFrame=b(l):n.apply(o)};b(l)},s.getRelativePosition=function(t){var i,e,s=t.originalEvent||t,n=t.currentTarget||t.srcElement,o=n.getBoundingClientRect();return s.touches?(i=s.touches[0].clientX-o.left,e=s.touches[0].clientY-o.top):(i=s.clientX-o.left,e=s.clientY-o.top),{x:i,y:e}},s.addEvent=function(t,i,e){t.addEventListener?t.addEventListener(i,e):t.attachEvent?t.attachEvent("on"+i,e):t["on"+i]=e}),k=s.removeEvent=function(t,i,e){t.removeEventListener?t.removeEventListener(i,e,!1):t.detachEvent?t.detachEvent("on"+i,e):t["on"+i]=c},F=(s.bindEvents=function(t,i,e){t.events||(t.events={}),n(i,function(i){t.events[i]=function(){e.apply(t,arguments)},L(t.chart.canvas,i,t.events[i])})},s.unbindEvents=function(t,i){n(i,function(i,e){k(t.chart.canvas,e,i)})}),R=s.getMaximumWidth=function(t){var i=t.parentNode;return i.clientWidth},T=s.getMaximumHeight=function(t){var i=t.parentNode;return i.clientHeight},A=(s.getMaximumSize=s.getMaximumWidth,s.retinaScale=function(t){var i=t.ctx,e=t.canvas.width,s=t.canvas.height;window.devicePixelRatio&&(i.canvas.style.width=e+"px",i.canvas.style.height=s+"px",i.canvas.height=s*window.devicePixelRatio,i.canvas.width=e*window.devicePixelRatio,i.scale(window.devicePixelRatio,window.devicePixelRatio))}),M=s.clear=function(t){t.ctx.clearRect(0,0,t.width,t.height)},W=s.fontString=function(t,i,e){return i+" "+t+"px "+e},z=s.longestText=function(t,i,e){t.font=i;var s=0;return n(e,function(i){var e=t.measureText(i).width;s=e>s?e:s}),s},B=s.drawRoundedRectangle=function(t,i,e,s,n,o){t.beginPath(),t.moveTo(i+o,e),t.lineTo(i+s-o,e),t.quadraticCurveTo(i+s,e,i+s,e+o),t.lineTo(i+s,e+n-o),t.quadraticCurveTo(i+s,e+n,i+s-o,e+n),t.lineTo(i+o,e+n),t.quadraticCurveTo(i,e+n,i,e+n-o),t.lineTo(i,e+o),t.quadraticCurveTo(i,e,i+o,e),t.closePath()};e.instances={},e.Type=function(t,i,s){this.options=i,this.chart=s,this.id=u(),e.instances[this.id]=this,i.responsive&&this.resize(),this.initialize.call(this,t)},a(e.Type.prototype,{initialize:function(){return this},clear:function(){return M(this.chart),this},stop:function(){return P(this.animationFrame),this},resize:function(t){this.stop();var i=this.chart.canvas,e=R(this.chart.canvas),s=this.options.maintainAspectRatio?e/this.chart.aspectRatio:T(this.chart.canvas);return i.width=this.chart.width=e,i.height=this.chart.height=s,A(this.chart),"function"==typeof t&&t.apply(this,Array.prototype.slice.call(arguments,1)),this},reflow:c,render:function(t){return t&&this.reflow(),this.options.animation&&!t?s.animationLoop(this.draw,this.options.animationSteps,this.options.animationEasing,this.options.onAnimationProgress,this.options.onAnimationComplete,this):(this.draw(),this.options.onAnimationComplete.call(this)),this},generateLegend:function(){return C(this.options.legendTemplate,this)},destroy:function(){this.clear(),F(this,this.events);var t=this.chart.canvas;t.width=this.chart.width,t.height=this.chart.height,t.style.removeProperty?(t.style.removeProperty("width"),t.style.removeProperty("height")):(t.style.removeAttribute("width"),t.style.removeAttribute("height")),delete e.instances[this.id]},showTooltip:function(t,i){"undefined"==typeof this.activeElements&&(this.activeElements=[]);var o=function(t){var i=!1;return t.length!==this.activeElements.length?i=!0:(n(t,function(t,e){t!==this.activeElements[e]&&(i=!0)},this),i)}.call(this,t);if(o||i){if(this.activeElements=t,this.draw(),this.options.customTooltips&&this.options.customTooltips(!1),t.length>0)if(this.datasets&&this.datasets.length>1){for(var a,h,r=this.datasets.length-1;r>=0&&(a=this.datasets[r].points||this.datasets[r].bars||this.datasets[r].segments,h=l(a,t[0]),-1===h);r--);var c=[],u=[],d=function(){var t,i,e,n,o,a=[],l=[],r=[];return s.each(this.datasets,function(i){t=i.points||i.bars||i.segments,t[h]&&t[h].hasValue()&&a.push(t[h])}),s.each(a,function(t){l.push(t.x),r.push(t.y),c.push(s.template(this.options.multiTooltipTemplate,t)),u.push({fill:t._saved.fillColor||t.fillColor,stroke:t._saved.strokeColor||t.strokeColor})},this),o=m(r),e=g(r),n=m(l),i=g(l),{x:n>this.chart.width/2?n:i,y:(o+e)/2}}.call(this,h);new e.MultiTooltip({x:d.x,y:d.y,xPadding:this.options.tooltipXPadding,yPadding:this.options.tooltipYPadding,xOffset:this.options.tooltipXOffset,fillColor:this.options.tooltipFillColor,textColor:this.options.tooltipFontColor,fontFamily:this.options.tooltipFontFamily,fontStyle:this.options.tooltipFontStyle,fontSize:this.options.tooltipFontSize,titleTextColor:this.options.tooltipTitleFontColor,titleFontFamily:this.options.tooltipTitleFontFamily,titleFontStyle:this.options.tooltipTitleFontStyle,titleFontSize:this.options.tooltipTitleFontSize,cornerRadius:this.options.tooltipCornerRadius,labels:c,legendColors:u,legendColorBackground:this.options.multiTooltipKeyBackground,title:t[0].label,chart:this.chart,ctx:this.chart.ctx,custom:this.options.customTooltips}).draw()}else n(t,function(t){var i=t.tooltipPosition();new e.Tooltip({x:Math.round(i.x),y:Math.round(i.y),xPadding:this.options.tooltipXPadding,yPadding:this.options.tooltipYPadding,fillColor:this.options.tooltipFillColor,textColor:this.options.tooltipFontColor,fontFamily:this.options.tooltipFontFamily,fontStyle:this.options.tooltipFontStyle,fontSize:this.options.tooltipFontSize,caretHeight:this.options.tooltipCaretSize,cornerRadius:this.options.tooltipCornerRadius,text:C(this.options.tooltipTemplate,t),chart:this.chart,custom:this.options.customTooltips}).draw()},this);return this}},toBase64Image:function(){return this.chart.canvas.toDataURL.apply(this.chart.canvas,arguments)}}),e.Type.extend=function(t){var i=this,s=function(){return i.apply(this,arguments)};if(s.prototype=o(i.prototype),a(s.prototype,t),s.extend=e.Type.extend,t.name||i.prototype.name){var n=t.name||i.prototype.name,l=e.defaults[i.prototype.name]?o(e.defaults[i.prototype.name]):{};e.defaults[n]=a(l,t.defaults),e.types[n]=s,e.prototype[n]=function(t,i){var o=h(e.defaults.global,e.defaults[n],i||{});return new s(t,o,this)}}else d("Name not provided for this chart, so it hasn't been registered");return i},e.Element=function(t){a(this,t),this.initialize.apply(this,arguments),this.save()},a(e.Element.prototype,{initialize:function(){},restore:function(t){return t?n(t,function(t){this[t]=this._saved[t]},this):a(this,this._saved),this},save:function(){return this._saved=o(this),delete this._saved._saved,this},update:function(t){return n(t,function(t,i){this._saved[i]=this[i],this[i]=t},this),this},transition:function(t,i){return n(t,function(t,e){this[e]=(t-this._saved[e])*i+this._saved[e]},this),this},tooltipPosition:function(){return{x:this.x,y:this.y}},hasValue:function(){return f(this.value)}}),e.Element.extend=r,e.Point=e.Element.extend({display:!0,inRange:function(t,i){var e=this.hitDetectionRadius+this.radius;return Math.pow(t-this.x,2)+Math.pow(i-this.y,2)<Math.pow(e,2)},draw:function(){if(this.display){var t=this.ctx;t.beginPath(),t.arc(this.x,this.y,this.radius,0,2*Math.PI),t.closePath(),t.strokeStyle=this.strokeColor,t.lineWidth=this.strokeWidth,t.fillStyle=this.fillColor,t.fill(),t.stroke()}}}),e.Arc=e.Element.extend({inRange:function(t,i){var e=s.getAngleFromPoint(this,{x:t,y:i}),n=e.angle>=this.startAngle&&e.angle<=this.endAngle,o=e.distance>=this.innerRadius&&e.distance<=this.outerRadius;return n&&o},tooltipPosition:function(){var t=this.startAngle+(this.endAngle-this.startAngle)/2,i=(this.outerRadius-this.innerRadius)/2+this.innerRadius;return{x:this.x+Math.cos(t)*i,y:this.y+Math.sin(t)*i}},draw:function(t){var i=this.ctx;i.beginPath(),i.arc(this.x,this.y,this.outerRadius,this.startAngle,this.endAngle),i.arc(this.x,this.y,this.innerRadius,this.endAngle,this.startAngle,!0),i.closePath(),i.strokeStyle=this.strokeColor,i.lineWidth=this.strokeWidth,i.fillStyle=this.fillColor,i.fill(),i.lineJoin="bevel",this.showStroke&&i.stroke()}}),e.Rectangle=e.Element.extend({draw:function(){var t=this.ctx,i=this.width/2,e=this.x-i,s=this.x+i,n=this.base-(this.base-this.y),o=this.strokeWidth/2;this.showStroke&&(e+=o,s-=o,n+=o),t.beginPath(),t.fillStyle=this.fillColor,t.strokeStyle=this.strokeColor,t.lineWidth=this.strokeWidth,t.moveTo(e,this.base),t.lineTo(e,n),t.lineTo(s,n),t.lineTo(s,this.base),t.fill(),this.showStroke&&t.stroke()},height:function(){return this.base-this.y},inRange:function(t,i){return t>=this.x-this.width/2&&t<=this.x+this.width/2&&i>=this.y&&i<=this.base}}),e.Tooltip=e.Element.extend({draw:function(){var t=this.chart.ctx;t.font=W(this.fontSize,this.fontStyle,this.fontFamily),this.xAlign="center",this.yAlign="above";var i=this.caretPadding=2,e=t.measureText(this.text).width+2*this.xPadding,s=this.fontSize+2*this.yPadding,n=s+this.caretHeight+i;this.x+e/2>this.chart.width?this.xAlign="left":this.x-e/2<0&&(this.xAlign="right"),this.y-n<0&&(this.yAlign="below");var o=this.x-e/2,a=this.y-n;if(t.fillStyle=this.fillColor,this.custom)this.custom(this);else{switch(this.yAlign){case"above":t.beginPath(),t.moveTo(this.x,this.y-i),t.lineTo(this.x+this.caretHeight,this.y-(i+this.caretHeight)),t.lineTo(this.x-this.caretHeight,this.y-(i+this.caretHeight)),t.closePath(),t.fill();break;case"below":a=this.y+i+this.caretHeight,t.beginPath(),t.moveTo(this.x,this.y+i),t.lineTo(this.x+this.caretHeight,this.y+i+this.caretHeight),t.lineTo(this.x-this.caretHeight,this.y+i+this.caretHeight),t.closePath(),t.fill()}switch(this.xAlign){case"left":o=this.x-e+(this.cornerRadius+this.caretHeight);break;case"right":o=this.x-(this.cornerRadius+this.caretHeight)}B(t,o,a,e,s,this.cornerRadius),t.fill(),t.fillStyle=this.textColor,t.textAlign="center",t.textBaseline="middle",t.fillText(this.text,o+e/2,a+s/2)}}}),e.MultiTooltip=e.Element.extend({initialize:function(){this.font=W(this.fontSize,this.fontStyle,this.fontFamily),this.titleFont=W(this.titleFontSize,this.titleFontStyle,this.titleFontFamily),this.height=this.labels.length*this.fontSize+(this.labels.length-1)*(this.fontSize/2)+2*this.yPadding+1.5*this.titleFontSize,this.ctx.font=this.titleFont;var t=this.ctx.measureText(this.title).width,i=z(this.ctx,this.font,this.labels)+this.fontSize+3,e=g([i,t]);this.width=e+2*this.xPadding;var s=this.height/2;this.y-s<0?this.y=s:this.y+s>this.chart.height&&(this.y=this.chart.height-s),this.x>this.chart.width/2?this.x-=this.xOffset+this.width:this.x+=this.xOffset},getLineHeight:function(t){var i=this.y-this.height/2+this.yPadding,e=t-1;return 0===t?i+this.titleFontSize/2:i+(1.5*this.fontSize*e+this.fontSize/2)+1.5*this.titleFontSize},draw:function(){if(this.custom)this.custom(this);else{B(this.ctx,this.x,this.y-this.height/2,this.width,this.height,this.cornerRadius);var t=this.ctx;t.fillStyle=this.fillColor,t.fill(),t.closePath(),t.textAlign="left",t.textBaseline="middle",t.fillStyle=this.titleTextColor,t.font=this.titleFont,t.fillText(this.title,this.x+this.xPadding,this.getLineHeight(0)),t.font=this.font,s.each(this.labels,function(i,e){t.fillStyle=this.textColor,t.fillText(i,this.x+this.xPadding+this.fontSize+3,this.getLineHeight(e+1)),t.fillStyle=this.legendColorBackground,t.fillRect(this.x+this.xPadding,this.getLineHeight(e+1)-this.fontSize/2,this.fontSize,this.fontSize),t.fillStyle=this.legendColors[e].fill,t.fillRect(this.x+this.xPadding,this.getLineHeight(e+1)-this.fontSize/2,this.fontSize,this.fontSize)},this)}}}),e.Scale=e.Element.extend({initialize:function(){this.fit()},buildYLabels:function(){this.yLabels=[];for(var t=v(this.stepValue),i=0;i<=this.steps;i++)this.yLabels.push(C(this.templateString,{value:(this.min+i*this.stepValue).toFixed(t)}));this.yLabelWidth=this.display&&this.showLabels?z(this.ctx,this.font,this.yLabels):0},addXLabel:function(t){this.xLabels.push(t),this.valuesCount++,this.fit()},removeXLabel:function(){this.xLabels.shift(),this.valuesCount--,this.fit()},fit:function(){this.startPoint=this.display?this.fontSize:0,this.endPoint=this.display?this.height-1.5*this.fontSize-5:this.height,this.startPoint+=this.padding,this.endPoint-=this.padding;var t,i=this.endPoint-this.startPoint;for(this.calculateYRange(i),this.buildYLabels(),this.calculateXLabelRotation();i>this.endPoint-this.startPoint;)i=this.endPoint-this.startPoint,t=this.yLabelWidth,this.calculateYRange(i),this.buildYLabels(),t<this.yLabelWidth&&this.calculateXLabelRotation()},calculateXLabelRotation:function(){this.ctx.font=this.font;var t,i,e=this.ctx.measureText(this.xLabels[0]).width,s=this.ctx.measureText(this.xLabels[this.xLabels.length-1]).width;if(this.xScalePaddingRight=s/2+3,this.xScalePaddingLeft=e/2>this.yLabelWidth+10?e/2:this.yLabelWidth+10,this.xLabelRotation=0,this.display){var n,o=z(this.ctx,this.font,this.xLabels);this.xLabelWidth=o;for(var a=Math.floor(this.calculateX(1)-this.calculateX(0))-6;this.xLabelWidth>a&&0===this.xLabelRotation||this.xLabelWidth>a&&this.xLabelRotation<=90&&this.xLabelRotation>0;)n=Math.cos(S(this.xLabelRotation)),t=n*e,i=n*s,t+this.fontSize/2>this.yLabelWidth+8&&(this.xScalePaddingLeft=t+this.fontSize/2),this.xScalePaddingRight=this.fontSize/2,this.xLabelRotation++,this.xLabelWidth=n*o;this.xLabelRotation>0&&(this.endPoint-=Math.sin(S(this.xLabelRotation))*o+3)}else this.xLabelWidth=0,this.xScalePaddingRight=this.padding,this.xScalePaddingLeft=this.padding},calculateYRange:c,drawingArea:function(){return this.startPoint-this.endPoint},calculateY:function(t){var i=this.drawingArea()/(this.min-this.max);return this.endPoint-i*(t-this.min)},calculateX:function(t){var i=(this.xLabelRotation>0,this.width-(this.xScalePaddingLeft+this.xScalePaddingRight)),e=i/Math.max(this.valuesCount-(this.offsetGridLines?0:1),1),s=e*t+this.xScalePaddingLeft;return this.offsetGridLines&&(s+=e/2),Math.round(s)},update:function(t){s.extend(this,t),this.fit()},draw:function(){var t=this.ctx,i=(this.endPoint-this.startPoint)/this.steps,e=Math.round(this.xScalePaddingLeft);this.display&&(t.fillStyle=this.textColor,t.font=this.font,n(this.yLabels,function(n,o){var a=this.endPoint-i*o,h=Math.round(a),l=this.showHorizontalLines;t.textAlign="right",t.textBaseline="middle",this.showLabels&&t.fillText(n,e-10,a),0!==o||l||(l=!0),l&&t.beginPath(),o>0?(t.lineWidth=this.gridLineWidth,t.strokeStyle=this.gridLineColor):(t.lineWidth=this.lineWidth,t.strokeStyle=this.lineColor),h+=s.aliasPixel(t.lineWidth),l&&(t.moveTo(e,h),t.lineTo(this.width,h),t.stroke(),t.closePath()),t.lineWidth=this.lineWidth,t.strokeStyle=this.lineColor,t.beginPath(),t.moveTo(e-5,h),t.lineTo(e,h),t.stroke(),t.closePath()},this),n(this.xLabels,function(i,e){var s=this.calculateX(e)+x(this.lineWidth),n=this.calculateX(e-(this.offsetGridLines?.5:0))+x(this.lineWidth),o=this.xLabelRotation>0,a=this.showVerticalLines;0!==e||a||(a=!0),a&&t.beginPath(),e>0?(t.lineWidth=this.gridLineWidth,t.strokeStyle=this.gridLineColor):(t.lineWidth=this.lineWidth,t.strokeStyle=this.lineColor),a&&(t.moveTo(n,this.endPoint),t.lineTo(n,this.startPoint-3),t.stroke(),t.closePath()),t.lineWidth=this.lineWidth,t.strokeStyle=this.lineColor,t.beginPath(),t.moveTo(n,this.endPoint),t.lineTo(n,this.endPoint+5),t.stroke(),t.closePath(),t.save(),t.translate(s,o?this.endPoint+12:this.endPoint+8),t.rotate(-1*S(this.xLabelRotation)),t.font=this.font,t.textAlign=o?"right":"center",t.textBaseline=o?"middle":"top",t.fillText(i,0,0),t.restore()},this))}}),e.RadialScale=e.Element.extend({initialize:function(){this.size=m([this.height,this.width]),this.drawingArea=this.display?this.size/2-(this.fontSize/2+this.backdropPaddingY):this.size/2},calculateCenterOffset:function(t){var i=this.drawingArea/(this.max-this.min);return(t-this.min)*i},update:function(){this.lineArc?this.drawingArea=this.display?this.size/2-(this.fontSize/2+this.backdropPaddingY):this.size/2:this.setScaleSize(),this.buildYLabels()},buildYLabels:function(){this.yLabels=[];for(var t=v(this.stepValue),i=0;i<=this.steps;i++)this.yLabels.push(C(this.templateString,{value:(this.min+i*this.stepValue).toFixed(t)}))},getCircumference:function(){return 2*Math.PI/this.valuesCount},setScaleSize:function(){var t,i,e,s,n,o,a,h,l,r,c,u,d=m([this.height/2-this.pointLabelFontSize-5,this.width/2]),p=this.width,g=0;for(this.ctx.font=W(this.pointLabelFontSize,this.pointLabelFontStyle,this.pointLabelFontFamily),i=0;i<this.valuesCount;i++)t=this.getPointPosition(i,d),e=this.ctx.measureText(C(this.templateString,{value:this.labels[i]})).width+5,0===i||i===this.valuesCount/2?(s=e/2,t.x+s>p&&(p=t.x+s,n=i),t.x-s<g&&(g=t.x-s,a=i)):i<this.valuesCount/2?t.x+e>p&&(p=t.x+e,n=i):i>this.valuesCount/2&&t.x-e<g&&(g=t.x-e,a=i);l=g,r=Math.ceil(p-this.width),o=this.getIndexAngle(n),h=this.getIndexAngle(a),c=r/Math.sin(o+Math.PI/2),u=l/Math.sin(h+Math.PI/2),c=f(c)?c:0,u=f(u)?u:0,this.drawingArea=d-(u+c)/2,this.setCenterPoint(u,c)},setCenterPoint:function(t,i){var e=this.width-i-this.drawingArea,s=t+this.drawingArea;this.xCenter=(s+e)/2,this.yCenter=this.height/2},getIndexAngle:function(t){var i=2*Math.PI/this.valuesCount;return t*i-Math.PI/2},getPointPosition:function(t,i){var e=this.getIndexAngle(t);return{x:Math.cos(e)*i+this.xCenter,y:Math.sin(e)*i+this.yCenter}},draw:function(){if(this.display){var t=this.ctx;if(n(this.yLabels,function(i,e){if(e>0){var s,n=e*(this.drawingArea/this.steps),o=this.yCenter-n;if(this.lineWidth>0)if(t.strokeStyle=this.lineColor,t.lineWidth=this.lineWidth,this.lineArc)t.beginPath(),t.arc(this.xCenter,this.yCenter,n,0,2*Math.PI),t.closePath(),t.stroke();else{t.beginPath();for(var a=0;a<this.valuesCount;a++)s=this.getPointPosition(a,this.calculateCenterOffset(this.min+e*this.stepValue)),0===a?t.moveTo(s.x,s.y):t.lineTo(s.x,s.y);t.closePath(),t.stroke()}if(this.showLabels){if(t.font=W(this.fontSize,this.fontStyle,this.fontFamily),this.showLabelBackdrop){var h=t.measureText(i).width;t.fillStyle=this.backdropColor,t.fillRect(this.xCenter-h/2-this.backdropPaddingX,o-this.fontSize/2-this.backdropPaddingY,h+2*this.backdropPaddingX,this.fontSize+2*this.backdropPaddingY)}t.textAlign="center",t.textBaseline="middle",t.fillStyle=this.fontColor,t.fillText(i,this.xCenter,o)}}},this),!this.lineArc){t.lineWidth=this.angleLineWidth,t.strokeStyle=this.angleLineColor;for(var i=this.valuesCount-1;i>=0;i--){if(this.angleLineWidth>0){var e=this.getPointPosition(i,this.calculateCenterOffset(this.max));t.beginPath(),t.moveTo(this.xCenter,this.yCenter),t.lineTo(e.x,e.y),t.stroke(),t.closePath()}var s=this.getPointPosition(i,this.calculateCenterOffset(this.max)+5);t.font=W(this.pointLabelFontSize,this.pointLabelFontStyle,this.pointLabelFontFamily),t.fillStyle=this.pointLabelFontColor;var o=this.labels.length,a=this.labels.length/2,h=a/2,l=h>i||i>o-h,r=i===h||i===o-h;t.textAlign=0===i?"center":i===a?"center":a>i?"left":"right",t.textBaseline=r?"middle":l?"bottom":"top",t.fillText(this.labels[i],s.x,s.y)}}}}}),s.addEvent(window,"resize",function(){var t;return function(){clearTimeout(t),t=setTimeout(function(){n(e.instances,function(t){t.options.responsive&&t.resize(t.render,!0)})},50)}}()),p?define(function(){return e}):"object"==typeof module&&module.exports&&(module.exports=e),t.Chart=e,e.noConflict=function(){return t.Chart=i,e}}).call(this),function(){"use strict";var t=this,i=t.Chart,e=i.helpers,s={scaleBeginAtZero:!0,scaleShowGridLines:!0,scaleGridLineColor:"rgba(0,0,0,.05)",scaleGridLineWidth:1,scaleShowHorizontalLines:!0,scaleShowVerticalLines:!0,barShowStroke:!0,barStrokeWidth:2,barValueSpacing:5,barDatasetSpacing:1,legendTemplate:'<ul class="<%=name.toLowerCase()%>-legend"><% for (var i=0; i<datasets.length; i++){%><li><span style="background-color:<%=datasets[i].fillColor%>"></span><%if(datasets[i].label){%><%=datasets[i].label%><%}%></li><%}%></ul>'};i.Type.extend({name:"Bar",defaults:s,initialize:function(t){var s=this.options;this.ScaleClass=i.Scale.extend({offsetGridLines:!0,calculateBarX:function(t,i,e){var n=this.calculateBaseWidth(),o=this.calculateX(e)-n/2,a=this.calculateBarWidth(t);return o+a*i+i*s.barDatasetSpacing+a/2},calculateBaseWidth:function(){return this.calculateX(1)-this.calculateX(0)-2*s.barValueSpacing},calculateBarWidth:function(t){var i=this.calculateBaseWidth()-(t-1)*s.barDatasetSpacing;return i/t}}),this.datasets=[],this.options.showTooltips&&e.bindEvents(this,this.options.tooltipEvents,function(t){var i="mouseout"!==t.type?this.getBarsAtEvent(t):[];this.eachBars(function(t){t.restore(["fillColor","strokeColor"])}),e.each(i,function(t){t.fillColor=t.highlightFill,t.strokeColor=t.highlightStroke}),this.showTooltip(i)}),this.BarClass=i.Rectangle.extend({strokeWidth:this.options.barStrokeWidth,showStroke:this.options.barShowStroke,ctx:this.chart.ctx}),e.each(t.datasets,function(i){var s={label:i.label||null,fillColor:i.fillColor,strokeColor:i.strokeColor,bars:[]};this.datasets.push(s),e.each(i.data,function(e,n){s.bars.push(new this.BarClass({value:e,label:t.labels[n],datasetLabel:i.label,strokeColor:i.strokeColor,fillColor:i.fillColor,highlightFill:i.highlightFill||i.fillColor,highlightStroke:i.highlightStroke||i.strokeColor}))},this)},this),this.buildScale(t.labels),this.BarClass.prototype.base=this.scale.endPoint,this.eachBars(function(t,i,s){e.extend(t,{width:this.scale.calculateBarWidth(this.datasets.length),x:this.scale.calculateBarX(this.datasets.length,s,i),y:this.scale.endPoint}),t.save()},this),this.render()},update:function(){this.scale.update(),e.each(this.activeElements,function(t){t.restore(["fillColor","strokeColor"])}),this.eachBars(function(t){t.save()}),this.render()},eachBars:function(t){e.each(this.datasets,function(i,s){e.each(i.bars,t,this,s)},this)},getBarsAtEvent:function(t){for(var i,s=[],n=e.getRelativePosition(t),o=function(t){s.push(t.bars[i])},a=0;a<this.datasets.length;a++)for(i=0;i<this.datasets[a].bars.length;i++)if(this.datasets[a].bars[i].inRange(n.x,n.y))return e.each(this.datasets,o),s;return s},buildScale:function(t){var i=this,s=function(){var t=[];return i.eachBars(function(i){t.push(i.value)}),t},n={templateString:this.options.scaleLabel,height:this.chart.height,width:this.chart.width,ctx:this.chart.ctx,textColor:this.options.scaleFontColor,fontSize:this.options.scaleFontSize,fontStyle:this.options.scaleFontStyle,fontFamily:this.options.scaleFontFamily,valuesCount:t.length,beginAtZero:this.options.scaleBeginAtZero,integersOnly:this.options.scaleIntegersOnly,calculateYRange:function(t){var i=e.calculateScaleRange(s(),t,this.fontSize,this.beginAtZero,this.integersOnly);e.extend(this,i)},xLabels:t,font:e.fontString(this.options.scaleFontSize,this.options.scaleFontStyle,this.options.scaleFontFamily),lineWidth:this.options.scaleLineWidth,lineColor:this.options.scaleLineColor,showHorizontalLines:this.options.scaleShowHorizontalLines,showVerticalLines:this.options.scaleShowVerticalLines,gridLineWidth:this.options.scaleShowGridLines?this.options.scaleGridLineWidth:0,gridLineColor:this.options.scaleShowGridLines?this.options.scaleGridLineColor:"rgba(0,0,0,0)",padding:this.options.showScale?0:this.options.barShowStroke?this.options.barStrokeWidth:0,showLabels:this.options.scaleShowLabels,display:this.options.showScale};this.options.scaleOverride&&e.extend(n,{calculateYRange:e.noop,steps:this.options.scaleSteps,stepValue:this.options.scaleStepWidth,min:this.options.scaleStartValue,max:this.options.scaleStartValue+this.options.scaleSteps*this.options.scaleStepWidth}),this.scale=new this.ScaleClass(n)},addData:function(t,i){e.each(t,function(t,e){this.datasets[e].bars.push(new this.BarClass({value:t,label:i,x:this.scale.calculateBarX(this.datasets.length,e,this.scale.valuesCount+1),y:this.scale.endPoint,width:this.scale.calculateBarWidth(this.datasets.length),base:this.scale.endPoint,strokeColor:this.datasets[e].strokeColor,fillColor:this.datasets[e].fillColor}))
    },this),this.scale.addXLabel(i),this.update()},removeData:function(){this.scale.removeXLabel(),e.each(this.datasets,function(t){t.bars.shift()},this),this.update()},reflow:function(){e.extend(this.BarClass.prototype,{y:this.scale.endPoint,base:this.scale.endPoint});var t=e.extend({height:this.chart.height,width:this.chart.width});this.scale.update(t)},draw:function(t){var i=t||1;this.clear();this.chart.ctx;this.scale.draw(i),e.each(this.datasets,function(t,s){e.each(t.bars,function(t,e){t.hasValue()&&(t.base=this.scale.endPoint,t.transition({x:this.scale.calculateBarX(this.datasets.length,s,e),y:this.scale.calculateY(t.value),width:this.scale.calculateBarWidth(this.datasets.length)},i).draw())},this)},this)}})}.call(this),function(){"use strict";var t=this,i=t.Chart,e=i.helpers,s={segmentShowStroke:!0,segmentStrokeColor:"#fff",segmentStrokeWidth:2,percentageInnerCutout:50,animationSteps:100,animationEasing:"easeOutBounce",animateRotate:!0,animateScale:!1,legendTemplate:'<ul class="<%=name.toLowerCase()%>-legend"><% for (var i=0; i<segments.length; i++){%><li><span style="background-color:<%=segments[i].fillColor%>"></span><%if(segments[i].label){%><%=segments[i].label%><%}%></li><%}%></ul>'};i.Type.extend({name:"Doughnut",defaults:s,initialize:function(t){this.segments=[],this.outerRadius=(e.min([this.chart.width,this.chart.height])-this.options.segmentStrokeWidth/2)/2,this.SegmentArc=i.Arc.extend({ctx:this.chart.ctx,x:this.chart.width/2,y:this.chart.height/2}),this.options.showTooltips&&e.bindEvents(this,this.options.tooltipEvents,function(t){var i="mouseout"!==t.type?this.getSegmentsAtEvent(t):[];e.each(this.segments,function(t){t.restore(["fillColor"])}),e.each(i,function(t){t.fillColor=t.highlightColor}),this.showTooltip(i)}),this.calculateTotal(t),e.each(t,function(t,i){this.addData(t,i,!0)},this),this.render()},getSegmentsAtEvent:function(t){var i=[],s=e.getRelativePosition(t);return e.each(this.segments,function(t){t.inRange(s.x,s.y)&&i.push(t)},this),i},addData:function(t,i,e){var s=i||this.segments.length;this.segments.splice(s,0,new this.SegmentArc({value:t.value,outerRadius:this.options.animateScale?0:this.outerRadius,innerRadius:this.options.animateScale?0:this.outerRadius/100*this.options.percentageInnerCutout,fillColor:t.color,highlightColor:t.highlight||t.color,showStroke:this.options.segmentShowStroke,strokeWidth:this.options.segmentStrokeWidth,strokeColor:this.options.segmentStrokeColor,startAngle:1.5*Math.PI,circumference:this.options.animateRotate?0:this.calculateCircumference(t.value),label:t.label})),e||(this.reflow(),this.update())},calculateCircumference:function(t){return 2*Math.PI*(Math.abs(t)/this.total)},calculateTotal:function(t){this.total=0,e.each(t,function(t){this.total+=Math.abs(t.value)},this)},update:function(){this.calculateTotal(this.segments),e.each(this.activeElements,function(t){t.restore(["fillColor"])}),e.each(this.segments,function(t){t.save()}),this.render()},removeData:function(t){var i=e.isNumber(t)?t:this.segments.length-1;this.segments.splice(i,1),this.reflow(),this.update()},reflow:function(){e.extend(this.SegmentArc.prototype,{x:this.chart.width/2,y:this.chart.height/2}),this.outerRadius=(e.min([this.chart.width,this.chart.height])-this.options.segmentStrokeWidth/2)/2,e.each(this.segments,function(t){t.update({outerRadius:this.outerRadius,innerRadius:this.outerRadius/100*this.options.percentageInnerCutout})},this)},draw:function(t){var i=t?t:1;this.clear(),e.each(this.segments,function(t,e){t.transition({circumference:this.calculateCircumference(t.value),outerRadius:this.outerRadius,innerRadius:this.outerRadius/100*this.options.percentageInnerCutout},i),t.endAngle=t.startAngle+t.circumference,t.draw(),0===e&&(t.startAngle=1.5*Math.PI),e<this.segments.length-1&&(this.segments[e+1].startAngle=t.endAngle)},this)}}),i.types.Doughnut.extend({name:"Pie",defaults:e.merge(s,{percentageInnerCutout:0})})}.call(this),function(){"use strict";var t=this,i=t.Chart,e=i.helpers,s={scaleShowGridLines:!0,scaleGridLineColor:"rgba(0,0,0,.05)",scaleGridLineWidth:1,scaleShowHorizontalLines:!0,scaleShowVerticalLines:!0,bezierCurve:!0,bezierCurveTension:.4,pointDot:!0,pointDotRadius:4,pointDotStrokeWidth:1,pointHitDetectionRadius:20,datasetStroke:!0,datasetStrokeWidth:2,datasetFill:!0,legendTemplate:'<ul class="<%=name.toLowerCase()%>-legend"><% for (var i=0; i<datasets.length; i++){%><li><span style="background-color:<%=datasets[i].strokeColor%>"></span><%if(datasets[i].label){%><%=datasets[i].label%><%}%></li><%}%></ul>'};i.Type.extend({name:"Line",defaults:s,initialize:function(t){this.PointClass=i.Point.extend({strokeWidth:this.options.pointDotStrokeWidth,radius:this.options.pointDotRadius,display:this.options.pointDot,hitDetectionRadius:this.options.pointHitDetectionRadius,ctx:this.chart.ctx,inRange:function(t){return Math.pow(t-this.x,2)<Math.pow(this.radius+this.hitDetectionRadius,2)}}),this.datasets=[],this.options.showTooltips&&e.bindEvents(this,this.options.tooltipEvents,function(t){var i="mouseout"!==t.type?this.getPointsAtEvent(t):[];this.eachPoints(function(t){t.restore(["fillColor","strokeColor"])}),e.each(i,function(t){t.fillColor=t.highlightFill,t.strokeColor=t.highlightStroke}),this.showTooltip(i)}),e.each(t.datasets,function(i){var s={label:i.label||null,fillColor:i.fillColor,strokeColor:i.strokeColor,pointColor:i.pointColor,pointStrokeColor:i.pointStrokeColor,points:[]};this.datasets.push(s),e.each(i.data,function(e,n){s.points.push(new this.PointClass({value:e,label:t.labels[n],datasetLabel:i.label,strokeColor:i.pointStrokeColor,fillColor:i.pointColor,highlightFill:i.pointHighlightFill||i.pointColor,highlightStroke:i.pointHighlightStroke||i.pointStrokeColor}))},this),this.buildScale(t.labels),this.eachPoints(function(t,i){e.extend(t,{x:this.scale.calculateX(i),y:this.scale.endPoint}),t.save()},this)},this),this.render()},update:function(){this.scale.update(),e.each(this.activeElements,function(t){t.restore(["fillColor","strokeColor"])}),this.eachPoints(function(t){t.save()}),this.render()},eachPoints:function(t){e.each(this.datasets,function(i){e.each(i.points,t,this)},this)},getPointsAtEvent:function(t){var i=[],s=e.getRelativePosition(t);return e.each(this.datasets,function(t){e.each(t.points,function(t){t.inRange(s.x,s.y)&&i.push(t)})},this),i},buildScale:function(t){var s=this,n=function(){var t=[];return s.eachPoints(function(i){t.push(i.value)}),t},o={templateString:this.options.scaleLabel,height:this.chart.height,width:this.chart.width,ctx:this.chart.ctx,textColor:this.options.scaleFontColor,fontSize:this.options.scaleFontSize,fontStyle:this.options.scaleFontStyle,fontFamily:this.options.scaleFontFamily,valuesCount:t.length,beginAtZero:this.options.scaleBeginAtZero,integersOnly:this.options.scaleIntegersOnly,calculateYRange:function(t){var i=e.calculateScaleRange(n(),t,this.fontSize,this.beginAtZero,this.integersOnly);e.extend(this,i)},xLabels:t,font:e.fontString(this.options.scaleFontSize,this.options.scaleFontStyle,this.options.scaleFontFamily),lineWidth:this.options.scaleLineWidth,lineColor:this.options.scaleLineColor,showHorizontalLines:this.options.scaleShowHorizontalLines,showVerticalLines:this.options.scaleShowVerticalLines,gridLineWidth:this.options.scaleShowGridLines?this.options.scaleGridLineWidth:0,gridLineColor:this.options.scaleShowGridLines?this.options.scaleGridLineColor:"rgba(0,0,0,0)",padding:this.options.showScale?0:this.options.pointDotRadius+this.options.pointDotStrokeWidth,showLabels:this.options.scaleShowLabels,display:this.options.showScale};this.options.scaleOverride&&e.extend(o,{calculateYRange:e.noop,steps:this.options.scaleSteps,stepValue:this.options.scaleStepWidth,min:this.options.scaleStartValue,max:this.options.scaleStartValue+this.options.scaleSteps*this.options.scaleStepWidth}),this.scale=new i.Scale(o)},addData:function(t,i){e.each(t,function(t,e){this.datasets[e].points.push(new this.PointClass({value:t,label:i,x:this.scale.calculateX(this.scale.valuesCount+1),y:this.scale.endPoint,strokeColor:this.datasets[e].pointStrokeColor,fillColor:this.datasets[e].pointColor}))},this),this.scale.addXLabel(i),this.update()},removeData:function(){this.scale.removeXLabel(),e.each(this.datasets,function(t){t.points.shift()},this),this.update()},reflow:function(){var t=e.extend({height:this.chart.height,width:this.chart.width});this.scale.update(t)},draw:function(t){var i=t||1;this.clear();var s=this.chart.ctx,n=function(t){return null!==t.value},o=function(t,i,s){return e.findNextWhere(i,n,s)||t},a=function(t,i,s){return e.findPreviousWhere(i,n,s)||t};this.scale.draw(i),e.each(this.datasets,function(t){var h=e.where(t.points,n);e.each(t.points,function(t,e){t.hasValue()&&t.transition({y:this.scale.calculateY(t.value),x:this.scale.calculateX(e)},i)},this),this.options.bezierCurve&&e.each(h,function(t,i){var s=i>0&&i<h.length-1?this.options.bezierCurveTension:0;t.controlPoints=e.splineCurve(a(t,h,i),t,o(t,h,i),s),t.controlPoints.outer.y>this.scale.endPoint?t.controlPoints.outer.y=this.scale.endPoint:t.controlPoints.outer.y<this.scale.startPoint&&(t.controlPoints.outer.y=this.scale.startPoint),t.controlPoints.inner.y>this.scale.endPoint?t.controlPoints.inner.y=this.scale.endPoint:t.controlPoints.inner.y<this.scale.startPoint&&(t.controlPoints.inner.y=this.scale.startPoint)},this),s.lineWidth=this.options.datasetStrokeWidth,s.strokeStyle=t.strokeColor,s.beginPath(),e.each(h,function(t,i){if(0===i)s.moveTo(t.x,t.y);else if(this.options.bezierCurve){var e=a(t,h,i);s.bezierCurveTo(e.controlPoints.outer.x,e.controlPoints.outer.y,t.controlPoints.inner.x,t.controlPoints.inner.y,t.x,t.y)}else s.lineTo(t.x,t.y)},this),s.stroke(),this.options.datasetFill&&h.length>0&&(s.lineTo(h[h.length-1].x,this.scale.endPoint),s.lineTo(h[0].x,this.scale.endPoint),s.fillStyle=t.fillColor,s.closePath(),s.fill()),e.each(h,function(t){t.draw()})},this)}})}.call(this),function(){"use strict";var t=this,i=t.Chart,e=i.helpers,s={scaleShowLabelBackdrop:!0,scaleBackdropColor:"rgba(255,255,255,0.75)",scaleBeginAtZero:!0,scaleBackdropPaddingY:2,scaleBackdropPaddingX:2,scaleShowLine:!0,segmentShowStroke:!0,segmentStrokeColor:"#fff",segmentStrokeWidth:2,animationSteps:100,animationEasing:"easeOutBounce",animateRotate:!0,animateScale:!1,legendTemplate:'<ul class="<%=name.toLowerCase()%>-legend"><% for (var i=0; i<segments.length; i++){%><li><span style="background-color:<%=segments[i].fillColor%>"></span><%if(segments[i].label){%><%=segments[i].label%><%}%></li><%}%></ul>'};i.Type.extend({name:"PolarArea",defaults:s,initialize:function(t){this.segments=[],this.SegmentArc=i.Arc.extend({showStroke:this.options.segmentShowStroke,strokeWidth:this.options.segmentStrokeWidth,strokeColor:this.options.segmentStrokeColor,ctx:this.chart.ctx,innerRadius:0,x:this.chart.width/2,y:this.chart.height/2}),this.scale=new i.RadialScale({display:this.options.showScale,fontStyle:this.options.scaleFontStyle,fontSize:this.options.scaleFontSize,fontFamily:this.options.scaleFontFamily,fontColor:this.options.scaleFontColor,showLabels:this.options.scaleShowLabels,showLabelBackdrop:this.options.scaleShowLabelBackdrop,backdropColor:this.options.scaleBackdropColor,backdropPaddingY:this.options.scaleBackdropPaddingY,backdropPaddingX:this.options.scaleBackdropPaddingX,lineWidth:this.options.scaleShowLine?this.options.scaleLineWidth:0,lineColor:this.options.scaleLineColor,lineArc:!0,width:this.chart.width,height:this.chart.height,xCenter:this.chart.width/2,yCenter:this.chart.height/2,ctx:this.chart.ctx,templateString:this.options.scaleLabel,valuesCount:t.length}),this.updateScaleRange(t),this.scale.update(),e.each(t,function(t,i){this.addData(t,i,!0)},this),this.options.showTooltips&&e.bindEvents(this,this.options.tooltipEvents,function(t){var i="mouseout"!==t.type?this.getSegmentsAtEvent(t):[];e.each(this.segments,function(t){t.restore(["fillColor"])}),e.each(i,function(t){t.fillColor=t.highlightColor}),this.showTooltip(i)}),this.render()},getSegmentsAtEvent:function(t){var i=[],s=e.getRelativePosition(t);return e.each(this.segments,function(t){t.inRange(s.x,s.y)&&i.push(t)},this),i},addData:function(t,i,e){var s=i||this.segments.length;this.segments.splice(s,0,new this.SegmentArc({fillColor:t.color,highlightColor:t.highlight||t.color,label:t.label,value:t.value,outerRadius:this.options.animateScale?0:this.scale.calculateCenterOffset(t.value),circumference:this.options.animateRotate?0:this.scale.getCircumference(),startAngle:1.5*Math.PI})),e||(this.reflow(),this.update())},removeData:function(t){var i=e.isNumber(t)?t:this.segments.length-1;this.segments.splice(i,1),this.reflow(),this.update()},calculateTotal:function(t){this.total=0,e.each(t,function(t){this.total+=t.value},this),this.scale.valuesCount=this.segments.length},updateScaleRange:function(t){var i=[];e.each(t,function(t){i.push(t.value)});var s=this.options.scaleOverride?{steps:this.options.scaleSteps,stepValue:this.options.scaleStepWidth,min:this.options.scaleStartValue,max:this.options.scaleStartValue+this.options.scaleSteps*this.options.scaleStepWidth}:e.calculateScaleRange(i,e.min([this.chart.width,this.chart.height])/2,this.options.scaleFontSize,this.options.scaleBeginAtZero,this.options.scaleIntegersOnly);e.extend(this.scale,s,{size:e.min([this.chart.width,this.chart.height]),xCenter:this.chart.width/2,yCenter:this.chart.height/2})},update:function(){this.calculateTotal(this.segments),e.each(this.segments,function(t){t.save()}),this.reflow(),this.render()},reflow:function(){e.extend(this.SegmentArc.prototype,{x:this.chart.width/2,y:this.chart.height/2}),this.updateScaleRange(this.segments),this.scale.update(),e.extend(this.scale,{xCenter:this.chart.width/2,yCenter:this.chart.height/2}),e.each(this.segments,function(t){t.update({outerRadius:this.scale.calculateCenterOffset(t.value)})},this)},draw:function(t){var i=t||1;this.clear(),e.each(this.segments,function(t,e){t.transition({circumference:this.scale.getCircumference(),outerRadius:this.scale.calculateCenterOffset(t.value)},i),t.endAngle=t.startAngle+t.circumference,0===e&&(t.startAngle=1.5*Math.PI),e<this.segments.length-1&&(this.segments[e+1].startAngle=t.endAngle),t.draw()},this),this.scale.draw()}})}.call(this),function(){"use strict";var t=this,i=t.Chart,e=i.helpers;i.Type.extend({name:"Radar",defaults:{scaleShowLine:!0,angleShowLineOut:!0,scaleShowLabels:!1,scaleBeginAtZero:!0,angleLineColor:"rgba(0,0,0,.1)",angleLineWidth:1,pointLabelFontFamily:"'Arial'",pointLabelFontStyle:"normal",pointLabelFontSize:10,pointLabelFontColor:"#666",pointDot:!0,pointDotRadius:3,pointDotStrokeWidth:1,pointHitDetectionRadius:20,datasetStroke:!0,datasetStrokeWidth:2,datasetFill:!0,legendTemplate:'<ul class="<%=name.toLowerCase()%>-legend"><% for (var i=0; i<datasets.length; i++){%><li><span style="background-color:<%=datasets[i].strokeColor%>"></span><%if(datasets[i].label){%><%=datasets[i].label%><%}%></li><%}%></ul>'},initialize:function(t){this.PointClass=i.Point.extend({strokeWidth:this.options.pointDotStrokeWidth,radius:this.options.pointDotRadius,display:this.options.pointDot,hitDetectionRadius:this.options.pointHitDetectionRadius,ctx:this.chart.ctx}),this.datasets=[],this.buildScale(t),this.options.showTooltips&&e.bindEvents(this,this.options.tooltipEvents,function(t){var i="mouseout"!==t.type?this.getPointsAtEvent(t):[];this.eachPoints(function(t){t.restore(["fillColor","strokeColor"])}),e.each(i,function(t){t.fillColor=t.highlightFill,t.strokeColor=t.highlightStroke}),this.showTooltip(i)}),e.each(t.datasets,function(i){var s={label:i.label||null,fillColor:i.fillColor,strokeColor:i.strokeColor,pointColor:i.pointColor,pointStrokeColor:i.pointStrokeColor,points:[]};this.datasets.push(s),e.each(i.data,function(e,n){var o;this.scale.animation||(o=this.scale.getPointPosition(n,this.scale.calculateCenterOffset(e))),s.points.push(new this.PointClass({value:e,label:t.labels[n],datasetLabel:i.label,x:this.options.animation?this.scale.xCenter:o.x,y:this.options.animation?this.scale.yCenter:o.y,strokeColor:i.pointStrokeColor,fillColor:i.pointColor,highlightFill:i.pointHighlightFill||i.pointColor,highlightStroke:i.pointHighlightStroke||i.pointStrokeColor}))},this)},this),this.render()},eachPoints:function(t){e.each(this.datasets,function(i){e.each(i.points,t,this)},this)},getPointsAtEvent:function(t){var i=e.getRelativePosition(t),s=e.getAngleFromPoint({x:this.scale.xCenter,y:this.scale.yCenter},i),n=2*Math.PI/this.scale.valuesCount,o=Math.round((s.angle-1.5*Math.PI)/n),a=[];return(o>=this.scale.valuesCount||0>o)&&(o=0),s.distance<=this.scale.drawingArea&&e.each(this.datasets,function(t){a.push(t.points[o])}),a},buildScale:function(t){this.scale=new i.RadialScale({display:this.options.showScale,fontStyle:this.options.scaleFontStyle,fontSize:this.options.scaleFontSize,fontFamily:this.options.scaleFontFamily,fontColor:this.options.scaleFontColor,showLabels:this.options.scaleShowLabels,showLabelBackdrop:this.options.scaleShowLabelBackdrop,backdropColor:this.options.scaleBackdropColor,backdropPaddingY:this.options.scaleBackdropPaddingY,backdropPaddingX:this.options.scaleBackdropPaddingX,lineWidth:this.options.scaleShowLine?this.options.scaleLineWidth:0,lineColor:this.options.scaleLineColor,angleLineColor:this.options.angleLineColor,angleLineWidth:this.options.angleShowLineOut?this.options.angleLineWidth:0,pointLabelFontColor:this.options.pointLabelFontColor,pointLabelFontSize:this.options.pointLabelFontSize,pointLabelFontFamily:this.options.pointLabelFontFamily,pointLabelFontStyle:this.options.pointLabelFontStyle,height:this.chart.height,width:this.chart.width,xCenter:this.chart.width/2,yCenter:this.chart.height/2,ctx:this.chart.ctx,templateString:this.options.scaleLabel,labels:t.labels,valuesCount:t.datasets[0].data.length}),this.scale.setScaleSize(),this.updateScaleRange(t.datasets),this.scale.buildYLabels()},updateScaleRange:function(t){var i=function(){var i=[];return e.each(t,function(t){t.data?i=i.concat(t.data):e.each(t.points,function(t){i.push(t.value)})}),i}(),s=this.options.scaleOverride?{steps:this.options.scaleSteps,stepValue:this.options.scaleStepWidth,min:this.options.scaleStartValue,max:this.options.scaleStartValue+this.options.scaleSteps*this.options.scaleStepWidth}:e.calculateScaleRange(i,e.min([this.chart.width,this.chart.height])/2,this.options.scaleFontSize,this.options.scaleBeginAtZero,this.options.scaleIntegersOnly);e.extend(this.scale,s)},addData:function(t,i){this.scale.valuesCount++,e.each(t,function(t,e){var s=this.scale.getPointPosition(this.scale.valuesCount,this.scale.calculateCenterOffset(t));this.datasets[e].points.push(new this.PointClass({value:t,label:i,x:s.x,y:s.y,strokeColor:this.datasets[e].pointStrokeColor,fillColor:this.datasets[e].pointColor}))},this),this.scale.labels.push(i),this.reflow(),this.update()},removeData:function(){this.scale.valuesCount--,this.scale.labels.shift(),e.each(this.datasets,function(t){t.points.shift()},this),this.reflow(),this.update()},update:function(){this.eachPoints(function(t){t.save()}),this.reflow(),this.render()},reflow:function(){e.extend(this.scale,{width:this.chart.width,height:this.chart.height,size:e.min([this.chart.width,this.chart.height]),xCenter:this.chart.width/2,yCenter:this.chart.height/2}),this.updateScaleRange(this.datasets),this.scale.setScaleSize(),this.scale.buildYLabels()},draw:function(t){var i=t||1,s=this.chart.ctx;this.clear(),this.scale.draw(),e.each(this.datasets,function(t){e.each(t.points,function(t,e){t.hasValue()&&t.transition(this.scale.getPointPosition(e,this.scale.calculateCenterOffset(t.value)),i)},this),s.lineWidth=this.options.datasetStrokeWidth,s.strokeStyle=t.strokeColor,s.beginPath(),e.each(t.points,function(t,i){0===i?s.moveTo(t.x,t.y):s.lineTo(t.x,t.y)},this),s.closePath(),s.stroke(),s.fillStyle=t.fillColor,s.fill(),e.each(t.points,function(t){t.hasValue()&&t.draw()})},this)}})}.call(this);

}(jQuery));
    

(function($){
    //Deprecated//
}(jQuery));  
    
    
(function($){
    /*
     * SimpleModal 1.4.4 - jQuery Plugin
     * http://simplemodal.com/
     * Copyright (c) 2013 Eric Martin
     * Licensed under MIT and GPL
     * Date: Sun, Jan 20 2013 15:58:56 -0800
     */
    (function(b) {
        "function" === typeof define && define.amd ? define(["jquery"], b) : b(jQuery)
    })(function(b) {
        var j = [],
            n = b(document),
            k = navigator.userAgent.toLowerCase(),
            l = b(window),
            g = [],
            o = null,
            p = /msie/.test(k) && !/opera/.test(k),
            q = /opera/.test(k),
            m, r;
        m = p && /msie 6./.test(k) && "object" !== typeof window.XMLHttpRequest;
        r = p && /msie 7.0/.test(k);
        b.modal = function(a, h) {
            return b.modal.impl.init(a, h)
        };
        b.modal.close = function() {
            b.modal.impl.close()
        };
        b.modal.focus = function(a) {
            b.modal.impl.focus(a)
        };
        b.modal.setContainerDimensions =
            function() {
                b.modal.impl.setContainerDimensions()
        };
        b.modal.setPosition = function() {
            b.modal.impl.setPosition()
        };
        b.modal.update = function(a, h) {
            b.modal.impl.update(a, h)
        };
        b.fn.modal = function(a) {
            return b.modal.impl.init(this, a)
        };
        b.modal.defaults = {
            appendTo: "body",
            focus: !1,
            opacity: 80,
            overlayId: "simplemodal-overlay",
            overlayCss: {},
            containerId: "simplemodal-container",
            containerCss: {},
            dataId: "simplemodal-data",
            dataCss: {},
            minHeight: null,
            minWidth: null,
            maxHeight: null,
            maxWidth: null,
            autoResize: !0,
            autoPosition: !0,
            zIndex: 99999,
            close: !0,
            closeHTML: '<a class="modalCloseImg" title="Close"></a>',
            closeClass: "modal-close",
            escClose: !0,
            overlayClose: !0,
            fixed: !0,
            position: null,
            persist: !1,
            modal: !0,
            onOpen: function (dialog) {
                        dialog.overlay.fadeIn(150);
                        dialog.data.fadeOut(250);
                        dialog.container.fadeIn(250);
                        dialog.data.fadeIn(250);	 
                    },
            onShow: null,
            onClose: function (dialog) {
                        dialog.overlay.fadeOut(500);
                        dialog.data.fadeOut(250);
                        dialog.container.fadeOut(250);
                        dialog.data.fadeOut('250',function(){
                            setTimeout(function() { $.modal.close(); }, 250);
                        });	 
                    }
        };
        b.modal.impl = {
            d: {},
            init: function(a, h) {
                if (this.d.data) return !1;
                o = p && !b.support.boxModel;
                this.o = b.extend({}, b.modal.defaults, h);
                this.zIndex = this.o.zIndex;
                this.occb = !1;
                if ("object" === typeof a) {
                    if (a = a instanceof b ? a : b(a), this.d.placeholder = !1, 0 < a.parent().parent().size() && (a.before(b("<span></span>").attr("id",
                        "simplemodal-placeholder").css({
                        display: "none"
                    })), this.d.placeholder = !0, this.display = a.css("display"), !this.o.persist)) this.d.orig = a.clone(!0)
                } else if ("string" === typeof a || "number" === typeof a) a = b("<div></div>").html(a);
                else return alert("SimpleModal Error: Unsupported data type: " + typeof a), this;
                this.create(a);
                this.open();
                b.isFunction(this.o.onShow) && this.o.onShow.apply(this, [this.d]);
                return this
            },
            create: function(a) {
                this.getDimensions();
                if (this.o.modal && m) this.d.iframe = b('<iframe src="javascript:false;"></iframe>').css(b.extend(this.o.iframeCss, {
                    display: "none",
                    opacity: 0,
                    position: "fixed",
                    height: g[0],
                    width: g[1],
                    zIndex: this.o.zIndex,
                    top: 0,
                    left: 0
                })).appendTo(this.o.appendTo);
                this.d.overlay = b("<div></div>").attr("id", this.o.overlayId).addClass("simplemodal-overlay").css(b.extend(this.o.overlayCss, {
                    display: "none",
                    opacity: this.o.opacity / 100,
                    height: this.o.modal ? j[0] : 0,
                    width: this.o.modal ? j[1] : 0,
                    position: "fixed",
                    left: 0,
                    top: 0,
                    zIndex: this.o.zIndex + 1
                })).appendTo(this.o.appendTo);
                this.d.container = b("<div></div>").attr("id", this.o.containerId).addClass("simplemodal-container").css(b.extend({
                    position: this.o.fixed ?
                        "fixed" : "absolute"
                }, this.o.containerCss, {
                    display: "none",
                    zIndex: this.o.zIndex + 2
                })).append(this.o.close && this.o.closeHTML ? b(this.o.closeHTML).addClass(this.o.closeClass) : "").appendTo(this.o.appendTo);
                this.d.wrap = b("<div></div>").attr("tabIndex", -1).addClass("simplemodal-wrap").css({
                    height: "100%",
                    outline: 0,
                    width: "100%"
                }).appendTo(this.d.container);
                this.d.data = a.attr("id", a.attr("id") || this.o.dataId).addClass("simplemodal-data").css(b.extend(this.o.dataCss, {
                    display: "none"
                })).appendTo("body");
                this.setContainerDimensions();
                this.d.data.appendTo(this.d.wrap);
                (m || o) && this.fixIE()
            },
            bindEvents: function() {
                var a = this;
                b("." + a.o.closeClass).bind("click.simplemodal", function(b) {
                    b.preventDefault();
                    a.close()
                });
                a.o.modal && a.o.close && a.o.overlayClose && a.d.overlay.bind("click.simplemodal", function(b) {
                    b.preventDefault();
                    a.close()
                });
                n.bind("keydown.simplemodal", function(b) {
                    a.o.modal && 9 === b.keyCode ? a.watchTab(b) : a.o.close && a.o.escClose && 27 === b.keyCode && (b.preventDefault(), a.close())
                });
                l.bind("resize.simplemodal orientationchange.simplemodal",
                    function() {
                        a.getDimensions();
                        a.o.autoResize ? a.setContainerDimensions() : a.o.autoPosition && a.setPosition();
                        m || o ? a.fixIE() : a.o.modal && (a.d.iframe && a.d.iframe.css({
                            height: g[0],
                            width: g[1]
                        }), a.d.overlay.css({
                            height: j[0],
                            width: j[1]
                        }))
                    })
            },
            unbindEvents: function() {
                b("." + this.o.closeClass).unbind("click.simplemodal");
                n.unbind("keydown.simplemodal");
                l.unbind(".simplemodal");
                this.d.overlay.unbind("click.simplemodal")
            },
            fixIE: function() {
                var a = this.o.position;
                b.each([this.d.iframe || null, !this.o.modal ? null : this.d.overlay,
                    "fixed" === this.d.container.css("position") ? this.d.container : null
                ], function(b, e) {
                    if (e) {
                        var f = e[0].style;
                        f.position = "absolute";
                        if (2 > b) f.removeExpression("height"), f.removeExpression("width"), f.setExpression("height", 'document.body.scrollHeight > document.body.clientHeight ? document.body.scrollHeight : document.body.clientHeight + "px"'), f.setExpression("width", 'document.body.scrollWidth > document.body.clientWidth ? document.body.scrollWidth : document.body.clientWidth + "px"');
                        else {
                            var c, d;
                            a && a.constructor ===
                                Array ? (c = a[0] ? "number" === typeof a[0] ? a[0].toString() : a[0].replace(/px/, "") : e.css("top").replace(/px/, ""), c = -1 === c.indexOf("%") ? c + ' + (t = document.documentElement.scrollTop ? document.documentElement.scrollTop : document.body.scrollTop) + "px"' : parseInt(c.replace(/%/, "")) + ' * ((document.documentElement.clientHeight || document.body.clientHeight) / 100) + (t = document.documentElement.scrollTop ? document.documentElement.scrollTop : document.body.scrollTop) + "px"', a[1] && (d = "number" === typeof a[1] ?
                                    a[1].toString() : a[1].replace(/px/, ""), d = -1 === d.indexOf("%") ? d + ' + (t = document.documentElement.scrollLeft ? document.documentElement.scrollLeft : document.body.scrollLeft) + "px"' : parseInt(d.replace(/%/, "")) + ' * ((document.documentElement.clientWidth || document.body.clientWidth) / 100) + (t = document.documentElement.scrollLeft ? document.documentElement.scrollLeft : document.body.scrollLeft) + "px"')) : (c = '(document.documentElement.clientHeight || document.body.clientHeight) / 2 - (this.offsetHeight / 2) + (t = document.documentElement.scrollTop ? document.documentElement.scrollTop : document.body.scrollTop) + "px"',
                                    d = '(document.documentElement.clientWidth || document.body.clientWidth) / 2 - (this.offsetWidth / 2) + (t = document.documentElement.scrollLeft ? document.documentElement.scrollLeft : document.body.scrollLeft) + "px"');
                            f.removeExpression("top");
                            f.removeExpression("left");
                            f.setExpression("top", c);
                            f.setExpression("left", d)
                        }
                    }
                })
            },
            focus: function(a) {
                var h = this,
                    a = a && -1 !== b.inArray(a, ["first", "last"]) ? a : "first",
                    e = b(":input:enabled:visible:" + a, h.d.wrap);
                setTimeout(function() {
                        0 < e.length ? e.focus() : h.d.wrap.focus()
                    },
                    10)
            },
            getDimensions: function() {
                var a = "undefined" === typeof window.innerHeight ? l.height() : window.innerHeight;
                j = [n.height(), n.width()];
                g = [a, l.width()]
            },
            getVal: function(a, b) {
                return a ? "number" === typeof a ? a : "auto" === a ? 0 : 0 < a.indexOf("%") ? parseInt(a.replace(/%/, "")) / 100 * ("h" === b ? g[0] : g[1]) : parseInt(a.replace(/px/, "")) : null
            },
            update: function(a, b) {
                if (!this.d.data) return !1;
                this.d.origHeight = this.getVal(a, "h");
                this.d.origWidth = this.getVal(b, "w");
                this.d.data.hide();
                a && this.d.container.css("height", a);
                b && this.d.container.css("width",
                    b);
                this.setContainerDimensions();
                this.d.data.show();
                this.o.focus && this.focus();
                this.unbindEvents();
                this.bindEvents()
            },
            setContainerDimensions: function() {
                var a = m || r,
                    b = this.d.origHeight ? this.d.origHeight : q ? this.d.container.height() : this.getVal(a ? this.d.container[0].currentStyle.height : this.d.container.css("height"), "h"),
                    a = this.d.origWidth ? this.d.origWidth : q ? this.d.container.width() : this.getVal(a ? this.d.container[0].currentStyle.width : this.d.container.css("width"), "w"),
                    e = this.d.data.outerHeight(!0),
                    f =
                        this.d.data.outerWidth(!0);
                this.d.origHeight = this.d.origHeight || b;
                this.d.origWidth = this.d.origWidth || a;
                var c = this.o.maxHeight ? this.getVal(this.o.maxHeight, "h") : null,
                    d = this.o.maxWidth ? this.getVal(this.o.maxWidth, "w") : null,
                    c = c && c < g[0] ? c : g[0],
                    d = d && d < g[1] ? d : g[1],
                    i = this.o.minHeight ? this.getVal(this.o.minHeight, "h") : "auto",
                    b = b ? this.o.autoResize && b > c ? c : b < i ? i : b : e ? e > c ? c : this.o.minHeight && "auto" !== i && e < i ? i : e : i,
                    c = this.o.minWidth ? this.getVal(this.o.minWidth, "w") : "auto",
                    a = a ? this.o.autoResize && a > d ? d : a < c ? c : a : f ?
                        f > d ? d : this.o.minWidth && "auto" !== c && f < c ? c : f : c;
                this.d.container.css({
                    height: b,
                    width: a
                });
                this.d.wrap.css({
                    overflow: e > b || f > a ? "auto" : "visible"
                });
                this.o.autoPosition && this.setPosition()
            },
            setPosition: function() {
                var a, b;
                a = g[0] / 2 - this.d.container.outerHeight(!0) / 2;
                b = g[1] / 2 - this.d.container.outerWidth(!0) / 2;
                var e = "fixed" !== this.d.container.css("position") ? l.scrollTop() : 0;
                this.o.position && "[object Array]" === Object.prototype.toString.call(this.o.position) ? (a = e + (this.o.position[0] || a), b = this.o.position[1] || b) :
                    a = e + a;
                this.d.container.css({
                    left: b,
                    top: a
                })
            },
            watchTab: function(a) {
                if (0 < b(a.target).parents(".simplemodal-container").length) {
                    if (this.inputs = b(":input:enabled:visible:first, :input:enabled:visible:last", this.d.data[0]), !a.shiftKey && a.target === this.inputs[this.inputs.length - 1] || a.shiftKey && a.target === this.inputs[0] || 0 === this.inputs.length) a.preventDefault(), this.focus(a.shiftKey ? "last" : "first")
                } else a.preventDefault(), this.focus()
            },
            open: function() {
                this.d.iframe && this.d.iframe.show();
                b.isFunction(this.o.onOpen) ?
                    this.o.onOpen.apply(this, [this.d]) : (this.d.overlay.show(), this.d.container.show(), this.d.data.show());
                this.o.focus && this.focus();
                this.bindEvents()
            },
            close: function() {
                if (!this.d.data) return !1;
                this.unbindEvents();
                if (b.isFunction(this.o.onClose) && !this.occb) this.occb = !0, this.o.onClose.apply(this, [this.d]);
                else {
                    if (this.d.placeholder) {
                        var a = b("#simplemodal-placeholder");
                        this.o.persist ? a.replaceWith(this.d.data.removeClass("simplemodal-data").css("display", this.display)) : (this.d.data.hide().remove(), a.replaceWith(this.d.orig))
                    } else this.d.data.hide().remove();
                    this.d.container.hide().remove();
                    this.d.overlay.hide();
                    this.d.iframe && this.d.iframe.hide().remove();
                    this.d.overlay.remove();
                    this.d = {}
                }
            }
        }
    });

}(jQuery));

    
(function($){
    /*! Copyright (c) 2013 Brandon Aaron (http://brandon.aaron.sh)
     * Licensed under the MIT License.
     *
     * jquery.mousewheel.min.js
     * Version: 3.1.12
     *
     * Requires: jQuery 1.2.2+
     */
    !function(a){"function"==typeof define&&define.amd?define(["jquery"],a):"object"==typeof exports?module.exports=a:a(jQuery)}(function(a){function b(b){var g=b||window.event,h=i.call(arguments,1),j=0,l=0,m=0,n=0,o=0,p=0;if(b=a.event.fix(g),b.type="mousewheel","detail"in g&&(m=-1*g.detail),"wheelDelta"in g&&(m=g.wheelDelta),"wheelDeltaY"in g&&(m=g.wheelDeltaY),"wheelDeltaX"in g&&(l=-1*g.wheelDeltaX),"axis"in g&&g.axis===g.HORIZONTAL_AXIS&&(l=-1*m,m=0),j=0===m?l:m,"deltaY"in g&&(m=-1*g.deltaY,j=m),"deltaX"in g&&(l=g.deltaX,0===m&&(j=-1*l)),0!==m||0!==l){if(1===g.deltaMode){var q=a.data(this,"mousewheel-line-height");j*=q,m*=q,l*=q}else if(2===g.deltaMode){var r=a.data(this,"mousewheel-page-height");j*=r,m*=r,l*=r}if(n=Math.max(Math.abs(m),Math.abs(l)),(!f||f>n)&&(f=n,d(g,n)&&(f/=40)),d(g,n)&&(j/=40,l/=40,m/=40),j=Math[j>=1?"floor":"ceil"](j/f),l=Math[l>=1?"floor":"ceil"](l/f),m=Math[m>=1?"floor":"ceil"](m/f),k.settings.normalizeOffset&&this.getBoundingClientRect){var s=this.getBoundingClientRect();o=b.clientX-s.left,p=b.clientY-s.top}return b.deltaX=l,b.deltaY=m,b.deltaFactor=f,b.offsetX=o,b.offsetY=p,b.deltaMode=0,h.unshift(b,j,l,m),e&&clearTimeout(e),e=setTimeout(c,200),(a.event.dispatch||a.event.handle).apply(this,h)}}function c(){f=null}function d(a,b){return k.settings.adjustOldDeltas&&"mousewheel"===a.type&&b%120===0}var e,f,g=["wheel","mousewheel","DOMMouseScroll","MozMousePixelScroll"],h="onwheel"in document||document.documentMode>=9?["wheel"]:["mousewheel","DomMouseScroll","MozMousePixelScroll"],i=Array.prototype.slice;if(a.event.fixHooks)for(var j=g.length;j;)a.event.fixHooks[g[--j]]=a.event.mouseHooks;var k=a.event.special.mousewheel={version:"3.1.12",setup:function(){if(this.addEventListener)for(var c=h.length;c;)this.addEventListener(h[--c],b,!1);else this.onmousewheel=b;a.data(this,"mousewheel-line-height",k.getLineHeight(this)),a.data(this,"mousewheel-page-height",k.getPageHeight(this))},teardown:function(){if(this.removeEventListener)for(var c=h.length;c;)this.removeEventListener(h[--c],b,!1);else this.onmousewheel=null;a.removeData(this,"mousewheel-line-height"),a.removeData(this,"mousewheel-page-height")},getLineHeight:function(b){var c=a(b),d=c["offsetParent"in a.fn?"offsetParent":"parent"]();return d.length||(d=a("body")),parseInt(d.css("fontSize"),10)||parseInt(c.css("fontSize"),10)||16},getPageHeight:function(b){return a(b).height()},settings:{adjustOldDeltas:!0,normalizeOffset:!0}};a.fn.extend({mousewheel:function(a){return a?this.bind("mousewheel",a):this.trigger("mousewheel")},unmousewheel:function(a){return this.unbind("mousewheel",a)}})});
}(jQuery));


(function($){
    /* 
        Morphext - v2.3.4 - 2015-04-14 
        Morphext is licensed under the MIT license (http://ian.mit-license.org/)
    */
    !function(a){"use strict";function b(b,c){this.element=a(b),this.settings=a.extend({},d,c),this._defaults=d,this._init()}var c="Morphext",d={animation:"bounceIn",separator:",",speed:2e3,complete:a.noop};b.prototype={_init:function(){var b=this;this.phrases=[],this.element.addClass("morphext"),a.each(this.element.text().split(this.settings.separator),function(a,c){b.phrases.push(c.trim())}),this.index=-1,this.animate(),this.start()},animate:function(){this.index=++this.index%this.phrases.length,this.element[0].innerHTML='<span class="animated '+this.settings.animation+'">'+this.phrases[this.index]+"</span>",a.isFunction(this.settings.complete)&&this.settings.complete.call(this)},start:function(){var a=this;this._interval=setInterval(function(){a.animate()},this.settings.speed)},stop:function(){this._interval=clearInterval(this._interval)}},a.fn[c]=function(d){return this.each(function(){a.data(this,"plugin_"+c)||a.data(this,"plugin_"+c,new b(this,d))})}}(jQuery);
}(jQuery));



(function($){
    /*!
     * Justified Gallery - v3.6.0
     * http://miromannino.github.io/Justified-Gallery/
     * Copyright (c) 2015 Miro Mannino
     * Licensed under the MIT license.
     */
    !function(t){var i=function(i,e){this.settings=e,this.checkSettings(),this.imgAnalyzerTimeout=null,this.entries=null,this.buildingRow={entriesBuff:[],width:0,aspectRatio:0},this.lastAnalyzedIndex=-1,this["yield"]={every:2,flushed:0},this.border=e.border>=0?e.border:e.margins,this.maxRowHeight=this.retrieveMaxRowHeight(),this.suffixRanges=this.retrieveSuffixRanges(),this.offY=this.border,this.spinner={phase:0,timeSlot:150,$el:t('<div class="spinner"><span></span><span></span><span></span></div>'),intervalId:null},this.checkWidthIntervalId=null,this.galleryWidth=i.width(),this.$gallery=i};i.prototype.getSuffix=function(t,i){var e,s;for(e=t>i?t:i,s=0;s<this.suffixRanges.length;s++)if(e<=this.suffixRanges[s])return this.settings.sizeRangeSuffixes[this.suffixRanges[s]];return this.settings.sizeRangeSuffixes[this.suffixRanges[s-1]]},i.prototype.removeSuffix=function(t,i){return t.substring(0,t.length-i.length)},i.prototype.endsWith=function(t,i){return-1!==t.indexOf(i,t.length-i.length)},i.prototype.getUsedSuffix=function(t){for(var i in this.settings.sizeRangeSuffixes)if(this.settings.sizeRangeSuffixes.hasOwnProperty(i)){if(0===this.settings.sizeRangeSuffixes[i].length)continue;if(this.endsWith(t,this.settings.sizeRangeSuffixes[i]))return this.settings.sizeRangeSuffixes[i]}return""},i.prototype.newSrc=function(t,i,e){var s=t.match(this.settings.extension),n=null!=s?s[0]:"",r=t.replace(this.settings.extension,"");return r=this.removeSuffix(r,this.getUsedSuffix(r)),r+=this.getSuffix(i,e)+n},i.prototype.showImg=function(t,i){this.settings.cssAnimation?(t.addClass("entry-visible"),i&&i()):t.stop().fadeTo(this.settings.imagesAnimationDuration,1,i)},i.prototype.extractImgSrcFromImage=function(t){var i="undefined"!=typeof t.data("safe-src")?t.data("safe-src"):t.attr("src");return t.data("jg.originalSrc",i),i},i.prototype.imgFromEntry=function(t){var i=t.find("> img");return 0===i.length&&(i=t.find("> a > img")),0===i.length?null:i},i.prototype.captionFromEntry=function(t){var i=t.find("> .caption");return 0===i.length?null:i},i.prototype.displayEntry=function(i,e,s,n,r,a){i.width(n),i.height(a),i.css("top",s),i.css("left",e);var o=this.imgFromEntry(i);if(null!==o){o.css("width",n),o.css("height",r),o.css("margin-left",-n/2),o.css("margin-top",-r/2);var h=o.attr("src"),g=this.newSrc(h,n,r);o.one("error",function(){o.attr("src",o.data("jg.originalSrc"))});var l=function(){h!==g&&o.attr("src",g)};"skipped"===i.data("jg.loaded")?this.onImageEvent(h,t.proxy(function(){this.showImg(i,l),i.data("jg.loaded",!0)},this)):this.showImg(i,l)}else this.showImg(i);this.displayEntryCaption(i)},i.prototype.displayEntryCaption=function(i){var e=this.imgFromEntry(i);if(null!==e&&this.settings.captions){var s=this.captionFromEntry(i);if(null==s){var n=e.attr("alt");"undefined"==typeof n&&(n=i.attr("title")),"undefined"!=typeof n&&(s=t('<div class="caption">'+n+"</div>"),i.append(s),i.data("jg.createdCaption",!0))}null!==s&&(this.settings.cssAnimation||s.stop().fadeTo(0,this.settings.captionSettings.nonVisibleOpacity),this.addCaptionEventsHandlers(i))}else this.removeCaptionEventsHandlers(i)},i.prototype.onEntryMouseEnterForCaption=function(i){var e=this.captionFromEntry(t(i.currentTarget));this.settings.cssAnimation?e.addClass("caption-visible").removeClass("caption-hidden"):e.stop().fadeTo(this.settings.captionSettings.animationDuration,this.settings.captionSettings.visibleOpacity)},i.prototype.onEntryMouseLeaveForCaption=function(i){var e=this.captionFromEntry(t(i.currentTarget));this.settings.cssAnimation?e.removeClass("caption-visible").removeClass("caption-hidden"):e.stop().fadeTo(this.settings.captionSettings.animationDuration,this.settings.captionSettings.nonVisibleOpacity)},i.prototype.addCaptionEventsHandlers=function(i){var e=i.data("jg.captionMouseEvents");"undefined"==typeof e&&(e={mouseenter:t.proxy(this.onEntryMouseEnterForCaption,this),mouseleave:t.proxy(this.onEntryMouseLeaveForCaption,this)},i.on("mouseenter",void 0,void 0,e.mouseenter),i.on("mouseleave",void 0,void 0,e.mouseleave),i.data("jg.captionMouseEvents",e))},i.prototype.removeCaptionEventsHandlers=function(t){var i=t.data("jg.captionMouseEvents");"undefined"!=typeof i&&(t.off("mouseenter",void 0,i.mouseenter),t.off("mouseleave",void 0,i.mouseleave),t.removeData("jg.captionMouseEvents"))},i.prototype.prepareBuildingRow=function(t){var i,e,s,n,r,a=!0,o=0,h=this.galleryWidth-2*this.border-(this.buildingRow.entriesBuff.length-1)*this.settings.margins,g=h/this.buildingRow.aspectRatio,l=this.buildingRow.width/h>this.settings.justifyThreshold;if(t&&"hide"===this.settings.lastRow&&!l){for(i=0;i<this.buildingRow.entriesBuff.length;i++)e=this.buildingRow.entriesBuff[i],this.settings.cssAnimation?e.removeClass("entry-visible"):e.stop().fadeTo(0,0);return-1}for(t&&!l&&"nojustify"===this.settings.lastRow&&(a=!1),i=0;i<this.buildingRow.entriesBuff.length;i++)e=this.buildingRow.entriesBuff[i],s=e.data("jg.width")/e.data("jg.height"),a?(n=i===this.buildingRow.entriesBuff.length-1?h:g*s,r=g):(n=this.settings.rowHeight*s,r=this.settings.rowHeight),h-=Math.round(n),e.data("jg.jwidth",Math.round(n)),e.data("jg.jheight",Math.ceil(r)),(0===i||o>r)&&(o=r);return this.settings.fixedHeight&&o>this.settings.rowHeight&&(o=this.settings.rowHeight),{minHeight:o,justify:a}},i.prototype.clearBuildingRow=function(){this.buildingRow.entriesBuff=[],this.buildingRow.aspectRatio=0,this.buildingRow.width=0},i.prototype.flushRow=function(t){var i,e,s,n=this.settings,r=this.border;if(s=this.prepareBuildingRow(t),e=s.minHeight,t&&"hide"===n.lastRow&&-1===e)return void this.clearBuildingRow();this.maxRowHeight.percentage?this.maxRowHeight.value*n.rowHeight<e&&(e=this.maxRowHeight.value*n.rowHeight):this.maxRowHeight.value>0&&this.maxRowHeight.value<e&&(e=this.maxRowHeight.value);for(var a=0;a<this.buildingRow.entriesBuff.length;a++)i=this.buildingRow.entriesBuff[a],this.displayEntry(i,r,this.offY,i.data("jg.jwidth"),i.data("jg.jheight"),e),r+=i.data("jg.jwidth")+n.margins;this.$gallery.height(this.offY+e+this.border+(this.isSpinnerActive()?this.getSpinnerHeight():0)),(!t||e<=this.settings.rowHeight&&s.justify)&&(this.offY+=e+this.settings.margins,this.clearBuildingRow(),this.$gallery.trigger("jg.rowflush"))},i.prototype.checkWidth=function(){this.checkWidthIntervalId=setInterval(t.proxy(function(){var t=parseInt(this.$gallery.width(),10);this.galleryWidth!==t&&(this.galleryWidth=t,this.rewind(),this.startImgAnalyzer(!0))},this),this.settings.refreshTime)},i.prototype.isSpinnerActive=function(){return null!=this.spinner.intervalId},i.prototype.getSpinnerHeight=function(){return this.spinner.$el.innerHeight()},i.prototype.stopLoadingSpinnerAnimation=function(){clearInterval(this.spinner.intervalId),this.spinner.intervalId=null,this.$gallery.height(this.$gallery.height()-this.getSpinnerHeight()),this.spinner.$el.detach()},i.prototype.startLoadingSpinnerAnimation=function(){var t=this.spinner,i=t.$el.find("span");clearInterval(t.intervalId),this.$gallery.append(t.$el),this.$gallery.height(this.offY+this.getSpinnerHeight()),t.intervalId=setInterval(function(){t.phase<i.length?i.eq(t.phase).fadeTo(t.timeSlot,1):i.eq(t.phase-i.length).fadeTo(t.timeSlot,0),t.phase=(t.phase+1)%(2*i.length)},t.timeSlot)},i.prototype.rewind=function(){this.lastAnalyzedIndex=-1,this.offY=this.border,this.clearBuildingRow()},i.prototype.hideBuildingRowImages=function(){for(var t=0;t<this.buildingRow.entriesBuff.length;t++)this.settings.cssAnimation?this.buildingRow.entriesBuff[t].removeClass("entry-visible"):this.buildingRow.entriesBuff[t].stop().fadeTo(0,0)},i.prototype.updateEntries=function(i){return this.entries=this.$gallery.find(this.settings.selector).toArray(),0===this.entries.length?!1:(this.settings.filter?this.modifyEntries(this.filterArray,i):this.modifyEntries(this.resetFilters,i),t.isFunction(this.settings.sort)?this.modifyEntries(this.sortArray,i):this.settings.randomize&&this.modifyEntries(this.shuffleArray,i),!0)},i.prototype.insertToGallery=function(i){var e=this;t.each(i,function(){t(this).appendTo(e.$gallery)})},i.prototype.shuffleArray=function(t){var i,e,s;for(i=t.length-1;i>0;i--)e=Math.floor(Math.random()*(i+1)),s=t[i],t[i]=t[e],t[e]=s;return this.insertToGallery(t),t},i.prototype.sortArray=function(t){return t.sort(this.settings.sort),this.insertToGallery(t),t},i.prototype.resetFilters=function(i){for(var e=0;e<i.length;e++)t(i[e]).removeClass("jg-filtered");return i},i.prototype.filterArray=function(i){var e=this.settings;return"string"===t.type(e.filter)?i.filter(function(i){var s=t(i);return s.is(e.filter)?(s.removeClass("jg-filtered"),!0):(s.addClass("jg-filtered"),!1)}):t.isFunction(e.filter)?i.filter(e.filter):void 0},i.prototype.modifyEntries=function(t,i){var e=i?this.entries.splice(this.lastAnalyzedIndex+1,this.entries.length-this.lastAnalyzedIndex-1):this.entries;e=t.call(this,e),this.entries=i?this.entries.concat(e):e},i.prototype.destroy=function(){clearInterval(this.checkWidthIntervalId),t.each(this.entries,t.proxy(function(i,e){var s=t(e);s.css("width",""),s.css("height",""),s.css("top",""),s.css("left",""),s.data("jg.loaded",void 0),s.removeClass("jg-entry");var n=this.imgFromEntry(s);n.css("width",""),n.css("height",""),n.css("margin-left",""),n.css("margin-top",""),n.attr("src",n.data("jg.originalSrc")),n.data("jg.originalSrc",void 0),this.removeCaptionEventsHandlers(s);var r=this.captionFromEntry(s);s.data("jg.createdCaption")?(s.data("jg.createdCaption",void 0),null!=r&&r.remove()):null!=r&&r.fadeTo(0,1)},this)),this.$gallery.css("height",""),this.$gallery.removeClass("justified-gallery"),this.$gallery.data("jg.controller",void 0)},i.prototype.analyzeImages=function(i){for(var e=this.lastAnalyzedIndex+1;e<this.entries.length;e++){var s=t(this.entries[e]);if(s.data("jg.loaded")===!0||"skipped"===s.data("jg.loaded")){var n=this.galleryWidth-2*this.border-(this.buildingRow.entriesBuff.length-1)*this.settings.margins,r=s.data("jg.width")/s.data("jg.height");if(n/(this.buildingRow.aspectRatio+r)<this.settings.rowHeight&&(this.flushRow(!1),++this["yield"].flushed>=this["yield"].every))return void this.startImgAnalyzer(i);this.buildingRow.entriesBuff.push(s),this.buildingRow.aspectRatio+=r,this.buildingRow.width+=r*this.settings.rowHeight,this.lastAnalyzedIndex=e}else if("error"!==s.data("jg.loaded"))return}this.buildingRow.entriesBuff.length>0&&this.flushRow(!0),this.isSpinnerActive()&&this.stopLoadingSpinnerAnimation(),this.stopImgAnalyzerStarter(),this.$gallery.trigger(i?"jg.resize":"jg.complete")},i.prototype.stopImgAnalyzerStarter=function(){this["yield"].flushed=0,null!==this.imgAnalyzerTimeout&&clearTimeout(this.imgAnalyzerTimeout)},i.prototype.startImgAnalyzer=function(t){var i=this;this.stopImgAnalyzerStarter(),this.imgAnalyzerTimeout=setTimeout(function(){i.analyzeImages(t)},.001)},i.prototype.onImageEvent=function(i,e,s){if(e||s){var n=new Image,r=t(n);e&&r.one("load",function(){r.off("load error"),e(n)}),s&&r.one("error",function(){r.off("load error"),s(n)}),n.src=i}},i.prototype.init=function(){var i=!1,e=!1,s=this;t.each(this.entries,function(n,r){var a=t(r),o=s.imgFromEntry(a);if(a.addClass("jg-entry"),a.data("jg.loaded")!==!0&&"skipped"!==a.data("jg.loaded"))if(null!==s.settings.rel&&a.attr("rel",s.settings.rel),null!==s.settings.target&&a.attr("target",s.settings.target),null!==o){var h=s.extractImgSrcFromImage(o);if(o.attr("src",h),s.settings.waitThumbnailsLoad===!1){var g=parseInt(o.attr("width"),10),l=parseInt(o.attr("height"),10);if(!isNaN(g)&&!isNaN(l))return a.data("jg.width",g),a.data("jg.height",l),a.data("jg.loaded","skipped"),e=!0,s.startImgAnalyzer(!1),!0}a.data("jg.loaded",!1),i=!0,s.isSpinnerActive()||s.startLoadingSpinnerAnimation(),s.onImageEvent(h,function(t){a.data("jg.width",t.width),a.data("jg.height",t.height),a.data("jg.loaded",!0),s.startImgAnalyzer(!1)},function(){a.data("jg.loaded","error"),s.startImgAnalyzer(!1)})}else a.data("jg.loaded",!0),a.data("jg.width",a.width()|a.css("width")|1),a.data("jg.height",a.height()|a.css("height")|1)}),i||e||this.startImgAnalyzer(!1),this.checkWidth()},i.prototype.checkOrConvertNumber=function(i,e){if("string"===t.type(i[e])&&(i[e]=parseFloat(i[e])),"number"!==t.type(i[e]))throw e+" must be a number";if(isNaN(i[e]))throw"invalid number for "+e},i.prototype.checkSizeRangesSuffixes=function(){if("object"!==t.type(this.settings.sizeRangeSuffixes))throw"sizeRangeSuffixes must be defined and must be an object";var i=[];for(var e in this.settings.sizeRangeSuffixes)this.settings.sizeRangeSuffixes.hasOwnProperty(e)&&i.push(e);for(var s={0:""},n=0;n<i.length;n++)if("string"===t.type(i[n]))try{var r=parseInt(i[n].replace(/^[a-z]+/,""),10);s[r]=this.settings.sizeRangeSuffixes[i[n]]}catch(a){throw"sizeRangeSuffixes keys must contains correct numbers ("+a+")"}else s[i[n]]=this.settings.sizeRangeSuffixes[i[n]];this.settings.sizeRangeSuffixes=s},i.prototype.retrieveMaxRowHeight=function(){var i={};if("string"===t.type(this.settings.maxRowHeight))this.settings.maxRowHeight.match(/^[0-9]+%$/)?(i.value=parseFloat(this.settings.maxRowHeight.match(/^([0-9])+%$/)[1])/100,i.percentage=!1):(i.value=parseFloat(this.settings.maxRowHeight),i.percentage=!0);else{if("number"!==t.type(this.settings.maxRowHeight))throw"maxRowHeight must be a number or a percentage";i.value=this.settings.maxRowHeight,i.percentage=!1}if(isNaN(i.value))throw"invalid number for maxRowHeight";return i.percentage?i.value<100&&(i.value=100):i.value>0&&i.value<this.settings.rowHeight&&(i.value=this.settings.rowHeight),i},i.prototype.checkSettings=function(){if(this.checkSizeRangesSuffixes(),this.checkOrConvertNumber(this.settings,"rowHeight"),this.checkOrConvertNumber(this.settings,"margins"),this.checkOrConvertNumber(this.settings,"border"),"nojustify"!==this.settings.lastRow&&"justify"!==this.settings.lastRow&&"hide"!==this.settings.lastRow)throw'lastRow must be "nojustify", "justify" or "hide"';if(this.checkOrConvertNumber(this.settings,"justifyThreshold"),this.settings.justifyThreshold<0||this.settings.justifyThreshold>1)throw"justifyThreshold must be in the interval [0,1]";if("boolean"!==t.type(this.settings.cssAnimation))throw"cssAnimation must be a boolean";if("boolean"!==t.type(this.settings.captions))throw"captions must be a boolean";if(this.checkOrConvertNumber(this.settings.captionSettings,"animationDuration"),this.checkOrConvertNumber(this.settings.captionSettings,"visibleOpacity"),this.settings.captionSettings.visibleOpacity<0||this.settings.captionSettings.visibleOpacity>1)throw"captionSettings.visibleOpacity must be in the interval [0, 1]";if(this.checkOrConvertNumber(this.settings.captionSettings,"nonVisibleOpacity"),this.settings.captionSettings.nonVisibleOpacity<0||this.settings.captionSettings.nonVisibleOpacity>1)throw"captionSettings.nonVisibleOpacity must be in the interval [0, 1]";if("boolean"!==t.type(this.settings.fixedHeight))throw"fixedHeight must be a boolean";if(this.checkOrConvertNumber(this.settings,"imagesAnimationDuration"),this.checkOrConvertNumber(this.settings,"refreshTime"),"boolean"!==t.type(this.settings.randomize))throw"randomize must be a boolean";if("string"!==t.type(this.settings.selector))throw"selector must be a string";if(this.settings.sort!==!1&&!t.isFunction(this.settings.sort))throw"sort must be false or a comparison function";if(this.settings.filter!==!1&&!t.isFunction(this.settings.sort)&&"string"!==t.type(this.settings.filter))throw"filter must be false, a string or a filter function"},i.prototype.retrieveSuffixRanges=function(){var t=[];for(var i in this.settings.sizeRangeSuffixes)this.settings.sizeRangeSuffixes.hasOwnProperty(i)&&t.push(parseInt(i,10));return t.sort(function(t,i){return t>i?1:i>t?-1:0}),t},i.prototype.updateSettings=function(i){this.settings=t.extend({},this.settings,i),this.checkSettings(),this.border=this.settings.border>=0?this.settings.border:this.settings.margins,this.maxRowHeight=this.retrieveMaxRowHeight(),this.suffixRanges=this.retrieveSuffixRanges()},t.fn.justifiedGallery=function(e){return this.each(function(s,n){var r=t(n);r.addClass("justified-gallery");var a=r.data("jg.controller");if("undefined"==typeof a){if("undefined"!=typeof e&&null!==e&&"object"!==t.type(e))throw"The argument must be an object";a=new i(r,t.extend({},t.fn.justifiedGallery.defaults,e)),r.data("jg.controller",a)}else if("norewind"===e)a.hideBuildingRowImages();else{if("destroy"===e)return void a.destroy();a.updateSettings(e),a.rewind()}a.updateEntries("norewind"===e)&&a.init()})},t.fn.justifiedGallery.defaults={sizeRangeSuffixes:{},rowHeight:120,maxRowHeight:"200%",margins:1,border:-1,lastRow:"nojustify",justifyThreshold:.75,fixedHeight:!1,waitThumbnailsLoad:!0,captions:!1,cssAnimation:!1,imagesAnimationDuration:500,captionSettings:{animationDuration:500,visibleOpacity:.7,nonVisibleOpacity:0},rel:null,target:null,extension:/\.[^.\\/]+$/,refreshTime:100,randomize:!1,sort:!1,filter:!1,selector:"> a, > div:not(.spinner)"}}(jQuery);

}(jQuery));

(function(){
    /* Add to Homescreen v3.2.2 ~ (c) 2015 Matteo Spinelli ~ @license: http://cubiq.org/license */
    !function(e,t){function i(){e.removeEventListener("load",i,!1),r=!0}function n(e){return l=l||new n.Class(e)}function o(e,t){for(var i in t)e[i]=t[i];return e}function s(){"#ath"==t.location.hash&&history.replaceState("",e.document.title,t.location.href.split("#")[0]),h.test(t.location.href)&&history.replaceState("",e.document.title,t.location.href.replace(h,"$1")),d.test(t.location.search)&&history.replaceState("",e.document.title,t.location.href.replace(d,"$2"))}var a="addEventListener"in e,r=!1;"complete"===t.readyState?r=!0:a&&e.addEventListener("load",i,!1);var l,h=/\/ath(\/)?$/,d=/([\?&]ath=[^&]*$|&ath=[^&]*(&))/;n.intl={de_de:{ios:"Um diese Web-App zum Home-Bildschirm hinzuzufügen, tippen Sie auf %icon und dann <strong>Zum Home-Bildschirm</strong>.",android:'Um diese Web-App zum Home-Bildschirm hinzuzufügen, öffnen Sie das Menü und tippen dann auf <strong>Zum Startbildschirm hinzufügen</strong>. <small>Wenn Ihr Gerät eine Menütaste hat, lässt sich das Browsermenü über diese öffnen. Ansonsten tippen Sie auf <span class="ath-action-icon">icon</span>.</small>'},da_dk:{ios:"For at tilføje denne web app til hjemmeskærmen: Tryk %icon og derefter <strong>Føj til hjemmeskærm</strong>.",android:'For at tilføje denne web app til hjemmeskærmen, åbn browser egenskaber menuen og tryk på <strong>Føj til hjemmeskærm</strong>. <small>Denne menu kan tilgås ved at trykke på menu knappen, hvis din enhed har en, eller ved at trykke på det øverste højre menu ikon <span class="ath-action-icon">icon</span>.</small>'},en_us:{ios:"To add this web app to the home screen: tap %icon and then <strong>Add to Home Screen</strong>.",android:'To add this web app to the home screen open the browser option menu and tap on <strong>Add to homescreen</strong>. <small>The menu can be accessed by pressing the menu hardware button if your device has one, or by tapping the top right menu icon <br><br><span class="ath-action-icon">icon</span></small>'},es_es:{ios:"Para añadir esta aplicación web a la pantalla de inicio: pulsa %icon y selecciona <strong>Añadir a pantalla de inicio</strong>.",android:'Para añadir esta aplicación web a la pantalla de inicio, abre las opciones y pulsa <strong>Añadir a pantalla inicio</strong>. <small>El menú se puede acceder pulsando el botón táctil en caso de tenerlo, o bien el icono de la parte superior derecha de la pantalla <span class="ath-action-icon">icon</span>.</small>'},fi_fi:{ios:"Liitä tämä sovellus kotivalikkoon: klikkaa %icon ja tämän jälkeen <strong>Lisää kotivalikkoon</strong>.",android:'Lisätäksesi tämän sovelluksen aloitusnäytölle, avaa selaimen valikko ja klikkaa tähti -ikonia tai <strong>Lisää aloitusnäytölle tekstiä</strong>. <small>Valikkoon pääsee myös painamalla menuvalikkoa, jos laitteessasi on sellainen tai koskettamalla oikealla yläkulmassa menu ikonia <span class="ath-action-icon">icon</span>.</small>'},fr_fr:{ios:"Pour ajouter cette application web sur l'écran d'accueil : Appuyez %icon et sélectionnez <strong>Ajouter sur l'écran d'accueil</strong>.",android:'Pour ajouter cette application web sur l\'écran d\'accueil : Appuyez sur le bouton "menu", puis sur <strong>Ajouter sur l\'écran d\'accueil</strong>. <small>Le menu peut-être accessible en appyant sur le bouton "menu" du téléphone s\'il en possède un <i class="fa fa-bars"></i>. Sinon, il se trouve probablement dans la coin supérieur droit du navigateur %icon.</small>'},he_il:{ios:'<span dir="rtl">להוספת האפליקציה למסך הבית: ללחוץ על %icon ואז <strong>הוסף למסך הבית</strong>.</span>',android:'To add this web app to the home screen open the browser option menu and tap on <strong>Add to homescreen</strong>. <small>The menu can be accessed by pressing the menu hardware button if your device has one, or by tapping the top right menu icon <span class="ath-action-icon">icon</span>.</small>'},it_it:{ios:"Per aggiungere questa web app alla schermata iniziale: premi %icon e poi <strong>Aggiungi a Home</strong>.",android:'Per aggiungere questa web app alla schermata iniziale, apri il menu opzioni del browser e premi su <strong>Aggiungi alla homescreen</strong>. <small>Puoi accedere al menu premendo il pulsante hardware delle opzioni se la tua device ne ha uno, oppure premendo l\'icona <span class="ath-action-icon">icon</span> in alto a destra.</small>'},ja_jp:{ios:"このウェプアプリをホーム画面に追加するために%iconを押して<strong>ホーム画面に追加</strong>。",android:'To add this web app to the home screen open the browser option menu and tap on <strong>Add to homescreen</strong>. <small>The menu can be accessed by pressing the menu hardware button if your device has one, or by tapping the top right menu icon <span class="ath-action-icon">icon</span>.</small>'},ko_kr:{ios:"홈 화면에 바로가기 생성: %icon 을 클릭한 후 <strong>홈 화면에 추가</strong>.",android:'브라우저 옵션 메뉴의 <string>홈 화면에 추가</string>를 클릭하여 홈화면에 바로가기를 생성할 수 있습니다. <small>옵션 메뉴는 장치의 메뉴 버튼을 누르거나 오른쪽 상단의 메뉴 아이콘 <span class="ath-action-icon">icon</span>을 클릭하여 접근할 수 있습니다.</small>'},nb_no:{ios:"For å installere denne appen på hjem-skjermen: trykk på %icon og deretter <strong>Legg til på Hjem-skjerm</strong>.",android:'For å legge til denne webappen på startsiden åpner en nettlesermenyen og velger <strong>Legg til på startsiden</strong>. <small>Menyen åpnes ved å trykke på den fysiske menyknappen hvis enheten har det, eller ved å trykke på menyikonet øverst til høyre <span class="ath-action-icon">icon</span>.</small>'},pt_br:{ios:"Para adicionar este app à tela de início: clique %icon e então <strong>Tela de início</strong>.",android:'To add this web app to the home screen open the browser option menu and tap on <strong>Add to homescreen</strong>. <small>The menu can be accessed by pressing the menu hardware button if your device has one, or by tapping the top right menu icon <span class="ath-action-icon">icon</span>.</small>'},pt_pt:{ios:"Para adicionar esta app ao ecrã principal: clique %icon e depois <strong>Ecrã principal</strong>.",android:'To add this web app to the home screen open the browser option menu and tap on <strong>Add to homescreen</strong>. <small>The menu can be accessed by pressing the menu hardware button if your device has one, or by tapping the top right menu icon <span class="ath-action-icon">icon</span>.</small>'},nl_nl:{ios:"Om deze webapp op je telefoon te installeren, klik op %icon en dan <strong>Zet in beginscherm</strong>.",android:'To add this web app to the home screen open the browser option menu and tap on <strong>Add to homescreen</strong>. <small>The menu can be accessed by pressing the menu hardware button if your device has one, or by tapping the top right menu icon <span class="ath-action-icon">icon</span>.</small>'},ru_ru:{ios:'Чтобы добавить этот сайт на свой домашний экран, нажмите на иконку %icon и затем <strong>На экран "Домой"</strong>.',android:'Чтобы добавить сайт на свой домашний экран, откройте меню браузера и нажмите на <strong>Добавить на главный экран</strong>. <small>Меню можно вызвать, нажав на кнопку меню вашего телефона, если она есть. Или найдите иконку сверху справа <span class="ath-action-icon">иконка</span>.</small>'},sv_se:{ios:"För att lägga till denna webbapplikation på hemskärmen: tryck på %icon och därefter <strong>Lägg till på hemskärmen</strong>.",android:'För att lägga till den här webbappen på hemskärmen öppnar du webbläsarens alternativ-meny och väljer <strong>Lägg till på startskärmen</strong>. <small>Man hittar menyn genom att trycka på hårdvaruknappen om din enhet har en sådan, eller genom att trycka på menyikonen högst upp till höger <span class="ath-action-icon">icon</span>.</small>'},zh_cn:{ios:"如要把应用程式加至主屏幕,请点击%icon, 然后<strong>加至主屏幕</strong>",android:'To add this web app to the home screen open the browser option menu and tap on <strong>Add to homescreen</strong>. <small>The menu can be accessed by pressing the menu hardware button if your device has one, or by tapping the top right menu icon <span class="ath-action-icon">icon</span>.</small>'},zh_tw:{ios:"如要把應用程式加至主屏幕, 請點擊%icon, 然後<strong>加至主屏幕</strong>.",android:'To add this web app to the home screen open the browser option menu and tap on <strong>Add to homescreen</strong>. <small>The menu can be accessed by pressing the menu hardware button if your device has one, or by tapping the top right menu icon <span class="ath-action-icon">icon</span>.</small>'}};for(var p in n.intl)n.intl[p.substr(0,2)]=n.intl[p];n.defaults={appID:"org.cubiq.11addtohome",fontSize:15,debug:!1,logging:!1,modal:!1,mandatory:!1,autostart:!0,skipFirstVisit:!1,startDelay:1,lifespan:15,displayPace:1440,maxDisplayCount:0,icon:!0,message:"",validLocation:[],onInit:null,onShow:null,onRemove:null,onAdd:null,onPrivate:null,privateModeOverride:!1,detectHomescreen:!1};var c=e.navigator.userAgent,m=e.navigator;o(n,{hasToken:"#ath"==t.location.hash||h.test(t.location.href)||d.test(t.location.search),isRetina:e.devicePixelRatio&&e.devicePixelRatio>1,isIDevice:/iphone|ipod|ipad/i.test(c),isMobileChrome:c.indexOf("Android")>-1&&/Chrome\/[.0-9]*/.test(c)&&-1==c.indexOf("Version"),isMobileIE:c.indexOf("Windows Phone")>-1,language:m.language&&m.language.toLowerCase().replace("-","_")||""}),n.language=n.language&&n.language in n.intl?n.language:"en_us",n.isMobileSafari=n.isIDevice&&c.indexOf("Safari")>-1&&c.indexOf("CriOS")<0,n.OS=n.isIDevice?"ios":n.isMobileChrome?"android":n.isMobileIE?"windows":"unsupported",n.OSVersion=c.match(/(OS|Android) (\d+[_\.]\d+)/),n.OSVersion=n.OSVersion&&n.OSVersion[2]?+n.OSVersion[2].replace("_","."):0,n.isStandalone="standalone"in e.navigator&&e.navigator.standalone,n.isTablet=n.isMobileSafari&&c.indexOf("iPad")>-1||n.isMobileChrome&&c.indexOf("Mobile")<0,n.isCompatible=n.isMobileSafari&&n.OSVersion>=6||n.isMobileChrome;var u={lastDisplayTime:0,returningVisitor:!1,displayCount:0,optedout:!1,added:!1};n.removeSession=function(e){try{if(!localStorage)throw new Error("localStorage is not defined");localStorage.removeItem(e||n.defaults.appID)}catch(t){}},n.doLog=function(e){this.options.logging&&console.log(e)},n.Class=function(i){if(this.doLog=n.doLog,this.options=o({},n.defaults),o(this.options,i),i.debug&&"undefined"==typeof i.logging&&(this.options.logging=!0),a){if(this.options.mandatory=this.options.mandatory&&("standalone"in e.navigator||this.options.debug),this.options.modal=this.options.modal||this.options.mandatory,this.options.mandatory&&(this.options.startDelay=-.5),this.options.detectHomescreen=this.options.detectHomescreen===!0?"hash":this.options.detectHomescreen,this.options.debug&&(n.isCompatible=!0,n.OS="string"==typeof this.options.debug?this.options.debug:"unsupported"==n.OS?"android":n.OS,n.OSVersion="ios"==n.OS?"8":"4"),this.container=t.documentElement,this.session=this.getItem(this.options.appID),this.session=this.session?JSON.parse(this.session):void 0,!n.hasToken||n.isCompatible&&this.session||(n.hasToken=!1,s()),!n.isCompatible)return void this.doLog("Add to homescreen: not displaying callout because device not supported");this.session=this.session||u;try{if(!localStorage)throw new Error("localStorage is not defined");localStorage.setItem(this.options.appID,JSON.stringify(this.session)),n.hasLocalStorage=!0}catch(r){n.hasLocalStorage=!1,this.options.onPrivate&&this.options.onPrivate.call(this)}for(var l=!this.options.validLocation.length,h=this.options.validLocation.length;h--;)if(this.options.validLocation[h].test(t.location.href)){l=!0;break}if(this.getItem("addToHome")&&this.optOut(),this.session.optedout)return void this.doLog("Add to homescreen: not displaying callout because user opted out");if(this.session.added)return void this.doLog("Add to homescreen: not displaying callout because already added to the homescreen");if(!l)return void this.doLog("Add to homescreen: not displaying callout because not a valid location");if(n.isStandalone)return this.session.added||(this.session.added=!0,this.updateSession(),this.options.onAdd&&n.hasLocalStorage&&this.options.onAdd.call(this)),void this.doLog("Add to homescreen: not displaying callout because in standalone mode");if(this.options.detectHomescreen){if(n.hasToken)return s(),this.session.added||(this.session.added=!0,this.updateSession(),this.options.onAdd&&n.hasLocalStorage&&this.options.onAdd.call(this)),void this.doLog("Add to homescreen: not displaying callout because URL has token, so we are likely coming from homescreen");"hash"==this.options.detectHomescreen?history.replaceState("",e.document.title,t.location.href+"#ath"):"smartURL"==this.options.detectHomescreen?history.replaceState("",e.document.title,t.location.href.replace(/(\/)?$/,"/ath$1")):history.replaceState("",e.document.title,t.location.href+(t.location.search?"&":"?")+"ath=")}if(!this.session.returningVisitor&&(this.session.returningVisitor=!0,this.updateSession(),this.options.skipFirstVisit))return void this.doLog("Add to homescreen: not displaying callout because skipping first visit");if(!this.options.privateModeOverride&&!n.hasLocalStorage)return void this.doLog("Add to homescreen: not displaying callout because browser is in private mode");this.ready=!0,this.options.onInit&&this.options.onInit.call(this),this.options.autostart&&(this.doLog("Add to homescreen: autostart displaying callout"),this.show())}},n.Class.prototype={events:{load:"_delayedShow",error:"_delayedShow",orientationchange:"resize",resize:"resize",scroll:"resize",click:"remove",touchmove:"_preventDefault",transitionend:"_removeElements",webkitTransitionEnd:"_removeElements",MSTransitionEnd:"_removeElements"},handleEvent:function(e){var t=this.events[e.type];t&&this[t](e)},show:function(i){if(this.options.autostart&&!r)return void setTimeout(this.show.bind(this),50);if(this.shown)return void this.doLog("Add to homescreen: not displaying callout because already shown on screen");var o=Date.now(),s=this.session.lastDisplayTime;if(i!==!0){if(!this.ready)return void this.doLog("Add to homescreen: not displaying callout because not ready");if(o-s<6e4*this.options.displayPace)return void this.doLog("Add to homescreen: not displaying callout because displayed recently");if(this.options.maxDisplayCount&&this.session.displayCount>=this.options.maxDisplayCount)return void this.doLog("Add to homescreen: not displaying callout because displayed too many times already")}this.shown=!0,this.session.lastDisplayTime=o,this.session.displayCount++,this.updateSession(),this.applicationIcon||(this.applicationIcon=t.querySelector("ios"==n.OS?'head link[rel^=apple-touch-icon][sizes="152x152"],head link[rel^=apple-touch-icon][sizes="144x144"],head link[rel^=apple-touch-icon][sizes="120x120"],head link[rel^=apple-touch-icon][sizes="114x114"],head link[rel^=apple-touch-icon]':'head link[rel^="shortcut icon"][sizes="196x196"],head link[rel^=apple-touch-icon]'));var a="";"object"==typeof this.options.message&&n.language in this.options.message?a=this.options.message[n.language][n.OS]:"object"==typeof this.options.message&&n.OS in this.options.message?a=this.options.message[n.OS]:this.options.message in n.intl?a=n.intl[this.options.message][n.OS]:""!==this.options.message?a=this.options.message:n.OS in n.intl[n.language]&&(a=n.intl[n.language][n.OS]),a="<p>"+a.replace("%icon",'<span class="ath-action-icon">icon</span>')+"</p>",this.viewport=t.createElement("div"),this.viewport.className="ath-viewport",this.options.modal&&(this.viewport.className+=" ath-modal"),this.options.mandatory&&(this.viewport.className+=" ath-mandatory"),this.viewport.style.position="absolute",this.element=t.createElement("div"),this.element.className="ath-container ath-"+n.OS+" ath-"+n.OS+(n.OSVersion+"").substr(0,1)+" ath-"+(n.isTablet?"tablet":"phone"),this.element.style.cssText="-webkit-transition-property:-webkit-transform,opacity;-webkit-transition-duration:0s;-webkit-transition-timing-function:ease-out;transition-property:transform,opacity;transition-duration:0s;transition-timing-function:ease-out;",this.element.style.webkitTransform="translate3d(0,-"+e.innerHeight+"px,0)",this.element.style.transform="translate3d(0,-"+e.innerHeight+"px,0)",this.options.icon&&this.applicationIcon&&(this.element.className+=" ath-icon",this.img=t.createElement("img"),this.img.className="ath-application-icon",this.img.addEventListener("load",this,!1),this.img.addEventListener("error",this,!1),this.img.src=this.applicationIcon.href,this.element.appendChild(this.img)),this.element.innerHTML+=a,this.viewport.style.left="-99999em",this.viewport.appendChild(this.element),this.container.appendChild(this.viewport),this.img?this.doLog("Add to homescreen: not displaying callout because waiting for img to load"):this._delayedShow()},_delayedShow:function(){setTimeout(this._show.bind(this),1e3*this.options.startDelay+500)},_show:function(){var i=this;this.updateViewport(),e.addEventListener("resize",this,!1),e.addEventListener("scroll",this,!1),e.addEventListener("orientationchange",this,!1),this.options.modal&&t.addEventListener("touchmove",this,!0),this.options.mandatory||setTimeout(function(){i.element.addEventListener("click",i,!0)},1e3),setTimeout(function(){i.element.style.webkitTransitionDuration="1.2s",i.element.style.transitionDuration="1.2s",i.element.style.webkitTransform="translate3d(0,0,0)",i.element.style.transform="translate3d(0,0,0)"},0),this.options.lifespan&&(this.removeTimer=setTimeout(this.remove.bind(this),1e3*this.options.lifespan)),this.options.onShow&&this.options.onShow.call(this)},remove:function(){clearTimeout(this.removeTimer),this.img&&(this.img.removeEventListener("load",this,!1),this.img.removeEventListener("error",this,!1)),e.removeEventListener("resize",this,!1),e.removeEventListener("scroll",this,!1),e.removeEventListener("orientationchange",this,!1),t.removeEventListener("touchmove",this,!0),this.element.removeEventListener("click",this,!0),this.element.addEventListener("transitionend",this,!1),this.element.addEventListener("webkitTransitionEnd",this,!1),this.element.addEventListener("MSTransitionEnd",this,!1),this.element.style.webkitTransitionDuration="0.3s",this.element.style.opacity="0"},_removeElements:function(){this.element.removeEventListener("transitionend",this,!1),this.element.removeEventListener("webkitTransitionEnd",this,!1),this.element.removeEventListener("MSTransitionEnd",this,!1),this.container.removeChild(this.viewport),this.shown=!1,this.options.onRemove&&this.options.onRemove.call(this)},updateViewport:function(){if(this.shown){this.viewport.style.width=e.innerWidth+"px",this.viewport.style.height=e.innerHeight+"px",this.viewport.style.left=e.scrollX+"px",this.viewport.style.top=e.scrollY+"px";var i=t.documentElement.clientWidth;this.orientation=i>t.documentElement.clientHeight?"landscape":"portrait";var o="ios"==n.OS?"portrait"==this.orientation?screen.width:screen.height:screen.width;this.scale=screen.width>i?1:o/e.innerWidth,this.element.style.fontSize=this.options.fontSize/this.scale+"px"}},resize:function(){clearTimeout(this.resizeTimer),this.resizeTimer=setTimeout(this.updateViewport.bind(this),100)},updateSession:function(){n.hasLocalStorage!==!1&&localStorage&&localStorage.setItem(this.options.appID,JSON.stringify(this.session))},clearSession:function(){this.session=u,this.updateSession()},getItem:function(e){try{if(!localStorage)throw new Error("localStorage is not defined");return localStorage.getItem(e)}catch(t){n.hasLocalStorage=!1}},optOut:function(){this.session.optedout=!0,this.updateSession()},optIn:function(){this.session.optedout=!1,this.updateSession()},clearDisplayCount:function(){this.session.displayCount=0,this.updateSession()},_preventDefault:function(e){e.preventDefault(),e.stopPropagation()}},e.addToHomescreen=n}(window,document);
}(jQuery));

/*
	*jQuery Contact form developed by CosminCotor & Enabled
	*Licensed to be used ONLY by CosminCotor & Enabled on the Envato Marketplaces 
	*DO NOT use in commercial projects outside Regular or Extended licenses for the marketplaces.
*/

(function($){
    var formSubmitted="false";jQuery(document).ready(function(e){function t(t,n){formSubmitted="true";var r=e("#"+t).serialize();e.post(e("#"+t).attr("action"),r,function(n){e("#"+t).hide();e("#formSuccessMessageWrap").fadeIn(500)})}function n(n,r){e(".formValidationError").hide();e(".fieldHasError").removeClass("fieldHasError");e("#"+n+" .requiredField").each(function(i){if(e(this).val()==""||e(this).val()==e(this).attr("data-dummy")){e(this).val(e(this).attr("data-dummy"));e(this).focus();e(this).addClass("fieldHasError");e("#"+e(this).attr("id")+"Error").fadeIn(300);return false}if(e(this).hasClass("requiredEmailField")){var s=/^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/;var o="#"+e(this).attr("id");if(!s.test(e(o).val())){e(o).focus();e(o).addClass("fieldHasError");e(o+"Error2").fadeIn(300);return false}}if(formSubmitted=="false"&&i==e("#"+n+" .requiredField").length-1){t(n,r)}})}e("#formSuccessMessageWrap").hide(0);e(".formValidationError").fadeOut(0);e('input[type="text"], input[type="password"], textarea').focus(function(){if(e(this).val()==e(this).attr("data-dummy")){e(this).val("")}});e("input, textarea").blur(function(){if(e(this).val()==""){e(this).val(e(this).attr("data-dummy"))}});e("#contactSubmitButton").click(function(){n(e(this).attr("data-formId"));return false})})
}(jQuery));



    