import { LitElement, css, html } from "./lit-core.min.js";
import api from "../api.js";

export default class DocumentView extends LitElement {
    static properties = {
        docid: {},
        doc: { type: Object },
        links: { type: Array },
    };

    async connectedCallback() {
        super.connectedCallback();
        let res = await api.document(this.docid);
        console.log("document", res);
        this.doc = res.doc;
        this.links = res.links;
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
        if (!this.doc) return "";
        console.log("render doc view ", this.links);
        return html`
            <h1>${this.doc._id}</h1>
            <section class="links">
                ${this.links.map((it) => {
                    return html`<a .href=${it.path} target="_dev"
                        >${it.path}</a
                    >`;
                })}
            </section>
            <json-explorer .reflinks=${true} .data=${this.doc}></json-explorer>
        `;
    }
    createRenderRoot() {
        return this;
    }
}

customElements.define("document-view", DocumentView);
