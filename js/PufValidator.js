class PufValidator {
    static validateForm(form) {
        const requiredFields = form.querySelectorAll("[required]");
        for (let field of requiredFields) {
            if (!field.value.trim()) {
                alert("Please fill in all required fields.");
                return false;
            }
        }
        return true;
    }
}