//wrappers
const figuresElement = document.getElementById("js-figures");
const commentsElement = document.getElementById("js-comments");
const usersElement = document.getElementById("js-users");

//pagination 
const nextFiguresPagination = document.getElementById("next_figures_pgn");
const prvsFiguresPagination = document.getElementById("prvs_figures_pgn");

const nextCommentsPagination = document.getElementById("next_comments_pgn");
const prvsCommentsPagination = document.getElementById("prvs_comments_pgn");

const nextUsersPagination = document.getElementById("next_users_pgn");
const prvsUsersPagination = document.getElementById("prvs_users_pgn");

let figuresTBodies = figuresElement.nextElementSibling.tBodies;
let commentsTBodies = commentsElement.nextElementSibling.tBodies;
let usersTBodies = usersElement.nextElementSibling.tBodies;

// Fetching the last id to determine the offset
let figuresOffset = "";
let commentsOffset = "";
let usersOffset = "";



//--//--//------FIGURES---------/--//--//--//-----------

//Next  Pagination
nextFiguresPagination.addEventListener('click', function (event) {

    prvsFiguresPagination.removeAttribute("hidden");
    figuresOffset = figuresElement.nextElementSibling.lastElementChild.lastElementChild.firstElementChild.textContent;


    event.stopPropagation();
    event.preventDefault();

    //AJAX QUERY

    fetch('/admin/next/figures/' + figuresOffset, {
        method: 'GET',
        headers: {
            "X-Requested-Width": "XMLHttpRequest",
            "Content-Type": "application/json"
        },
    }).then(
        response => response.json()
    ).then(data => {
        if (data.slice.length < 5) {
            nextFiguresPagination.setAttribute("hidden", true)
        }
        //effacement des éléments précédents
        for (let i = figuresTBodies.length; i >= 1; i--) {
            figuresTBodies[i - 1].remove();
        }
        //création d'une ligne par élément récupéré
        data.slice.forEach(e => {
            tbody = figureRows(e);
        });
    }).catch(e => alert(e));
})

//Previous  Pagination
prvsFiguresPagination.addEventListener('click', function (event) {

    nextFiguresPagination.removeAttribute("hidden");
    figuresOffset = figuresElement.nextElementSibling.tBodies[0].firstElementChild.firstElementChild.textContent;

    event.stopPropagation();
    event.preventDefault();

    //AJAX QUERY

    fetch('/admin/prvs/figures/' + figuresOffset, {
        method: 'GET',
        headers: {
            "X-Requested-Width": "XMLHttpRequest",
            "Content-Type": "application/json"
        },
    }).then(
        response => response.json()
    ).then(data => {
        if (data.slice.length < 1) {
            alert("Pas d'entrées plus récentes !")
        }
        else {
            //effacement des 5 éléments précédents
            for (let i = figuresTBodies.length; i >= 1; i--) {
                figuresTBodies[i - 1].remove();
            }
            //création d'une ligne par élément récupéré
            data.slice.reverse().forEach(e => {
                figureRows(e);
            });
        }
    }).catch(e => alert(e));
})



//--//--//------COMMENTS---------/--//--//--//-----------

//Next  Pagination
nextCommentsPagination.addEventListener('click', function (event) {

    prvsCommentsPagination.removeAttribute("hidden");
    commentsOffset = commentsElement.nextElementSibling.lastElementChild.lastElementChild.firstElementChild.textContent;

    event.stopPropagation();
    event.preventDefault();

    //AJAX QUERY

    fetch('/admin/next/comments/' + commentsOffset, {
        method: 'GET',
        headers: {
            "X-Requested-Width": "XMLHttpRequest",
            "Content-Type": "application/json"
        },
    }).then(
        response => response.json()
    ).then(data => {
        if (data.slice.length < 5) {
            nextCommentsPagination.setAttribute("hidden", true)
        }
        //effacement des éléments précédents
        for (let i = commentsTBodies.length; i >= 1; i--) {
            commentsTBodies[i - 1].remove();
        }
        //création d'une ligne par élément récupéré
        data.slice.forEach(c => {
            commentRows(c);
        });
    }).catch(e => alert(e));
})

//Previous  Pagination
prvsCommentsPagination.addEventListener('click', function (event) {

    nextCommentsPagination.removeAttribute("hidden");
    commentsOffset = commentsElement.nextElementSibling.tBodies[0].firstElementChild.firstElementChild.textContent;

    event.stopPropagation();
    event.preventDefault();

    //AJAX QUERY

    fetch('/admin/prvs/comments/' + commentsOffset, {
        method: 'GET',
        headers: {
            "X-Requested-Width": "XMLHttpRequest",
            "Content-Type": "application/json"
        },
    }).then(
        response => response.json()
    ).then(data => {
        if (data.slice.length < 1) {
            alert("Pas d'entrées plus récentes !")
        }
        else {
            //effacement des 5 éléments précédents
            for (let i = commentsTBodies.length; i >= 1; i--) {
                commentsTBodies[i - 1].remove();
            }
            //création d'une ligne par élément récupéré
            data.slice.reverse().forEach(c => {
                commentRows(c);
            });
        }
    }).catch(e => alert(e));
})



//--//--//------USERS---------/--//--//--//-----------

//Next  Pagination
nextUsersPagination.addEventListener('click', function (event) {

    prvsUsersPagination.removeAttribute("hidden");
    usersOffset = usersElement.nextElementSibling.lastElementChild.lastElementChild.firstElementChild.textContent;


    event.stopPropagation();
    event.preventDefault();

    //AJAX QUERY

    fetch('/admin/next/users/' + usersOffset, {
        method: 'GET',
        headers: {
            "X-Requested-Width": "XMLHttpRequest",
            "Content-Type": "application/json"
        },
    }).then(
        response => response.json()
    ).then(data => {
        if (data.slice.length < 5) {
            nextUsersPagination.setAttribute("hidden", true)
        }
        //effacement des éléments précédents
        for (let i = usersTBodies.length; i >= 1; i--) {
            usersTBodies[i - 1].remove();
        }
        //création d'une ligne par élément récupéré
        data.slice.forEach(u => {
            userRows(u);
        });
    }).catch(e => alert(e));
})

//Previous  Pagination
prvsUsersPagination.addEventListener('click', function (event) {

    nextUsersPagination.removeAttribute("hidden");
    usersOffset = usersElement.nextElementSibling.tBodies[0].firstElementChild.firstElementChild.textContent;

    event.stopPropagation();
    event.preventDefault();

    //AJAX QUERY

    fetch('/admin/prvs/users/' + usersOffset, {
        method: 'GET',
        headers: {
            "X-Requested-Width": "XMLHttpRequest",
            "Content-Type": "application/json"
        },
    }).then(
        response => response.json()
    ).then(data => {
        if (data.slice.length < 1) {
            alert("Pas d'entrées plus récentes !")
        }
        else {
            //effacement des 5 éléments précédents
            for (let i = usersTBodies.length; i >= 1; i--) {
                usersTBodies[i - 1].remove();
            }
            //création d'une ligne par élément récupéré
            data.slice.reverse().forEach(e => {
                userRows(e);
            });
        }
    }).catch(e => alert(e));
})



//-----//--//--//--ROWS_GENERATING---//--//--//--//-------------//

//figures 
const figureRows = function (e) {
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

//comments 
const commentRows = function (c) {
    let tbody = document.createElement("tbody");
    let tr = document.createElement("tr");

    let td1 = document.createElement("td");
    td1.textContent = c.id;
    let td2 = document.createElement("td");
    td2.textContent = c.content;
    let td3 = document.createElement("td");
    td3.textContent = c.createdAt;
    let td4 = document.createElement("td");
    td4.textContent = c.author.username;

    let activationlink = document.createElement("a");

    if (c.activatedAt !== null) {
        activationlink.setAttribute("href", "/comment/desactivate/" + c.id);
        activationlink.setAttribute("activation", "true");
        activationlink.setAttribute("data-token", "{{ csrf_token('delete'" + c.id + ")}}");
        activationlink.classList.add("btn", "btn-warning", "btn-sm");
        activationlink.textContent = "Désactiver";
    }
    else {
        activationlink.setAttribute("href", "/comment/activate/" + c.id);
        activationlink.setAttribute("activation", "false");
        activationlink.setAttribute("data-token", "{{ csrf_token('activate'" + c.id + ")}}");
        activationlink.classList.add("btn", "btn-success", "btn-sm");
        activationlink.textContent = "Activer";
    }


    commentsElement.nextElementSibling.append(tbody);
    tbody.appendChild(tr);
    tr.appendChild(td1);
    tr.appendChild(td2);
    tr.appendChild(td3);
    tr.appendChild(td4);
    tr.appendChild(activationlink);

}

//users 
const userRows = function (u) {
    let tbody = document.createElement("tbody");
    let tr = document.createElement("tr")

    let td1 = document.createElement("td");
    td1.textContent = u.id;
    let td2 = document.createElement("td");
    td2.textContent = u.username;
    let td3 = document.createElement("td");
    td3.textContent = u.createdAt;
    let td4 = document.createElement("td");
    td4.textContent = u.email;
    let td5 = document.createElement("td");
    td5.textContent = u.roles;

    let activationlink = document.createElement("a");

    if (u.activatedAt !== null) {
        activationlink.setAttribute("href", "/user/desactivate/" + u.id);
        activationlink.setAttribute("activation", "true");
        activationlink.setAttribute("data-token", "{{ csrf_token('delete'" + u.id + ")}}");
        activationlink.classList.add("btn", "btn-warning", "btn-sm");
        activationlink.textContent = "Désactiver";
    }
    else {
        activationlink.setAttribute("href", "/user/activate/" + u.id);
        activationlink.setAttribute("activation", "false");
        activationlink.setAttribute("data-token", "{{ csrf_token('activate'" + u.id + ")}}");
        activationlink.classList.add("btn", "btn-success", "btn-sm");
        activationlink.textContent = "Activer";
    }


    usersElement.nextElementSibling.append(tbody);
    tbody.appendChild(tr);
    tr.appendChild(td1);
    tr.appendChild(td2);
    tr.appendChild(td3);
    tr.appendChild(td4);
    tr.appendChild(td5);
    tr.appendChild(activationlink);

}
