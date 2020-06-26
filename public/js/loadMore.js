
const request = new XMLHttpRequest();

const loadButton = document.querySelector("#js-load");
loadButton.addEventListener('click', function (event) {
    event.preventDefault();
    event.stopPropagation();
    const url = this.href;    
    request.onreadystatechange = function () {
        if (this.readyState == XMLHttpRequest.DONE && this.status == 200) {
            const response = this.response;
            console.log(response);
        }
    };
    request.open("POST", url);
    request.send();

});
