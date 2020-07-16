//wrappers
const figuresElement = document.getElementById("js-figures");
const commentsElement = document.getElementById("js-comments");
const usersElement = document.getElementById("js-users");


let figuresContent = figuresElement.tBodies;
let commentsContent = commentsElement.tBodies;
let usersContent = usersElement.tBodies;



let figurePaginationlinks = document.querySelectorAll("[data-figureBtns]");
let figuresCount = document.querySelector("[data-figuresCount").getAttribute("data-figuresCount");
let figuresCurrentPage = 1;
let figuresPageTarget = null;

let commentPaginationlinks = document.querySelectorAll("[data-commentBtns]");
let commentsCount = document.querySelector("[data-commentsCount").getAttribute("data-commentsCount");
let commentsCurrentPage = 1;
let commentsPageTarget = null;

let userPaginationlinks = document.querySelectorAll("[data-userBtns]");
let usersCount = document.querySelector("[data-usersCount").getAttribute("data-usersCount");
let usersCurrentPage = 1;
let usersPageTarget = null;

let pageTarget = null;
let currentPage = null;
let elementsCount = null;
let maxPerPage = 5;
let arrow = null;



//--------------FIGURES----------------//

for (figurePaginationlink of figurePaginationlinks) {
    figurePaginationlink.addEventListener('click', function (event) {
        event.preventDefault();
        event.stopPropagation();

        if (this.classList.contains('prev')) {
            arrow = "prev";
        } else if (this.classList.contains('next')) {
            arrow = "next";
        }

        figuresPageTarget = parseInt(this.textContent, 10); //Récupération du contenu du lien de la pagination (1, 2, 3, 4, "prev", "next")
        [currentPage, pageTarget] = paginationLogic(figuresCurrentPage, figuresPageTarget, figuresCount, arrow); //On pose la logique en fonction du lien activé et on récupère les valeurs.

        figuresCurrentPage = parseInt(currentPage, 10);
        figuresPageTarget = pageTarget;

        ajaxQuery('/admin/next/figures/', figuresPageTarget, figuresContent); //on va chercher les éléments en base
    })
}


//--------------COMMENTS----------------//

for (commentPaginationlink of commentPaginationlinks) {
    commentPaginationlink.addEventListener('click', function (event) {
        event.preventDefault();
        event.stopPropagation();

        if (this.classList.contains('prev')) {
            arrow = "prev";
        } else if (this.classList.contains('next')) {
            arrow = "next";
        }

        commentsPageTarget = parseInt(this.textContent, 10); //Récupération du contenu du lien de la pagination (1, 2, 3, 4, "prev", "next")

        [currentPage, pageTarget] = paginationLogic(commentsCurrentPage, commentsPageTarget, commentsCount, arrow); //On pose la logique en fonction du lien activé et on récupère les valeurs.

        commentsCurrentPage = parseInt(currentPage, 10);
        commentsPageTarget = pageTarget;


        ajaxQuery('/admin/next/comments/', commentsPageTarget, commentsContent); //on va chercher les éléments en base
    })
}


//--------------USERS----------------//

for (userPaginationlink of userPaginationlinks) {
    userPaginationlink.addEventListener('click', function (event) {
        event.preventDefault();
        event.stopPropagation();

        if (this.classList.contains('prev')) {
            arrow = "prev";
        } else if (this.classList.contains('next')) {
            arrow = "next";
        }

        usersPageTarget = parseInt(this.textContent, 10); //Récupération du contenu du lien de la pagination (1, 2, 3, 4, "prev", "next")

        [currentPage, pageTarget] = paginationLogic(usersCurrentPage, usersPageTarget, usersCount, arrow); //On pose la logique en fonction du lien activé et on récupère les valeurs.

        usersCurrentPage = parseInt(currentPage, 10);
        usersPageTarget = pageTarget;

        ajaxQuery('/admin/next/users/', usersPageTarget, usersContent); //on va chercher les éléments en base
    })
}



//---------------------LOGIC--------------------//


const paginationLogic = function (currentPage, pageTarget, elementsCount, arrow = null) {

    if (arrow) {
        if (arrow == "prev") { //prev  btn
            if (currentPage != 1) {
                pageTarget = currentPage - 1;
                currentPage -= 1;
            } else {
                alert('pas d\'entrées plus récentes')
                pageTarget = 1;
            }
        } else { //next btn
            if ((currentPage + 1) > (Math.ceil(elementsCount / maxPerPage))) {
                alert('Vous êtes arrivé au bout du tunnel !');
                pageTarget = Math.ceil(elementsCount / maxPerPage);
            } else {
                pageTarget = currentPage + 1
                currentPage += 1;
            }
        }
        arrow = null;
    } else { //targetPage  is a number
        currentPage = pageTarget;
    }

    return [currentPage, pageTarget];
}



//------------FETCH DATA & DELETE ROWS----------------//


const ajaxQuery = function (url, pageTarget, content) {
    fetch(url + pageTarget, {
        method: 'GET',
        headers: {
            "X-Requested-Width": "XMLHttpRequest",
            "Content-Type": "application/json"
        },
    }).then(
        response => response.json()
    ).then(data => {
        //effacement des 5 éléments précédents
        for (let i = content.length; i >= 1; i--) {
            content[i - 1].remove();
        }

        data.slice.forEach(e => {
            if (content === usersContent) {
                userRows(e);
            } else if (content === commentsContent) {
                commentRows(e);
            } else {
                figureRows(e);
            }

        })
    })
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
        activationlink.classList.add("btn", "btn-warning", "btn-sm");
        activationlink.textContent = "Désactiver";
    }
    else {
        activationlink.setAttribute("href", "/figure/activate/" + e.id);
        activationlink.setAttribute("activation", "false");
        activationlink.classList.add("btn", "btn-success", "btn-sm");
        activationlink.textContent = "Activer";
    }


    figuresElement.append(tbody);
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


    commentsElement.append(tbody);
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
        activationlink.classList.add("btn", "btn-warning", "btn-sm");
        activationlink.textContent = "Désactiver";
    }
    else {
        activationlink.setAttribute("href", "/user/activate/" + u.id);
        activationlink.setAttribute("activation", "false");
        activationlink.classList.add("btn", "btn-success", "btn-sm");
        activationlink.textContent = "Activer";
    }


    usersElement.append(tbody);
    tbody.appendChild(tr);
    tr.appendChild(td1);
    tr.appendChild(td2);
    tr.appendChild(td3);
    tr.appendChild(td4);
    tr.appendChild(td5);
    tr.appendChild(activationlink);

}


