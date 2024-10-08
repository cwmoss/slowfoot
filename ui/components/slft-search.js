let tmpl = document.createElement("template");
// https://stackoverflow.com/questions/56992820/any-way-to-keep-a-custom-elemnts-template-markup-and-style-outside-of-a-javascr
tmpl.innerHTML = /* html */ `<label class="has_icon">
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
export default class Search extends HTMLElement {
  constructor() {
    super()
      .attachShadow({ mode: "open" })
      .appendChild(tmpl.content.cloneNode(true));

    this.results = this.shadowRoot.querySelector("#results");
    // if (txt) this.render();
  }
}
customElements.define("slft-search", Search);
