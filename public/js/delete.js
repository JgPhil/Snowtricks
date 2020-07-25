// Gestion des liens de suppression
let links = document.querySelectorAll("[data-delete]");

//on boucle sur links
for (link of links) {

    let videoId = link.href.split('/').pop();

    //on écoute le click
    link.addEventListener("click", function (e) {
        e.preventDefault();
        e.stopPropagation();
        //Confirmation ?
        if (confirm("Voulez-vous vraiment supprimer l'élément ?")) {
            //On envoie une requète Ajax vers le href du lien avec la méthode DELETE
            fetch(this.getAttribute("href"), {
                method: "DELETE",
                headers: {
                    "X-Requested-Width": "XMLHttpRequest",
                    "Content-Type": "application/json"
                }
            }).then(
                response => response.json()
            ).then(data => {
                if (data.success && this.parentElement.firstElementChild.tagName == "IFRAME") {
                    //on supprime la balise <li> correspondante
                    document.querySelector("#liDelete-" + videoId).parentElement.remove();
                    this.parentElement.remove();
                } else if (data.success) {
                    this.parentElement.remove();
                } else {
                    alert(data.error)
                }
            }).catch(e => alert(e))
        }
    })
}




$('.custom-file input').change(function (e) {
    let files = [];
    for (var i = 0; i < $(this)[0].files.length; i++) {
        files.push($(this)[0].files[i].name);
    }
    $(this).next('.custom-file-label').html(files.join(', '));
});

