const xhr = new XMLHttpRequest();

let loadButton = document.querySelector("#js-load");
let result = null;
let more = document.querySelector("#js-more"); //all figures container

let scrollUpBtn = document.getElementById("js-btn-scroll-up");

scrollUpBtn.addEventListener('click', function(){
    scrollUpBtn.style.display = 'none';
  })
  
loadButton.addEventListener('click', function (event) {
    event.stopPropagation();
    event.preventDefault();

    let lasFigUrlArray = more.lastElementChild.lastElementChild.lastElementChild.firstElementChild.firstElementChild.firstElementChild.href.split('/');
    //the last figure url link splitted into an array
    let offset = lasFigUrlArray.pop(); // search the last occurence in the array -> figureID
    let url = "/more/figures/" + offset; // construct the url with the last figureID to get new figures in database

    xhr.open("GET", url);
    xhr.onreadystatechange = function () {
        if (this.readyState == XMLHttpRequest.DONE && this.status == 200) {
            let response = this.response
            result = JSON.parse(response);

            if (result.sliceFigures.length < 5) {
                loadButton.style.display = 'none';
            }

            result.sliceFigures.forEach(function (k) {
                let div1 = document.createElement('div');
                div1.classList.add("col-md-6", "col-lg-4");

                let div2 = document.createElement('div');
                div2.classList.add("card", "mb-4");

                let img = document.createElement('img');
                img.classList.add('card-img-top');
                img.src = k.pictures.length !== 0 ? "/uploads/pictures/" + k.pictures[0].name : "http://placehold.it/300x200";

                let div3 = document.createElement('div');
                div3.classList.add("card-body");

                let div4 = document.createElement('div');
                div4.classList.add("row");

                let div5 = document.createElement('div');
                div5.classList.add("col-5");

                let h5 = document.createElement('h5');
                h5.classList.add("col-7");

                let title = document.createElement('a');
                title.classList.add("card-title");
                title.innerHTML = k.title;
                title.href = "/figure/" + k.id;


                //Update icon
                let fasUpdateIcon = document.createElement('i');
                fasUpdateIcon.classList.add("fas", "fa-pencil-alt", "fa-2x");

                let updateLink = document.createElement('a');
                updateLink.href = "/figure/edit/" + k.id;
                updateLink.classList.add("p-1");


                //Delete Icon
                let fasDeleteIcon = document.createElement('i');
                fasDeleteIcon.classList.add("fas", "fa-trash-alt", "fa-2x");

                let deleteLink = document.createElement('a');
                deleteLink.href = "/figure/delete/" + k.id;
                deleteLink.classList.add("p-1");


                div1.appendChild(div2);
                div2.appendChild(img);
                div2.appendChild(div3);
                div3.appendChild(div4);
                h5.appendChild(title);
                div4.appendChild(h5);
                div4.appendChild(div5);
                if (window.user) { //if user logged in
                    updateLink.appendChild(fasUpdateIcon);
                    deleteLink.appendChild(fasDeleteIcon);
                    div5.appendChild(updateLink);
                    div5.appendChild(deleteLink);
                }


                more.appendChild(div1);
            });
        }
    };
    xhr.send();

    if (more.childElementCount >= 10) {
        scrollUpBtn.removeAttribute("hidden");
    }
});