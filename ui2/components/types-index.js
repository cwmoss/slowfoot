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
    console.log("type-index", res?.rows);
    this.items = res?.rows;
    console.log("type-index!!!1", this.index);
    //this.render();
  }

  path(t) {
    return `id/${t}`;
  }

  xrender() {
    return html`<p>okokokok</p>`;
  }

  render() {
    console.log("render type index????", this.items);
    if (!this.items) return "no results";
    console.log("render type index", this.items);
    return html`
      <div>hello TYPES-INDEX</div>
      <nav class="index">
        ${this.items.map(
          (el) => html` <a href="${this.path(el._id)}">${el._id}</a> `
        )}
      </nav>
    `;
  }
}

customElements.define("types-index", TypesIndex);
