class PufDataFetcher {
    constructor(itemType, onDataFetched) {
        this.itemType = itemType;
        this.onDataFetched = onDataFetched;
    }
    fetchItemData(itemId) {
        const url = "/get-item/" + this.itemType + "/" + itemId;
        console.log("Fetching item from:", url);
        fetch(url).then(response => response.json()).then(data => {
            console.log("Fetched data:", data);
            if (!data.success || !data[this.itemType]) {
                throw new Error("Failed to fetch " + this.itemType + " " + itemId);
            }
            console.log("Extracting data with key:", this.itemType, "Data:", data[this.itemType]);
            this.onDataFetched(data[this.itemType]);
        }).catch(error => {
            console.error("PufDataFetcher: Fetch error", error);
        });
    }
}