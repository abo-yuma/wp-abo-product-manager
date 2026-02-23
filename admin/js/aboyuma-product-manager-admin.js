(function ($) {
  "use strict";

  /**
   * All of the code for your admin-facing JavaScript source
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
  jQuery(document).ready(function ($) {
    /**
     * Initialize DataTable safely
     */
    function initDataTable(selector) {
      if ($(selector).length) {
        $(selector).DataTable({
          pageLength: 10,
          searching: true,
        });
      }
    }

    /**
     * Generic delete handler
     */
    function initDeleteHandler(options) {
      const {
        tableSelector,
        buttonClass,
        ajaxObject,
        actionName,
        confirmMessage,
      } = options;

      if (!$(tableSelector).length) return;

      $(tableSelector).on("click", buttonClass, function (e) {
        e.preventDefault();

        if (!confirm(confirmMessage)) return;

        const button = $(this);
        const row = button.closest("tr");
        const id = button.data("id");

        $.post(
          ajaxObject.ajaxurl,
          {
            action: actionName,
            nonce: ajaxObject.nonce,
            id: id,
          },
          function (response) {
            if (response.success) {
              row.fadeOut(200, function () {
                $(this).remove();
              });
            } else {
              alert(response.data || "An error occurred.");
            }
          },
        );
      });
    }

    // Category
    initDataTable("#aboyuma-product-category-table");

    initDeleteHandler({
      tableSelector: "#aboyuma-product-category-table",
      buttonClass: ".delete-category-button",
      ajaxObject: productCategoryDelete,
      actionName: "aboyuma_product_category_delete",
      confirmMessage: "Are you sure you want to delete this category?",
    });

    // Brand
    initDataTable("#aboyuma-product-brand-table");

    initDeleteHandler({
      tableSelector: "#aboyuma-product-brand-table",
      buttonClass: ".delete-brand-button",
      ajaxObject: productBrandDelete,
      actionName: "aboyuma_product_brand_delete",
      confirmMessage: "Are you sure you want to delete this brand?",
    });

    // Product property
    initDataTable("#aboyuma-product-property-table");

    initDeleteHandler({
      tableSelector: "#aboyuma-product-property-table",
      buttonClass: ".delete-property-button",
      ajaxObject: productPropertyDelete,
      actionName: "aboyuma_product_property_delete",
      confirmMessage: "Are you sure you want to delete this property?",
    });
  });
})(jQuery);
