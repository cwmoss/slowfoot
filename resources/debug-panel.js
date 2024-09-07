// import { JsonViewer } from "https://unpkg.com/@alenaksu/json-viewer@2.0.0/dist/json-viewer.bundle.js";

let tmpl = document.createElement("template");
// https://stackoverflow.com/questions/56992820/any-way-to-keep-a-custom-elemnts-template-markup-and-style-outside-of-a-javascr
tmpl.innerHTML = /* html */ `
<style>
:host{
    --col-bg: #2a2f3a;
    --font-size: 0.8rem;
	font-family: system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', 
        Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif;
    font-size: var(--font-size);
    position:fixed;z-index:122;
    max-height:90vh;max-width:100vw;
    overflow-y:scroll;top:32px;left:16px;
    background-color:var(--col-bg);
    color:black;line-height:1.5;
    /* border-radius:3px; */
}
main{
    padding:1rem;
}
nav{
    display:flex;
    gap: 1rem;
}
nav a{
    color:white;
    font-weight:bold;
    text-decoration:none;
}
#close{
  position:absolute;
  top:12px;
  right:0;
  cursor: pointer;
  border: none;
  background: transparent;
}
</style>
<main>
<button id="close">❌</button>
    <nav><a href="/__ui/" target="_sf_navigator">
    <svg width="16" height="16" fill="white">
      <g transform="translate(0 0)">
        <path
          d="M9.2,0H5.4c-0.4,0-0.8,0.3-1,0.7l-2,7C2.2,8.4,2.7,9,3.3,9H7l-1.5,7l7.3-9.4C13.3,6,12.8,5,12,5H9l1.1-3.7 C10.3,0.6,9.8,0,9.2,0z"></path>
      </g>
    </svg>
    navigator</a>
    <button id="refetch">re-fetch</button>
    
    </nav>

    <json-viewer style="--font-size: var(--font-size);"></json-viewer>

</main>
`;

// pfeil:  ➚

class DebugPanel extends HTMLElement {
  constructor() {
    super();
    this.data = JSON.parse(this.innerText);
    console.log("my data", this.data);
    this.attachShadow({ mode: "open" }).appendChild(
      tmpl.content.cloneNode(true)
    );
    this.fetchbtn = this.shadowRoot.querySelector("#refetch");

    document.body.addEventListener("keydown", (e) => {
      // console.log(e)
      if (e.ctrlKey && e.key == "d") {
        console.log("strg D gedrückt");

        this.toggleAttribute("hidden");
      }
    });
  }
  connectedCallback() {
    console.log("connected");
    this.shadowRoot
      .querySelector("#close")
      .addEventListener("click", () => this.toggleAttribute("hidden"));
    this.shadowRoot.querySelector("json-viewer").data = this.data;
    this.fetchbtn.addEventListener("click", () => this.refetch());
  }

  async refetch() {
    console.log("refetch", this.fetchbtn);
    this.fetchbtn.disabled = true;
    await fetch("/__api/fetch", { method: "POST" }).finally(() => {
      this.fetchbtn.disabled = false;
      window.location.reload();
    });
  }
}

customElements.define("debug-panel", DebugPanel);
