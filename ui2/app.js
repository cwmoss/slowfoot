import api from "./api.js";
import NavigoPlus from "./components/navigo-plus.js";
let main = document.querySelector("main");
let spinner = document.getElementById("spinner");
let time_spent = document.getElementById("time-spent");
let refetch_btn = document.querySelector("#refetch");

/*
  routing
*/

const router = NavigoPlus("/__ui2");
router
  .on("/type/:type", ({ data }) => {
    console.log("navigated to type", data); // { id: 'xxx', action: 'save' }
    page_type(data.type);
  })
  .on("/id/:id", ({ data }) => {
    console.log("navigated to id", data); // { id: 'xxx', action: 'save' }
    page_id(data.id);
  })
  .on("/about", () => {
    page_about();
  })
  .on("/", () => {
    page_index();
  });

// console.log("++ router", router);
/*
  global fetch-status
*/
document.addEventListener("fetch-start", () =>
  spinner.removeAttribute("hidden")
);
document.addEventListener("fetch-end", (e) => {
  spinner.setAttribute("hidden", "");
  time_spent.innerHTML = `<span>${e.detail?.time_print}</span>`;
  // router.updatePageLinks();
});
refetch_btn.addEventListener("click", () => {
  refetch_btn.disabled = true;
  api.refetch().finally(() => (refetch_btn.disabled = false));
});
/*
render pages
*/

function page_type(type) {
  main.innerHTML = `<types-index type="${type}"></types-index>`;
}

function page_id(id) {
  main.innerHTML = `<document-view docid="${id}"></document-view>`;
}

function page_about() {
  main.innerHTML = `<h1>slowfoot navigator</h1>
    <p>navigate through your data</p>`;
}

function page_index() {
  main.innerHTML = ``;
}

/*
  initiale action
*/
router.start_from_current_url();
/* -- */
