<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>jQuery UI Accordion - Default functionality</title>
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script>
        $(function () {
            $("#accordion").accordion();
        });
    </script>
</head>
<body>
    <div id="accordion">

        @foreach($rates as $currency => $data)
            <h3>{{ $currency }}</h3>
            <div>
                <ul>
                    @foreach($data as $item)
                        <li>{{ $item['to'] }} - {{ $item['rate'] }}</li>
                    @endforeach
                </ul>
            </div>
        @endforeach

    </div>
</body>
</html>