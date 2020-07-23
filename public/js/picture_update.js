let updatePictureLinks = document.querySelectorAll("[data-update]");


for (updateLink of updatePictureLinks) {
    let oldPictureId = updateLink.parentElement.children[1].textContent
    let figureId = updateLink.parentElement.children[2].textContent;
    let oldPictureOrder = updateLink.parentElement.children[3].textContent;


    updateLink.addEventListener("click", function (e) {
        e.preventDefault();
        e.stopPropagation();

        let form_data = new FormData;
        let file = this.parentElement.firstElementChild.files[0];

        form_data.append("file", file);

        fetch("/figure/" + figureId + "/update/oldPicture/" + oldPictureId + "/oldPictureOrder/" + oldPictureOrder, {
            method: "POST",
            body: form_data,
            contentType: false,
            prrocessData: false
        }).then(
            response => response.json()
        ).then(data => {
            if (this.parentElement.parentElement.parentElement.classList.contains("jumbotron")) {
                this.parentElement.parentElement.parentElement.style["backgroundImage"] =
                    "url('/uploads/pictures/" + data.newPictureFilename;
            } else {
                this.parentElement.parentElement.children[0].src =
                    "/uploads/pictures/" + data.newPictureFilename;
            }


        })
    })
}
