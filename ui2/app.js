import Api from "./api.js";

let main = document.querySelector("main");
let spinner = document.getElementById("spinner");
let time_spent = document.getElementById("time-spent");
let nav = document.querySelector("types-nav");

document.addEventListener("fetch-start", () =>
  spinner.removeAttribute("hidden")
);
document.addEventListener("fetch-end", (e) => {
  spinner.setAttribute("hidden", "");
  time_spent.innerHTML = `<span>${e.detail?.time_print}</span>`;
});

let types = await Api.types();
console.log("types", types);
nav.types = types;
