//wrappers
const figuresElement = document.getElementById("js-figures");
const commentsElement = document.getElementById("js-comments");
const usersElement = document.getElementById("js-users");
const maxPerPage = 5;
const tables = document.querySelectorAll("table");
const dateOptions = { year: 'numeric', month: '2-digit', day: '2-digit', hour: '2-digit', minute: '2-digit' };

let figuresContent = figuresElement.tBodies;
let commentsContent = commentsElement.tBodies;
let usersContent = usersElement.tBodies;


let figurePaginationlinks = document.querySelectorAll("[data-figure-link]");
figurePaginationlinks[1].classList.add("font-weight-bolder");
let figuresCount = document.querySelector("[data-figuresCount").getAttribute("data-figuresCount");
let figuresCurrentPage = 1;
let figuresPageTarget = null;

let commentPaginationlinks = document.querySelectorAll("[data-comment-link]");
commentPaginationlinks[1].classList.add("font-weight-bolder");
let commentsCount = document.querySelector("[data-commentsCount").getAttribute("data-commentsCount");
let commentsCurrentPage = 1;
let commentsPageTarget = null;

let userPaginationlinks = document.querySelectorAll("[data-user-link]");
userPaginationlinks[1].classList.add("font-weight-bolder");
let usersCount = document.querySelector("[data-usersCount").getAttribute("data-usersCount");
let usersCurrentPage = 1;
let usersPageTarget = null;

let pageTarget = null;
let currentPage = null;
let elementsCount = null;
let arrow = null;
let confirmation = null;



[figurePaginationlinks, commentPaginationlinks, userPaginationlinks].forEach(entityLinks => {
    for (let link of entityLinks) {
        link.addEventListener('click', function (event) {
            event.preventDefault();
            event.stopPropagation();

            if (this.classList.contains('prev')) {
                arrow = "prev";
            } else if (this.classList.contains('next')) {
                arrow = "next";
            }

            linkAttribute = link.getAttributeNames()[1]; // i.e. : data-figure-link attribute for the <a>
            entityName = linkAttribute.split('-')[1] + 's'; // extract the entity name 
            const prevPage = eval(entityName + 'CurrentPage');
            const count = eval(entityName + 'Count');
            target = parseInt(this.textContent, 10); //Récupération du contenu du lien de la pagination (1, 2, 3, 4, "prev", "next")
            [currentPage, pageTarget] = paginationLogic(prevPage, target, count, arrow); //On pose la logique en fonction du lien activé et on récupère les valeurs.

            arrow = null;
            eval(entityName + 'CurrentPage =' + pageTarget)
            eval(pageTarget = entityName + 'PageTarget');
            const content = eval(entityName + 'Content');

            ajaxQuery(`/admin/next/${entityName}/`, parseInt(currentPage, 10), content); //on va chercher les éléments en base

        })

    }
})




//---------------------LOGIC--------------------//


const paginationLogic = function (currentPage, pageTarget, elementsCount, arrow) {

    if (arrow) {
        if (arrow == "prev") { //is prev btn
            if (currentPage != 1) {
                pageTarget = currentPage - 1;
                currentPage -= 1;
            } else {
                pageTarget = 1;
                return [currentPage, pageTarget]; //first page  => return
            }
        } else { //is next btn
            if ((currentPage + 1) > (Math.ceil(elementsCount / maxPerPage))) { //last page => return
                pageTarget = Math.ceil(elementsCount / maxPerPage);
                return [currentPage, pageTarget];
            } else {
                pageTarget = currentPage + 1
                currentPage += 1;
            }
        }
    } else { //targetPage  is a number
        currentPage = pageTarget;
    }
    return [currentPage, pageTarget];
};



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
        // Integration des nouveaux liens créés via JavaScript
        links = document.querySelectorAll("[activation]");
    })


}




//-----//--//--//--TD_ROWS_GENERATING---//--//--//--//-------------//

//figures
const figureRows = function (e) {
    let tbody = document.createElement("tbody");
    let tr = document.createElement("tr");

    let td1 = document.createElement("td");
    td1.textContent = e.id;
    let td2 = document.createElement("td");
    td2.textContent = e.title;
    let td3 = document.createElement("td");
    td3.textContent = e.category.title;
    let figureDate = new Date(e.createdAt);
    let td4 = document.createElement("td");
    td4.textContent = figureDate.toLocaleDateString('fr-FR', dateOptions);
    let td5 = document.createElement("td");
    td5.textContent = e.author.username;
    let activationlink = document.createElement("a");

    if (e.activatedAt !== null) {
        activationlink.setAttribute("href", "/figure/desactivate/" + e.slug);
        activationlink.setAttribute("activation", "true");
        activationlink.classList.add("btn", "btn-warning", "btn-sm");
        activationlink.textContent = "Désactiver";
    } else {
        activationlink.setAttribute("href", "/figure/activate/" + e.slug);
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


    // Bold current page
    for (let f of figurePaginationlinks) {
        f.classList.remove("font-weight-bolder");
        if (parseInt(f.textContent, 10) === figuresCurrentPage) {
            f.classList.add("font-weight-bolder");
        }
    }

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
    let commentDate = new Date(c.createdAt);
    td3.textContent = commentDate.toLocaleDateString('fr-FR', dateOptions);
    let td4 = document.createElement("td");
    td4.textContent = c.author.username;

    let activationlink = document.createElement("a");

    if (c.activatedAt !== null) {
        activationlink.setAttribute("href", "/comment/desactivate/" + c.id);
        activationlink.setAttribute("activation", "true");
        activationlink.classList.add("btn", "btn-warning", "btn-sm");
        activationlink.textContent = "Désactiver";
    } else {
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


    // Bold font  current page

    for (let f of commentPaginationlinks) {
        f.classList.remove("font-weight-bolder");
        if (parseInt(f.textContent, 10) === commentsCurrentPage) {
            f.classList.add("font-weight-bolder");
        }
    }

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
    let userDate = new Date(u.createdAt);
    td3.textContent = userDate.toLocaleDateString('fr-FR', dateOptions);
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
    } else {
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


    // Bold current Page

    for (let f of userPaginationlinks) {
        f.classList.remove("font-weight-bolder");
        if (parseInt(f.textContent, 10) === usersCurrentPage) {
            f.classList.add("font-weight-bolder");
        }
    }

}


//on écoute le click sur l'élément parent car les liens sont ajoutés dynamiquement lors du changement de page !

for (table of tables) {

    table.addEventListener("click", function (e) {
        if (e.target && e.target.matches("a") && e.target.hasAttribute('activation')) {
            e.preventDefault();
            e.stopPropagation();
            let link = e.target;
            //if activation
            if (link.attributes.activation.value == "true") {
                confirmation = "Voulez-vous vraiment désactiver l'élément ?";
                methodR = "DELETE";
            } else {
                confirmation = "Voulez-vous vraiment activer l'élément ?";
                methodR = "PUT";

            }

            //Confirmation ?
            if (confirm(confirmation)) {
                //On envoie une requète Ajax vers le href du lien avec la méthode DELETE
                fetch(link.getAttribute("href"), {
                    method: methodR,
                    headers: {
                        "X-Requested-Width": "XMLHttpRequest",
                        "Content-Type": "application/json"
                    }
                }).then(
                    //Récupération de la réponse en JSON
                    (response) => response.json()
                ).catch(e => alert(e))
                linkSWapp(link);
            }
        }
    })
}


//Cacher un bouton et afficher l'autre

const linkSWapp = function (link) {

    if (link.textContent === "Désactiver") {
        link.textContent = "Activer"
        link.href.split('/')[3] = "activate";
        link.attributes.activation.value = false;
        link.classList.replace("btn-warning", "btn-success");
    } else {
        link.textContent = "Désactiver";
        link.href.split('/')[3] = "desactivate";
        link.attributes.activation.value = true;
        link.classList.replace("btn-success", "btn-warning");
    }
}