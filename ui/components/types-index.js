import { LitElement, css, html } from "./lit-core.min.js";
import api from "../api.js";

export default class TypesIndex extends LitElement {
  static properties = {
    type: {},
    items: { type: Array },
  };

  async connectedCallback() {
    super.connectedCallback();
    let res = await api.type_index(this.type);
    this.items = res?.rows;
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
    if (!this.items) return "";
    console.log("render type index", this.items);
    return html`
      <h1>${this.type == "__paths" ? "paths" : this.type}</h1>
      <ol>
        ${this.items.map(
          (el) => html` <li><a href=${this.path(el)}>${this.title(el)}</a></li>`
        )}
      </ol>
    `;
  }

  createRenderRoot() {
    return this;
  }
}

customElements.define("types-index", TypesIndex);
