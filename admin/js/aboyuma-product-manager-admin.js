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
    "use strict";

    /*
     * TABLES
     */
    const AdminTables = {
      init(selector) {
        if ($(selector).length) {
          $(selector).DataTable({
            pageLength: 10,
            searching: true,
          });
        }
      },
    };

    /*
     * DELETE HANDLER
     */
    const DeleteHandler = {
      init(options) {
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

          const userInput = prompt(
            confirmMessage + "\n\nType DELETE to confirm.",
          );

          if (userInput !== "DELETE") {
            alert("Deletion cancelled.");
            return;
          }

          const row = $(this).closest("tr");
          const id = $(this).data("id");

          $.post(ajaxObject.ajaxurl, {
            action: actionName,
            nonce: ajaxObject.nonce,
            id: id,
          }).done(function (response) {
            if (response.success) {
              row.fadeOut(200, function () {
                $(this).remove();
              });
            } else {
              alert(response.data || "An error occurred.");
            }
          });
        });
      },
    };

    /*
     * CATEGORY AJAX LOAD
     */
    const CategoryLoader = {
      init() {
        $("#parent_category").on("change", function () {
          const parentId = $(this).val();
          const $subSelect = $("#product_subcategory");

          $subSelect.html('<option value="">Loading...</option>');

          $.ajax({
            url: ajaxurl,
            method: "GET",
            data: {
              action: "get_subcategories",
              parent_id: parentId,
            },
            dataType: "json",
          })
            .done(function (data) {
              let options =
                '<option value="">-- Select Subcategory --</option>';

              if (data.length > 0) {
                $.each(data, function (index, subcat) {
                  options += `<option value="${subcat.id}">${subcat.category_name}</option>`;
                });
              } else {
                options =
                  '<option value="">-- No Subcategories Found --</option>';
              }

              $subSelect.html(options);
            })
            .fail(function () {
              $subSelect.html(
                '<option value="">Failed to load subcategories</option>',
              );
            });
        });
      },
    };

    /*
     * MEDIA MANAGER
     */
    const MediaManager = {
      mainFrame: null,
      galleryFrame: null,

      initMainImage() {
        $("#select-main-image").on("click", (e) => {
          e.preventDefault();

          if (this.mainFrame) {
            this.mainFrame.open();
            return;
          }

          this.mainFrame = wp.media({
            title: "Select Main Image",
            button: { text: "Use this image" },
            multiple: false,
          });

          this.mainFrame.on("select", () => {
            const attachment = this.mainFrame
              .state()
              .get("selection")
              .first()
              .toJSON();

            const imageUrl =
              attachment.sizes && attachment.sizes.medium
                ? attachment.sizes.medium.url
                : attachment.url;

            $("#main_image_id").val(attachment.id);
            $("#main-image-preview").attr("src", imageUrl);
            $("#main-image-wrapper").show();
          });

          this.mainFrame.open();
        });
      },

      initGallery() {
        $("#add-gallery-images").on("click", (e) => {
          e.preventDefault();

          if (this.galleryFrame) {
            this.galleryFrame.open();
            return;
          }

          this.galleryFrame = wp.media({
            title: "Select Gallery Images",
            button: { text: "Add images" },
            multiple: true,
          });

          this.galleryFrame.on("select", () => {
            const attachments = this.galleryFrame
              .state()
              .get("selection")
              .toJSON();

            attachments.forEach(function (attachment) {
              if (
                $('#gallery-container input[value="' + attachment.id + '"]')
                  .length
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

          this.galleryFrame.open();
        });

        $(document).on("click", ".remove-image", function () {
          $(this).closest(".gallery-item").remove();
        });

        $("#gallery-container").sortable({
          items: ".gallery-item",
          cursor: "move",
          opacity: 0.8,
        });
      },
    };

    /*
     * INIT
     */
    AdminTables.init("#aboyuma-product-category-table");
    AdminTables.init("#aboyuma-product-brand-table");
    AdminTables.init("#aboyuma-product-property-table");
    AdminTables.init("#aboyuma-product-table");

    DeleteHandler.init({
      tableSelector: "#aboyuma-product-category-table",
      buttonClass: ".delete-category-button",
      ajaxObject: productCategoryDelete,
      actionName: "aboyuma_product_category_delete",
      confirmMessage: "Delete category?",
    });

    DeleteHandler.init({
      tableSelector: "#aboyuma-product-brand-table",
      buttonClass: ".delete-brand-button",
      ajaxObject: productBrandDelete,
      actionName: "aboyuma_product_brand_delete",
      confirmMessage: "Delete brand?",
    });

    DeleteHandler.init({
      tableSelector: "#aboyuma-product-property-table",
      buttonClass: ".delete-property-button",
      ajaxObject: productPropertyDelete,
      actionName: "aboyuma_product_property_delete",
      confirmMessage: "Delete property?",
    });

    DeleteHandler.init({
      tableSelector: "#aboyuma-product-table",
      buttonClass: ".delete-product-button",
      ajaxObject: productDelete,
      actionName: "aboyuma_product_delete",
      confirmMessage: "Delete product?",
    });

    CategoryLoader.init();
    MediaManager.initMainImage();
    MediaManager.initGallery();
  });
})(jQuery);
