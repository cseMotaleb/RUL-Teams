jQuery(document).ready(function ($) {
    // Update team member
    $('#update-team-member').on('click', function (e) {
        e.preventDefault();

        const formData = $('#edit-team-member-form').serialize();

        $.ajax({
            url: rulTeams.ajax_url,
            type: 'POST',
            data: formData + '&action=update_team_member',
            success: function (response) {
                if (response.success) {
                    // Redirect to the edit page with a success message
                    const redirectUrl = new URL(window.location.href);
                    redirectUrl.searchParams.set('message', 'member_updated');
                    window.location.href = redirectUrl.toString();
                } else {
                    // Show error message in the notice area
                    $('#edit-team-member-form').before(
                        `<div class="notice notice-error is-dismissible"><p>${response.data.message || 'Failed to update Team Member.'}</p></div>`
                    );
                }
            },
            error: function () {
                $('#edit-team-member-form').before(
                    `<div class="notice notice-error is-dismissible"><p>An error occurred. Please try again.</p></div>`
                );
            }
        });
    });
    
    // Single Delete Action
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
                    alert(response.data.message);
                    location.reload();
                } else {
                    alert(response.data.message);
                }
            },
            error: function () {
                alert('An error occurred while processing your request.');
            },
        });
    });

   // Bulk Delete Action
// Bulk Delete Action
$('#doaction').on('click', function (e) {
    e.preventDefault();

    const selectedAction = $('#bulk-action-selector-top').val(); // Bulk action selector
    if (selectedAction !== 'delete') {
        alert('Please select "Delete" from bulk actions.');
        return;
    }

    const ids = [];
    $('input[name="id[]"]:checked').each(function () {
        ids.push($(this).val());
    });

    if (ids.length === 0) {
        alert('No team members selected for deletion.');
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
                location.reload(); // Reload the page to reflect changes
            } else {
                alert(response.data.message || 'Failed to delete selected team members.');
            }
        },
        error: function () {
            alert('An error occurred while processing your request.');
        },
    });
});


});
