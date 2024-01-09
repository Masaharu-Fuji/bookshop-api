export default class {

	pram = {
		heeders: { "Content-Type": "application/json" },
		//body: this.query,
		method: "GET",
	};

	element = document.getElementById("getApi");
	searchParams = new URLSearchParams(window.location.search);

	querySearch = str => {
		return this.searchParams.has(str) ? this.searchParams.get(str) : '';
	};

	queryWriters = this.searchParams.has("writers")
		? `
		writers
		{
			edges{
				node{
					${this.querySearch("writer-id")},
					${this.querySearch("writer-email")},
					${this.querySearch("writer-username")},
				}
			}
		}
		`
		: "";

	queryComments = this.searchParams.has("comments")
		? `
			comments(page:2 order: [{id: "desc"}])
			{
				collection{
						${this.querySearch("comment-id")},
						${this.querySearch("comment-title")},
						${this.querySearch("comment-sentence")},
				}
			}
			`
		: "";
	query = `
			${this.queryWriters},
			${this.queryComments}
		`;
	operation = `
		{
			${this.query}
		}
		`;
}
