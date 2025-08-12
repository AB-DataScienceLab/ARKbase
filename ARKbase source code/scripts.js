function formValidation(form) {
    if (form.key1.value.trim() === "") {
        alert("Please enter a keyword!");
        return false;
    }
    return true;
}