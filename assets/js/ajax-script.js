jQuery(document).ready(function ($) {
    /**
     * Update team member via AJAX
     */
    $('#update-team-member').on('click', function (e) {
        e.preventDefault();

        const formData = $('#edit-team-member-form').serialize();
        $.ajax({
            url: rulTeams.ajax_url,
            type: 'POST',
            data: formData + '&action=update_team_member&nonce=' + rulTeams.nonce,
            success: function (response) {
                if (response.success) {
                    // Display success message
                    showNotice('success', response.data.message);

                    // Optionally update UI elements dynamically without reload
                    const updatedName = $('#name').val();
                    const updatedDesignation = $('#designation').val();
                    const updatedEmail = $('#email').val();

                    // Update the relevant table row (example: dynamic UI update)
                    const rowId = $('#edit-team-member-form input[name="id"]').val();
                    const row = $(`#team-member-${rowId}`);
                    row.find('.name-column').text(updatedName);
                    row.find('.designation-column').text(updatedDesignation);
                    row.find('.email-column').text(updatedEmail);
                } else {
                    showNotice('error', response.data.message);
                }
            },
            error: function () {
                showNotice('error', 'An error occurred. Please try again.');
            }
        });
    });

    /**
     * Utility function to show admin notices dynamically
     * @param {string} type - Notice type ('success' or 'error')
     * @param {string} message - The message to display
     */
    function showNotice(type, message) {
        const noticeClass = type === 'success' ? 'notice-success' : 'notice-error';
        const noticeHTML = `
            <div class="notice ${noticeClass} is-dismissible">
                <p>${message}</p>
                <button type="button" class="notice-dismiss">
                    <span class="screen-reader-text">Dismiss this notice.</span>
                </button>
            </div>
        `;
        $('.wrap').prepend(noticeHTML);

        // Dismiss Notice
        $('.notice-dismiss').on('click', function () {
            $(this).closest('.notice').remove();
        });
    }


    /**
     * Delete Team Member and Reload Table via Ajax
     */
    $('.delete-team-member').on('click', function (e) {
        e.preventDefault();
    
        const id = $(this).data('id'); // Get the team member ID
        if (!confirm('Are you sure you want to delete this team member?')) return;
    
        $.ajax({
            url: rulTeams.ajax_url, // WordPress Ajax URL
            type: 'POST',
            data: {
                action: 'delete_team_member', // Action registered in PHP
                nonce: rulTeams.nonce, // Nonce for security
                id: id, // Team member ID
            },
            success: function (response) {
                if (response.success) {
                    alert(response.data.message); // Display success message
                    
                    // Remove the row from the table dynamically
                    $(`#team-member-${id}`).fadeOut(300, function () {
                        $(this).remove(); // Fully remove the row after fade-out
                    });
                } else {
                    alert(response.data.message); // Display error message
                }
            },
            error: function () {
                alert('An error occurred while processing your request.'); // Display error message
            }
        });
    });
    

    /**
     * Bulk delete team members via AJAX
     */
    $('#doaction').on('click', function (e) {
        e.preventDefault();

        const selectedAction = $('#bulk-action-selector-top').val(); // Get selected bulk action
        if (selectedAction !== 'delete') {
            showNotice('error', 'Please select "Delete" from bulk actions.');
            return;
        }

        const ids = [];
        $('input[name="id[]"]:checked').each(function () {
            ids.push($(this).val());
        });

        if (ids.length === 0) {
            showNotice('error', 'No team members selected for deletion.');
            return;
        }

        if (!confirm('Are you sure you want to delete the selected team members?')) return;

        $.ajax({
            url: rulTeams.ajax_url,
            type: 'POST',
            data: {
                action: 'bulk_delete_team_members',
                nonce: rulTeams.nonce,
                ids: ids,
            },
            success: function (response) {
                if (response.success) {
                    showNotice('success', response.data.message);
                    location.reload();
                } else {
                    showNotice('error', response.data.message || 'Failed to delete selected team members.');
                }
            },
            error: function () {
                showNotice('error', 'An error occurred while processing your request.');
            },
        });
    });

 
});
