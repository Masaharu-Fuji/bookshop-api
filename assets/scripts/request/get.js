export default class {

	pram = {
		heeders: { "Content-Type": "application/json" },
		//body: this.query,
		method: "GET",
	};

	element = document.getElementById("getApi");
	searchParams = new URLSearchParams(window.location.search);

	isQuerySearch = str => {
		return this.searchParams.has(str) && this.searchParams.get(str) !== "";
	};

	querySearch = str => {
		return this.searchParams.get(str) ?? "";
	};

	queryWriter = this.isQuerySearch("writer-req-id")
		? `
		writer(id:"/api/writers/${this.querySearch("writer-req-id")}")
		{
			${this.querySearch("writer-id")},
			${this.querySearch("writer-email")},
			${this.querySearch("writer-username")},
		}
		`
		: "";

	queryComment = this.isQuerySearch("comment-req-id")
		? `
		comment(id:"/api/comments/${this.querySearch("comment-req-id")}")
		{
			${this.querySearch("comment-id")},
			${this.querySearch("comment-title")},
			${this.querySearch("comment-sentence")},
			${this.querySearch("comment-writer")}{
				${this.querySearch("comment-writer-id")},
				${this.querySearch("comment-writer-username")},
				${this.querySearch("comment-writer-nickname")},
				${this.querySearch("comment-writer-email")},
			}
		}
		`
		: "";

	query = `
			${this.queryWriter},
			${this.queryComment}
		`;
	operation = `
		query {
			${this.query}
		}
		`;
}
