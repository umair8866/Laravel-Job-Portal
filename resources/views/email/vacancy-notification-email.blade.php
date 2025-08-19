<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Vacancy Notification Email</title>
</head>
<body>
    <h1>Hello {{  $jobdata['employer']['name'] }}</h1>
    <p>Vacancy Title: {{  $jobdata['vacancy']['title'] }}</p>

    <p>Employee Details:</p>
    <p>Name: {{ $jobdata['user']['name'] }}</p>
    <p>Email: {{ $jobdata['user']['email'] }}</p>
    <p>Mobile No: {{ $jobdata['user']['mobile'] }}</p>


    
</body>
</html>