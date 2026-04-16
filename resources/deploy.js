const html = (strings, ...args) => strings.reduce((result, str, i) => `${result}${args[i - 1]}${str}`)

// https://css-tricks.com/books/greatest-css-tricks/pin-scrolling-to-bottom/
// funktioniert leider wohl nicht
let output_styles = `
:host{
display:block;
/*width:400px;
height:300px;
background:green;*/
font-family: sans-serif;
}
article {
  margin-top:0.5rem;
  height: 240px;
  overflow: scroll;
}
article * {
  overflow-anchor: none;
}
output {
  display:block;
  width: 350px;
  white-space: pre-wrap;
  font-family: monospace;
}
#anchor {
  overflow-anchor: auto;
  height: 1px;
}
button{

    display: inline-block;
    outline: 0;
    border: none;
    cursor: pointer;
    padding: 0 24px;
    border-radius: 50px;
    min-width: 200px;
    height: 50px;
    font-size: 18px;
    background-color: #fd0;
    font-weight: 500;
    color: #222;
                

}


                
`

let html_template = () => {
    let t = document.createElement("template")
    t.innerHTML = html`
    <style>${output_styles}</style>
      <article><h1>DEPLOY</h1>
        <button name="deploy">Deploy</button>
        <main>
          <output></output>
          <div id="anchor"></div>
        </main>
        <footer></footer>
    </article>`;
    return t;
}

const deploy = function (secrets, setOutput, cb) {
    const xhr = new XMLHttpRequest();
    console.log("+++ widget options", secrets);

    xhr.open("POST", secrets.url, true);
    xhr.setRequestHeader("x-slft-deploy", secrets.apikey);

    xhr.onprogress = function (e) {
        console.log("progress");
        console.log(e);

        var outp = e.currentTarget.responseText;
        console.log(outp);
        setOutput(outp);
    };
    xhr.onreadystatechange = function () {
        if (xhr.readyState === XMLHttpRequest.DONE) {
            // && xhr.status === 200
            cb();
        }
    };
    xhr.send();
};

export default class DeployWidget extends HTMLElement {
    static properties = {
        show_dialog: { state: true, type: Boolean },
        deploying: { state: true, type: Boolean },
        preferences: { state: true, type: Object },
        output: {}
    };

    static styles = [output_styles];

    docid = ".deployment";
    constructor() {
        super()
            .attachShadow({ mode: "open" })
            .append(html_template().content.cloneNode(true))

        this.addEventListener("click", this);
    }
    handleEvent(e) {
        let output = this.shadowRoot.querySelector("output")
        console.log("event", e, e.target.matches(".deploy"), output);
    }
    connectedCallback() {
        // super.connectedCallback();

        console.log("connected deploy", this.preferences);

        // let doc = await api.document(this.docid);
        // if (!doc) doc = { _id: this.docid, _type: "deploy" };
        // this.preferences = doc;
        console.log("connected deploy", this.preferences);
    }
    async save_preferences(e) {
        let doc = e.detail;
        console.log("$ save", doc);
        await api.mutate(doc);
        this.preferences = doc;
    }

    do_deploy() {
        console.log("preferences", this.preferences);

        // this.output = `deploying site ${this.preferences.url}\n<span style="background-color: black; color: white">hier </span>`;
        let section = this.shadowRoot.querySelector("section")
        let output = this.shadowRoot.querySelector("output")
        output.insertAdjacentHTML('beforeend', `deploying site ${this.preferences.url}\n`)

        // section.scroll(0, 1);

        // ${html`${this.output}`}

        deploy({ url: this.preferences.url, apikey: this.preferences.apikey },
            (res) => {
                // this.output += res
                // output.insertAdjacentHTML('beforeend', res)
                output.innerHTML = res
                section.scrollTop = section.scrollHeight;
            },
            () => output.insertAdjacentHTML('beforeend', "\ndeploy finished"));
    }

}


customElements.define("deploy-widget", DeployWidget);
