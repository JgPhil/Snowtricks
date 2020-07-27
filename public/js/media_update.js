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

            newVideoUrlChecked = checkVideoUrl(newVideoUrl);

            if (newVideoUrlChecked !== null) {
                fetch(url, {
                    method: "post",
                    body: newVideoUrlChecked
                }).then(
                    response => response.json()
                ).then(data => {
                    videoUpdate(data, this);
                    document.getElementById("alert").innerHTML = data.message;
                })
            } else alert("Cette adrersse URL n'est pas valide");



        } else { //-----------------------------------------------------------------------PICTURE
            init = {
                method: "POST",
                body: form_data,
                contentType: false,
                prrocessData: false
            }
            let file = this.parentElement.firstElementChild.files[0];
            if (this.parentElement.children[1].textContent != "") {
                let oldPictureId = this.parentElement.children[1].textContent;
                let oldPictureOrder = this.parentElement.children[3].textContent;
                url += "oldPicture/" + oldPictureId + "/oldPictureOrder/" + oldPictureOrder;

            } else {
                url += "oldPicture/" + null + "/oldPictureOrder/" + null;
            }

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

    if (updateLink.previousElementSibling.textContent == 1) { //jumbotron default picture
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


const checkVideoUrl = function (newVideoUrl) {
    // Decompose Url and check
    let splitUrl = newVideoUrl.split('/')
    let videoServiceProvider = splitUrl[2];
    let videoId = null;

    console.log(videoServiceProvider);

    if (videoServiceProvider === "www.youtube.com") {
        videoId = newVideoUrl.split('=').pop();
        newVideoUrl = "https://www.youtube.com/embed/" + videoId;
    } else if (videoServiceProvider === "www.dailymotion.com") {
        videoId = splitUrl.pop();
        newVideoUrl = "https://www.dailymotion.com/embed/video/" + videoId;
    } else {
        newVideoUrl = null;
    }
    return newVideoUrl;
}


// File Input form filename
$('.custom-file-input').change(function (e) {
    let files = [];
    for (var i = 0; i < $(this)[0].files.length; i++) {
        files.push($(this)[0].files[i].name);
    }
    $(this).next('.custom-file-label').html(files.join(', '));
});