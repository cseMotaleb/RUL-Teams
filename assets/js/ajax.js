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
                    alert(response.data.message || 'Team Member updated successfully!');
                    window.location.href = 'admin.php?page=rul-teams';
                } else {
                    alert(response.data.message || 'Failed to update Team Member.');
                }
            },
            error: function () {
                alert('An error occurred. Please try again.');
            }
        });
    });

    // Single Delete
    $('.delete-team-member').on('click', function (e) {
        e.preventDefault();

        if (!confirm('Are you sure you want to delete this team member?')) {
            return;
        }

        const memberId = $(this).data('id');

        $.ajax({
            url: rulTeams.ajax_url,
            type: 'POST',
            data: {
                action: 'delete_team_member',
                id: memberId,
                nonce: rulTeams.nonce,
            },
            success: function (response) {
                if (response.success) {
                    alert('Team member deleted successfully.');
                    location.reload();
                } else {
                    alert(response.data.message || 'Failed to delete team member.');
                }
            },
            error: function () {
                alert('An error occurred. Please try again.');
            },
        });
    });

    // Bulk Delete
    $('#bulk-delete-button').on('click', function (e) {
        e.preventDefault();

        const selectedIds = [];
        $('input[name="id[]"]:checked').each(function () {
            selectedIds.push($(this).val());
        });

        if (selectedIds.length === 0) {
            alert('Please select at least one team member.');
            return;
        }

        if (!confirm('Are you sure you want to delete the selected team members?')) {
            return;
        }

        $.ajax({
            url: rulTeams.ajax_url,
            type: 'POST',
            data: {
                action: 'bulk_delete_team_members',
                ids: selectedIds,
                nonce: rulTeams.nonce,
            },
            success: function (response) {
                if (response.success) {
                    alert('Selected team members deleted successfully.');
                    location.reload();
                } else {
                    alert(response.data.message || 'Failed to delete team members.');
                }
            },
            error: function () {
                alert('An error occurred. Please try again.');
            },
        });
    });
});
