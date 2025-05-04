$(document).ready(function () {
    const PENDING = 1;
    const COMPLETE = 2;

    $('#importsTable').DataTable({
        processing: true,
        serverSide: false,
        ajax: {
            url: '/products/get-imports',
            dataSrc: 'data'
        },
        columns: [
            { data: 'id' },
            {
                data: 'status_id',
                render: function (data, type, row) {
                    if (data === PENDING) {
                        return '<span style="color: red; font-weight: bold;">Pending</span>';
                    } else if (data === COMPLETE) {
                        return '<span style="color: green; font-weight: bold;">Completed</span>';
                    } else {
                        return '<span style="color: gray;">Unknown</span>';
                    }
                }
            },
            { data: 'original_filename' },
            {
                data: 'created_at',
                render: function (data, type, row) {
                    const date = new Date(data);
                    return date.toLocaleDateString('en-GB', {
                        day: '2-digit',
                        month: 'short',
                        year: 'numeric'
                    });
                }
            }]
    });


});