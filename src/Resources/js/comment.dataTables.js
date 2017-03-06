$(document).ready(function() {
    $('#comment-dataTables').DataTable( {
            "paging": true,
            "language": {
                "url": "../src/Resources/js/dataTables.french.json"
            },
            "ordering": false,
        }

    );
})

