
const request = new XMLHttpRequest();
let result = null;
const loadButton = document.querySelector("#js-load");

loadButton.addEventListener('click', function (event) {
    const url = this.href;
    const html = '<div class="col-md-6 col-lg-4"><div class="card mb-4 "><img class="card-img-top" src="" alt="" id="js-figPic"><div class="card-body"><h5 class="card-title" id="js-figTitle"><a href=""></a></h5><p class="card-text" id="js-figText"></p><a href="{{ path("trick_show", { id: figure.id } ) }}" class="btn btn-primary btn-lg">Voir</a></div></div> </div>';
    const more = document.querySelector("#js-more");
    event.preventDefault();
    event.stopPropagation();
    request.open("POST", url);
    request.onreadystatechange = function () {
        if (this.readyState == XMLHttpRequest.DONE && this.status == 200) {
            const response = this.response;
            result = JSON.parse(response);
            //transformation JSON => array
            result.sliceFigures.forEach(
                more.innerHTML = html);
        }
    };
    request.send();
});
