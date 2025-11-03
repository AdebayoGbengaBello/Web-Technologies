async function fetchFromAPI(endpoint) {
    try {
        const response = await fetch(endpoint);
        if (!response.ok) {
            throw new Error(`API error: ${response.status}`);
        }
        return await response.json();
    } catch (error) {
        console.error('Fetch error:', error);
        throw error;
    }
}

async function loadDashboardData() {
    try {
        const data = await fetchFromAPI('dashboard_api.php');
        console.log("Dashboard data:", data);
        loadProducts(data);
    } catch (error) {
        console.error("Error loading dashboard data:", error);
    }
}

function loadProducts(products){
    const productList = document.getElementById('productsContainer');
    productList.innerHTML = '';
    products.forEach(product => {
        const listItem = document.createElement('li');
        listItem.textContent = product.product_name;
        productList.appendChild(listItem);
    });
}
document.addEventListener('DOMContentLoaded', loadDashboardData);
loadDashboardData();