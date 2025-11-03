document.addEventListener('DOMContentLoaded', loadDashboardData);

async function fetchFromAPI(endpoint) {
    try {
        const response = await fetch(endpoint);
        if (!response.ok) {
            throw new Error(`API error: ${response.status} ${response.statusText}`);
        }
        return await response.json();
    } catch (error) {
        console.error('Fetch error:', error);
        throw error;
    }
}

async function loadDashboardData() {
    const container = document.getElementById('productsContainer');
    try {
        const data = await fetchFromAPI('dashboard_api.php');
        console.log("Dashboard data:", data);
        loadProducts(data);
    } catch (error) {
        console.error("Error loading dashboard data:", error);
        container.innerHTML = '<p class="error">Error: Could not load your products. Please try refreshing the page.</p>';
    }
}

/**
 * Renders the list of products as an HTML table
 * @param {Array} products - An array of product objects
 */
function loadProducts(products) {
    const container = document.getElementById('productsContainer');
    container.innerHTML = '';

    if (!products || products.length === 0) {
        container.innerHTML = '<p>You have not added any products yet. <a href="add_product.php">Add one now!</a></p>';
        return;
    }

    const table = document.createElement('table');
    table.className = 'product-table';
    table.innerHTML = `
        <thead>
            <tr>
                <th>Product Name</th>
                <th>Description</th>
                <th>Price</th>
                <th>Link</th>
            </tr>
        </thead>
    `;
    const tbody = document.createElement('tbody');
    products.forEach(product => {
        const tr = document.createElement('tr');
        const price = parseFloat(product.price).toFixed(2);
        tr.innerHTML = `
            <td>${product.product_name}</td>
            <td>${product.description}</td>
            <td>$${price}</td>
            <td><a href="${product.linked_url}" target="_blank" rel="noopener noreferrer">View Product</a></td>
        `;
        tbody.appendChild(tr);
    });

    table.appendChild(tbody);
    container.appendChild(table);
    return;
}
