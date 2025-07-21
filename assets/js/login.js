let loginEmail = false;
let loginPass = false;

document.querySelector("#email").addEventListener("input", checkLoginEmail);
document.querySelector("#password").addEventListener("input", checkLoginPass);

function checkLoginEmail() {
    const input = this;
    const regex = /\S+@\S+\.\S+/;
    loginEmail = regex.test(input.value);

    if (loginEmail) {
        input.classList.add("is-valid");
        input.classList.remove("is-invalid");
    } else {
        input.classList.remove("is-valid");
        input.classList.add("is-invalid");
    }

    checkLoginReady();
}

function checkLoginPass() {
    const input = this;
    loginPass = input.value.length > 12;

    if (loginPass) {
        input.classList.add("is-valid");
        input.classList.remove("is-invalid");
    } else {
        input.classList.remove("is-valid");
        input.classList.add("is-invalid");
    }

    checkLoginReady();
}

function checkLoginReady() {
    const btn = document.querySelector("#login-button");
    if (loginEmail && loginPass) {
        btn.removeAttribute("disabled");
    } else {
        btn.setAttribute("disabled", "disabled");
    }
}
