class Api {
    endpoint = window.API_BASE;

    async types() {
        return await this.get("index");
    }
    async type_index(type) {
        return await this.get("type/" + type);
    }
    async document(id) {
        return await this.get("id", { id: id });
    }
    async refetch() {
        return await this.post("fetch");
    }
    async lolql(query) {
        return await this.get("lolql", { query: query });
    }

    get(path, data) {
        document.dispatchEvent(new CustomEvent("fetch-start"));
        let meta = null;
        let url = `${this.endpoint}${path}`;
        if (data) url += "?" + new URLSearchParams(data);

        return fetch(url)
            .then((resp) => {
                let time = resp.headers.get("x-time-elapsed");
                if (time) meta = { time_print: time }
                return resp.json()
            })
            .then((resp) => {
                if (resp.__meta) {
                    meta = resp.__meta;
                }
                return resp.res ?? resp;
            })
            .finally(() =>
                document.dispatchEvent(
                    new CustomEvent("fetch-end", {
                        detail: meta,
                    })
                )
            );
    }

    async post(path, data = {}) {
        document.dispatchEvent(new CustomEvent("fetch-start"));
        let meta = null;
        let url = `${this.endpoint}${path}`;

        return fetch(url, {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
            },
            body: JSON.stringify(data),
        })
            .then((resp) => {
                let time = resp.headers.get("x-time-elapsed");
                if (time) meta = { time_print: time }
                return resp.json()
            })
            .then((resp) => {
                if (resp.__meta) {
                    meta = resp.__meta;
                }
                return resp.res ?? resp;
            })
            .finally(() =>
                document.dispatchEvent(
                    new CustomEvent("fetch-end", {
                        detail: meta,
                    })
                )
            );
    }
}

export default new Api();
