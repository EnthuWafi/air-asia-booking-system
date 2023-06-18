function updateModal(formID, modalBtnID) {
    let modalBtn = document.getElementById(modalBtnID);
    modalBtn.setAttribute("form", formID);
}