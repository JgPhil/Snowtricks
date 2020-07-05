window.onload = () => {
    // Gestion des liens de suppression
    let links = document.querySelectorAll("[activation]")
    let confirmation = "Voulez-vous vraiment activer l'élément ?";
    let methodR = "PUT";

    //on boucle sur links
    for (link of links) {
        
        //on écoute le click
        link.addEventListener("click", function (e) {
            e.preventDefault();
        //if activation
        if (this.getAttribute("activation") == "true") {
            confirmation ="Voulez-vous vraiment désactiver l'élément ?";
            methodR = "DELETE";
        } 

            //Confirmation ?
            if (confirm(confirmation)) {
                //On envoie une requète Ajax vers le href du lien avec la méthode DELETE
                fetch(this.getAttribute("href"), {
                    method: methodR,
                    headers: {
                        "X-Requested-Width": "XMLHttpRequest",
                        "Content-Type": "application/json"
                    },
                    body: JSON.stringify({ "_token": this.dataset.token })
                }).then(
                    //Récupération de la réponse en JSON
                    response => response.json()
                ).then(data => {
                    if (data.success) {
                        document.location.reload(true);
                    } else {
                        alert(data.error)
                    }
                }).catch(e => alert(e))
            }
        })
    }


}
