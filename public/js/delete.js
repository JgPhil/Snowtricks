
window.onload = () => {
    // Gestion des liens de suppression
    let links = document.querySelectorAll("[data-delete]")

    //on boucle sur links
    for (link of links) {
        //on écoute le click
        link.addEventListener("click", function (e) {
            e.preventDefault();
            //Confirmation ?
            if (confirm("Voulez-vous vraiment supprimer l'élément ?")) {
                //On envoie une requète Ajax vers le href du lien avec la méthode DELETE
                fetch(this.getAttribute("href"), {
                    method: "DELETE",
                    headers: {
                        "X-Requested-Width": "XMLHttpRequest",
                        "Content-Type": "application/json"
                    },
                    body: JSON.stringify({ "_token": this.dataset.token })
                }).then(
                    //Récupération de la réponse en JSON
                    response => response.json()
                ).then(data => {
                    if (data.success && this.parentElement.classList.contains("img-thumbnail")) {
                        //on supprime la div parent
                        this.parentElement.remove();
                    } else if (data.success) {
                        this.parentElement.parentElement.parentElement.remove();
                        document.location.reload(false);
                    } else {
                        alert(data.error)
                    }
                }).catch(e => alert(e))
            }
        })
    }


}


$('.custom-file input').change(function (e) {
    let files = [];
    for (var i = 0; i < $(this)[0].files.length; i++) {
        files.push($(this)[0].files[i].name);
    }
    $(this).next('.custom-file-label').html(files.join(', '));
});

