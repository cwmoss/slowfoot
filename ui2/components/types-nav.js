import { LitElement, css, html } from "./lit-core.min.js";

export default class TypesNav extends LitElement {
  static properties = {
    types: { type: Array },
  };

  path(t) {
    return `/type/${t}`;
  }
  render() {
    if (!this.types) return "";
    return html`
      <nav class="subnav">
        ${this.types.map(
          (type) => html`
            <a href="${this.path(type._type)}">${type._type} (${type.total})</a>
          `
        )}
      </nav>
    `;
  }
  createRenderRoot() {
    return this;
  }
}

customElements.define("types-nav", TypesNav);
