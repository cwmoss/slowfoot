import api from "./api.js";
import NavigoPlus from "./components/navigo-plus.js";
import JsonExplorer from "./components/json-explorer.js";
import FetchState from "./components/fetch-state.js";
import SlftSearch from "./components/slft-search.js";
import TypesNav from "./components/types-nav.js";
import TypesIndex from "./components/types-index.js";
import DocumentView from "./components/document-view.js";
import LolqlPlayground from "./components/lolql-playground.js";

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
