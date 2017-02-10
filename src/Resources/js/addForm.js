var btnsResponse = document.querySelectorAll("a.response");
console.log(btnsResponse);
btnsResponse.forEach(function(btn){

    btn.addEventListener("click",function (e) {
            var liElt = btn.parentNode.parentNode.parentNode;
            var id = this.getAttribute('id');
            console.log(id);
            var form = document.createElement('form');
            addForm(id, form);
            console.log(e.target);
            liElt.appendChild(form);
            e.target.parentNode.removeChild(e.target);
            e.preventDefault();
        }
    )
})
// fonction génerant le formulaire
function addForm(id, form){
    form.setAttribute("method", "post");
    form.setAttribute("action", "/response/".id);
    form.classList.add("form-horizontal");
    form.classList.add("well");
    var hElt = document.createElement("h2");
    hElt.classList.add("text-center");
    hElt.innerHTML = "Répondre ";
    var divGroup = document.createElement("div");
    divGroup.classList.add("form-group");

    var textLabel = document.createElement("label");
    textLabel.setAttribute("for", "response");
    textLabel.classList.add("control-label");
    textLabel.classList.add("col-sm-3");
    textLabel.innerHTML = "Commentaire :";

    var divElt = document.createElement("div");
    divElt.classList.add("col-sm-5");

    var text = document.createElement("textarea");
    text.setAttribute("name", "response");
    text.setAttribute("id", "response");
    text.setAttribute("rows", "10");
    text.setAttribute("cols", "50");

    var divBtn = document.createElement("div");
    divBtn.classList.add("text-center");

    var button = document.createElement("input");
    button.setAttribute("type", "submit");
    button.setAttribute("value", "Répondre");
    button.classList.add("btn");
    button.classList.add("btn-xs");
    button.classList.add("btn-info");

    divElt.appendChild(text);

    divGroup.appendChild(textLabel);
    divGroup.appendChild(divElt);

    divBtn.appendChild(button);

    form.appendChild(divGroup);
    form.appendChild(divBtn);

    return form;

}