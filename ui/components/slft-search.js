import api from "../api.js";

let tmpl = document.createElement("template");
// https://stackoverflow.com/questions/56992820/any-way-to-keep-a-custom-elemnts-template-markup-and-style-outside-of-a-javascr
tmpl.innerHTML = /* html */ `<label class="has_icon">
    <input type="search" >
    <svg width="16" height="16" fill="currentColor">
        <title>Search</title>
        <path d="M12.7 11.3c.9-1.2 1.4-2.6 1.4-4.2 0-3.9-3.1-7.1-7-7.1S0 3.2 0 7.1c0 3.9 3.2 7.1 7.1 7.1 1.6 0 3.1-.5 4.2-1.4l3 3c.2.2.5.3.7.3s.5-.1.7-.3c.4-.4.4-1 0-1.4l-3-3.1zm-5.6.8c-2.8 0-5.1-2.2-5.1-5S4.3 2 7.1 2s5.1 2.3 5.1 5.1-2.3 5-5.1 5z"></path>
      </svg>
</label>
<div id="results" popover></div>

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
#results {
    max-width: 80vw;
    max-height: 80vh;
    overflow: scroll;
    a {
        color:black;
        text-decoration:none;
        display: block;
        margin-bottom:0.5rem;
        padding: 0.5rem;
        span {
            display: block;
            text-transform: uppercase;
        }
    }

    a:hover {
        text-decoration:underline;
    }
}

[popover]:popover-open {
  opacity: 1;
  transform: scaleX(1);
}

[popover] {
  font-size: 1.2rem;
  padding: 10px;

  /* Final state of the exit animation */
  opacity: 0;
  transform: scaleX(0);

  transition:
    opacity 0.7s,
    transform 0.7s,
    overlay 0.7s allow-discrete,
    display 0.7s allow-discrete;
  /* Equivalent to
  transition: all 0.7s allow-discrete; */
}

/* Needs to be after the previous [popover]:popover-open rule
to take effect, as the specificity is the same */
@starting-style {
  [popover]:popover-open {
    opacity: 0;
    transform: scaleX(0);
  }
}

/* Transition for the popover's backdrop */

[popover]::backdrop {
  background-color: transparent;
  transition:
    display 0.7s allow-discrete,
    overlay 0.7s allow-discrete,
    background-color 0.7s;
  /* Equivalent to
  transition: all 0.7s allow-discrete; */
}

[popover]:popover-open::backdrop {
  background-color: rgb(0 0 0 / 25%);
}

/* The nesting selector (&) cannot represent pseudo-elements
so this starting-style rule cannot be nested */

@starting-style {
  [popover]:popover-open::backdrop {
    background-color: transparent;
  }
}


</style>
`;

let path = (el) => {
    if (el._type == "__paths") return `/id/${encodeURIComponent(el._id)}`;
    return `/id/${encodeURIComponent(el._id)}`;
}

export default class Search extends HTMLElement {
    constructor() {
        super()
            .attachShadow({ mode: "open" })
            .appendChild(tmpl.content.cloneNode(true));

        this.results = this.shadowRoot.querySelector("#results");
        this.input = this.shadowRoot.querySelector("input");
        this.input.addEventListener("input", this)
        this.input.addEventListener("click", this)
        this.results.addEventListener("click", this)
        // if (txt) this.render();
    }

    handleEvent(ev) {
        console.log("search for ", ev.target.value);
        if (ev.type == "click" && ev.target == this.input) {
            this.results.showPopover();
        } else {
            // clicked on result-link
            if (ev.type == "click") {
                this.results.hidePopover();
            } else {
                this.query(ev.target.value)
            }
        }
    }

    async query(q) {
        let res = await api.fts(q);
        this.results.innerHTML = "";
        res.forEach(it => this.results.insertAdjacentHTML("beforeend", `<a href="${path(it)}">${it.body} <span>${it._type}</span></a>`))

        console.log(res);
    }
}
customElements.define("slft-search", Search);
