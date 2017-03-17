$(document).ready(function () {
    // appelle de la fonction deleteAlert au bout de 3 secondes
    setTimeout(deleteAlert, 3000);

});
function deleteAlert() {
    // Récuperation de toutes les balises avec la class alert
    var alertElts = $(".alert");
    // on supprime les balises
    alertElts.remove();
}
