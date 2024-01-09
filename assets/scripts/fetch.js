import GetCollection from './request/getCollection.js';
import Get from './request/get.js';
import Delete from './request/delete.js';
import Update from './request/update.js';
export default class {

    constructor(url) {
        this.url = url;
    }

    pram = {
        heeders: { "Content-Type": "application/json" },
        //body: this.query,
        method: "GET",
    };

    getCollection = new GetCollection().query;
    get_recode = new Get().query;

    delete_recode = new Delete().query;
    update_recode = new Update().query;

    request_query = `{
        ${this.getCollection},
        ${this.get_recode}
    }`;

    request_mutation = `mutation{
        ${this.update_recode}
    }`;

    query_parameters = `/?query=${this.request_query}`;

    element = document.getElementById("getApi");
    child = document.createElement("div");

    fetch() {
        console.log(this.query_parameters);
        fetch(encodeURI(this.url + this.query_parameters), this.pram)
            .then((data) => {
                return data.json();
            })
            .then((res) => {
                console.log(res); //response

                if (!("data" in res)) {
                    console.log("response:no_data");
                    return;
                }

                if ("writers" in res.data) {
                    res.data.writers.edges.forEach((element) => {
                        this.element.appendChild(this.child)
                        this.child.insertAdjacentHTML(
                            "beforeEnd",
                            (element.node.id ?? "") + ":: "
                        );
                        this.child.insertAdjacentHTML(
                            "beforeEnd",
                            (element.node.username ?? "") + ":: "
                        );
                        this.child.insertAdjacentHTML(
                            "beforeEnd",
                            (element.node.email ?? "") + "; "
                        );
                    }); //recode一覧
                }

                if ("comments" in res.data) {
                    res.data.comments.collection.forEach((element) => {
                        this.element.appendChild(this.child)
                        this.child.insertAdjacentHTML(
                            "beforeEnd",
                            (element.id ?? "--") + ":: "
                        );
                        this.child.insertAdjacentHTML(
                            "beforeEnd",
                            (element.title ?? "--") + ":: "
                        );
                        this.child.insertAdjacentHTML(
                            "beforeEnd",
                            (element.sentence ?? "--") + "; "
                        );
                    }); //recode一覧
                }

                if ("writer" in res.data) {
                    this.element.appendChild(this.child)
                    this.child.insertAdjacentHTML(
                        "beforeEnd",
                        (res.data.writer.id ?? "--") + ":: "
                    );
                    this.child.insertAdjacentHTML(
                        "beforeEnd",
                        (res.data.writer.username ?? "--") + ":: "
                    );
                    this.child.insertAdjacentHTML(
                        "beforeEnd",
                        (res.data.writer.email ?? "--") + "; "
                    );
                }

                if ("comment" in res.data) {
                    this.element.appendChild(this.child)
                    this.child.insertAdjacentHTML(
                        "beforeEnd",
                        (res.data.comment.id ?? "--") + ":: "
                    );
                    this.child.insertAdjacentHTML(
                        "beforeEnd",
                        (res.data.comment.title ?? "--") + ":: "
                    );
                    this.child.insertAdjacentHTML(
                        "beforeEnd",
                        (res.data.comment.sentence ?? "--") + "; "
                    );
                }

                if ("deleteWriter" in res.data && res.data.deleteWriter !== null) {
                    console.log("success:delete:writer");
                }

                if ("deleteComment" in res.data && res.data.deleteComment !== null) {
                    console.log("success:delete:comment");
                }
                if ("updateWriter" in res.data && res.data.updateWriter !== null) {
                    console.log("success:update:writer");
                }

                if ("updateComment" in res.data && res.data.updateComment !== null) {
                    console.log("success:update:comment");
                }
            });
    }
}