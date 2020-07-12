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
    ajaxQuery('/admin/next/figures/', figuresOffset, figuresTBodies, nextFiguresPagination);
})

//Previous  Pagination
prvsFiguresPagination.addEventListener('click', function (event) {

    nextFiguresPagination.removeAttribute("hidden");
    figuresOffset = figuresElement.nextElementSibling.tBodies[0].firstElementChild.firstElementChild.textContent;

    event.stopPropagation();
    event.preventDefault();

    //AJAX QUERY
    ajaxQuery('/admin/prvs/figures/', figuresOffset, figuresTBodies, prvsFiguresPagination, true);
})



//--//--//------COMMENTS---------/--//--//--//-----------

//Next  Pagination
nextCommentsPagination.addEventListener('click', function (event) {

    prvsCommentsPagination.removeAttribute("hidden");
    commentsOffset = commentsElement.nextElementSibling.lastElementChild.lastElementChild.firstElementChild.textContent;

    event.stopPropagation();
    event.preventDefault();

    //AJAX QUERY
    ajaxQuery('/admin/next/comments/', commentsOffset, commentsTBodies, nextCommentsPagination);
})

//Previous  Pagination
prvsCommentsPagination.addEventListener('click', function (event) {

    nextCommentsPagination.removeAttribute("hidden");
    commentsOffset = commentsElement.nextElementSibling.tBodies[0].firstElementChild.firstElementChild.textContent;

    event.stopPropagation();
    event.preventDefault();

    //AJAX QUERY
    ajaxQuery('/admin/prvs/comments/', commentsOffset, commentsTBodies, prvsCommentsPagination, true);

})



//--//--//------USERS---------/--//--//--//-----------

//Next  Pagination
nextUsersPagination.addEventListener('click', function (event) {

    prvsUsersPagination.removeAttribute("hidden");
    usersOffset = usersElement.nextElementSibling.lastElementChild.lastElementChild.firstElementChild.textContent;


    event.stopPropagation();
    event.preventDefault();

    //AJAX QUERY
    ajaxQuery('/admin/next/users/', usersOffset, usersTBodies, nextUsersPagination)

})

//Previous  Pagination
prvsUsersPagination.addEventListener('click', function (event) {

    nextUsersPagination.removeAttribute("hidden");
    usersOffset = usersElement.nextElementSibling.tBodies[0].firstElementChild.firstElementChild.textContent;

    event.stopPropagation();
    event.preventDefault();

    //AJAX QUERY
    ajaxQuery('/admin/prvs/users/', usersOffset, usersTBodies, prvsUsersPagination, true);
})



//-----//--//--//--AJAX_QUERY---//--//--//--//-------------//
const ajaxQuery = function (url, offset, tBodies, pgnButton, prvs = false) {

    fetch(url + offset, {
        method: 'GET',
        headers: {
            "X-Requested-Width": "XMLHttpRequest",
            "Content-Type": "application/json"
        },
    }).then(
        response => response.json()
    ).then(data => {
        if (data.slice.length < 5) {
            pgnButton.setAttribute("hidden", true);
        }
        else {
            //effacement des 5 éléments précédents
            for (let i = tBodies.length; i >= 1; i--) {
                tBodies[i - 1].remove();
            }
            // Si c'est un click "Previous" => on retourne le résultat pour avoir les éléments par ordre DESC
            if (prvs) {
                data.slice.reverse().forEach(e => {  //création d'une ligne par élément récupéré
                    if (tBodies === usersTBodies) {
                        userRows(e);
                    } else if (tBodies === commentsTBodies) {
                        commentRows(e);
                    } else {
                        figureRows(e);
                    }

                });
            } else {
                data.slice.forEach(e => {
                    if (tBodies === usersTBodies) {
                        userRows(e);
                    } else if (tBodies === commentsTBodies) {
                        commentRows(e);
                    } else {
                        figureRows(e);
                    }

                });
            }

        }
    }).catch(e => alert(e));

}


//-----//--//--//--TD_ROWS_GENERATING---//--//--//--//-------------//

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
