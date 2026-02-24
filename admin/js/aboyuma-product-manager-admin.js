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

    // CATEGORY AJAX LOAD
    $("#parent_category").on("change", function () {
      var parentId = $(this).val();
      var $subSelect = $("#product_subcategory");
      $subSelect.html('<option value="">Loading...</option>');

      $.ajax({
        url: ajaxurl,
        method: "GET",
        data: {
          action: "get_subcategories",
          parent_id: parentId,
        },
        dataType: "json",
        success: function (data) {
          var options = '<option value="">-- Select Subcategory --</option>';

          if (data.length > 0) {
            $.each(data, function (index, subcat) {
              options += `<option value="${subcat.id}">${subcat.category_name}</option>`;
            });
          } else {
            options = '<option value="">-- No Subcategories Found --</option>';
          }

          $subSelect.html(options);
        },
        error: function () {
          $subSelect.html(
            '<option value="">Failed to load subcategories</option>',
          );
        },
      });
    });

    /**
     * MAIN IMAGE
     */
    let mainFrame;

    $("#select-main-image").on("click", function (e) {
      e.preventDefault();

      if (mainFrame) {
        mainFrame.open();
        return;
      }

      mainFrame = wp.media({
        title: "Select Main Image",
        button: { text: "Use this image" },
        multiple: false,
      });

      mainFrame.on("select", function () {
        const attachment = mainFrame.state().get("selection").first().toJSON();

        const imageUrl =
          attachment.sizes && attachment.sizes.medium
            ? attachment.sizes.medium.url
            : attachment.url;

        $("#main_image_id").val(attachment.id);
        $("#main-image-preview").attr("src", imageUrl);
        $("#main-image-wrapper").show();
      });

      mainFrame.open();
    });

    /**
     * GALLERY
     */
    let galleryFrame;

    $("#add-gallery-images").on("click", function (e) {
      e.preventDefault();

      if (galleryFrame) {
        galleryFrame.open();
        return;
      }

      galleryFrame = wp.media({
        title: "Select Gallery Images",
        button: { text: "Add images" },
        multiple: true,
      });

      galleryFrame.on("select", function () {
        const attachments = galleryFrame.state().get("selection").toJSON();

        attachments.forEach(function (attachment) {
          if (
            $('#gallery-container input[value="' + attachment.id + '"]').length
          ) {
            return;
          }

          const imageUrl =
            attachment.sizes && attachment.sizes.thumbnail
              ? attachment.sizes.thumbnail.url
              : attachment.url;

          const html = `
            <div class="gallery-item">
              <span class="remove-image">&times;</span>
              <img src="${imageUrl}">
              <input type="hidden" name="gallery_image_ids[]" value="${attachment.id}">
            </div>
          `;

          $("#gallery-container").append(html);
        });
      });

      galleryFrame.open();
    });

    /**
     * REMOVE
     */
    $(document).on("click", ".remove-image", function () {
      $(this).closest(".gallery-item").remove();
    });

    /**
     * SORTABLE
     */
    $("#gallery-container").sortable({
      items: ".gallery-item",
      cursor: "move",
      opacity: 0.8,
    });
  });
})(jQuery);
