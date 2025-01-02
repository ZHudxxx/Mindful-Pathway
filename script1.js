document.getElementById("submit-btn").addEventListener("click", function () {
    // Get the confirmation message element
    const confirmationMessage = document.getElementById("confirmation-message");

    // Display the confirmation message
    confirmationMessage.classList.remove("hidden");

    // Optional: Clear the form fields after submission
    document.getElementById("article-form").reset();
});

<button class="back-to-top" onclick="scrollToTop()">↑</button>
<script>
    document.getElementById("submit-btn").addEventListener("click", function () {
        // Get the confirmation message element
        const confirmationMessage = document.getElementById("confirmation-message");

        // Display the confirmation message
        confirmationMessage.classList.remove("hidden");

        // Optional: Clear the form fields after submission
        document.getElementById("article-form").reset();
    });

    // Scroll to top functionality for the back-to-top button
    function scrollToTop() {
        window.scrollTo({ top: 0, behavior: "smooth" });
    }
</script>

  // Close the search bar
  function closeSearch() {
    document.getElementById('search-input').value = '';
  }

  // Scroll to top function
  function scrollToTop() {
    window.scrollTo({
      top: 0,
      behavior: 'smooth'
    });
  }

  function showNotifications() {
    var dropdown = document.getElementById("notifications-dropdown");
    if (dropdown.style.display === "none" || dropdown.style.display === "") {
      dropdown.style.display = "block";
    } else {
      dropdown.style.display = "none";
    }
  }

  function markAsRead(notificationID) {
    // Make an AJAX request to mark the notification as read
    $.ajax({
      url: 'noti_mark_as_read.php', // PHP script to mark as read
      method: 'POST',
      data: {
        notificationID: notificationID
      },
      success: function(response) {
        // Optionally, update the UI to remove the notification or mark it as read
        alert('Notification marked as read!');
      },
      error: function() {
        alert('Error marking notification as read.');
      }
    });
  }


  // Optionally, you can hide the notifications when clicking anywhere outside the dropdown.
  document.addEventListener("click", function(event) {
    var dropdown = document.getElementById("notifications-dropdown");
    if (!dropdown.contains(event.target) && !event.target.closest(".fas.fa-bell")) {
      dropdown.style.display = "none";
    }
  });


  function toggleSidebar() {
    var sidebar = document.querySelector('.sidebar');
    if (sidebar.style.display === 'none' || sidebar.style.display === '') {
      sidebar.style.display = 'block';
    } else {
      sidebar.style.display = 'none';
    }
  }
  window.addEventListener('resize', function() {
    var sidebar = document.querySelector('.sidebar');
    if (window.innerWidth > 768) {
      sidebar.style.display = 'block';
    } else {
      sidebar.style.display = 'none';
    }
  });
