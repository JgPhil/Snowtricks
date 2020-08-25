let loadButton = document.querySelector("#js-load");
let result = null;
let more = document.querySelector("#js-more"); //all figures container

const resultsperWidth = function() {
    if (window.matchMedia("(min-width: 1200px)").matches) {
        maxResults = 8;
    } else if (window.matchMedia("(min-width: 992px)").matches) {
        maxResults = 6;
    } else if (window.matchMedia("(min-width: 576px)").matches) {
        maxResults = 4;
    }
    return maxResults;
}



loadButton.addEventListener('click', function(event) {
    event.stopPropagation();
    event.preventDefault();

    let lasFigUrlArray = more.lastElementChild.lastElementChild.lastElementChild.firstElementChild.firstElementChild.firstElementChild.href.split('/');
    //the last figure url link splitted into an array
    let slug = lasFigUrlArray.pop(); // search the last occurence in the array -> figureID
    let maxResults = resultsperWidth();

    let url = "/more/figures/" + slug + "/" + maxResults; // construct the url with the last figureID to get new figures in database

    fetch(url, {
        method: "GET"
    }).then(response => response.json()).then(data => {
        if (data.sliceFigures.length < 4) {
            loadButton.style.display = "none";
        }
        data.sliceFigures.forEach(function(k) {
            let defaultPicture = null;

            let div1 = document.createElement('div');
            div1.classList.add("col-sm-6", "col-md-4", "col-lg-3");

            let div2 = document.createElement('div');
            div2.classList.add("card", "mb-4");

            let img = document.createElement('img');
            img.classList.add('card-img-top');
            // Search for default picture
            for (picture of k.pictures) {
                if (picture.sortOrder == 1) {
                    defaultPicture = picture;
                }
            }
            img.src = defaultPicture != null ? "/uploads/pictures/" + defaultPicture.name : "uploads/pictures/fail.jpg";


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
            deleteLink.href = "/figure/desactivate/" + k.id;
            deleteLink.classList.add("p-1");
            deleteLink.setAttribute("data-delete", "");


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

        links = document.querySelectorAll("[data-delete]"); // on re-séléctionne tous les liens
    });
});