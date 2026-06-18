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

    var table = $("#aboyuma-product-list-table");

    if (table.length) {
      table.DataTable({
        paging: true,
        scrollCollapse: true,
        scrollX: true,
        searching: true,
        autoWidth: false,
        responsive: true,
        columnDefs: columnDefs,
        info: false,
        ordering: true,
      });
    }

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
  });
})(jQuery);
