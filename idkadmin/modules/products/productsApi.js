export const fetchProducts = async () => {
  try {
    const response = await fetch("modules/products/api.php?action=fetch");
    if (!response.ok) throw new Error("Failed to fetch products");
    return await response.json();
  } catch (error) {
    console.error("Error fetching products:", error);
    return [];
  }
};

export const syncProducts = async () => {
  try {
    const response = await fetch("modules/products/api.php?action=sync");
    if (!response.ok) throw new Error("Failed to sync products");
    return await response.json();
  } catch (error) {
    console.error("Error syncing products:", error);
    return { success: false };
  }
};
