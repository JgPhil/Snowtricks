//wrappers
const figuresElement = document.getElementById("js-figures");
/* const commentsElement = document.getElementById("js-comments");
const usersElement = document.getElementById("js-users");

//pagination buttons
const nextFiguresPagination = document.getElementById("next_figures_pgn");
const prvsFiguresPagination = document.getElementById("prvs_figures_pgn");

const nextCommentsPagination = document.getElementById("next_comments_pgn");
const prvsCommentsPagination = document.getElementById("prvs_comments_pgn");

const nextUsersPagination = document.getElementById("next_users_pgn");
const prvsUsersPagination = document.getElementById("prvs_users_pgn");
 */
let figuresContent = figuresElement.nextElementSibling.tBodies;
/* let commentsContent = commentsElement.nextElementSibling.tBodies;
let usersContent = usersElement.nextElementSibling.tBodies; */

let figuresOffset = "";/* 
let commentsOffset = "";
let usersOffset = "";  */

/////////////////////////////////////////Nouvel essai

let figurePaginationlinks = document.querySelectorAll("[data-figure]");
let figuresCount = document.querySelector("[data-figuresCount");/* 
let figuresCurrentPage = document.querySelector("[data-figuresCurrentPage"); */
let figuresPage = null;

for (figurePaginationlink of figurePaginationlinks) {
    figurePaginationlink.addEventListener('click', function (event) {
        event.stopPropagation();
        event.preventDefault();

        if (Number.isInteger(parseInt(this.textContent))) {
            figuresPage = this.textContent;
        } else {
            if (this.textContent == 'prev') { //prev  btn
                if (figuresCurrentPage != 1) {
                    figuresPage = figuresCurrentPage - 1;
                    figuresCurrentPage -= 1;
                } else {
                    alert('pas d\'entrées plus récentes')
                }
            } else { //next btn
                figuresPage = figuresCurrentPage + 1
                figuresCurrentPage += 1;
            }
        }


        fetch('/admin/next/figures/' + figuresPage, {
            method: 'GET',
            headers: {
                "X-Requested-Width": "XMLHttpRequest",
                "Content-Type": "application/json"
            },
        }).then(
            response => response.json()
        ).then(data => {

            //effacement des 5 éléments précédents
            for (let i = figuresContent.length; i >= 1; i--) {
                figuresContent[i - 1].remove();
            }

            data.figuresList.forEach(e => {
                figureRows(e);

            })
        })
    })
}





/*
 
//--//--//------FIGURES---------/--//--//--//-----------
 
//Next  Pagination
nextFiguresPagination.addEventListener('click', function (event) {
 
    prvsFiguresPagination.removeAttribute("hidden");
    figuresOffset = figuresElement.nextElementSibling.lastElementChild.lastElementChild.firstElementChild.textContent;
 
 
    event.stopPropagation();
    event.preventDefault();
 
    //AJAX QUERY
    ajaxQuery('/admin/next/figures/', figuresPage, figuresContent, nextFiguresPagination);
})
 
//Previous  Pagination
prvsFiguresPagination.addEventListener('click', function (event) {
 
    nextFiguresPagination.removeAttribute("hidden");
    figuresOffset = figuresElement.nextElementSibling.tBodies[0].firstElementChild.firstElementChild.textContent;
 
    event.stopPropagation();
    event.preventDefault();
 
    //AJAX QUERY
    ajaxQuery('/admin/prvs/figures/', figuresOffset, figuresContent, prvsFiguresPagination, true);
})
 
 
 
//--//--//------ADMIN_PANEL_COMMENTS---------/--//--//--//-----------
 
//Next  Pagination
nextCommentsPagination.addEventListener('click', function (event) {
 
    prvsCommentsPagination.removeAttribute("hidden");
    commentsOffset = commentsElement.nextElementSibling.lastElementChild.lastElementChild.firstElementChild.textContent;
 
    event.stopPropagation();
    event.preventDefault();
 
    //AJAX QUERY
    ajaxQuery('/admin/next/comments/', commentsOffset, commentsContent, nextCommentsPagination);
})
 
//Previous  Pagination
prvsCommentsPagination.addEventListener('click', function (event) {
 
    nextCommentsPagination.removeAttribute("hidden");
    commentsOffset = commentsElement.nextElementSibling.tBodies[0].firstElementChild.firstElementChild.textContent;
 
    event.stopPropagation();
    event.preventDefault();
 
    //AJAX QUERY
    ajaxQuery('/admin/prvs/comments/', commentsOffset, commentsContent, prvsCommentsPagination, true);
 
})
 
 
//--//--//------USERS---------/--//--//--//-----------
 
//Next  Pagination
nextUsersPagination.addEventListener('click', function (event) {
 
    prvsUsersPagination.removeAttribute("hidden");
    usersOffset = usersElement.nextElementSibling.lastElementChild.lastElementChild.firstElementChild.textContent;
 
 
    event.stopPropagation();
    event.preventDefault();
 
    //AJAX QUERY
    ajaxQuery('/admin/next/users/', usersOffset, usersContent, nextUsersPagination)
 
})
 
//Previous  Pagination
prvsUsersPagination.addEventListener('click', function (event) {
 
    nextUsersPagination.removeAttribute("hidden");
    usersOffset = usersElement.nextElementSibling.tBodies[0].firstElementChild.firstElementChild.textContent;
 
    event.stopPropagation();
    event.preventDefault();
 
    //AJAX QUERY
    ajaxQuery('/admin/prvs/users/', usersOffset, usersContent, prvsUsersPagination, true);
}) */


/*
//-----//--//--//--AJAX_QUERY---//--//--//--//-------------//
const ajaxQuery = function (url, page, content, pgnButton, prvs = false) {
 
    fetch(url + page, {
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
            for (let i = content.length; i >= 1; i--) {
                content[i - 1].remove();
            }
            // Si c'est un click "Previous" => on retourne le résultat pour avoir les éléments par ordre DESC
            if (prvs) {
                data.slice.reverse().forEach(e => {  //création d'une ligne par élément récupéré
                    if (content === usersContent) {
                        userRows(e);
                    } else if (content === commentsContent) {
                        commentRows(e);
                    } else {
                        figureRows(e);
                    }
 
                });
            } else {
                data.slice.forEach(e => {
                    if (content === usersContent) {
                        userRows(e);
                    } else if (content === commentsContent) {
                        commentRows(e);
                    } else {
                        figureRows(e);
                    }
 
                });
            }
 
        }
    }).catch(e => alert(e));
 
} */


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
        activationlink.classList.add("btn", "btn-warning", "btn-sm");
        activationlink.textContent = "Désactiver";
    }
    else {
        activationlink.setAttribute("href", "/figure/activate/" + e.id);
        activationlink.setAttribute("activation", "false");
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

//comments admin panel
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
        activationlink.classList.add("btn", "btn-warning", "btn-sm");
        activationlink.textContent = "Désactiver";
    }
    else {
        activationlink.setAttribute("href", "/comment/activate/" + c.id);
        activationlink.setAttribute("activation", "false");
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

//comments forum


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
        activationlink.classList.add("btn", "btn-warning", "btn-sm");
        activationlink.textContent = "Désactiver";
    }
    else {
        activationlink.setAttribute("href", "/user/activate/" + u.id);
        activationlink.setAttribute("activation", "false");
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


