let tmpl = document.createElement("template");
// https://stackoverflow.com/questions/56992820/any-way-to-keep-a-custom-elemnts-template-markup-and-style-outside-of-a-javascr
tmpl.innerHTML = /* html */ `
<style>
*,*:before, *:after{
    box-sizing:border-box;
}
:host{
    --col-bg: white;
    --font-size: 1rem;
	font-family: system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', 
        Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif;
    font-size: var(--font-size);
    /* position:fixed;z-index:122;*/
    max-height:90vh;max-width:100vw;
    overflow-y:scroll;top:32px;left:16px;
    background-color:var(--col-bg);
    color:black;line-height:1.5;
    /* border-radius:3px; */

    --col-key: rgb( 0, 136, 204 );
    --col-val: rgb( 50, 150, 80 );
}

ul, li{
    list-style-type:none;
    margin:0;
    padding:0;
    }
details>ul{
    padding-left: 1rem;
    border-left: 1px solid transparent;
}
details>ul:hover{
    border-left:1px solid silver;
}
summary{
    cursor: pointer;
}
summary  {
 position: relative;
 /*padding-left: 2.2rem;*/
 padding-left: 1rem;
 display: block;
  list-style: none;
}

/* sigh, Safari again */

summary::-webkit-details-marker {
  display: none;
}

summary:before {
  content: '';
  border-width: .4rem;
  border-style: solid;
  border-color: transparent transparent transparent #000;
  position: absolute;
  top: 0.4rem;
  left: -2px;
  transform: rotate(0);
  transform-origin: .2rem 50%;
  transition: .25s transform ease;
}
/* THE MAGIC 🧙‍♀️ */
details[open] > summary:before {
  transform: rotate(90deg);
}

details summary::marker {
  display:none;
}

details{
    color: var(--col-val)
}
details strong{
    color: var(--col-key)
}
</style>
<main>
</main>
`;

export default class JsonExplorer extends HTMLElement {
  _data = {};

  constructor() {
    super()
      .attachShadow({ mode: "open" })
      .appendChild(tmpl.content.cloneNode(true));
    /*
    console.log(
      "$$$ explorer text",
      this.textContent,
      this.innerText,
      this.innerHTML,
      txt
    );*/
    // if (txt) this._data = JSON.parse(this.innerText);
    // console.log("my data", this.data);

    this.main = this.shadowRoot.querySelector("main");
    // if (txt) this.render();
  }

  connectedCallback() {
    setTimeout(() => {
      console.log("connected late", this.innerText, this.textContent);
      if (this.textContent.trim()) {
        this.data = JSON.parse(this.textContent);
      }
    });
  }

  set data(d) {
    console.log("$$$ set data", d);
    this._data = d;
    this.render();
  }

  get data() {
    return this._data;
  }

  path(t) {
    return t;
  }

  html_encode(input) {
    const textArea = document.createElement("textarea");
    textArea.innerText = input;
    return textArea.innerHTML.split("<br>").join("\n");
  }
  render_value(node, key, val) {
    node.insertAdjacentHTML(
      "beforeend",
      `<li><strong class="k">${key}:</strong> ${this.html_encode(val)}</li>`
    );
  }
  render_array(node, key, value) {
    let li = document.createElement("li");
    let tag = document.createElement("details");
    tag.classList.add("a");
    tag.insertAdjacentHTML(
      "beforeend",
      `<summary><strong>${key}:</strong> [${value.length} items]</summary>`
    );
    let list = document.createElement("ul");
    value.forEach((val, index) => {
      if (Array.isArray(val)) {
        this.render_array(list, index, val);
      } else {
        if (typeof val === "object") {
          this.render_object(list, index, val);
        } else {
          this.render_value(list, index, val);
        }
      }
    });
    tag.appendChild(list);
    li.appendChild(tag);
    node.appendChild(li);
  }

  render_object(node, key, o, open = false, is_root = false) {
    let li = document.createElement("li");
    let tag = document.createElement("details");
    tag.classList.add("o");
    if (open) tag.setAttribute("open", "");
    tag.insertAdjacentHTML(
      "beforeend",
      `<summary><strong>${key || key === 0 ? key : "root"}:</strong> {${
        Object.keys(o).length
      } keys}</summary>`
    );
    let list = document.createElement("ul");
    Object.keys(o).forEach((key) => {
      let val = o[key];
      if (Array.isArray(val)) {
        this.render_array(list, key, val);
      } else {
        if (typeof val === "object") {
          this.render_object(list, key, val);
        } else {
          this.render_value(list, key, val);
        }
      }
    });
    tag.appendChild(list);

    if (!is_root) {
      li.appendChild(tag);
      node.appendChild(li);
    } else {
      node.appendChild(tag);
    }
  }

  // TODO: root is array
  render() {
    console.log("rendering", this._data);
    this.main.innerHTML = "";
    this.render_object(this.main, "", this._data, true, true);
  }
}

customElements.define("json-explorer", JsonExplorer);
