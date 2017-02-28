$(document).ready(function() {
    $('#comment-dataTables').DataTable( {
            "paging": true,
            "language": {
                "url": "../src/Resources/js/dataTables.french.json"
            },
            "order": [[3, "desc"]]
        }

    );
})

