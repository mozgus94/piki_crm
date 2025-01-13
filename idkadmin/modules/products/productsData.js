import { fetchProducts } from "./productsApi.js";

export const fetchAndRenderProducts = async () => {
  const products = await fetchProducts();
  const tableBody = document.querySelector("#idkProductsTable tbody");

  // Render table rows
  tableBody.innerHTML = products
    .map(
      (product) => `
      <tr>
        <td><img src="/crm/idkadmin/files/products/images/${
          product.product_image
        }" alt="${
        product.product_name
      }" style="width: 50px; height: 50px; border-radius: 50%;"></td>
        <td>${product.product_sku}</td>
        <td>${product.product_name}</td>
        <td>${Number(product.mpc_price).toFixed(2)} KM</td>
        <td>${Number(product.product_price).toFixed(2)} KM</td>
        <td>
          <div class="dropdown">
            <button class="btn btn-secondary dropdown-toggle" type="button" data-toggle="dropdown">Akcije</button>
            <ul class="dropdown-menu">
              <li><a href="/edit/${product.product_id}">Uredi</a></li>
              <li><a href="/archive/${product.product_id}">Arhiviraj</a></li>
            </ul>
          </div>
        </td>
      </tr>
    `
    )
    .join("");

  if ($.fn.DataTable.isDataTable("#idkProductsTable")) {
    $("#idkProductsTable").DataTable().destroy();
  }
  $("#idkProductsTable").DataTable({
    paging: true,
    searching: true,
    ordering: true,
    pageLength: 10, // Number of rows per page
    columnDefs: [
      { orderable: false, targets: 0 }, // Disable ordering for the image column
    ],
    language: {
      url: "//cdn.datatables.net/plug-ins/1.13.6/i18n/Bosnian.json", // For Bosnian language support
    },
  });
};

// Attach to window for global access
window.fetchAndRenderProducts = fetchAndRenderProducts;
