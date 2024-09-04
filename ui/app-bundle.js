class Api {
  endpoint = window.API_BASE;

  async types() {
    return await this.get("index");
  }
  async type_index(type) {
    return await this.get("type/" + type);
  }
  async document(id) {
    return await this.get("id", { id: id });
  }
  async refetch() {
    return await this.post("fetch");
  }
  async lolql(query) {
    return await this.get("lolql", { query: query });
  }

  get(path, data) {
    document.dispatchEvent(new CustomEvent("fetch-start"));
    let meta = null;
    let url = `${this.endpoint}${path}`;
    if (data) url += "?" + new URLSearchParams(data);

    return fetch(url)
      .then((resp) => resp.json())
      .then((resp) => {
        if (resp.__meta) {
          meta = resp.__meta;
        }
        return resp.res ?? null;
      })
      .finally(() =>
        document.dispatchEvent(
          new CustomEvent("fetch-end", {
            detail: meta,
          })
        )
      );
  }

  async post(path, data = {}) {
    document.dispatchEvent(new CustomEvent("fetch-start"));
    let meta = null;
    let url = `${this.endpoint}${path}`;

    return fetch(url, {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify(data),
    })
      .then((resp) => resp.json())
      .then((resp) => {
        if (resp.__meta) {
          meta = resp.__meta;
        }
        return resp.res ?? null;
      })
      .finally(() =>
        document.dispatchEvent(
          new CustomEvent("fetch-end", {
            detail: meta,
          })
        )
      );
  }
}

var api = new Api();

/**
 * Bundled by jsDelivr using Rollup v2.79.1 and Terser v5.19.2.
 * Original file: /npm/navigo@8.11.1/lib/es/index.js
 *
 * Do NOT use SRI with dynamically generated files! More information: https://www.jsdelivr.com/using-sri-with-dynamic-files
 */
var t$1=/([:*])(\w+)/g,n$1="([^/]+)",e$1=/\*/g,o$1="?(?:.*)",r=/\/\?/g,a$1="/?([^/]+|)",i$1="(?:/^|^)",s$1="";function c$1(t){return void 0===t&&(t="/"),m$1()?location.pathname+location.search+location.hash:t}function u$1(t){return t.replace(/\/+$/,"").replace(/^\/+/,"")}function h$1(t){return "string"==typeof t}function f$1(t){return t&&t.indexOf("#")>=0&&t.split("#").pop()||""}function l$1(t){var n=u$1(t).split(/\?(.*)?$/);return [u$1(n[0]),n.slice(1).join("")]}function p$1(t){for(var n={},e=t.split("&"),o=0;o<e.length;o++){var r=e[o].split("=");if(""!==r[0]){var a=decodeURIComponent(r[0]);n[a]?(Array.isArray(n[a])||(n[a]=[n[a]]),n[a].push(decodeURIComponent(r[1]||""))):n[a]=decodeURIComponent(r[1]||"");}}return n}function d$1(c,d){var v,g=l$1(u$1(c.currentLocationPath)),m=g[0],y=g[1],_=""===y?null:p$1(y),k=[];if(h$1(d.path)){if(v=i$1+u$1(d.path).replace(t$1,(function(t,e,o){return k.push(o),n$1})).replace(e$1,o$1).replace(r,a$1)+"$",""===u$1(d.path)&&""===u$1(m))return {url:m,queryString:y,hashString:f$1(c.to),route:d,data:null,params:_}}else v=d.path;var O=new RegExp(v,s$1),w=m.match(O);if(w){var L=h$1(d.path)?function(t,n){return 0===n.length?null:t?t.slice(1,t.length).reduce((function(t,e,o){return null===t&&(t={}),t[n[o]]=decodeURIComponent(e),t}),null):null}(w,k):w.groups?w.groups:w.slice(1);return {url:u$1(m.replace(new RegExp("^"+c.instance.root),"")),queryString:y,hashString:f$1(c.to),route:d,data:L,params:_}}return !1}function v$1(){return !("undefined"==typeof window||!window.history||!window.history.pushState)}function g$1(t,n){return void 0===t[n]||!0===t[n]}function m$1(){return "undefined"!=typeof window}function y$1(t,n){return void 0===t&&(t=[]),void 0===n&&(n={}),t.filter((function(t){return t})).forEach((function(t){["before","after","already","leave"].forEach((function(e){t[e]&&(n[e]||(n[e]=[]),n[e].push(t[e]));}));})),n}function _$1(t,n,e){var o=n||{},r=0;!function n(){t[r]?Array.isArray(t[r])?(t.splice.apply(t,[r,1].concat(t[r][0](o)?t[r][1]:t[r][2])),n()):t[r](o,(function(t){void 0===t||!0===t?(r+=1,n()):e&&e(o);})):e&&e(o);}();}function k$1(t,n){void 0===t.currentLocationPath&&(t.currentLocationPath=t.to=c$1(t.instance.root)),t.currentLocationPath=t.instance._checkForAHash(t.currentLocationPath),n();}function O$1(t,n){for(var e=0;e<t.instance.routes.length;e++){var o=d$1(t,t.instance.routes[e]);if(o&&(t.matches||(t.matches=[]),t.matches.push(o),"ONE"===t.resolveOptions.strategy))return void n()}n();}function w$1(t,n){t.navigateOptions&&(void 0!==t.navigateOptions.shouldResolve&&console.warn('"shouldResolve" is deprecated. Please check the documentation.'),void 0!==t.navigateOptions.silent&&console.warn('"silent" is deprecated. Please check the documentation.')),n();}function L$1(t,n){!0===t.navigateOptions.force?(t.instance._setCurrent([t.instance._pathToMatchObject(t.to)]),n(!1)):n();}_$1.if=function(t,n,e){return Array.isArray(n)||(n=[n]),Array.isArray(e)||(e=[e]),[t,n,e]};var A$1=m$1(),b$1=v$1();function R$1(t,n){if(g$1(t.navigateOptions,"updateBrowserURL")){var e=("/"+t.to).replace(/\/\//g,"/"),o=A$1&&t.resolveOptions&&!0===t.resolveOptions.hash;b$1?(history[t.navigateOptions.historyAPIMethod||"pushState"](t.navigateOptions.stateObj||{},t.navigateOptions.title||"",o?"#"+e:e),location&&location.hash&&(t.instance.__freezeListening=!0,setTimeout((function(){if(!o){var n=location.hash;location.hash="",location.hash=n;}t.instance.__freezeListening=!1;}),1))):A$1&&(window.location.href=t.to);}n();}function P$1(t,n){var e=t.instance;e.lastResolved()?_$1(e.lastResolved().map((function(n){return function(e,o){if(n.route.hooks&&n.route.hooks.leave){var r=!1,a=t.instance.matchLocation(n.route.path,t.currentLocationPath,!1);if("*"!==n.route.path)r=!a;else r=!(!!t.matches&&t.matches.find((function(t){return n.route.path===t.route.path})));g$1(t.navigateOptions,"callHooks")&&r?_$1(n.route.hooks.leave.map((function(n){return function(e,o){return n((function(n){!1===n?t.instance.__markAsClean(t):o();}),t.matches&&t.matches.length>0?1===t.matches.length?t.matches[0]:t.matches:void 0)}})).concat([function(){return o()}])):o();}else o();}})),{},(function(){return n()})):n();}function S$1(t,n){g$1(t.navigateOptions,"updateState")&&t.instance._setCurrent(t.matches),n();}var E$1=[function(t,n){var e=t.instance.lastResolved();if(e&&e[0]&&e[0].route===t.match.route&&e[0].url===t.match.url&&e[0].queryString===t.match.queryString)return e.forEach((function(n){n.route.hooks&&n.route.hooks.already&&g$1(t.navigateOptions,"callHooks")&&n.route.hooks.already.forEach((function(n){return n(t.match)}));})),void n(!1);n();},function(t,n){t.match.route.hooks&&t.match.route.hooks.before&&g$1(t.navigateOptions,"callHooks")?_$1(t.match.route.hooks.before.map((function(n){return function(e,o){return n((function(n){!1===n?t.instance.__markAsClean(t):o();}),t.match)}})).concat([function(){return n()}])):n();},function(t,n){g$1(t.navigateOptions,"callHandler")&&t.match.route.handler(t.match),t.instance.updatePageLinks(),n();},function(t,n){t.match.route.hooks&&t.match.route.hooks.after&&g$1(t.navigateOptions,"callHooks")&&t.match.route.hooks.after.forEach((function(n){return n(t.match)})),n();}],H$1=[P$1,function(t,n){var e=t.instance._notFoundRoute;if(e){t.notFoundHandled=!0;var o=l$1(t.currentLocationPath),r=o[0],a=o[1],i=f$1(t.to);e.path=u$1(r);var s={url:e.path,queryString:a,hashString:i,data:null,route:e,params:""!==a?p$1(a):null};t.matches=[s],t.match=s;}n();},_$1.if((function(t){return t.notFoundHandled}),E$1.concat([S$1]),[function(t,n){t.resolveOptions&&!1!==t.resolveOptions.noMatchWarning&&void 0!==t.resolveOptions.noMatchWarning||console.warn('Navigo: "'+t.currentLocationPath+"\" didn't match any of the registered routes."),n();},function(t,n){t.instance._setCurrent(null),n();}])];function C$1(){return C$1=Object.assign||function(t){for(var n=1;n<arguments.length;n++){var e=arguments[n];for(var o in e)Object.prototype.hasOwnProperty.call(e,o)&&(t[o]=e[o]);}return t},C$1.apply(this,arguments)}function x$1(t,n){var e=0;P$1(t,(function o(){e!==t.matches.length?_$1(E$1,C$1({},t,{match:t.matches[e]}),(function(){e+=1,o();})):S$1(t,n);}));}function j$1(t){t.instance.__markAsClean(t);}function U$1(){return U$1=Object.assign||function(t){for(var n=1;n<arguments.length;n++){var e=arguments[n];for(var o in e)Object.prototype.hasOwnProperty.call(e,o)&&(t[o]=e[o]);}return t},U$1.apply(this,arguments)}var q$1="[data-navigo]";function F$1(t,n){var e,o=n||{strategy:"ONE",hash:!1,noMatchWarning:!1,linksSelector:q$1},r=this,a="/",i=null,s=[],g=!1,A=v$1(),b=m$1();function P(t){return t.indexOf("#")>=0&&(t=!0===o.hash?t.split("#")[1]||"/":t.split("#")[0]),t}function S(t){return u$1(a+"/"+u$1(t))}function E(t,n,e,o){return t=h$1(t)?S(t):t,{name:o||u$1(String(t)),path:t,handler:n,hooks:y$1(e)}}function C(t,n){if(!r.__dirty){r.__dirty=!0,t=t?u$1(a)+"/"+u$1(t):void 0;var e={instance:r,to:t,currentLocationPath:t,navigateOptions:{},resolveOptions:U$1({},o,n)};return _$1([k$1,O$1,_$1.if((function(t){var n=t.matches;return n&&n.length>0}),x$1,H$1)],e,j$1),!!e.matches&&e.matches}r.__waiting.push((function(){return r.resolve(t,n)}));}function F(t,n){if(r.__dirty)r.__waiting.push((function(){return r.navigate(t,n)}));else {r.__dirty=!0,t=u$1(a)+"/"+u$1(t);var e={instance:r,to:t,navigateOptions:n||{},resolveOptions:n&&n.resolveOptions?n.resolveOptions:o,currentLocationPath:P(t)};_$1([w$1,L$1,O$1,_$1.if((function(t){var n=t.matches;return n&&n.length>0}),x$1,H$1),R$1,j$1],e,j$1);}}function I(){if(b)return function(){if(b)return [].slice.call(document.querySelectorAll(o.linksSelector||q$1));return []}().forEach((function(t){"false"!==t.getAttribute("data-navigo")&&"_blank"!==t.getAttribute("target")?t.hasListenerAttached||(t.hasListenerAttached=!0,t.navigoHandler=function(n){if((n.ctrlKey||n.metaKey)&&"a"===n.target.tagName.toLowerCase())return !1;var e=t.getAttribute("href");if(null==e)return !1;if(e.match(/^(http|https)/)&&"undefined"!=typeof URL)try{var o=new URL(e);e=o.pathname+o.search;}catch(t){}var a=function(t){if(!t)return {};var n,e=t.split(","),o={};return e.forEach((function(t){var e=t.split(":").map((function(t){return t.replace(/(^ +| +$)/g,"")}));switch(e[0]){case"historyAPIMethod":o.historyAPIMethod=e[1];break;case"resolveOptionsStrategy":n||(n={}),n.strategy=e[1];break;case"resolveOptionsHash":n||(n={}),n.hash="true"===e[1];break;case"updateBrowserURL":case"callHandler":case"updateState":case"force":o[e[0]]="true"===e[1];}})),n&&(o.resolveOptions=n),o}(t.getAttribute("data-navigo-options"));g||(n.preventDefault(),n.stopPropagation(),r.navigate(u$1(e),a));},t.addEventListener("click",t.navigoHandler)):t.hasListenerAttached&&t.removeEventListener("click",t.navigoHandler);})),r}function M(t,n,e){var o=s.find((function(n){return n.name===t})),r=null;if(o){if(r=o.path,n)for(var i in n)r=r.replace(":"+i,n[i]);r=r.match(/^\//)?r:"/"+r;}return r&&e&&!e.includeRoot&&(r=r.replace(new RegExp("^/"+a),"")),r}function N(t){var n=l$1(u$1(t)),o=n[0],r=n[1],a=""===r?null:p$1(r);return {url:o,queryString:r,hashString:f$1(t),route:E(o,(function(){}),[e],o),data:null,params:a}}function T(t,n,e){return "string"==typeof n&&(n=z(n)),n?(n.hooks[t]||(n.hooks[t]=[]),n.hooks[t].push(e),function(){n.hooks[t]=n.hooks[t].filter((function(t){return t!==e}));}):(console.warn("Route doesn't exists: "+n),function(){})}function z(t){return "string"==typeof t?s.find((function(n){return n.name===S(t)})):s.find((function(n){return n.handler===t}))}t?a=u$1(t):console.warn('Navigo requires a root path in its constructor. If not provided will use "/" as default.'),this.root=a,this.routes=s,this.destroyed=g,this.current=i,this.__freezeListening=!1,this.__waiting=[],this.__dirty=!1,this.__markAsClean=function(t){t.instance.__dirty=!1,t.instance.__waiting.length>0&&t.instance.__waiting.shift()();},this.on=function(t,n,o){var r=this;return "object"!=typeof t||t instanceof RegExp?("function"==typeof t&&(o=n,n=t,t=a),s.push(E(t,n,[e,o])),this):(Object.keys(t).forEach((function(n){if("function"==typeof t[n])r.on(n,t[n]);else {var o=t[n],a=o.uses,i=o.as,c=o.hooks;s.push(E(n,a,[e,c],i));}})),this)},this.off=function(t){return this.routes=s=s.filter((function(n){return h$1(t)?u$1(n.path)!==u$1(t):"function"==typeof t?t!==n.handler:String(n.path)!==String(t)})),this},this.resolve=C,this.navigate=F,this.navigateByName=function(t,n,e){var o=M(t,n);return null!==o&&(F(o.replace(new RegExp("^/?"+a),""),e),!0)},this.destroy=function(){this.routes=s=[],A&&window.removeEventListener("popstate",this.__popstateListener),this.destroyed=g=!0;},this.notFound=function(t,n){return r._notFoundRoute=E("*",t,[e,n],"__NOT_FOUND__"),this},this.updatePageLinks=I,this.link=function(t){return "/"+a+"/"+u$1(t)},this.hooks=function(t){return e=t,this},this.extractGETParameters=function(t){return l$1(P(t))},this.lastResolved=function(){return i},this.generate=M,this.getLinkPath=function(t){return t.getAttribute("href")},this.match=function(t){var n={instance:r,currentLocationPath:t,to:t,navigateOptions:{},resolveOptions:o};return O$1(n,(function(){})),!!n.matches&&n.matches},this.matchLocation=function(t,n,e){void 0===n||void 0!==e&&!e||(n=S(n));var o={instance:r,to:n,currentLocationPath:n};k$1(o,(function(){})),"string"==typeof t&&(t=void 0===e||e?S(t):t);var a=d$1(o,{name:String(t),path:t,handler:function(){},hooks:{}});return a||!1},this.getCurrentLocation=function(){return N(u$1(c$1(a)).replace(new RegExp("^"+a),""))},this.addBeforeHook=T.bind(this,"before"),this.addAfterHook=T.bind(this,"after"),this.addAlreadyHook=T.bind(this,"already"),this.addLeaveHook=T.bind(this,"leave"),this.getRoute=z,this._pathToMatchObject=N,this._clean=u$1,this._checkForAHash=P,this._setCurrent=function(t){return i=r.current=t},function(){A&&(this.__popstateListener=function(){r.__freezeListening||C();},window.addEventListener("popstate",this.__popstateListener));}.call(this),I.call(this);}

const removePrefix = (value, prefix) =>
  value.startsWith(prefix) ? value.slice(prefix.length) : value;

/*
https://stackoverflow.com/questions/2136461/use-javascript-to-intercept-all-document-link-clicks

intercept all links
*/
function setup_linkhandler(router) {
  document.addEventListener("click", (e) => {
    console.log("$$$ click handler: target", e);

    // let target = e.originalTarget.closest("a") ?? null;
    let target = e.target.closest("a");
    if (target) {
      e.preventDefault(); // tell the browser not to respond to the link click
      router.navigate(target.getAttribute("href"));
    }
  });
}
/*
    root will be / when empty
    root will be anything/without/start-slash otherwise
*/
function NavigoPlus(base, options) {
  let router = new F$1(base, options);
  setup_linkhandler(router);
  router.start_from_current_url = () => {
    let path = new URL(window.location.href).pathname;
    let root = router.root;
    // console.log("START", path, root);
    path = removePrefix(path, "/");
    root = removePrefix(root, "/");
    path = removePrefix(path, root);
    // console.log("START", path);
    return router.navigate(path);
  };
  return router;
}

let tmpl$3 = document.createElement("template");
// https://stackoverflow.com/questions/56992820/any-way-to-keep-a-custom-elemnts-template-markup-and-style-outside-of-a-javascr
tmpl$3.innerHTML = /* html */ `
<style>
*,*:before, *:after{
    box-sizing:border-box;
}
:host{
    --col-bg: white;
    --font-size: 1rem;
	font-family: system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', 
        Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif;
    font-size: var(--font-size);
    /* position:fixed;z-index:122;*/
    max-height:90vh;max-width:100vw;
    overflow-y:scroll;top:32px;left:16px;
    background-color:var(--col-bg);
    color:black;line-height:1.5;
    /* border-radius:3px; */

    --col-key: rgb( 0, 136, 204 );
    --col-val: rgb( 50, 150, 80 );
}

ul, li{
    list-style-type:none;
    margin:0;
    padding:0;
    }
details>ul{
    padding-left: 1rem;
    border-left: 1px solid transparent;
}
details>ul:hover{
    border-left:1px solid silver;
}
summary{
    cursor: pointer;
}
summary  {
 position: relative;
 /*padding-left: 2.2rem;*/
 padding-left: 1rem;
 display: block;
  list-style: none;
}

/* sigh, Safari again */

summary::-webkit-details-marker {
  display: none;
}

summary:before {
  content: '';
  border-width: .4rem;
  border-style: solid;
  border-color: transparent transparent transparent #000;
  position: absolute;
  top: 0.4rem;
  left: -2px;
  transform: rotate(0);
  transform-origin: .2rem 50%;
  transition: .25s transform ease;
}
/* THE MAGIC 🧙‍♀️ */
details[open] > summary:before {
  transform: rotate(90deg);
}

details summary::marker {
  display:none;
}

details{
    color: var(--col-val)
}
details strong{
    color: var(--col-key)
}
</style>
<main>
</main>
`;

class JsonExplorer extends HTMLElement {
  _data = {};

  constructor() {
    super()
      .attachShadow({ mode: "open" })
      .appendChild(tmpl$3.content.cloneNode(true));
    /*
    console.log(
      "$$$ explorer text",
      this.textContent,
      this.innerText,
      this.innerHTML,
      txt
    );*/
    // if (txt) this._data = JSON.parse(this.innerText);
    // console.log("my data", this.data);

    this.main = this.shadowRoot.querySelector("main");
    // if (txt) this.render();
  }

  connectedCallback() {
    setTimeout(() => {
      console.log("connected late", this.innerText, this.textContent);
      if (this.textContent.trim()) {
        this.data = JSON.parse(this.textContent);
      }
    });
  }

  set data(d) {
    console.log("$$$ set data", d);
    this._data = d;
    this.render();
  }

  get data() {
    return this._data;
  }

  path(t) {
    return t;
  }

  html_encode(input) {
    const textArea = document.createElement("textarea");
    textArea.innerText = input;
    return textArea.innerHTML.split("<br>").join("\n");
  }
  render_value(node, key, val) {
    node.insertAdjacentHTML(
      "beforeend",
      `<li><strong class="k">${key}:</strong> ${this.html_encode(val)}</li>`
    );
  }
  render_array(node, key, value) {
    let li = document.createElement("li");
    let tag = document.createElement("details");
    tag.classList.add("a");
    tag.insertAdjacentHTML(
      "beforeend",
      `<summary><strong>${key}:</strong> [${value.length} items]</summary>`
    );
    let list = document.createElement("ul");
    value.forEach((val, index) => {
      if (Array.isArray(val)) {
        this.render_array(list, index, val);
      } else {
        if (typeof val === "object") {
          this.render_object(list, index, val);
        } else {
          this.render_value(list, index, val);
        }
      }
    });
    tag.appendChild(list);
    li.appendChild(tag);
    node.appendChild(li);
  }

  render_object(node, key, o, open = false, is_root = false) {
    let li = document.createElement("li");
    let tag = document.createElement("details");
    tag.classList.add("o");
    if (open) tag.setAttribute("open", "");
    tag.insertAdjacentHTML(
      "beforeend",
      `<summary><strong>${key}:</strong> {${
        Object.keys(o).length
      } keys}</summary>`
    );
    let list = document.createElement("ul");
    Object.keys(o).forEach((key) => {
      let val = o[key];
      if (Array.isArray(val)) {
        this.render_array(list, key, val);
      } else {
        if (typeof val === "object") {
          this.render_object(list, key, val);
        } else {
          this.render_value(list, key, val);
        }
      }
    });
    tag.appendChild(list);

    if (!is_root) {
      li.appendChild(tag);
      node.appendChild(li);
    } else {
      node.appendChild(tag);
    }
  }

  // TODO: root is array
  render() {
    console.log("rendering", this._data);
    this.main.innerHTML = "";
    this.render_object(this.main, "root", this._data, true, true);
  }
}

customElements.define("json-explorer", JsonExplorer);

let tmpl$2 = document.createElement("template");
tmpl$2.innerHTML = /* html */ `
<style>
:host{
    display:inline-flex;
}
.spinner {
  position: relative;
}
.spinner svg {
  width: 20px;
  margin-left: 8px;
  animation: 1s linear 0s infinite normal none running rotate;
  position: absolute;
  z-index: 99;
  top: -8px;
  left: 4px;
}
@keyframes rotate {
  0% {
    transform: rotate(0);
  }
  100% {
    transform: rotate(360deg);
  }
}
</style>
<div id="time-spent" class="nav-stats"></div>
<div hidden id="spinner" class="spinner">
    <svg
    version="1.1"
    width="32"
    height="32"
    viewBox="0 0 16 16"
    class="octicon octicon-infinity"
    aria-hidden="true"
    xmlns="http://www.w3.org/2000/svg"
    xmlns:xlink="http://www.w3.org/1999/xlink"
    fill="currentColor">
    <path
        fill-rule="evenodd"
        d="M3.5 6c-1.086 0-2 .914-2 2 0 1.086.914 2 2 2 .525 0 1.122-.244 1.825-.727.51-.35 1.025-.79 1.561-1.273-.536-.483-1.052-.922-1.56-1.273C4.621 6.244 4.025 6 3.5 6zm4.5.984c-.59-.533-1.204-1.066-1.825-1.493-.797-.548-1.7-.991-2.675-.991C1.586 4.5 0 6.086 0 8s1.586 3.5 3.5 3.5c.975 0 1.878-.444 2.675-.991.621-.427 1.235-.96 1.825-1.493.59.533 1.204 1.066 1.825 1.493.797.547 1.7.991 2.675.991 1.914 0 3.5-1.586 3.5-3.5s-1.586-3.5-3.5-3.5c-.975 0-1.878.443-2.675.991-.621.427-1.235.96-1.825 1.493zM9.114 8c.536.483 1.052.922 1.56 1.273.704.483 1.3.727 1.826.727 1.086 0 2-.914 2-2 0-1.086-.914-2-2-2-.525 0-1.122.244-1.825.727-.51.35-1.025.79-1.561 1.273z" />
    </svg>
</div>
`;

class FetchState extends HTMLElement {
  constructor() {
    super()
      .attachShadow({ mode: "open" })
      .appendChild(tmpl$2.content.cloneNode(true));
    this.spinner = this.shadowRoot.querySelector("#spinner");
    this.time_spent = this.shadowRoot.querySelector("#time-spent");

    document.addEventListener("fetch-start", this);
    document.addEventListener("fetch-end", this);
  }

  handleEvent(e) {
    if (e.type == "fetch-start") {
      this.spinner.removeAttribute("hidden");
    } else {
      this.spinner.setAttribute("hidden", "");
      this.time_spent.innerHTML = `${e.detail?.time_print}`;
    }
  }
}

customElements.define("fetch-state", FetchState);

let tmpl$1 = document.createElement("template");
// https://stackoverflow.com/questions/56992820/any-way-to-keep-a-custom-elemnts-template-markup-and-style-outside-of-a-javascr
tmpl$1.innerHTML = /* html */ `<label class="has_icon">
    <input type="text" >
    <svg width="16" height="16" fill="currentColor">
        <title>Search</title>
        <path d="M12.7 11.3c.9-1.2 1.4-2.6 1.4-4.2 0-3.9-3.1-7.1-7-7.1S0 3.2 0 7.1c0 3.9 3.2 7.1 7.1 7.1 1.6 0 3.1-.5 4.2-1.4l3 3c.2.2.5.3.7.3s.5-.1.7-.3c.4-.4.4-1 0-1.4l-3-3.1zm-5.6.8c-2.8 0-5.1-2.2-5.1-5S4.3 2 7.1 2s5.1 2.3 5.1 5.1-2.3 5-5.1 5z"></path>
      </svg>
      <div id="results"></div>
</label>

<style>
input{
    padding:4px;
}
.has_icon{
    position: relative;
}
.has_icon input + svg{
    position: absolute;
    top:2px;
    right: 18px;
}
</style>
`;
class Search extends HTMLElement {
  constructor() {
    super()
      .attachShadow({ mode: "open" })
      .appendChild(tmpl$1.content.cloneNode(true));

    this.results = this.shadowRoot.querySelector("#results");
    // if (txt) this.render();
  }
}
customElements.define("slft-search", Search);

/**
 * @license
 * Copyright 2019 Google LLC
 * SPDX-License-Identifier: BSD-3-Clause
 */
const t=globalThis,s=t.ShadowRoot&&(void 0===t.ShadyCSS||t.ShadyCSS.nativeShadow)&&"adoptedStyleSheets"in Document.prototype&&"replace"in CSSStyleSheet.prototype,i=Symbol(),e=new WeakMap;class o{constructor(t,s,e){if(this._$cssResult$=!0,e!==i)throw Error("CSSResult is not constructable. Use `unsafeCSS` or `css` instead.");this.cssText=t,this.t=s;}get styleSheet(){let t=this.i;const i=this.t;if(s&&void 0===t){const s=void 0!==i&&1===i.length;s&&(t=e.get(i)),void 0===t&&((this.i=t=new CSSStyleSheet).replaceSync(this.cssText),s&&e.set(i,t));}return t}toString(){return this.cssText}}const h=t=>new o("string"==typeof t?t:t+"",void 0,i),n=(i,e)=>{if(s)i.adoptedStyleSheets=e.map((t=>t instanceof CSSStyleSheet?t:t.styleSheet));else for(const s of e){const e=document.createElement("style"),o=t.litNonce;void 0!==o&&e.setAttribute("nonce",o),e.textContent=s.cssText,i.appendChild(e);}},c=s?t=>t:t=>t instanceof CSSStyleSheet?(t=>{let s="";for(const i of t.cssRules)s+=i.cssText;return h(s)})(t):t
/**
 * @license
 * Copyright 2017 Google LLC
 * SPDX-License-Identifier: BSD-3-Clause
 */,{is:a,defineProperty:l,getOwnPropertyDescriptor:u,getOwnPropertyNames:d,getOwnPropertySymbols:f,getPrototypeOf:p}=Object,v=globalThis,m=v.trustedTypes,y=m?m.emptyScript:"",g=v.reactiveElementPolyfillSupport,_=(t,s)=>t,b={toAttribute(t,s){switch(s){case Boolean:t=t?y:null;break;case Object:case Array:t=null==t?t:JSON.stringify(t);}return t},fromAttribute(t,s){let i=t;switch(s){case Boolean:i=null!==t;break;case Number:i=null===t?null:Number(t);break;case Object:case Array:try{i=JSON.parse(t);}catch(t){i=null;}}return i}},S=(t,s)=>!a(t,s),w={attribute:!0,type:String,converter:b,reflect:!1,hasChanged:S};Symbol.metadata??=Symbol("metadata"),v.litPropertyMetadata??=new WeakMap;class $ extends HTMLElement{static addInitializer(t){this.o(),(this.l??=[]).push(t);}static get observedAttributes(){return this.finalize(),this.u&&[...this.u.keys()]}static createProperty(t,s=w){if(s.state&&(s.attribute=!1),this.o(),this.elementProperties.set(t,s),!s.noAccessor){const i=Symbol(),e=this.getPropertyDescriptor(t,i,s);void 0!==e&&l(this.prototype,t,e);}}static getPropertyDescriptor(t,s,i){const{get:e,set:o}=u(this.prototype,t)??{get(){return this[s]},set(t){this[s]=t;}};return {get(){return e?.call(this)},set(s){const h=e?.call(this);o.call(this,s),this.requestUpdate(t,h,i);},configurable:!0,enumerable:!0}}static getPropertyOptions(t){return this.elementProperties.get(t)??w}static o(){if(this.hasOwnProperty(_("elementProperties")))return;const t=p(this);t.finalize(),void 0!==t.l&&(this.l=[...t.l]),this.elementProperties=new Map(t.elementProperties);}static finalize(){if(this.hasOwnProperty(_("finalized")))return;if(this.finalized=!0,this.o(),this.hasOwnProperty(_("properties"))){const t=this.properties,s=[...d(t),...f(t)];for(const i of s)this.createProperty(i,t[i]);}const t=this[Symbol.metadata];if(null!==t){const s=litPropertyMetadata.get(t);if(void 0!==s)for(const[t,i]of s)this.elementProperties.set(t,i);}this.u=new Map;for(const[t,s]of this.elementProperties){const i=this.p(t,s);void 0!==i&&this.u.set(i,t);}this.elementStyles=this.finalizeStyles(this.styles);}static finalizeStyles(t){const s=[];if(Array.isArray(t)){const i=new Set(t.flat(1/0).reverse());for(const t of i)s.unshift(c(t));}else void 0!==t&&s.push(c(t));return s}static p(t,s){const i=s.attribute;return !1===i?void 0:"string"==typeof i?i:"string"==typeof t?t.toLowerCase():void 0}constructor(){super(),this.v=void 0,this.isUpdatePending=!1,this.hasUpdated=!1,this.m=null,this._();}_(){this.S=new Promise((t=>this.enableUpdating=t)),this._$AL=new Map,this.$(),this.requestUpdate(),this.constructor.l?.forEach((t=>t(this)));}addController(t){(this.P??=new Set).add(t),void 0!==this.renderRoot&&this.isConnected&&t.hostConnected?.();}removeController(t){this.P?.delete(t);}$(){const t=new Map,s=this.constructor.elementProperties;for(const i of s.keys())this.hasOwnProperty(i)&&(t.set(i,this[i]),delete this[i]);t.size>0&&(this.v=t);}createRenderRoot(){const t=this.shadowRoot??this.attachShadow(this.constructor.shadowRootOptions);return n(t,this.constructor.elementStyles),t}connectedCallback(){this.renderRoot??=this.createRenderRoot(),this.enableUpdating(!0),this.P?.forEach((t=>t.hostConnected?.()));}enableUpdating(t){}disconnectedCallback(){this.P?.forEach((t=>t.hostDisconnected?.()));}attributeChangedCallback(t,s,i){this._$AK(t,i);}C(t,s){const i=this.constructor.elementProperties.get(t),e=this.constructor.p(t,i);if(void 0!==e&&!0===i.reflect){const o=(void 0!==i.converter?.toAttribute?i.converter:b).toAttribute(s,i.type);this.m=t,null==o?this.removeAttribute(e):this.setAttribute(e,o),this.m=null;}}_$AK(t,s){const i=this.constructor,e=i.u.get(t);if(void 0!==e&&this.m!==e){const t=i.getPropertyOptions(e),o="function"==typeof t.converter?{fromAttribute:t.converter}:void 0!==t.converter?.fromAttribute?t.converter:b;this.m=e,this[e]=o.fromAttribute(s,t.type),this.m=null;}}requestUpdate(t,s,i){if(void 0!==t){if(i??=this.constructor.getPropertyOptions(t),!(i.hasChanged??S)(this[t],s))return;this.T(t,s,i);}!1===this.isUpdatePending&&(this.S=this.A());}T(t,s,i){this._$AL.has(t)||this._$AL.set(t,s),!0===i.reflect&&this.m!==t&&(this.M??=new Set).add(t);}async A(){this.isUpdatePending=!0;try{await this.S;}catch(t){Promise.reject(t);}const t=this.scheduleUpdate();return null!=t&&await t,!this.isUpdatePending}scheduleUpdate(){return this.performUpdate()}performUpdate(){if(!this.isUpdatePending)return;if(!this.hasUpdated){if(this.renderRoot??=this.createRenderRoot(),this.v){for(const[t,s]of this.v)this[t]=s;this.v=void 0;}const t=this.constructor.elementProperties;if(t.size>0)for(const[s,i]of t)!0!==i.wrapped||this._$AL.has(s)||void 0===this[s]||this.T(s,this[s],i);}let t=!1;const s=this._$AL;try{t=this.shouldUpdate(s),t?(this.willUpdate(s),this.P?.forEach((t=>t.hostUpdate?.())),this.update(s)):this.k();}catch(s){throw t=!1,this.k(),s}t&&this._$AE(s);}willUpdate(t){}_$AE(t){this.P?.forEach((t=>t.hostUpdated?.())),this.hasUpdated||(this.hasUpdated=!0,this.firstUpdated(t)),this.updated(t);}k(){this._$AL=new Map,this.isUpdatePending=!1;}get updateComplete(){return this.getUpdateComplete()}getUpdateComplete(){return this.S}shouldUpdate(t){return !0}update(t){this.M&&=this.M.forEach((t=>this.C(t,this[t]))),this.k();}updated(t){}firstUpdated(t){}}$.elementStyles=[],$.shadowRootOptions={mode:"open"},$[_("elementProperties")]=new Map,$[_("finalized")]=new Map,g?.({ReactiveElement:$}),(v.reactiveElementVersions??=[]).push("2.0.4");
/**
 * @license
 * Copyright 2017 Google LLC
 * SPDX-License-Identifier: BSD-3-Clause
 */
const P=globalThis,C=P.trustedTypes,T=C?C.createPolicy("lit-html",{createHTML:t=>t}):void 0,x="$lit$",A=`lit$${Math.random().toFixed(9).slice(2)}$`,M="?"+A,k=`<${M}>`,E=document,U=()=>E.createComment(""),N=t=>null===t||"object"!=typeof t&&"function"!=typeof t,O=Array.isArray,R=t=>O(t)||"function"==typeof t?.[Symbol.iterator],z="[ \t\n\f\r]",V=/<(?:(!--|\/[^a-zA-Z])|(\/?[a-zA-Z][^>\s]*)|(\/?$))/g,L=/-->/g,I=/>/g,j=RegExp(`>|${z}(?:([^\\s"'>=/]+)(${z}*=${z}*(?:[^ \t\n\f\r"'\`<>=]|("|')|))|$)`,"g"),D=/'/g,H=/"/g,B=/^(?:script|style|textarea|title)$/i,W=t=>(s,...i)=>({_$litType$:t,strings:s,values:i}),q=W(1),Z=Symbol.for("lit-noChange"),F=Symbol.for("lit-nothing"),G=new WeakMap,K=E.createTreeWalker(E,129);function Q(t,s){if(!Array.isArray(t)||!t.hasOwnProperty("raw"))throw Error("invalid template strings array");return void 0!==T?T.createHTML(s):s}const X=(t,s)=>{const i=t.length-1,e=[];let o,h=2===s?"<svg>":"",r=V;for(let s=0;s<i;s++){const i=t[s];let n,c,a=-1,l=0;for(;l<i.length&&(r.lastIndex=l,c=r.exec(i),null!==c);)l=r.lastIndex,r===V?"!--"===c[1]?r=L:void 0!==c[1]?r=I:void 0!==c[2]?(B.test(c[2])&&(o=RegExp("</"+c[2],"g")),r=j):void 0!==c[3]&&(r=j):r===j?">"===c[0]?(r=o??V,a=-1):void 0===c[1]?a=-2:(a=r.lastIndex-c[2].length,n=c[1],r=void 0===c[3]?j:'"'===c[3]?H:D):r===H||r===D?r=j:r===L||r===I?r=V:(r=j,o=void 0);const u=r===j&&t[s+1].startsWith("/>")?" ":"";h+=r===V?i+k:a>=0?(e.push(n),i.slice(0,a)+x+i.slice(a)+A+u):i+A+(-2===a?s:u);}return [Q(t,h+(t[i]||"<?>")+(2===s?"</svg>":"")),e]};class Y{constructor({strings:t,_$litType$:s},i){let e;this.parts=[];let o=0,h=0;const r=t.length-1,n=this.parts,[c,a]=X(t,s);if(this.el=Y.createElement(c,i),K.currentNode=this.el.content,2===s){const t=this.el.content.firstChild;t.replaceWith(...t.childNodes);}for(;null!==(e=K.nextNode())&&n.length<r;){if(1===e.nodeType){if(e.hasAttributes())for(const t of e.getAttributeNames())if(t.endsWith(x)){const s=a[h++],i=e.getAttribute(t).split(A),r=/([.?@])?(.*)/.exec(s);n.push({type:1,index:o,name:r[2],strings:i,ctor:"."===r[1]?ot:"?"===r[1]?ht:"@"===r[1]?rt:et}),e.removeAttribute(t);}else t.startsWith(A)&&(n.push({type:6,index:o}),e.removeAttribute(t));if(B.test(e.tagName)){const t=e.textContent.split(A),s=t.length-1;if(s>0){e.textContent=C?C.emptyScript:"";for(let i=0;i<s;i++)e.append(t[i],U()),K.nextNode(),n.push({type:2,index:++o});e.append(t[s],U());}}}else if(8===e.nodeType)if(e.data===M)n.push({type:2,index:o});else {let t=-1;for(;-1!==(t=e.data.indexOf(A,t+1));)n.push({type:7,index:o}),t+=A.length-1;}o++;}}static createElement(t,s){const i=E.createElement("template");return i.innerHTML=t,i}}function tt(t,s,i=t,e){if(s===Z)return s;let o=void 0!==e?i.U?.[e]:i.N;const h=N(s)?void 0:s._$litDirective$;return o?.constructor!==h&&(o?._$AO?.(!1),void 0===h?o=void 0:(o=new h(t),o._$AT(t,i,e)),void 0!==e?(i.U??=[])[e]=o:i.N=o),void 0!==o&&(s=tt(t,o._$AS(t,s.values),o,e)),s}class st{constructor(t,s){this._$AV=[],this._$AN=void 0,this._$AD=t,this._$AM=s;}get parentNode(){return this._$AM.parentNode}get _$AU(){return this._$AM._$AU}O(t){const{el:{content:s},parts:i}=this._$AD,e=(t?.creationScope??E).importNode(s,!0);K.currentNode=e;let o=K.nextNode(),h=0,r=0,n=i[0];for(;void 0!==n;){if(h===n.index){let s;2===n.type?s=new it(o,o.nextSibling,this,t):1===n.type?s=new n.ctor(o,n.name,n.strings,this,t):6===n.type&&(s=new nt(o,this,t)),this._$AV.push(s),n=i[++r];}h!==n?.index&&(o=K.nextNode(),h++);}return K.currentNode=E,e}R(t){let s=0;for(const i of this._$AV)void 0!==i&&(void 0!==i.strings?(i._$AI(t,i,s),s+=i.strings.length-2):i._$AI(t[s])),s++;}}class it{get _$AU(){return this._$AM?._$AU??this.V}constructor(t,s,i,e){this.type=2,this._$AH=F,this._$AN=void 0,this._$AA=t,this._$AB=s,this._$AM=i,this.options=e,this.V=e?.isConnected??!0;}get parentNode(){let t=this._$AA.parentNode;const s=this._$AM;return void 0!==s&&11===t?.nodeType&&(t=s.parentNode),t}get startNode(){return this._$AA}get endNode(){return this._$AB}_$AI(t,s=this){t=tt(this,t,s),N(t)?t===F||null==t||""===t?(this._$AH!==F&&this._$AR(),this._$AH=F):t!==this._$AH&&t!==Z&&this.L(t):void 0!==t._$litType$?this.I(t):void 0!==t.nodeType?this.j(t):R(t)?this.D(t):this.L(t);}H(t){return this._$AA.parentNode.insertBefore(t,this._$AB)}j(t){this._$AH!==t&&(this._$AR(),this._$AH=this.H(t));}L(t){this._$AH!==F&&N(this._$AH)?this._$AA.nextSibling.data=t:this.j(E.createTextNode(t)),this._$AH=t;}I(t){const{values:s,_$litType$:i}=t,e="number"==typeof i?this._$AC(t):(void 0===i.el&&(i.el=Y.createElement(Q(i.h,i.h[0]),this.options)),i);if(this._$AH?._$AD===e)this._$AH.R(s);else {const t=new st(e,this),i=t.O(this.options);t.R(s),this.j(i),this._$AH=t;}}_$AC(t){let s=G.get(t.strings);return void 0===s&&G.set(t.strings,s=new Y(t)),s}D(t){O(this._$AH)||(this._$AH=[],this._$AR());const s=this._$AH;let i,e=0;for(const o of t)e===s.length?s.push(i=new it(this.H(U()),this.H(U()),this,this.options)):i=s[e],i._$AI(o),e++;e<s.length&&(this._$AR(i&&i._$AB.nextSibling,e),s.length=e);}_$AR(t=this._$AA.nextSibling,s){for(this._$AP?.(!1,!0,s);t&&t!==this._$AB;){const s=t.nextSibling;t.remove(),t=s;}}setConnected(t){void 0===this._$AM&&(this.V=t,this._$AP?.(t));}}class et{get tagName(){return this.element.tagName}get _$AU(){return this._$AM._$AU}constructor(t,s,i,e,o){this.type=1,this._$AH=F,this._$AN=void 0,this.element=t,this.name=s,this._$AM=e,this.options=o,i.length>2||""!==i[0]||""!==i[1]?(this._$AH=Array(i.length-1).fill(new String),this.strings=i):this._$AH=F;}_$AI(t,s=this,i,e){const o=this.strings;let h=!1;if(void 0===o)t=tt(this,t,s,0),h=!N(t)||t!==this._$AH&&t!==Z,h&&(this._$AH=t);else {const e=t;let r,n;for(t=o[0],r=0;r<o.length-1;r++)n=tt(this,e[i+r],s,r),n===Z&&(n=this._$AH[r]),h||=!N(n)||n!==this._$AH[r],n===F?t=F:t!==F&&(t+=(n??"")+o[r+1]),this._$AH[r]=n;}h&&!e&&this.B(t);}B(t){t===F?this.element.removeAttribute(this.name):this.element.setAttribute(this.name,t??"");}}class ot extends et{constructor(){super(...arguments),this.type=3;}B(t){this.element[this.name]=t===F?void 0:t;}}class ht extends et{constructor(){super(...arguments),this.type=4;}B(t){this.element.toggleAttribute(this.name,!!t&&t!==F);}}class rt extends et{constructor(t,s,i,e,o){super(t,s,i,e,o),this.type=5;}_$AI(t,s=this){if((t=tt(this,t,s,0)??F)===Z)return;const i=this._$AH,e=t===F&&i!==F||t.capture!==i.capture||t.once!==i.once||t.passive!==i.passive,o=t!==F&&(i===F||e);e&&this.element.removeEventListener(this.name,this,i),o&&this.element.addEventListener(this.name,this,t),this._$AH=t;}handleEvent(t){"function"==typeof this._$AH?this._$AH.call(this.options?.host??this.element,t):this._$AH.handleEvent(t);}}class nt{constructor(t,s,i){this.element=t,this.type=6,this._$AN=void 0,this._$AM=s,this.options=i;}get _$AU(){return this._$AM._$AU}_$AI(t){tt(this,t);}}const at=P.litHtmlPolyfillSupport;at?.(Y,it),(P.litHtmlVersions??=[]).push("3.1.3");const lt=(t,s,i)=>{const e=i?.renderBefore??s;let o=e._$litPart$;if(void 0===o){const t=i?.renderBefore??null;e._$litPart$=o=new it(s.insertBefore(U(),t),t,void 0,i??{});}return o._$AI(t),o};
/**
 * @license
 * Copyright 2017 Google LLC
 * SPDX-License-Identifier: BSD-3-Clause
 */class ut extends ${constructor(){super(...arguments),this.renderOptions={host:this},this.ht=void 0;}createRenderRoot(){const t=super.createRenderRoot();return this.renderOptions.renderBefore??=t.firstChild,t}update(t){const s=this.render();this.hasUpdated||(this.renderOptions.isConnected=this.isConnected),super.update(t),this.ht=lt(s,this.renderRoot,this.renderOptions);}connectedCallback(){super.connectedCallback(),this.ht?.setConnected(!0);}disconnectedCallback(){super.disconnectedCallback(),this.ht?.setConnected(!1);}render(){return Z}}ut._$litElement$=!0,ut[("finalized")]=!0,globalThis.litElementHydrateSupport?.({LitElement:ut});const dt=globalThis.litElementPolyfillSupport;dt?.({LitElement:ut});(globalThis.litElementVersions??=[]).push("4.0.5");

class TypesNav extends ut {
  static properties = {
    types: { type: Array },
  };

  async connectedCallback() {
    super.connectedCallback();
    this.types = await api.types();
  }

  path(t) {
    return `/type/${t}`;
  }

  render() {
    if (!this.types) return "";
    return q`
      <nav class="subnav">
        ${this.types.map(
          (type) => q`
            <a href="${this.path(type._type)}">${type._type} (${type.total})</a>
          `
        )}
      </nav>
    `;
  }
  createRenderRoot() {
    return this;
  }
}

customElements.define("types-nav", TypesNav);

class TypesIndex extends ut {
  static properties = {
    type: {},
    items: { type: Array },
  };

  async connectedCallback() {
    super.connectedCallback();
    let res = await api.type_index(this.type);
    this.items = res?.rows;
  }

  path(el) {
    if (this.type == "__paths") return `/id/${encodeURIComponent(el.id)}`;
    return `/id/${encodeURIComponent(el._id)}`;
  }

  title(el) {
    if (this.type == "__paths") return el.path;
    let title = el.title;
    if (!title) title = el.name;
    if (!title) title = el._id;
    return title;
  }

  render() {
    console.log("render type index????", this.items);
    if (!this.items) return "";
    console.log("render type index", this.items);
    return q`
      <h1>${this.type == "__paths" ? "paths" : this.type}</h1>
      <ol>
        ${this.items.map(
          (el) => q` <li><a href=${this.path(el)}>${this.title(el)}</a></li>`
        )}
      </ol>
    `;
  }

  createRenderRoot() {
    return this;
  }
}

customElements.define("types-index", TypesIndex);

class DocumentView extends ut {
  static properties = {
    docid: {},
    doc: { type: Object },
  };

  async connectedCallback() {
    super.connectedCallback();
    let res = await api.document(this.docid);
    console.log("document", res);
    this.doc = res;
    //this.render();
  }

  path(el) {
    if (this.type == "__paths") return `/id/${encodeURIComponent(el.id)}`;
    return `/id/${encodeURIComponent(el._id)}`;
  }

  title(el) {
    if (this.type == "__paths") return el.path;
    let title = el.title;
    if (!title) title = el.name;
    if (!title) title = el._id;
    return title;
  }

  render() {
    console.log("render type index????", this.items);
    if (!this.doc) return "";
    console.log("render type index", this.items);
    return q`
      <h1>${this.doc._id}</h1>
      <json-explorer .data=${this.doc}></json-explorer>
    `;
  }
}

customElements.define("document-view", DocumentView);

let tmpl = document.createElement("template");
// https://stackoverflow.com/questions/56992820/any-way-to-keep-a-custom-elemnts-template-markup-and-style-outside-of-a-javascr
tmpl.innerHTML = /* html */ `
<section>
<div id="editor" contenteditable="true"></div>
<div id="control"><button class="play">play</button></div>
</section>
<div id="results">
<json-explorer></json-explorer>
</div>

<style>
section{
  position:relative;
  width:65ch;
  background-color:#fafafa;
  border: 2px solid #e6e6e6
}
#editor{
      min-height: 5rem;
    padding: 0.5rem 1rem 2rem 0.5rem;
    white-space: pre-wrap;
    font-family: "SFMono-Regular", Consolas, "Liberation Mono", Menlo, Courier, monospace;
    color:black;
  }
#editor:empty::before {
    display: inline-block;
    content: '*(_type=="...")';
    color: #999;
}
#control{
  position:absolute;
  bottom:0;
}
button{
  border:none;
  background-color:transparent;
  font-size:1.2rem;
  padding:0.5rem;
}
button:hover{
  background-color:var(--cgelb);
}
  #results{
  margin-top: 1rem;
  }
</style>
`;
class LolqlPlayground extends HTMLElement {
  constructor() {
    super()
      .attachShadow({ mode: "open" })
      .appendChild(tmpl.content.cloneNode(true));

    this.editor = this.shadowRoot.querySelector("#editor");
    this.play = this.shadowRoot.querySelector(".play");
    this.results = this.shadowRoot.querySelector("json-explorer");
    this.play.addEventListener("click", this);
    // if (txt) this.render();
  }
  get query() {
    const selectedQuery = window.getSelection().toString().trim();
    return selectedQuery || this.editor.innerText;
  }
  async handleEvent(e) {
    console.log("++ exec", this.query);
    let res = await api.lolql(this.query);
    this.results.data = { result: res };
  }
}
customElements.define("lolql-playground", LolqlPlayground);

/*
<script type="module" src="/__ui/components/fetch-state.js"></script>
<script type="module" src="/__ui/components/lolql-playground.js"></script>
<script type="module" src="/__ui/components/slft-search.js"></script>
<script type="module" src="/__ui/components/types-nav.js"></script>
<script type="module" src="/__ui/components/types-index.js"></script>
<script type="module" src="/__ui/components/document-view.js"></script>
<script type="module" src="/__ui/components/json-explorer.js"></script>
*/

let main = document.querySelector("main");
let refetch_btn = document.querySelector("#refetch");

/*
  routing
*/
const router = NavigoPlus("/__ui");
router
  .on("/type/:type", ({ data }) => {
    main.innerHTML = `<types-index type="${data.type}"></types-index>`;
  })
  .on("/id/:id", ({ data }) => {
    main.innerHTML = `<document-view docid="${data.id}"></document-view>`;
  })
  .on("/playground", () => {
    main.innerHTML = `<h1>lolql playground</h1>
    <lolql-playground></lolql-playground>`;
  })
  .on("/about", () => {
    main.innerHTML = `<h1>slowfoot navigator</h1>
    <p>navigate through your data</p>`;
  })
  .on("/", () => {
    main.innerHTML = ``;
  });

/*
  refetch action
*/
refetch_btn.addEventListener("click", () => {
  refetch_btn.disabled = true;
  api.refetch().finally(() => (refetch_btn.disabled = false));
});

/*
  initial action
*/
router.start_from_current_url();
