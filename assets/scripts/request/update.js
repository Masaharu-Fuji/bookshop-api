export default class {

    element = document.getElementById("getApi");
    searchParams = new URLSearchParams(window.location.search);

    isQuerySearch = str => {
        return this.searchParams.has(str) && this.searchParams.get(str) !== "";
    }


    inputParameter = (str, entity) => {
        if (!this.isQuerySearch(entity + "-update-" + str)) {
            return "";
        }
        return `
        ${str}: "${this.searchParams.get(entity + "-update-" + str)}"
        `;
    }
    queryWriter = this.isQuerySearch("writer-update-id")
        ? `
		updateWriter(input:{
            id:"/api/writers/${this.searchParams.get("writer-update-id")}"
            ${this.inputParameter("username", "writer")}
            ${this.inputParameter("nickname", "writer")}
            ${this.inputParameter("email", "writer")}
        }){
            __typename
		}
		`
        : "";

    queryComment = this.isQuerySearch("comment-update-id")
        ? `
		updateComment(input:{
            id:"/api/comments/${this.searchParams.get("comment-update-id")}"
        }){
            __typename
		}
		`
        : "";

    query = `
			${this.queryWriter}
		`;

    operation = `
		mutation {
			${this.query}
		}
		`;

    pram = {
        heeders: { "Content-Type": "application/json" },
        //body: this.query,
        method: "GET",
    };

    fetch() {
        console.log(this.pram);//request
        fetch(this.url + "/?query=" + this.operation, this.pram)
            .then((data) => {
                return data.json();
            })
            .then((res) => {
                console.log(res); //response
                if (!("data" in res)) {
                    console.log("error:error");
                    return;
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
