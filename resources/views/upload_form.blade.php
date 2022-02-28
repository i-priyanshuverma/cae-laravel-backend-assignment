<!doctype html>

<html lang="{{ app()->getLocale() }}">

<head>

    <meta charset="utf-8">

    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Laravel Uploading</title>

    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">

    <!-- Optional theme -->

    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css" integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">

    <!-- Fonts -->

    <link href="https://fonts.googleapis.com/css?family=Raleway:100,600" rel="stylesheet" type="text/css">

    <!-- Styles -->

    <style>
        .container {

            margin-top: 2%;

        }
    </style>

</head>

<body>

    @if(Session::has('flash_success'))
    <p class="alert {{ Session::get('alert-class', 'alert-info') }}">{{ Session::get('flash_success') }}</p>
    @endif

    @if(Session::has('flash_error'))
    <p class="alert alert-danger {{ Session::get('alert-class', 'alert-info') }}">{{ Session::get('flash_error') }}</p>
    @endif

    @if (count($errors) > 0)

    <div class="alert alert-danger">

        <ul>

            @foreach ($errors->all() as $error)

            <li>{{ $error }}</li>

            @endforeach

        </ul>

    </div>

    @endif

    <div class="container">

        <div class="row">

            <div class="col-md-2"> <img src="/32114.png" width="80" /></div>

            <div class="col-md-8">
                <h2>RosterBuster</h2>

            </div>

        </div>

        <br>

        <div class="row">

            <div class="col-md-3"></div>

            <div class="col-md-6">

                <form action="/save-file" method="post" enctype="multipart/form-data">

                    {{ csrf_field() }}

                    <label for="Product Name">Roster (html only):</label>

                    <br />

                    <input type="file" class="form-control" name="file" />

                    <br /><br />

                    <input type="submit" class="btn btn-primary" value="Upload" />

                </form>
            </div>

            <div class="col-md-4">
                <form action={{ route('parse-roster') }} method="post">

                    <button type="submit" class="btn btn-info">Parse HTML</button>
                </form>
            </div>
        </div>
        <br><br>
        <div class="row">
            <p class="p-3 mb-2 bg-info text-white"> Use uri <button type="button" onclick="window.location='{{ route("get-data") }}'">/api/get-flight-data</button>
                to get parsed data!
                <br>/**
                <br>* use below given params in query string to get data filtered accordingly :
                <br>* @var <strong>from</strong> & @var <strong>to</strong> are used simultaneously to get records in between those dates
                <br>* @var <strong>keyword</strong> with value <strong>weekly</strong> is used for to get records on weekly basis
                <br>* @var <strong>keyword</strong> with value <strong>weekly_standby</strong> is used for to get records on weekly basis where activity is SBY/StandBy
                <br>* @var <strong>start_location</strong> is used to get records basis on from where the flight will depart
                <br>*/</p>
        </div>
    </div>
</body>

</html>