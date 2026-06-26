(function ($) {
  "use strict";

  /**
   * All of the code for your public-facing JavaScript source
   * should reside in this file.
   *
   * Note: It has been assumed you will write jQuery code here, so the
   * $ function reference has been prepared for usage within the scope
   * of this function.
   *
   * This enables you to define handlers, for when the DOM is ready:
   *
   * $(function() {
   *
   * });
   *
   * When the window is loaded:
   *
   * $( window ).load(function() {
   *
   * });
   *
   * ...and/or other possibilities.
   *
   * Ideally, it is not considered best practise to attach more than a
   * single DOM-ready or window-load handler for a particular page.
   * Although scripts in the WordPress core, Plugins and Themes may be
   * practising this, we should strive to set a better example in our own work.
   */

  $(function () {
    // Check if DataTables is loaded
    if (typeof $.fn.DataTable === "undefined") {
      console.error("DataTables not loaded");
      return;
    }

    const isDesktop = window.matchMedia("(min-width: 769px)").matches;

    let columnDefs = [];

    if (isDesktop) {
      columnDefs = [
        {
          width: "45%",
          targets: 0,
        },
        {
          width: "55%",
          targets: 1,
        },
      ];
    }

    /**
     * Initialize product DataTable.
     *
     * Used:
     * - Initial page load
     * - After AJAX search refresh
     */
    function initProductDataTable() {
      return $("#aboyuma-product-list-table").DataTable({
        paging: true,
        scrollCollapse: true,
        scrollX: true,
        searching: true,
        autoWidth: false,
        responsive: true,
        columnDefs: columnDefs,
        info: true,
        ordering: true,
        dom: "lrtip",
      });
    }

    let table = null;

    if ($("#aboyuma-product-list-table").length) {
      table = initProductDataTable();
    }

    /**
     * AJAX product search flow.
     *
     * User clicks the "Search" button
     *      ↓
     * JavaScript collects:
     *      - keyword
     *      - parent category
     *      - subcategory
     *      ↓
     * JavaScript sends an AJAX request
     * to WordPress admin-ajax.php
     *      ↓
     * WordPress routes the request
     * using action=aboyuma_search_products
     *      ↓
     * ajax_search_products() is executed [entry point]
     *      ↓
     * Search filters are validated and sanitized
     *      ↓
     * get_products() queries the database
     *      ↓
     * Matching products are passed to
     * template-product-list-rows.php
     *      ↓
     * PHP generates table row HTML
     * using output buffering
     *      ↓
     * HTML is returned as a JSON response
     *      ↓
     * JavaScript receives the response
     *      ↓
     * Existing table rows are replaced
     * with the new search results
     *      ↓
     * DataTable is re-initialized
     * without reloading the page
     */
    $("#aboyuma-product-search-button").on("click", function () {
      const keyword = $("#aboyuma-product-search-keyword").val();
      const parentCategoryId = $("#parent_category").val();
      const subcategoryId = $("#product_subcategory").val();

      $.ajax({
        url: productSearch.ajaxurl,
        type: "POST",
        data: {
          action: "aboyuma_search_products",
          nonce: productSearch.nonce,
          keyword: keyword,
          parent_category_id: parentCategoryId,
          subcategory_id: subcategoryId,
        },
      }).done(function (response) {
        table.destroy();

        $("#aboyuma-product-list-table tbody").html(response.data.html);

        $("#aboyuma-product-search-message").html(`
          <div class="alert alert-success alert-dismissible fade show mb-2" role="alert">
              พบสินค้า ${response.data.count} รายการ
              <button
                  type="button"
                  class="btn-close"
                  data-bs-dismiss="alert"
                  aria-label="Close">
              </button>
          </div>
        `);

        table = initProductDataTable();
      });
    });

    // Product image simple lightbox gallery
    var productImageLightbox = new SimpleLightbox(".product-lightbox", {
      captions: false,
      showCounter: true,
      navText: ["‹", "›"],
    });

    // Fancybox videos
    Fancybox.bind("[data-fancybox]", {
      dragToClose: false,
    });

    $("#parent_category").on("change", function () {
      const parentId = $(this).val();

      $("#product_subcategory").val("");

      $("#product_subcategory option").hide();
      $("#product_subcategory option:first").show();

      if (!parentId) {
        return;
      }

      $(
        '#product_subcategory option[data-parent-id="' + parentId + '"]',
      ).show();
    });
  });
})(jQuery);
