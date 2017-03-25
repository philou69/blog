$(document).ready(function() {
    $('#chapter-dataTables').DataTable( {
            "paging": true,
            "language": {
                "url": "../../src/Resources/js/dataTables.french.json"
            },
            "order": [[2, "asc"]]
        }

    );
})
