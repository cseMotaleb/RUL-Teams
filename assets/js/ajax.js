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
            data: formData + '&action=update_team_member',
            success: function (response) {
                if (response.success) {
                    // Redirect with success message
                    const redirectUrl = new URL(window.location.href);
                    redirectUrl.searchParams.set('message', 'member_updated');
                    window.location.href = redirectUrl.toString();
                } else {
                    // Display error message in the notice area
                    showNotice('error', response.data.message || 'Failed to update Team Member.');
                }
            },
            error: function () {
                showNotice('error', 'An error occurred. Please try again.');
            }
        });
    });

    /**
     * Delete a single team member via AJAX
     */
    $('.delete-team-member').on('click', function (e) {
        e.preventDefault();

        const id = $(this).data('id');
        if (!confirm('Are you sure you want to delete this team member?')) return;

        $.ajax({
            url: rulTeams.ajax_url,
            type: 'POST',
            data: {
                action: 'delete_team_member',
                nonce: rulTeams.nonce,
                id: id,
            },
            success: function (response) {
                if (response.success) {
                    showNotice('success', response.data.message);
                    location.reload();
                } else {
                    showNotice('error', response.data.message);
                }
            },
            error: function () {
                showNotice('error', 'An error occurred while processing your request.');
            },
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

    /**
     * Utility function to display admin notices
     * @param {string} type - Notice type: 'success' or 'error'
     * @param {string} message - The notice message
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

        // Handle dismiss
        $('.notice-dismiss').on('click', function () {
            $(this).closest('.notice').remove();
        });
    }
});
