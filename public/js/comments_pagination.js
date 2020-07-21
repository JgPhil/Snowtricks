
const forumCommentsElement = document.getElementById("forumComments");
const dateOptions = { year: 'numeric', month: '2-digit', day: '2-digit', hour: '2-digit', minute: '2-digit' };
let figureId = document.getElementById("figureId").textContent;
let url = '/figure/' + figureId + '/next/comments/';

window.addEventListener('scroll', function () {
    if (window.scrollY + window.innerHeight >= document.documentElement.scrollHeight) {
        commentsOffset = forumCommentsElement.children[forumCommentsElement.children.length - 2].firstElementChild.textContent;
        ajaxQuery(url, commentsOffset);
        if (forumCommentsElement.childElementCount >= 10) {
            scrollUpBtn.removeAttribute("hidden");
        }
    }
})




const ajaxQuery = function (url, offset) {

    fetch(url + offset, {
        method: 'GET',
        headers: {
            "X-Requested-Width": "XMLHttpRequest",
            "Content-Type": "application/json"
        },
    }).then(
        response => response.json()
    ).then(data => {
        data.slice.forEach(e => {
            forumCommentsRows(e);
        });
    }).catch(e => alert(e));
}


const forumCommentsRows = function (e) {


    let src = e.pictures ? e.pictures[0].name : "https://eu.ui-avatars.com/api/?name=" + e.author.username;


    let div1 = document.createElement('div');
    div1.classList.add("media", "mb-4");

    let div2 = document.createElement("div");
    div2.setAttribute("hidden", true);
    div2.textContent = e.id;

    let userImg = document.createElement('img');
    userImg.setAttribute("src", src);
    userImg.classList.add("d-flex", "mr-3", "rounded-circle");

    let div3 = document.createElement('div');
    div3.classList.add("media-body");

    let h5 = document.createElement('h5');
    h5.classList.add("mt-0");
    h5.textContent = e.author.username + ' (';

    let date = new Date(e.createdAt);
    let small = document.createElement('small');
    small.textContent = date.toLocaleDateString('fr-FR', dateOptions) + ') ';

    let p = document.createElement('p');
    p.textContent = e.content;



    h5.appendChild(small);
    div3.appendChild(h5);
    div3.appendChild(p);
    div1.appendChild(div2);
    div1.appendChild(userImg);
    div1.appendChild(div3);

    forumCommentsElement.append(div1);

}