document.addEventListener("DOMContentLoaded", function () {
  if (document.body.classList.contains("home-body")) {
    document.body.classList.add("home-motion-ready");
  }

  const cards = document.querySelectorAll(".article-card, .quick-card");

  if (!("IntersectionObserver" in window)) {
    cards.forEach(function (card) {
      card.classList.add("is-visible");
    });
    return;
  }

  const observer = new IntersectionObserver(
    function (entries) {
      entries.forEach(function (entry) {
        if (entry.isIntersecting) {
          entry.target.classList.add("is-visible");
          observer.unobserve(entry.target);
        }
      });
    },
    {
      threshold: 0.1,
    },
  );

  cards.forEach(function (card) {
    card.classList.add("reveal");
    observer.observe(card);
  });
});


/* =================================
   SEHATIN - ARTICLES PAGE
================================= */

document.addEventListener("DOMContentLoaded", function () {

    const articleGrid = document.getElementById("articleGrid");

    // Jalankan hanya pada halaman artikel
    if (!articleGrid) return;

    const articles = Array.from(
        articleGrid.querySelectorAll(".searchable-article")
    );

    const searchInput = document.getElementById("searchArticle");
    const searchButton = document.getElementById("searchButton");
    const filterButtons = document.querySelectorAll(".filter-btn");
    const pagination = document.getElementById("pagination");
    const emptyMessage = document.getElementById("emptyMessage");

    const articlesPerPage = 3;

    let activeCategory = "Semua artikel";
    let searchKeyword = "";
    let currentPage = 1;

    function getFilteredArticles() {

        return articles.filter(function (article) {

            const category = article.dataset.category;
            const title = article.dataset.title.toLowerCase();
            const description = article.dataset.description.toLowerCase();

            const matchesCategory =
                activeCategory === "Semua artikel" ||
                category === activeCategory;

            const matchesSearch =
                title.includes(searchKeyword) ||
                description.includes(searchKeyword) ||
                category.toLowerCase().includes(searchKeyword);

            return matchesCategory && matchesSearch;

        });

    }

    function renderArticles() {

        const filteredArticles = getFilteredArticles();

        const totalPages = Math.ceil(
            filteredArticles.length / articlesPerPage
        );

        if (totalPages > 0 && currentPage > totalPages) {
            currentPage = totalPages;
        }

        const start = (currentPage - 1) * articlesPerPage;

        const visibleArticles = filteredArticles.slice(
            start,
            start + articlesPerPage
        );

        articles.forEach(function (article) {
            article.style.display = "none";
        });

        visibleArticles.forEach(function (article) {
            article.style.display = "";
        });

        emptyMessage.hidden = filteredArticles.length > 0;

        renderPagination(totalPages);
    }

    function renderPagination(totalPages) {

        pagination.innerHTML = "";

        if (totalPages <= 1) return;

        // Tombol sebelumnya
        const previous = createPageButton(
            "Sebelumnya",
            currentPage - 1,
            "nav-btn"
        );

        previous.disabled = currentPage === 1;

        pagination.appendChild(previous);

        // Nomor halaman
        for (let i = 1; i <= totalPages; i++) {

            const button = createPageButton(
                i,
                i,
                currentPage === i ? "active" : ""
            );

            pagination.appendChild(button);

        }

        // Tombol berikutnya
        const next = createPageButton(
            "Berikutnya",
            currentPage + 1,
            "nav-btn"
        );

        next.disabled = currentPage === totalPages;

        pagination.appendChild(next);
    }

    function createPageButton(label, page, className) {

        const button = document.createElement("button");

        button.type = "button";
        button.className = "page-btn " + className;
        button.textContent = label;

        button.addEventListener("click", function () {

            currentPage = page;

            renderArticles();

            articleGrid.scrollIntoView({
                behavior: "smooth",
                block: "start"
            });

        });

        return button;
    }

    // Filter kategori
    filterButtons.forEach(function (button) {

        button.addEventListener("click", function () {

            filterButtons.forEach(function (item) {
                item.classList.remove("active");
            });

            button.classList.add("active");

            activeCategory = button.dataset.category;

            currentPage = 1;

            renderArticles();

        });

    });

    // Fungsi pencarian
    function searchArticles() {

        searchKeyword = searchInput.value
            .trim()
            .toLowerCase();

        currentPage = 1;

        renderArticles();
    }

    searchButton.addEventListener("click", searchArticles);

    searchInput.addEventListener("keydown", function (event) {

        if (event.key === "Enter") {
            searchArticles();
        }

    });

    renderArticles();

});


/* =================================
   SEHATIN - FACILITIES PAGE
================================= */

document.addEventListener("DOMContentLoaded", function () {

    const facilityGrid = document.getElementById("facilityGrid");

    // Jalankan hanya pada halaman fasilitas
    if (!facilityGrid) return;

    const facilities = Array.from(
        facilityGrid.querySelectorAll(".facility-card")
    );

    const searchInput = document.getElementById("searchFacility");
    const searchButton = document.getElementById("searchFacilityButton");
    const filterButtons = document.querySelectorAll(".facility-filter");
    const emptyMessage = document.getElementById("facilityEmptyMessage");

    let activeType = "Semua jenis";
    let keyword = "";

    function filterFacilities() {

        let visibleCount = 0;

        facilities.forEach(function (facility) {

            const type = facility.dataset.type;
            const name = facility.dataset.name.toLowerCase();
            const address = facility.dataset.address.toLowerCase();

            const matchesType =
                activeType === "Semua jenis" ||
                type === activeType;

            const matchesSearch =
                name.includes(keyword) ||
                address.includes(keyword) ||
                type.toLowerCase().includes(keyword);

            const isVisible = matchesType && matchesSearch;

            facility.style.display = isVisible ? "" : "none";

            if (isVisible) {
                visibleCount++;
            }

        });

        emptyMessage.hidden = visibleCount > 0;
    }

    // Filter kategori
    filterButtons.forEach(function (button) {

        button.addEventListener("click", function () {

            filterButtons.forEach(function (item) {
                item.classList.remove("active");
            });

            button.classList.add("active");

            activeType = button.dataset.type;

            filterFacilities();

        });

    });

    // Pencarian fasilitas
    function searchFacilities() {

        keyword = searchInput.value.trim().toLowerCase();

        filterFacilities();

    }

    searchButton.addEventListener("click", searchFacilities);

    searchInput.addEventListener("input", searchFacilities);

});
