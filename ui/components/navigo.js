/**
 * Bundled by jsDelivr using Rollup v2.79.1 and Terser v5.19.2.
 * Original file: /npm/navigo@8.11.1/lib/es/index.js
 *
 * Do NOT use SRI with dynamically generated files! More information: https://www.jsdelivr.com/using-sri-with-dynamic-files
 */
var t=/([:*])(\w+)/g,n="([^/]+)",e=/\*/g,o="?(?:.*)",r=/\/\?/g,a="/?([^/]+|)",i="(?:/^|^)",s="";function c(t){return void 0===t&&(t="/"),m()?location.pathname+location.search+location.hash:t}function u(t){return t.replace(/\/+$/,"").replace(/^\/+/,"")}function h(t){return"string"==typeof t}function f(t){return t&&t.indexOf("#")>=0&&t.split("#").pop()||""}function l(t){var n=u(t).split(/\?(.*)?$/);return[u(n[0]),n.slice(1).join("")]}function p(t){for(var n={},e=t.split("&"),o=0;o<e.length;o++){var r=e[o].split("=");if(""!==r[0]){var a=decodeURIComponent(r[0]);n[a]?(Array.isArray(n[a])||(n[a]=[n[a]]),n[a].push(decodeURIComponent(r[1]||""))):n[a]=decodeURIComponent(r[1]||"")}}return n}function d(c,d){var v,g=l(u(c.currentLocationPath)),m=g[0],y=g[1],_=""===y?null:p(y),k=[];if(h(d.path)){if(v=i+u(d.path).replace(t,(function(t,e,o){return k.push(o),n})).replace(e,o).replace(r,a)+"$",""===u(d.path)&&""===u(m))return{url:m,queryString:y,hashString:f(c.to),route:d,data:null,params:_}}else v=d.path;var O=new RegExp(v,s),w=m.match(O);if(w){var L=h(d.path)?function(t,n){return 0===n.length?null:t?t.slice(1,t.length).reduce((function(t,e,o){return null===t&&(t={}),t[n[o]]=decodeURIComponent(e),t}),null):null}(w,k):w.groups?w.groups:w.slice(1);return{url:u(m.replace(new RegExp("^"+c.instance.root),"")),queryString:y,hashString:f(c.to),route:d,data:L,params:_}}return!1}function v(){return!("undefined"==typeof window||!window.history||!window.history.pushState)}function g(t,n){return void 0===t[n]||!0===t[n]}function m(){return"undefined"!=typeof window}function y(t,n){return void 0===t&&(t=[]),void 0===n&&(n={}),t.filter((function(t){return t})).forEach((function(t){["before","after","already","leave"].forEach((function(e){t[e]&&(n[e]||(n[e]=[]),n[e].push(t[e]))}))})),n}function _(t,n,e){var o=n||{},r=0;!function n(){t[r]?Array.isArray(t[r])?(t.splice.apply(t,[r,1].concat(t[r][0](o)?t[r][1]:t[r][2])),n()):t[r](o,(function(t){void 0===t||!0===t?(r+=1,n()):e&&e(o)})):e&&e(o)}()}function k(t,n){void 0===t.currentLocationPath&&(t.currentLocationPath=t.to=c(t.instance.root)),t.currentLocationPath=t.instance._checkForAHash(t.currentLocationPath),n()}function O(t,n){for(var e=0;e<t.instance.routes.length;e++){var o=d(t,t.instance.routes[e]);if(o&&(t.matches||(t.matches=[]),t.matches.push(o),"ONE"===t.resolveOptions.strategy))return void n()}n()}function w(t,n){t.navigateOptions&&(void 0!==t.navigateOptions.shouldResolve&&console.warn('"shouldResolve" is deprecated. Please check the documentation.'),void 0!==t.navigateOptions.silent&&console.warn('"silent" is deprecated. Please check the documentation.')),n()}function L(t,n){!0===t.navigateOptions.force?(t.instance._setCurrent([t.instance._pathToMatchObject(t.to)]),n(!1)):n()}_.if=function(t,n,e){return Array.isArray(n)||(n=[n]),Array.isArray(e)||(e=[e]),[t,n,e]};var A=m(),b=v();function R(t,n){if(g(t.navigateOptions,"updateBrowserURL")){var e=("/"+t.to).replace(/\/\//g,"/"),o=A&&t.resolveOptions&&!0===t.resolveOptions.hash;b?(history[t.navigateOptions.historyAPIMethod||"pushState"](t.navigateOptions.stateObj||{},t.navigateOptions.title||"",o?"#"+e:e),location&&location.hash&&(t.instance.__freezeListening=!0,setTimeout((function(){if(!o){var n=location.hash;location.hash="",location.hash=n}t.instance.__freezeListening=!1}),1))):A&&(window.location.href=t.to)}n()}function P(t,n){var e=t.instance;e.lastResolved()?_(e.lastResolved().map((function(n){return function(e,o){if(n.route.hooks&&n.route.hooks.leave){var r=!1,a=t.instance.matchLocation(n.route.path,t.currentLocationPath,!1);if("*"!==n.route.path)r=!a;else r=!(!!t.matches&&t.matches.find((function(t){return n.route.path===t.route.path})));g(t.navigateOptions,"callHooks")&&r?_(n.route.hooks.leave.map((function(n){return function(e,o){return n((function(n){!1===n?t.instance.__markAsClean(t):o()}),t.matches&&t.matches.length>0?1===t.matches.length?t.matches[0]:t.matches:void 0)}})).concat([function(){return o()}])):o()}else o()}})),{},(function(){return n()})):n()}function S(t,n){g(t.navigateOptions,"updateState")&&t.instance._setCurrent(t.matches),n()}var E=[function(t,n){var e=t.instance.lastResolved();if(e&&e[0]&&e[0].route===t.match.route&&e[0].url===t.match.url&&e[0].queryString===t.match.queryString)return e.forEach((function(n){n.route.hooks&&n.route.hooks.already&&g(t.navigateOptions,"callHooks")&&n.route.hooks.already.forEach((function(n){return n(t.match)}))})),void n(!1);n()},function(t,n){t.match.route.hooks&&t.match.route.hooks.before&&g(t.navigateOptions,"callHooks")?_(t.match.route.hooks.before.map((function(n){return function(e,o){return n((function(n){!1===n?t.instance.__markAsClean(t):o()}),t.match)}})).concat([function(){return n()}])):n()},function(t,n){g(t.navigateOptions,"callHandler")&&t.match.route.handler(t.match),t.instance.updatePageLinks(),n()},function(t,n){t.match.route.hooks&&t.match.route.hooks.after&&g(t.navigateOptions,"callHooks")&&t.match.route.hooks.after.forEach((function(n){return n(t.match)})),n()}],H=[P,function(t,n){var e=t.instance._notFoundRoute;if(e){t.notFoundHandled=!0;var o=l(t.currentLocationPath),r=o[0],a=o[1],i=f(t.to);e.path=u(r);var s={url:e.path,queryString:a,hashString:i,data:null,route:e,params:""!==a?p(a):null};t.matches=[s],t.match=s}n()},_.if((function(t){return t.notFoundHandled}),E.concat([S]),[function(t,n){t.resolveOptions&&!1!==t.resolveOptions.noMatchWarning&&void 0!==t.resolveOptions.noMatchWarning||console.warn('Navigo: "'+t.currentLocationPath+"\" didn't match any of the registered routes."),n()},function(t,n){t.instance._setCurrent(null),n()}])];function C(){return C=Object.assign||function(t){for(var n=1;n<arguments.length;n++){var e=arguments[n];for(var o in e)Object.prototype.hasOwnProperty.call(e,o)&&(t[o]=e[o])}return t},C.apply(this,arguments)}function x(t,n){var e=0;P(t,(function o(){e!==t.matches.length?_(E,C({},t,{match:t.matches[e]}),(function(){e+=1,o()})):S(t,n)}))}function j(t){t.instance.__markAsClean(t)}function U(){return U=Object.assign||function(t){for(var n=1;n<arguments.length;n++){var e=arguments[n];for(var o in e)Object.prototype.hasOwnProperty.call(e,o)&&(t[o]=e[o])}return t},U.apply(this,arguments)}var q="[data-navigo]";function F(t,n){var e,o=n||{strategy:"ONE",hash:!1,noMatchWarning:!1,linksSelector:q},r=this,a="/",i=null,s=[],g=!1,A=v(),b=m();function P(t){return t.indexOf("#")>=0&&(t=!0===o.hash?t.split("#")[1]||"/":t.split("#")[0]),t}function S(t){return u(a+"/"+u(t))}function E(t,n,e,o){return t=h(t)?S(t):t,{name:o||u(String(t)),path:t,handler:n,hooks:y(e)}}function C(t,n){if(!r.__dirty){r.__dirty=!0,t=t?u(a)+"/"+u(t):void 0;var e={instance:r,to:t,currentLocationPath:t,navigateOptions:{},resolveOptions:U({},o,n)};return _([k,O,_.if((function(t){var n=t.matches;return n&&n.length>0}),x,H)],e,j),!!e.matches&&e.matches}r.__waiting.push((function(){return r.resolve(t,n)}))}function F(t,n){if(r.__dirty)r.__waiting.push((function(){return r.navigate(t,n)}));else{r.__dirty=!0,t=u(a)+"/"+u(t);var e={instance:r,to:t,navigateOptions:n||{},resolveOptions:n&&n.resolveOptions?n.resolveOptions:o,currentLocationPath:P(t)};_([w,L,O,_.if((function(t){var n=t.matches;return n&&n.length>0}),x,H),R,j],e,j)}}function I(){if(b)return function(){if(b)return[].slice.call(document.querySelectorAll(o.linksSelector||q));return[]}().forEach((function(t){"false"!==t.getAttribute("data-navigo")&&"_blank"!==t.getAttribute("target")?t.hasListenerAttached||(t.hasListenerAttached=!0,t.navigoHandler=function(n){if((n.ctrlKey||n.metaKey)&&"a"===n.target.tagName.toLowerCase())return!1;var e=t.getAttribute("href");if(null==e)return!1;if(e.match(/^(http|https)/)&&"undefined"!=typeof URL)try{var o=new URL(e);e=o.pathname+o.search}catch(t){}var a=function(t){if(!t)return{};var n,e=t.split(","),o={};return e.forEach((function(t){var e=t.split(":").map((function(t){return t.replace(/(^ +| +$)/g,"")}));switch(e[0]){case"historyAPIMethod":o.historyAPIMethod=e[1];break;case"resolveOptionsStrategy":n||(n={}),n.strategy=e[1];break;case"resolveOptionsHash":n||(n={}),n.hash="true"===e[1];break;case"updateBrowserURL":case"callHandler":case"updateState":case"force":o[e[0]]="true"===e[1]}})),n&&(o.resolveOptions=n),o}(t.getAttribute("data-navigo-options"));g||(n.preventDefault(),n.stopPropagation(),r.navigate(u(e),a))},t.addEventListener("click",t.navigoHandler)):t.hasListenerAttached&&t.removeEventListener("click",t.navigoHandler)})),r}function M(t,n,e){var o=s.find((function(n){return n.name===t})),r=null;if(o){if(r=o.path,n)for(var i in n)r=r.replace(":"+i,n[i]);r=r.match(/^\//)?r:"/"+r}return r&&e&&!e.includeRoot&&(r=r.replace(new RegExp("^/"+a),"")),r}function N(t){var n=l(u(t)),o=n[0],r=n[1],a=""===r?null:p(r);return{url:o,queryString:r,hashString:f(t),route:E(o,(function(){}),[e],o),data:null,params:a}}function T(t,n,e){return"string"==typeof n&&(n=z(n)),n?(n.hooks[t]||(n.hooks[t]=[]),n.hooks[t].push(e),function(){n.hooks[t]=n.hooks[t].filter((function(t){return t!==e}))}):(console.warn("Route doesn't exists: "+n),function(){})}function z(t){return"string"==typeof t?s.find((function(n){return n.name===S(t)})):s.find((function(n){return n.handler===t}))}t?a=u(t):console.warn('Navigo requires a root path in its constructor. If not provided will use "/" as default.'),this.root=a,this.routes=s,this.destroyed=g,this.current=i,this.__freezeListening=!1,this.__waiting=[],this.__dirty=!1,this.__markAsClean=function(t){t.instance.__dirty=!1,t.instance.__waiting.length>0&&t.instance.__waiting.shift()()},this.on=function(t,n,o){var r=this;return"object"!=typeof t||t instanceof RegExp?("function"==typeof t&&(o=n,n=t,t=a),s.push(E(t,n,[e,o])),this):(Object.keys(t).forEach((function(n){if("function"==typeof t[n])r.on(n,t[n]);else{var o=t[n],a=o.uses,i=o.as,c=o.hooks;s.push(E(n,a,[e,c],i))}})),this)},this.off=function(t){return this.routes=s=s.filter((function(n){return h(t)?u(n.path)!==u(t):"function"==typeof t?t!==n.handler:String(n.path)!==String(t)})),this},this.resolve=C,this.navigate=F,this.navigateByName=function(t,n,e){var o=M(t,n);return null!==o&&(F(o.replace(new RegExp("^/?"+a),""),e),!0)},this.destroy=function(){this.routes=s=[],A&&window.removeEventListener("popstate",this.__popstateListener),this.destroyed=g=!0},this.notFound=function(t,n){return r._notFoundRoute=E("*",t,[e,n],"__NOT_FOUND__"),this},this.updatePageLinks=I,this.link=function(t){return"/"+a+"/"+u(t)},this.hooks=function(t){return e=t,this},this.extractGETParameters=function(t){return l(P(t))},this.lastResolved=function(){return i},this.generate=M,this.getLinkPath=function(t){return t.getAttribute("href")},this.match=function(t){var n={instance:r,currentLocationPath:t,to:t,navigateOptions:{},resolveOptions:o};return O(n,(function(){})),!!n.matches&&n.matches},this.matchLocation=function(t,n,e){void 0===n||void 0!==e&&!e||(n=S(n));var o={instance:r,to:n,currentLocationPath:n};k(o,(function(){})),"string"==typeof t&&(t=void 0===e||e?S(t):t);var a=d(o,{name:String(t),path:t,handler:function(){},hooks:{}});return a||!1},this.getCurrentLocation=function(){return N(u(c(a)).replace(new RegExp("^"+a),""))},this.addBeforeHook=T.bind(this,"before"),this.addAfterHook=T.bind(this,"after"),this.addAlreadyHook=T.bind(this,"already"),this.addLeaveHook=T.bind(this,"leave"),this.getRoute=z,this._pathToMatchObject=N,this._clean=u,this._checkForAHash=P,this._setCurrent=function(t){return i=r.current=t},function(){A&&(this.__popstateListener=function(){r.__freezeListening||C()},window.addEventListener("popstate",this.__popstateListener))}.call(this),I.call(this)}export{F as default};
//# sourceMappingURL=/sm/93c44781711c06db71e4af82b08265ef68d8174578b5376bcfe6a52e4aedad86.map