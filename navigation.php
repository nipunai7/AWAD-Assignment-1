<link
    rel="stylesheet"
    href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css"
    integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh"
    crossorigin="anonymous">

    <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <style>
                    .form-control {
                        margin-top: 4px;
                    }

                    .label-mid {
                        display: flex;
                        flex-direction: column;
                        justify-content: flex-end;
                    }

                    .ui-datepicker-calendar {
                        display: none;
                    }

                    .ui-datepicker-month {
                        display: none;
                    }

                    .ui-datepicker-next,
                    .ui-datepicker-prev {
                        display: none;
                    }
                </style>
                <nav class="navbar navbar-expand-lg navbar-light bg-light text-center">
                    <a href="/?#">
                        <button class="btn btn-info text-center m-1">Home</button>
                    </a>
                    <a href="stat.php">
                        <button class="btn btn-info text-center m-1">Filters</button>
                    </a>
                    <a>
                        <button class="btn btn-info text-center m-1" onclick="downloadXML()">Downlaod XML File</button>
                    </a>
                    <a
                        href="https://nipunamunasinghe.com"
                        target="_blank"
                        rel="noopener noreferrer">
                        <button class="btn btn-danger ml-5">Nipuna Munasinghe - IM/2017/047</button>
                    </a>
                </nav>