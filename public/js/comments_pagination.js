
const forumCommentsElement = document.getElementById("forumComments");
let figureId = document.getElementById("figureId").textContent;
let url = '/figure/'+figureId+'/next/comments/';

//--//--//------COMMENTS---------/--//--//--//-----------


window.addEventListener('scroll', function () {
    if (window.scrollY + window.innerHeight + 100 >= document.documentElement.scrollHeight) {
        commentsOffset = forumCommentsElement.lastElementChild.firstElementChild.textContent;
        ajaxQuery(url, commentsOffset);
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
    })/* .catch(e => alert(e))  */;
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