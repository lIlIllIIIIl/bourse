const page = document.querySelector("body")
// 
const list = page.querySelector(".actual_actions")
const shButton = page.querySelector(".shButton")
// 
const listSell = page.querySelector(".actual_actions_sell")
const shButtonSell = page.querySelector(".shButtonSell")
// 
const a = page.querySelectorAll(".colorCh")
//
const droite = page.querySelector(".droite")
const gauche = page.querySelector(".gauche")
const tools = droite.querySelector(".buttonTools")


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

function showActionsSell(){
    if (shButtonSell.classList[1] == "show"){
        listSell.style.display = "block"
        shButtonSell.textContent = "Cacher mes actions vendues"
        shButtonSell.classList.replace("show", "hide")
    } else {
        listSell.style.display = "none"
        shButtonSell.textContent = "Afficher mes actions vendues"
        shButtonSell.classList.replace("hide", "show")
    }
}

for (i in a){
    // console.log(a[i].textContent);
    if (a[i].textContent){
        if (a[i].textContent.includes('-')){
            a[i].style.background = "red";
        } else {
            a[i].style.background = "green";
        }
    }
}

function showTools(){
    if (tools.classList[1] == "unactive"){
        tools.classList.replace("unactive", "active")
        gauche.style.display="none"
    } else if(tools.classList[1] == "active"){
        tools.classList.replace("active", "unactive")
        gauche.style.display="block"
    }
}