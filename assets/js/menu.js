document.addEventListener("DOMContentLoaded", () => {
  const menuNav = document.querySelector(".menu-navigation");
  const menuItems = document.querySelectorAll(".menu-item");
  const searchInput = document.getElementById("menu-search");

  if (!menuNav || menuItems.length === 0) return;

  const categories = [
    ...new Set([...menuItems].map((item) => item.dataset.category)),
  ];

  categories.forEach((cat) => {
    const button = document.createElement("button");
    button.classList.add("menu-nav-btn");
    button.textContent = cat;
    button.setAttribute("data-category", cat);

    button.addEventListener("click", () => {
      const targetItem = [...menuItems].find(
        (item) => item.dataset.category === cat
      );
      if (targetItem) targetItem.scrollIntoView({ behavior: "smooth" });
    });

    menuNav.appendChild(button);
  });

  if (searchInput) {
    searchInput.addEventListener("input", () => {
      const query = searchInput.value.toLowerCase();

      menuItems.forEach((item) => {
        const name = item.querySelector(".item-name").textContent.toLowerCase();
        const category = item.dataset.category.toLowerCase();

        if (name.includes(query) || category.includes(query)) {
          item.style.display = "block";
        } else {
          item.style.display = "none";
        }
      });
    });
  }
});
