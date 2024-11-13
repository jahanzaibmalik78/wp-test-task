jQuery(document).ready(function($) {
    $.ajax({
        url: ajaxurl, 
        method: 'POST',
        data: {
            action: 'get_architecture_projects'
        },
        success: function(response) {
            if (response.success) {
                console.log(response.data); 
                // You can add code here to display the projects on the page
            } else {
                console.log("No projects found.");
            }
        }
    });
});
