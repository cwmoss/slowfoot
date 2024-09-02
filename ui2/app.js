import Api from "./api.js";
import Navigo from "./components/navigo.js";
let main = document.querySelector("main");
let spinner = document.getElementById("spinner");
let time_spent = document.getElementById("time-spent");
let nav = document.querySelector("types-nav");

let base = "/__ui2";
const router = new Navigo();
router.on(base + "/type/:type", ({ data }) => {
  console.log("navigated to type", data); // { id: 'xxx', action: 'save' }
  page_type(data.type);
});
router.on(base + "/id/:id", ({ data }) => {
  console.log("navigated to id", data); // { id: 'xxx', action: 'save' }
});

document.addEventListener("fetch-start", () =>
  spinner.removeAttribute("hidden")
);
document.addEventListener("fetch-end", (e) => {
  spinner.setAttribute("hidden", "");
  time_spent.innerHTML = `<span>${e.detail?.time_print}</span>`;
  // router.updatePageLinks();
});

document.addEventListener("click", (e) => {
  let target = e.target.closest("a");
  if (target) {
    // if the click was on or within an <a>
    // then based on some condition...
    console.log("clicked on", target.getAttribute("href"), target);
    //if (target.getAttribute("href").startsWith("/foo")) {
    e.preventDefault(); // tell the browser not to respond to the link click
    router.navigate(base + target.getAttribute("href"));
    // then maybe do something else
    //}
  }
});

let types = await Api.types();
console.log("types", types);
nav.types = types;

// setTimeout(() => router.updatePageLinks());

function page_type(type) {
  main.innerHTML = `<types-index type="${type}"></types-index>`;
}
