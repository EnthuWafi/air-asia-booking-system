$(document).ready(function () {
    // Validate Confirm Password
    $("#conpasscheck").hide();
    let confirmPasswordError = true;
    $("#confirm-password").keyup(function () {
        validateConfirmPassword();
    });
    function validateConfirmPassword() {
        let confirmPasswordValue = $("#confirm-password").val();
        let passwordValue = $("#password").val();
        if (passwordValue != confirmPasswordValue) {
            $("#conpasscheck").show();
            $("#conpasscheck").html("**Password didn't Match");
            $("#conpasscheck").css("color", "red");
            confirmPasswordError = false;
            return false;
        } else {
            $("#conpasscheck").hide();
        }
    }

});