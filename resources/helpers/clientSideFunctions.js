// Client-side linkification (for updates)
function linkifyText(text) {
  text = text.replace(/(\b(https?:\/\/|www\.)[^\s<]+)/gi, function (match) {
    var href = /^https?:\/\//i.test(match) ? match : "http://" + match;
    return (
      '<a href="' +
      href +
      '" target="_blank" rel="noopener noreferrer">' +
      match +
      "</a>"
    );
  });
  text = text.replace(
    /\b([\w.+-]+@[\w.-]+\.[A-Za-z]{2,})\b/g,
    '<a href="mailto:$1">$1</a>',
  );
  text = text.replace(/(\B@[a-zA-Z0-9_]{1,15})\b/g, function (match) {
    return (
      '<a href="https://twitter.com/' +
      match.substring(1) +
      '" target="_blank" rel="noopener noreferrer">' +
      match +
      "</a>"
    );
  });
  return text;
}

$(document).ready(function () {
  console.log("**LOGGED 3**");

  // Sorting dropdown menus
  // Function to sort select options alphanumerically
  function sortSelectOptions(selectElement) {
    var options = $(selectElement).find("option");
    options.sort(function (a, b) {
      return $(a).text().localeCompare($(b).text(), undefined, {
        numeric: true,
        sensitivity: "base",
      });
    });
    $(selectElement).html(options);
  }

  // Sort all select elements on page load
  $("select").each(function () {
    sortSelectOptions(this);
  });

  // Sort select elements when a new record is saved or an edit is saved
  $(".new-save-btn, .save-btn").on("click", function () {
    setTimeout(function () {
      $("select").each(function () {
        sortSelectOptions(this);
      });
    }, 100); // A small delay to ensure the DOM has updated
  });

  // Menu bar popovers
  $(".menu-button").click(function () {
    var popup = $(this).data("popup");
    $(".overlay").show();
    $("#" + popup + "-popup").show();
  });
  $(".overlay, .close-button").click(function () {
    $(".overlay, .popover").hide();
  });
  $(".popover").click(function (e) {
    e.stopPropagation();
  });

  // Tab switching with persistence
  $(".tab-button").click(function () {
    var table = $(this).data("table");
    localStorage.setItem("activeTab", table);
    $(".tab-button").removeClass("active");
    $(this).addClass("active");
    $(".table-container").hide();
    $("#container-" + table).show();
  });
  var activeTab = localStorage.getItem("activeTab") || "stories";
  $('.tab-button[data-table="' + activeTab + '"]').click();

  // Calendar widget for date inputs
  var calendarInput;
  $(".calendar-btn").click(function (e) {
    e.stopPropagation();
    calendarInput = $(this).closest("td").find("input");
    $("#calendar-popup")
      .css({ top: e.pageY + "px", left: e.pageX + "px" })
      .datepicker({
        dateFormat: "yy-mm-dd",
        onSelect: function (dateText) {
          if (calendarInput) {
            calendarInput.val(dateText);
            $("#calendar-popup").hide();
          }
        },
      })
      .show();
  });
  $(document).click(function (event) {
    if (
      !$(event.target).closest("#calendar-popup").length &&
      !$(event.target).hasClass("calendar-btn")
    ) {
      $("#calendar-popup").hide();
    }
  });
  $("#calendar-popup").on("click", function (e) {
    e.stopPropagation();
  });

  // Delete row
  $("button.delete-btn").click(function () {
    var row = $(this).closest("tr");
    var id = row.data("id");
    var table = row.closest("table").data("table");
    if (confirm("Are you sure you want to delete this entry?")) {
      $.post(
        "",
        { action: "delete", table: table, record_id: id },
        function (response) {
          if (response.status === "success") {
            row.remove();
          }
        },
        "json",
      );
    }
  });

  // Enter edit mode
  $("button.edit-btn").click(function () {
    console.log("Entering edit mode.");
    var row = $(this).closest("tr");
    row.addClass("editing");

    // For each dropdown in the editing row
    row.find("select.edit-input").each(function () {
      var currentCellValue = $(this)
        .closest("td")
        .find(".cell-content")
        .text()
        .trim();

      // Set the dropdown to match the current cell value
      $(this)
        .find("option")
        .each(function () {
          if ($(this).text().trim() === currentCellValue) {
            $(this).prop("selected", true);
          }
        });
      if ($(this).attr("name") === "Type") {
        $(this)
          .find('option[value="disabled"]')
          .prop("selected", true)
          .closest("select")
          .prop("disabled", true);
      }
    });
  });

  // Function to determine story type based on wordcount
  function getStoryType(wordcount) {
    console.log("Word count received:", wordcount);
    wordcount = parseInt(wordcount);
    console.log("Parsed word count:", wordcount);
    if (isNaN(wordcount)) {
      console.log("Word count is not a number.");
    } else {
      console.log("Word count is a number.");
    }

    if (isNaN(wordcount)) return "";
    if (wordcount <= 1000) return "Flash Fiction";
    if (wordcount <= 7500) return "Short Story";
    if (wordcount <= 17500) return "Novelette";
    if (wordcount <= 40000) return "Novella";
    return "Novel";
  }

  // Save edits
  $("button.save-btn").click(function () {
    var row = $(this).closest("tr");
    var table = row.closest("table").data("table");
    var id = row.data("id");
    var updates = {};

    row.find(".edit-input").each(function () {
      updates[$(this).attr("name")] = $(this).val();

      // If this is the wordcount field, automatically update the story type
      if ($(this).attr("name") === "wordcount") {
        const newStoryType = getStoryType($(this).val());
        updates["story_type"] = newStoryType;

        // Update the story type dropdown to reflect the new value
        row.find('.edit-input[name="story_type"]').val(newStoryType);
      }
    });
    $.post(
      "",
      {
        action: "update",
        table: table,
        record_id: id,
        updates: JSON.stringify(updates),
      },
      function (response) {
        console.log("Server response:", response);
        if (response.status === "success") {
          editing = false; // Reset editing flag after successful save
          //TODO location.reload(); // Refresh the page after successful update
        } else {
          console.error("Update failed:", response);
        }
      },
      "json",
    );
  });

  // Cancel editing
  $("button.cancel-btn").click(function () {
    $(this).closest("tr").removeClass("editing");
    editing = false; // Reset editing flag when cancel is clicked
  });

  // Save new record
  $(".new-save-btn").click(function () {
    var table = $(this).data("table");
    var row = $("#new-record-row-" + table);
    var data = { action: "add", table: table };
    row.find("input, select").each(function () {
      data[$(this).attr("name")] = $(this).val();
    });
    $.post(
      "",
      data,
      function (response) {
        if (response.status === "success") {
          row.find("input:not([readonly]), select").val("");
          // Refresh the page after adding new record
          //TODO location.reload();
        }
      },
      "json",
    );
  });

  // Sorting functionality with updated arrow sizes and colors
  var sortOrder = {
    asc: "desc",
    desc: "asc",
  };

  function sortTable(table, colIndex, order) {
    var rows = $("tbody tr", table).get();

    rows.sort(function (a, b) {
      var A = $(a).children("td").eq(colIndex).text().toUpperCase();
      var B = $(b).children("td").eq(colIndex).text().toUpperCase();
      if (A < B) return order === "asc" ? -1 : 1;
      if (A > B) return order === "asc" ? 1 : -1;
      return 0;
    });

    $.each(rows, function (index, row) {
      table.append(row);
    });
  }

  $("th").click(function () {
    var table = $(this).closest("table");
    var colIndex = $(this).index();
    // Check if this is not the new row
    if (
      $(this).parent().attr("id") !==
      "new-record-row-" + table.data("table")
    ) {
      var currentOrder = $(this).hasClass("sorted-asc") ? "asc" : "desc";
      var newOrder = sortOrder[currentOrder];

      $("th", table).removeClass("sorted-asc sorted-desc");
      $(this).addClass("sorted-" + newOrder);

      sortTable(table, colIndex, newOrder);
    }
  });

  // Warning when trying to leave the page while editing
  var editing = false;
  $(document).on("click", ".edit-btn", function () {
    editing = true;
  });

  window.onbeforeunload = function () {
    if (editing) {
      return "You have unsaved changes. Are you sure you want to leave?";
    }
  };
});

// This part should remain outside since it listens for the window load event, which happens after DOM is ready
window.addEventListener("load", function () {
  const audio = document.getElementById("myAudio");
  const playPromise = audio.play();
  if (playPromise !== undefined) {
    playPromise.catch((error) => {
      console.log("Autoplay prevented:", error);
      // Optionally, show a play button to let the user start playback.
    });
  }
});
