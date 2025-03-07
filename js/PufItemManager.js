class PufItemManager {
    constructor(itemType) {
        this.itemType = itemType;
        this.selectedItem = {
            images: Array(10).fill(null),
            newFiles: []
        };
        this.isDeleting = false;
        this.formHandler = new PufFormHandler(itemType);
        this.modalManager = new PufModalManager("addEditItemModal");
        this.dataFetcher = new PufDataFetcher(itemType, this.handleItemData.bind(this));
        this.init();
    }

    init() {
        document.body.addEventListener("click", (event) => this.handleButtonClick(event));
        this.formHandler.form.addEventListener("submit", (event) => this.formHandler.handleFormSubmit(event));
    }

    handleButtonClick(event) {
        if (event.target.classList.contains("edit-item-button") || event.target.id === "add_item_button") {
            this.openModal(event.target.dataset.itemId || null);
        }

        if (event.target.classList.contains("delete-item-button")) {
            this.confirmDelete(event.target.dataset.itemId || null);
        }
    }

    openModal(itemId) {
        this.formHandler.setModalTitleAndAction(itemId);
        itemId ? this.dataFetcher.fetchItemData(itemId) : this.formHandler.resetForm();
        this.modalManager.open();
    }

    handleItemData(item) {
        if (!item) {
            alert("Error fetching item data.");
            return;
        }

        // Ensure images are always an array
        item.images = item.images && Array.isArray(item.images) ? item.images : [];

        this.selectedItem = item;
        this.formHandler.populateForm(item);
    }

    confirmDelete(itemId) {
        if (!itemId) {
            console.error("❌ Missing item ID for deletion.");
            return;
        }

        if (!confirm(`Are you sure you want to delete this ${this.itemType}?`)) return;

        this.deleteItem(itemId);
    }

    deleteItem(itemId) {
        console.log(`🗑️ Preparing to delete ${this.itemType} ID: ${itemId}`);

        fetch(`/delete-item`, {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ type: this.itemType, id: itemId }),
        })
            .then((response) => {
                if (!response.ok) {
                    throw new Error(`HTTP error! Status: ${response.status}`);
                }
                return response.json();
            })
            .then((data) => {
                if (data.success) {
                    console.log(`✅ ${this.itemType} deleted successfully.`);
                    document.querySelector(`[data-id='${itemId}']`)?.remove();
                } else {
                    console.error(`❌ Error deleting ${this.itemType}: ${data.message}`);
                }
            })
            .catch((error) => {
                console.error("❌ Fetch error:", error);
            });
    }
}
