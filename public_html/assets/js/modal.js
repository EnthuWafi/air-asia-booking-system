function updateModal(formID, modalBtnID) {
    let modalBtn = document.getElementById(modalBtnID);
    modalBtn.setAttribute("form", formID);
}

function updateElement(ID, formID, inputHidden) {
    let form = document.getElementById(formID);
    let input = form.querySelector(`[name="${inputHidden}"]`);
    input.value = ID;
}