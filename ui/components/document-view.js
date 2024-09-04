import { LitElement, css, html } from "./lit-core.min.js";
import api from "../api.js";

export default class DocumentView extends LitElement {
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
    return html`
      <h1>${this.doc._id}</h1>
      <json-explorer .data=${this.doc}></json-explorer>
    `;
  }
}

customElements.define("document-view", DocumentView);
