import api from "../api.js";

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
export default class LolqlPlayground extends HTMLElement {
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
