<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>publish</title>
</head>

<body>
    <form action="/pulk_publish" method="post"> 
        @csrf
       <h1  style="color: red;"> You are about publish (published - pending publish) </h1>
        <button type="submit">RUN PUBLISH NOW</button>
    </form>
</body>

</html>