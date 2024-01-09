export default class {

    element = document.getElementById("getApi");
    searchParams = new URLSearchParams(window.location.search);

    isQuerySearch = str => {
        return this.searchParams.has(str) && this.searchParams.get(str) !== "";
    };

    queryWriter = this.isQuerySearch("writer-delete-id")
        ? `
		deleteWriter(input:{
            id:"/api/writers/${this.searchParams.get("writer-delete-id")}"
        }){
            __typename
		}
		`
        : "";

    queryComment = this.isQuerySearch("comment-delete-id")
        ? `
		deleteComment(input:{
            id:"/api/comments/${this.searchParams.get("comment-delete-id")}"
        }){
            __typename
		}
		`
        : "";

    query = `
			${this.queryWriter},
			${this.queryComment}
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
                if ("deleteWriter" in res.data && res.data.deleteWriter !== null) {
                    console.log("success:delete:writer");
                }
                if ("deleteComment" in res.data && res.data.deleteComment !== null) {
                    console.log("success:delete:comment");
                }
            });
    }
}
