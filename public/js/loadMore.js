
const xhr = new XMLHttpRequest();

const loadButton = document.querySelector("#js-load");
let result = null;

loadButton.addEventListener('click', function (event) {
    const url = this.href;
    let more = document.querySelector("#js-more");
    let div1,div2,img,div3,h5,link,description,button = null;
    event.stopPropagation();
    event.preventDefault();
    xhr.open("POST", url);
    xhr.onreadystatechange = function () {
        if (this.readyState == XMLHttpRequest.DONE && this.status == 200) {
            let response = this.response
            result = JSON.parse(response);
            result.sliceFigures.forEach(function(k) {
                div1 = document.createElement('div');
                div1.classList.add("col-md-6", "col-lg-4");

                div2 = document.createElement('div');
                div2.classList.add("card", "mb-4");

                img = document.createElement('img');
                img.classList.add('card-img-top');
                img.src = 'http://placehold.it/300x200';

                div3 = document.createElement('div');
                div3.classList.add("card-body");

                h5 = document.createElement('h5');
                h5.classList.add("card-title");

                link = document.createElement('a');
                link.classList.add("card-title");


                description = document.createElement('p');
                button = document.createElement('a');
                button.classList.add("btn", "btn-primary", "btn-lg");
                button.href = "http://127.0.0.1:8000/figure/"+k.id;
                button.text = "Voir";
                
                description.classList.add("card-text");
                description.innerHTML = k.description;
                link.innerHTML = k.title;

                div1.appendChild(div2);                
                div2.appendChild(img);
                div2.appendChild(div3);
                h5.appendChild(link);
                div3.appendChild(h5);
                div3.appendChild(description);
                div3.appendChild(button);
                more.appendChild(div1);
            });
        }
    };
    xhr.send();
});