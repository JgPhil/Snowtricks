let updateMediaLinks = document.querySelectorAll("[data-update]");


for (updateLink of updateMediaLinks) {

    let figureId = updateLink.parentElement.children[2].textContent;

    updateLink.addEventListener("click", function (e) {
        e.preventDefault();
        e.stopPropagation();
        let form_data = new FormData;
        let init = null;
        let url = "/figure/" + figureId + "/update/";

        if (this.parentElement.parentElement.classList.contains("videos-container")) { //-----------VIDEO

            let newVideoUrl = this.parentElement.firstElementChild.value;
            let oldVideoId = this.parentElement.children[1].textContent;
            url += "oldVideo/" + oldVideoId;


            fetch(url, {
                method: "post",
                body: newVideoUrl
            }).then(
                response => response.json()
            ).then(data => {
                videoUpdate(data, this);
                document.getElementById("alert").innerHTML = data.message;
            })

        } else { //-----------------------------------------------------------------------PICTURE
            init = {
                method: "POST",
                body: form_data,
                contentType: false,
                prrocessData: false
            }
            let file = this.parentElement.firstElementChild.files[0];
            let oldPictureId = this.parentElement.children[1].textContent;
            let oldPictureOrder = this.parentElement.children[3].textContent;
            url += "oldPicture/" + oldPictureId + "/oldPictureOrder/" + oldPictureOrder;
            form_data.append("file", file);

            fetch(url, init).then(
                response => response.json()
            ).then(data => {
                pictureUpdate(data, this);
                document.getElementById("alert").innerHTML = data.message;
            })
        }
    })
}

const pictureUpdate = function (data, updateLink) {

    if (updateLink.previousElementSibling.textContent == 1) {
        updateLink.parentElement.parentElement.parentElement.style["backgroundImage"] =
            "url('/uploads/pictures/" + data.newPictureFilename;
    } else {
        updateLink.parentElement.parentElement.children[0].src =
            "/uploads/pictures/" + data.newPictureFilename;
    }


}

const videoUpdate = function (data, updateLink) {
    updateLink.parentElement.previousElementSibling.children[0].src = data.newVideoUrl;
}