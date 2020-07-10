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
let tBodies = figuresElement.nextElementSibling.tBodies;

//rows for every category
let rows = 5;
let page = 1;

// Fetching the last id to determine the offset
let figuresOffset = "";
let commentsOffset = commentsElement.nextElementSibling.lastElementChild.lastElementChild.firstElementChild.textContent;
let usersOffset = usersElement.nextElementSibling.lastElementChild.lastElementChild.firstElementChild.textContent;




//Next Pagination
nextFigurePagination.addEventListener('click', function (event) {

    figuresOffset = figuresElement.nextElementSibling.lastElementChild.lastElementChild.firstElementChild.textContent;


    event.stopPropagation();
    event.preventDefault();

    //AJAX QUERY

    fetch('/admin/more/figures/' + figuresOffset, {
        method: 'GET',
        headers: {
            "X-Requested-Width": "XMLHttpRequest",
            "Content-Type": "application/json"
        },
    }).then(
        response => response.json()
    ).then(data => {
        //effacement des 5 éléments précédents
        for (let i = 5; i > 0; i--) {
            tBodies[i - 1].remove();
        }
        //création d'une ligne par élément récupéré
        data.slice.forEach(e => {
            tbody = trArray(e);
        });;
    }).catch(e => alert(e));



})


//fonction de création de ligne table tbody
const trArray = function (e) {
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

    if (e.activatedAt !== null) {
        activationlink.setAttribute("href", "/figure/desactivate/" + e.id);
        activationlink.setAttribute("activation", "true");
        activationlink.setAttribute("data-token", "{{ csrf_token('delete'" + e.id + ")}}");
        activationlink.classList.add("btn", "btn-warning", "btn-sm");
        activationlink.textContent = "Désactiver";
    }
    else {
        activationlink.setAttribute("href", "/figure/activate/" + e.id);
        activationlink.setAttribute("activation", "false");
        activationlink.setAttribute("data-token", "{{ csrf_token('activate'" + e.id + ")}}");
        activationlink.classList.add("btn", "btn-success", "btn-sm");
        activationlink.textContent = "Activer";
    }


    figuresElement.nextElementSibling.append(tbody);
    tbody.appendChild(tr);
    tr.appendChild(td1);
    tr.appendChild(td2);
    tr.appendChild(td3);
    tr.appendChild(td4);
    tr.appendChild(td5);
    tr.appendChild(activationlink);

}