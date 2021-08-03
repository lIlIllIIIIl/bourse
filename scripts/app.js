const page = document.querySelector("body")
const list = page.querySelector(".actual_actions")
const shButton = page.querySelector(".shButton")
const a = page.querySelectorAll(".colorCh")
console.log(a);


function showActions(){
    if (shButton.classList[1] == "show"){
        list.style.display = "block"
        shButton.textContent = "Cacher mes actions"
        shButton.classList.replace("show", "hide")
    } else {
        list.style.display = "none"
        shButton.textContent = "Afficher mes actions"
        shButton.classList.replace("hide", "show")
    }
}

for (i in a){
    if (a[i].textContent < 0){
        a[i].style.background = "red";
    } else {
        a[i].style.background = "green";
    }
}