class Api {
  endpoint = "http://localhost:1199/__api/";

  async types() {
    return await this.get("index");
  }
  async type_index(type) {
    return await this.get("type/" + type);
  }

  get(path) {
    document.dispatchEvent(new CustomEvent("fetch-start"));
    let meta = null;
    return fetch(`${this.endpoint}${path}`)
      .then((resp) => resp.json())
      .then((resp) => {
        if (resp.__meta) {
          meta = resp.__meta;
        }
        return resp.res ?? null;
      })
      .finally(() =>
        document.dispatchEvent(
          new CustomEvent("fetch-end", {
            detail: meta,
          })
        )
      );
  }

  async post(path, data) {
    return fetch(`${this.endpoint}${path}`, {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify(data),
    }).then((resp) => resp.json());
  }
}

export default new Api();
