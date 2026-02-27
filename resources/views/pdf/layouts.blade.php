<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Report</title>

    <style>
        * {
            font-size: 12px;
        }
        .table {
            width: 100%;
            margin-bottom: 1rem;
            color: #212529;
            border-spacing: 0;
        }

        .table th,
        .table td {
            padding: 0.2rem;
            vertical-align: top;
            border-top: 1px solid #dee2e6;
        }

        .table thead th {
            vertical-align: bottom;
            border-bottom: 1px solid #dee2e6;
        }

        .table tbody + tbody {
            border-top: 1px solid #dee2e6;
        }

        .table-bordered {
            /*border: 1px solid #dee2e6;*/
        }


        .table-bordered th,
        .table-bordered td,
        .table-bordered tr:first-child th,
        .table-bordered tr:first-child td {
            border: 0;
            border-bottom: 1px solid #dee2e6;
            border-right: 1px solid #dee2e6;
            margin: 0;
        }
        .table-bordered tr.table-header th {
            border-top: 1px solid #dee2e6;
        }

        .table-bordered thead tr > th:first-child,
        .table-bordered tbody tr > td:first-child,
        .table-bordered tfoot th:first-child {
            border-left: 1px solid #dee2e6;
        }

        .table-bordered thead th,
        .table-bordered thead td {
            margin: 0;
        }

        .table-borderless,
        .table-borderless th,
        .table-borderless td,
        .table-borderless tr:first-child th,
        .table-borderless tr:first-child td {
            border: 0;
        }

        .text-center {
            text-align: center;
        }
        .text-end {
            text-align: right;
        }
        .text-start {
            text-align: left;
        }

        .mb-0 {
            margin-bottom: 0;
        }
    </style>
</head>
<body>
    @yield('content')
</body>
</html>
