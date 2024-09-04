import api from "./api.js";
import NavigoPlus from "./components/navigo-plus.js";
let main = document.querySelector("main");
let refetch_btn = document.querySelector("#refetch");

/*
  routing
*/
const router = NavigoPlus("/__ui2");
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
