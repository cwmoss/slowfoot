let tmpl = document.createElement("template");
tmpl.innerHTML = /* html */ `
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

export default class FetchState extends HTMLElement {
  constructor() {
    super()
      .attachShadow({ mode: "open" })
      .appendChild(tmpl.content.cloneNode(true));
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
