const xhr = new XMLHttpRequest();

let loadButton = document.querySelector("#js-load");
let result = null;
let more = document.querySelector("#js-more"); //all figures container

loadButton.addEventListener('click', function (event) {
    event.stopPropagation();
    event.preventDefault();

    let lasFigUrlArray = more.lastElementChild.lastElementChild.lastElementChild.firstElementChild.firstElementChild.href.split('/');
    //the last figure url link splitted into an array
    let offset = lasFigUrlArray.pop(); // search the last occurence in the array -> figureID
    let url = "/more/" + offset; // construct the url with the last figureID to get new figures in database

    xhr.open("GET", url);
    xhr.onreadystatechange = function () {
        if (this.readyState == XMLHttpRequest.DONE && this.status == 200) {
            let response = this.response
            result = JSON.parse(response);

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

                let h5 = document.createElement('h5');
                h5.classList.add("card-title");

                let showLink = document.createElement('a');
                showLink.classList.add("card-title");
                showLink.innerHTML = k.id;
                showLink.href = "/figure/" + k.id;


                //Update icon
                let fasUpdateIcon = document.createElement('i');
                fasUpdateIcon.classList.add("fas", "fa-pencil-alt", "fa-2x");

                let updateLink = document.createElement('a');
                updateLink.href = "/figure/edit/" + k.id;


                //Delete Icon
                let fasDeleteIcon = document.createElement('i');
                fasDeleteIcon.classList.add("fas", "fa-trash-alt", "fa-2x");

                let deleteLink = document.createElement('a');
                deleteLink.href = "/figure/delete/" + k.id;


                let description = document.createElement('p');
                description.classList.add("card-text");
                description.innerHTML = k.description.substring(0, 90);

                let button = document.createElement('a');
                button.classList.add("btn", "btn-primary", "btn-lg");
                button.href = "http://127.0.0.1:8000/figure/" + k.id;
                button.text = "Voir";




                div1.appendChild(div2);
                div2.appendChild(img);
                div2.appendChild(div3);
                h5.appendChild(showLink);
                div3.appendChild(h5);
                div3.appendChild(description);
                div3.appendChild(button);
                if (window.user) { //if user logged in
                    updateLink.appendChild(fasUpdateIcon);
                    deleteLink.appendChild(fasDeleteIcon);
                    div3.appendChild(updateLink);
                    div3.appendChild(deleteLink);
                }


                more.appendChild(div1);
            });
        }
    };
    xhr.send();
});