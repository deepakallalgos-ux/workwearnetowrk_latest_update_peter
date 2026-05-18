var jQuery_1_8_2 = jQuery_1_8_2 || $.noConflict();

(function ($, undefined) {
  "use strict";
  $(function () {
    var dialog = $.fn.dialog !== undefined,
      datagrid = $.fn.datagrid !== undefined,
      validate = $.fn.validate !== undefined,
      $dialogDeleteLogo = $("#dialogDeleteLogo"),
      $dialogSendInvoice = $("#dialogSendInvoice"),
      $frmInvoiceConfig = $("#frmInvoiceConfig"),
      tabs = $.fn.tabs !== undefined,
      $tabs = $("#tabs"),
      tOpt = {
        activate: function (event, ui) {
          $(":input[name='tab_id']").val($(ui.newPanel).prop("id"));
        },
      };

    if ($tabs.length > 0 && tabs) {
      $tabs.tabs(tOpt);
    }

    if ($frmInvoiceConfig.length > 0) {
      $frmInvoiceConfig.validate({
        rules: {
          y_email: { email: true },
          y_url:   { url:   true },
        },
        errorPlacement: function (error, element) {
          error.insertAfter(element.parent());
        },
        onkeyup: false,
        errorClass: "err",
        wrapper: "em",
        ignore: "",
        // De globale submitHandler in pjBaseCore.js start alleen Ladda maar
        // roept form.submit() niet aan — daardoor werd de form nooit verzonden.
        // We overrulen de globale default met een handler die ook daadwerkelijk
        // de form submit triggert.
        submitHandler: function (form) {
          var laddaButtons = $(form).find('.ladda-button');
          if (laddaButtons.length > 0 && typeof laddaButtons.ladda === 'function') {
            try { laddaButtons.ladda().ladda('start'); } catch (e) {}
          }
          form.submit();
          return true;
        },
        invalidHandler: function (event, validator) {
          if (validator.numberOfInvalids()) {
            var index = $(validator.errorList[0].element, this)
              .closest("div[id^='tabs-']")
              .index();
            if ($tabs.length > 0 && tabs && index !== -1) {
              $tabs.tabs(tOpt).tabs("option", "active", index - 1);
            }
          }
        },
      });
    }

    $(document)
      .on("search", ".frm-filter", function (e) {
        var $this = $(this),
          content = $grid.datagrid("option", "content"),
          cache = $grid.datagrid("option", "cache");

        $.extend(cache, { q: $this.find("input[name='q']").val() });

        $grid.datagrid("option", "cache", cache);

        $grid.datagrid(
          "load",
          "index.php?controller=pjInvoice&action=pjActionGetInvoices",
          "created",
          "DESC",
          content.page,
          content.rowCount
        );
      })
      .on("submit", ".frm-filter", function (e) {
        if (e && e.preventDefault) { e.preventDefault(); }
        var $form = $(this),
          $q = $form.find("input[name='q']");
        $q.val($q.val().replace(/^\s+|\s+$/g, ""));
        $form.trigger("search");
        return false;
      })
      .on("change", "select[name='foreign_id']", function (e) {
        var $this = $(this),
          content = $grid.datagrid("option", "content"),
          cache = $grid.datagrid("option", "cache");

        $.extend(cache, { foreign_id: $this.find("option:selected").val() });

        $grid.datagrid("option", "cache", cache);

        $grid.datagrid(
          "load",
          "index.php?controller=pjInvoice&action=pjActionGetInvoices",
          "created",
          "DESC",
          content.page,
          content.rowCount
        );
      })
      .on("change", "select[name='status_filter']", function () {
        if (typeof $grid === "undefined" || !$grid) { return; }
        var $this = $(this),
          content = $grid.datagrid("option", "content"),
          cache = $grid.datagrid("option", "cache");
        $.extend(cache, { status: $this.val() });
        $grid.datagrid("option", "cache", cache);
        $grid.datagrid(
          "load",
          "index.php?controller=pjInvoice&action=pjActionGetInvoices",
          "created",
          "DESC",
          1,
          content.rowCount
        );
      });

    function formatInvoiceNumber(str, obj) {
      return '<a href="index.php?controller=pjInvoice&action=generatePdf&id=' + encodeURIComponent(obj.uuid) + '&uuid=' + encodeURIComponent(obj.order_id) + '" target="_blank">' + str + '</a>';
    }

    function formatOrderId(str, obj) {
      if (
        !("booking_url" in myLabel) ||
        myLabel.booking_url === "" ||
        myLabel.booking_url === "#"
      ) {
        return str;
      }

      return [
        '<a href="',
        myLabel.booking_url.replace("{ORDER_ID}", str),
        '">',
        str,
        "</a>",
      ].join("");
    }

    function formatTotal(str, obj) {
      return obj.total_formated;
    }

    function escapeHtml(str) {
      if (str === null || str === undefined) { return ""; }
      return String(str)
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;")
        .replace(/'/g, "&#039;");
    }

    function formatCustomer(str, obj) {
      var label = obj.customer_label || "";
      var sub   = obj.customer_sub || "";
      if (!label && !sub) { return '<span class="text-muted">—</span>'; }
      var html = '<div style="line-height:1.3"><div>' + escapeHtml(label) + "</div>";
      if (sub) {
        html += '<div style="font-size:11px;color:#888">' + escapeHtml(sub) + "</div>";
      }
      html += "</div>";
      return html;
    }

    function formatCreated(str) {
      if (str === null || str.length === 0) {
        return myLabel.empty_datetime;
      }
      if (str === "0000-00-00 00:00:00") {
        return myLabel.invalid_datetime;
      }
      if (str.match(/\d{4}-\d{2}-\d{2}\s\d{2}:\d{2}:\d{2}/) !== null) {
        var x = str.split(" "),
          date = x[0],
          time = x[1],
          dx = date.split("-"),
          tx = time.split(":"),
          y = dx[0],
          m = parseInt(dx[1], 10) - 1,
          d = dx[2],
          hh = tx[0],
          mm = tx[1],
          ss = tx[2];
        return $.datagrid.formatDate(
          new Date(y, m, d, hh, mm, ss),
          pjGrid.jsDateFormat + ", hh:mm:ss"
        );
      }
    }

    $(document).ajaxSuccess(function (event, xhr, settings, response) {
      if (
        settings.url.indexOf(
          "controller=pjInvoice&action=pjActionGetInvoices"
        ) !== -1 &&
        response.hide_loader
      ) {
        $(".sk-spinner").hide();
      }
    });

    if ($("#grid").length > 0 && datagrid) {
      var $grid = $("#grid").datagrid({
        buttons: [
          // Inline icoon-knoppen — geen dropdown (die werd afgeknipt door .pj-grid overflow)
          // Datagrid genereert auto: <a class="pj-table-icon-{type}"><i class="fa fa-{type}"></i></a>
          // PDF: directe download (modern). Envelope: opent e-mail dialoog (linkClass intercept).
          // Delete: verwijdert factuur.
          {
            type: "file-pdf-o",
            url: "index.php?controller=pjInvoice&action=generatePdf&id={:uuid}&uuid={:order_id}",
            title: myLabel.download_pdf_invoice,
            target: "_blank",
          },
          {
            type: "envelope",
            url: "index.php?controller=pjInvoice&action=pjActionSend&id={:uuid}&uuid={:order_id}",
            title: myLabel.email_invoice,
            linkClass: "plugin_invoice_email",
          },
          {
            type: "delete",
            url: "index.php?controller=pjInvoice&action=pjActionDelete&id={:id}",
            title: "Delete",
          },
        ],

        columns: [
          {
            text: myLabel.num,
            type: "text",
            sortable: true,
            editable: false,
            renderer: formatInvoiceNumber,
          },
          {
            text: myLabel.order_id,
            type: "text",
            sortable: true,
            editable: false,
            renderer: formatOrderId,
          },
          {
            text: myLabel.customer,
            type: "text",
            sortable: true,
            editable: false,
            renderer: formatCustomer,
          },
          {
            text: myLabel.issue_date,
            type: "date",
            sortable: true,
            editable: false,
            renderer: $.datagrid._formatDate,
            dateFormat: pjGrid.jsDateFormat,
          },
          {
            text: myLabel.status,
            type: "select",
            sortable: true,
            editable: true,
            applyClass: "pj-status",
            options: [
              { label: myLabel.paid, value: "paid" },
              { label: myLabel.not_paid, value: "not_paid" },
              { label: myLabel.cancelled, value: "cancelled" },
            ],
          },
          {
            text: myLabel.total,
            type: "text",
            sortable: true,
            editable: false,
            align: "right",
            renderer: formatTotal,
          },
        ],

        dataUrl: "index.php?controller=pjInvoice&action=pjActionGetInvoices",
        dataType: "json",
        fields: ["uuid", "order_id", "b_name", "issue_date", "status", "total"],

        paginator: {
          actions: [
            {
              text: myLabel.download_pdfs_bulk,
              url: "index.php?controller=pjInvoice&action=pjActionDownloadBulk",
              render: true,
            },
            {
              text: myLabel.delete_title,
              url: "index.php?controller=pjInvoice&action=pjActionDeleteBulk",
              render: true,
              confirmation: myLabel.delete_body,
            },
          ],
          gotoPage: true,
          paginate: true,
          total: true,
          rowCount: true,
        },

        saveUrl: "index.php?controller=pjInvoice&action=pjActionSaveInvoice&id={:id}",

        select: { field: "id", name: "record[]" },
      });

      var m = window.location.href.match(/&q=(.*)/);
      if (m !== null) {
        $(".frm-filter").trigger("search");
      }

      m = window.location.href.match(/&(foreign_id=)(\d+)?/);
      if (m !== null) {
        var content = $grid.datagrid("option", "content"),
          cache = $grid.datagrid("option", "cache");

        $.extend(cache, { foreign_id: m[2] !== undefined ? m[2] : "" });
        $grid.datagrid("option", "cache", cache);

        $grid.datagrid(
          "load",
          "index.php?controller=pjInvoice&action=pjActionGetInvoices",
          "created",
          "DESC",
          content.page,
          content.rowCount
        );
      }
    }

    $(document)
      .on("click", ".plugin_invoice_delete_logo", function (e) {
        e.preventDefault();
        swal(
          {
            title: myLabel.btn_delete,
            text: myLabel.confirm_delete_msg,
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: myLabel.btn_delete,
            cancelButtonText: myLabel.btn_cancel,
            closeOnConfirm: false,
            showLoaderOnConfirm: true,
          },
          function () {
            $.post("index.php?controller=pjInvoice&action=pjActionDeleteLogo")
              .done(function () {
                $("#plugin_invoice_box_logo").html(
                  '<input type="file" name="y_logo" id="y_logo" />'
                );
                swal.close();
              })
              .fail(function () {
                swal("Error", "Something went wrong while deleting the logo.", "error");
              });
          }
        );
      })
      .on("change", "input[name='p_accept_paypal'],input[name='p_accept_mollie'], input[name='p_accept_authorize'], input[name='p_accept_bank']",
        function () {
          var $this = $(this);
          if ($this.is(":checked")) {
            $($this.data("box")).show();
          } else {
            $($this.data("box")).hide();
          }
        }
      )
      .on("click", ".plugin_invoice_email", function (e) {
        if (e && e.preventDefault) { e.preventDefault(); }

        var href = $(this).attr("href");
        var uuidMatch = href.match(/[?&]uuid=([^&]+)/);
        var orderUuid = uuidMatch ? decodeURIComponent(uuidMatch[1]) : null;

        if (!orderUuid) {
          return false;
        }

        // Framework slaat volledige rij-data op via $a.data("id", rowData)
        var rowData    = $(this).data("id") || {};
        var clientMail = rowData.b_email || "";
        var clientName = rowData.b_name  || "";

        // Hergebruik de bestaande betalingsbevestigingsmail (variant=payment) — zelfde
        // template + PDF-bijlage als wanneer Mollie de betaling bevestigt. Voorkomt dat
        // er een aparte factuur-template gebruikt wordt naast de andere notificaties.
        if (typeof swal === "function") {
          var bodyText = "Stuur de betalingsbevestiging (incl. factuur als bijlage indien ingesteld) naar:\n\n"
                       + (clientName ? clientName + "\n" : "")
                       + clientMail;
          swal({
            title: "Betalingsbevestiging versturen?",
            text:  bodyText,
            type:  "info",
            showCancelButton: true,
            confirmButtonText: myLabel.btn_send   || "Verstuur",
            cancelButtonText:  myLabel.btn_cancel || "Annuleren",
            closeOnConfirm: false,
            showLoaderOnConfirm: true
          }, function (confirm) {
            if (confirm === false) { return false; }
            $.post("index.php?controller=pjAdminOrders&action=pjActionSendStatusEmail", {
              uuid:    orderUuid,
              variant: "payment"
            }).done(function (r) {
              if (r && r.status === "OK") {
                swal("Verstuurd!", "De betalingsbevestiging is verstuurd naar " + clientMail + ".", "success");
              } else {
                swal("Fout", (r && r.text) ? r.text : "Versturen mislukt.", "error");
              }
            }).fail(function () {
              swal("Fout", "Versturen mislukt (server-fout).", "error");
            });
          });
        }

        return false;
      });

    $("li.plugin_view_invoice a").unbind("click");

    if ($dialogSendInvoice.length > 0 && dialog) {
      var buttons = {};
      buttons[myLabel.btn_send] = function () {
        var $this = $(this);
        $.post(
          "index.php?controller=pjInvoice&action=pjActionSend",
          $dialogSendInvoice.find("form").serialize()
        ).always(function () { $this.dialog("close"); });
      };
      buttons[myLabel.btn_cancel] = function () { $(this).dialog("close"); };

      $dialogSendInvoice.dialog({
        modal: true,
        autoOpen: false,
        draggable: false,
        resizable: false,
        width: 770,
        open: function () {
          var $this = $(this);
          $dialogSendInvoice.html("");
          $.get("index.php?controller=pjInvoice&action=pjActionSend", {
            id: $this.data("id"),
            uuid: $this.data("uuid"),
          })
            .done(function (data) { $dialogSendInvoice.html(data); })
            .always(function () { $dialogSendInvoice.dialog("option", "position", "center"); });
        },
        buttons: buttons,
      });
    }

    if ($dialogDeleteLogo.length > 0 && dialog) {
      var dlButtons = {};
      dlButtons[myLabel.btn_delete] = function () {
        var $this = $(this);
        $.post("index.php?controller=pjInvoice&action=pjActionDeleteLogo")
          .done(function () {
            $("#plugin_invoice_box_logo").html('<input type="file" name="y_logo" id="y_logo" />');
          })
          .always(function () { $this.dialog("close"); });
      };
      dlButtons[myLabel.btn_cancel] = function () { $(this).dialog("close"); };

      $dialogDeleteLogo.dialog({
        modal: true,
        autoOpen: false,
        draggable: false,
        resizable: false,
        buttons: dlButtons,
      });
    }

    if (window.tinymce !== undefined) {
      tinymce.init({
        selector: "textarea.mceEditor",
        theme: "modern",
        height: 700,
        plugins: [
          "advlist autolink link image lists charmap print preview hr anchor pagebreak",
          "searchreplace visualblocks visualchars code fullscreen insertdatetime media nonbreaking",
          "save table contextmenu directionality emoticons template paste textcolor",
        ],
        toolbar:
          "insertfile undo redo | styleselect fontselect | fontsizeselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image | print preview media fullpage | forecolor backcolor emoticons",
      });
    }
  });
})(jQuery_1_8_2);
