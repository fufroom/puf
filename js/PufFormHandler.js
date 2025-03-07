class PufFormHandler {
    constructor(itemType) {
        this.itemType = itemType;
        this.form = document.getElementById("add_edit_item_form");

        if (!this.form) {
            console.error("❌ PufFormHandler: Form with ID 'add_edit_item_form' not found.");
            return;
        }

        this.uploader = new PufUploader("images", "imagePreviewContainer");
        this.setupFormListener();
    }

    setupFormListener() {
        this.form.addEventListener("submit", (event) => this.handleFormSubmit(event));
    }

    setModalTitleAndAction(itemId) {
        const modalTitle = document.getElementById("addEditItemModalLabel");
        if (modalTitle) modalTitle.textContent = itemId ? "Edit Item" : "Add Item";
    
        const actionUrl = itemId ? `/edit-item/${this.itemType}` : `/add-item/${this.itemType}`;
        this.form.setAttribute("action", actionUrl);
    
        // ✅ Ensure the `id` field is cleared when adding a new item
        if (!itemId) {
            const idField = this.form.querySelector("[name='id']");
            if (idField) idField.value = "";
        }
    
        console.log("🔍 Setting form action:", actionUrl);
        console.log("🔗 After setting, form action is:", this.form.getAttribute("action"));
    }
    

    populateForm(item) {
        if (!item) {
            console.error("❌ PufFormHandler: No item data provided to populate form.");
            return;
        }
    
        console.log("📋 Populating form with item data:", item);
    
        this.form.querySelector("#item_id").value = item.id || "";
        this.form.querySelector("#title").value = item.title || "";
        this.form.querySelector("#description").value = item.description || "";
        this.form.querySelector("#link").value = item.link || "";
    
        console.log("🔍 Raw images from item data:", item.images);
        this.uploader.setImages(item.images || []);
    }
    

    resetForm() {
        console.log("🔄 Resetting form.");
        this.form.reset();
        this.uploader.reset();
    }

    handleFormSubmit(event) {
        event.preventDefault();
    
        let itemId = this.form.querySelector("[name='id']").value.trim();
        const actionUrl = itemId ? `/edit-item/${this.itemType}` : `/add-item/${this.itemType}`;
    
        // ✅ Ensure `id` is removed for new items before submission
        if (!itemId) {
            this.form.querySelector("[name='id']").remove();
        }
    
        this.form.setAttribute("action", actionUrl);
        console.log("🔍 Re-confirming form action before submit:", this.form.getAttribute("action"));
    
        if (!this.validateForm()) return;
        this.submitForm();
    }
    

    validateForm() {
        console.log("✅ Validating form fields...");
        const requiredFields = this.form.querySelectorAll("[required]");

        for (let field of requiredFields) {
            if (!field.value.trim()) {
                alert(`⚠️ Please fill in all required fields. Missing: ${field.name}`);
                console.error(`❌ Validation failed - Empty field: ${field.name}`);
                return false;
            }
        }

        console.log("✅ Form validation passed.");
        return true;
    }

    submitForm() {
        const formData = new FormData(this.form);
        const imageOrder = this.uploader.getImageList();
    
        formData.append("image_order", JSON.stringify(imageOrder));
    
        console.log("🚀 Submitting FormData...");
        console.log("   Raw image_order before submission:", imageOrder);
    
        fetch(this.form.getAttribute("action"), { method: "POST", body: formData })
            .then(response => {
                console.log("📡 Received response:", response);
                return response.text();
            })
            .then(text => {
                console.log("📩 Raw response text:", text);
                return JSON.parse(text);  // This is where the error happens
            })
            .then(data => this.handleFormResponse(data))
            .catch(error => console.error("❌ PufFormHandler: Submit error", error));
    }
    

    handleFormResponse(data) {
        console.log("📩 Received response data:", data);
        
        if (data.success) {
            console.log("✅ Form submission successful. Reloading page...");
            document.getElementById("images").value = "";
            setTimeout(() => location.reload(), 500);
        } else {
            console.error("❌ Submission failed:", data.error || "Unknown error");
            alert(`❌ Submission failed: ${data.error || "Unknown error"}`);
        }
    }
}
