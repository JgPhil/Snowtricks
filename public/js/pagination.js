//wrappers
const figuresElement = document.getElementById("js-figures");
const commentsElement = document.getElementById("js-comments");
const usersElement = document.getElementById("js-users");

//pagination 
const nextFigurePagination = document.getElementById("next_figures_pgn");
const prvsFigurePagination = document.getElementById("prvs_figures_pgn");

const nextCommentsPagination = document.getElementById("next_comments_pgn");
const prvsCommentsPagination = document.getElementById("prvs_comments_pgn");

const nextUsersPagination = document.getElementById("next_users_pgn");
const prvsUsersPagination = document.getElementById("prvs_users_pgn");

let figuresCurrentPage, commentsCurrentPage, usersCurrentPage = 1;

//rows for every category
let rows = 5;


// Fetching the last id to determine the offset
let figuresOffset = "";
let commentsOffset = commentsElement.nextElementSibling.lastElementChild.lastElementChild.firstElementChild.textContent;
let usersOffset = usersElement.nextElementSibling.lastElementChild.lastElementChild.firstElementChild.textContent;

nextFigurePagination.addEventListener('click', function (event) {

    figuresOffset = figuresElement.nextElementSibling.lastElementChild.lastElementChild.firstElementChild.textContent;

    event.stopPropagation();
    event.preventDefault();

    fetch('/admin/more/figures/' + figuresOffset, {
        method: 'GET',
        headers: {
            "X-Requested-Width": "XMLHttpRequest",
            "Content-Type": "application/json"
        },
    }).then(
        response => response.json()
    ).then(data => {
        data.slice.forEach(e => {
            tbody = trArray(e);
            

        });;
    })

})

const trArray = function(e) {
    let tbody = document.createElement("tbody");
            let tr = document.createElement("tr")

            let td1 = document.createElement("td");
            td1.textContent = e.id;
            let td2 = document.createElement("td");
            td2.textContent = e.title;
            let td3 = document.createElement("td");
            td3.textContent = e.category.title;
            let td4 = document.createElement("td");
            td4.textContent = e.createdAt;
            let td5 = document.createElement("td");
            td5.textContent = e.author.username;
            let activationlink = document.createElement("a");
            activationlink.textContent = "Alors là ?"

            figuresElement.nextElementSibling.append(tbody);
            tbody.appendChild(tr);

            tr.appendChild(td1);
            tr.appendChild(td2);
            tr.appendChild(td3);
            tr.appendChild(td4);
            tr.appendChild(td5);
            tr.appendChild(activationlink);

}