const rightNoLogin = document.getElementById("right-most-no-login");
const rightLogin = document.getElementById("right-most-login");

if (userData === false) {
    //login register
    rightLogin.classList.add("d-none");
}
else {
    //user
    rightNoLogin.classList.add("d-none");
}