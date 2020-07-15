
const forumCommentsElement = document.getElementById("forumComments");
const forumCommentsContent = forumCommentsElement.children;
const nextForumCommentsPagination = document.getElementById("next_forum_comments_pgn");
const prvsForumCommentsPagination = document.getElementById("prvs_forum_comments_pgn");
let figureId = document.getElementById("figureId").textContent;

//--//--//------FORUM_COMMENTS---------/--//--//--//-----------

//Next  Pagination
nextForumCommentsPagination.addEventListener('click', function (event) {

    prvsForumCommentsPagination.removeAttribute("hidden");
    commentsOffset = forumCommentsElement.lastElementChild.firstElementChild.textContent;

    event.stopPropagation();
    event.preventDefault();

    //AJAX QUERY
    ajaxQuery('/next/comments/', figureId, commentsOffset, forumCommentsContent, nextForumCommentsPagination);
})

//Previous  Pagination
prvsForumCommentsPagination.addEventListener('click', function (event) {

    nextForumCommentsPagination.removeAttribute("hidden");
    commentsOffset = forumCommentsElement.firstElementChild.firstElementChild.textContent;

    event.stopPropagation();
    event.preventDefault();

    //AJAX QUERY
    ajaxQuery('/prvs/comments/', figureId, commentsOffset, forumCommentsContent, prvsForumCommentsPagination, true);

})

const ajaxQuery = function (url, figureId, offset, content, pgnButton, prvs = false) {

    fetch(url + offset + '/' + offset, {
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
                    forumCommentsRows(e)
                });
            } else {
                data.slice.forEach(e => {
                    forumCommentsRows(e)
                });
            }
        }
    }).catch(e => alert(e));
}


const forumCommentsRows = function (e) {

    let div1 = document.createElement('div');
    div1.classList.add("media", "mb-4");

    let div2 = document.createElement("div");
    div2.setAttribute("hidden", true);
    div2.textContent = e.id;

    let userImg = document.createElement('img');
    userImg.setAttribute("src", "");
    userImg.classList.add("d-flex", "mr-3", "rounded-circle");

    let div3 = document.createElement('div');
    div3.classList.add("media-body");

    let h5 = document.createElement('h5');
    h5.classList.add("mt-0");
    h5.textContent = e.author.username + ' (';

    let small = document.createElement('small');
    small.textContent = e.createdAt + ') ';

    let p = document.createElement('p');
    p.textContent = e.id;



    h5.appendChild(small);
    div3.appendChild(h5);
    div3.appendChild(p);
    div1.appendChild(div2);
    div1.appendChild(userImg);
    div1.appendChild(div3);

    forumCommentsElement.append(div1);

}