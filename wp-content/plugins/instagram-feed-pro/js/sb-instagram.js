(function($){

  (function(){var e,t;e=function(){function e(e,t){var n,r;this.options={target:"instafeed",get:"popular",resolution:"thumbnail",sortBy:"none",links:!0,mock:!1,useHttp:!1};if(typeof e=="object")for(n in e)r=e[n],this.options[n]=r;this.context=t!=null?t:this,this.unique=this._genKey()}return e.prototype.hasNext=function(){return typeof this.context.nextUrl=="string"&&this.context.nextUrl.length>0},e.prototype.next=function(){return this.hasNext()?this.run(this.context.nextUrl):!1},e.prototype.run=function(t){var n,r,i;if(typeof this.options.clientId!="string"&&typeof this.options.accessToken!="string")throw new Error("Missing clientId or accessToken.");if(typeof this.options.accessToken!="string"&&typeof this.options.clientId!="string")throw new Error("Missing clientId or accessToken.");return this.options.before!=null&&typeof this.options.before=="function"&&this.options.before.call(this),typeof document!="undefined"&&document!==null&&(i=document.createElement("script"),i.id="instafeed-fetcher",i.src=t||this._buildUrl(),n=document.getElementsByTagName("head"),n[0].appendChild(i),r="instafeedCache"+this.unique,window[r]=new e(this.options,this),window[r].unique=this.unique),!0},e.prototype.parse=function(e){var t,n,r,i,s,o,u,a,f,l,c,h,p,d,v,m,g,y,b,w,E,S;if(typeof e!="object"){if(this.options.error!=null&&typeof this.options.error=="function")return this.options.error.call(this,"Invalid JSON data"),!1;throw new Error("Invalid JSON response")}if(e.meta.code!==200){if(this.options.error!=null&&typeof this.options.error=="function")return this.options.error.call(this,e.meta.error_message),!1;throw new Error("Error from Instagram: "+e.meta.error_message)}if(e.data.length===0){if(this.options.error!=null&&typeof this.options.error=="function")return this.options.error.call(this,"No images were returned from Instagram"),!1;throw new Error("No images were returned from Instagram")}this.options.success!=null&&typeof this.options.success=="function"&&this.options.success.call(this,e),this.context.nextUrl="",e.pagination!=null&&(this.context.nextUrl=e.pagination.next_url);if(this.options.sortBy!=="none"){this.options.sortBy==="random"?d=["","random"]:d=this.options.sortBy.split("-"),p=d[0]==="least"?!0:!1;switch(d[1]){case"random":e.data.sort(function(){return.5-Math.random()});break;case"recent":e.data=this._sortBy(e.data,"created_time",p);break;case"liked":e.data=this._sortBy(e.data,"likes.count",p);break;case"commented":e.data=this._sortBy(e.data,"comments.count",p);break;default:throw new Error("Invalid option for sortBy: '"+this.options.sortBy+"'.")}}if(typeof document!="undefined"&&document!==null&&this.options.mock===!1){a=e.data,this.options.limit!=null&&a.length>this.options.limit&&(a=a.slice(0,this.options.limit+1||9e9)),n=document.createDocumentFragment(),this.options.filter!=null&&typeof this.options.filter=="function"&&(a=this._filter(a,this.options.filter));if(this.options.template!=null&&typeof this.options.template=="string"){i="",o="",l="",v=document.createElement("div");for(m=0,b=a.length;m<b;m++)s=a[m],u=s.images[this.options.resolution].url,this.options.useHttp||(u=u.replace("http://","//")),o=this._makeTemplate(this.options.template,{model:s,id:s.id,link:s.link,image:u,caption:this._getObjectProperty(s,"caption.text"),likes:s.likes.count,comments:s.comments.count,location:this._getObjectProperty(s,"location.name")}),i+=o;v.innerHTML=i,S=[].slice.call(v.childNodes);for(g=0,w=S.length;g<w;g++)h=S[g],n.appendChild(h)}else for(y=0,E=a.length;y<E;y++)s=a[y],f=document.createElement("img"),u=s.images[this.options.resolution].url,this.options.useHttp||(u=u.replace("http://","//")),f.src=u,this.options.links===!0?(t=document.createElement("a"),t.href=s.link,t.appendChild(f),n.appendChild(t)):n.appendChild(f);this.options.target.append(n),r=document.getElementsByTagName("head")[0],r.removeChild(document.getElementById("instafeed-fetcher")),c="instafeedCache"+this.unique,window[c]=void 0;try{delete window[c]}catch(x){}}return this.options.after!=null&&typeof this.options.after=="function"&&this.options.after.call(this),!0},e.prototype._buildUrl=function(){var e,t,n;e="https://api.instagram.com/v1";switch(this.options.get){case"popular":t="media/popular";break;case"tagged":if(typeof this.options.tagName!="string")throw new Error("No tag name specified. Use the 'tagName' option.");t="tags/"+this.options.tagName+"/media/recent";break;case"location":if(typeof this.options.locationId!="number")throw new Error("No location specified. Use the 'locationId' option.");t="locations/"+this.options.locationId+"/media/recent";break;case"user":if(typeof this.options.userId!="number")throw new Error("No user specified. Use the 'userId' option.");if(typeof this.options.accessToken!="string")throw new Error("No access token. Use the 'accessToken' option.");t="users/"+this.options.userId+"/media/recent";break;default:throw new Error("Invalid option for get: '"+this.options.get+"'.")}return n=""+e+"/"+t,this.options.accessToken!=null?n+="?access_token="+this.options.accessToken:n+="?client_id="+this.options.clientId,this.options.limit!=null&&(n+="&count="+this.options.limit),n+="&callback=instafeedCache"+this.unique+".parse",n},e.prototype._genKey=function(){var e;return e=function(){return((1+Math.random())*65536|0).toString(16).substring(1)},""+e()+e()+e()+e()},e.prototype._makeTemplate=function(e,t){var n,r,i,s,o;r=/(?:\{{2})([\w\[\]\.]+)(?:\}{2})/,n=e;while(r.test(n))i=n.match(r)[1],s=(o=this._getObjectProperty(t,i))!=null?o:"",n=n.replace(r,""+s);return n},e.prototype._getObjectProperty=function(e,t){var n,r;t=t.replace(/\[(\w+)\]/g,".$1"),r=t.split(".");while(r.length){n=r.shift();if(!(e!=null&&n in e))return null;e=e[n]}return e},e.prototype._sortBy=function(e,t,n){var r;return r=function(e,r){var i,s;return i=this._getObjectProperty(e,t),s=this._getObjectProperty(r,t),n?i>s?1:-1:i<s?1:-1},e.sort(r.bind(this)),e},e.prototype._filter=function(e,t){var n,r,i,s,o;n=[],i=function(e){if(t(e))return n.push(e)};for(s=0,o=e.length;s<o;s++)r=e[s],i(r);return n},e}(),t=typeof exports!="undefined"&&exports!==null?exports:window,t.instagramfeed=e}).call(this);

  //Shim for "fixing" IE's lack of support (IE < 9) for applying slice on host objects like NamedNodeMap, NodeList, and HTMLCollection) https://github.com/stevenschobert/instafeed.js/issues/84
  (function(){"use strict";var e=Array.prototype.slice;try{e.call(document.documentElement)}catch(t){Array.prototype.slice=function(t,n){n=typeof n!=="undefined"?n:this.length;if(Object.prototype.toString.call(this)==="[object Array]"){return e.call(this,t,n)}var r,i=[],s,o=this.length;var u=t||0;u=u>=0?u:o+u;var a=n?n:o;if(n<0){a=o+n}s=a-u;if(s>0){i=new Array(s);if(this.charAt){for(r=0;r<s;r++){i[r]=this.charAt(u+r)}}else{for(r=0;r<s;r++){i[r]=this[u+r]}}}return i}}})()

  //IE8 also doesn't offer the .bind() method triggered by the 'sortBy' property. Copy and paste the polyfill offered here:
  if(!Function.prototype.bind){Function.prototype.bind=function(e){if(typeof this!=="function"){throw new TypeError("Function.prototype.bind - what is trying to be bound is not callable")}var t=Array.prototype.slice.call(arguments,1),n=this,r=function(){},i=function(){return n.apply(this instanceof r&&e?this:e,t.concat(Array.prototype.slice.call(arguments)))};r.prototype=this.prototype;i.prototype=new r;return i}}

    /*! jQuery Mobile v1.4.5 | Copyright 2010, 2014 jQuery Foundation, Inc. | jquery.org/license */
    (function(e,t,n){typeof define=="function"&&define.amd?define(["jquery"],function(r){return n(r,e,t),r.mobile}):n(e.jQuery,e,t)})(this,document,function(e,t,n,r){(function(e,t,n,r){function T(e){while(e&&typeof e.originalEvent!="undefined")e=e.originalEvent;return e}function N(t,n){var i=t.type,s,o,a,l,c,h,p,d,v;t=e.Event(t),t.type=n,s=t.originalEvent,o=e.event.props,i.search(/^(mouse|click)/)>-1&&(o=f);if(s)for(p=o.length,l;p;)l=o[--p],t[l]=s[l];i.search(/mouse(down|up)|click/)>-1&&!t.which&&(t.which=1);if(i.search(/^touch/)!==-1){a=T(s),i=a.touches,c=a.changedTouches,h=i&&i.length?i[0]:c&&c.length?c[0]:r;if(h)for(d=0,v=u.length;d<v;d++)l=u[d],t[l]=h[l]}return t}function C(t){var n={},r,s;while(t){r=e.data(t,i);for(s in r)r[s]&&(n[s]=n.hasVirtualBinding=!0);t=t.parentNode}return n}function k(t,n){var r;while(t){r=e.data(t,i);if(r&&(!n||r[n]))return t;t=t.parentNode}return null}function L(){g=!1}function A(){g=!0}function O(){E=0,v.length=0,m=!1,A()}function M(){L()}function _(){D(),c=setTimeout(function(){c=0,O()},e.vmouse.resetTimerDuration)}function D(){c&&(clearTimeout(c),c=0)}function P(t,n,r){var i;if(r&&r[t]||!r&&k(n.target,t))i=N(n,t),e(n.target).trigger(i);return i}function H(t){var n=e.data(t.target,s),r;!m&&(!E||E!==n)&&(r=P("v"+t.type,t),r&&(r.isDefaultPrevented()&&t.preventDefault(),r.isPropagationStopped()&&t.stopPropagation(),r.isImmediatePropagationStopped()&&t.stopImmediatePropagation()))}function B(t){var n=T(t).touches,r,i,o;n&&n.length===1&&(r=t.target,i=C(r),i.hasVirtualBinding&&(E=w++,e.data(r,s,E),D(),M(),d=!1,o=T(t).touches[0],h=o.pageX,p=o.pageY,P("vmouseover",t,i),P("vmousedown",t,i)))}function j(e){if(g)return;d||P("vmousecancel",e,C(e.target)),d=!0,_()}function F(t){if(g)return;var n=T(t).touches[0],r=d,i=e.vmouse.moveDistanceThreshold,s=C(t.target);d=d||Math.abs(n.pageX-h)>i||Math.abs(n.pageY-p)>i,d&&!r&&P("vmousecancel",t,s),P("vmousemove",t,s),_()}function I(e){if(g)return;A();var t=C(e.target),n,r;P("vmouseup",e,t),d||(n=P("vclick",e,t),n&&n.isDefaultPrevented()&&(r=T(e).changedTouches[0],v.push({touchID:E,x:r.clientX,y:r.clientY}),m=!0)),P("vmouseout",e,t),d=!1,_()}function q(t){var n=e.data(t,i),r;if(n)for(r in n)if(n[r])return!0;return!1}function R(){}function U(t){var n=t.substr(1);return{setup:function(){q(this)||e.data(this,i,{});var r=e.data(this,i);r[t]=!0,l[t]=(l[t]||0)+1,l[t]===1&&b.bind(n,H),e(this).bind(n,R),y&&(l.touchstart=(l.touchstart||0)+1,l.touchstart===1&&b.bind("touchstart",B).bind("touchend",I).bind("touchmove",F).bind("scroll",j))},teardown:function(){--l[t],l[t]||b.unbind(n,H),y&&(--l.touchstart,l.touchstart||b.unbind("touchstart",B).unbind("touchmove",F).unbind("touchend",I).unbind("scroll",j));var r=e(this),s=e.data(this,i);s&&(s[t]=!1),r.unbind(n,R),q(this)||r.removeData(i)}}}var i="virtualMouseBindings",s="virtualTouchID",o="vmouseover vmousedown vmousemove vmouseup vclick vmouseout vmousecancel".split(" "),u="clientX clientY pageX pageY screenX screenY".split(" "),a=e.event.mouseHooks?e.event.mouseHooks.props:[],f=e.event.props.concat(a),l={},c=0,h=0,p=0,d=!1,v=[],m=!1,g=!1,y="addEventListener"in n,b=e(n),w=1,E=0,S,x;e.vmouse={moveDistanceThreshold:10,clickDistanceThreshold:10,resetTimerDuration:1500};for(x=0;x<o.length;x++)e.event.special[o[x]]=U(o[x]);y&&n.addEventListener("click",function(t){var n=v.length,r=t.target,i,o,u,a,f,l;if(n){i=t.clientX,o=t.clientY,S=e.vmouse.clickDistanceThreshold,u=r;while(u){for(a=0;a<n;a++){f=v[a],l=0;if(u===r&&Math.abs(f.x-i)<S&&Math.abs(f.y-o)<S||e.data(u,s)===f.touchID){t.preventDefault(),t.stopPropagation();return}}u=u.parentNode}}},!0)})(e,t,n),function(e){e.mobile={}}(e),function(e,t){var r={touch:"ontouchend"in n};e.mobile.support=e.mobile.support||{},e.extend(e.support,r),e.extend(e.mobile.support,r)}(e),function(e,t,r){function l(t,n,i,s){var o=i.type;i.type=n,s?e.event.trigger(i,r,t):e.event.dispatch.call(t,i),i.type=o}var i=e(n),s=e.mobile.support.touch,o="touchmove scroll",u=s?"touchstart":"mousedown",a=s?"touchend":"mouseup",f=s?"touchmove":"mousemove";e.each("touchstart touchmove touchend tap taphold swipe swipeleft swiperight scrollstart scrollstop".split(" "),function(t,n){e.fn[n]=function(e){return e?this.bind(n,e):this.trigger(n)},e.attrFn&&(e.attrFn[n]=!0)}),e.event.special.scrollstart={enabled:!0,setup:function(){function s(e,n){r=n,l(t,r?"scrollstart":"scrollstop",e)}var t=this,n=e(t),r,i;n.bind(o,function(t){if(!e.event.special.scrollstart.enabled)return;r||s(t,!0),clearTimeout(i),i=setTimeout(function(){s(t,!1)},50)})},teardown:function(){e(this).unbind(o)}},e.event.special.tap={tapholdThreshold:750,emitTapOnTaphold:!0,setup:function(){var t=this,n=e(t),r=!1;n.bind("vmousedown",function(s){function a(){clearTimeout(u)}function f(){a(),n.unbind("vclick",c).unbind("vmouseup",a),i.unbind("vmousecancel",f)}function c(e){f(),!r&&o===e.target?l(t,"tap",e):r&&e.preventDefault()}r=!1;if(s.which&&s.which!==1)return!1;var o=s.target,u;n.bind("vmouseup",a).bind("vclick",c),i.bind("vmousecancel",f),u=setTimeout(function(){e.event.special.tap.emitTapOnTaphold||(r=!0),l(t,"taphold",e.Event("taphold",{target:o}))},e.event.special.tap.tapholdThreshold)})},teardown:function(){e(this).unbind("vmousedown").unbind("vclick").unbind("vmouseup"),i.unbind("vmousecancel")}},e.event.special.swipe={scrollSupressionThreshold:30,durationThreshold:1e3,horizontalDistanceThreshold:30,verticalDistanceThreshold:30,getLocation:function(e){var n=t.pageXOffset,r=t.pageYOffset,i=e.clientX,s=e.clientY;if(e.pageY===0&&Math.floor(s)>Math.floor(e.pageY)||e.pageX===0&&Math.floor(i)>Math.floor(e.pageX))i-=n,s-=r;else if(s<e.pageY-r||i<e.pageX-n)i=e.pageX-n,s=e.pageY-r;return{x:i,y:s}},start:function(t){var n=t.originalEvent.touches?t.originalEvent.touches[0]:t,r=e.event.special.swipe.getLocation(n);return{time:(new Date).getTime(),coords:[r.x,r.y],origin:e(t.target)}},stop:function(t){var n=t.originalEvent.touches?t.originalEvent.touches[0]:t,r=e.event.special.swipe.getLocation(n);return{time:(new Date).getTime(),coords:[r.x,r.y]}},handleSwipe:function(t,n,r,i){if(n.time-t.time<e.event.special.swipe.durationThreshold&&Math.abs(t.coords[0]-n.coords[0])>e.event.special.swipe.horizontalDistanceThreshold&&Math.abs(t.coords[1]-n.coords[1])<e.event.special.swipe.verticalDistanceThreshold){var s=t.coords[0]>n.coords[0]?"swipeleft":"swiperight";return l(r,"swipe",e.Event("swipe",{target:i,swipestart:t,swipestop:n}),!0),l(r,s,e.Event(s,{target:i,swipestart:t,swipestop:n}),!0),!0}return!1},eventInProgress:!1,setup:function(){var t,n=this,r=e(n),s={};t=e.data(this,"mobile-events"),t||(t={length:0},e.data(this,"mobile-events",t)),t.length++,t.swipe=s,s.start=function(t){if(e.event.special.swipe.eventInProgress)return;e.event.special.swipe.eventInProgress=!0;var r,o=e.event.special.swipe.start(t),u=t.target,l=!1;s.move=function(t){if(!o||t.isDefaultPrevented())return;r=e.event.special.swipe.stop(t),l||(l=e.event.special.swipe.handleSwipe(o,r,n,u),l&&(e.event.special.swipe.eventInProgress=!1)),Math.abs(o.coords[0]-r.coords[0])>e.event.special.swipe.scrollSupressionThreshold&&t.preventDefault()},s.stop=function(){l=!0,e.event.special.swipe.eventInProgress=!1,i.off(f,s.move),s.move=null},i.on(f,s.move).one(a,s.stop)},r.on(u,s.start)},teardown:function(){var t,n;t=e.data(this,"mobile-events"),t&&(n=t.swipe,delete t.swipe,t.length--,t.length===0&&e.removeData(this,"mobile-events")),n&&(n.start&&e(this).off(u,n.start),n.move&&i.off(f,n.move),n.stop&&i.off(a,n.stop))}},e.each({scrollstop:"scrollstart",taphold:"tap",swipeleft:"swipe.left",swiperight:"swipe.right"},function(t,n){e.event.special[t]={setup:function(){e(this).bind(n,e.noop)},teardown:function(){e(this).unbind(n)}}})}(e,this)});

  /* Lightbox v2.7.1 by Lokesh Dhakar - http://lokeshdhakar.com/projects/lightbox2/ */
  (function() {
    var a = jQuery,
        b = function() {
            function a() {
                this.fadeDuration = 500, this.fitImagesInViewport = !0, this.resizeDuration = 700, this.positionFromTop = 50, this.showImageNumberLabel = !0, this.alwaysShowNavOnTouchDevices = !1, this.wrapAround = !1
            }
            return a.prototype.albumLabel = function(a, b) {
                return a + " / " + b
            }, a
        }(),
        c = function() {
            function b(a) {
                this.options = a, this.album = [], this.currentImageIndex = void 0, this.init()
            }
            return b.prototype.init = function() {
                this.enable(), this.build()
            }, b.prototype.enable = function() {
                var b = this;
                a("body").on("click", "a[rel^=lightbox], area[rel^=lightbox], a[data-lightbox-sbi], area[data-lightbox-sbi]", function(c) {
                    return b.start(a(c.currentTarget)), !1
                })
            }, b.prototype.build = function() {
                var b = this;
                a("<div id='sbi_lightboxOverlay' class='sbi_lightboxOverlay'></div><div id='sbi_lightbox' class='sbi_lightbox'><div class='sbi_lb-outerContainer'><div class='sbi_lb-container'><video class='sbi_video' src='' poster='' controls></video><img class='sbi_lb-image' src='' /><div class='sbi_lb-nav'><a class='sbi_lb-prev' href='' ></a><a class='sbi_lb-next' href='' ></a></div><div class='sbi_lb-loader'><a class='sbi_lb-cancel'></a></div></div></div><div class='sbi_lb-dataContainer'><div class='sbi_lb-data'><div class='sbi_lb-details'><span class='sbi_lb-caption'></span><span class='sbi_lb-number'></span><div class='sbi_lightbox_action sbi_share'><a href='JavaScript:void(0);'><i class='fa fa-share'></i>Share</a><p class='sbi_lightbox_tooltip sbi_tooltip_social'><a href='' target='_blank' id='sbi_facebook_icon'><i class='fa fa-facebook-square'></i></a><a href='' target='_blank' id='sbi_twitter_icon'><i class='fa fa-twitter'></i></a><a href='' target='_blank' id='sbi_google_icon'><i class='fa fa-google-plus'></i></a><a href='' target='_blank' id='sbi_linkedin_icon'><i class='fa fa-linkedin'></i></a><a href='' id='sbi_pinterest_icon' target='_blank'><i class='fa fa-pinterest'></i></a><i class='fa fa-play fa-rotate-90'></i></p></div><div class='sbi_lightbox_action sbi_instagram'><a href='http://instagram.com/' target='_blank'><i class='fa fa-instagram'></i>Instagram</a></div><div id='sbi_mod_link' class='sbi_lightbox_action'><a href='JavaScript:void(0);'><i class='fa fa-times'></i>Hide photo (admin)</a><p id='sbi_mod_box' class='sbi_lightbox_tooltip'>Add this ID to the plugin's <strong>Hide Photos</strong> setting: <span id='sbi_photo_id'></span><i class='fa fa-play fa-rotate-90'></i></p></div></div><div class='sbi_lb-closeContainer'><a class='sbi_lb-close'></a></div></div></div></div>").appendTo(a("body")), this.$lightbox = a("#sbi_lightbox"), this.$overlay = a("#sbi_lightboxOverlay"), this.$outerContainer = this.$lightbox.find(".sbi_lb-outerContainer"), this.$container = this.$lightbox.find(".sbi_lb-container"), this.containerTopPadding = parseInt(this.$container.css("padding-top"), 10), this.containerRightPadding = parseInt(this.$container.css("padding-right"), 10), this.containerBottomPadding = parseInt(this.$container.css("padding-bottom"), 10), this.containerLeftPadding = parseInt(this.$container.css("padding-left"), 10), this.$overlay.hide().on("click", function() {
                    return b.end(), !1
              }), $(document).on('click', function(event, b, c) {
              //Fade out the lightbox if click anywhere outside of the two elements defined below
          if (!$(event.target).closest('.sbi_lb-outerContainer').length) {
            if (!$(event.target).closest('.sbi_lb-dataContainer').length) {
              //Fade out lightbox
              $('#sbi_lightboxOverlay, #sbi_lightbox').fadeOut();
              //Pause video
              if( supports_video() ) $('#sbi_lightbox .sbi_video')[0].pause();
            }
          }
        }), this.$lightbox.hide(),
                $('#sbi_lightboxOverlay').on("click", function(c) {
                    if( supports_video() ) $('#sbi_lightbox .sbi_video')[0].pause();
                    return "sbi_lightbox" === a(c.target).attr("id") && b.end(), !1
                }), this.$lightbox.find(".sbi_lb-prev").on("click", function() {
                    if( supports_video() ) $('#sbi_lightbox .sbi_video')[0].pause();
                    return b.changeImage(0 === b.currentImageIndex ? b.album.length - 1 : b.currentImageIndex - 1), !1
                }), this.$lightbox.find(".sbi_lb-container").on("swiperight", function() {
                    if( supports_video() ) $('#sbi_lightbox .sbi_video')[0].pause();
                    return b.changeImage(0 === b.currentImageIndex ? b.album.length - 1 : b.currentImageIndex - 1), !1
                }), this.$lightbox.find(".sbi_lb-next").on("click", function() {
                    if( supports_video() ) $('#sbi_lightbox .sbi_video')[0].pause();
                    return b.changeImage(b.currentImageIndex === b.album.length - 1 ? 0 : b.currentImageIndex + 1), !1
                }), this.$lightbox.find(".sbi_lb-container").on("swipeleft", function() {
                    if( supports_video() ) $('#sbi_lightbox .sbi_video')[0].pause();
                    return b.changeImage(b.currentImageIndex === b.album.length - 1 ? 0 : b.currentImageIndex + 1), !1
                }), this.$lightbox.find(".sbi_lb-loader, .sbi_lb-close").on("click", function() {
                    if( supports_video() ) $('#sbi_lightbox .sbi_video')[0].pause();
                    return b.end(), !1
                })
            }, b.prototype.start = function(b) {
                function c(a) {
                    d.album.push({
                        link: a.attr("href"),
                        title: a.attr("data-title") || a.attr("title"),
                        video: a.attr("data-video"),
                        id: a.attr("data-id"),
                        url: a.attr("data-url"),
                        user: a.attr("data-user"),
                        avatar: a.attr("data-avatar")
                    })
                }
                var d = this,
                    e = a(window);
                e.on("resize", a.proxy(this.sizeOverlay, this)), a("select, object, embed").css({
                    visibility: "hidden"
                }), this.sizeOverlay(), this.album = [];
                var f, g = 0,
                    h = b.attr("data-lightbox-sbi");
                if (h) {
                    f = a(b.prop("tagName") + '[data-lightbox-sbi="' + h + '"]');
                    for (var i = 0; i < f.length; i = ++i) c(a(f[i])), f[i] === b[0] && (g = i)
                } else if ("lightbox" === b.attr("rel")) c(b);
                else {
                    f = a(b.prop("tagName") + '[rel="' + b.attr("rel") + '"]');
                    for (var j = 0; j < f.length; j = ++j) c(a(f[j])), f[j] === b[0] && (g = j)
                }
                var k = e.scrollTop() + this.options.positionFromTop,
                    l = e.scrollLeft();
                this.$lightbox.css({
                    top: k + "px",
                    left: l + "px"
                }).fadeIn(this.options.fadeDuration), this.changeImage(g)
            }, b.prototype.changeImage = function(b) {
                var c = this;
                this.disableKeyboardNav();
                var d = this.$lightbox.find(".sbi_lb-image");
                this.$overlay.fadeIn(this.options.fadeDuration), a(".sbi_lb-loader").fadeIn("slow"), this.$lightbox.find(".sbi_lb-image, .sbi_lb-nav, .sbi_lb-prev, .sbi_lb-next, .sbi_lb-dataContainer, .sbi_lb-numbers, .sbi_lb-caption").hide(), this.$outerContainer.addClass("animating");
                var e = new Image;
                e.onload = function() {
                    var f, g, h, i, j, k, l;
                    d.attr("src", c.album[b].link), f = a(e), d.width(e.width), d.height(e.height), c.options.fitImagesInViewport && (l = a(window).width(), k = a(window).height(), j = l - c.containerLeftPadding - c.containerRightPadding - 20, i = k - c.containerTopPadding - c.containerBottomPadding - 150, (e.width > j || e.height > i) && (e.width / j > e.height / i ? (h = j, g = parseInt(e.height / (e.width / h), 10), d.width(h), d.height(g)) : (g = i, h = parseInt(e.width / (e.height / g), 10), d.width(h), d.height(g)))), c.sizeContainer(d.width(), d.height())
                }, e.src = this.album[b].link, this.currentImageIndex = b
            }, b.prototype.sizeOverlay = function() {
                this.$overlay.width(a(window).width()).height(a(document).height())
            }, b.prototype.sizeContainer = function(a, b) {
                function c() {
                    d.$lightbox.find(".sbi_lb-dataContainer").width(g), d.$lightbox.find(".sbi_lb-prevLink").height(h), d.$lightbox.find(".sbi_lb-nextLink").height(h), d.showImage()
                }
                var d = this,
                    e = this.$outerContainer.outerWidth(),
                    f = this.$outerContainer.outerHeight(),
                    g = a + this.containerLeftPadding + this.containerRightPadding,
                    h = b + this.containerTopPadding + this.containerBottomPadding;
                e !== g || f !== h ? this.$outerContainer.animate({
                    width: g,
                    height: h
                }, this.options.resizeDuration, "swing", function() {
                    c()
                }) : c()
            }, b.prototype.showImage = function() {
                this.$lightbox.find(".sbi_lb-loader").hide(), this.$lightbox.find(".sbi_lb-image").fadeIn("slow"), this.updateNav(), this.updateDetails(), this.preloadNeighboringImages(), this.enableKeyboardNav()
            }, b.prototype.updateNav = function() {
                var a = !1;
                try {
                    document.createEvent("TouchEvent"), a = this.options.alwaysShowNavOnTouchDevices ? !0 : !1
                } catch (b) {}
                this.$lightbox.find(".sbi_lb-nav").show(), this.album.length > 1 && (this.options.wrapAround ? (a && this.$lightbox.find(".sbi_lb-prev, .sbi_lb-next").css("opacity", "1"), this.$lightbox.find(".sbi_lb-prev, .sbi_lb-next").show()) : (this.currentImageIndex > 0 && (this.$lightbox.find(".sbi_lb-prev").show(), a && this.$lightbox.find(".sbi_lb-prev").css("opacity", "1")), this.currentImageIndex < this.album.length - 1 && (this.$lightbox.find(".sbi_lb-next").show(), a && this.$lightbox.find(".sbi_lb-next").css("opacity", "1"))))
            }, b.prototype.updateDetails = function() {
                var b = this;

                /** NEW PHOTO ACTION **/
                //Switch video when either a new popup or navigating to new one
                if( supports_video() ){
                  $('#sbi_lightbox').removeClass('sbi_video_lightbox');
                  if( this.album[this.currentImageIndex].video.length ){
                    $('#sbi_lightbox').addClass('sbi_video_lightbox');
                    $('.sbi_video').attr({
                      'src' : this.album[this.currentImageIndex].video,
                      'poster' : this.album[this.currentImageIndex].link,
                      'autoplay' : 'true'
                    });
                }
            }
                $('#sbi_lightbox .sbi_instagram a').attr('href', this.album[this.currentImageIndex].url);
                $('#sbi_lightbox .sbi_lightbox_tooltip').hide();
                $('#sbi_lightbox #sbi_mod_box').find('#sbi_photo_id').text( this.album[this.currentImageIndex].id );
                //Change social media sharing links on the fly
                $('#sbi_lightbox #sbi_facebook_icon').attr('href', 'https://www.facebook.com/sharer/sharer.php?u=' + this.album[this.currentImageIndex].url)+'&t=Text';
                $('#sbi_lightbox #sbi_twitter_icon').attr('href', 'https://twitter.com/home?status='+this.album[this.currentImageIndex].url+' ' + this.album[this.currentImageIndex].title);
                $('#sbi_lightbox #sbi_google_icon').attr('href', 'https://plus.google.com/share?url='+this.album[this.currentImageIndex].url);
                $('#sbi_lightbox #sbi_linkedin_icon').attr('href', 'https://www.linkedin.com/shareArticle?mini=true&url='+this.album[this.currentImageIndex].url+'&title='+this.album[this.currentImageIndex].title);
                $('#sbi_lightbox #sbi_pinterest_icon').attr('href', 'https://pinterest.com/pin/create/button/?url='+this.album[this.currentImageIndex].url+'&media='+this.album[this.currentImageIndex].link+'&description='+this.album[this.currentImageIndex].title);

                "undefined" != typeof this.album[this.currentImageIndex].title && "" !== this.album[this.currentImageIndex].title && this.$lightbox.find(".sbi_lb-caption").html('<a class="sbi_lightbox_username" href="http://instagram.com/'+this.album[this.currentImageIndex].user+'" target="_blank"><img src="'+this.album[this.currentImageIndex].avatar+'" /><p>@'+this.album[this.currentImageIndex].user + '</p></a> ' + this.album[this.currentImageIndex].title).fadeIn("fast"), this.album.length > 1 && this.options.showImageNumberLabel ? this.$lightbox.find(".sbi_lb-number").text(this.options.albumLabel(this.currentImageIndex + 1, this.album.length)).fadeIn("fast") : this.$lightbox.find(".sbi_lb-number").hide(), this.$outerContainer.removeClass("animating"), this.$lightbox.find(".sbi_lb-dataContainer").fadeIn(this.options.resizeDuration, function() {
                    return b.sizeOverlay()
                })
            }, b.prototype.preloadNeighboringImages = function() {
                if (this.album.length > this.currentImageIndex + 1) {
                    var a = new Image;
                    a.src = this.album[this.currentImageIndex + 1].link
                }
                if (this.currentImageIndex > 0) {
                    var b = new Image;
                    b.src = this.album[this.currentImageIndex - 1].link
                }
            }, b.prototype.enableKeyboardNav = function() {
                a(document).on("keyup.keyboard", a.proxy(this.keyboardAction, this))
            }, b.prototype.disableKeyboardNav = function() {
                a(document).off(".keyboard")
            }, b.prototype.keyboardAction = function(a) {
                var b = 27,
                    c = 37,
                    d = 39,
                    e = a.keyCode,
                    f = String.fromCharCode(e).toLowerCase();
                e === b || f.match(/x|o|c/) ? this.end() : "p" === f || e === c ? 0 !== this.currentImageIndex ? this.changeImage(this.currentImageIndex - 1) : this.options.wrapAround && this.album.length > 1 && this.changeImage(this.album.length - 1) : ("n" === f || e === d) && (this.currentImageIndex !== this.album.length - 1 ? this.changeImage(this.currentImageIndex + 1) : this.options.wrapAround && this.album.length > 1 && this.changeImage(0))
            }, b.prototype.end = function() {
                this.disableKeyboardNav(), a(window).off("resize", this.sizeOverlay), this.$lightbox.fadeOut(this.options.fadeDuration), this.$overlay.fadeOut(this.options.fadeDuration), a("select, object, embed").css({
                    visibility: "visible"
                })
            }, b
        }();
    a(function() {
        {
            var a = new b;
            new c(a)
        }
    })
}).call(this);
//Checks whether browser support HTML5 video element
function supports_video() {
  return !!document.createElement('video').canPlayType;
}

  function sbi_init(){

        var sbiTouchDevice = false;
        if (isTouchDevice() === true) sbiTouchDevice = true;

    var $i = 0; //Used for iterating lightbox
    $('#sb_instagram.sbi').each(function(){
      $i = $i+2;

      var $self = $(this),
        $target = $self.find('#sbi_images'),
        $loadBtn = $self.find("#sbi_load .sbi_load_btn"),
        imgRes = 'standard_resolution',
                cols = parseInt( this.getAttribute('data-cols') ),
        //Convert styles JSON string to an object
        feedOptions = JSON.parse( this.getAttribute('data-options') ),
        showcaption = '',
        showlikes = '',
        getType = 'user',
        sortby = 'none',
                hovercolorstyles = '',
        hovertextstyles = '',
                img_full = '',
                num = this.getAttribute('data-num'),
                user_id = this.getAttribute('data-id'),
                posts_arr = [],
                $header = '',
                disablelightbox = feedOptions.disablelightbox;

      if( feedOptions.showcaption == 'false' || feedOptions.showcaption == '' ) showcaption = 'style="display: none;"';
      if( feedOptions.showlikes == 'false' || feedOptions.showlikes == '' ) showlikes = 'display: none;';
      if( feedOptions.type == 'hashtag' ) getType = 'tagged';
      if( feedOptions.sortby !== '' ) sortby = feedOptions.sortby;
            if( feedOptions.hovercolor !== '0,0,0' ) hovercolorstyles = 'style="background: rgba('+feedOptions.hovercolor+',0.85)"';
      if( feedOptions.hovertextcolor !== '0,0,0' ) hovertextstyles = 'style="color: rgba('+feedOptions.hovertextcolor+',1)"';


      switch( this.getAttribute('data-res') ) {
                case 'auto':
                    var feedWidth = $('#sb_instagram').innerWidth(),
                        colWidth = $('#sb_instagram').innerWidth() / cols;

                    if( feedWidth < 680 ) colWidth = 300; //Use 306x306 images
                    if( feedWidth < 480 && feedWidth > 300 ) colWidth = 480; //Use full size images

                    if( colWidth < 150 ){
                        imgRes = 'thumbnail';
                    } else if( colWidth < 306 ){
                        imgRes = 'low_resolution';
                    } else {
                        imgRes = 'standard_resolution';
                    }

                    break;
          case 'thumb':
              imgRes = 'thumbnail';
              break;
          case 'medium':
              imgRes = 'low_resolution';
              break;
          default:
              imgRes = 'standard_resolution';
      }


            //Split comma separated hashtags into array
            if(getType == 'tagged'){
                var hashtags_arr = feedOptions.hashtag.replace(/ /g,'').split(",");
                var looparray = hashtags_arr;
            } else if(getType == 'user'){
                var ids_arr = user_id.replace(/ /g,'').split(",");
                var looparray = ids_arr;
            }

            //Get page info for first User ID
            if(getType == 'user'){

                var headerStyles = '',
                    sbi_page_url = 'https://api.instagram.com/v1/users/' + ids_arr[0] + '?access_token=' + sb_instagram_js_options.sb_instagram_at;

                if(feedOptions.headercolor.length) headerStyles = 'style="color: #'+feedOptions.headercolor+'"';

                $.ajax({
                    method: "GET",
                    url: sbi_page_url,
                    dataType: "jsonp",
                    success: function(data) {
                        $header = '<a href="http://instagram.com/'+data.data.username+'" target="_blank" title="@'+data.data.username+'" class="sbi_header_link" '+headerStyles+'>';
                        $header += '<div class="sbi_header_text">';
                        $header += '<h3'
                        if( data.data.bio.length == 0 ) $header += ' class="sbi_no_bio"';
                        $header += '>@'+data.data.username+'</h3>';
                        if( data.data.bio.length ) $header += '<p class="sbi_bio">'+data.data.bio+'</p>';
                        $header += '</div>';
                        $header += '<div class="sbi_header_img">';
                        $header += '<div class="sbi_header_img_hover" '+hovercolorstyles+'><i class="fa fa-instagram" '+hovertextstyles+'></i></div>';
                        $header += '<img src="'+data.data.profile_picture+'" alt="'+data.data.full_name+'" width="50" height="50">';
                        $header += '</div>';
                        $header += '</a>';
                        //Add the header
                        $self.find('.sb_instagram_header').prepend( $header );
                        //Change the URL of the follow button
                        if( $self.find('.sbi_follow_btn').length ) $self.find('.sbi_follow_btn a').attr('href', 'http://instagram.com/' + data.data.username )
                    }
                });

            } // End get page info

            //Loop through ids or hashtags
            // looparray.forEach(function(entry) {
            for (var i = 0, len = looparray.length; i < len; i++) {
                var entry = looparray[i];

          var userFeed = new instagramfeed({
                    // mock: true,
            target: $target,
              get: getType,
              tagName : entry,
              sortBy: sortby,
              resolution: imgRes,
              limit: parseInt( num ),
              template: '<div class="sbi_item sbi_type_{{model.type}} sbi_new" id="sbi_{{id}}" data-date="{{model.created_time_raw}}"><div class="sbi_photo_wrap"><i class="fa fa-play sbi_playbtn"></i><div class="sbi_link" '+hovercolorstyles+'><p class="sbi_username"><a href="http://instagram.com/{{model.user.username}}" target="_blank" '+hovertextstyles+'>{{model.user.username}}</a></p><a class="sbi_instagram_link" href="{{link}}" target="_blank" title="Instagram" '+hovertextstyles+'><i class="fa fa-instagram"></i></a><p class="sbi_date" '+hovertextstyles+'>{{model.created_time}}</p><a class="sbi_link_area" href="{{model.images.standard_resolution.url}}" data-lightbox-sbi="'+($i+1)+'" data-title="{{caption}}" data-video="{{model.videos.standard_resolution.url}}" data-id="sbi_{{id}}" data-user="{{model.user.username}}" data-url="{{link}}" data-avatar="{{model.user.profile_picture}}"><i class="fa fa-play sbi_playbtn" '+hovertextstyles+'></i><span class="sbi_lightbox_link" '+hovertextstyles+'><i class="fa fa-arrows-alt"></i></span></a></div><a class="sbi_photo" href="{{link}}" target="_blank"><img src="{{image}}" alt="{{caption}}" /></a></div><div class="sbi_info"><p class="sbi_caption_wrap" '+showcaption+'><span class="sbi_caption" style="color: #'+feedOptions.captioncolor+'; font-size: '+feedOptions.captionsize+'px;">{{caption}}</span><span class="sbi_expand"> <a href="#"><span class="sbi_more">...</span></a></span></p><div class="sbi_meta" style="color: #'+feedOptions.likescolor+'; '+showlikes+'"><span class="sbi_likes" style="font-size: '+feedOptions.likessize+'px;"><i class="fa fa-heart" style="font-size: '+feedOptions.likessize+'px;"></i>{{likes}}</span><span class="sbi_comments" style="font-size: '+feedOptions.likessize+'px;"><i class="fa fa-comment" style="font-size: '+feedOptions.likessize+'px;"></i>{{comments}}</span></div></div></div>',
              filter: function(image) {
              var date = new Date(image.created_time*1000);

                        //Create time for sorting
                        var time = date.getTime();
                        image.created_time_raw = time;

                  //Create pretty date for display
              m = date.getMonth();
              d = date.getDate();
              y = date.getFullYear();

              var month_names = new Array ( );
              month_names[month_names.length] = "Jan";
              month_names[month_names.length] = "Feb";
              month_names[month_names.length] = "Mar";
              month_names[month_names.length] = "Apr";
              month_names[month_names.length] = "May";
              month_names[month_names.length] = "Jun";
              month_names[month_names.length] = "Jul";
              month_names[month_names.length] = "Aug";
              month_names[month_names.length] = "Sep";
              month_names[month_names.length] = "Oct";
              month_names[month_names.length] = "Nov";
              month_names[month_names.length] = "Dec";

              var thetime = d + ' ' + month_names[m];

              image.created_time = thetime;

              //Replace double quotes in the captions with the HTML symbol
                        //Always check to make sure it exists
              if(image.caption != null) image.caption.text = image.caption.text.replace(/"/g, "&quot;");

              return true;
            },
              userId: parseInt( entry ),
              accessToken: sb_instagram_js_options.sb_instagram_at,
              after: function() {

                        $self.find('.sbi_loader').remove();

                /* Load more button */
                if (this.hasNext()) {
                   $loadBtn.show();
                } else {
                  $loadBtn.hide();
                  $self.css('padding-bottom', 0);
                }

                /* Scripts for each feed */
                $self.find('.sbi_item').each(function(){

                var $self = $(this),
                                $sbi_link_area = $self.find('.sbi_link_area'),
                                linkHref = $sbi_link_area.attr('href');

                //Change lightbox image to be full size
                var $sbi_lightbox = $('#sbi_lightbox');
                $self.find('.sbi_lightbox_link').click(function(){
                  $sbi_lightbox.removeClass('sbi_video_lightbox');
                  if( $self.hasClass('sbi_type_video') ){
                    $sbi_lightbox.addClass('sbi_video_lightbox');
                    //Add the image as the poster so doesn't show an empty video element when clicking the first video link
                    $('.sbi_video').attr({
                              'poster' : $(this).attr('href')
                            });

                  }
                });

                //Expand post
                var	$post_text = $self.find('.sbi_info .sbi_caption'),
                  text_limit = feedOptions.captionlength;
                if (typeof text_limit === 'undefined' || text_limit == '') text_limit = 99999;

                //Set the full text to be the caption (used in the image alt)
                var	full_text = $self.find('.sbi_photo img').attr('alt');
                if(full_text == undefined) full_text = '';
                var short_text = full_text.substring(0,text_limit);

                //Cut the text based on limits set
                $post_text.html( short_text );
                //Show the 'See More' link if needed
                if (full_text.length > text_limit) $self.find('.sbi_expand').show();
                //Click function
                $self.find('.sbi_expand a').unbind('click').bind('click', function(e){
                  e.preventDefault();
                  var $expand = jQuery(this),
                    $more = $expand.find('.sbi_more');
                  if ( $self.hasClass('sbi_caption_full') ){
                    $post_text.html( short_text );
                    $self.removeClass('sbi_caption_full');
                  } else {
                    $post_text.html( full_text );
                    $self.addClass('sbi_caption_full');
                  }
                });

                //Photo links
                            //If lightbox is disabled
                            if( disablelightbox == 'true' ){
                                if( !sbiTouchDevice ){ //Only apply hover effect if not touch screen device
                                    $self.find('.sbi_photo').hover(function(){
                                        $(this).fadeTo(200, 0.85);
                                    }, function(){
                                        $(this).stop().fadeTo(500, 1);
                                    });
                                }
                            //If lightbox is enabled add lightbox links
                            } else {

                                var $sbi_photo_wrap = $self.find('.sbi_photo_wrap'),
                                    $sbi_link = $sbi_photo_wrap.find('.sbi_link');

                                if(sbiTouchDevice){
                                    //launch lightbox on click
                                    $sbi_link.css('background', 'none').show();
                                    $sbi_link.find('*').hide().end().find('.sbi_link_area').show();

                                    // if ( $self.hasClass('sbi_type_video') ){
                                    //     $self.find('.sbi_photo_wrap').css('height', $self.find('.sbi_photo_wrap').innerWidth());
                                    //     $self.find('.sbi_photo_wrap').html("<video class='sbi_video' style='display: block; height: "+$self.find('.sbi_photo_wrap').innerWidth()+"px;' src='"+$self.find('.sbi_link_area').attr('data-video')+"' poster='' controls></video>");
                                    // }
                                } else {
                                    //Fade in links on hover
                                    $sbi_photo_wrap.hover(function(){
                                        $sbi_link.fadeIn(200);
                                    }, function(){
                                        $sbi_link.stop().fadeOut(600);
                                    });
                                }

                            }


              }); //End .sbi_item each


              // Call Custom JS if it exists
              if (typeof sbi_custom_js == 'function') sbi_custom_js();

                //Only check the width once the resize event is over
              var sbidelay = (function(){
                var sbitimer = 0;
                  return function(sbicallback, sbims){
                  clearTimeout (sbitimer);
                  sbitimer = setTimeout(sbicallback, sbims);
                };
              })();
              $(window).resize(function() {
                  sbidelay(function(){
                    sbiGetItemSize();
                  }, 500);
              });

              function sbiGetItemSize(){
                $self.removeClass('sbi_small sbi_medium');
                var sbiItemWidth = $self.find('.sbi_item').innerWidth();
                if( sbiItemWidth > 120 && sbiItemWidth < 240 ){
                    $self.addClass('sbi_medium');
                } else if( sbiItemWidth <= 120 ){
                    $self.addClass('sbi_small');
                }

              }
              sbiGetItemSize();

                        //Lightbox hide photo function
                        $('.sbi_lightbox_action a').unbind().bind('click', function(){
                            $(this).parent().find('.sbi_lightbox_tooltip').toggle();
                        });


                        //Sort posts by date
                        //only sort the new posts that are loaded in, not the whole feed, otherwise some photos will switch positions due to dates
                        $self.find('#sbi_images .sbi_item.sbi_new').sort(function (a, b) {
                            var aComp = $(a).attr("data-date"),
                                bComp = $(b).attr("data-date");

                            if(sortby == 'none'){
                                //Order by date
                                return bComp - aComp;
                            } else {
                                //Randomize
                                return (Math.round(Math.random())-0.5);
                            }

                        }).appendTo( $self.find("#sbi_images") );

                        //Remove the new class after 500ms, once the sorting is done
                        setTimeout(function(){
                            $('#sbi_images .sbi_item.sbi_new').removeClass('sbi_new');
                        }, 500);

                        //Header profile pic hover
                        $self.find('.sb_instagram_header a').hover(function(){
                            $self.find('.sb_instagram_header .sbi_header_img_hover').fadeIn(200);
                        }, function(){
                            $self.find('.sb_instagram_header .sbi_header_img_hover').stop().fadeOut(600);
                        });


            }, // End 'after' function
                    error: function(data) {
                        var sbiErrorMsg = '',
                            sbiErrorDir = '';

                        if( data.indexOf('access_token') > -1 ){
                            sbiErrorMsg += '<p><b>Error: Access Token is not valid</b><br /><span>This error message is only visible to WordPress admins</span>';
                            sbiErrorDir = "<p>There's an issue with the Instagram Access Token that you are using. Please obtain a new Access Token on the plugin's Settings page.";
                            //You can do this by doing the following:</p><ol><li>Navigate to plugin's Settings page</li><li>Click on the blue Instagram Login button</li><li>If prompted, log into your Instagram account and authorize the plugin</li><li>Copy and paste the Access Token into the plugin's Access Token field and save the changes</li></ol>
                        } else if( data.indexOf('user does not exist') > -1 ){
                            sbiErrorMsg += '<p><b>Error: The User ID does not exist</b><br /><span>This error is only visible to WordPress admins</span>';
                            sbiErrorDir = "<p>Please double check the Instagram User ID that you are using. To find your User ID simply enter your Instagram user name into this <a href='http://www.otzberg.net/iguserid/' target='_blank'>tool</a>.</p>";
                        }

                        //Add the error message to the page unless the user is displaying multiple ids or hashtags
                        if(looparray.length < 2) $('#sbi_images').empty().append( '<p style="text-align: center;">Unable to show Instagram photos</p><div id="sbi_mod_error">' + sbiErrorMsg + sbiErrorDir + '</div>');
                    }
          });


          $loadBtn.click(function() {
            userFeed.next();
          });

          userFeed.run();

            // }); //End hashtag array loop
            } //End hashtag array loop


    }); // End $('#sb_instagram.sbi').each


        function isTouchDevice() {
            return true == ("ontouchstart" in window || window.DocumentTouch && document instanceof DocumentTouch);
        }


  }
  sbi_init();




})(jQuery);
