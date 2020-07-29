
more.addEventListener("click", function (e) { // more = figures cards container

    if (
        e.target && e.target.parentElement.matches('a')
        && e.target.parentElement.href.split('/')[3] === "figure"
        && e.target.parentElement.href.split('/')[4] === "desactivate"
    ) {
        e.preventDefault();
        e.stopPropagation();
        link = e.target.parentElement;

        //Confirmation ?
        if (confirm("Voulez-vous vraiment supprimer l'élément ?")) {
            //On envoie une requète Ajax vers le href du lien avec la méthode DELETE
            fetch(link.getAttribute("href"), {
                method: "DELETE",
                headers: {
                    "X-Requested-Width": "XMLHttpRequest",
                    "Content-Type": "application/json"
                }
            }).then(
                response => response.json()
            ).then(data => {
                if (data.success){
                    link.parentElement.parentElement.parentElement.parentElement.parentElement.remove();
                }
                else {
                    alert(data.error)
                }
            }).catch(e => alert(e))
        }
    }
})

